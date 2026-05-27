<?php

require __DIR__ . '/../vendor/autoload.php';

use Smalot\PdfParser\Parser;

$pdfPath = __DIR__ . '/../public/onboarding/1779551861_Invoice_322_Safaga.pdf';

try {
    $parser = new Parser();
    $pdf = $parser->parseFile($pdfPath);
    $text = $pdf->getText();
    
    // Parse Invoice Number
    $invoiceNumber = '';
    if (preg_match('/INVOICE\s*:\s*([^\r\n]+)/i', $text, $matches)) {
        $invoiceNumber = trim($matches[1]);
    }

    // Parse Date
    $invoiceDate = '';
    if (preg_match('/DATE\s*:\s*([^\r\n]+)/i', $text, $matches)) {
        $dateStr = trim($matches[1]);
        if (preg_match('/(\d{2})[\/\.-](\d{2})[\/\.-](\d{4})/', $dateStr, $dateParts)) {
            $invoiceDate = "{$dateParts[3]}-{$dateParts[2]}-{$dateParts[1]}"; // Y-m-d
        }
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
        // Look for items with Qty and Unit
        // Example: Safaga Magic Moist Barier 1.000 Pcs Rp. 12.000 RP. 12.000.000
        if (preg_match('/^(.+?)\s+([\d.,]+)\s+(Pcs|Dus|Box|Pack|Kg|Roll|Set|Unit)\s+(?:Rp\.?\s*)?([\d.,]+)\s+(?:Rp\.?\s*)?([\d.,]+)/i', $line, $itemMatches)) {
            $name = trim($itemMatches[1]);
            $qty = cleanIndonesianNumber($itemMatches[2]);
            $unit = trim($itemMatches[3]);
            $price = cleanIndonesianNumber($itemMatches[4]);
            $subtotal = cleanIndonesianNumber($itemMatches[5]);
            
            $items[] = [
                'name' => $name,
                'qty' => $qty,
                'unit' => $unit,
                'price' => $price,
                'subtotal' => $subtotal
            ];
        }
    }

    echo "Parsed Invoice Number: $invoiceNumber\n";
    echo "Parsed Date: $invoiceDate\n";
    echo "Parsed Customer: $customerName\n";
    echo "Parsed Items:\n";
    print_r($items);

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

function cleanIndonesianNumber($val) {
    $val = str_replace('Rp', '', $val);
    $val = str_replace('RP', '', $val);
    $val = trim($val);
    if (strpos($val, '.') !== false && strpos($val, ',') === false) {
        $val = str_replace('.', '', $val);
    } elseif (strpos($val, ',') !== false) {
        $val = str_replace('.', '', $val);
        $val = str_replace(',', '.', $val);
    }
    return floatval($val);
}
