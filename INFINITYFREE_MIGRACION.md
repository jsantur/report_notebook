# Guía de Migración a InfinityFree (notebookmarte.page.gd)

## Credenciales de tu cuenta:
- **Dominio**: notebookmarte.page.gd
- **Servidor FTP**: ftpupload.net
- **Usuario FTP**: if0_41852788
- **Contraseña FTP**: Tu contraseña de InfinityFree
- **Puerto FTP**: 21

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

### Opcion B: Usar SmartFTP (recomendado para archivos grandes)
- **PASOS PARA SMARTFTP:
1. Abre SmartFTP y crea una nueva conexión:
   - Host (Servidor): `ftpupload.net`
   - Port: 21
   - Usuario: `if0_41852788`
   - Contraseña: Tu contraseña de InfinityFree
2. Haz clic en Conectar
3. En el panel izquierdo (local): Abre tu carpeta `c:\laragon\www\report_notebook`
4. En el panel derecho (remoto): Abre la carpeta `htdocs/`
5. **OPCIÓN B (SUPER SIMPLE): Copia TODO el contenido de `c:\laragon\www\report_notebook` a `htdocs/` (todos los archivos y carpetas)
6. Copia `index.php-htdocs-root` a `htdocs/index.php` (sobrescribe el existente)
7. Copia `.htaccess-htdocs` a `htdocs/.htaccess`
8. Copia tu archivo `.env` local (ajusta las credenciales para MySQL primero!) a `htdocs/.env`

## Paso 4: Configurar el archivo .env

Si usas la Opción A (carpeta laravel/ separada):
1. Copia el archivo `/.env.infinityfree` a `laravel/.env`
2. Edita `laravel/.env` con tus credenciales MySQL de InfinityFree y tus claves API

Si usas la Opción B (todo en htdocs/):
1. Copia el archivo `/.env.infinityfree` a `htdocs/.env`
2. Edita `htdocs/.env` con tus credenciales MySQL de InfinityFree y tus claves API

Para cualquier opción:
3. Genera una nueva APP_KEY para producción (Haz esto LOCALMENTE en tu terminal):
   ```bash
   php artisan key:generate  # Haz esto LOCALMENTE y luego copia la APP_KEY al .env en InfinityFree
   ```
4. Asegúrate de que APP_ENV=production y APP_DEBUG=false
5. Asegúrate de que NGROK_URL esté configurada a https://goal-amply-skinhead.ngrok-free.dev

## Paso 5: Configurar permisos (IMPORTANTE)
Si usas la Opción A (carpeta laravel/ separada):
- `laravel/storage/` y subcarpetas: 775
- `laravel/bootstrap/cache/`: 775

Si usas la Opción B (todo en htdocs/):
- `htdocs/storage/` y subcarpetas: 775
- `htdocs/bootstrap/cache/`: 775

**Cómo establecer permisos en SmartFTP**:
1. Haz clic derecho en la carpeta (ej: htdocs/storage/)
2. Selecciona **Properties** o **Permisos**
3. Establece los permisos numéricos a 755 o 775
4. Asegúrate de aplicar los cambios a todas las subcarpetas y archivos

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
