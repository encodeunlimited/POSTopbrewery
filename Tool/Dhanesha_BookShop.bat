@echo off
for /f "delims=" %%a in ('wmic OS Get localdatetime  ^| find "."') do set dt=%%a
set YYYY=%dt:~0,4%
set MM=%dt:~4,2%
set DD=%dt:~6,2%
set HH=%dt:~8,2%
set Min=%dt:~10,2%
set Sec=%dt:~12,2%

set stamp=%YYYY%-%MM%-%DD%_%HH%-%Min%-%Sec%

copy "C:\xampp\htdocs\bookshop\db\bookshop.db" "D:\DRL\bookshop_%stamp%.db"

START C:\xampp\xampp-control.exe

start "" http://192.168.1.101/bookshop