<?php
require_once __DIR__ . '/../php/config.php';

// Simple admin check
$is_admin = false;
if (isLoggedIn() && isAdmin()) {
    $is_admin = true;
} elseif (isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'demo@student.com') {
    $is_admin = true;
} 

if (!$is_admin) {
    header("Location: ../php/login.php");
    exit();
}

$success_message = '';
$error_message = '';

// Handle image upload
function handleImageUpload() {
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === 0) {
        $uploadDir = '../images/events/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileName = time() . '_' . basename($_FILES['event_image']['name']);
        $filePath = $uploadDir . $fileName;
        
        // Check file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($_FILES['event_image']['type'], $allowedTypes)) {
            if (move_uploaded_file($_FILES['event_image']['tmp_name'], $filePath)) {
                return 'images/events/' . $fileName;
            }
        }
    }
    return null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $event_date = $_POST['event_date'];
    $venue = $_POST['venue'];
    $organizer = $_POST['organizer'] ?? 'Admin';
    $max_participants = $_POST['max_participants'] ?? 50;
    $estimated_budget = $_POST['estimated_budget'] ?? 0;
    $image_url = $_POST['image_url']; // Custom URL input
    
    // Handle file upload
    $uploadedImage = handleImageUpload();
    if ($uploadedImage) {
        $image_url = $uploadedImage;
    }
    
    // Demo mode - simulate event creation
    if (!empty($title) && !empty($event_date) && !empty($venue)) {
        $success_message = "Event '$title' created successfully! (Demo Mode)<br>";
        if ($image_url) {
            $success_message .= "Image: " . htmlspecialchars($image_url);
        }
    } else {
        $error_message = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .image-preview {
            max-width: 300px;
            max-height: 200px;
            border-radius: 8px;
            margin-top: 10px;
        }
        .image-options {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-calendar-check me-2"></i>EventHub Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../index.php">Home</a>
                <a class="nav-link" href="manage-events.php">Manage Events</a>
                <a class="nav-link active" href="create-event.php">Create Event</a>
                <a class="nav-link" href="../profile.php">Profile</a>
                <a class="nav-link" href="../php/logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-10 mx-auto">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-plus-circle me-2"></i>Create New Event</h4>
                    </div>
                    <div class="card-body">
<?php if ($success_message): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                        </div>
<?php endif; ?>

<?php if ($error_message): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                        </div>
<?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <!-- Basic Event Information -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Event Title *</label>
                                                <input type="text" name="title" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Category *</label>
                                                <select name="category" class="form-control" required>
                                                    <option value="">Select Category</option>
                                                    <option value="Workshop">Workshop</option>
                                                    <option value="Seminar">Seminar</option>
                                                    <option value="Cultural">Cultural</option>
                                                    <option value="Sports">Sports</option>
                                                    <option value="Academic">Academic</option>
                                                    <option value="Social">Social</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                        <label class="form-label">Date & Time *</label>
                        <input type="datetime-local" name="event_date" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Venue *</label>
                        <input type="text" name="venue" class="form-control" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Organizer *</label>
                        <input type="text" name="organizer" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Max Participants</label>
                        <input type="number" name="max_participants" class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Estimated Budget ($)</label>
                        <input type="number" step="0.01" name="estimated_budget" class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4"></textarea>
                    </div>
                </div>
            </div>
            <div class="mb-3">
    <label class="form-label">Event Image URL</label>
    <input type="text" name="image_url" class="form-control" 
           placeholder="https://images.unsplash.com/photo-...">
    <small class="form-text text-muted">
        Use free images from <a href="https://unsplash.com" target="_blank">Unsplash.com</a>
    </small>
</div>
                            
                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>Create Event
                                </button>
                                <a href="dashboard.php" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview uploaded image
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').innerHTML = 
                        '<img src="' + e.target.result + '" class="image-preview img-fluid">';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Preview image from URL
        function previewImageURL(input) {
            if (input.value) {
                document.getElementById('imagePreview').innerHTML = 
                    '<img src="' + input.value + '" class="image-preview img-fluid" onerror="this.style.display=\'none\'">';
            }
        }
        
        // Select predefined image
        function selectImage(url) {
            document.querySelector('input[name="image_url"]').value = url;
            document.getElementById('imagePreview').innerHTML = 
                '<img src="' + url + '" class="image-preview img-fluid">';
        }
        
        // Auto-fill organizer based on category
        document.querySelector('select[name="category"]').addEventListener('change', function() {
            const organizers = {
                'Workshop': 'Tech Club',
                'Seminar': 'Academic Department', 
                'Cultural': 'Cultural Committee',
                'Sports': 'Athletics Department',
                'Academic': 'Research Committee',
                'Social': 'Student Council'
            };
            
            if (organizers[this.value]) {
                document.querySelector('input[name="organizer"]').value = organizers[this.value];
            }
        });
    </script>
</body>
</html>
