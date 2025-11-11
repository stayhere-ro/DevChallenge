# Hairdresser Booking System

A Laravel-based booking platform for hairdresser appointments with basic functionality including reservation management and admin dashboard.

## Features

- **Public Booking Form**: Accessible without authentication
  - Collects client name, email, date, and hour
  - Only 1 booking per hour allowed
  - No bookings on weekends
  - Business hours: 8:00 AM - 5:00 PM
  
- **Admin Dashboard**: Protected by authentication
  - View all bookings
  - Client information display
  - Sortable by date and time

## Technology Stack

- Laravel 10
- PHP 8.x
- SQLite Database
- Bootstrap 5
- Laravel UI for authentication

## Installation

### Prerequisites

- PHP >= 8.1
- Composer
- Node.js & NPM

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd hairdresser-booking
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Create database**
   The SQLite database file should already exist at `database/database.sqlite`.
   If not, create it:
   ```bash
   # Windows PowerShell
   New-Item -Path "database\database.sqlite" -ItemType File
   
   # Linux/Mac
   touch database/database.sqlite
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Create hairdresser user**
   ```bash
   php artisan db:seed --class=HairdresserSeeder
   ```
   
   This creates an admin user with:
   - **Email**: hairdresser@example.com
   - **Password**: password

7. **Install and build frontend assets**
   ```bash
   npm install
   npm run build
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

9. **Access the application**
   - Public booking form: http://localhost:8000
   - Login: http://localhost:8000/login
   - Admin dashboard: http://localhost:8000/admin/dashboard

## Usage

### Making a Booking (Public)

1. Navigate to the homepage (http://localhost:8000)
2. Fill in the booking form with:
   - Your name
   - Email address
   - Preferred date (weekdays only)
   - Time slot (8:00 AM - 5:00 PM)
3. Submit the form
4. You'll receive a confirmation message

### Viewing Bookings (Hairdresser)

1. Navigate to http://localhost:8000/login
2. Login with:
   - Email: hairdresser@example.com
   - Password: password
3. You'll be redirected to the dashboard
4. Or navigate directly to http://localhost:8000/admin/dashboard
5. View all bookings with client details

## Business Rules

- **Weekend Restriction**: No bookings allowed on Saturday or Sunday
- **Business Hours**: Bookings only between 8:00 AM and 5:00 PM
- **One Booking Per Hour**: Each hour slot can only be booked once
- **Date Validation**: Bookings must be for today or future dates

## Database Schema

### bookings table
- `id` - Primary key
- `name` - Client name
- `email` - Client email
- `date` - Appointment date
- `hour` - Appointment time
- `created_at` - Booking timestamp
- `updated_at` - Last update timestamp

### users table (for authentication)
- Standard Laravel user table for hairdresser authentication

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
./vendor/bin/pint
```

## Security

- User registration is disabled (only pre-created hairdresser account)
- Admin dashboard requires authentication
- CSRF protection on all forms
- Input validation and sanitization

## License

This is a demo project created for the DevChallenge.

## Contact

For questions or issues, please contact the development team.

