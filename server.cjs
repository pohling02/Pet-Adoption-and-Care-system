const express = require("express");
const http = require("http");
const socketIo = require("socket.io");
const cors = require("cors");

const app = express();
const server = http.createServer(app);
const io = socketIo(server, {
  cors: {
    origin: "*",
    methods: ["GET", "POST"]
  }
});

io.on("connection", (socket) => {
    console.log("New user connected:", socket.id);

    // When user joins the room
    socket.on("join-room", (roomId, userId) => {
        socket.join(roomId);
        socket.broadcast.to(roomId).emit("user-connected", userId);
    });

    // Handle Offer
    socket.on("offer", (data) => {
        socket.to(data.target).emit("offer", data);
    });

    // Handle Answer
    socket.on("answer", (data) => {
        socket.to(data.target).emit("answer", data);
    });

    // Handle ICE Candidates
    socket.on("ice-candidate", (data) => {
        socket.to(data.target).emit("ice-candidate", data);
    });

    // User disconnect
    socket.on("disconnect", () => {
        console.log("User disconnected:", socket.id);
    });
});

server.listen(3000, () => {
    console.log("Signaling server running on port 3000");
});
