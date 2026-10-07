<x-app-layout>
    @php
        // Helper closure to convert number to words
        $numToWordsClosure = function ($num) use (&$numToWordsClosure) {
            $ones = [
                0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
                5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
                10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen',
                14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen',
                18 => 'Eighteen', 19 => 'Nineteen'
            ];
            $tens = [
                2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
                6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
            ];
            
            $num = (int)$num;
            if ($num === 0) return 'Zero';
            
            $str = '';
            if ($num >= 10000000) {
                $str .= $numToWordsClosure((int)($num / 10000000)) . ' Crore ';
                $num %= 10000000;
            }
            if ($num >= 100000) {
                $str .= $numToWordsClosure((int)($num / 100000)) . ' Lakh ';
                $num %= 100000;
            }
            if ($num >= 1000) {
                $str .= $numToWordsClosure((int)($num / 1000)) . ' Thousand ';
                $num %= 1000;
            }
            if ($num >= 100) {
                $str .= $numToWordsClosure((int)($num / 100)) . ' Hundred ';
                $num %= 100;
            }
            if ($num > 0) {
                if ($num < 20) {
                    $str .= $ones[$num];
                } else {
                    $str .= $tens[(int)($num / 10)];
                    if ($num % 10) {
                        $str .= ' ' . $ones[$num % 10];
                    }
                }
            }
            return trim($str);
        };

        $totalInvoiceAmount = floatval($amount);
        $amountInWords = trim($numToWordsClosure($totalInvoiceAmount)) . ' Rupees Only';

        // Gather only the selected services for this invoice
        $servicesList = collect();
        if (isset($services) && $services->count() > 0) {
            $servicesList = $services;
        } elseif (isset($invoice->services) && $invoice->services->count() > 0) {
            $servicesList = $invoice->services;
        } elseif ($service) {
            $servicesList = collect([$service]);
        } elseif (isset($invoice->service) && $invoice->service) {
            $servicesList = collect([$invoice->service]);
        }

        $serviceCount = $servicesList->count();

        // Prepare selected items with their respective amounts
        $items = [];
        $grandMonthly = 0;
        $grandArrears = 0;
        $grandLate = 0;
        $grandBills = 0;
        $grandPayable = 0;

        foreach ($servicesList as $s) {
            $monthly = floatval($s->pivot->monthly_charges ?? $s->pivot->amount ?? $s->amount ?? 0);
            $arrears = floatval($s->pivot->arrears ?? 0);
            $late = floatval($s->pivot->late_surcharge ?? 0);
            $bills = floatval($s->pivot->total_bills ?? $monthly + $arrears + $late);
            $payable = floatval($s->pivot->total_payable ?? $bills);

            $grandMonthly += $monthly;
            $grandArrears += $arrears;
            $grandLate += $late;
            $grandBills += $bills;
            $grandPayable += $payable;

            $items[] = [
                'name' => $s->name,
                'monthly' => $monthly,
                'arrears' => $arrears,
                'late' => $late,
                'bills' => $bills,
                'payable' => $payable,
            ];
        }

        if (empty($items)) {
            $items[] = [
                'name' => 'Service Charges',
                'monthly' => $totalInvoiceAmount,
                'arrears' => 0,
                'late' => 0,
                'bills' => $totalInvoiceAmount,
                'payable' => $totalInvoiceAmount,
            ];
            $grandMonthly = $grandBills = $grandPayable = $totalInvoiceAmount;
        }

        $totalTableRows = 12;
        $numItems = count($items);
        $rowsToRender = max($totalTableRows, $numItems);

        $formattedIssueDate = \Carbon\Carbon::parse($service_date)->format('d/m/Y');
        $billingMonthStr = \Carbon\Carbon::parse($service_date)->format('F');
        
        // Smart parse of address if available (e.g., "B-193, Sector B, Phase II")
        $rawAddress = trim($customer->address ?? '');
        
        $addressParts = explode(',', $rawAddress);
        $plotNo = trim($addressParts[0] ?? '');
        
        $sector = '';
        $streetNo = '';
        $phase = '';
        
        $category = $invoice->category ?? '';
        $type = $invoice->type ?? '';

        if (preg_match('/(plot|house|h\.no|p\.no)\s*[:#\-]?\s*([a-zA-Z0-9\-\/]+)/i', $rawAddress, $m)) {
            $plotNo = $m[2];
        }
        if (preg_match('/sector\s*[:#\-]?\s*([a-zA-Z0-9]+)/i', $rawAddress, $m)) {
            $sector = $m[1];
        }
        if (preg_match('/street\s*[:#\-]?\s*([a-zA-Z0-9]+)/i', $rawAddress, $m)) {
            $streetNo = $m[1];
        }
        if (preg_match('/phase\s*[:#\-]?\s*([a-zA-Z0-9]+)/i', $rawAddress, $m)) {
            $phase = $m[1];
        }
        
        // Due Date: use invoice due_date if provided, else fallback to 10th of month or 10 days
        $actualDueDate = $due_date ?? ($invoice->due_date ?? null);
        if ($actualDueDate) {
            $dueDateStr = \Carbon\Carbon::parse($actualDueDate)->format('d/m/Y');
        } else {
            $carbonDate = \Carbon\Carbon::parse($service_date);
            $dueDateStr = ($carbonDate->day <= 10) 
                ? $carbonDate->copy()->setDay(10)->format('d/m/Y')
                : $carbonDate->copy()->addDays(10)->format('d/m/Y');
        }
    @endphp

    <style>
        /* Main Container */
        .ga-wrapper {
            background-color: #f1f5f9;
            min-height: 100vh;
            padding: 30px 16px;
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
            color: #111827;
        }

        /* Top Action Bar (Hidden in Print) */
        .ga-action-bar {
            max-width: 820px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .ga-btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            background: #ffffff;
            color: #374151;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .ga-btn-back:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }
        .ga-btn-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 22px;
            background: #15803d;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(21, 128, 61, 0.25);
        }
        .ga-btn-print:hover {
            background: #166534;
            transform: translateY(-1px);
        }

        /* Printable Invoice Sheet */
        .ga-sheet {
            max-width: 820px;
            margin: 0 auto;
            background: #ffffff;
            border: 1.5px solid #222222;
            padding: 32px 36px 36px 36px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            position: relative;
            box-sizing: border-box;
        }

        /* Header Area */
        .ga-header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 16px;
            margin-bottom: 12px;
        }
        .ga-logo-wrap {
            flex-shrink: 0;
        }
        .ga-header-title-wrap {
            flex: 1;
        }
        .ga-main-title {
            font-family: 'Times New Roman', Georgia, serif;
            font-size: 26px;
            font-weight: 900;
            color: #111827;
            letter-spacing: -0.2px;
            line-height: 1.2;
            margin: 0;
        }

        /* Top 3 Meta Boxes Row */
        .ga-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 14px 0 18px 0;
            gap: 16px;
        }
        .ga-box-receipt {
            border: 1.5px solid #222222;
            border-radius: 3px;
            padding: 6px 12px;
            width: 250px;
            background: #fff;
        }
        .ga-receipt-line {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            font-size: 13.5px;
            line-height: 1.4;
        }
        .ga-receipt-line:first-child {
            margin-bottom: 4px;
        }
        .ga-receipt-label {
            font-weight: 600;
            color: #1f2937;
        }
        .ga-receipt-val {
            font-weight: 800;
            color: #000000;
            font-family: 'Times New Roman', Georgia, monospace;
            font-size: 15px;
        }

        /* Center INVOICE Banner */
        .ga-invoice-badge {
            background-color: #1e5b38;
            color: #ffffff;
            font-family: Arial, sans-serif;
            font-size: 21px;
            font-weight: 900;
            letter-spacing: 3px;
            text-transform: uppercase;
            padding: 8px 44px;
            border-radius: 4px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Right Customer Copy Box */
        .ga-box-copy {
            border: 1.5px solid #222222;
            border-radius: 4px;
            padding: 10px 24px;
            font-size: 15px;
            font-weight: 600;
            color: #111827;
            text-align: center;
            background: #fff;
        }

        /* Customer / Plot Details Section */
        .ga-details-section {
            margin-bottom: 12px;
        }
        .ga-details-row {
            display: flex;
            align-items: flex-end;
            margin-bottom: 7px;
            gap: 18px;
        }
        .ga-field {
            display: flex;
            align-items: flex-end;
            font-size: 13px;
        }
        .ga-field-label {
            font-weight: 600;
            color: #111827;
            white-space: nowrap;
            margin-right: 6px;
        }
        .ga-field-line {
            flex: 1;
            border-bottom: 1px solid #444444;
            min-height: 18px;
            padding: 0 4px 1px 6px;
            font-weight: 600;
            font-size: 13.5px;
            color: #000000;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Charges Table */
        .ga-table-wrap {
            margin-top: 10px;
        }
        .ga-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000000;
            font-size: 12px;
        }
        .ga-table th, .ga-table td {
            border: 1px solid #000000;
            padding: 3px 5px;
            vertical-align: middle;
        }
        .ga-table thead th {
            background-color: #ffffff;
            font-weight: 700;
            font-size: 11.5px;
            text-align: center;
            line-height: 1.25;
            padding: 6px 3px;
            color: #000000;
        }
        .ga-col-sr { width: 5.5%; text-align: center; }
        .ga-col-details { width: 37.5%; text-align: left; padding-left: 8px !important; }
        .ga-col-monthly { width: 13%; text-align: center; }
        .ga-col-arrears { width: 11%; text-align: center; }
        .ga-col-late { width: 12%; text-align: center; }
        .ga-col-bills { width: 10%; text-align: center; }
        .ga-col-payable { width: 11%; text-align: center; }

        .ga-table tbody tr {
            height: 24px;
        }
        .ga-table tbody td.ga-col-sr {
            text-align: center;
            font-weight: 500;
            color: #222;
        }
        .ga-table tbody td.ga-col-details {
            font-weight: 600;
            color: #111;
        }
        .ga-table tbody td.ga-col-monthly,
        .ga-table tbody td.ga-col-payable {
            font-weight: 700;
            font-size: 12.5px;
            color: #000;
        }

        /* Grand Total Row */
        .ga-row-grand-total td {
            font-weight: 800;
            height: 27px;
            background-color: #fafafa;
        }
        .ga-grand-total-label {
            text-align: right;
            padding-right: 14px !important;
            font-style: italic;
            font-size: 13px;
        }
        .ga-grand-total-val {
            font-size: 13.5px;
            font-weight: 900;
            text-align: center;
            border-bottom: 2px double #000000 !important;
        }

        /* Amount in Words Bar */
        .ga-words-bar {
            display: flex;
            align-items: baseline;
            border-left: 1.5px solid #000000;
            border-right: 1.5px solid #000000;
            border-bottom: 1.5px solid #000000;
            padding: 5px 8px;
            font-size: 12.5px;
            background: #ffffff;
        }
        .ga-words-label {
            font-style: italic;
            font-weight: 600;
            color: #111827;
            margin-right: 8px;
            white-space: nowrap;
        }
        .ga-words-text {
            font-weight: 700;
            color: #000000;
            border-bottom: 1px dotted #333333;
            flex: 1;
            padding-bottom: 1px;
            padding-left: 6px;
        }

        /* Notes Box */
        .ga-notes-box {
            border: 1px solid #000000;
            padding: 7px 10px;
            margin-top: 14px;
            background: #ffffff;
            font-size: 11px;
            line-height: 1.45;
            color: #111827;
        }
        .ga-notes-heading {
            font-weight: 800;
            font-style: italic;
            margin-bottom: 2px;
            font-size: 11.5px;
        }
        .ga-notes-line {
            margin-bottom: 2px;
        }
        .ga-notes-line:last-child {
            margin-bottom: 0;
        }

        /* Signatures Section (Clean lines, NO STAMPS, NO HAND SIGNATURES) */
        .ga-signatures {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 48px;
            padding: 0 24px 6px 24px;
        }
        .ga-sig-block {
            text-align: center;
            width: 200px;
        }
        .ga-sig-line {
            border-top: 1px solid #111111;
            width: 100%;
            margin-bottom: 5px;
        }
        .ga-sig-title {
            font-size: 12.5px;
            font-weight: 600;
            color: #111827;
        }

        /* Print Specific Rules */
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }
            body, html {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            body * {
                visibility: hidden;
            }
            nav, header, .ga-action-bar, .no-print, [role="navigation"] {
                display: none !important;
            }
            .ga-wrapper {
                background: transparent !important;
                padding: 0 !important;
                margin: 0 !important;
                min-height: auto !important;
            }
            .ga-sheet, .ga-sheet * {
                visibility: visible;
            }
            .ga-sheet {
                position: absolute;
                left: 10mm;
                top: 10mm;
                width: calc(100% - 20mm) !important;
                max-width: none !important;
                margin: 0 !important;
                border: 1.5px solid #000000 !important;
                box-shadow: none !important;
                padding: 20px 24px !important;
            }
            .ga-invoice-badge {
                background-color: #1e5b38 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color: #ffffff !important;
            }
        }
    </style>

    <div class="ga-wrapper">
        <!-- Top Action Bar -->
        <div class="ga-action-bar no-print">
            <a href="{{ route('create-bill') }}" class="ga-btn-back">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Bills
            </a>
            <button onclick="window.print()" class="ga-btn-print">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Invoice
            </button>
        </div>

        <!-- The Printable Invoice Sheet -->
        <div class="ga-sheet">
            
            <!-- Header: Logo & Name -->
            <div class="ga-header">
                <div class="ga-logo-wrap">
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
                <div class="ga-header-title-wrap">
                    <h1 class="ga-main-title">Green Acres Housing Scheme, Mardan</h1>
                </div>
            </div>

            <!-- Top Row: Receipt No/Date, INVOICE Badge, Customer Copy -->
            <div class="ga-meta-row">
                <div class="ga-box-receipt">
                    <div class="ga-receipt-line">
                        <span class="ga-receipt-label">Receipt No.</span>
                        <span class="ga-receipt-val">{{ str_pad($invoice->id, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="ga-receipt-line">
                        <span class="ga-receipt-label">Date:</span>
                        <span class="ga-receipt-val">{{ $formattedIssueDate }}</span>
                    </div>
                </div>

                <div class="ga-invoice-badge">
                    INVOICE
                </div>

                <div class="ga-box-copy">
                    Customer Copy
                </div>
            </div>

            <!-- Customer / Plot Details Form Lines -->
            <div class="ga-details-section">
                <!-- Line 1: Plot/House No., Category, Type -->
                <div class="ga-details-row">
                    <div class="ga-field" style="flex: 1.35;">
                        <span class="ga-field-label">Plot/House No.</span>
                        <span class="ga-field-line">{{ $plotNo }}</span>
                    </div>
                    <div class="ga-field" style="flex: 1;">
                        <span class="ga-field-label">Category</span>
                        <span class="ga-field-line">{{ $category }}</span>
                    </div>
                    <div class="ga-field" style="flex: 1;">
                        <span class="ga-field-label">Type</span>
                        <span class="ga-field-line">{{ $type }}</span>
                    </div>
                </div>

                <!-- Line 2: Sector, Street No., Phase -->
                <div class="ga-details-row">
                    <div class="ga-field" style="flex: 1.35;">
                        <span class="ga-field-label">Sector</span>
                        <span class="ga-field-line">{{ $sector }}</span>
                    </div>
                    <div class="ga-field" style="flex: 1;">
                        <span class="ga-field-label">Street No.</span>
                        <span class="ga-field-line">{{ $streetNo }}</span>
                    </div>
                    <div class="ga-field" style="flex: 1;">
                        <span class="ga-field-label">Phase</span>
                        <span class="ga-field-line">{{ $phase }}</span>
                    </div>
                </div>

                <!-- Line 3: Owner Name -->
                <div class="ga-details-row">
                    <div class="ga-field" style="flex: 1;">
                        <span class="ga-field-label">Owner Name</span>
                        <span class="ga-field-line" style="font-weight: 700; text-transform: uppercase;">
                            {{ $customer->name ?? '' }}
                            @if(!empty($customer->contact_number))
                                <span style="font-weight: 500; font-size: 12px; margin-left: 10px; text-transform: none; color: #374151;">(Ph: {{ $customer->contact_number }})</span>
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Line 4: Billing Month, Issue Date, Due Date -->
                <div class="ga-details-row">
                    <div class="ga-field" style="flex: 1.35;">
                        <span class="ga-field-label">Billing Month</span>
                        <span class="ga-field-line">{{ $billingMonthStr }}</span>
                    </div>
                    <div class="ga-field" style="flex: 1;">
                        <span class="ga-field-label">Issue Date</span>
                        <span class="ga-field-line">{{ $formattedIssueDate }}</span>
                    </div>
                    <div class="ga-field" style="flex: 1;">
                        <span class="ga-field-label">Due Date</span>
                        <span class="ga-field-line">{{ $dueDateStr }}</span>
                    </div>
                </div>
            </div>

            <!-- Charges Table: Only Selected Service(s) Appear -->
            <div class="ga-table-wrap">
                <table class="ga-table">
                    <thead>
                        <tr>
                            <th class="ga-col-sr">Sr. No.</th>
                            <th class="ga-col-details">Details of Charges</th>
                            <th class="ga-col-monthly">Monthly Charges</th>
                            <th class="ga-col-arrears">Arrears</th>
                            <th class="ga-col-late">Late Payment Surcharge</th>
                            <th class="ga-col-bills">Total Bills</th>
                            <th class="ga-col-payable">Total Payable</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 0; $i < $rowsToRender; $i++)
                            @if(isset($items[$i]))
                                <tr>
                                    <td class="ga-col-sr">{{ $i + 1 }}</td>
                                    <td class="ga-col-details">{{ ucwords($items[$i]['name']) }}</td>
                                    <td class="ga-col-monthly">{{ number_format($items[$i]['monthly'], 0) }}/-</td>
                                    <td class="ga-col-arrears">{{ floatval($items[$i]['arrears']) > 0 ? number_format($items[$i]['arrears'], 0) . '/-' : '' }}</td>
                                    <td class="ga-col-late">{{ floatval($items[$i]['late']) > 0 ? number_format($items[$i]['late'], 0) . '/-' : '' }}</td>
                                    <td class="ga-col-bills">{{ floatval($items[$i]['bills']) > 0 ? number_format($items[$i]['bills'], 0) . '/-' : '' }}</td>
                                    <td class="ga-col-payable">{{ number_format($items[$i]['payable'], 0) }}/-</td>
                                </tr>
                            @else
                                <tr>
                                    <td class="ga-col-sr">{{ $i + 1 }}</td>
                                    <td class="ga-col-details"></td>
                                    <td class="ga-col-monthly"></td>
                                    <td class="ga-col-arrears"></td>
                                    <td class="ga-col-late"></td>
                                    <td class="ga-col-bills"></td>
                                    <td class="ga-col-payable"></td>
                                </tr>
                            @endif
                        @endfor

                        <!-- Grand Total Row -->
                        <tr class="ga-row-grand-total">
                            <td colspan="2" class="ga-grand-total-label">Grand Total</td>
                            <td class="ga-col-monthly ga-grand-total-val">{{ number_format($grandMonthly, 0) }}/-</td>
                            <td class="ga-col-arrears"></td>
                            <td class="ga-col-late"></td>
                            <td class="ga-col-bills"></td>
                            <td class="ga-col-payable ga-grand-total-val">{{ number_format($grandPayable, 0) }}/-</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Amount in Words -->
                <div class="ga-words-bar">
                    <span class="ga-words-label">Amount in Words: (Rupees):</span>
                    <span class="ga-words-text">{{ $amountInWords }} ({{ number_format($totalInvoiceAmount, 0) }}/-)</span>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="ga-notes-box">
                <div class="ga-notes-heading">Notes:</div>
                <div class="ga-notes-line">10% will be charged extra after due date of payment.</div>
                <div class="ga-notes-line">Please deposit the amount before due date i.e up to 10th of every month at our Executive Block Control Room Office Near Executive Block Mosque.</div>
            </div>

            <!-- Signatures Section (Clean, NO STAMPS, NO HAND SIGNATURES) -->
            <div class="ga-signatures">
                <div class="ga-sig-block">
                    <div class="ga-sig-line"></div>
                    <div class="ga-sig-title">Accountant</div>
                </div>
                <div class="ga-sig-block">
                    <div class="ga-sig-line"></div>
                    <div class="ga-sig-title">Incharge Control Room</div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
