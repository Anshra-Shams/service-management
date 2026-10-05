<?php
// A script to replace the table and JS in add-bill.blade.php
$content = file_get_contents('resources/views/add-bill.blade.php');

$newTable = <<<EOT
                        <div class="flex justify-between items-center mb-3 mt-6">
                            <h4 class="text-lg font-semibold text-gray-700">Invoice Items</h4>
                            <button type="button" id="add-row-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm transition-all flex items-center gap-1 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Add Row
                            </button>
                        </div>
                        
                        <div class="overflow-x-auto border border-gray-200 rounded-lg mb-4">
                            <table class="min-w-full divide-y divide-gray-200" style="table-layout: fixed;" id="invoice-items-table">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" style="width: 28%;" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service Rendered</th>
                                        <th scope="col" style="width: 13%;" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monthly Chgs</th>
                                        <th scope="col" style="width: 13%;" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arrears</th>
                                        <th scope="col" style="width: 13%;" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Late Surcharge</th>
                                        <th scope="col" style="width: 13%;" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Bills</th>
                                        <th scope="col" style="width: 14%;" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Payable</th>
                                        <th scope="col" style="width: 6%;" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">x</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="invoice-items-body">
                                    <!-- Dynamic rows will be added here -->
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-50">
                                        <td colspan="5" class="px-4 py-3 text-right font-bold text-gray-700">Grand Total:</td>
                                        <td class="px-4 py-3 font-bold text-gray-900" id="grand-total-display">Rs. 0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <!-- Hidden input for the final amount to send to backend -->
                        <input type="hidden" name="amount" id="grand_total_amount" value="0">
EOT;

$newJS = <<<EOT
            function initSelect2(element) {
                element.select2({
                    width: '100%',
                    placeholder: 'Select service...'
                });
            }

            function setDueDateFromIssue(issueVal) {
                if (!issueVal) return;
                let d = new Date(issueVal);
                if (d.getDate() <= 10) {
                    d.setDate(10);
                } else {
                    d.setDate(d.getDate() + 10);
                }
                let y = d.getFullYear();
                let m = String(d.getMonth() + 1).padStart(2, '0');
                let day = String(d.getDate()).padStart(2, '0');
                $('#due_date_input').val(\`\${y}-\${m}-\${day}\`);
            }

            $('#issue_date_input').on('change', function() {
                setDueDateFromIssue($(this).val());
            });

            if ($('#issue_date_input').val() && !$('#due_date_input').val()) {
                setDueDateFromIssue($('#issue_date_input').val());
            }

            const serviceOptions = \`
                <option value=""></option>
                @foreach(\$services as \$service)
                    <option value="{{ \$service->id }}" data-amount="{{ \$service->amount ?? 0 }}">{{ \$service->name }} &mdash; Rs. {{ number_format(\$service->amount ?? 0, 2) }}</option>
                @endforeach
            \`;

            function addRow() {
                const tr = \$('<tr>').addClass('item-row');
                
                tr.html(\`
                    <td class="px-3 py-3 align-top">
                        <select name="service_ids[]" class="service-select w-full" required>
                            \${serviceOptions}
                        </select>
                    </td>
                    <td class="px-2 py-3 align-top">
                        <input type="number" name="monthly_charges[]" placeholder="0" step="0.01" class="calc-input block w-full bg-gray-50 border-gray-200 rounded-lg text-sm py-2 px-2">
                    </td>
                    <td class="px-2 py-3 align-top">
                        <input type="number" name="arrears[]" placeholder="0" step="0.01" class="calc-input block w-full bg-gray-50 border-gray-200 rounded-lg text-sm py-2 px-2">
                    </td>
                    <td class="px-2 py-3 align-top">
                        <input type="number" name="late_payment_surcharge[]" placeholder="0" step="0.01" class="calc-input block w-full bg-gray-50 border-gray-200 rounded-lg text-sm py-2 px-2">
                    </td>
                    <td class="px-2 py-3 align-top">
                        <input type="number" name="row_total_amount[]" placeholder="0" step="0.01" class="calc-input row-amount block w-full bg-gray-50 border-gray-200 rounded-lg text-sm font-semibold text-gray-900 py-2 px-2" readonly>
                    </td>
                    <td class="px-2 py-3 align-top">
                        <input type="number" name="row_total_payable[]" placeholder="0" step="0.01" class="row-payable block w-full bg-blue-50 border-blue-200 rounded-lg text-sm font-bold text-gray-900 py-2 px-2" readonly>
                    </td>
                    <td class="px-2 py-3 align-top text-center">
                        <button type="button" class="remove-row text-red-500 hover:bg-red-50 p-1.5 rounded border border-red-200 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </td>
                \`);
                
                $('#invoice-items-body').append(tr);
                initSelect2(tr.find('.service-select'));
            }

            // Initial row
            addRow();

            $('#add-row-btn').on('click', function() {
                addRow();
            });

            $(document).on('click', '.remove-row', function() {
                if ($('.item-row').length > 1) {
                    $(this).closest('tr').remove();
                    calculateGrandTotal();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Wait!',
                        text: 'You must have at least one invoice item.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });

            $(document).on('change', '.service-select', function() {
                const tr = $(this).closest('tr');
                let amt = parseFloat($(this).find('option:selected').data('amount')) || 0;
                tr.find('.row-amount').val(amt.toFixed(2));
                calculateRow(tr);
            });

            $(document).on('input change', '.calc-input', function() {
                const tr = $(this).closest('tr');
                calculateRow(tr);
            });

            function calculateRow(tr) {
                let s_amount = parseFloat(tr.find('.row-amount').val()) || 0;
                let mc = parseFloat(tr.find('input[name="monthly_charges[]"]').val()) || 0;
                let ar = parseFloat(tr.find('input[name="arrears[]"]').val()) || 0;
                let ls = parseFloat(tr.find('input[name="late_payment_surcharge[]"]').val()) || 0;
                
                let rowTotal = s_amount + mc + ar + ls;
                tr.find('.row-payable').val(rowTotal.toFixed(2));
                
                calculateGrandTotal();
            }

            function calculateGrandTotal() {
                let grandTotal = 0;
                $('.row-payable').each(function() {
                    grandTotal += parseFloat($(this).val()) || 0;
                });
                
                $('#grand_total_amount').val(grandTotal.toFixed(2));
                $('#grand-total-display').text('Rs. ' + grandTotal.toFixed(2));
            }
EOT;

// Replace table div
$startTable = strpos($content, '<div class="overflow-x-auto border border-gray-200 rounded-lg mb-4">');
$endTable = strpos($content, '<div class="flex justify-end gap-4 pt-3 mt-3 border-t border-gray-100">');
if ($startTable !== false && $endTable !== false) {
    $content = substr_replace($content, $newTable . "\n                        ", $startTable, $endTable - $startTable);
}

// Replace JS
$startJs = strpos($content, "function setDueDateFromIssue(issueVal) {");
$endJs = strpos($content, "@if(session('success'))");

if ($startJs !== false && $endJs !== false) {
    $content = substr_replace($content, $newJS . "\n\n            ", $startJs, $endJs - $startJs);
}

// Remove the standalone select2 init since we init it dynamically now
$select2Init = <<<EOT
            $('.searchable-select').each(function() {
                var placeholder = $(this).data('placeholder') || 'Select an option';
                $(this).select2({
                    width: '100%',
                    placeholder: placeholder
                });
            });
EOT;
$content = str_replace($select2Init, "\n            // Initializing select2 dynamically for regular selects\n            $('.searchable-select').not('.service-select').select2({width: '100%', placeholder: 'Select option'});\n", $content);

file_put_contents('resources/views/add-bill.blade.php', $content);
echo "Updated completely.";
?>
