# StudentHub Portal

A PHP + MySQL student portal with secure login, role-based access, student
and event management, AJAX CRUD and an admin dashboard.

## Features
- Register/login with roles (student, teacher, admin), logout
- Sessions: 15 min idle timeout, remember-me token (30 days), lockout after 5 failed logins
- Role-based menu and page access (403 for unauthorized roles)
- Student CRUD with search and filter (MySQLi prepared statements)
- Event CRUD with poster upload (type + size validation)
- JSON API + Fetch-based student CRUD without page reload
- Admin dashboard: statistics, latest registrations, audit log

## Tech stack
PHP 8, MySQL/MariaDB (MySQLi), HTML, CSS, JavaScript (Fetch API), XAMPP/Apache

## Setup
1. Install XAMPP and start Apache and MySQL.
2. Copy this folder to `C:\xampp\htdocs\studenthub`.
3. Open phpMyAdmin, then Import `database.sql`.
4. Open `http://localhost/studenthub/register.html` and create an account.
5. Make it admin: `UPDATE users SET role='admin' WHERE email='you@example.com';`
6. Log in with the **Admin** role selected.

## Virtual host (optional)
See the "Local deployment" section: open `http://studenthub.local`.

## Security
Password hashing (`password_hash`), prepared statements, output escaping,
HttpOnly/SameSite cookies, session ID regeneration, hashed remember-me tokens,
CSRF-safe role checks on the server, MIME-based upload validation.

## Roles
| Role | Access |
|---|---|
| Student | Profile, courses, attendance, assignment, result, contact |
| Teacher | Student pages + Students management |
| Admin | Everything + Dashboard, Events, Users |

## Author
Your Name, Roll No, Class