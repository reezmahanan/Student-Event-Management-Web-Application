<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - EventHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            padding-top: 70px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .navbar { box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .register-container { 
            max-width: 500px; 
            margin: 50px auto; 
            padding: 30px; 
            background: white; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2); 
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-calendar-alt"></i> EventHub
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Home</a>
                <a class="nav-link" href="events-calendar.php">Events</a>
                <a class="nav-link" href="login.php">Login</a>
                <a class="nav-link active" href="register.php">Register</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="register-container">
            <h2 class="text-center mb-4"><i class="fas fa-user-plus text-primary"></i> Create Account</h2>
            <p class="text-center text-muted mb-4">Join EventHub to register for events</p>
            
            <form action="php/register.php" method="POST" id="registerForm">
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-user"></i> Full Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-id-card"></i> Student ID *</label>
                    <input type="text" name="student_id" class="form-control" placeholder="Enter your student ID" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-envelope"></i> Email *</label>
                    <input type="email" name="email" class="form-control" placeholder="student@example.com" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-phone"></i> Phone Number *</label>
                    <input type="tel" name="contact_number" class="form-control" placeholder="Enter your phone number" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-lock"></i> Password *</label>
                    <input type="password" name="password" class="form-control" placeholder="Create a strong password" required minlength="6">
                    <small class="text-muted">Minimum 6 characters</small>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-2">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
                
                <p class="text-center mt-3 mb-0">
                    Already have an account? <a href="login.php" class="text-primary fw-bold">Login here</a>
                </p>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Basic client-side validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]').value;
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long');
                return false;
            }
        });
    </script>
</body>
</html>
