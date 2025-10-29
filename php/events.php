<?php
// Include database connection
require_once __DIR__ . '/config.php';

// Function to get all events
function getAllEvents($db) {
    if (!$db) {
        return [];
    }
    try {
        $query = "SELECT * FROM events ORDER BY event_date DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

// Function to register for event
if (isset($_POST['register_event'])) {
    if (!isLoggedIn()) {
        echo "<script>alert('Please login first!'); window.location.href='../login.php';</script>";
        exit();
    }
    
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id'];
    
    try {
        // Check if already registered
        $check_query = "SELECT * FROM registrations WHERE user_id = :user_id AND event_id = :event_id";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(":user_id", $user_id);
        $check_stmt->bindParam(":event_id", $event_id);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            echo "<script>alert('You are already registered for this event!'); window.history.back();</script>";
        } else {
            // Register for event
            $register_query = "INSERT INTO registrations (user_id, event_id) VALUES (:user_id, :event_id)";
            $register_stmt = $db->prepare($register_query);
            $register_stmt->bindParam(":user_id", $user_id);
            $register_stmt->bindParam(":event_id", $event_id);
            
            if ($register_stmt->execute()) {
                echo "<script>alert('Successfully registered for the event!'); window.history.back();</script>";
            }
        }
    } catch(PDOException $exception) {
        echo "<script>alert('Error: " . $exception->getMessage() . "'); window.history.back();</script>";
    }
}

// Get all events
if ($db && $GLOBALS['db_connected']) {
    try {
        $events = getAllEvents($db);
    } catch (Exception $e) {
        $events = [];
    }
} else {
    $events = [];
}
?>
<script>
// AJAX Event Registration
document.querySelectorAll('form[action="php/events.php"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Show loading
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';
        submitBtn.disabled = true;
        
        fetch('php/events.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            // Show success message
            alert('Registration successful!');
            submitBtn.innerHTML = '<i class="fas fa-check"></i> Registered';
            submitBtn.classList.remove('btn-primary');
            submitBtn.classList.add('btn-success');
            submitBtn.disabled = true;
        })
        .catch(error => {
            alert('Registration failed!');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
});
</script>