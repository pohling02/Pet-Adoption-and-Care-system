<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Message</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: #007bff;
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .content {
            padding: 20px;
            font-size: 16px;
            line-height: 1.5;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
            color: gray;
        }
        .highlight {
            font-weight: bold;
            color: #007bff;
        }
        .message-box {
            background: #f9f9f9;
            padding: 15px;
            border-left: 5px solid #007bff;
            margin-top: 10px;
            white-space: pre-line;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            📩 New Contact Form Submission
        </div>
        <div class="content">
            <p><strong>Name:</strong> <span class="highlight">{{ $name }}</span></p>
            <p><strong>Email:</strong> <a href="mailto:{{ $email }}">{{ $email }}</a></p>
            <p><strong>Message:</strong></p>
            <div class="message-box">
                {!! $messageContent !!}
            </div>
        </div>
        <div class="footer">
            <p>Petopia Adoption & Care System | TARUMT</p>
            <p>Do not reply to this email. If you need assistance, contact us at <a href="mailto:support@petopia.com">support@petopia.com</a>.</p>
        </div>
    </div>
</body>
</html>
s