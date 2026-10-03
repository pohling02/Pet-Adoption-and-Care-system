@extends('layouts.shelter_master')

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
    #adopter-location {
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

    <p id="adopter-location">Waiting for adopter's location...</p>
</div>

<!-- Incoming Call Modal -->
<div id="incomingCallModal" class="incoming-call-modal" style="display: none;">
    <p><strong>Incoming Video Call from <span id="callerName"></span></strong></p>
    <button class="accept-call" onclick="acceptCall()">Accept</button>
    <button class="decline-call" onclick="declineCall()">Decline</button>
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

    async function startCall() {
        try {
            localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
            document.getElementById('localVideo').srcObject = localStream;
            
            peerConnection = new RTCPeerConnection(configuration);
            localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

            peerConnection.ontrack = event => {
                let remoteStream = new MediaStream();
                document.getElementById('remoteVideo').srcObject = remoteStream;
                remoteStream.addTrack(event.track);
            };

            signalingServer.onmessage = async (message) => {
                let data = JSON.parse(message.data);

                if (data.type === "location") {
                    document.getElementById("adopter-location").innerText = `Adopter's Live Location: ${data.latitude}, ${data.longitude}`;
                }
            };

        } catch (error) {
            console.error('Error starting call:', error);
        }
    }
    
    function acceptCall() {
        document.getElementById("incomingCallModal").style.display = "none";
        document.getElementById("videoCallContainer").style.display = "flex";

        navigator.mediaDevices.getUserMedia({ video: true, audio: true })
            .then(stream => {
                localStream = stream;
                document.getElementById("localVideo").srcObject = localStream;
                peerConnection = new RTCPeerConnection({ iceServers: [{ urls: "stun:stun.l.google.com:19302" }] });

                localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

                peerConnection.setRemoteDescription(new RTCSessionDescription(data.offer));
                peerConnection.createAnswer()
                    .then(answer => peerConnection.setLocalDescription(answer))
                    .then(() => {
                        signalingServer.send(JSON.stringify({ type: "answer", answer: peerConnection.localDescription }));
                    });
            });
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
    
    function declineCall() {
        document.getElementById("incomingCallModal").style.display = "none";
        signalingServer.send(JSON.stringify({ type: "decline", receiver: "{{ auth()->user()->UserID }}" }));
    }

    window.onload = startCall;
</script>

@endsection
