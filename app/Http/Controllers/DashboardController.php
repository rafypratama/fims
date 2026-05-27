<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Employee dashboard - simple view with inline data
        if ($request->user()->isKaryawan()) {
            return view('dashboard');
        }

        // Admin dashboard - full analytics
        [$startDate, $endDate, $period, $periodLabel] = $this->resolvePeriod($request);
        $search = trim((string) $request->input('q', ''));

        $invoiceScope = Invoice::query()
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()]);

        $totalInvoiceAmount = (clone $invoiceScope)
            ->where('status', '!=', 'Dibatalkan')
            ->sum('grand_total');

        $paidAmount = (clone $invoiceScope)
            ->where('status', 'Lunas')
            ->sum('grand_total');

        $unpaidStatuses = ['Draft', 'Dikirim', 'Dibayar Sebagian'];
        $unpaidAmount = (clone $invoiceScope)
            ->whereIn('status', $unpaidStatuses)
            ->sum('grand_total');

        $overdueAmount = (clone $invoiceScope)
            ->where(function ($query) {
                $query->where('status', 'Jatuh Tempo')
                    ->orWhere(function ($dueQuery) {
                        $dueQuery->whereNotIn('status', ['Lunas', 'Dibatalkan'])
                            ->whereNotNull('due_date')
                            ->whereDate('due_date', '<', Carbon::today());
                    });
            })
            ->sum('grand_total');

        $invoiceCount = (clone $invoiceScope)->where('status', '!=', 'Dibatalkan')->count();
        $paidCount = (clone $invoiceScope)->where('status', 'Lunas')->count();
        $unpaidCount = (clone $invoiceScope)->whereIn('status', $unpaidStatuses)->count();
        $overdueCount = (clone $invoiceScope)
            ->where(function ($query) {
                $query->where('status', 'Jatuh Tempo')
                    ->orWhere(function ($dueQuery) {
                        $dueQuery->whereNotIn('status', ['Lunas', 'Dibatalkan'])
                            ->whereNotNull('due_date')
                            ->whereDate('due_date', '<', Carbon::today());
                    });
            })
            ->count();

        $activeCustomers = Customer::where('status', true)->count();
        $newCustomers = Customer::whereBetween('created_at', [$startDate, $endDate])->count();
        $previousTotal = $this->previousPeriodInvoiceTotal($startDate, $endDate);

        $stats = [
            'invoice_amount' => $totalInvoiceAmount,
            'invoice_growth' => $this->percentageChange((float) $totalInvoiceAmount, $previousTotal),
            'invoice_count' => $invoiceCount,
            'unpaid_amount' => $unpaidAmount,
            'unpaid_count' => $unpaidCount,
            'overdue_amount' => $overdueAmount,
            'overdue_count' => $overdueCount,
            'active_customers' => $activeCustomers,
            'new_customers' => $newCustomers,
            'products_count' => Product::count(),
            'delivery_orders_count' => DeliveryOrder::whereBetween('delivery_date', [$startDate->toDateString(), $endDate->toDateString()])->count(),
        ];

        $monthlySales = $this->monthlySales($startDate, $endDate);

        $statusDistribution = [
            [
                'label' => 'Dibayar',
                'count' => $paidCount,
                'amount' => $paidAmount,
                'percent' => $invoiceCount > 0 ? round(($paidCount / $invoiceCount) * 100) : 0,
                'bar' => 'bg-emerald-500',
                'text' => 'text-emerald-600',
                'href' => route('invoices.index', ['status' => 'Lunas']),
            ],
            [
                'label' => 'Belum Dibayar',
                'count' => $unpaidCount,
                'amount' => $unpaidAmount,
                'percent' => $invoiceCount > 0 ? round(($unpaidCount / $invoiceCount) * 100) : 0,
                'bar' => 'bg-amber-500',
                'text' => 'text-amber-600',
                'href' => route('invoices.index', ['status_group' => 'unpaid']),
            ],
            [
                'label' => 'Jatuh Tempo',
                'count' => $overdueCount,
                'amount' => $overdueAmount,
                'percent' => $invoiceCount > 0 ? round(($overdueCount / $invoiceCount) * 100) : 0,
                'bar' => 'bg-rose-500',
                'text' => 'text-rose-600',
                'href' => route('invoices.index', ['status' => 'Jatuh Tempo']),
            ],
        ];

        $paidRatio = $invoiceCount > 0 ? round(($paidCount / $invoiceCount) * 100) : 0;
        $billingHealth = $paidRatio >= 70 ? 'Sehat' : ($paidRatio >= 40 ? 'Perlu Dipantau' : 'Risiko');

        return view('dashboard-admin', [
            'stats' => $stats,
            'period' => $period,
            'periodLabel' => $periodLabel,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'search' => $search,
            'searchResults' => $this->searchResults($search),
            'monthlySales' => $monthlySales,
            'statusDistribution' => $statusDistribution,
            'billingHealth' => $billingHealth,
            'paidRatio' => $paidRatio,
            'recentActivities' => $this->recentActivities(6),
            'topCustomers' => $this->topCustomers($startDate, $endDate),
            'quickLinks' => $this->quickLinks($request),
        ]);
    }

    public function downloadReport(Request $request)
    {
        [$startDate, $endDate] = $this->resolvePeriod($request);
        $fileName = 'fims-laporan-invoice-'.$startDate->format('Ymd').'-'.$endDate->format('Ymd').'.csv';

        $invoices = Invoice::with(['customer', 'creator'])
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('date')
            ->get();

        return response()->streamDownload(function () use ($invoices, $startDate, $endDate) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Laporan Invoice FIMS']);
            fputcsv($output, ['Periode', $startDate->format('d/m/Y').' - '.$endDate->format('d/m/Y')]);
            fputcsv($output, []);
            fputcsv($output, ['Tanggal', 'No Invoice', 'Customer', 'Brand', 'Status', 'Jatuh Tempo', 'Total', 'Dibuat Oleh']);

            foreach ($invoices as $invoice) {
                fputcsv($output, [
                    optional($invoice->date)->format('d/m/Y'),
                    $invoice->invoice_number,
                    optional($invoice->customer)->name,
                    optional($invoice->customer)->brand_name,
                    $invoice->status,
                    optional($invoice->due_date)->format('d/m/Y'),
                    (float) $invoice->grand_total,
                    optional($invoice->creator)->name ?? 'Sistem',
                ]);
            }

            fclose($output);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function downloadActivityLog()
    {
        $fileName = 'fims-activity-log-'.now()->format('Ymd-His').'.csv';
        $activities = $this->recentActivities(100);

        return response()->streamDownload(function () use ($activities) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Waktu', 'Pengguna', 'Aksi', 'Target', 'Status']);

            foreach ($activities as $activity) {
                fputcsv($output, [
                    optional($activity['time'])->format('d/m/Y H:i'),
                    $activity['user'],
                    $activity['action'],
                    $activity['target'],
                    $activity['status'],
                ]);
            }

            fclose($output);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function masterData()
    {
        $customersCount = Customer::count();
        $productsCount = Product::count();
        $banksCount = \App\Models\BankAccount::count();

        return view('master_data.index', compact('customersCount', 'productsCount', 'banksCount'));
    }

    private function resolvePeriod(Request $request): array
    {
        $period = $request->input('period', 'year');
        $today = Carbon::today();

        if ($period === 'month') {
            $startDate = $today->copy()->startOfMonth();
            $endDate = $today->copy()->endOfMonth();
            $periodLabel = 'Bulan Ini';
        } elseif ($period === 'custom') {
            $startDate = $request->filled('start_date') ? Carbon::parse($request->input('start_date')) : $today->copy()->startOfYear();
            $endDate = $request->filled('end_date') ? Carbon::parse($request->input('end_date')) : $today->copy()->endOfMonth();
            
            // Format dynamic range label for custom date, e.g. "01 Jan 2026 - 15 Mei 2026"
            $periodLabel = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
        } else {
            $period = 'year';
            // YTD: January of current year up to end of the current month
            $startDate = $today->copy()->startOfYear();
            $endDate = $today->copy()->endOfMonth();
            
            $periodLabel = $this->monthName($startDate->month).' '.$startDate->year.' - '.$this->monthName($endDate->month).' '.$endDate->year;
        }

        if ($startDate->greaterThan($endDate)) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        return [$startDate->startOfDay(), $endDate->endOfDay(), $period, $periodLabel];
    }

    private function previousPeriodInvoiceTotal(Carbon $startDate, Carbon $endDate): float
    {
        $days = $startDate->diffInDays($endDate) + 1;
        $previousEnd = $startDate->copy()->subDay()->endOfDay();
        $previousStart = $previousEnd->copy()->subDays($days - 1)->startOfDay();

        return (float) Invoice::whereBetween('date', [$previousStart->toDateString(), $previousEnd->toDateString()])
            ->where('status', '!=', 'Dibatalkan')
            ->sum('grand_total');
    }

    private function percentageChange(float $current, float $previous): float
    {
        if ($previous <= 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function monthlySales(Carbon $startDate, Carbon $endDate): array
    {
        $rows = Invoice::select(
                DB::raw('YEAR(date) as invoice_year'),
                DB::raw('MONTH(date) as invoice_month'),
                DB::raw('COUNT(*) as invoices_count'),
                DB::raw('SUM(grand_total) as total')
            )
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('status', '!=', 'Dibatalkan')
            ->groupBy('invoice_year', 'invoice_month')
            ->get()
            ->keyBy(fn ($row) => $row->invoice_year.'-'.str_pad((string) $row->invoice_month, 2, '0', STR_PAD_LEFT));

        $points = [];
        $cursor = $startDate->copy()->startOfMonth();
        $end = $endDate->copy()->startOfMonth();

        while ($cursor->lessThanOrEqualTo($end)) {
            $key = $cursor->year.'-'.str_pad((string) $cursor->month, 2, '0', STR_PAD_LEFT);
            $row = $rows->get($key);
            $monthStart = $cursor->copy()->startOfMonth();
            $monthEnd = $cursor->copy()->endOfMonth();

            $points[] = [
                'label' => $this->monthName($cursor->month),
                'month' => $cursor->format('Y-m'),
                'total' => (float) optional($row)->total,
                'count' => (int) optional($row)->invoices_count,
                'href' => route('invoices.index', [
                    'date_from' => $monthStart->toDateString(),
                    'date_to' => $monthEnd->toDateString(),
                ]),
                'is_current' => $cursor->isSameMonth(Carbon::today()),
            ];
            $cursor->addMonth();
        }

        $max = max(1, max(array_column($points, 'total') ?: [1]));

        return array_map(function ($point) use ($max) {
            $point['percent'] = $point['total'] > 0 ? max(8, round(($point['total'] / $max) * 100)) : 2;
            return $point;
        }, $points);
    }

    private function topCustomers(Carbon $startDate, Carbon $endDate)
    {
        return Invoice::with('customer')
            ->select('customer_id', DB::raw('COUNT(*) as invoices_count'), DB::raw('SUM(grand_total) as total_revenue'))
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('status', '!=', 'Dibatalkan')
            ->groupBy('customer_id')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get()
            ->filter(fn ($row) => $row->customer)
            ->values()
            ->map(function ($row) {
                return [
                    'customer' => $row->customer,
                    'initials' => $this->initials($row->customer->name),
                    'invoices_count' => $row->invoices_count,
                    'total_revenue' => (float) $row->total_revenue,
                    'href' => route('customers.index', ['search' => $row->customer->name]),
                ];
            });
    }

    private function recentActivities(int $limit)
    {
        $invoices = Invoice::with(['customer', 'creator'])->latest('updated_at')->limit($limit)->get()->map(function ($invoice) {
            $isUpdate = $invoice->updated_at && $invoice->created_at && $invoice->updated_at->gt($invoice->created_at->copy()->addMinute());

            return [
                'time' => $invoice->updated_at ?? $invoice->created_at,
                'user' => optional($invoice->creator)->name ?? 'Sistem',
                'initials' => $this->initials(optional($invoice->creator)->name ?? 'Sistem'),
                'action' => $isUpdate ? 'Memperbarui Invoice' : 'Membuat Invoice Baru',
                'target' => $invoice->invoice_number,
                'target_subtitle' => optional($invoice->customer)->name,
                'href' => route('invoices.show', $invoice),
                'status' => $invoice->status,
                'status_class' => $this->statusClass($invoice->status),
            ];
        });

        $deliveryOrders = DeliveryOrder::with(['customer', 'creator'])->latest('updated_at')->limit($limit)->get()->map(function ($deliveryOrder) {
            return [
                'time' => $deliveryOrder->updated_at ?? $deliveryOrder->created_at,
                'user' => optional($deliveryOrder->creator)->name ?? 'Sistem',
                'initials' => $this->initials(optional($deliveryOrder->creator)->name ?? 'Sistem'),
                'action' => 'Menerbitkan Surat Jalan',
                'target' => $deliveryOrder->sj_number,
                'target_subtitle' => optional($deliveryOrder->customer)->name,
                'href' => route('delivery-orders.show', $deliveryOrder),
                'status' => 'Selesai',
                'status_class' => 'bg-emerald-50 text-emerald-700',
            ];
        });

        $customers = Customer::latest('updated_at')->limit($limit)->get()->map(function ($customer) {
            return [
                'time' => $customer->updated_at ?? $customer->created_at,
                'user' => 'Admin',
                'initials' => 'AD',
                'action' => 'Update Profile Customer',
                'target' => $customer->name,
                'target_subtitle' => $customer->brand_name,
                'href' => route('customers.edit', $customer),
                'status' => $customer->status ? 'Aktif' : 'Nonaktif',
                'status_class' => $customer->status ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600',
            ];
        });

        return $invoices
            ->concat($deliveryOrders)
            ->concat($customers)
            ->sortByDesc('time')
            ->take($limit)
            ->values();
    }

    private function searchResults(string $search): array
    {
        if ($search === '') {
            return [];
        }

        return [
            [
                'label' => 'Invoice',
                'items' => Invoice::with('customer')
                    ->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('brand_name', 'like', "%{$search}%");
                    })
                    ->limit(4)
                    ->get()
                    ->map(fn ($invoice) => [
                        'title' => $invoice->invoice_number,
                        'subtitle' => optional($invoice->customer)->name.' - '.$invoice->status,
                        'href' => route('invoices.show', $invoice),
                    ]),
            ],
            [
                'label' => 'Customer',
                'items' => Customer::where('name', 'like', "%{$search}%")
                    ->orWhere('brand_name', 'like', "%{$search}%")
                    ->limit(4)
                    ->get()
                    ->map(fn ($customer) => [
                        'title' => $customer->name,
                        'subtitle' => $customer->brand_name,
                        'href' => route('customers.index', ['search' => $customer->name]),
                    ]),
            ],
            [
                'label' => 'Surat Jalan',
                'items' => DeliveryOrder::with('customer')
                    ->where('sj_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('brand_name', 'like', "%{$search}%");
                    })
                    ->limit(4)
                    ->get()
                    ->map(fn ($deliveryOrder) => [
                        'title' => $deliveryOrder->sj_number,
                        'subtitle' => optional($deliveryOrder->customer)->name,
                        'href' => route('delivery-orders.show', $deliveryOrder),
                    ]),
            ],
            [
                'label' => 'Produk',
                'items' => Product::with('customer')
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->limit(4)
                    ->get()
                    ->map(fn ($product) => [
                        'title' => $product->name,
                        'subtitle' => optional($product->customer)->name,
                        'href' => route('products.index', ['search' => $product->name]),
                    ]),
            ],
        ];
    }

    private function quickLinks(Request $request): array
    {
        $links = [
            ['label' => 'Invoice', 'description' => 'Kelola tagihan', 'href' => route('invoices.index'), 'color' => 'bg-indigo-50 text-indigo-700'],
            ['label' => 'Buat Invoice', 'description' => 'Transaksi baru', 'href' => route('invoices.create'), 'color' => 'bg-violet-50 text-violet-700'],
            ['label' => 'Surat Jalan', 'description' => 'Dokumen kirim', 'href' => route('delivery-orders.index'), 'color' => 'bg-sky-50 text-sky-700'],
            ['label' => 'Customer & Brand', 'description' => 'Data pelanggan', 'href' => route('customers.index'), 'color' => 'bg-emerald-50 text-emerald-700'],
            ['label' => 'Produk', 'description' => 'Katalog barang', 'href' => route('products.index'), 'color' => 'bg-amber-50 text-amber-700'],
            ['label' => 'Profil', 'description' => 'Akun pengguna', 'href' => route('profile.edit'), 'color' => 'bg-slate-100 text-slate-700'],
        ];

        if ($request->user()?->isAdmin()) {
            $links[] = ['label' => 'Master Data', 'description' => 'Hub data dasar', 'href' => route('master-data.index'), 'color' => 'bg-blue-50 text-blue-700'];
            $links[] = ['label' => 'Onboarding', 'description' => 'Import data', 'href' => route('onboarding.index'), 'color' => 'bg-fuchsia-50 text-fuchsia-700'];
            $links[] = ['label' => 'Pengaturan', 'description' => 'Sistem & bank', 'href' => route('settings.index'), 'color' => 'bg-rose-50 text-rose-700'];
        }

        return $links;
    }

    private function statusClass(string $status): string
    {
        return [
            'Draft' => 'bg-slate-100 text-slate-700',
            'Dikirim' => 'bg-indigo-50 text-indigo-700',
            'Dibayar Sebagian' => 'bg-blue-50 text-blue-700',
            'Lunas' => 'bg-emerald-50 text-emerald-700',
            'Jatuh Tempo' => 'bg-rose-50 text-rose-700',
            'Dibatalkan' => 'bg-slate-100 text-slate-500',
        ][$status] ?? 'bg-slate-100 text-slate-700';
    }

    private function initials(string $name): string
    {
        $words = preg_split('/\s+/', trim($name));
        $initials = '';

        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }

        return substr($initials ?: 'F', 0, 2);
    }

    private function monthName(int $month): string
    {
        return [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ][$month] ?? '';
    }
}
