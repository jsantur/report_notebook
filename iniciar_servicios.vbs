' Ruta a tu proyecto Laravel
projectPath = "C:\laragon\www\report_notebook"

' Objeto Shell
Set WshShell = CreateObject("WScript.Shell")

' Iniciar php artisan serve
WshShell.Run "cmd /c cd /d """ & projectPath & """ && php artisan serve", 0, False

' Esperar 3 segundos para que el servidor se inicie
WScript.Sleep 3000

' Iniciar ngrok con tu URL personalizada
WshShell.Run "cmd /c cd /d """ & projectPath & """ && ngrok http --url=goal-amply-skinhead.ngrok-free.dev 8000", 0, False

' Esperar 2 segundos más
WScript.Sleep 2000

' Iniciar el Scheduler de Laravel (para sincronización automática cada 5 minutos)
WshShell.Run "cmd /c cd /d """ & projectPath & """ && php artisan schedule:work", 0, False

' Mensaje de confirmación (opcional, si quieres ver que se ejecutó)
' WshShell.Popup "Servicios iniciados correctamente! (Scheduler activado)", 3, "Report Notebook"