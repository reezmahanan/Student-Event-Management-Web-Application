
# Student Event Management System

A web application for managing college events built with PHP and MySQL. Students can browse and register for events while administrators can create and manage events.

## Features

### For Students
- View all upcoming events
- Register for events
- View registered events in profile
- Submit feedback and ratings for events
- Volunteer for events

### For Administrators  
- Create and manage events
- View all registrations
- Manage event budgets
- Approve/decline volunteer applications
- View statistics and reports

## Technologies Used
- HTML, CSS, Bootstrap 5
- JavaScript
- PHP 8
- MySQL/MariaDB
- Apache Server (XAMPP)

## Installation Steps

1. Download/Clone this repository
```bash
git clone https://github.com/reezmahanan/Student-Event-Management-Web-Application.git
```

2. Copy to XAMPP htdocs folder
```
C:\xampp\htdocs\project
```

3. Start XAMPP (Apache and MySQL)

4. Create Database
- Open phpMyAdmin (http://localhost/phpmyadmin)
- Import the `db_structure.sql` file
- This will create the `eventhub` database with sample data

5. Configure Database (if needed)
- Open `php/config.php`
- Update MySQL port if different (default: 3307)
```php
private $port = "3307";  // Change if needed
```

6. Access the Application
- Homepage: http://localhost/project/
- Login: http://localhost/project/login.php
- Admin: http://localhost/project/admin/dashboard.php

## Test Accounts

### Admin Login
Email: admin@eventhub.com  
Password: password123

### Student Login
Email: jane@student.com  
Password: password123

(More student accounts available - check db_structure.sql)

## Database Tables
- users - User accounts (students and admin)
- events - Event information
- registrations - Event registrations
- volunteers - Volunteer applications
- feedback - Event feedback from students
- event_budgets - Budget tracking
- event_categories - Event categories
- event_gallery - Event photos
- notifications - User notifications
- event_feedback - Additional feedback system

## Project Structure
```
project/
├── admin/              - Admin panel files
├── assets/            
│   ├── css/           - Stylesheets
│   └── images/        - Event images
├── php/               - Backend PHP scripts
├── js/                - JavaScript files
├── db_structure.sql   - Database file
├── index.php          - Homepage
├── login.php          - Login page
├── register.php       - Registration page
└── profile.php        - User profile
```

## Common Issues

**Cannot login:**
- Check if MySQL is running in XAMPP
- Make sure db_structure.sql is imported
- Use password: password123 for all test accounts

**Database connection error:**
- Verify MySQL port in php/config.php
- Default port is 3307 (sometimes 3306)

**Events not showing:**
- Re-import db_structure.sql file
- Check if Apache and MySQL are running

## Security Features Implemented
- Password hashing using bcrypt
- SQL injection prevention with PDO prepared statements
- Session-based authentication
- Role-based access control (admin/student)

## Screenshots
(Add screenshots here if needed)

## Future Improvements
- Email notifications for event reminders
- QR code generation for event tickets
- Payment integration for paid events
- Mobile responsive improvements
- Export reports to PDF

## Author
Reezma Hanan  
GitHub: https://github.com/reezmahanan

## Acknowledgments
- Bootstrap for UI components
- Font Awesome for icons
- XAMPP for development environment



