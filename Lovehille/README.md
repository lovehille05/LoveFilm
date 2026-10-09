# LoveFilm

## First-time setup with XAMPP

1. Start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Open [phpMyAdmin](http://localhost/phpmyadmin), choose **Import**, select `database.sql`, and press **Import**. This creates the `lovefilm` database and its accounts and personal watchlists tables.
3. Open [LoveFilm registration](http://localhost/Lovehille/register.php), create your account, and sign in. The home page is available at [LoveFilm](http://localhost/Lovehille/).

The local XAMPP configuration defaults to MySQL at `127.0.0.1` using the `root` account with no password. If your MySQL account has a password, configure the `LOVEFILM_DB_PASSWORD` environment variable before starting Apache. The connection can also be customized with `LOVEFILM_DB_HOST`, `LOVEFILM_DB_NAME`, and `LOVEFILM_DB_USER` in your Apache environment.

New accounts are stored with PHP's password hashing, and personal saved titles are associated with their account in MySQL. Login and watchlist changes use CSRF protection and prepared SQL statements.

The included empty MySQL password is for a private local XAMPP installation only. Set a strong MySQL password and configure the environment variables before exposing this site to the internet.
