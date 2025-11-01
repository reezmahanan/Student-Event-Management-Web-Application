
# EventHub - Student Event Management System

A web application for managing college/university events. Students can browse and register for events, while admins can create and manage all event details.

## Features

### Student Features
- Browse events in a calendar view (month by month)
- Register for events with one click
- View your registered events in your profile
- Give feedback and ratings after attending events
- Cannot register for events that already happened

### Admin Features
- Create and edit events with images
- Track event budgets (add budget items with costs)
- See all registrations and student details
- Manage user accounts
- View statistics (total events, registrations, etc.)
- Calendar view with month navigation
- Events show as "Past" or "Upcoming" automatically

## Technology Used
- HTML, CSS (Bootstrap 5), JavaScript
- PHP 8
- MySQL (or MariaDB)
- Apache (XAMPP recommended)

## Requirements
- XAMPP (includes Apache and MySQL)
- PHP 8 or newer
- MySQL running on port 3307 (adjustable in config.php)

## Installation
1. Clone or download this repository
   ```bash
   git clone https://github.com/reezmahanan/Student-Event-Management-Web-Application.git
   ```

2. Copy the project folder to your web root (XAMPP example)
   ```bash
   cp -r Student-Event-Management-Web-Application C:/xampp/htdocs/project
   ```

3. Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database called `eventhub`
   - Import the file `db_structure.sql`

4. Check database settings in `php/config.php`:
   ```php
   private $host = "localhost";
   private $port = "3307";  // Change if your MySQL uses a different port
   private $db_name = "eventhub";
   private $username = "root";
   private $password = "";  // Add your MySQL password if you have one
   ```

5. Open the website:
   - Student page: http://localhost/project/
   - Admin page: http://localhost/project/admin/dashboard.php

## Default Admin Credentials (example)
- Email: `admin@eventhub.com`
- Password: `password123`

## Default Students Credentials (example)
- Email: `jane@student.com`
- Password: `password123`  

## Database Tables
- `users` - Student and admin accounts
- `events` - All event details
- `registrations` - Who registered for which event
- `feedback` - Student ratings and comments
- `volunteers` - Volunteer applications
- `event_categories` - Event types/categories
- `event_budgets` - Budget items for each event (item name, estimated cost, actual cost)
- `notifications` - Messages for users

## Key Features
- Calendar view with month navigation
- Past/Upcoming status detection
- Budget line items with aggregated totals
- CSRF protection and secure session management
- PDO with prepared statements + password_hash()

## Configuration
- Change MySQL port in `php/config.php` if necessary.
- Events use Unsplash images by default; to use local images upload them to `assets/images/` and update `image_url` in DB.


## Author
**reezmahanan**
- GitHub: https://github.com/reezmahanan


