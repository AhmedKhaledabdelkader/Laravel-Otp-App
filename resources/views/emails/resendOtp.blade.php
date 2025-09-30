
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resend OTP - Verify Your Email</title>
</head>
<body style="margin:0; padding:0; font-family: Arial, sans-serif; background-color:#f9fafb;">

    <!-- Container -->
    <div style="max-width:600px; margin:20px auto; background:#ffffff; border-radius:10px; box-shadow:0 4px 8px rgba(0,0,0,0.05); overflow:hidden;">

        <!-- Header / Logo -->
        <div style="background:linear-gradient(90deg,#4CAF50,#2F855A); padding:20px; text-align:center;">
            <img src="https://img.icons8.com/color/96/000000/shopping-bag.png" alt="ShoppingGo" style="width:60px; margin-bottom:10px;">
            <h1 style="color:#ffffff; margin:0; font-size:28px; font-weight:bold;">ShoppingGo</h1>
            <p style="color:#e6f7ec; margin:5px 0 0; font-size:14px;">Your smart shopping companion 🛒</p>
        </div>

        <!-- Body -->
        <div style="padding:30px; text-align:center; color:#333;">
            <h2 style="margin-bottom:15px; color:#2F855A;">Hello {{ $user->username }},</h2>
            <p style="font-size:16px; line-height:1.5; margin-bottom:20px;">
                We noticed you requested to <strong>resend your OTP code</strong> 🔄 <br>
                Use the new OTP below to verify your email address:
            </p>

            <!-- OTP Box -->
            <div style="display:inline-block; padding:15px 30px; font-size:24px; font-weight:bold; color:#2F855A; border:2px dashed #2F855A; border-radius:8px; margin-bottom:20px;">
                {{ $user->otp }}
            </div>

            <p style="font-size:14px; color:#555;">
                This code will expire in <strong>10 minutes</strong>. <br>
                If you didn’t request a new code, you can safely ignore this email.
            </p>
        </div>

        <!-- Footer -->
        <div style="background:#f1f5f9; padding:15px; text-align:center; font-size:12px; color:#777;">
            <p>© {{ date('Y') }} ShoppingGo. All rights reserved.</p>
            <p>Made with ❤️ by the ShoppingGo Team</p>
        </div>
    </div>

</body>
</html>
