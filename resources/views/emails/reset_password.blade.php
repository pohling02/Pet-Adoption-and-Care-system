<!DOCTYPE html>
<html>
<head>
    <title>Password Reset Notification</title>
</head>
<body>
    <h2>Hello {{ $name }},</h2>
    <p>Your password has been reset by the admin.</p>
    <p>Your new temporary password is: <strong>{{ $password }}</strong></p>
    <p>Please log in and change your password immediately.</p>
    <br>
    <p>Thank you,</p>
    <p>Pet Adoption System Team</p>
</body>
</html>
