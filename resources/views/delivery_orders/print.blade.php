<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cetak Surat Jalan - {{ $deliveryOrder->sj_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            margin: 20px;
        }
        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            background-image: url('{{ asset("assets/brand/logo-watermark.png") }}');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: 55%;
        }
        .header {
            width: 100%;
            border-collapse: collapse;
        }
        .header td {
            vertical-align: top;
        }
        .company-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .sj-title {
            font-size: 20px;
            font-weight: bold;
            text-align: right;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .meta-table td {
            vertical-align: top;
            padding: 3px 0;
        }
        .meta-label {
            font-weight: bold;
            width: 100px;
        }
        .meta-value {
            width: 300px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items-table th {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            font-weight: bold;
            text-align: left;
            padding: 6px 2px;
        }
        .items-table td {
            padding: 6px 2px;
            vertical-align: top;
            border-bottom: 1px dotted #ccc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .signature-section {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        .signature-section td {
            width: 33%;
            text-align: center;
            vertical-align: top;
        }
        .signature-space {
            height: 60px;
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
        }
    </style>
</head>
<body>
    <!-- Print Helper Button -->
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 12px; font-weight: bold; cursor: pointer;">Cetak Sekarang</button>
        <button onclick="window.close()" style="padding: 6px 12px; cursor: pointer; margin-left: 5px;">Tutup Halaman</button>
    </div>

    <div class="container">
        <!-- Top Header -->
        <table class="header">
            <tr>
                <td>
                    <span class="company-title">{{ $settings['company_name'] }}</span><br>
                    <span>
                        {{ $settings['company_address'] }}<br>
                        Telp: {{ $settings['company_phone'] }}
                    </span>
                </td>
                <td style="text-align: right;">
                    <span class="sj-title">SURAT JALAN</span><br>
                    <span style="font-size: 14px; font-weight: bold;">No: {{ $deliveryOrder->sj_number }}</span>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- Metadata Details -->
        <table class="meta-table">
            <tr>
                <td class="meta-label">Penerima Yth:</td>
                <td class="meta-value">
                    <strong>{{ $deliveryOrder->customer->name }}</strong>
                </td>
                <td class="meta-label" style="padding-left: 40px;">Tanggal:</td>
                <td class="meta-value">{{ $deliveryOrder->delivery_date->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="meta-label">Brand:</td>
                <td class="meta-value">{{ $deliveryOrder->customer->brand_name }}</td>
                <td class="meta-label" style="padding-left: 40px;">No. Invoice:</td>
                <td class="meta-value">
                    @if($deliveryOrder->invoice)
                        {{ $deliveryOrder->invoice->invoice_number }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        </table>

        <!-- Table Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 50%;">Nama Barang / Deskripsi</th>
                    <th style="width: 15%; text-align: center;">Satuan</th>
                    <th style="width: 15%; text-align: right;">Qty</th>
                    <th style="width: 15%; text-align: left; padding-left: 10px;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deliveryOrder->items as $idx => $item)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td class="text-center">{{ $item->unit }}</td>
                        <td class="text-right">{{ number_format($item->qty, 0, ',', '.') }}</td>
                        <td style="padding-left: 10px;">{{ $item->notes ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold; border-top: 1px dashed #000; padding: 6px 2px;">Total</td>
                    <td style="text-align: right; font-weight: bold; border-top: 1px dashed #000; padding: 6px 2px;">{{ number_format($deliveryOrder->items->sum('qty'), 0, ',', '.') }}</td>
                    <td style="border-top: 1px dashed #000; padding: 6px 2px;"></td>
                </tr>
            </tfoot>
        </table>

        <div style="font-size: 11px; italic; margin-bottom: 15px;">
            * Catatan: {{ $deliveryOrder->notes ?? 'Mohon diperiksa kembali dengan teliti saat serah terima barang.' }}
        </div>

        <!-- Footer Triple Signatures -->
        <table class="signature-section">
            <tr>
                <td>
                    <span>Penerima Barang,</span>
                    <div class="signature-space"></div>
                    <span>(......................)</span>
                </td>
                <td>
                    <span>Pengirim / Sopir,</span>
                    <div class="signature-space"></div>
                    <span>( {{ $deliveryOrder->sender_name ?? '......................' }} )</span>
                </td>
                <td>
                    <span>Hormat Kami,</span>
                    <div class="signature-space"></div>
                    <span style="font-weight: bold;">PT. TRI JAYA FAACOS</span>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
