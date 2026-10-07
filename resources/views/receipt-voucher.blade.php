<x-app-layout>
    <style>
        @media print {
            nav, .no-print { display: none !important; }
            body, .print-bg { background: #fff !important; }
            .print-card { box-shadow: none !important; border: none !important; margin: 0 !important; padding: 0 !important; }
        }
        .dashed-line {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
    </style>

    <?php
    function numberToWords($num) {
        $ones = array(
            0 => "Zero", 1 => "One", 2 => "Two", 3 => "Three", 4 => "Four", 5 => "Five", 6 => "Six", 7 => "Seven", 8 => "Eight", 9 => "Nine",
            10 => "Ten", 11 => "Eleven", 12 => "Twelve", 13 => "Thirteen", 14 => "Fourteen", 15 => "Fifteen", 16 => "Sixteen", 17 => "Seventeen", 18 => "Eighteen", 19 => "Nineteen"
        );
        $tens = array(
            0 => "Zero", 1 => "Ten", 2 => "Twenty", 3 => "Thirty", 4 => "Forty", 5 => "Fifty", 6 => "Sixty", 7 => "Seventy", 8 => "Eighty", 9 => "Ninety"
        );
        $hundreds = array(
            "Hundred", "Thousand", "Million", "Billion", "Trillion", "Quadrillion"
        );
        $num = number_format($num,2,".",",");
        $num_arr = explode(".",$num);
        $wholenum = $num_arr[0];
        $decnum = $num_arr[1];
        $whole_arr = array_reverse(explode(",",$wholenum));
        krsort($whole_arr);
        $rettxt = "";
        foreach($whole_arr as $key => $i) {
            if($i < 20) {
                $rettxt .= $ones[(int)$i];
            } elseif($i < 100) {
                $rettxt .= $tens[substr($i,0,1)];
                $rettxt .= " ".$ones[substr($i,1,1)];
            } else {
                $rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0];
                $rettxt .= " ".$tens[substr($i,1,1)];
                $rettxt .= " ".$ones[substr($i,2,1)];
            }
            if($key > 0) {
                $rettxt .= " ".$hundreds[$key]." ";
            }
        }
        if($decnum > 0) {
            $rettxt .= " and ";
            if($decnum < 20) {
                $rettxt .= $ones[(int)$decnum];
            } elseif($decnum < 100) {
                $rettxt .= $tens[substr($decnum,0,1)];
                $rettxt .= " ".$ones[substr($decnum,1,1)];
            }
            $rettxt .= " Paisa";
        }
        return $rettxt;
    }

    $currentBalance = $receipt->customer ? $receipt->customer->closing_balance : 0;
    // Since receipt is already added to db, previous balance = current balance + receipt amount
    $previousBalance = $currentBalance + $receipt->amount;
    
    $formatBalance = function($amount) {
        if ($amount > 0) return number_format($amount, 2) . " Dr";
        if ($amount < 0) return number_format(abs($amount), 2) . " Cr";
        return "0.00";
    };
    ?>

    <div class="py-8 bg-gray-50 print-bg flex justify-center">
        <div class="w-full px-4" style="max-width: 820px;">

            <div class="no-print flex justify-between items-center mb-4">
                <a href="{{ route('receipt') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition-colors text-sm font-medium">
                    <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Receipts
                </a>
                <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm text-sm">Print Voucher</button>
            </div>

            <div class="print-card bg-white shadow-sm rounded-xl p-8" style="font-family: Arial, sans-serif; color: #000;">
                
                <!-- Header part -->
                <div class="flex items-center justify-start gap-4 mb-6">
                    <div class="flex-shrink-0">
                        <svg viewBox="0 0 170 70" style="height: 58px; width: auto;" xmlns="http://www.w3.org/2000/svg">
                            <!-- Top Red Swoosh -->
                            <path d="M 12,50 C 18,18 48,4 88,6 C 62,10 32,24 26,50 Z" fill="#d32f2f" />
                            <!-- Bottom Green Swoosh -->
                            <path d="M 22,51 C 28,30 54,16 95,18 C 70,22 45,34 38,51 Z" fill="#2e7d32" />
                            <!-- Green Acres Text -->
                            <text x="56" y="32" font-family="'Times New Roman', Georgia, serif" font-weight="900" font-size="24" fill="#1b4d24" letter-spacing="-0.5">Green</text>
                            <text x="58" y="50" font-family="'Times New Roman', Georgia, serif" font-weight="900" font-size="20" fill="#1b4d24" letter-spacing="-0.5">Acres</text>
                            <text x="14" y="64" font-family="'Times New Roman', Georgia, serif" font-size="10.5" font-weight="700" fill="#2d5a37" letter-spacing="0.3">Housing Scheme</text>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold" style="color: #111827; font-family: 'Times New Roman', Georgia, serif; font-size: 26px; font-weight: 900; letter-spacing: -0.2px;">Green Acres Housing Scheme, Mardan</h1>
                    </div>
                </div>

                <div class="border border-black rounded text-center py-1 mb-4 font-bold text-lg uppercase tracking-wide">
                    RECEIPT VOUCHER
                </div>

                <div class="dashed-line"></div>

                <div class="flex justify-between font-medium mb-1">
                    <div>Voucher No: <span class="font-normal">RVID-{{ str_pad($receipt->id, 3, '0', STR_PAD_LEFT) }}</span></div>
                    <div>{{ \Carbon\Carbon::parse($receipt->receipt_date)->format('d/m/Y') }}</div>
                </div>
                <div class="font-bold mb-2">
                    Received From: <span class="font-normal">{{ $receipt->customer->name ?? '-' }}</span>
                </div>

                <div class="dashed-line"></div>
                <div class="dashed-line" style="margin-top:-6px;"></div>

                <table class="w-full font-medium mb-2">
                    <thead>
                        <tr>
                            <th class="text-left w-10">#</th>
                            <th class="text-left">Account</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="pt-2">1</td>
                            <td class="pt-2">-</td>
                            <td class="pt-2 text-right font-bold">{{ number_format($receipt->amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="italic mt-4 mb-2">
                    In Words: <span class="font-bold">{{ numberToWords($receipt->amount) }} Only</span>
                </div>

                <div class="dashed-line"></div>

                <div class="flex justify-between items-center py-1">
                    <span>Previous Balance</span>
                    <span>{{ $formatBalance($previousBalance) }}</span>
                </div>
                <div class="dashed-line my-1"></div>
                <div class="flex justify-between items-center py-1 font-bold">
                    <span>Amount Received (-)</span>
                    <span>{{ number_format($receipt->amount, 2) }} Cr</span>
                </div>
                <div class="dashed-line my-1"></div>
                <div class="flex justify-between items-center py-1 font-bold text-lg">
                    <span>Balance Remaining</span>
                    <span>{{ $formatBalance($currentBalance) }}</span>
                </div>
                <div class="dashed-line mt-1"></div>
                <div class="dashed-line" style="margin-top:-6px;"></div>

            </div>
        </div>
    </div>
</x-app-layout>
