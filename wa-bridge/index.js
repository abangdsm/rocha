const { default: makeWASocket, useMultiFileAuthState, DisconnectReason } = require('@whiskeysockets/baileys');
const express = require('express');
const QRCode = require('qrcode-terminal');
const path = require('path');

const app = express();
app.use(express.json());

const connections = new Map();

async function connectToWhatsApp(accountId) {
    const sessionPath = path.join(__dirname, 'sessions', accountId);
    
    const { state, saveCreds } = await useMultiFileAuthState(sessionPath);
    
    const sock = makeWASocket({
        auth: state,
        printQRInTerminal: false,
        browser: ['Rocha12', 'Chrome', '1.0.0']
    });
    
    sock.ev.on('connection.update', (update) => {
        const { connection, lastDisconnect, qr } = update;
        
        if (qr) {
            console.log(`\n📱 QR CODE untuk akun: ${accountId}`);
            QRCode.generate(qr, { small: true });
        }
        
        if (connection === 'open') {
            console.log(`✅ Akun ${accountId} TERHUBUNG!`);
            connections.set(accountId, sock);
        }
        
        if (connection === 'close') {
            const shouldReconnect = (lastDisconnect.error?.output?.statusCode !== DisconnectReason.loggedOut);
            if (shouldReconnect) {
                console.log(`🔄 Reconnect ${accountId}...`);
                setTimeout(() => connectToWhatsApp(accountId), 5000);
            } else {
                console.log(`❌ Akun ${accountId} logout`);
                connections.delete(accountId);
            }
        }
    });
    
    sock.ev.on('creds.update', saveCreds);
    
    return sock;
}

app.post('/api/connect', async (req, res) => {
    const { accountId } = req.body;
    if (!accountId) return res.status(400).json({ error: 'accountId required' });
    
    try {
        await connectToWhatsApp(accountId);
        res.json({ success: true, message: 'Connecting...' });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

app.post('/api/send', async (req, res) => {
    const { accountId, to, message } = req.body;
    
    const sock = connections.get(accountId);
    if (!sock) return res.status(404).json({ error: 'Account not connected' });
    
    try {
        const jid = to.includes('@') ? to : `${to}@s.whatsapp.net`;
        const result = await sock.sendMessage(jid, { text: message });
        res.json({ success: true, result });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

app.get('/api/status/:accountId', (req, res) => {
    const { accountId } = req.params;
    res.json({ accountId, connected: connections.has(accountId) });
});

const PORT = 3000;
app.listen(PORT, () => {
    console.log(`\n🚀 WA BRIDGE running on port ${PORT}`);
    console.log(`📍 Endpoints:`);
    console.log(`   POST /api/connect`);
    console.log(`   POST /api/send`);
    console.log(`   GET /api/status/:accountId`);
    console.log(`\n✅ Siap!\n`);
});