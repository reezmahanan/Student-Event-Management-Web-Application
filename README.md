
# EventHub - Student Event Management System

A comprehensive web-based platform for managing college/university events. Students can browse, register for events, and provide feedback, while administrators have full control over event creation, management, and analytics.

## 🌟 Features

### Student Features
- 📅 Browse events in an interactive calendar view with month navigation
- ✅ One-click event registration with real-time availability updates
- 👤 Personal profile dashboard showing registered events
- 🎯 Volunteer application system for events
- ⭐ Rate and provide feedback after attending events
- 🔔 Smart validation prevents registration for past events
- 📧 Email notifications for event updates

### Admin Features
- 🎨 Create and edit events with rich descriptions and images
- 💰 Track event budgets with detailed line items (estimated vs actual costs)
- 📊 Comprehensive dashboard with statistics and analytics
- 👥 View all registrations and manage attendees
- 🤝 Manage volunteer applications (approve/decline)
- 📈 Event performance metrics and feedback analysis
- 📷 Event gallery management
- 🗓️ Calendar view with automatic past/upcoming status detection

## 🛠️ Technology Stack
- **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend:** PHP 8.x
- **Database:** MySQL/MariaDB
- **Server:** Apache (XAMPP)
- **Security:** PDO prepared statements, password hashing (bcrypt), session management

## 📋 Requirements
- XAMPP (or similar LAMP/WAMP stack)
- PHP 8.0 or newer
- MySQL/MariaDB 5.7+ (running on port 3307 by default)
- Modern web browser (Chrome, Firefox, Edge, Safari)

## 🚀 Installation

### Step 1: Clone the Repository
```bash
git clone https://github.com/reezmahanan/Student-Event-Management-Web-Application.git
```

### Step 2: Setup Project Directory
Copy the project to your web server root:
```bash
# For XAMPP on Windows
Copy to: C:\xampp\htdocs\project

# For XAMPP on Linux/Mac
Copy to: /opt/lampp/htdocs/project
```

### Step 3: Create Database
1. Start XAMPP (Apache and MySQL services)
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. The `db_structure.sql` file will automatically:
   - Drop existing `eventhub` database (if exists)
   - Create new `eventhub` database
   - Create all 10 required tables
   - Insert sample data (10 students, 1 admin, 10 events)

4. **Import the SQL file:**
   - Click "Import" tab in phpMyAdmin
   - Choose `db_structure.sql` from the project root
   - Click "Go" to execute

### Step 4: Configure Database Connection
Check `php/config.php` for your environment:
```php
private $host = "localhost";
private $port = "3307";        // Change if MySQL uses different port (default: 3306)
private $db_name = "eventhub";
private $username = "root";
private $password = "";        // Add password if MySQL requires authentication
```

### Step 5: Access the Application
- **Homepage:** `http://localhost/project/`
- **Login Page:** `http://localhost/project/login.php`
- **Admin Dashboard:** `http://localhost/project/admin/dashboard.php`

## 🔐 Default Test Credentials

### Admin Account
- **Email:** `admin@eventhub.com`
- **Password:** `password123`
- **Access:** Full administrative privileges

### Student Accounts (10 test accounts)
| Name | Email | Password | Student ID |
|------|-------|----------|------------|
| John Doe | `john@student.com` | `password123` | STU2024001 |
| Jane Smith | `jane@student.com` | `password123` | STU2024002 |
| Mike Johnson | `mike@student.com` | `password123` | STU2024003 |
| Sarah Williams | `sarah@student.com` | `password123` | STU2024004 |
| Tom Brown | `tom@student.com` | `password123` | STU2024005 |
| Emily Davis | `emily@student.com` | `password123` | STU2024006 |
| David Wilson | `david@student.com` | `password123` | STU2024007 |
| Lisa Anderson | `lisa@student.com` | `password123` | STU2024008 |
| Robert Taylor | `robert@student.com` | `password123` | STU2024009 |
| Jennifer Martinez | `jennifer@student.com` | `password123` | STU2024010 |

## 🗄️ Database Structure

### Core Tables (10 tables)
1. **users** - Student and admin accounts with authentication
2. **events** - Complete event details (title, date, venue, capacity)
3. **registrations** - Event registration records linking users to events
4. **volunteers** - Volunteer applications with approval status
5. **feedback** - Student ratings and comments for attended events
6. **event_feedback** - Extended feedback system with recommendations
7. **event_categories** - Event classification and organization
8. **event_budgets** - Budget tracking with estimated and actual costs
9. **event_gallery** - Event photo management system
10. **notifications** - User notification and messaging system

### Key Relationships
- Foreign keys maintain referential integrity
- Cascade delete for dependent records
- Unique constraints prevent duplicate registrations
- Indexed columns for optimized query performance

## 🔒 Security Features
- **Password Hashing:** bcrypt algorithm with cost factor 10
- **SQL Injection Prevention:** PDO prepared statements throughout
- **Session Management:** Secure session handling with proper logout
- **Input Validation:** Server-side validation for all user inputs
- **XSS Protection:** HTML entity encoding for output
- **Access Control:** Role-based authentication (admin/student)

## 📁 Project Structure
```
project/
├── admin/                      # Admin panel files
│   ├── dashboard.php          # Admin dashboard with statistics
│   ├── create-event.php       # Event creation form
│   └── manage-events.php      # Event management interface
├── assets/                    # Static resources
│   ├── css/
│   │   └── style.css         # Main stylesheet (744 lines)
│   └── images/               # Event images and uploads
├── php/                       # Backend PHP scripts
│   ├── config.php            # Database configuration
│   ├── login.php             # Authentication logic
│   ├── register.php          # User registration
│   ├── events.php            # Event operations
│   └── calendar.php          # Calendar functionality
├── js/                        # JavaScript files
│   ├── main.js               # Core functionality
│   └── validation.js         # Form validation
├── db_structure.sql          # Complete database schema + sample data
├── index.php                 # Landing page
├── login.php                 # Login interface
├── register.php              # Registration interface
└── profile.php               # User profile page
```

## 🎯 Key Functionality

### Event Registration System
- Real-time capacity checking
- Duplicate registration prevention
- Automatic participant counter updates
- Waitlist support for full events

### Calendar View
- Interactive month-by-month navigation
- Color-coded event status (past/upcoming)
- Date-based filtering
- Responsive design for mobile devices

### Budget Management
- Line-item budget tracking
- Estimated vs actual cost comparison
- Automatic total calculations
- Per-event budget reports

### Volunteer Management
- Application submission system
- Admin approval workflow
- Status tracking (pending/approved/declined)
- Role assignment functionality

## 🚨 Troubleshooting

### Login Issues
**Problem:** "Invalid email or password" error  
**Solution:** 
1. Verify MySQL is running on the correct port (check phpMyAdmin)
2. Ensure `db_structure.sql` was imported successfully
3. Check `php/config.php` has correct database credentials
4. All test accounts use password: `password123`

### Database Connection Error
**Problem:** "Database connection error" message  
**Solution:**
1. Start MySQL service in XAMPP Control Panel
2. Verify port number in `php/config.php` (usually 3306 or 3307)
3. Check MySQL username/password match configuration
4. Test connection in phpMyAdmin first

### Events Not Showing
**Problem:** Calendar or event list is empty  
**Solution:**
1. Re-import `db_structure.sql` to restore sample data
2. Check browser console for JavaScript errors
3. Verify Apache and MySQL services are running

### Permission Denied
**Problem:** Cannot access admin pages as student  
**Solution:**
- This is expected behavior
- Use admin account: `admin@eventhub.com` / `password123`
- Check session is active (try logging out and back in)

## 🔧 Configuration Options

### Change MySQL Port
Edit `php/config.php`:
```php
private $port = "3306";  // Change to your MySQL port
```

### Enable Debug Mode
Add to `php/config.php`:
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

### Customize Event Images
- Upload images to `assets/images/`
- Update `image_url` field in `events` table
- Or use Unsplash URLs (current default)

## 📊 Sample Data Included
- **10 Events:** Various types (workshops, seminars, competitions, festivals)
- **11 Users:** 10 students + 1 admin
- **Multiple Registrations:** Pre-populated event sign-ups
- **Feedback Samples:** Example ratings and comments
- **Date Range:** Events spanning November 2025 - January 2026

## 🤝 Contributing
Contributions are welcome! Please follow these guidelines:
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License
This project is open source and available for educational purposes.

## 👨‍💻 Author
**Reez Mahanan**
- GitHub: [@reezmahanan](https://github.com/reezmahanan)
- Repository: [Student-Event-Management-Web-Application](https://github.com/reezmahanan/Student-Event-Management-Web-Application)

## 🙏 Acknowledgments
- Bootstrap 5 for responsive UI components
- Font Awesome for icons
- Unsplash for sample event images
- XAMPP for local development environment

---

**Version:** 1.0  
**Last Updated:** November 2025  
**Status:** ✅ Active Development


