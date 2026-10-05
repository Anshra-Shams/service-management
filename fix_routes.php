<?php
$content = file_get_contents('routes/web.php');
$apiRoute = "
Route::post('/api/whatsapp/send/{id}', function (\Illuminate\Http\Request \$request, \$id) {
    \$invoice = \App\Models\Invoice::with('customer', 'services')->findOrFail(\$id);
    
    if (!\$invoice->customer || !\$invoice->customer->phone) {
        return response()->json(['success' => false, 'error' => 'Customer phone number is missing']);
    }

    \$phone = \$invoice->customer->phone;
    
    \$servicesList = \$invoice->services->pluck('name')->implode(', ');
    if (empty(\$servicesList)) {
        \$servicesList = \$invoice->service ? \$invoice->service->name : 'General Services';
    }

    \$message = \"*INVOICE #INV-\" . str_pad(\$invoice->id, 4, '0', STR_PAD_LEFT) . \"*\\n\\n\";
    \$message .= \"Dear {\$invoice->customer->name},\\n\";
    \$message .= \"Here are your invoice details:\\n\";
    \$message .= \"- *Services:* {\$servicesList}\\n\";
    \$message .= \"- *Issue Date:* {\$invoice->service_date}\\n\";
    \$message .= \"- *Due Date:* {\$invoice->due_date}\\n\";
    \$message .= \"- *Total Amount:* Rs. {\$invoice->amount}\\n\\n\";
    \$message .= \"Thank you for your business!\";

    try {
        \$response = \Illuminate\Support\Facades\Http::post('http://localhost:3000/send-message', [
            'phone' => \$phone,
            'message' => \$message
        ]);
        
        return \$response->json();
    } catch (\Exception \$e) {
        return response()->json(['success' => false, 'error' => 'Could not connect to WhatsApp Server']);
    }
});
";

if (strpos($content, '/api/whatsapp/send') === false) {
    file_put_contents('routes/web.php', $content . $apiRoute);
}
?>
