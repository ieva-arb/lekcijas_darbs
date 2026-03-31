@echo off
echo Updating database...

set MYSQL_USER=root
set MYSQL_PASS=
set MYSQL_DB=photo_gallery
set MYSQL_PATH="C:\xampp\mysql\bin\mysql.exe"

%MYSQL_PATH% -u %MYSQL_USER% -p%MYSQL_PASS% %MYSQL_DB% < update.sql

echo.
echo Database update complete!
pause