
# Student Event Management System

A web application for managing campus events built with PHP and MySQL. Students can browse and register for events while administrators can create and manage events.


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
- **Optional:** Run `update_event_images.sql` to add Unsplash images to events

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
- Email logging for development (email system ready for production)

## Email System
The application includes a complete email confirmation system:
- Registration confirmation emails
- Event registration notifications (template ready)
- Password reset emails (template ready)
- Development mode: Emails logged to `logs/emails.log`
- Production ready: Configure SMTP in `php/email-config.php`



## Screenshots
See the `event hub screenshots/` folder for comprehensive screenshots of all features including:
- Admin Dashboard & Quick Links
- Event Management & Creation
- User Profile & Calendar View
- Email Notifications
- Feedback System
- Reports & Analytics

## What Makes This Website Special

### Cool Features That Stand Out

**1. Everything About Events in One Place**
- Students can browse, register, volunteer, and give feedback all from one dashboard
- Admins get a separate powerful dashboard to create events, manage budgets, and track everything
- No need to jump between different pages - smooth navigation throughout

**2. Beautiful Card-Based Layout**
- Events displayed as clean, modern cards with eye-catching images
- Hover effects that make the UI feel alive and interactive
- Color-coded categories so you can spot tech events, sports, cultural activities instantly
- Smooth animations when you register or perform actions

**3. Calendar View That Actually Makes Sense**
- Visual monthly calendar showing all upcoming events
- Click on any date to see what's happening
- Color badges for different event types
- Way better than scrolling through endless lists!

**4. Smart Email System**
- Get instant confirmation emails when you register
- Reminders before events so you don't miss out
- Admins can send bulk notifications to all students at once

**5. Rate & Review Events**
- Star rating system (1-5 stars) just like Netflix or Amazon
- Share your honest feedback after attending events
- Helps improve future events based on real student opinions

**6. Admin Power Tools**
- Dashboard with charts and stats that look professional
- Manage hundreds of students and events without getting lost
- Quick action buttons for common tasks
- Reports that make sense even if you're not tech-savvy

## Design & User Experience

### Modern Aesthetic Design

**Visual Style:**
- Clean, minimalist interface - not cluttered or overwhelming
- Modern card-based layout (like Pinterest or Airbnb style)
- Professional blue and white color scheme with accent colors
- Smooth hover effects and transitions that feel premium
- Large, high-quality event images from Unsplash
- Rounded corners and subtle shadows for depth

**Navigation & Layout:**
- **Top Navigation Bar:** Always visible with login status and quick links
- **Student Dashboard:** 
  - Welcome hero section
  - Event cards in a grid layout
  - Sidebar filters for event categories
  - Easy access to profile, calendar, and registered events
  
- **Admin Dashboard:**
  - Stats cards showing key numbers (events, users, registrations)
  - Quick action buttons (Create Event, Manage Users, etc.)
  - Charts and graphs for visual data
  - Side menu for all admin functions
  
- **Calendar Page:**
  - Monthly calendar view
  - Event indicators on dates
  - Click for details with smooth popups

### Tech Stack

**Frontend:**
- Bootstrap 5 - responsive design framework
- Custom CSS - unique look and feel
- Font Awesome - clean icons
- JavaScript - form validation and interactivity

**Backend:**
- PHP 8 - handles logic and database operations
- MySQL - stores all data
- OOP approach - organized, clean code
- PHPMailer - professional email notifications

**Why This Stack?**
- Easy to learn for beginners
- Works on any computer with XAMPP
- No complex build tools needed
- Perfect for university projects

## Browser Support

Works perfectly on all modern browsers!

| Browser | Status |
|---------|--------|
| Chrome | Recommended |
| Firefox | Fully Supported |
| Edge | Fully Supported |
| Safari | Fully Supported |
| Opera | Fully Supported |

**Responsive Design:** Works on desktop, tablet, and mobile devices.

## Technical Features (For the Curious Minds)

### Security - We Take It Seriously
- Passwords are hashed with bcrypt (not stored as plain text!)
- Protection against SQL injection using prepared statements
- Session-based login system with timeout
- Only admins can access admin pages (role-based access)
- Forms have CSRF protection
- User inputs are sanitized to prevent XSS attacks

### Performance & Speed
- Fast loading times even with lots of events
- Images load only when needed (lazy loading)
- Database queries are optimized with indexes
- Sessions cache user data to reduce database hits
- Clean, efficient code = smooth experience

### Cool Functionality
- **Calendar Integration:** Interactive calendar that's actually useful
- **Real-time Updates:** See registrations update without refreshing
- **Email Notifications:** Professional HTML emails (not plain text!)
- **Search & Filter:** Find events by category, date, or keyword instantly
- **Image Handling:** Event images stored and displayed efficiently
- **Reports & Analytics:** Visual charts and graphs for admins
- **Activity Logging:** Track what's happening in the system

### Database Structure
- 10+ tables all connected properly
- Foreign keys to keep data consistent
- Sample data included so you can test right away
- ER diagram included in screenshots folder
- Easy to understand even if you're learning SQL

### Code Quality
- Object-Oriented PHP (classes, not messy functions everywhere)
- Comments explaining complex parts
- Reusable code components
- Organized file structure (easy to find things)
- Error messages that actually help you debug

**Bottom Line:** This isn't just thrown-together code. It's built properly, secure, and ready for real-world use (or your university project submission!)

## Future Improvements

- QR code generation for event tickets
- Payment integration for paid events
- Mobile app development (React Native)
- Export reports to PDF
- Event image upload functionality
- Real-time notifications with WebSocket
- Multi-language support
- Social media integration
- Advanced analytics dashboard
- Event recommendation system using AI

## Contributing

Want to make this project even better? Awesome! Here's how:

### Quick Start for Contributors

1. **Fork this repo** (button at the top right on GitHub)

2. **Clone your copy:**
   ```bash
   git clone https://github.com/YOUR-USERNAME/Student-Event-Management-Web-Application.git
   cd Student-Event-Management-Web-Application
   ```

3. **Create a new branch for your feature:**
   ```bash
   git checkout -b add-cool-feature
   ```

4. **Make your changes** - code away!

5. **Commit your work:**
   ```bash
   git add .
   git commit -m "Added cool feature that does XYZ"
   ```

6. **Push it up:**
   ```bash
   git push origin add-cool-feature
   ```

7. **Create a Pull Request** on GitHub and describe what you did

### Ideas for Contributions

Not sure what to work on? Try these:

- Fix bugs (check the Issues tab)
- Add new features (dark mode, anyone?)
- Improve the UI/UX
- Better mobile experience
- Add multi-language support
- Improve documentation
- Accessibility improvements
- Security enhancements
- Speed optimizations

### Need Help or Have Questions?

Feel free to open an issue or reach out if you need any support or have questions about the project!

## Author
Reezma Hanan  
GitHub: https://github.com/reezmahanan

## License
This project is open source and available for educational purposes.

## Acknowledgments
- Bootstrap for UI components
- Font Awesome for icons
- XAMPP for development environment
- PHP and MySQL communities for documentation and support
- Unsplash for high-quality event images
- My Web Technologies Lecturer for guidance and feedback








