# MAU Registration - Backend (XAMPP / MySQL) Setup

This workspace contains a static `index.html` (frontend) and a simple PHP API to persist users and announcements to MySQL (designed for XAMPP). Follow the steps below to enable database-backed registration and login.

Steps

1. Install XAMPP (Windows): https://www.apachefriends.org/index.html
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Copy this project into `C:/xampp/htdocs/mau` (or place the `index.html` and `api/` folder inside a folder under `htdocs`).
4. Open phpMyAdmin (http://localhost/phpmyadmin) and run `create_tables.sql` to create the `mau_db` database and tables.
   - Or run: `mysql -u root < create_tables.sql` from command line.
5. If your MySQL root user has a password, update `api/db.php` and change `$DB_PASS` accordingly.
6. In `index.html` ensure `USE_API` is `true` (it's set by default). Then open `http://localhost/mau/index.html` in your browser.

Notes

- The PHP API files are minimal and intended as a starting point: `api/db.php`, `api/register.php`, `api/login.php`, `api/announcements.php`.
- Passwords are hashed using `password_hash`.
- The frontend retains an in-memory fallback if the API is unreachable.

Security & Next Steps

- Add CSRF protections and input validation on both client and server for production.
- Use HTTPS in production and restrict DB credentials.
- Implement sessions or JWT for persistent authenticated requests.
