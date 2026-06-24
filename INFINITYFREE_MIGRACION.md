# Guía de Migración a InfinityFree (notebookmarte.page.gd)

## Requisitos previos:
- Cuenta activa en InfinityFree (notebookmarte.page.gd)
- Acceso a File Manager y MySQL Databases en InfinityFree
- Base de datos MySQL creada en InfinityFree
- Credenciales de la base de datos MySQL (host, nombre DB, usuario, contraseña)

## Paso 1: Configurar la Base de Datos

1. Ve a tu panel de InfinityFree > **MySQL Databases**
2. Crea una nueva base de datos y copia las credenciales (host, nombre DB, usuario, contraseña)
3. Exporta tu base de datos SQLite actual a SQL:
   ```bash
   php artisan migrate:status  # Verifica las migraciones
   php artisan migrate:fresh --seed  # Si quieres mantener los datos, usa un paquete como "laravel-sqlite-to-mysql" o exporta manualmente
   ```

## Paso 2: Preparar los archivos para InfinityFree

### Estructura recomendada en InfinityFree:
- **htdocs/** (publica, tu dominio apunta aquí):
  - Contenido de `public/` de Laravel
  - Archivo `.htaccess` (actualizado)
- **laravel/** (privado, fuera de htdocs):
  - Todo el resto del proyecto (app, bootstrap, config, database, resources, routes, storage, vendor, etc.)

### Archivos clave:
- `/.env.infinityfree`: Plantilla de configuración para InfinityFree (copia a `htdocs/.env` o `laravel/.env`)
- `/.htaccess-htdocs`: Archivo para `htdocs/` (renombrar a `.htaccess`)
- `/public/.htaccess`: Mantener, pero ajustar

## Paso 3: Subir los archivos a InfinityFree

### Opcion A: Usar el File Manager de InfinityFree
1. Crea una carpeta `laravel/` en el directorio raíz (junto a htdocs)
2. Sube todos los archivos del proyecto (excepto la carpeta `public/`) a `laravel/`
3. Sube el contenido de la carpeta `public/` a `htdocs/`
4. Renombra `/.htaccess-htdocs` a `htdocs/.htaccess`

### Opcion B: Usar FTP (recomendado para archivos grandes)
- Usa FileZilla o WinSCP
- Credenciales FTP están en el panel de InfinityFree

## Paso 4: Configurar el archivo .env

1. Copia el archivo `/.env.infinityfree` a `laravel/.env`
2. Edita `laravel/.env` con tus credenciales MySQL de InfinityFree y tus claves API
3. Genera una nueva APP_KEY para producción:
   ```bash
   php artisan key:generate  # Haz esto LOCALMENTE y luego copia la APP_KEY al .env en InfinityFree
   ```
4. Asegúrate de que APP_ENV=production y APP_DEBUG=false

## Paso 5: Configurar permisos (IMPORTANTE)
En el File Manager de InfinityFree, establece estos permisos:
- `laravel/storage/` y subcarpetas: 775
- `laravel/bootstrap/cache/`: 775

## Paso 6: Ejecutar migraciones y seeders
Si tienes SSH (no es común en InfinityFree), pero si no:
1. Usa un paquete como "Laravel Web Installer" para ejecutar migraciones desde el navegador
2. O exporta tu base de datos MySQL local y la importa desde el panel de InfinityFree (phpMyAdmin)

## Paso 7: Configurar Ngrok (para el escaneo de cámaras)
- Mantén tu máquina local encendida con Ngrok y el servidor Laravel corriendo
- Asegúrate de que NGROK_URL en tu .env sea correcta
- El sistema en InfinityFree se conectará a tu máquina local via Ngrok para escanear cámaras

## Problemas comunes y soluciones:
- **Error 500**: Revisa los permisos de storage y bootstrap/cache, y el archivo .env
- **No se ve la página**: Asegúrate de que el contenido de public/ está en htdocs/ y que el archivo .htaccess está correcto
- **Base de datos no conecta**: Verifica las credenciales en el .env y que el host de MySQL es correcto (usualmente es algo como sqlXXX.infinityfree.com)
- **Cámaras no se actualizan**: Asegúrate de que Ngrok está corriendo localmente y que la URL es correcta en el .env
