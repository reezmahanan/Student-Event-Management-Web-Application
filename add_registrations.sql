-- Add registrations for existing events and users
-- This will populate the database with sample registration data

-- Insert registrations (distribute across existing events)
INSERT IGNORE INTO registrations (user_id, event_id, status, registration_date)
SELECT 
    u.user_id,
    e.event_id,
    'confirmed',
    DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 30) DAY)
FROM users u
CROSS JOIN events e
WHERE u.user_type = 'student'
AND RAND() < 0.3  -- 30% chance of registration
ORDER BY RAND()
LIMIT 100;

-- Add some feedback entries
INSERT IGNORE INTO feedback (user_id, event_id, rating, comments, submitted_at)
SELECT 
    r.user_id,
    r.event_id,
    FLOOR(3 + (RAND() * 3)) as rating,
    CASE FLOOR(RAND() * 10)
        WHEN 0 THEN 'Excellent event! Learned a lot and enjoyed the experience.'
        WHEN 1 THEN 'Great content and well organized. Would attend again.'
        WHEN 2 THEN 'Very informative session with practical examples.'
        WHEN 3 THEN 'Amazing experience! The team did a fantastic job.'
        WHEN 4 THEN 'Helpful insights and great networking opportunities.'
        WHEN 5 THEN 'Best event I have attended so far. Highly recommend!'
        WHEN 6 THEN 'Good event but could use more hands-on activities.'
        WHEN 7 THEN 'Interesting topic and knowledgeable speakers.'
        WHEN 8 THEN 'Well organized event with great participation.'
        ELSE 'Enjoyed the event and met interesting people.'
    END as comment,
    DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 20) DAY)
FROM registrations r
WHERE RAND() < 0.4  -- 40% of registrations leave feedback
LIMIT 50;

-- Update event participant counts
UPDATE events e
SET current_participants = (
    SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.event_id AND r.status = 'confirmed'
);

SELECT 'Sample data added successfully!' as status;
SELECT 
    (SELECT COUNT(*) FROM events) as total_events,
    (SELECT COUNT(*) FROM registrations) as total_registrations,
    (SELECT COUNT(*) FROM users WHERE user_type = 'student') as total_students,
    (SELECT COUNT(*) FROM feedback) as total_feedback;
