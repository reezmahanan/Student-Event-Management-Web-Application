# 🎓 EventHub — Student Event Management System

A comprehensive web-based event management system for educational institutions, built with PHP and MySQL.

---

## 🌟 Features

### For Students
- 📅 Event Calendar — Browse upcoming events with images  
- 🔐 User Registration & Login — Secure authentication  
- ✅ Event Registration — Register for events with one click  
- 👤 User Profile — View registered events and manage your account  
- ⭐ Event Feedback — Rate and review attended events

### For Administrators
- 📊 Dashboard — Overview of events, users, and statistics  
- 📅 Calendar Management — Create, edit, delete events with month navigation  
- 👥 User Management — View and manage student accounts  
- 📸 Event Images — Upload or link images for events  
- 📈 Analytics — Track registrations, feedback, and engagement

---

## 🛠️ Technology Stack
- **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript  
- **Backend:** PHP 8.0+  
- **Database:** MySQL / MariaDB  
- **Server:** Apache (XAMPP recommended)

---

## 📋 Prerequisites
- XAMPP (or similar) with Apache and MySQL  
- PHP 8.0 or higher  
- MySQL 5.7 or higher (project examples use port **3307**, change if your setup differs)

---

## 🚀 Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/reezmahanan/Student-Event-Management-Web-Application.git eventhub
   ```

2. Move the project into your web server document root:
   - On Windows (XAMPP):
     - Copy the folder to C:\xampp\htdocs\project (or any folder name you prefer):
       ```powershell
       xcopy /E /I eventhub C:\xampp\htdocs\project
       ```
     - Or using File Explorer copy/paste.
   - On Linux / macOS:
     ```bash
     cp -r eventhub /opt/lampp/htdocs/project    # or your web root
     ```

3. Create the database:
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Create a new database named `eventhub`
   - Import the SQL schema from the project root file `db_structure.sql`

4. Configure database connection:
   - Edit `php/config.php` (or the config file your project uses) and update credentials:
     ```php
     private $host = "localhost";
     private $port = "3307";      // change to 3306 if your MySQL uses the default port
     private $db_name = "eventhub";
     private $username = "root";
     private $password = "";
     ```

5. Access the website:
   - Homepage: http://localhost/project/
   - Admin Dashboard: http://localhost/project/admin/dashboard.php

---

## 🔑 Default Login Credentials

> Important: Change these passwords after first login.

### Admin
- Email: `admin@eventhub.com`  
- Password: `password123`

### Student
- Email: `john@student.com`  
- Password: `password123`

---

## 📊 Database Schema

Main tables included:
- `users` — User accounts (students and admins)  
- `events` — Event details with images  
- `registrations` — Event registrations  
- `feedback` — Event feedback and ratings  
- `volunteers` — Event volunteer applications  
- `event_categories` — Event categorization  
- `event_budgets` — Budget tracking  
- `notifications` — User notifications

(See `db_structure.sql` for full table definitions.)

---

## 🎨 Key UI / UX Features
- Animated, dark-themed homepage with loading animation  
- Glassmorphism effects, smooth transitions and parallax effects  
- Responsive calendar with month-by-month navigation and filters  
- Event images pulled from Unsplash by default (can be replaced with local images)

---

## 🗓️ Event Calendar
- Month navigation (Previous / Next)  
- Event thumbnails (Unsplash by default)  
- Filter by date and category  
- Responsive layout for mobile and desktop

---

## 🧭 Admin Dashboard
- Real-time statistics and quick action buttons  
- Calendar view for event management  
- User engagement and registration analytics

---

## 📁 Project Structure (example)
```
/index.php                - Homepage
/profile.php              - User profile
/db_structure.sql         - Database schema SQL file
/events-calendar.php      - Event listing
/login.php                - Login page
/register.php             - Registration page
/admin/
    ├── dashboard.php     - Admin home
    ├── manage-events.php - Event management
    └── create-event.php  - Create new events
/assets/
    ├── css/
    ├── js/
    └── images/
php/
    └── config.php         - Database configuration
```

---

## 🔧 Configuration

### Database Port
If your MySQL server uses a different port than the default shown, edit `php/config.php` and update:
```php
private $port = "3306"; // or your MySQL port
```

### Images
By default, event images are linked from Unsplash. To use local images:
1. Upload images to `assets/images/`
2. Update the `image_url` (or image field) in the `events` table to point to your uploaded files (e.g., `assets/images/my-event.jpg`)

---


## 👨‍💻 Author
Reezma Hanan — GitHub: [@reezmahanan](https://github.com/reezmahanan)


```


