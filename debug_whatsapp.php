<?php
$content = file_get_contents("resources/views/create-bill.blade.php");

$js = "
    <script>
        function sendWhatsApp(invoiceId) {
            Swal.fire({
                title: 'Sending WhatsApp Message...',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            fetch('/api/whatsapp/send/' + invoiceId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(async response => {
                const text = await response.text();
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error('Invalid JSON: ' + text.substring(0, 100));
                }
            })
            .then(data => {
                if(data.success) {
                    Swal.fire('Success!', 'Invoice sent via WhatsApp.', 'success');
                } else {
                    Swal.fire('Error!', data.error || 'Failed to send message.', 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error!', err.message, 'error');
            });
        }
    </script>
</x-app-layout>";

$content = preg_replace('/<script>\s*function sendWhatsApp.*?<\/x-app-layout>/s', $js, $content);

file_put_contents("resources/views/create-bill.blade.php", $content);
?>
