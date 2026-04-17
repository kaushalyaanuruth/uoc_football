<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UOC Football - Team Portal Login</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/login/styles.css">
</head>
<body>
    <div class="container">

        <div class="home-button">
            <a href="<?php echo ROOT; ?>/landingPage" id="homeBtn">Home</a>
        </div>

        <div class="login-container">
            <div class="logo-section">
                <img src="<?php echo ROOT; ?>/assets/images/login/Logo.png" alt="UOC Football Logo" class="logo">
                <h1 class="title">Team Portal</h1>
                <p class="subtitle">login</p>
            </div>

            <form class="login-form" id="loginForm" action="<?php echo ROOT; ?>/login/authenticate" method="POST">
                <?php if (!empty($data['error'])): ?>
                    <div class="error-message" style="background-color: #fee; border: 1px solid #fcc; color: #c33; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; font-weight: 500; text-align: center;">
                         <?php echo htmlspecialchars($data['error']); ?>
                    </div>
                <?php endif; ?>

                <div class="input-group">
                    <input type="text" id="indexNumber" name="username" placeholder="Username" required>
                </div>

                <div class="input-group">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>

                <div class="forgot-password">
                    <a href="#" id="forgotPasswordLink">Forgot password?</a>
                </div>
                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
    </div>
    <script src="<?php echo ROOT; ?>/assets/js/login/script.js"></script>
</body>
</html>
