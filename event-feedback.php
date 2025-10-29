<?php
require_once __DIR__ . '/php/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$success_message = '';
$error_message = '';

if ($event_id <= 0) {
    header('Location: profile.php');
    exit();
}

// Fetch event details
try {
    $stmt = $db->prepare("SELECT * FROM events WHERE event_id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $event = false;
}

if (!$event) {
    die('Event not found');
}

// Check if user is registered for this event
try {
    $stmt = $db->prepare("SELECT * FROM registrations WHERE event_id = ? AND user_id = ?");
    $stmt->execute([$event_id, $_SESSION['user_id']]);
    $registration = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $registration = false;
}

if (!$registration) {
    die('You must be registered for this event to give feedback');
}

// Check if user already gave feedback
try {
    $stmt = $db->prepare("SELECT * FROM feedback WHERE event_id = ? AND user_id = ?");
    $stmt->execute([$event_id, $_SESSION['user_id']]);
    $existing_feedback = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $existing_feedback = false;
}

// Process feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$existing_feedback) {
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comments = trim($_POST['comments'] ?? '');
    
    if ($rating < 1 || $rating > 5) {
        $error_message = 'Please select a rating between 1 and 5 stars';
    } elseif (empty($comments)) {
        $error_message = 'Please provide your feedback comments';
    } else {
        try {
            $stmt = $db->prepare("
                INSERT INTO feedback (user_id, event_id, rating, comments, submitted_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$_SESSION['user_id'], $event_id, $rating, $comments]);
            $success_message = 'Thank you for your feedback!';
            $existing_feedback = true; // Prevent resubmission
        } catch (Exception $e) {
            $error_message = 'Error submitting feedback: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Give Feedback - <?php echo htmlspecialchars($event['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .feedback-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 700px;
            margin: 0 auto;
        }
        .feedback-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
        }
        .star-rating {
            font-size: 3rem;
            cursor: pointer;
        }
        .star-rating i {
            color: #ddd;
            transition: color 0.2s;
        }
        .star-rating i.active,
        .star-rating i:hover,
        .star-rating i:hover ~ i {
            color: #ffc107;
        }
        .btn-submit {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            padding: 12px 40px;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="mb-3">
            <a href="profile.php" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>

        <div class="feedback-card">
            <div class="feedback-header text-center">
                <h2><i class="fas fa-star me-2"></i>Give Feedback</h2>
                <p class="mb-0"><?php echo htmlspecialchars($event['title']); ?></p>
            </div>

            <div class="card-body p-4">
                <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <div class="text-center mt-3">
                    <a href="profile.php" class="btn btn-primary">Return to Profile</a>
                    <a href="event-details.php?id=<?php echo $event_id; ?>" class="btn btn-outline-primary">View Event</a>
                </div>
                <?php elseif ($existing_feedback): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> You have already submitted feedback for this event.
                </div>
                <div class="text-center">
                    <a href="profile.php" class="btn btn-primary">Return to Profile</a>
                </div>
                <?php else: ?>
                
                <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <form method="POST" id="feedbackForm">
                    <div class="mb-4 text-center">
                        <label class="form-label fw-bold fs-5">How would you rate this event?</label>
                        <div class="star-rating" id="starRating">
                            <i class="fas fa-star" data-rating="1"></i>
                            <i class="fas fa-star" data-rating="2"></i>
                            <i class="fas fa-star" data-rating="3"></i>
                            <i class="fas fa-star" data-rating="4"></i>
                            <i class="fas fa-star" data-rating="5"></i>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="0" required>
                        <div class="mt-2">
                            <span id="ratingText" class="text-muted">Click to rate</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="comments" class="form-label fw-bold">Your Feedback</label>
                        <textarea class="form-control" id="comments" name="comments" rows="6" 
                                  placeholder="Share your experience about this event..." required></textarea>
                        <div class="form-text">Tell us what you liked or what could be improved</div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-submit">
                            <i class="fas fa-paper-plane"></i> Submit Feedback
                        </button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Star rating functionality
        const stars = document.querySelectorAll('.star-rating i');
        const ratingInput = document.getElementById('ratingInput');
        const ratingText = document.getElementById('ratingText');
        const ratingLabels = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
        
        let selectedRating = 0;

        stars.forEach(star => {
            star.addEventListener('click', function() {
                selectedRating = parseInt(this.getAttribute('data-rating'));
                ratingInput.value = selectedRating;
                updateStars(selectedRating);
                ratingText.textContent = ratingLabels[selectedRating];
            });

            star.addEventListener('mouseenter', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                updateStars(rating);
            });
        });

        document.querySelector('.star-rating').addEventListener('mouseleave', function() {
            updateStars(selectedRating);
        });

        function updateStars(rating) {
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
        }

        // Form validation
        document.getElementById('feedbackForm').addEventListener('submit', function(e) {
            if (selectedRating === 0) {
                e.preventDefault();
                alert('Please select a star rating before submitting');
                return false;
            }
        });
    </script>
</body>
</html>
