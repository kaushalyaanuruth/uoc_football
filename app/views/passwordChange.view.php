<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - UOC Football</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: Poppins, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f7f2fa;
            color: #1f2937;
        }

        .card {
            width: min(420px, 92vw);
            background: #ffffff;
            border: 1px solid #eadff0;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 16px 36px rgba(74, 17, 80, 0.12);
        }

        h1 {
            margin: 0 0 8px;
            font-size: 1.5rem;
            color: #4a1150;
        }

        p {
            margin: 0 0 16px;
            color: #6b7280;
            font-size: 0.95rem;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #374151;
        }

        input {
            width: 100%;
            height: 42px;
            border-radius: 10px;
            border: 1px solid #d6c7e0;
            padding: 0 12px;
            margin-bottom: 14px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            height: 44px;
            border: 0;
            border-radius: 10px;
            background: #4a1150;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
        }

        .error {
            margin-bottom: 12px;
            padding: 10px;
            border-radius: 8px;
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Change Your Password</h1>
        <p>For security, you must change the default password before continuing.</p>

        <?php if (!empty($data['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($data['error']); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo ROOT; ?>/PasswordChange/update">
            <label for="newPassword">New Password</label>
            <input id="newPassword" name="new_password" type="password" minlength="6" required>

            <label for="confirmPassword">Confirm Password</label>
            <input id="confirmPassword" name="confirm_password" type="password" minlength="6" required>

            <button type="submit">Update Password</button>
        </form>
    </div>
</body>
</html>
