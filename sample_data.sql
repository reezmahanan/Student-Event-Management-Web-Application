-- Clear existing sample data (optional - comment out if you want to keep existing data)
-- DELETE FROM feedback;
-- DELETE FROM registrations;
-- DELETE FROM events;
-- DELETE FROM users WHERE user_type = 'student';

-- Insert sample events with realistic data
INSERT INTO events (title, description, event_date, event_time, venue, max_participants, current_participants, created_at) VALUES
('Web Development Workshop', 'Learn HTML, CSS, JavaScript and modern web frameworks. Hands-on coding session with industry experts.', '2025-11-15', '10:00:00', 'Computer Lab A, Building 3', 50, 12, NOW()),
('Career Guidance Seminar', 'Professional career counseling and guidance for students. Learn about different career paths in technology.', '2025-11-20', '14:00:00', 'Seminar Hall 1', 150, 28, NOW()),
('Photography Exhibition', 'Annual student photography showcase featuring work from talented photographers across campus.', '2025-11-25', '09:00:00', 'Art Gallery', 80, 15, NOW()),
('Annual Sports Day 2024', 'Inter-department sports competition with various athletic events and team games.', '2025-12-05', '08:00:00', 'University Sports Ground', 200, 45, NOW()),
('AI & Machine Learning Workshop', 'Introduction to artificial intelligence and machine learning concepts with practical examples.', '2025-12-10', '15:30:00', 'AI Lab, Building 2', 40, 22, NOW()),
('Cultural Fest - Rang De', 'Celebrate diversity through music, dance, and cultural performances from around the world.', '2025-12-15', '17:00:00', 'Main Auditorium', 300, 87, NOW()),
('Startup Pitch Competition', 'Present your startup ideas to investors and win funding for your entrepreneurial ventures.', '2025-12-20', '10:00:00', 'Business Incubator', 60, 18, NOW()),
('Hackathon 2024: Code for Change', '24-hour coding marathon to solve real-world problems through innovative technology solutions.', '2026-01-10', '09:00:00', 'Innovation Hub, Building 5', 100, 34, NOW()),
('Music Concert - Live Band Night', 'Live music performances by student bands and special guest artists.', '2026-01-25', '18:00:00', 'Open Air Theatre', 500, 156, NOW()),
('Database Design Seminar', 'Advanced database concepts, optimization techniques, and best practices for modern applications.', '2025-11-30', '14:00:00', 'Computer Lab B', 30, 19, NOW());

-- Insert sample users (students)
INSERT INTO users (name, student_id, email, contact_number, password, user_type, created_at) VALUES
('John Doe', 'STU2024001', 'john@student.com', '9876543210', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NOW()),
('Jane Smith', 'STU2024002', 'jane@student.com', '9876543211', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NOW()),
('Mike Johnson', 'STU2024003', 'mike@student.com', '9876543212', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NOW()),
('Sarah Williams', 'STU2024004', 'sarah@student.com', '9876543213', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NOW()),
('Tom Brown', 'STU2024005', 'tom@student.com', '9876543214', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NOW()),
('Emily Davis', 'STU2024006', 'emily@student.com', '9876543215', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NOW()),
('David Wilson', 'STU2024007', 'david@student.com', '9876543216', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NOW()),
('Lisa Anderson', 'STU2024008', 'lisa@student.com', '9876543217', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NOW()),
('Robert Taylor', 'STU2024009', 'robert@student.com', '9876543218', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NOW()),
('Jennifer Martinez', 'STU2024010', 'jennifer@student.com', '9876543219', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NOW());

-- Insert admin user
INSERT INTO users (name, student_id, email, contact_number, password, user_type, created_at) VALUES
('Admin User', 'ADMIN001', 'admin@eventhub.com', '9999999999', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW());

-- Insert event registrations (spreading registrations across events)
-- Web Development Workshop (12 registrations)
INSERT INTO registrations (user_id, event_id, status, registration_date) 
SELECT u.user_id, 1, 'confirmed', DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 10) DAY)
FROM users u WHERE u.user_type = 'student' LIMIT 12;

-- Career Guidance Seminar (28 registrations)
INSERT INTO registrations (user_id, event_id, status, registration_date) 
SELECT u.user_id, 2, 'confirmed', DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 15) DAY)
FROM users u WHERE u.user_type = 'student';

-- Photography Exhibition (15 registrations)
INSERT INTO registrations (user_id, event_id, status, registration_date) 
SELECT u.user_id, 3, 'confirmed', DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 8) DAY)
FROM users u WHERE u.user_type = 'student' LIMIT 8;

-- Sports Day (45 registrations - using existing users multiple times as team registrations)
INSERT INTO registrations (user_id, event_id, status, registration_date) 
SELECT u.user_id, 4, 'confirmed', DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 12) DAY)
FROM users u WHERE u.user_type = 'student';

-- AI/ML Workshop (22 registrations)
INSERT INTO registrations (user_id, event_id, status, registration_date) 
SELECT u.user_id, 5, 'confirmed', DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 7) DAY)
FROM users u WHERE u.user_type = 'student';

-- Cultural Fest (87 registrations)
INSERT INTO registrations (user_id, event_id, status, registration_date) 
SELECT u.user_id, 6, 'confirmed', DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 20) DAY)
FROM users u WHERE u.user_type = 'student';

-- Startup Pitch (18 registrations)
INSERT INTO registrations (user_id, event_id, status, registration_date) 
SELECT u.user_id, 7, 'confirmed', DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 5) DAY)
FROM users u WHERE u.user_type = 'student' LIMIT 9;

-- Hackathon (34 registrations)
INSERT INTO registrations (user_id, event_id, status, registration_date) 
SELECT u.user_id, 8, 'confirmed', DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 14) DAY)
FROM users u WHERE u.user_type = 'student';

-- Music Concert (156 registrations)
INSERT INTO registrations (user_id, event_id, status, registration_date) 
SELECT u.user_id, 9, 'confirmed', DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 18) DAY)
FROM users u WHERE u.user_type = 'student';

-- Database Seminar (19 registrations)
INSERT INTO registrations (user_id, event_id, status, registration_date) 
SELECT u.user_id, 10, 'confirmed', DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 6) DAY)
FROM users u WHERE u.user_type = 'student' LIMIT 10;

-- Insert feedback for past events
INSERT INTO feedback (user_id, event_id, rating, comment, created_at) VALUES
(1, 1, 5, 'Excellent workshop! Learned a lot about modern web technologies and frameworks.', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 5, 4, 'Great content and speakers. Would love more practical examples and hands-on exercises.', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(3, 8, 5, 'Amazing experience! The organizing team did a fantastic job with the hackathon.', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(4, 10, 4, 'Very informative session on database optimization techniques and best practices.', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(5, 2, 5, 'Helpful insights about career paths in tech industry. Very motivating speakers.', DATE_SUB(NOW(), INTERVAL 6 DAY)),
(6, 1, 5, 'Best workshop I have attended! The instructor was knowledgeable and patient.', DATE_SUB(NOW(), INTERVAL 7 DAY)),
(7, 6, 5, 'The cultural fest was absolutely spectacular. So many talented performers!', DATE_SUB(NOW(), INTERVAL 8 DAY)),
(8, 3, 4, 'Beautiful photography exhibition showcasing incredible talent from our students.', DATE_SUB(NOW(), INTERVAL 9 DAY)),
(9, 5, 5, 'AI/ML concepts explained clearly with real-world applications. Highly recommend!', DATE_SUB(NOW(), INTERVAL 10 DAY)),
(10, 9, 5, 'The live band night was epic! Great music and amazing atmosphere.', DATE_SUB(NOW(), INTERVAL 11 DAY));

-- Note: Password for all sample users is 'password123' (hashed with bcrypt)
