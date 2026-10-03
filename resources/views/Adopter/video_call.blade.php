@extends('layouts.adopter_master')

@section('title', 'Video Call')

@push('styles')
<style>
    .video-container {
        text-align: center;
        padding: 20px;
    }
    video {
        width: 60%;
        max-width: 800px;
        border-radius: 10px;
        border: 2px solid #007bff;
    }
    .controls {
        margin-top: 20px;
        display: flex;
        justify-content: center;
        gap: 20px;
    }
    .btn {
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        border: none;
    }
    .btn-end {
        background: red;
        color: white;
    }
    .btn-action {
        background: #007bff;
        color: white;
    }
    #location-info {
        margin-top: 10px;
        font-size: 16px;
        color: #333;
    }
</style>
@endpush

@section('content')
<div class="video-container">
    <h2>Video Call with {{ $receiver->name }}</h2>
    <video id="localVideo" autoplay playsinline></video>
    <video id="remoteVideo" autoplay playsinline></video>

    <div class="controls">
        <button class="btn btn-action" onclick="toggleCamera()">Toggle Camera</button>
        <button class="btn btn-action" onclick="toggleMute()">Mute</button>
        <button class="btn btn-end" onclick="endCall()">End Call</button>
    </div>

    <p id="location-info">Fetching location...</p>
</div>

<script>
    let localStream;
    let peerConnection;
    let signalingServer = new WebSocket("wss://192.168.0.13:3000");
    
    signalingServer.onopen = function () {
        console.log("✅ WebSocket connected securely.");
    };

    signalingServer.onerror = function (error) {
        console.error("❌ WebSocket error:", error);
    };

    signalingServer.onmessage = function (message) {
        let data = JSON.parse(message.data);
        console.log("📩 Received message:", data);
    };

    const configuration = { iceServers: [{ urls: 'stun:stun.l.google.com:19302' }] };

    signalingServer.onmessage = async (message) => {
        let data = JSON.parse(message.data);

        if (data.type === "offer") {
            // 🔥 SHOW INCOMING CALL PROMPT ON THE SHELTER STAFF SIDE
            if (data.receiver === "{{ auth()->user()->UserID }}") {
                document.getElementById("callerName").innerText = data.senderName;
                document.getElementById("incomingCallModal").style.display = "block";
            }
        }

        if (data.type === "answer") {
            peerConnection.setRemoteDescription(new RTCSessionDescription(data.answer));
        }

        if (data.type === "candidate") {
            peerConnection.addIceCandidate(new RTCIceCandidate(data.candidate));
        }
    };

    function startCall() {
        navigator.mediaDevices.getUserMedia({ video: true, audio: true })
            .then(stream => {
                localStream = stream;
                document.getElementById('localVideo').srcObject = localStream;

                peerConnection = new RTCPeerConnection(configuration);
                localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

                peerConnection.createOffer()
                    .then(offer => peerConnection.setLocalDescription(offer))
                    .then(() => {
                        signalingServer.send(JSON.stringify({
                            type: "offer",
                            offer: peerConnection.localDescription,
                            receiver: "{{ $receiver->UserID }}",
                            sender: "{{ auth()->user()->UserID }}",
                            senderName: "{{ auth()->user()->name }}"
                        }));
                    });

                trackLiveLocation();
            });
    }

    function trackLiveLocation() {
        if ("geolocation" in navigator) {
            navigator.geolocation.watchPosition(async function(position) {
                let latitude = position.coords.latitude;
                let longitude = position.coords.longitude;

                document.getElementById("location-info").innerText = `Your Live Location: ${latitude}, ${longitude}`;

                // Send live location to shelter staff
                signalingServer.send(JSON.stringify({
                    type: "location",
                    sender: "{{ auth()->user()->UserID }}",
                    receiver: "{{ $receiver->UserID }}",
                    latitude: latitude,
                    longitude: longitude
                }));
            }, function(error) {
                console.error("Error obtaining location:", error);
            }, { enableHighAccuracy: true });
        } else {
            document.getElementById("location-info").innerText = "Geolocation is not supported.";
        }
    }

    function toggleCamera() {
        let videoTrack = localStream.getVideoTracks()[0];
        videoTrack.enabled = !videoTrack.enabled;
    }

    function toggleMute() {
        let audioTrack = localStream.getAudioTracks()[0];
        audioTrack.enabled = !audioTrack.enabled;
    }

    function endCall() {
        localStream.getTracks().forEach(track => track.stop());
        peerConnection.close();
        window.location.href = "{{ route('messages.list') }}";
    }

    window.onload = startCall;
</script>

@endsection
