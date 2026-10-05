<?php
$content = file_get_contents("resources/views/edit-bill.blade.php");
$content = preg_replace(
    "/<select name=\"customer_id\" class=\"searchable-select w-full\" data-placeholder=\"Select a customer\.\.\.\" required>.*?<\/select>/s",
    "<select name=\"customer_id\" class=\"searchable-select w-full\" data-placeholder=\"Select a customer...\" required>
                                    <option value=\"\"></option>
                                    @foreach(\$customers as \$customer)
                                        <option value=\"{{ \$customer->id }}\" {{ \$invoice->customer_id == \$customer->id ? 'selected' : '' }}>{{ \$customer->name }} - (Balance: Rs. {{ number_format(\$customer->opening_balance, 2) }})</option>
                                    @endforeach
                                </select>",
    $content
);
file_put_contents("resources/views/edit-bill.blade.php", $content);
?>
