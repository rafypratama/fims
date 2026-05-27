<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Invoice;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;

class OnboardingController extends Controller
{
    public function index()
    {
        return view('onboarding.index');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_type' => 'required|in:excel,pdf',
            'file' => 'required|file|max:10240', // 10MB limit
        ]);

        $file = $request->file('file');

        if ($request->file_type === 'excel') {
            try {
                $originalName = $file->getClientOriginalName();
                $customerName = 'Default Customer';
                
                // Regex to find "Harga [Customer Name]"
                if (preg_match('/Harga\s+(.+)\.xlsx/i', $originalName, $matches)) {
                    $customerName = trim($matches[1]);
                } else {
                    $customerName = pathinfo($originalName, PATHINFO_FILENAME);
                }

                // Find or create customer
                $customer = Customer::firstOrCreate(
                    ['name' => $customerName],
                    [
                        'brand_name' => $customerName,
                        'address' => 'Diimpor otomatis via onboarding',
                        'phone' => '-',
                        'email' => strtolower(str_replace(' ', '', $customerName)) . '@example.com'
                    ]
                );

                $spreadsheet = IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();

                $importedCount = 0;
                foreach ($rows as $index => $row) {
                    if ($index === 0) continue; // Skip header row
                    
                    $code = isset($row[0]) ? trim($row[0]) : null;
                    $name = isset($row[1]) ? trim($row[1]) : null;
                    $unit = isset($row[2]) ? trim($row[2]) : 'Pcs';
                    $price = isset($row[3]) ? floatval($row[3]) : 0;

                    if (empty($name)) continue;

                    // Upsert Product in catalog
                    Product::updateOrCreate(
                        [
                            'customer_id' => $customer->id,
                            'name' => $name,
                        ],
                        [
                            'code' => $code,
                            'unit' => $unit,
                            'default_price' => $price,
                        ]
                    );
                    $importedCount++;
                }

                return back()->with('success', "Berhasil memetakan katalog. Impor {$importedCount} produk selesai untuk Customer: {$customerName}");
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
            }
        } else {
            // PDF upload path (Jalur 2: Split screen)
            try {
                $onboardingPath = public_path('uploads/onboarding');
                if (!file_exists($onboardingPath)) {
                    mkdir($onboardingPath, 0755, true);
                }

                $originalFilename = $file->getClientOriginalName();
                $filename = time() . '_' . str_replace(' ', '_', $originalFilename);
                $localFilePath = $file->getRealPath();

                // Extract customer name candidate from the original PDF filename
                // e.g. "Invoice 322 Safaga.pdf" => "Safaga", "Invoice 01 ML.pdf" => "ML"
                $customerNameFromFile = '';
                $baseName = pathinfo($originalFilename, PATHINFO_FILENAME); // e.g. "Invoice 322 Safaga"
                if (preg_match('/Invoice\s*\d+\s+(.+)/i', $baseName, $fnMatches)) {
                    $customerNameFromFile = trim($fnMatches[1]);
                } elseif (preg_match('/Harga\s+(.+)/i', $baseName, $fnMatches)) {
                    $customerNameFromFile = trim($fnMatches[1]);
                } else {
                    // Fallback: use entire basename minus common prefixes/numbers
                    $customerNameFromFile = preg_replace('/^[\d_\s]+/', '', $baseName);
                    $customerNameFromFile = trim($customerNameFromFile);
                }

                // Parse the PDF
                $parser = new Parser();
                $pdf = $parser->parseFile($localFilePath);
                $text = $pdf->getText();

                // Parse Invoice Number
                $invoiceNumber = '';
                if (preg_match('/INVOICE\s*:\s*([^\r\n]+)/i', $text, $matches)) {
                    $invoiceNumber = trim($matches[1]);
                }

                // Parse Date
                $invoiceDate = date('Y-m-d');
                if (preg_match('/DATE\s*:\s*([^\r\n]+)/i', $text, $matches)) {
                    $dateStr = trim($matches[1]);
                    if (preg_match('/(\d{2})[\/\.-](\d{2})[\/\.-](\d{4})/', $dateStr, $dateParts)) {
                        $invoiceDate = "{$dateParts[3]}-{$dateParts[2]}-{$dateParts[1]}"; // Y-m-d
                    }
                }

                // If invoiceNumber is numeric sequence, e.g. "00321", format as "INV-YYYY-MM-XXXXX" based on parsed date
                if ($invoiceNumber && is_numeric($invoiceNumber)) {
                    $dateYear = date('Y', strtotime($invoiceDate));
                    $dateMonth = date('m', strtotime($invoiceDate));
                    $invoiceNumber = "INV-{$dateYear}-{$dateMonth}-" . str_pad($invoiceNumber, 5, '0', STR_PAD_LEFT);
                }

                // Parse Customer
                $customerName = '';
                if (preg_match('/KEPADA\s+(?:Up\.\s+)?([^\r\n]+)/i', $text, $matches)) {
                    $customerName = trim($matches[1]);
                }

                // Parse Items
                $items = [];
                $lines = explode("\n", $text);
                foreach ($lines as $line) {
                    $line = trim($line);
                    // Format: Safaga Magic Moist Barier 1.000 Pcs Rp. 12.000 RP. 12.000.000
                    if (preg_match('/^(.+?)\s+([\d.,]+)\s+(Pcs|Dus|Box|Pack|Kg|Roll|Set|Unit)\s+(?:Rp\.?\s*)?([\d.,]+)\s+(?:Rp\.?\s*)?([\d.,]+)/i', $line, $itemMatches)) {
                        $name = trim($itemMatches[1]);
                        
                        $cleanQty = function($val) {
                            $val = trim($val);
                            if (strpos($val, '.') !== false && strpos($val, ',') === false) {
                                $val = str_replace('.', '', $val);
                            } elseif (strpos($val, ',') !== false) {
                                $val = str_replace('.', '', $val);
                                $val = str_replace(',', '.', $val);
                            }
                            return floatval($val);
                        };

                        $qty = $cleanQty($itemMatches[2]);
                        $unit = trim($itemMatches[3]);
                        $price = $cleanQty($itemMatches[4]);
                        $subtotal = $cleanQty($itemMatches[5]);

                        $items[] = [
                            'name' => $name,
                            'qty' => $qty,
                            'unit' => $unit,
                            'price' => $price,
                            'subtotal' => $subtotal
                        ];
                    }
                }

                $file->move($onboardingPath, $filename);
                $pdfUrl = asset('uploads/onboarding/' . $filename);

                return redirect()->route('onboarding.verify', ['pdf_url' => $pdfUrl])
                    ->with('parsed_data', [
                        'invoice_number' => $invoiceNumber,
                        'date' => $invoiceDate,
                        'customer_name' => $customerName,
                        'customer_name_from_file' => $customerNameFromFile,
                        'items' => $items
                    ]);
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal memproses file PDF: ' . $e->getMessage());
            }
        }
    }

    public function verify(Request $request)
    {
        $pdfUrl = $request->query('pdf_url');
        if (empty($pdfUrl)) {
            return redirect()->route('onboarding.index')->with('error', 'PDF URL tidak valid.');
        }

        $customers = Customer::all();
        
        $parsedData = session('parsed_data');

        // On-the-fly PDF text extraction fallback if session flash got lost (e.g. cookie/host redirects)
        if (empty($parsedData)) {
            try {
                $filename = basename($pdfUrl);
                $localFilePath = public_path('uploads/onboarding/' . $filename);

                // Extract customer name from stored filename
                // Stored filename pattern: {timestamp}_Invoice_322_Safaga.pdf
                $customerNameFromFile = '';
                $storedBase = pathinfo($filename, PATHINFO_FILENAME); // e.g. "1779551861_Invoice_322_Safaga"
                if (preg_match('/\d+_Invoice\s*_?\d+_(.+)/i', $storedBase, $fnMatches)) {
                    $customerNameFromFile = str_replace('_', ' ', trim($fnMatches[1]));
                } elseif (preg_match('/\d+_(.+)/i', $storedBase, $fnMatches)) {
                    $customerNameFromFile = str_replace('_', ' ', trim($fnMatches[1]));
                }

                if (file_exists($localFilePath)) {
                    $parser = new Parser();
                    $pdf = $parser->parseFile($localFilePath);
                    $text = $pdf->getText();

                    // Parse Invoice Number
                    $invoiceNumber = '';
                    if (preg_match('/INVOICE\s*:\s*([^\r\n]+)/i', $text, $matches)) {
                        $invoiceNumber = trim($matches[1]);
                    }

                    // Parse Date
                    $invoiceDate = date('Y-m-d');
                    if (preg_match('/DATE\s*:\s*([^\r\n]+)/i', $text, $matches)) {
                        $dateStr = trim($matches[1]);
                        if (preg_match('/(\d{2})[\/\.-](\d{2})[\/\.-](\d{4})/', $dateStr, $dateParts)) {
                            $invoiceDate = "{$dateParts[3]}-{$dateParts[2]}-{$dateParts[1]}"; // Y-m-d
                        }
                    }

                    // Format invoiceNumber as INV-YYYY-MM-XXXXX
                    if ($invoiceNumber && is_numeric($invoiceNumber)) {
                        $dateYear = date('Y', strtotime($invoiceDate));
                        $dateMonth = date('m', strtotime($invoiceDate));
                        $invoiceNumber = "INV-{$dateYear}-{$dateMonth}-" . str_pad($invoiceNumber, 5, '0', STR_PAD_LEFT);
                    }

                    // Parse Customer
                    $customerName = '';
                    if (preg_match('/KEPADA\s+(?:Up\.\s+)?([^\r\n]+)/i', $text, $matches)) {
                        $customerName = trim($matches[1]);
                    }

                    // Parse Items
                    $items = [];
                    $lines = explode("\n", $text);
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (preg_match('/^(.+?)\s+([\d.,]+)\s+(Pcs|Dus|Box|Pack|Kg|Roll|Set|Unit)\s+(?:Rp\.?\s*)?([\d.,]+)\s+(?:Rp\.?\s*)?([\d.,]+)/i', $line, $itemMatches)) {
                            $name = trim($itemMatches[1]);
                            
                            $cleanQty = function($val) {
                                $val = trim($val);
                                if (strpos($val, '.') !== false && strpos($val, ',') === false) {
                                    $val = str_replace('.', '', $val);
                                } elseif (strpos($val, ',') !== false) {
                                    $val = str_replace('.', '', $val);
                                    $val = str_replace(',', '.', $val);
                                }
                                return floatval($val);
                            };

                            $qty = $cleanQty($itemMatches[2]);
                            $unit = trim($itemMatches[3]);
                            $price = $cleanQty($itemMatches[4]);
                            $subtotal = $cleanQty($itemMatches[5]);

                            $items[] = [
                                'name' => $name,
                                'qty' => $qty,
                                'unit' => $unit,
                                'price' => $price,
                                'subtotal' => $subtotal
                            ];
                        }
                    }

                    $parsedData = [
                        'invoice_number' => $invoiceNumber,
                        'date' => $invoiceDate,
                        'customer_name' => $customerName,
                        'customer_name_from_file' => $customerNameFromFile,
                        'items' => $items
                    ];
                }
            } catch (\Exception $e) {
                logger('Failed to parse PDF on-the-fly: ' . $e->getMessage());
            }
        }
        
        if (empty($parsedData)) {
            $parsedData = [
                'invoice_number' => '',
                'date' => date('Y-m-d'),
                'customer_name' => '',
                'customer_name_from_file' => '',
                'items' => []
            ];
        }

        $invoiceNumber = $parsedData['invoice_number'];
        if (empty($invoiceNumber)) {
            // Generate automatic fallback
            $year = date('Y');
            $month = date('m');
            $lastInvoice = Invoice::whereYear('date', $year)->whereMonth('date', $month)->latest()->first();
            if ($lastInvoice) {
                $lastNum = intval(substr($lastInvoice->invoice_number, -4));
                $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '0001';
            }
            $invoiceNumber = "INV-{$year}-{$month}-{$nextNum}";
        }

        return view('onboarding.verify', compact('pdfUrl', 'customers', 'invoiceNumber', 'parsedData'));
    }
}
