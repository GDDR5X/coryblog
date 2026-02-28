@echo off
title Cory Blog Server
echo ========================================================
echo        Cory Blog Server is Starting...
echo        Address: http://localhost:8000
echo        PHP Path: D:\php8.3\php.exe
echo ========================================================
echo.

start http://localhost:8000

"D:\php8.3\php.exe" -S localhost:8000

pause
