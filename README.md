# EventHub - Student Event Management System

A comprehensive web-based event management system for educational institutions, built with PHP and MySQL.

## 🌟 Features

### For Students
- 📅 **Event Calendar** - Browse upcoming events with beautiful images
- 🔐 **User Registration & Login** - Secure authentication system
- ✅ **Event Registration** - Register for events with one click
- 👤 **User Profile** - View registered events and manage account
- ⭐ **Event Feedback** - Rate and review attended events

### For Administrators
- 📊 **Dashboard** - Overview of all events, users, and statistics
- 📅 **Calendar Management** - Create, edit, delete events with month navigation
- 👥 **User Management** - View and manage student accounts
- 📸 **Event Images** - Add beautiful images to events
- 📈 **Analytics** - Track registrations, feedback, and engagement

## 🛠️ Technology Stack

- **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend:** PHP 8.0+
- **Database:** MySQL/MariaDB
- **Server:** Apache (XAMPP recommended)

## 📋 Prerequisites

- XAMPP (or similar) with Apache and MySQL
- PHP 8.0 or higher
- MySQL 5.7 or higher (running on port 3307)

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/eventhub.git
   ```

2. **Move to XAMPP htdocs**
   ```bash
   cp -r eventhub C:/xampp/htdocs/project
   ```

3. **Create Database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create database named `eventhub`
   - Import `db_structure.sql/db_structure.sql`

4. **Configure Database Connection**
   - Edit `php/config.php`
   - Update database credentials if needed:
     ```php
     private $host = "localhost";
     private $port = "3307";
     private $db_name = "eventhub";
     private $username = "root";
     private $password = "";
     ```

5. **Access the Website**
   - Homepage: http://localhost/project/
   - Admin Dashboard: http://localhost/project/admin/dashboard.php

## 🔑 Default Login Credentials

### Admin Account
- Email: `admin@eventhub.com`
- Password: `password123`

### Student Account
- Email: `john@student.com`
- Password: `password123`

**⚠️ Change these passwords after first login!**

## 📊 Database Schema

The system includes 8 main tables:
- `users` - User accounts (students and admins)
- `events` - Event information with images
- `registrations` - Event registrations
- `feedback` - Event feedback and ratings
- `volunteers` - Event volunteer applications
- `event_categories` - Event categorization
- `event_budgets` - Budget tracking
- `notifications` - User notifications

## 🎨 Key Features

### Animated Homepage
- Dark-themed design with loading animation
- Glassmorphism effects
- Smooth transitions and parallax effects

### Event Calendar
- Month-by-month navigation (Previous/Next buttons)
- Event images from Unsplash
- Filter by date and category
- Responsive design

### Admin Dashboard
- Real-time statistics
- Quick action buttons
- Event management with calendar view
- User analytics

## 📱 Pages Structure

```
/index.php - Homepage
/profile.php - User Profile
/db_structure.sql/
  ├── events-calendar.php - Event listing
  ├── login.php - Login page
  └── register.php - Registration page
/admin/
  ├── dashboard.php - Admin home
  ├── manage-events.php - Event management
  └── create-event.php - Create new events
```

## 🔧 Configuration

### Database Port
The default MySQL port is set to **3307** (XAMPP default). If your MySQL runs on a different port:

1. Edit `php/config.php`
2. Change the port number:
   ```php
   private $port = "3306"; // Your MySQL port
   ```

### Image URLs
Events use Unsplash images by default. To use custom images:
1. Upload images to `assets/images/`
2. Update `image_url` in database

## 🤝 Contributing

This is a private repository. Contact the owner for contribution guidelines.

## 📄 License

Private - All rights reserved

## 👨‍💻 Author

**Your Name**
- GitHub: [@yourusername](https://github.com/yourusername)

## 📞 Support

For issues or questions, contact: your.email@example.com

---

**Note:** This is a student project for educational purposes. Change all default passwords before deploying to production!
