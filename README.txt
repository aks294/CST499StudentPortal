CST_499Project - Student Portal (folder-name agnostic)
----------------------------------------------------

Place this folder inside your XAMPP htdocs, start Apache & MySQL, then visit:
  http://localhost/CST_499Project/   (or rename the folder; links are relative)

1) Database setup:
   - Open http://localhost/phpmyadmin
   - Create database 'cst499_portal' (utf8mb4_unicode_ci)
   - Paste schema.sql into the SQL tab and run it

2) Try it:
   - Register a student account
   - Log in and reach the dashboard
   - Logout when done

Notes:
- All navigation links are RELATIVE, so renaming the folder won’t break URLs.
- Passwords use password_hash (PASSWORD_DEFAULT).
- Queries use PDO prepared statements.
