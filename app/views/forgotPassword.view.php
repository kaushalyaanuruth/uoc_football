<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - UOC Football</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/login/styles.css">
</head>
<body>
    <div class="container">
        <div class="home-button">
            <a href="<?php echo ROOT; ?>/login">Back to Login</a>
        </div>

        <div class="login-container">
            <div class="logo-section">
                <img src="<?php echo ROOT; ?>/assets/images/login/Logo.png" alt="UOC Football Logo" class="logo">
                <h1 class="title">Forgot Password</h1>
                <p class="subtitle">Enter your account email</p>
            </div>

            <form class="login-form" action="<?php echo ROOT; ?>/ForgotPassword/request" method="POST">
                <?php if (!empty($data['error'])): ?>
                    <div style="background-color:#fee;border:1px solid #fcc;color:#c33;padding:12px;border-radius:6px;margin-bottom:20px;font-size:14px;font-weight:500;text-align:center;">
                        <?php echo htmlspecialchars($data['error']); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($data['success'])): ?>
                    <div style="background-color:#ecfdf3;border:1px solid #86efac;color:#166534;padding:12px;border-radius:6px;margin-bottom:20px;font-size:14px;font-weight:500;text-align:center;">
                        <?php echo htmlspecialchars($data['success']); ?>
                    </div>
                <?php endif; ?>

                <div class="input-group">
                    <input type="email" name="email" placeholder="Email address" required>
                </div>

                <button type="submit" class="login-btn">Send Reset Link</button>
            </form>
        </div>
    </div>
</body>
</html>
