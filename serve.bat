@echo off
title Laravel - Report Notebook
color 0A

echo ============================================
echo   REPORT NOTEBOOK - Servidor Laravel
echo ============================================
echo.
echo  Iniciando servidor en http://127.0.0.1:8000
echo  Presiona Ctrl+C para detener el servidor.
echo.
echo ============================================
echo.

cd /d "%~dp0"
php artisan serve

pause
