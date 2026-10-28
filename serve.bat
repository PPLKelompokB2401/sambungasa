@echo off
echo ====================================================
echo  Menjalankan SambungAsa Dev Server (PHP 8.1 Laragon)
echo ====================================================
echo  Server URL: http://127.0.0.1:8000
echo  Tekan Ctrl+C untuk menghentikan server.
echo ====================================================
"C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe" "%~dp0artisan" serve --port=8000
