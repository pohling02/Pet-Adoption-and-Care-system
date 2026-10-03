const fs = require('fs');
const https = require('https');
const WebSocket = require('ws');

// ✅ Load SSL certificate and private key
const server = https.createServer({
    cert: fs.readFileSync('cert.pem'), // SSL Certificate
    key: fs.readFileSync('key.pem') // SSL Private Key
});

// ✅ Create a Secure WebSocket Server (wss://)
const wss = new WebSocket.Server({ server });

wss.on('connection', function connection(ws) {
    console.log("🔗 New client connected via Secure WebSocket");

    ws.on('message', function incoming(message) {
        console.log("📩 Received:", message);

        // ✅ Broadcast to all clients
        wss.clients.forEach(client => {
            if (client.readyState === WebSocket.OPEN) {
                client.send(message);
            }
        });
    });

    ws.on('close', () => {
        console.log("❌ Client disconnected");
    });
});

// ✅ Start the server on HTTPS port 3000
server.listen(3000, () => {
    console.log("✅ Secure WebSocket Server running on wss://192.168.0.13:3000");
});
