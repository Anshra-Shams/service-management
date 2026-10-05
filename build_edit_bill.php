<?php
$content = file_get_contents("resources/views/add-bill.blade.php");

$content = str_replace(
    'action="{{ route(\'generate-bill\') }}"',
    'action="{{ route(\'invoice.update\', $invoice->id) }}"',
    $content
);

$content = str_replace(
    "@csrf",
    "@csrf\n                        @method('PUT')",
    $content
);

$content = preg_replace(
    '/<select name="customer_id" class="searchable-select w-full" required>.*?<\/select>/s',
    '<select name="customer_id" class="searchable-select w-full" required>
                                    <option value="">Select a customer...</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ $invoice->customer_id == $customer->id ? \'selected\' : \'\' }}>
                                            {{ $customer->name }} - (Balance: Rs. {{ number_format($customer->opening_balance, 2) }})
                                        </option>
                                    @endforeach
                                </select>',
    $content
);

$content = str_replace(
    'name="category" placeholder="Enter category..."',
    'name="category" value="{{ $invoice->category }}" placeholder="Enter category..."',
    $content
);

$content = str_replace(
    'name="type" placeholder="Enter type..."',
    'name="type" value="{{ $invoice->type }}" placeholder="Enter type..."',
    $content
);

$content = str_replace(
    'name="service_date" id="issue_date_input"',
    'name="service_date" id="issue_date_input" value="{{ $invoice->service_date }}"',
    $content
);

$content = str_replace(
    'name="due_date" id="due_date_input"',
    'name="due_date" id="due_date_input" value="{{ $invoice->due_date }}"',
    $content
);

$content = str_replace('Generate Bill', 'Update Invoice', $content);
$content = str_replace('Generate New Bill', 'Edit Invoice #{{ str_pad($invoice->id, 3, \'0\', STR_PAD_LEFT) }}', $content);
$content = str_replace('Please provide the customer and service details to generate a new invoice.', 'Update the customer and service details for this invoice.', $content);

$tbody = '
                                <tbody class="bg-white divide-y divide-gray-200" id="invoice_tbody">
                                    @php
                                        $servicesList = $invoice->services->isNotEmpty() ? $invoice->services : collect([$invoice->service]);
                                    @endphp
                                    @foreach($servicesList as $s)
                                    <tr class="invoice-row">
                                        <td class="px-4 py-3">
                                            <select name="service_ids[]" class="service-select w-full" data-placeholder="Select service..." required>
                                                <option value=""></option>
                                                @foreach($services as $srv)
                                                    <option value="{{ $srv->id }}" {{ ($s && $srv->id == $s->id) ? \'selected\' : \'\' }}>
                                                        {{ $srv->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="monthly_charges[]" value="{{ $s->pivot->monthly_charges ?? $s->amount ?? 0 }}" placeholder="0.00" step="0.01" class="monthly-charges block w-full bg-gray-50 border-gray-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="arrears[]" value="{{ $s->pivot->arrears ?? 0 }}" placeholder="0.00" step="0.01" class="arrears block w-full bg-gray-50 border-gray-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="late_payment_surcharge[]" value="{{ $s->pivot->late_surcharge ?? 0 }}" placeholder="0.00" step="0.01" class="late-surcharge block w-full bg-gray-50 border-gray-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="total_amount[]" value="{{ $s->pivot->total_bills ?? ($s->pivot->monthly_charges ?? $s->amount ?? 0) + ($s->pivot->arrears ?? 0) + ($s->pivot->late_surcharge ?? 0) }}" placeholder="0.00" step="0.01" class="total-amount block w-full bg-gray-50 border-gray-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-semibold text-gray-900 py-2" required readonly>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="total_payable[]" value="{{ $s->pivot->total_payable ?? ($s->pivot->total_bills ?? ($s->pivot->monthly_charges ?? $s->amount ?? 0) + ($s->pivot->arrears ?? 0) + ($s->pivot->late_surcharge ?? 0)) }}" placeholder="0.00" step="0.01" class="total-payable block w-full bg-gray-50 border-gray-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-semibold text-gray-900 py-2" required>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button type="button" class="remove-row-btn rounded p-1.5 transition-colors" style="color: #ef4444; background-color: #fef2f2;">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
';

$content = preg_replace('/<tbody class="bg-white divide-y divide-gray-200" id="invoice_tbody">.*?<\/tbody>/s', $tbody, $content);

file_put_contents("resources/views/edit-bill.blade.php", $content);
?>
