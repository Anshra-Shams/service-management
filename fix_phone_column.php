<?php
$content = file_get_contents('routes/web.php');
$content = str_replace('!$invoice->customer->phone', '!$invoice->customer->contact_number', $content);
$content = str_replace('$phone = $invoice->customer->phone;', '$phone = $invoice->customer->contact_number;', $content);
file_put_contents('routes/web.php', $content);
?>
