const express = require('express');
const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const cors = require('cors');

const app = express();
app.use(cors());
app.use(express.json());

console.log("Initializing WhatsApp Client...");

const client = new Client({
    authStrategy: new LocalAuth(),
    puppeteer: {
        headless: true, // Set to false to act like a real browser
        args: [
            '--no-sandbox', 
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--no-first-run',
            '--no-zygote',
            '--disable-gpu'
        ]
    }
});

client.on('qr', (qr) => {
    console.log('\n=========================================');
    console.log('SCAN THIS QR CODE WITH YOUR WHATSAPP');
    console.log('=========================================\n');
    qrcode.generate(qr, { small: true });
});

client.on('ready', () => {
    console.log('\n✅ WhatsApp Client is READY and connected!');
});

client.on('authenticated', () => {
    console.log('✅ WhatsApp Authenticated Successfully!');
});

client.on('auth_failure', msg => {
    console.error('❌ WhatsApp Authentication failure:', msg);
});

client.initialize();

app.post('/send-message', async (req, res) => {
    try {
        const { phone, message } = req.body;
        if (!phone || !message) {
            return res.status(400).json({ success: false, error: 'Phone and message are required' });
        }

        let formattedPhone = phone.replace(/[^0-9]/g, '');
        if (formattedPhone.startsWith('0')) {
            formattedPhone = '92' + formattedPhone.substring(1);
        }
        const chatId = formattedPhone + '@c.us';
        const isRegistered = await client.isRegisteredUser(chatId);
        if (!isRegistered) {
            return res.status(400).json({ success: false, error: 'Number is not registered on WhatsApp' });
        }
        const response = await client.sendMessage(chatId, message);
        return res.status(200).json({ success: true, message: 'Message sent successfully', data: response.id._serialized });
    } catch (error) {
        console.error('Error sending message:', error);
        return res.status(500).json({ success: false, error: error.toString() });
    }
});

const PORT = 3000;
app.listen(PORT, () => {
    console.log(`\n🚀 API Server is running on http://localhost:${PORT}`);
    console.log(`Waiting for WhatsApp client to be ready...\n`);
});
