@extends('layouts.adopter_master')

@section('title', 'Message Center')

@push('styles')
<style>
    .chat-container {
        display: flex;
        flex-direction: column;
        height: 85vh;
        border-radius: 16px;
        overflow: hidden;
        border: none;
        background: white;
        max-width: 90%;
        margin: auto;
        box-shadow: 0px 8px 24px rgba(0, 0, 0, 0.15);
    }

    .chat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 20px;
        background: linear-gradient(135deg, #2185d0, #0056b3);
        color: white;
        font-weight: bold;
        border-bottom: none;
    }

    .chat-header-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .chat-header .receiver-info {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        font-size: 20px;
        font-weight: bold;
        color: white;
        transition: 0.3s;
        cursor: pointer;
        text-shadow: 0px 1px 2px rgba(0, 0, 0, 0.2);
    }

    .chat-header .receiver-info:hover {
        transform: translateX(-50%) scale(1.05);
        text-decoration: none;
    }

    .chat-header .back-button {
        background: rgba(255, 255, 255, 0.25);
        border: none;
        color: white;
        font-size: 16px;
        cursor: pointer;
        padding: 10px 16px;
        border-radius: 10px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .chat-header .back-button:hover {
        background: rgba(255, 255, 255, 0.4);
        transform: translateX(-5px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .chat-header .back-button:active {
        transform: translateX(-3px) scale(0.98);
    }

    .chat-messages {
        flex-grow: 1;
        padding: 20px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        background: #f8f9fa;
        background-image: linear-gradient(rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.7)),
            url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23007bff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
    }

    .message-container {
        display: flex;
        margin-bottom: 15px;
        max-width: 75%;
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .sent-container {
        align-self: flex-end;
        margin-left: auto;
    }

    .received-container {
        align-self: flex-start;
        margin-right: auto;
    }

    .message {
        padding: 14px 18px;
        border-radius: 18px;
        font-size: 16px;
        position: relative;
        word-wrap: break-word;
        white-space: normal;
        overflow-wrap: break-word;
        text-align: left;
        display: inline-block;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
        width: 100%;
    }

    .message:hover {
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
    }

    .sent {
        background: linear-gradient(135deg, #e3f2fd, #bbdefb);
        color: #0d47a1;
        border-bottom-right-radius: 5px;
    }

    .received {
        background: linear-gradient(135deg, #f5f5f5, #e9ecef);
        color: #343a40;
        border-bottom-left-radius: 5px;
    }

    .timestamp {
        font-size: 12px;
        margin-top: 6px;
        color: #6c757d;
        font-style: italic;
        display: block;
        text-align: right;
    }

    .no-messages {
        text-align: center;
        color: #6c757d;
        font-style: italic;
        margin: 50px auto;
        padding: 20px;
        background: rgba(255, 255, 255, 0.7);
        border-radius: 10px;
        max-width: 60%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .chat-input-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top: 1px solid #e6e6e6;
        background: white;
        padding: 15px 20px;
        gap: 12px;
        position: relative;
        flex-wrap: nowrap;
    }

    .chat-input-container input {
        flex-grow: 1;
        padding: 14px;
        border: 1px solid #dee2e6;
        border-radius: 24px;
        font-size: 16px;
        width: 100%;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .chat-input-container input:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
    }

    .chat-input-container .send-btn {
        padding: 14px 22px;
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        border: none;
        border-radius: 24px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .chat-input-container .send-btn:hover {
        background: linear-gradient(135deg, #0069d9, #004494);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .chat-input-container .send-btn:active {
        transform: translateY(1px);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    .attachment-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        font-size: 20px;
        cursor: pointer;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 50%;
        transition: all 0.3s ease;
        color: #6c757d;
    }

    .attachment-btn:hover {
        background: #e9ecef;
        color: #007bff;
        transform: rotate(15deg);
    }

    .attachment-menu {
        display: none;
        position: absolute;
        bottom: 60px;
        left: 10px;
        background: white;
        border: none;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        padding: 5px;
        flex-direction: column;
        z-index: 1000;
        min-width: 200px;
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .attachment-menu label {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        cursor: pointer;
        font-size: 16px;
        white-space: nowrap;
        transition: background 0.2s ease;
        border-radius: 8px;
    }

    .attachment-menu label:hover {
        background: #f1f9ff;
    }

    .attachment-menu input {
        display: none;
    }

    #filePreviewContainer {
        display: none;
        align-items: center;
        gap: 10px;
        position: absolute;
        bottom: 70px;
        left: 20px;
        background: white;
        padding: 15px;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        max-width: 300px;
        flex-wrap: wrap;
        animation: fadeIn 0.3s ease-in-out;
    }

    #filePreview img,
    #filePreview video {
        max-width: 120px;
        max-height: 120px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    #filePreview p {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        margin: 0;
    }

    .remove-file-btn {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        cursor: pointer;
        color: #dc3545;
        border-radius: 50%;
        transition: all 0.2s ease;
    }

    .remove-file-btn:hover {
        background: #dc3545;
        color: white;
        transform: rotate(90deg);
    }

    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        animation: fadeIn 0.3s ease;
    }

    .modal-content {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 450px;
        background: white;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        border-radius: 16px;
        z-index: 1001;
        padding: 25px;
        text-align: center;
    }

    .modal-content h3 {
        margin-top: 0;
        color: #007bff;
        font-size: 24px;
    }

    .close-btn {
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 24px;
        cursor: pointer;
        color: #6c757d;
        transition: all 0.2s ease;
    }

    .close-btn:hover {
        color: #dc3545;
        transform: rotate(90deg);
    }

    /* Styling for file attachments in messages */
    .message-content {
        margin-bottom: 8px;
    }

    .message-attachment {
        margin-bottom: 10px;
        display: block;
    }

    .message img,
    .message video {
        max-width: 200px;
        border-radius: 8px;
        margin-bottom: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .message a {
        display: inline-block;
        background: rgba(0, 123, 255, 0.1);
        padding: 8px 12px;
        border-radius: 8px;
        margin-bottom: 8px;
        color: #007bff;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .message a:hover {
        background: rgba(0, 123, 255, 0.2);
        text-decoration: none;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="chat-container">
        <div class="chat-header">
            <div class="chat-header-left">
                <a href="javascript:void(0);" class="back-button" onclick="goBack()">← Back</a>
            </div>

            @if(isset($receiver))
            <div class="receiver-info" onclick="openAdopterProfile()">
                {{ $receiver->name }}
            </div>
            @endif
        </div>

        <div class="chat-messages">
            @if(isset($messages) && $messages->count() > 0)
            @foreach($messages as $message)
            <div class="message-container {{ $message->SenderID == auth()->user()->UserID ? 'sent-container' : 'received-container' }}">
                <div class="message {{ $message->SenderID == auth()->user()->UserID ? 'sent' : 'received' }}">
                    @if ($message->file_path)
                    <div class="message-attachment">
                        @if (in_array($message->file_type, ['jpg', 'jpeg', 'png', 'gif']))
                        <img src="{{ asset('storage/' . $message->file_path) }}" alt="Uploaded Image">
                        @elseif (in_array($message->file_type, ['mp4', 'mov']))
                        <video controls>
                            <source src="{{ asset('storage/' . $message->file_path) }}" type="video/{{ $message->file_type }}">
                        </video>
                        @else
                        <a href="{{ asset('storage/' . $message->file_path) }}" target="_blank">📄 {{ $message->file_name ?? 'Download File' }}</a>
                        @endif
                    </div>
                    @endif

                    <div class="message-content">{{ $message->Content }}</div>
                    <div class="timestamp">{{ \Carbon\Carbon::parse($message->created_at)->format('H:i A') }}</div>
                </div>
            </div>
            @endforeach
            @else
            <p class="no-messages">No messages yet. Start the conversation!</p>
            @endif
        </div>

        @if(isset($receiver))
        <div class="chat-input-container">
            <button type="button" class="attachment-btn" onclick="toggleAttachmentMenu(event)">📎</button>

            <div class="attachment-menu" id="attachmentMenu">
                <label for="photoInput">📷 Photos & Videos</label>
                <input type="file" id="photoInput" name="attachment" accept="image/*,video/*" onchange="previewFile(event)">

                <label for="documentInput">📄 Document</label>
                <input type="file" id="documentInput" name="attachment" accept=".pdf,.doc,.docx,.txt" onchange="previewFile(event)">
            </div>

            <div id="filePreviewContainer">
                <div id="filePreview"></div>
                <button type="button" class="remove-file-btn" onclick="removeFile()">❌</button>
            </div>

            <form id="messageForm" action="{{ route('messages.send', ['receiverId' => $receiver->UserID]) }}" method="POST" enctype="multipart/form-data" style="display: flex; width: 100%;">
                @csrf
                <input type="text" name="Content" class="chat-input" placeholder="Type a message...">
                <input type="file" name="attachment" id="fileInput" style="display: none;">
                <button type="submit" class="send-btn">Send</button>
            </form>
        </div>
        @endif
    </div>
</div>

<div id="adopterProfileModal" class="modal-overlay">
    <div class="modal-content">
        <span class="close-btn" onclick="closeAdopterProfile()">&times;</span>
        <h3>Adopter Profile</h3>
        <div id="adopterProfileDetails">Loading...</div>
    </div>
</div>

@if(isset($receiver) && $receiver->UserID)
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Auto-scroll to bottom of chat on load
        const chatMessages = document.querySelector('.chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;

        let attachBtn = document.querySelector(".attachment-btn");
        if (attachBtn) {
            attachBtn.style.display = "block"; // Ensure button is shown
        }

        // Setup form submission
        const messageForm = document.getElementById("messageForm");
        if (messageForm) {
            messageForm.addEventListener("submit", function (e) {
                const contentInput = messageForm.querySelector('input[name="Content"]');
                const fileInput = document.getElementById("fileInput");

                // Only prevent default if there's no content and no file
                if (!contentInput.value.trim() && (!fileInput.files || fileInput.files.length === 0)) {
                    e.preventDefault();
                    alert("Please enter a message or attach a file.");
                }
            });
        }

        // Add direct event listener to the close button
        const closeBtn = document.querySelector(".close-btn");
        if (closeBtn) {
            closeBtn.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                closeAdopterProfile();
            });
        }

        // Close modal when clicking outside the modal content
        const modalOverlay = document.getElementById("adopterProfileModal");
        if (modalOverlay) {
            modalOverlay.addEventListener("click", function (e) {
                if (e.target === modalOverlay) {
                    closeAdopterProfile();
                }
            });
        }
    });

    function goBack() {
        // Check if the referrer is from the messages index page
        const referrer = document.referrer;
        const messagesIndexUrl = "{{ route('messages.index') }}";

        if (referrer.includes(messagesIndexUrl) || window.history.length <= 1) {
            // Direct navigation if coming from messages index or no history
            window.location.href = messagesIndexUrl;
        } else {
            // Use history navigation in other cases
            window.history.back();
        }
    }

    function toggleAttachmentMenu(event) {
        event.stopPropagation(); // Prevent event bubbling
        let menu = document.getElementById("attachmentMenu");

        // Toggle visibility
        if (menu.style.display === "block") {
            menu.style.display = "none";
        } else {
            menu.style.display = "block";

            // Close menu when clicking outside
            document.addEventListener("click", function hideMenu(e) {
                if (!menu.contains(e.target) && !e.target.closest(".attachment-btn")) {
                    menu.style.display = "none";
                    document.removeEventListener("click", hideMenu);
                }
            });
        }
    }

    function hideAttachmentMenu() {
        document.getElementById("attachmentMenu").style.display = "none";
    }

    function previewFile(event) {
        let file = event.target.files[0];
        let previewContainer = document.getElementById("filePreviewContainer");
        let preview = document.getElementById("filePreview");
        let fileInput = document.getElementById("fileInput");

        if (!file)
            return;

        let reader = new FileReader();
        reader.onload = function (e) {
            let fileType = file.type.split("/")[0];
            if (fileType === "image") {
                preview.innerHTML = `<img src="${e.target.result}" width="100" alt="Selected Image">`;
            } else if (fileType === "video") {
                preview.innerHTML = `<video width="100" controls><source src="${e.target.result}" type="${file.type}"></video>`;
            } else {
                preview.innerHTML = `<p>📄 ${file.name}</p>`;
            }

            previewContainer.style.display = "flex"; // Ensure visibility
            hideAttachmentMenu();
        };
        reader.readAsDataURL(file);
        fileInput.files = event.target.files;
    }

    function removeFile() {
        document.getElementById("filePreviewContainer").style.display = "none";
        document.getElementById("filePreview").innerHTML = "";
        document.getElementById("fileInput").value = "";
    }

// Function to open adopter profile modal
    function openAdopterProfile() {
        const modal = document.getElementById("adopterProfileModal");
        if (modal) {
            modal.style.display = "block";
            // Load profile data
            setTimeout(() => {
                const profileDetails = document.getElementById("adopterProfileDetails");
                if (profileDetails) {
                    profileDetails.innerHTML =
                            `<div style="text-align: left; padding: 15px;">
                        <p><strong>Name:</strong> {{ $receiver->name }}</p>
                        <p><strong>Email:</strong> {{ $receiver->email ?? 'Not provided' }}</p>
                        <p><strong>Member Since:</strong> {{ \Carbon\Carbon::parse($receiver->created_at)->format('M d, Y') }}</p>
                    </div>`;
                }
            }, 500);
        }
    }

// Function to close adopter profile modal - enhanced with debugging
    function closeAdopterProfile() {
        const modal = document.getElementById("adopterProfileModal");
        if (modal) {
            modal.style.display = "none";
            console.log("Modal closed"); // Debugging line
        } else {
            console.error("Modal element not found"); // Debugging line
        }
    }
</script>
@endif
@endsection