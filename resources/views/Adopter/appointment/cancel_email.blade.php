<!DOCTYPE html>
<html>
<head>
    <title>Appointment Canceled</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }
        h2 {
            color: #dc3545;
            text-align: center;
        }
        .details {
            font-size: 16px;
            line-height: 1.6;
            color: #333;
        }
        .appointment-box {
            background: #ffebeb;
            padding: 15px;
            border-left: 5px solid #dc3545;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #777;
            margin-top: 20px;
        }
        .button {
            display: inline-block;
            padding: 12px 20px;
            margin-top: 20px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background: #dc3545;
            text-decoration: none;
            border-radius: 5px;
        }
        .button:hover {
            background: #b02a37;
        }
    </style>
</head>
<body>

    <div class="email-container">
        <h2>🚫 Appointment Canceled</h2>

        <p class="details">
            Dear {{ $user->name }},
        </p>

        <p class="details">
            Your appointment has been <strong>canceled</strong> as per your request. Here were the details of your canceled appointment:
        </p>

        <div class="appointment-box">
            <strong>📅 Appointment Date:</strong> {{ \Carbon\Carbon::parse($appointment->AppointmentDate)->format('l, F j, Y') }}<br>
            <strong>🕒 Time:</strong> {{ $appointment->timeslot->Timeslot }}<br>
            <strong>👨‍⚕️ Veterinarian:</strong> Dr. {{ $appointment->doctor->DoctorName }} ({{ $appointment->doctor->Specialization }})<br>
            <strong>🐶 Pet Name:</strong> {{ $appointment->pet->PetName }}<br>
            <strong>📝 Purpose:</strong> {{ $appointment->Purpose }}
        </div>

        <p class="details">
            If you canceled by mistake or need to rebook, you can schedule a new appointment at any time.
        </p>

        <p class="details">
            If you have any questions, feel free to contact us.
        </p>

        <p class="footer">
            🏥 Petopia Veterinary Clinic<br>
            📍 Address: 123 Pet Street, Selangor, Malaysia<br>
            📞 Contact: +60 12-3456-789
        </p>
    </div>

</body>
</html>
