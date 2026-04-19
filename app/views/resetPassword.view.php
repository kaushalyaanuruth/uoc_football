<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - UOC Football</title>
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
                <h1 class="title">Reset Password</h1>
                <p class="subtitle">Create your new password</p>
            </div>

            <form class="login-form" action="<?php echo ROOT; ?>/ForgotPassword/update" method="POST">
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

                <input type="hidden" name="token" value="<?php echo htmlspecialchars($data['token'] ?? ''); ?>">

                <div class="input-group">
                    <input type="password" name="new_password" placeholder="New password" required minlength="6">
                </div>

                <div class="input-group">
                    <input type="password" name="confirm_password" placeholder="Confirm new password" required minlength="6">
                </div>

                <button type="submit" class="login-btn" <?php echo empty($data['token']) ? 'disabled' : ''; ?>>Update Password</button>
            </form>
        </div>
    </div>
</body>
</html>
