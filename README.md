# iCarRental

**Semester 3 University PHP Project**  
**Course:** Web Programming (ELTE Informatics)  
**Project Type:** CLI-compatible Web Application (PHP, HTML, JS)

## Overview

iCarRental is a simple web-based car rental system created during the third semester Web Programming (PHP) course. It supports core features like listing and filtering cars, booking them for date ranges, and managing them through an admin interface. The project was developed as part of an academic assignment and follows university rules for authenticity and originality.

## 💡 Features

### ✅ Minimum Requirements
- Homepage lists all cars with their basic attributes.
- Clicking on a car leads to a detailed page with full attributes and image.
- Homepage filters support all required criteria (including date range).
- Admin can create new cars with validation and error handling.
- Admin changes do **not** require login but must not be tampered with manually in `data/users`.

### 🔐 Authentication
- User registration with error handling.
- User login with validation and state tracking.
- After login, UI shows logged-in status.
- Logout available globally.

### 📅 Booking System
- Car bookings for specific dates (stored upon success).
- Users see success/failure messages with booking info.
- Profile page shows personal booking history.
- Admin profile page shows all bookings with delete options.

### 🛠 Admin Functions
- Admin can:
  - Create, edit, and delete car entries.
  - View and delete all bookings.
- Admin credentials (hardcoded for testing):
  - Email: `admin@yahoo.com`
  - Password: `admin123`
- Admin access starts from the homepage UI.

### 🎨 Design
- Polished, mobile-responsive layout.

### ✨ Bonus Features
- Booking calendar allows only available dates to be selected.
- Booking confirmation is done via AJAX (modal-based, no page refresh).

## 📂 Structure

- `index.php` – Main entry point and car list.
- `details.php` – Individual car view and booking interface.
- `admin/` – Car management functions.
- `auth/` – Login and registration.
- `data/` – Persistent data (cars, users, bookings).
- `scripts/` – AJAX handlers and modals.

## ⚠️ Academic Integrity

This project was submitted under ELTE’s official declaration.  
No unauthorized tools or code generation (including AI) were used.  
All logic, design, and implementation are original and follow Section 377/A of ELTE’s Academic and Examination Regulations.

## 📜 License

This project is academic and non-commercial. See `LICENSE` file for details.
