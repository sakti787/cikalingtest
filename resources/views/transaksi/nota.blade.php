<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Transaksi #{{ $transaction->transaction_id }} - Toko Rukun Jaya</title>
    @vite(['resources/css/app.css'])
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .receipt-card {
            background-color: #ffffff;
            width: 100%;
            max-width: 380px;
            margin: 2rem auto;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .receipt-separator {
            border-top: 1px dashed #cbd5e1;
            margin: 0.75rem 0;
        }

        .receipt-double-separator {
            border-top: 2px double #94a3b8;
            margin: 0.75rem 0;
        }

        @media print {
            body {
                background-color: #ffffff;
                color: #000000;
                margin: 0;
                padding: 0;
            }
            .receipt-card {
                box-shadow: none;
                border: none;
                margin: 0;
                padding: 10px;
                max-width: 100%;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="p-4 flex flex-col items-center justify-start min-h-screen">

    <!-- Screen-only Control Bar -->
    <div class="no-print w-full max-w-[380px] bg-slate-800 text-white rounded-xl p-4 mb-4 flex items-center justify-between shadow-md">
        <span class="text-xs font-semibold">Nota Transaksi</span>
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg cursor-pointer transition-all">
                Print
            </button>
            <button onclick="window.close()" class="bg-slate-600 hover:bg-slate-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg cursor-pointer transition-all">
                Tutup
            </button>
        </div>
    </div>

    <!-- Screen-only Close Hint -->
    <div class="no-print text-center text-xs text-slate-400 max-w-[380px] mb-2 leading-relaxed">
        Menutup halaman ini setelah print... Anda juga bisa menekan tombol <strong>Tutup</strong> di atas.
    </div>

    <!-- Receipt Container -->
    <div class="receipt-card text-xs">
        <!-- Header -->
        <div class="text-center">
            <div class="font-bold text-sm">══════════════════════════</div>
            <div class="font-bold text-base tracking-wider">TOKO RUKUN JAYA</div>
            <div class="text-slate-600">Jl. Magelang, Muntilan</div>
            <div class="font-bold text-sm">══════════════════════════</div>
        </div>

        <!-- Meta Info -->
        <div class="space-y-1 my-3 text-slate-700">
            <div class="flex justify-between">
                <span>ID Transaksi:</span>
                <span class="font-bold">#{{ $transaction->transaction_id }}</span>
            </div>
            <div class="flex justify-between">
                <span>Tanggal:</span>
                <span>{{ $transaction->transaction_date->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Kasir:</span>
                <span class="font-bold uppercase">{{ $transaction->kasir->username }}</span>
            </div>
        </div>

        <div class="receipt-separator"></div>

        <!-- Items -->
        <div class="space-y-3">
            <div class="grid grid-cols-12 gap-1 font-bold text-slate-800 uppercase tracking-wide">
                <div class="col-span-6">Nama</div>
                <div class="col-span-2 text-center">Qty</div>
                <div class="col-span-4 text-right">Subtotal</div>
            </div>
            
            <div class="receipt-separator"></div>

            @foreach($transaction->items as $item)
                <div class="grid grid-cols-12 gap-1 text-slate-700">
                    <div class="col-span-12 font-semibold text-slate-900">{{ $item->product->product_name }}</div>
                    <div class="col-span-6 pl-2 text-slate-500">
                        {{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                    </div>
                    <div class="col-span-2 text-center text-slate-600">
                        {{ $item->quantity }}
                    </div>
                    <div class="col-span-4 text-right font-bold">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="receipt-double-separator"></div>

        <!-- Total -->
        @php
            $itemsSubtotal = $transaction->items->sum('subtotal');
            $discount = $itemsSubtotal - $transaction->total_amount;
        @endphp

        @if($discount > 0)
            <div class="flex justify-between items-center text-slate-700 py-0.5">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($itemsSubtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center text-red-600 font-bold py-0.5">
                <span>Diskon Spesial:</span>
                <span>-Rp {{ number_format($discount, 0, ',', '.') }}</span>
            </div>
            <div class="receipt-separator"></div>
        @endif

        <div class="flex justify-between items-center font-bold text-sm text-slate-900 py-1">
            <span>TOTAL:</span>
            <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
        </div>

        <div class="receipt-double-separator"></div>

        <!-- Footer Note -->
        <div class="text-center text-slate-500 space-y-1 mt-3">
            <div>Terima kasih atas kunjungan Anda!</div>
            <div class="text-[10px] leading-tight">Barang yang sudah dibeli tidak dapat dikembalikan.</div>
        </div>
    </div>

    <script>
        window.onload = () => {
            // Trigger automatic printing on load
            window.print();
        }
    </script>
</body>
</html>
