# Hairdresser Booking System - Project Documentation

## Project Overview

This is a Laravel-based hairdresser booking platform created for the DevChallenge. The application allows customers to book appointments online while providing the hairdresser with an admin dashboard to view all bookings.

## Features Implemented

### ✅ Public Booking Form (No Authentication Required)
- **Route**: `/` (homepage)
- **Fields Collected**:
  - Client Name
  - Email Address
  - Appointment Date
  - Appointment Hour
- **Validation Rules**:
  - All fields are required
  - Email must be valid format
  - Date must be today or in the future
  - Time must be in HH:MM format
  
### ✅ Business Rules Enforced
1. **One Booking Per Hour**: Each time slot can only be booked once
2. **No Weekend Bookings**: Saturday and Sunday are blocked
3. **Business Hours Only**: Bookings restricted to 8:00 AM - 5:00 PM (8:00 - 16:00)
4. **Future Dates Only**: Cannot book appointments in the past

### ✅ Admin Dashboard (Authentication Required)
- **Route**: `/admin/dashboard`
- **Features**:
  - View all bookings sorted by date and time
  - Display client information (name, email, date, time)
  - Pagination support (15 bookings per page)
  - Total booking count
  - Protected by authentication middleware

### ✅ Authentication System
- Laravel UI with Bootstrap
- Login functionality for hairdresser
- Registration disabled for security
- Logout functionality
- Password reset capability

## Technical Stack

- **Framework**: Laravel 10.x
- **PHP**: 8.1+
- **Database**: MySQL (configurable)
- **Frontend**: Bootstrap 5 via Laravel UI
- **Build Tool**: Vite
- **Authentication**: Laravel Breeze/UI

## Database Structure

### Tables

#### `users`
Standard Laravel users table for hairdresser authentication.

#### `bookings`
```sql
- id (bigint, primary key, auto increment)
- name (varchar 255)
- email (varchar 255)
- date (date)
- hour (time)
- created_at (timestamp)
- updated_at (timestamp)
```

## Installation & Setup

### Prerequisites
- PHP >= 8.1
- Composer
- MySQL Server
- Node.js & NPM

### Step-by-Step Installation

1. **Clone the repository**
   ```bash
   git clone <your-repository-url>
   cd hairdresser-booking
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   ```
   
   Update `.env` with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=hairdresser
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Create database**
   ```bash
   mysql -u root -p
   CREATE DATABASE hairdresser CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   exit;
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Seed hairdresser user**
   ```bash
   php artisan db:seed --class=HairdresserSeeder
   ```
   
   **Default Login Credentials**:
   - Email: `hairdresser@example.com`
   - Password: `password`

8. **Install and build frontend assets**
   ```bash
   npm install
   npm run build
   ```

9. **Start development server**
   ```bash
   php artisan serve
   ```

10. **Access the application**
    - Homepage (Booking Form): http://localhost:8000
    - Login Page: http://localhost:8000/login
    - Admin Dashboard: http://localhost:8000/admin/dashboard

## Usage Guide

### For Customers (Public Access)

1. Navigate to the homepage
2. Fill in your details:
   - Enter your full name
   - Provide a valid email address
   - Select an appointment date (weekdays only)
   - Choose an available time slot (8:00 AM - 5:00 PM)
3. Click "Book Appointment"
4. Receive confirmation message

### For Hairdresser (Admin Access)

1. Go to `/login`
2. Enter credentials:
   - Email: `hairdresser@example.com`
   - Password: `password`
3. After login, you'll see the dashboard or navigate to `/admin/dashboard`
4. View all client bookings with details:
   - Client name and email
   - Appointment date and time
   - When the booking was made
5. Use pagination to browse through bookings

## Validation & Error Handling

### Booking Validation
- **Required Fields**: All fields must be filled
- **Email Format**: Must be a valid email address
- **Date Validation**:
  - Must be today or future date
  - Cannot be Saturday or Sunday
- **Time Validation**:
  - Must be between 08:00 and 16:00 (last slot at 4:00 PM)
  - Slot must not already be booked
  - Must be in HH:MM format

### User-Friendly Error Messages
All validation errors are displayed clearly to the user with specific instructions on how to fix them.

## File Structure

```
hairdresser-booking/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   └── DashboardController.php
│   │   │   └── BookingController.php
│   │   └── Requests/
│   │       └── BookingRequest.php
│   └── Models/
│       └── Booking.php
├── database/
│   ├── migrations/
│   │   └── 2025_11_11_082827_create_bookings_table.php
│   └── seeders/
│       └── HairdresserSeeder.php
├── resources/
│   └── views/
│       ├── admin/
│       │   └── dashboard.blade.php
│       └── bookings/
│           └── index.blade.php
└── routes/
    └── web.php
```

## API Endpoints (Routes)

### Public Routes
- `GET /` - Display booking form
- `POST /bookings` - Submit booking

### Protected Routes (Auth Required)
- `GET /admin/dashboard` - View all bookings

### Authentication Routes
- `GET /login` - Show login form
- `POST /login` - Process login
- `POST /logout` - Logout
- `GET /password/reset` - Password reset (available)

## Security Features

1. **CSRF Protection**: All forms protected with CSRF tokens
2. **Authentication Middleware**: Admin routes protected
3. **Input Validation**: All user inputs validated and sanitized
4. **SQL Injection Protection**: Using Eloquent ORM
5. **XSS Protection**: Blade templating auto-escapes output
6. **Registration Disabled**: Only pre-created admin account can access dashboard

## Testing

### Manual Testing Checklist

**Booking Form Tests**:
- ✅ Submit valid booking
- ✅ Try booking on weekend (should fail)
- ✅ Try booking outside business hours (should fail)
- ✅ Try booking same time slot twice (second should fail)
- ✅ Try booking past date (should fail)
- ✅ Submit with empty fields (should show errors)
- ✅ Submit with invalid email (should fail)

**Admin Dashboard Tests**:
- ✅ Access without login (should redirect to login)
- ✅ Login with valid credentials
- ✅ View bookings list
- ✅ Check pagination works
- ✅ Verify bookings are sorted by date/time

### Running Automated Tests
```bash
php artisan test
```

## Production Deployment Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Update `APP_URL` to production domain
- [ ] Configure production database
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Set up SSL certificate
- [ ] Configure proper file permissions
- [ ] Set up backup strategy
- [ ] Configure email settings for notifications

## Future Enhancements

Potential features for future versions:
- Email notifications to clients upon booking
- SMS reminders
- Cancel/reschedule functionality
- Multiple hairdressers support
- Service type selection
- Calendar view for admin
- Client management system
- Reports and analytics
- Online payment integration

## Troubleshooting

### Common Issues

**Issue**: "Base table or view not found"
- **Solution**: Run `php artisan migrate`

**Issue**: "Class 'Auth' not found"
- **Solution**: Make sure you've imported `use Illuminate\Support\Facades\Auth;`

**Issue**: Cannot access admin dashboard
- **Solution**: Make sure you've seeded the hairdresser user with `php artisan db:seed --class=HairdresserSeeder`

**Issue**: Assets not loading
- **Solution**: Run `npm install && npm run build`

**Issue**: SQLSTATE connection refused
- **Solution**: Check database credentials in `.env` and ensure MySQL is running

## Support & Contact

For issues or questions about this demo project, please contact the development team.

## License

This is a demo project created for the DevChallenge. Free to use and modify.

---

**Created**: November 2025  
**Framework**: Laravel 10  
**Purpose**: DevChallenge Demo Project

