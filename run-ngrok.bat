@echo off
title PHP Web (with ngrok)
echo ========================================================
echo        PHP Web
echo        Local: http://localhost:8000
echo        PHP Path: D:\php8.3\php.exe
echo ========================================================
echo.
echo Starting PHP server...
start "" "D:\php8.3\php.exe" -S localhost:8000

echo Waiting for PHP server to start...
timeout /t 2 /nobreak >nul

echo Starting ngrok...
echo.
echo NOTE: If ngrok fails, please configure your authtoken first:
echo   ngrok config add-authtoken YOUR_TOKEN
echo.
start "" ngrok http 8000

echo.
echo ========================================================
echo Servers started!
echo - Local: http://localhost:8000
echo - Ngrok: http://localhost:4040 (view public URL)
echo ========================================================
echo.
echo Press any key to open ngrok dashboard...
pause >nul

start http://localhost:4040

echo.
echo Press any key to stop servers...
pause >nul

taskkill /f /im php.exe 2>nul
taskkill /f /im ngrok.exe 2>nul

echo Servers stopped.
