<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UOC Football - Team Portal Login</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/login/styles.css">
    <script src="<?php echo ROOT; ?>/assets/js/login/script.js" defer></script>
</head>
<body>
    <div class="container">

        <div class="home-button">
            <a href="http://localhost/UOC_Football/public/landingPage" id="homeBtn">Home</a>
        </div>

        <div class="login-container">
            <div class="logo-section">
                <img src="<?php echo ROOT; ?>/assets/images/login/Logo.png" alt="UOC Football Logo" class="logo">
                <h1 class="title">Team Portal</h1>
                <p class="subtitle">login</p>
            </div>

            <form class="login-form" id="loginForm" action="login/authenticate" method="POST">
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
</body>
</html>
