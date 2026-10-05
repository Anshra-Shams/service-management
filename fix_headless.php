<?php
$content = file_get_contents('whatsapp-server/index.js');
$content = str_replace('headless: false', 'headless: true', $content);
file_put_contents('whatsapp-server/index.js', $content);
?>
