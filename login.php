<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EventHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container { 
            max-width: 450px; 
            width: 100%;
            margin: 20px;
            padding: 40px; 
            background: white; 
            border-radius: 15px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.3); 
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header i {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 10px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: bold;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-calendar-alt"></i>
            <h2>EventHub Login</h2>
            <p class="text-muted">Sign in to manage your events</p>
        </div>

        <?php
        // Display logout message
        if (isset($_GET['message']) && $_GET['message'] === 'logged_out') {
            echo '<div class="alert alert-success">You have been successfully logged out.</div>';
        }
        // Display error message
        if (isset($_GET['error'])) {
            $error_messages = [
                'invalid' => 'Invalid email or password.',
                'required' => 'Please enter both email and password.',
                'not_found' => 'User account not found.'
            ];
            $error = htmlspecialchars($_GET['error']);
            $message = $error_messages[$error] ?? 'An error occurred. Please try again.';
            echo '<div class="alert alert-danger">' . $message . '</div>';
        }
        ?>

        <form action="php/login.php" method="POST">
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>

        <hr class="my-4">
        
        <div class="text-center">
            <p class="mb-2">Don't have an account? <a href="register.php">Register here</a></p>
            <p class="mb-0"><a href="index.php"><i class="fas fa-home"></i> Back to Home</a></p>
        </div>

        <div class="mt-4 p-3 bg-light rounded">
            <small class="text-muted">
                <strong>Test Credentials:</strong><br>
                Admin: admin@eventhub.com / password123<br>
                Student: john@student.com / password123
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
