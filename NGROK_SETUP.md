# Guía de Configuración de Ngrok para Escaneo de Cámaras en Producción (Fly.io)

## Problema
La aplicación en Fly.io no puede acceder directamente a la red local para escanear las cámaras.

## Solución
Usamos un túnel Ngrok desde tu máquina local para exponer el endpoint de escaneo a la aplicación en producción.

## Paso a Paso

### 1. En tu máquina LOCAL (que tiene acceso a las cámaras):
1. Asegúrate de tener el archivo `storage/app/cameras.csv` con la lista de cámaras.
2. Configura tu authtoken de Ngrok (solo una vez):
   ```bash
   ngrok config add-authtoken 3FSQXx07eQiZtV1Hajnib7FYXiR_6qeVuDzfdEiJpxGUj2LnQ
   ```

### 2. Iniciar los servicios automáticamente (segundo plano):
He creado dos scripts para ti:
- `iniciar_servicios.vbs`: Ejecuta los comandos en segundo plano (sin ventanas visibles)
- `iniciar_servicios.bat`: Para ejecutarlo manualmente si lo necesitas

#### Para ejecutar manualmente:
Doble clic en `iniciar_servicios.bat`

#### Para que inicie automáticamente con Windows:
1. Presiona `Windows + R`
2. Escribe: `shell:startup` y presiona Enter
3. Crea un acceso directo a `iniciar_servicios.vbs` en esa carpeta

### 3. En Fly.io (producción):
1. Establece la variable de entorno `NGROK_URL` con tu URL personalizada:
   ```bash
   fly secrets set NGROK_URL=https://goal-amply-skinhead.ngrok-free.dev
   ```

## Funcionamiento
1. Cuando el usuario hace clic en "Refrescar" en el modal de gestión de cámaras en Fly.io:
2. La aplicación en Fly.io llama a `NGROK_URL/api/hikcentral/status`
3. Tu máquina local recibe la solicitud, escanea las cámaras en la red local, y devuelve el resultado
4. Fly.io muestra las cámaras activas en el modal!

## Notas importantes
- Tu PC debe permanecer encendida para que funcione el sistema
- Para una URL permanente, usas tu dominio personalizado de Ngrok
