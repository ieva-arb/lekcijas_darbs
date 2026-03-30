@echo off
echo Setting up database...

set MYSQL_USER=root
set MYSQL_PASS=
set MYSQL_PATH="C:\xampp\mysql\bin\mysql.exe"

%MYSQL_PATH% -u %MYSQL_USER% -p%MYSQL_PASS% < database.sql

echo.
echo Database setup complete!
pause