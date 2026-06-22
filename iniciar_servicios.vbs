Set WshShell = CreateObject("WScript.Shell")

' Ruta a tu proyecto Laravel
projectPath = "C:\laragon\www\report_notebook"

' Iniciar php artisan serve
WshShell.Run "cmd /c cd /d """ & projectPath & """ && php artisan serve", 0, False

' Esperar un poco para que el servidor se inicie
WScript.Sleep 3000

' Iniciar ngrok
WshShell.Run "cmd /c ngrok http --url=goal-amply-skinhead.ngrok-free.dev 8000", 0, False
