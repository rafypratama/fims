<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cetak Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
            margin: 15px;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            background-image: url('{{ asset("assets/brand/logo-watermark.png") }}');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: 55%;
        }
        table {
            border-collapse: collapse;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
        
        @media print {
            body {
                margin: 0;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none;
            }
            @page {
                size: 241mm 140mm;
                margin: 5mm 8mm 5mm 8mm;
            }
        }
    </style>
</head>
<body>
    @php
        if (!function_exists('penyebut')) {
            function penyebut($nilai) {
                $nilai = abs($nilai);
                $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
                $temp = "";
                if ($nilai < 12) {
                    $temp = " ". $huruf[$nilai];
                } else if ($nilai < 20) {
                    $temp = penyebut($nilai - 10). " belas";
                } else if ($nilai < 100) {
                    $temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10);
                } else if ($nilai < 200) {
                    $temp = " seratus" . penyebut($nilai - 100);
                } else if ($nilai < 1000) {
                    $temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100);
                } else if ($nilai < 2000) {
                    $temp = " seribu" . penyebut($nilai - 1000);
                } else if ($nilai < 1000000) {
                    $temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000);
                } else if ($nilai < 1000000000) {
                    $temp = penyebut($nilai/1000000) . " juta" . penyebut($nilai % 1000000);
                } else if ($nilai < 1000000000000) {
                    $temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai,1000000000));
                } else if ($nilai < 1000000000000000) {
                    $temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai,1000000000000));
                }     
                return $temp;
            }
        }
     
        if (!function_exists('terbilang')) {
            function terbilang($nilai) {
                if($nilai<0) {
                    $hasil = "minus ". trim(penyebut($nilai));
                } else {
                    $hasil = trim(penyebut($nilai));
                }     
                return strtoupper($hasil) . " RUPIAH";
            }
        }

        $user = auth()->user() ?? $invoice->creator;
        $salesName = 'OFFICE';
        if ($user) {
            $roleName = $user->role === 'admin' ? 'ADMIN' : 'KARYAWAN';
            $cleanName = trim(str_ireplace(['admin', 'karyawan'], '', $user->name));
            $salesName = $roleName . ($cleanName ? ' ' . strtoupper($cleanName) : '');
        }
    @endphp

    <!-- Print Helper Button -->
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 12px; font-weight: bold; cursor: pointer;">Cetak Sekarang</button>
        <button onclick="window.close()" style="padding: 6px 12px; cursor: pointer; margin-left: 5px;">Tutup Halaman</button>
    </div>

    <div class="container">
        <!-- Top Columns Box Section -->
        <table style="width: 100%; border: none; margin: 0; padding: 0;">
            <tr>
                <!-- Left Side: Company Info & Ship To (aligned vertically) -->
                <td style="width: 38%; vertical-align: top; padding-right: 8px;">
                    <!-- Company Info Box -->
                    <table style="width: 100%; border: 1px solid #000; margin-bottom: 8px;">
                        <tr>
                            <td style="text-align: center; font-weight: bold; font-size: 13px; padding: 3px; border-bottom: 1px solid #000;">
                                {{ strtoupper($settings['company_name']) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size: 10px; padding: 4px; border-bottom: 1px solid #000; text-align: left; line-height: 1.2;">
                                {{ $settings['company_address'] }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size: 10px; padding: 3px; text-align: left;">
                                TELP: {{ $settings['company_phone'] }}
                            </td>
                        </tr>
                    </table>

                    <!-- Ship To Box -->
                    <table style="width: 100%; border: 1px solid #000;">
                        <tr>
                            <td style="font-size: 10px; padding: 2px 4px; border-bottom: 1px dashed #000; font-weight: bold; background-color: #fafafa;">
                                Ship To :
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; line-height: 1.25; font-size: 10px;">
                                <strong style="font-size: 11px;">{{ strtoupper($invoice->customer->name) }}</strong><br>
                                {{ $invoice->customer->address }}<br>
                                {{ $invoice->customer->phone }}
                            </td>
                        </tr>
                    </table>
                </td>

                <!-- Middle Side: Keterangan & Bank Accounts -->
                <td style="width: 32%; vertical-align: top; padding-right: 8px;">
                    <table style="width: 100%; border: 1px solid #000; height: 100%;">
                        <tr>
                            <td style="padding: 4px; font-size: 10px; border-bottom: 1px solid #000; line-height: 1.2;">
                                <strong>Keterangan :</strong>
                                <div style="font-size: 9px; margin-top: 2px; color: #000;">
                                    Nota Putih bukti pembayaran & Penagihan yang Sah
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 4px; font-size: 9px; vertical-align: top;">
                                <strong style="font-size: 10px; display: block; text-align: center; margin-bottom: 3px; border-bottom: 1px dashed #000; padding-bottom: 2px;">TRANSFER KE :</strong>
                                <div style="line-height: 1.25; text-align: left;">
                                    @foreach($bankAccounts as $bank)
                                        <strong>{{ strtoupper($bank->bank_name) }}</strong>: {{ $bank->account_number }}<br>
                                        A/N {{ strtoupper($bank->account_holder) }}<br>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>

                <!-- Right Side: Title INVOICE & Metadata table -->
                <td style="width: 30%; vertical-align: top;">
                    <div style="text-align: right; font-weight: bold; font-size: 20px; margin-bottom: 6px; padding-right: 4px; font-family: sans-serif;">
                        INVOICE
                    </div>
                    
                    <table style="width: 100%; border: 1px solid #000; font-size: 10px;">
                        <tr>
                            <td style="width: 38%; padding: 3px; border-right: 1px solid #000; border-bottom: 1px solid #000; font-weight: bold;">Invoice No.</td>
                            <td style="width: 62%; padding: 3px; border-bottom: 1px solid #000;">: {{ $invoice->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px; border-right: 1px solid #000; border-bottom: 1px solid #000; font-weight: bold;">Invoice Date</td>
                            <td style="padding: 3px; border-bottom: 1px solid #000;">: {{ $invoice->date->format('d-M-Y') }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px; border-right: 1px solid #000; border-bottom: 1px solid #000; font-weight: bold;">Tgl Jt. Tempo</td>
                            <td style="padding: 3px; border-bottom: 1px solid #000;">: {{ $invoice->due_date ? $invoice->due_date->format('d-M-Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px; border-right: 1px solid #000; border-bottom: 1px solid #000; font-weight: bold;">Pengiriman Via</td>
                            <td style="padding: 3px; border-bottom: 1px solid #000;">: -</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px; border-right: 1px solid #000; font-weight: bold;">Sales</td>
                            <td style="padding: 3px;">: {{ $salesName }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table style="width: 100%; border: 1px solid #000; margin-top: 8px; font-size: 10px;">
            <thead>
                <tr style="background-color: #fafafa;">
                    <th style="border: 1px solid #000; padding: 4px; text-align: left; width: 15%;">Item</th>
                    <th style="border: 1px solid #000; padding: 4px; text-align: left; width: 45%;">Item Description</th>
                    <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 8%;">Qty</th>
                    <th style="border: 1px solid #000; padding: 4px; text-align: right; width: 12%;">Unit Price</th>
                    <th style="border: 1px solid #000; padding: 4px; text-align: center; width: 8%;">Disc(%)</th>
                    <th style="border: 1px solid #000; padding: 4px; text-align: right; width: 12%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td style="border: 1px solid #000; padding: 4px; vertical-align: top;">
                            {{ optional($item->product)->code ?? '-' }}
                        </td>
                        <td style="border: 1px solid #000; padding: 4px; vertical-align: top; line-height: 1.2;">
                            {{ $item->product_name }}
                        </td>
                        <td style="border: 1px solid #000; padding: 4px; text-align: center; vertical-align: top;">
                            {{ number_format($item->qty, 0, ',', '.') }}
                        </td>
                        <td style="border: 1px solid #000; padding: 4px; text-align: right; vertical-align: top;">
                            {{ number_format($item->unit_price, 0, ',', '.') }}
                        </td>
                        <td style="border: 1px solid #000; padding: 4px; text-align: center; vertical-align: top;">
                            0
                        </td>
                        <td style="border: 1px solid #000; padding: 4px; text-align: right; vertical-align: top;">
                            {{ number_format($item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
                
                <!-- Say Row -->
                <tr>
                    <td style="border: 1px solid #000; padding: 4px; font-weight: bold; text-align: center;">Say</td>
                    <td colspan="5" style="border: 1px solid #000; padding: 4px; font-weight: bold; font-style: italic;">
                        {{ terbilang($invoice->grand_total) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Bottom Section: Terms Notes, Totals Table, Signatures -->
        <table style="width: 100%; border: none; margin-top: 8px; font-size: 9px;">
            <tr>
                <!-- Bottom Left: Note Box -->
                <td style="width: 55%; vertical-align: top; padding-right: 12px;">
                    <table style="width: 100%; border: 1px solid #000; line-height: 1.3;">
                        <tr>
                            <td style="padding: 4px; font-weight: bold; background-color: #fafafa; border-bottom: 1px dashed #000;">
                                Note :
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 4px; font-size: 8.5px;">
                                1. SEMUA BARANG YANG TERTULIS DI INVOICE INI BERSIFAT TITIPAN UNTUK DIJUAL.<br>
                                2. SETELAH INVOICE INI DITANDA TANGAN/CAP BERSAMA MERUPAKAN ALAT BUKTI PENAGIHAN DAN PEMBAYARAN YANG SAH DAN MENGIKAT SECARA HUKUM YANG BERLAKU.<br>
                                3. PEMBAYARAN MELALUI TRANSFER HANYA BERLAKU MELALUI NO REK YG TERTERA DI INVOICE DILUAR NO REK TERSEBUT PEMBAYARAN DIANGGAP TIDAK SAH.<br>
                                4. KLAIM BOTOL PECAH MAKSIMAL 1 BULAN SETELAH BARANG DITERIMA, *SBK BERLAKU.
                            </td>
                        </tr>
                    </table>
                </td>

                <!-- Bottom Right: Calculation Summary & Signatures -->
                <td style="width: 45%; vertical-align: top;">
                    <!-- Calculation Summary -->
                    <table style="width: 100%; font-size: 10px; font-weight: bold; margin-bottom: 8px;">
                        <tr>
                            <td style="border: 1px solid #000; padding: 3px; width: 45%;">Sub Total</td>
                            <td style="border: 1px solid #000; padding: 3px; width: 15%; text-align: center;">Rp.</td>
                            <td style="border: 1px solid #000; padding: 3px; width: 40%; text-align: right;">{{ number_format($invoice->subtotal, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 3px;">Nilai Tukar</td>
                            <td style="border: 1px solid #000; padding: 3px; text-align: center;">Rp.</td>
                            <td style="border: 1px solid #000; padding: 3px; text-align: right;">1,00</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 3px;">Nilai Pajak</td>
                            <td style="border: 1px solid #000; padding: 3px; text-align: center;">Rp.</td>
                            <td style="border: 1px solid #000; padding: 3px; text-align: right;">
                                {{ $invoice->use_ppn ? number_format($invoice->subtotal * 0.11, 2, ',', '.') : '0,00' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 3px;">By Kirim</td>
                            <td style="border: 1px solid #000; padding: 3px; text-align: center;">Rp.</td>
                            <td style="border: 1px solid #000; padding: 3px; text-align: right;">0,00</td>
                        </tr>
                        @if($invoice->dp_amount > 0)
                        <tr>
                            <td style="border: 1px solid #000; padding: 3px;">Uang Muka (DP)</td>
                            <td style="border: 1px solid #000; padding: 3px; text-align: center;">Rp.</td>
                            <td style="border: 1px solid #000; padding: 3px; text-align: right;">-{{ number_format($invoice->dp_amount, 2, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr style="font-size: 11px;">
                            <td style="border: 1px solid #000; padding: 3px; font-weight: bold; background-color: #eaeaea;">Total Invoice</td>
                            <td style="border: 1px solid #000; padding: 3px; font-weight: bold; text-align: center; background-color: #eaeaea;">Rp.</td>
                            <td style="border: 1px solid #000; padding: 3px; font-weight: bold; text-align: right; background-color: #eaeaea;">
                                {{ number_format($invoice->grand_total, 2, ',', '.') }}
                            </td>
                        </tr>
                    </table>

                    <!-- Signatures Section -->
                    <table style="width: 100%; font-size: 10px; margin-top: 10px; line-height: 1.3;">
                        <tr>
                            <td style="width: 50%; text-align: left; padding-left: 6px;">
                                Disiapkan Oleh :<br>
                                <div style="height: 34px;"></div>
                                Nama : PIC Faacos
                            </td>
                            <td style="width: 50%; text-align: left; padding-left: 6px;">
                                Diterima Oleh :<br>
                                <div style="height: 34px;"></div>
                                Nama : PIC {{ $invoice->customer->name }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- Automatically open print dialog -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            window.print();
        });
    </script>
</body>
</html>
