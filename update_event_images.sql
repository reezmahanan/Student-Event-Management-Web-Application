-- Update existing events with Unsplash image URLs
-- Run this after executing add_missing_columns.sql

UPDATE events SET 
    image_url = 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800',
    organizer = 'CS Department'
WHERE title = 'Web Development Workshop';

UPDATE events SET 
    image_url = 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800',
    organizer = 'Career Services'
WHERE title = 'Career Guidance Seminar';

UPDATE events SET 
    image_url = 'https://images.unsplash.com/photo-1452587925148-ce544e77e70d?w=800',
    organizer = 'Arts Club'
WHERE title = 'Photography Exhibition';

UPDATE events SET 
    image_url = 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=800',
    organizer = 'Sports Committee'
WHERE title = 'Annual Sports Day 2024';

UPDATE events SET 
    image_url = 'https://images.unsplash.com/photo-1555255707-c07966088b7b?w=800',
    organizer = 'Tech Club'
WHERE title = 'AI & Machine Learning Workshop';

UPDATE events SET 
    image_url = 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=800',
    organizer = 'Cultural Committee'
WHERE title = 'Cultural Fest - Rang De';

UPDATE events SET 
    image_url = 'https://images.unsplash.com/photo-1559136555-9303baea8ebd?w=800',
    organizer = 'Entrepreneurship Cell'
WHERE title = 'Startup Pitch Competition';

UPDATE events SET 
    image_url = 'https://images.unsplash.com/photo-1504639725590-34d0984388bd?w=800',
    organizer = 'Tech Club'
WHERE title = 'Hackathon 2024: Code for Change';

UPDATE events SET 
    image_url = 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=800',
    organizer = 'Music Society'
WHERE title = 'Music Concert - Live Band Night';

UPDATE events SET 
    image_url = 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=800',
    organizer = 'CS Department'
WHERE title = 'Database Design Seminar';
