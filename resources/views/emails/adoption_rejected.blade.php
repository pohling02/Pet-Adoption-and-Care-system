<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            background-color: #f44336;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background: #f9f9f9;
        }
        .footer {
            padding: 10px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .info-box {
            background-color: #e1f5fe;
            border-left: 4px solid #03a9f4;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Adoption Application Update</h2>
    </div>
    
    <div class="content">
        <p>Dear {{ $adoption->FullName }},</p>
        
        <p>We regret to inform you that your application to adopt <strong>{{ $adoption->pet->PetName }}</strong> was not approved at this time.</p>
        
        <p><strong>Reason for rejection:</strong></p>
        <p>{{ $rejectionReason }}</p>
        
        @if($canResubmit)
        <div class="info-box">
            <p><strong>You can resubmit your application:</strong></p>
            <p>You have {{ 3 - $adoption->ResubmissionCount }} resubmission attempts remaining.</p>
            <p>Your next available resubmission date is: <strong>{{ $resubmitDate }}</strong>.</p>
            <p>Please address the concerns mentioned above to improve your chances of approval.</p>
        </div>
        @else
        <div class="info-box" style="border-left-color: #f44336; background-color: #ffebee;">
            <p><strong>Maximum resubmissions reached:</strong></p>
            <p>Unfortunately, you have reached the maximum number of resubmission attempts for this pet. </p>
        </div>
        @endif
        
        <p>If you have any questions or need clarification, please don't hesitate to contact us.</p>
        
        <p>Thank you for your interest in adopting from our shelter.</p>
        
        <p>Warm regards,<br>
        {{ $adoption->pet->shelter->name ?? 'The Shelter Team' }}</p>
    </div>
    
    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
    </div>
</body>
</html>
