# Hairdresser Booking System

A Laravel-based booking platform for hairdresser appointments with basic functionality including reservation management and admin dashboard.

![Laravel](https://img.shields.io/badge/Laravel-10.x-red)
![PHP](https://img.shields.io/badge/PHP-8.1+-blue)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange)

## 🎯 Features

### Public Booking Form (No Authentication)
- 📋 Collects: name, email, date, and hour
- ⏰ Only 1 booking per hour allowed
- 📅 No bookings on weekends
- 🕐 Business hours only: 8:00 AM - 5:00 PM

### Admin Dashboard (Authentication Required)
- 👀 View all bookings
- 📊 Sorted by date and time
- 📧 Client information display
- 📄 Pagination support

## 🚀 Quick Start

### Windows
```bash
# Run the setup script
setup.bat
```

### Manual Installation

1. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   # Update database credentials in .env
   php artisan key:generate
   ```

3. **Setup database**
   ```bash
   # Create database 'hairdresser' in MySQL
   php artisan migrate
   php artisan db:seed --class=HairdresserSeeder
   ```

4. **Build assets & run**
   ```bash
   npm run build
   php artisan serve
   ```

5. **Access the application**
   - 🏠 Homepage: http://localhost:8000
   - 🔐 Login: http://localhost:8000/login
   - 📊 Dashboard: http://localhost:8000/admin/dashboard

## 🔑 Default Login

- **Email**: `hairdresser@example.com`
- **Password**: `password`

## 📋 Requirements

- PHP >= 8.1
- Composer
- MySQL >= 5.7
- Node.js & NPM

## 🔒 Business Rules

- ❌ **No Weekend Bookings**: Saturday and Sunday blocked
- ⏰ **Business Hours**: 8:00 AM - 5:00 PM only
- 🎫 **One Per Hour**: Each time slot can only be booked once
- 📆 **Future Dates**: Today or future dates only

## 📁 Project Structure

```
hairdresser-booking/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/DashboardController.php
│   │   │   └── BookingController.php
│   │   └── Requests/
│   │       └── BookingRequest.php
│   └── Models/
│       └── Booking.php
├── database/
│   ├── migrations/
│   │   └── *_create_bookings_table.php
│   └── seeders/
│       └── HairdresserSeeder.php
├── resources/
│   └── views/
│       ├── admin/dashboard.blade.php
│       └── bookings/index.blade.php
└── routes/
    └── web.php
```

## 🗄️ Database Schema

### `bookings` table
| Column     | Type      | Description              |
|------------|-----------|--------------------------|
| id         | bigint    | Primary key              |
| name       | varchar   | Client name              |
| email      | varchar   | Client email             |
| date       | date      | Appointment date         |
| hour       | time      | Appointment time         |
| created_at | timestamp | Booking creation time    |
| updated_at | timestamp | Last update time         |

## 🛠️ Technology Stack

- **Backend**: Laravel 10
- **Database**: MySQL
- **Frontend**: Bootstrap 5
- **Build Tool**: Vite
- **Authentication**: Laravel UI

## 📚 Documentation

For detailed documentation, see [DOCUMENTATION.md](DOCUMENTATION.md)

## 🧪 Testing

```bash
php artisan test
```

## 🔐 Security

- ✅ CSRF protection on all forms
- ✅ Authentication middleware on admin routes
- ✅ Input validation and sanitization
- ✅ SQL injection protection via Eloquent ORM
- ✅ Registration disabled (admin-only access)

## 📸 Screenshots

### Public Booking Form
The homepage displays a user-friendly booking form where customers can schedule appointments.

### Admin Dashboard
Protected dashboard showing all bookings with client details, sortable and paginated.

## 🚧 Future Enhancements

- 📧 Email notifications
- 📱 SMS reminders
- 📅 Calendar view
- 💳 Payment integration
- 👥 Multiple hairdressers
- 📊 Reports & analytics

## 🤝 Contributing

This is a demo project for DevChallenge. Feel free to fork and modify.

## 📝 License

This project is open-sourced software for demonstration purposes.

## 👨‍💻 Developer

Created for StartUpHUB DevChallenge - November 2025

---

**Need Help?** Check [DOCUMENTATION.md](DOCUMENTATION.md) for detailed setup and usage instructions.

## What  I did in this project

 A) Selected tasks:
1. Create a basic booking endpoint
  - I created a basic api endpoint because it is essential to eny project to have api if whe plan to use in other platforms
2. Prevent overbooking
    - I used validation rules that were provided in the code with minor changes 
3. Seeder and factories
    - I used seeders to populate the database with some data
4. Basic API listing
    - I created a basic api listing to view all bookings, and hairsers

5. Service layer implementation
    - I created a service layer to handle business logic, and I think that it is a good practice to do so in every project
6. API documentation
  - I created a basic api documentation using swagger, here I have some trouble, I cant figured out why the post methodes are not working

 B) Tradeoffs and compromises:

  - I changed the database structure to make it more suitable for the future changes; 
  - the price was that I generated redundant data and introduced pivot tables
  - here at the db schema I have spent more time
  - because I changed the structure of db, the web route for bookings needs to be changed

C) Future improvements and next steps for myself:
  - we need to add validation rules for DTO objects
  - at this time the post methode in BookingControllerWithDTO is working without any validation
  - create unit and integration tests
  
D) Testing the application:
- I tested the application using postman
  https://web.postman.co/workspace/4752b982-10e6-4e86-84cb-a10f81e77a82













- 


