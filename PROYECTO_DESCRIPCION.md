# Proyecto Report Notebook (Laravel)
==================================

## Descripción General
---------------------
Sistema de gestión de reportes para serenazgo, diseñado para registrar y administrar reportes de turno, incluyendo:
- Gestión de personal (supervisores, operadores de cámaras, personal de campo
- Asignación de vehículos y unidades
- Registro de kilometraje
- Generación de reportes en PDF y Excel
- Integración con inteligencia artificial para corrección de texto
- Generación de reportes para WhatsApp

## Tecnologías y Versiones
---------------------------
- Framework: Laravel 12.x
- PHP: 8.2+
- Base de datos: SQLite (por defecto, configurable a MySQL/MariaDB)
- Librerías clave:
  - maatwebsite/excel: ^3.1 (para exportaciones Excel)
  - Laravel Pint (formateador de código)
  - PHPUnit (testing)

## Estructura del Proyecto
--------------------------
### Directorios Principales
- `/app`
  - `Console/Commands/`: Comandos Artisan
  - `Exports/`: Clases de exportación Excel
  - `Http/Controllers/`: Controladores
  - `Http/Middleware/`: Middleware
  - `Http/Requests/`: Form Requests
  - `Models/`: Modelos Eloquent
  - `Providers/`: Proveedores de servicios

- `/database`
  - `factories/`: Factories para testing
  - `migrations/`: Migraciones de base de datos
  - `seeders/`: Seeders para poblar BD

- `/resources`
  - `views/`: Vistas Blade
  - `css/`: Estilos
  - `js/`: JavaScript

- `/routes`
  - `web.php`: Rutas web principales

## Modelos Principales
------------------
1. **User**: Usuarios del sistema con roles (admin, etc.)
2. **Reporte**: Reportes de turno
3. **Serenazgo**: Personal de serenazgo
4. **Vehiculo**: Vehículos
5. **Asignacion**: Asignaciones de unidades a reportes
6. **AsignacionTemp**: Borrador de asignaciones
7. **Camara**: Cámaras
8. **Megafono**: Megáfonos
9. **Setting**: Configuraciones del sistema

## Rutas Principales
-------------------
### Públicas
- `/` → Redirección a login
- `/login` → Formulario de login
- `/password/recovery` → Recuperación de contraseña
- `/password/reset` → Restablecimiento de contraseña

### Protegidas (requieren autenticación)
- `/dashboard` → Panel principal
- `/reportes` → Gestión de reportes
- `/usuarios` → Gestión de usuarios (solo admin)
- `/serenazgo` → Gestión de personal serenazgo (solo admin)
- `/vehiculos` → Gestión de vehículos (solo admin)
- `/camaras` → Gestión de cámaras (solo admin)
- `/configuracion` → Configuración del sistema (solo admin)
- `/kilometrajes` → Gestión de kilometrajes
- `/api/*` → Endpoints API internos

## Middleware
-----------
- `auth`: Requiere autenticación
- `admin`: Requiere rol de administrador
- `EnsureDistribucionExists`: Verifica que exista distribución

## Funcionalidades Clave
-------------------------
1. **Autenticación**: Login con username/email/name, verificación de cuenta activa
2. **Roles y Permisos: Admin vs usuarios normales
3. **Reportes: Crear, buscar, ver PDF, exportar Excel
4. **Asignaciones: Borrador en tiempo real, sincronización
5. **IA: Corrección de texto con API de IA
6. **Reportes WhatsApp: Generación de texto para compartir
7. **Kilometraje: Registro y seguimiento de kilometraje

## Seguridad Actual
--------------------
- Contraseñas y respuestas de seguridad hasheadas
- Validación de entradas
- Transacciones de base de datos
- Middleware de autenticación y permisos
- Verificación de cuenta activa

## Áreas para Revisar (Buenas Prácticas y Seguridad)
-----------------------------------------------------------
- Verificar validación de todas las entradas
- Revisar políticas de autorización (Gate/Policy)
- Verificar manejo de sesiones y CSRF
- Revisar manejo de errores y logging
- Verificar backups de base de datos
- Revisar configuración de seguridad en producción
- Verificar tests unitarios y de características
