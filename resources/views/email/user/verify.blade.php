<!DOCTYPE html>
<html lang="en">

<head>
    <title>User Verification</title>
</head>

<body>
    <h4>Hi! <strong>{{ $firstName }}</strong></h4>
    <p>
        Your account has been created on [{{ $appName }}].<br />
        Kindly verify your e-mail address with this <a href="{{ $verificationUrl }}?code={{ $code }}&email={{ $email }}">link</a><br />
        or <strong>Copy</strong> / Paste the link-address provided below on to your browser's navigation-bar and click <strong>GO</strong>.<br /><br />
        <em>{{ $verificationUrl }}?code={{ $code }}&email={{ $email }}</em>

        <br /><br />
        Happy to serve you!

        <br />
        <small style="color:gray; text-decoration:italicize; font-size: 10px">Your mental well-being is our business.</small>
    </p>
</body>

</html>