-- Fix Duplicate Events in Database
-- Run this SQL script in phpMyAdmin to remove duplicate events

-- Step 1: Check for duplicates (Run this first to see the problem)
SELECT title, event_date, event_time, COUNT(*) as count
FROM events
GROUP BY title, event_date, event_time
HAVING count > 1;

-- Step 2: Keep only one copy of each event and delete duplicates
-- This keeps the event with the smallest event_id for each duplicate set
DELETE e1 FROM events e1
INNER JOIN events e2 
WHERE e1.event_id > e2.event_id 
AND e1.title = e2.title 
AND e1.event_date = e2.event_date 
AND e1.event_time = e2.event_time;

-- Step 3: Verify duplicates are gone
SELECT title, event_date, event_time, COUNT(*) as count
FROM events
GROUP BY title, event_date, event_time
HAVING count > 1;

-- If no results from Step 3, duplicates are successfully removed!
