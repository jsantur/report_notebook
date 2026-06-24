# Skills de Desarrollo Asistido — Report Notebook (JSANTUR)
**Versión:** 2.0 (robustecida)  
**Mantenido por:** Antigravity AI + equipo de desarrollo  
**Última validación:** 2026-06-21  

## Propósito del documento
Guiar a la IA para que realice **modificaciones seguras** en el sistema de reportes de serenazgo, evitando regresiones en:
- Persistencia de borradores (localStorage + debounce)
- Protección de cierre (beforeunload)
- Corrección de textos con IA + fallback local
- Sincronización de unidades ("Halcón") con personal de campo
- Reseteo de ubicación por modalidad de patrullaje
- Notificaciones unificadas (SweetAlert2 Toast)

**Regla de oro ante la duda:** Preguntar al usuario antes de cambiar cualquier lógica marcada como [INMUTABLE].

---

## 1. Arquitectura de Estado y Flujos Críticos

### 1.1 Gestión de Borradores (Draft)
| Elemento | Detalle | Riesgo si se modifica |
|----------|---------|------------------------|
| Clave localStorage | `reporte_draft_${reporteId}` | [INMUTABLE] Cambiar el formato rompe todos los borradores existentes. |
| Mecanismo | Sincronización con debounce (300ms) vía Alpine.js | Sin debounce, se escribe en cada keystroke (pérdida de rendimiento). |
| Restauración | Al cargar `nuevo.blade.php` se lee localStorage y se hace merge con `$reporte` | No respetar el merge → datos inconsistentes. |

**Acción segura para extender:**  
Si necesitas agregar un campo al borrador, actualiza también la propiedad correspondiente en el store Alpine (`window.reporteStore`) y en el controlador que guarda a BD.

### 1.2 Protección de Cierre (beforeunload)
*(Nota: El evento `beforeunload` ha sido eliminado por solicitud explícita del usuario para evitar las alertas constantes al recargar o finalizar un reporte.)*

### 1.3 Corrección de Textos con IA (Gemini + Fallback)
**Endpoint:** `POST /api/correct-text` → `AIController@correctText`  
**Flujo:**  
1. Intenta Gemini 1.5 Flash con `withoutVerifying()` (necesario en Windows con certificados locales).  
2. Si falla (red, cuota, clave inválida) → **fallback local** (reglas de reemplazo + anti-redundancia).  

**Invariantes:**  
- El fallback **nunca** debe depender de una clave API.  
- La salida siempre debe ser un texto capitalizado y con punto final.  
- No se debe cambiar el prompt de Gemini sin probar el fallback local con los mismos ejemplos.

**Acción segura:**  
Para mejorar el fallback, edita las reglas en `AIController@fallbackCorrectText` sin tocar la lógica de llamada a Gemini.

### 1.4 Consulta de DNI (Patrullaje Integrado)
**Endpoint:** `GET /api/consultar-dni` → `DniController@consultar`
**API Externa:** Graph Perú (`https://graphperu.daustinn.com/api/query/{dni}`)
**Implementación:** 
- [INMUTABLE] La integración debe realizarse a través del backend (`DniController`) para evitar problemas de CORS y mantener la seguridad en el reporte.
- La respuesta mapea las propiedades en inglés de Graph Perú (`names`, `paternalLastName`, `maternalLastName`, `fullName`) a la estructura local que espera el frontend (`nombres`, `apellido_paterno`, `apellido_materno`, `nombre_completo`).
- No requiere API Key en los headers.
- **Frontend (Serenazgo y Patrullaje Integrado):** Se implementó un botón con tooltip (`title`) para ejecutar la consulta de manera estrictamente manual a solicitud del usuario. Se eliminaron las validaciones automáticas al desenfocar (`@blur`) o presionar Enter para no bloquear el llenado manual si falla la API.

---

## 2. Dependencias Ocultas (Efectos Mariposa)

| Componente | Depende de | Si modificas A, podrías romper B |
|------------|------------|----------------------------------|
| `reporte-seccion-campo.blade.php` | `modal-campo.blade.php`, store `campoStore`, watcher de unidades "Halcón" | La tabla de patrullaje deja de reflejar los serenos asignados. |
| `modal-campo.blade.php` | Evento `@cambio_tipo_patrullaje` que limpia `ubicacion` | Los usuarios verían una ubicación antigua incompatible con la nueva modalidad. |
| `reporte-seccion-visualizaciones.blade.php` | `AIController`, modal de edición que carga texto *corregido* | Si editas una visualización y guardas sin recargar el texto corregido, se perderían las mejoras de IA. |
| `modal-gestion-camaras` | `nuevo.blade.php` | Alpine store (`reporteData`), modales | Contenedor principal de reactividad. |
| `scripts-nuevo.blade.php` | API `/api/reportes/draft`, Alpine store | Falla si el store Alpine se desvincula o `saveDraft` choca con validaciones. |
| `modal-unidades.blade.php` | Evento `unidades-report-saved`, store `reportesUnidades`, campo `unidades_reportes` en BD | Si el store no persiste los reportes, al editar se pierden los datos. |

**Antes de tocar cualquier vista, ejecuta en tu mente:**  
*"¿Hay algún watcher, evento o listener que reaccione a este cambio?"*

---

## 3. Reglas de Negocio No Negociables

### 3.1 Formato de Nombres y Búsqueda
- **Todos los nombres de personas, operadores, supervisores y unidades** deben guardarse y mostrarse en **MAYÚSCULAS** (`strtoupper()` en PHP, `.toUpperCase()` en JS).
- **Orden Alfabético y Formato de Nombres**: La API de búsqueda de serenazgo (`searchJson` en `SerenazgoController`) ordena el personal alfabéticamente (`apellido_paterno`, `apellido_materno`, `nombres`). En `modal-campo.blade.php`, las búsquedas y asignaciones para Chofer, Operador, Lince y Sereno se visualizan y guardan bajo el formato estándar `APELLIDOS, NOMBRES`, y se corrigió el binding de `modalSearch.query` vinculándolo dinámicamente al valor actual del input (en lugar de mantenerse siempre vacío), permitiendo que las búsquedas filtren en tiempo real a medida que el usuario escribe.

### 3.2 Reseteo de Ubicación por Modalidad
Regla de reseteo implementada en el watcher de `tipo_patrullaje` en `modal-campo.blade.php`:
- [INMUTABLE] Al cambiar de modalidad de patrullaje, se limpia automáticamente el campo "Zona / Sector" (`ubicacion = ''`) para evitar que se arrastren selecciones previas incompatibles.
- Si agregas una nueva modalidad (ej. "CUATRIMOTO"), asegúrate de que también resetee `ubicacion`.

### 3.3 Notificaciones Unificadas
- **Única función autorizada:** `triggerNotification(mensaje)` (SweetAlert2 Toast, fondo `#1e293b`, texto `#ffffff`, ícono `#38bdf8`).
- **Eliminación de redundancia:** Se eliminaron los banners HTML duplicados de `nuevo.blade.php`. Todas las notificaciones del sistema se centralizan en la función `triggerNotification`. Además, se corrigió la lógica en `handleCampoRecordSaved` para que solo envíe una notificación en caso de actualización (edición), evitando alertas dobles al registrar nuevo personal.
- **Prohibido:** `Swal.fire` directo (excepto en confirmaciones de eliminación o errores de validación de formulario), `alert()`, `console.log` como feedback al usuario.

### 3.4 Confirmación antes de eliminar
Cualquier acción que elimine un registro (personal, visualización, ocurrencia) debe mostrar un Swal de confirmación.  
Ejemplo patrón:
```javascript
Swal.fire({ title: '¿Eliminar?', text: 'No se podrá revertir', icon: 'warning', showCancelButton: true }).then((result) => {
  if (result.isConfirmed) { /* eliminar */ }
});
```

### 3.5 Reporte Excel Premium de Única Hoja (FromView)
- **Concepto**: Para evitar la redundancia y desorden de múltiples pestañas que reportaban datos incompletos, la exportación a Excel se consolidó en una **única hoja premium unificada**, estructurada de forma análoga al reporte PDF.
- **Implementación**:
  - Clase `ReporteExport` implementa `FromView`, `ShouldAutoSize`, y `WithTitle` en lugar de `WithMultipleSheets`.
  - La vista `resources/views/reportes/excel.blade.php` estructura la información en una tabla unificada con:
    1. Encabezado institucional elegante.
    2. Información general y responsable.
    3. Ocurrencias de relevo.
    4. Personal de cámaras (Operador, máquina y asignaciones).
    5. Distribución de personal de campo (Unidades, placas, serenos asignados y modalidades).
    6. Historial de reportes optimizado.
    7. Visualizaciones IA resaltantes.
    8. Recorrido y kilometraje de unidades (Halcón).
- [INMUTABLE] Mantener el uso de `FromView` para conservar el diseño estructurado y evitar volver al formato multi-pestaña desorganizado.

### 3.6 Actualización Reactiva Instantánea en Búsqueda
- **Concepto**: Cuando el usuario modifica y guarda códigos PO en el modal de detalles de un reporte (`buscar.blade.php`), los cambios deben reflejarse de forma **instantánea** en la interfaz y en futuras aperturas de modal sin necesidad de refrescar la página (`location.reload()`).
- **Implementación**:
  - Al recibir confirmación exitosa de guardado en `saveAllChanges()`, se busca el índice del reporte dentro de la colección reactiva `this.reportes`.
  - Se actualizan localmente las propiedades `distribucion_personal_campo` (como string JSON) y `asignaciones` (como copia del arreglo clonado).
- [INMUTABLE] Siempre sincronizar la colección local de Alpine `this.reportes` en métodos AJAX de edición para evitar obligar al usuario a recargar la página.

### 3.7 Estabilidad de API y Roles de Usuario
- **Seguridad en búsqueda global (`searchJson` en `SerenazgoController`)**: 
  - [INMUTABLE] Los usuarios estándar no deben recibir datos de DNI ni celular completo en búsquedas globales (se mapean los resultados para omitirlos).
  - [CRÍTICO] Al mapear colecciones a arreglos asociativos planos en PHP, **está prohibido** llamar a métodos de modelos/Eloquent (como `makeHidden()`) sobre el resultado mapeado, ya que esto arroja un error fatal 500 para usuarios que no son administradores.
- **Evitar Entidades HTML en URLs**:
  - [CRÍTICO] Al invocar la API desde JS/Blade, los parámetros de consulta de roles (como `Operador de Cámaras`) deben escribirse directamente con caracteres con acento (`á`) en lugar de entidades HTML (`&aacute;`). PHP no decodifica automáticamente las entidades HTML de la URL en `$request->input()`, lo que causa que las búsquedas en la base de datos devuelvan cero resultados.

### 3.8 Modelo de Responsabilidad Persistente (Dueño del Reporte)
- **Concepto**: Cada reporte tiene asignado un usuario responsable oficial (`user_id` en la tabla `reportes` vinculado a `users`). El creador del reporte se asigna automáticamente como dueño.
- **Control de Acceso Flexibilizado (Códigos PO)**:
  - [NUEVO] Para facilitar la colaboración entre turnos (ej. el turno noche llenando los códigos del turno tarde), **cualquier usuario autenticado** puede ahora ingresar o modificar los códigos PO y la distribución de personal de campo en `AsignacionController::updateCodes` y desde la vista de búsqueda.
  - [UI/UX] Se ha retirado el banner restrictivo de "Solo Lectura", habilitando los botones de edición de códigos PO y el botón "GUARDAR CAMBIOS" para todos los usuarios en el modal de detalles de `buscar.blade.php`.
- **Delegación Administrativa**:
  - [CRÍTICO] Solo los usuarios con rol `admin` están autorizados a delegar o reasignar la responsabilidad del reporte a otro usuario activo a través del selector en el modal de detalles, sincronizándose vía AJAX sin recargar la página.

### 3.9 Sincronización en Tiempo Real y Edición Colaborativa (Admin/Supervisor)
- **Concepto**: Para permitir una colaboración fluida y segura en la creación del cuaderno diario, se diseñó un ecosistema dual de monitoreo y edición colaborativa persistente a través de la tabla `reporte_drafts`, las asignaciones temporales `asignacion_temps`, y la variable de sesión PHP `admin_monitoring_user_id`.
- **Implementación**:
  - **Auto-Restauración de Sesión Administrador**: La sesión activa del administrador (`admin_monitoring_user_id`, `admin_monitoring_mode` y `admin_monitoring_draft_id`) persiste nativamente en PHP. Al recargar la página principal o volver desde una vista paralela (como `/kilometrajes`), Alpine recupera instantáneamente el estado visual (capa esmerilada de "Monitorear" o formulario abierto de "Modificar") sin intervención manual.
  - **Sincronización Bidireccional de Kilometrajes**: Cuando el administrador activa el monitoreo, las vistas y APIs resuelven las unidades usando la sesión del supervisor. Al editar kilometrajes en la ruta paralela `/kilometrajes`, el controlador guarda en `asignacion_temps` y ejecuta de forma inmediata `ReporteDraft::syncKilometrajes($userId)`. Este *Helper* inyecta los nuevos kilometrajes directamente dentro del caché JSON del borrador global (`reporte_drafts`), asegurando que cualquier carga de página posterior o *polling* del supervisor reciba los datos frescos y evite sobrescrituras con valores antiguos.
  - **Modalidades de Interacción del Administrador**:
    1. **Monitorear Llenado (Lectura en Vivo)**: El administrador visualiza los cambios del supervisor mediante un polling automático de 5 segundos. La interfaz se cubre con una **capa de vidrio esmerilado (glassmorphic overlay)** de solo lectura.
    2. **Modificar Borrador (Edición Colaborativa)**: Remueve la capa esmerilada, detiene el polling de lectura y permite la edición. Los cambios se guardan con rebote (debounce) en el borrador global bajo el rol `'admin'`.
  - **Notificación y Sincronización en Supervisor (Merge de 3 Vías Optimista)**: El navegador del supervisor realiza polling en segundo plano. Si detecta cambios realizados por el administrador (`last_modified_by === 'admin'`), ejecuta un algoritmo de **fusión (merge) de 3 vías optimista** (`loadDraftData(data, true)`):
    1. **Sin conflicto**: Integra automáticamente los campos modificados por el administrador sin tocar los campos en los que el supervisor está escribiendo. Este proceso es ahora **completamente silencioso** para evitar notificaciones repetitivas o "parpadeos" en la pantalla del supervisor cada vez que el administrador guarda un cambio continuo.
    2. **Con conflicto**: Si ambos usuarios modificaron el mismo campo, prevalece la versión de la base de datos (administrador), notificando al supervisor con un toast específico (`triggerNotification`).
  - **Limpieza Completa del Formulario**: Al presionar **"Detener Monitoreo"** o **"Terminar Edición"**, se limpia la sesión PHP, se detiene el polling y se invoca `resetForm()` en Alpine, vaciando las variables locales para evitar fugas de información.

#### 3.9.1 Prevención de Notificaciones Repetitivas y Parpadeo en Polling
- [INMUTABLE] El algoritmo de polling implementa un **hash de contenido** (`computeDraftHash`) para detectar cambios reales y evitar procesar o re-renderizar el mismo borrador múltiples veces si no ha habido alteraciones.
- [INMUTABLE] Las notificaciones al supervisor (`triggerNotification`) sobre cambios del administrador se emiten **una sola vez por cambio distinto**, usando un segundo control de hash (`lastNotifiedChangeHash`).
- [INMUTABLE] El merge de datos (fusión de 3 vías optimista) actualiza únicamente los campos modificados en la interfaz sin reemplazar el objeto de estado completo, preservando intacto el foco del cursor y el estado de edición en vivo del supervisor.

#### 3.9.2 Gestión de Reportes de Unidades (modal-unidades.blade.php)
- **Persistencia**: Los reportes generados se guardan en `reportesUnidades` (store), en `localStorage` (clave `reporte_unidades_draft`) y en backend (campo `unidades_reportes` de `reportes` y `reporte_drafts`).
- **Edición**: El componente principal debe proveer un método `editarReporteUnidades(reporte)` que dispare el evento `abrir-modal-unidades` con los datos del reporte a editar (hora y rawData clonado).
- [INMUTABLE] El modal nunca modifica directamente el store; solo emite eventos. La persistencia es responsabilidad del store principal.
- **Notificaciones**: Usar `triggerNotification` para confirmar guardado/eliminación. Los errores de validación (observación vacía) usan `Swal.fire` directamente.
- **[CRÍTICO] Limpieza al guardar el reporte definitivo**: El método `clearDraft()` (llamado en `@submit` del formulario) DEBE borrar tanto `reporte_draft` como `reporte_unidades_draft` del localStorage. Sin embargo, **[INMUTABLE] NO DEBE modificar variables reactivas como `this.reportesUnidades = []`** porque eso dispara los watchers de Alpine (`$watch`), lo cual encola una llamada a `saveDraft()`, marcando el reporte con cambios sin guardar (`hasUnsavedChanges = true`), mostrando el alert de salida ("se perderán cambios") e incluso resucitando el borrador en `localStorage` antes de que la página termine de enviarse. Se debe usar una bandera `isSubmitting` en `saveDraft()` para abortar escrituras accidentales durante el post.
- **[CRÍTICO] Alpine.js NO garantiza que `:value` en `<input type="hidden">` actualice la *propiedad* DOM en tiempo de submit**: Alpine actualiza el *atributo* HTML, pero el formulario POST nativo lee la *propiedad* `element.value`. Para inputs hidden que deben enviarse en un form tradicional, **NUNCA usar `:value="..."`**. En su lugar, inyectar el valor manualmente mediante `document.querySelector('input[name="..."]').value = JSON.stringify(data)` justo antes de que el form envíe (dentro de `clearDraft()`).
- **Apertura de Modal Automatizada:** Al abrir el modal mediante el parámetro en URL (`?open=halcon`) o mediante notificaciones globales, NUNCA se debe usar o modificar variables obsoletas (como `showPatrullandoModal`). Se debe disparar el evento global `window.dispatchEvent(new CustomEvent('abrir-modal-unidades'))` para garantizar la apertura correcta. Además, se debe verificar mediante JS si el usuario ya se encuentra en la ruta activa (`/reportes/nuevo`) para evitar recargar la página completa innecesariamente.
- [INMUTABLE] Mantener los flags `isLiveSyncing` y `isCollaborativeEditing` para regular los bloqueos y permisos de envío en el encabezado del reporte.

### 3.10 Módulo de Backups (Copias de Seguridad)
- **Concepto**: Un módulo exclusivo para administradores (`role:admin`) que permite generar, descargar, restaurar (con contraseña) y eliminar copias de la base de datos completa. Cuenta con atajo de teclado (`Alt + a`).
- **Implementación (Específica para SQLite)**:
  - **Generación:** Realiza una copia directa del archivo `database.sqlite` activo hacia el directorio privado `storage/app/backups/`. No depende de `mysqldump`.
  - **Nomenclatura:** Utiliza el formato corto y legible `backup_DD-MM-YYYY_HHhMM.sqlite` para evitar nombres excesivamente largos.
  - **Restauración:** Sobreescribe la base de datos activa con el archivo de backup. 
    - Soporta restaurar desde un backup del servidor o subiendo un archivo externo (`.sqlite` o `.db`).
    - [INMUTABLE] Requiere validación mediante contraseña (`password&clave&contrasena`).
    - [CRÍTICO] Siempre genera un respaldo automático (`pre_restore_...`) antes de sobreescribir, como medida de seguridad.
  - **Descargas:** Utiliza un MIME type explícito (`application/octet-stream`) para evitar depender de la extensión de PHP `fileinfo`. Tras iniciar la descarga, lanza un toast indicando que el archivo está en la carpeta de Descargas del usuario.
  - [INMUTABLE] Todas las notificaciones del módulo (éxito/error) deben emplear **únicamente** `triggerNotification`. 
  - El componente principal `backupModule` en Alpine.js gestiona modales interactivos (con alternancia de visibilidad de contraseña y cierre seguro vía `Escape`) y el estado de carga (`isProcessing`) para prevenir peticiones dobles.

### 3.11 Seguridad y Búsqueda Global
- [INMUTABLE] La API `searchJson` tiene estrictamente prohibido devolver el `dni` y el `celular` para usuarios con rol estándar. Estos campos deben limpiarse mediante el uso de `map()` en la colección, nunca invocando métodos mágicos que rompan la serialización de arrays.

### 3.12 Precauciones de Despliegue (Fly.io)
- [CRÍTICO] Almacenamiento: El directorio `storage/app/backups/` y `database.sqlite` DEBEN residir dentro de un Volumen Persistente (`[mounts]`) en `fly.toml`. De lo contrario, se borrarán todos los reportes y copias de seguridad en cada actualización.
- [INMUTABLE] Gemini SSL: La función `withoutVerifying()` en `AIController` solo está permitida si `app()->isLocal()`. Para producción, se debe exigir validación de certificados SSL.
- [CRÍTICO] Forzado de HTTPS: En el archivo `App\Providers\AppServiceProvider`, se debe mantener `\Illuminate\Support\Facades\URL::forceScheme('https');` cuando se está en el entorno de producción real. Si por razones de mantenimiento temporal necesitas trabajar sin SSL activo en un entorno pseudo-producción, puedes cambiarlo a `http` temporalmente, pero NUNCA debes olvidarte de restaurarlo a `https` para el despliegue final en la nube, para evitar errores de mixed content.

### 3.13 Arquitectura de Base de Datos y Rendimiento
- [INMUTABLE] **Cero Triggers/Procedimientos:** Queda estrictamente prohibido usar Triggers o Stored Procedures en la base de datos que modifiquen datos (especialmente timestamps). Toda modificación debe pasar por Eloquent para no romper el algoritmo de generación de Hashes de los borradores en tiempo real.
- [INMUTABLE] **Integridad Relacional:** Mantener el uso de `onDelete('cascade')` en migraciones para tablas hijas (como `asignaciones`), asegurando la limpieza nativa sin depender de PHP.
- [CRÍTICO] **Escalabilidad de Búsquedas:** No usar múltiples cláusulas `LIKE "%term%"` para búsquedas globales en producción sin un índice. Se ha implementado un índice FullText condicional (`idx_serenazgos_fulltext`) para MySQL/PostgreSQL para evitar colapsar la memoria con Full Table Scans.


### 3.14 Estándares de Código y Principios SOLID (Backend)
- [INMUTABLE] **Validación Delegada:** Estrictamente prohibido usar `$request->validate()` o lógica condicional (`if`) para validar reglas de negocio en los Controladores. Toda petición que modifique datos (POST, PUT, DELETE) DEBE inyectar un `FormRequest` específico.
- [INMUTABLE] **Autorización Centralizada:** Prohibido usar condicionales directos como `if (auth()->user()->role == 'X')` en los controladores para proteger rutas. Se deben utilizar de forma obligatoria las **Policies** de Laravel o el middleware `can:`.
- [CRÍTICO] **Controladores Delgados:** Los controladores solo deben recibir peticiones, invocar un Request para validar, llamar a un Servicio para procesar lógica compleja y devolver una respuesta. Si un controlador requiere una transacción de BD (`DB::transaction`), esa lógica pertenece a una clase en `app/Services`.
- [CRÍTICO] **Mapeo explícito de campos en `store()`:** `StoreReporteRequest::validated()` solo devuelve los campos declarados en `rules()`. Los campos JSON que el formulario envía con **nombre diferente** al de la columna BD deben reasignarse manualmente en `ReporteController@store` antes de pasar `$validated` a `ReporteService`. Campos críticos que DEBEN mapearse:
  - `operadores_camaras` → `distribucion_personal_camaras`
  - `personal_campo` → `distribucion_personal_campo`
  - `reporte_personal_patrullando` → `reporte_personal_patrullando` *(mismo nombre, pero NO se incluye automáticamente en `validated()` al no estar entre los campos que Eloquent recibe directamente — debe asignarse explícitamente)*
  - `visualizaciones_resaltantes` → `visualizaciones_resaltantes` *(mismo caso)*
- [CRÍTICO] **Casteo Automático de JSONs:** En el modelo `Reporte`, campos como `distribucion_personal_camaras`, `distribucion_personal_campo`, `reporte_personal_patrullando` y `visualizaciones_resaltantes` ya están casteados como `array` en la propiedad `$casts`. Al consultarlos usando Eloquent, Laravel los convierte automáticamente a arreglos. Queda estrictamente prohibido aplicar `json_decode($reporte->campo, true)` directamente asumiendo que es un string porque arrojará una excepción fatal `TypeError`. En su lugar, si necesitas asegurar el formato (por ejemplo, al manejar datos que puedan provenir crudos), usa validación condicional segura: `is_string($reporte->campo) ? json_decode($reporte->campo, true) : ($reporte->campo ?? [])`.
- [CRÍTICO] **Descarga de Archivos (Tolerancia a Fallos sin `fileinfo`):** Al devolver archivos al cliente mediante `response()->download()` o utilidades como `Excel::download()`, Laravel utiliza la clase `BinaryFileResponse` de Symfony. Ésta intenta adivinar el MIME type utilizando `mime_content_type()`, el cual requiere la extensión `php_fileinfo`. En entornos donde esta extensión está desactivada, la petición crasheará arrojando un `LogicException`. Para prevenir esto y asegurar alta compatibilidad, **SIEMPRE debes definir explícitamente el `Content-Type` en los encabezados HTTP** de cualquier descarga.
  - *Ejemplo correcto para Excel:* `return Excel::download(new Export($data), $name, \Maatwebsite\Excel\Excel::XLSX, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);`

### 3.15 Módulo de Kilometrajes (Vista Paralela `/kilometrajes`)
- **Concepto**: Vista paralela para registro de KM, AP y PO de unidades vehiculares. Funciona con el mismo usuario-objetivo que el borrador activo (`getTargetUserId()`).
- [INMUTABLE] Al guardar kilometrajes, SIEMPRE debe llamarse `ReporteDraft::syncKilometrajes($userId)` para inyectar los valores nuevos en el JSON del borrador maestro y garantizar que el polling del supervisor reciba datos frescos.
- [CRÍTICO] La búsqueda de reporte guardado (`Reporte::where('user_id', $userId)->latest()->first()`) DEBE incluir filtro de `user_id` para evitar sobrescrituras cruzadas entre reportes de diferentes supervisores.
- [INMUTABLE] El auto-guardado (`syncToDraft`) usa debounce de 500ms. No reducir este valor para evitar saturar la tabla `asignacion_temps`.

### 3.16 Auto-Distribución Equitativa de Cámaras y Postes
- **Concepto**: Al utilizar la función "Auto-Distribuir" en la gestión de cámaras, la asignación debe ser equilibrada para todos los operadores, evitando que componentes agrupados alfabéticamente (como los "POSTES") se asignen a un solo individuo.
- **Implementación (`scripts-nuevo.blade.php`)**:
  - [INMUTABLE] La función `distribuirCamaras()` DEBE separar primero las cámaras regulares de los postes (filtrando por la palabra `'POSTE'`).
  - [INMUTABLE] Debe distribuir ambos grupos por separado mediante el algoritmo de remanentes (`remainder`) para garantizar que **cada operador reciba equitativamente su cuota de cámaras regulares Y su cuota de postes**.
  - Al consolidar, se ordena el arreglo final de cada operador utilizando una función personalizada que **siempre envía los postes al final** de la lista (y ordena alfabéticamente dentro de su grupo) para no afectar la continuidad visual de las cámaras numéricas.
  - [INMUTABLE] La interfaz debe incluir un texto informativo (`modal-operadores.blade.php`) advirtiendo que el orden secuencial en el que se hace clic a cada operador dicta estrictamente quién recibe el primer lote de cámaras en el proceso de Auto-Distribución.

### 3.17 Conteo Global de Cámaras Operativas
- **Concepto**: Al mostrar el contador total de "CÁMARAS OPERATIVAS", este número **excluye** los "POSTES DE EMERGENCIA".
- **Implementación (`scripts-nuevo.blade.php`)**:
  - [INMUTABLE] En el `Alpine.store('camaras')`, la propiedad `total` DEBE filtrarse con `!c.name.toUpperCase().includes('POSTE')` para no mezclar ambas métricas, dado que los postes se muestran en un contador independiente.

---

## 4. Patrones de UI que Debes Conservar

| Elemento | Dónde se usa | Condición |
|----------|--------------|-----------|
| `animate-pulse` | Indicador "GUARDANDO...", campos obligatorios vacíos | No reemplazar por spinner estático. |
| Modal en lugar de inline editing | `modal-visualizaciones`, `modal-campo`, `modal-operadores` | La edición en línea rompe la lógica de borradores. |
| Uppercase en inputs de nombres | `modal-campo.blade.php` y `modal-lista-campo.blade.php` | Aplica `style="text-transform: uppercase"` y fuerza en JS/API. |
| Orden de Personal de Campo | `modal-lista-campo.blade.php` | [INMUTABLE] La lista completa siempre debe ordenarse visualmente usando `sortedCampoLista`: 1. Vehicular (HALCÓN), 2. Motorizado (CAZADOR), 3. A pie (SIERRA BRAVO), 4. Cecom, 5. Prevención. |
| Cierre de modales y popovers | `buscar.blade.php` (Modal de detalles y popovers de Cód PO) | [INMUTABLE] No cerrar al hacer click fuera del backdrop o contenedor (eliminar `@click.away`). Cerrar únicamente con tecla Escape (priorizando cerrar popovers primero, y si no hay, el modal) o mediante botones explícitos ("X", "Cancelar"). |
| Historial en Tabla Premium | `buscar.blade.php` | [INMUTABLE] Mantener la estructura de tabla moderna con bordes redondeados y colores HSL/soft para turnos en lugar de las pesadas tarjetas anteriores, optimizando la lectura masiva. |
| Prevención de FOUC (Parpadeo Alpine) | Todos los modales y overlays (`x-show`) | [INMUTABLE] Todo componente contenedor con `x-show` debe incluir el atributo `x-cloak` y `style="display: none;"` para evitar que se renderice brevemente antes de que AlpineJS se inicialice (el destello de ventanas). |
| Validación flexible de visualizaciones | `modal-visualizaciones.blade.php` | [INMUTABLE] Al guardar una visualización, el único campo estrictamente obligatorio es la `descripcion`. El nombre de la cámara es opcional para permitir ingresos más ágiles. |
| Edición de Cód. PO | `buscar.blade.php` (Detalle Reporte) | [INMUTABLE] Permitir la inserción/edición de Códigos PO para el personal "Sierra Bravo" (`tipo_patrullaje === 'A pie'`). **Regla adicional:** Si hay múltiples efectivos "A pie" con el mismo "N° de Puesto" (`cantidad`), la interfaz para ingresar Códigos PO solo debe mostrarse en el primer registro de ese grupo, evitando duplicidad visual. |
| Deshabilitar unidades ya asignadas | `modal-campo.blade.php` | Cuando se selecciona modalidad Vehicular o Motorizado, las unidades ya asignadas a otro registro deben estar deshabilitadas en el select, excepto cuando se está editando el propio registro. |
| IMEI sin validación de longitud | `modal-campo.blade.php` | El campo IMEI / ID ya no requiere exactamente 15 dígitos, puede guardarse con cualquier longitud. |
| Tiempo del servidor | `report-header.blade.php`, `routes/web.php` | El botón de Finalizar Reporte usa la hora del servidor (via API /api/server-time) para evitar problemas con la configuración de la PC del cliente. |
| Navegación por teclado en búsqueda | `modal-campo.blade.php` | Las listas de selección de personal (Chofer, Operador, Lince, Sereno) admiten flechas arriba/abajo para navegar y tecla Enter para seleccionar. También tiene una clase de resaltado para la opción activa. |
| Búsqueda y Scroll en Personal Registrado (Usuarios) | `usuarios/index.blade.php`, `usuarios/partials/list.blade.php`, `UserController@index` | La página de gestión de usuarios ("Personal Registrado") debe incluir una barra de búsqueda (con debounce de 300ms, AJAX), una tabla con scroll vertical (max-h-[500px]) y sticky header, y paginación de 10 elementos, siguiendo el mismo patrón que la página de Personal de Serenazgo. |
| Validación Estricta de Personal | `modal-campo.blade.php`, `modal-operadores.blade.php`, `scripts-nuevo.blade.php` | [INMUTABLE] Los inputs de búsqueda de personal (Chofer, Operador, Lince, Sereno, buscador de Operadores y los selectores TomSelect de Supervisores) **no admiten números** (se limpian en tiempo real) y obligan a que la selección final coincida estrictamente con una entrada válida de la Base de Datos (`create: false` en TomSelect). |
---

## 5. Guía de Modificaciones Seguras (Modo Quirúrgico)

### 5.1 Agregar un nuevo campo a un reporte
1. **Base de datos** → crear migración, agregar columna (nullable inicialmente).
2. **Modelo** `Reporte` → agregar a `$fillable`.
3. **Vista** `nuevo.blade.php` (o sección correspondiente) → agregar input/select.
4. **Store Alpine** (ej. `window.reporteStore`) → agregar propiedad reactiva.
5. **Controlador** `ReporteController@store` / `update` → incluir campo en `$request->validate` y asignación.
6. **Borrador** → en el método que guarda a localStorage, incluir el nuevo campo.
7. **Prueba de humo** → llenar el campo, recargar, verificar que persiste en borrador y en BD.

### 5.2 Mejorar el fallback local de IA
- Archivo: `AIController.php` → método `fallbackCorrectText()`.
- Puedes añadir nuevas reglas de reemplazo (arrays de `$buscar` / `$reemplazar`) y mejorar la lógica anti-redundancia.
- **No** cambiar la firma del método ni su valor de retorno (debe devolver `string`).

### 5.3 Agregar una nueva modalidad de patrullaje (PIE, MOTO, etc.)
1. Actualizar el `select` en `modal-campo.blade.php` con la nueva opción.
2. El watcher `watch.form.tipo_patrullaje` ya reseteará `ubicacion` automáticamente.
3. En el backend, actualizar validaciones (si aplica) y la API de búsqueda de personal.
4. Verificar que la tabla de patrullaje "Halcón" muestre correctamente la unidad.

---

## 6. Diagnóstico Rápido (cuando algo se rompe)

| Síntoma | Posible causa | Revisar |
|---------|---------------|---------|
| El borrador no se guarda | Clave localStorage cambiada o debounce eliminado | `localStorage` en DevTools, función `saveDraft` |
| No aparece alerta al cerrar pestaña | Evento `beforeunload` eliminado o `hayCambiosSinGuardar` siempre false | Revisar listeners y variable global |
| La corrección IA no funciona pero el fallback tampoco | Error en `AIController@correctText` que no lanza excepción capturable | Logs Laravel (`storage/logs/laravel.log`) |
| Al cambiar tipo patrullaje la ubicación no se limpia | Watcher eliminado o error en nombre del campo (`tipo_patrullaje` vs `tipo_patrullaje_id`) | Revisar script Alpine en `modal-campo.blade.php` |
| Las notificaciones aparecen duplicadas | Alguien llamó a `Swal.fire` además de `triggerNotification` | Buscar en código `Swal.fire` fuera de confirmaciones de borrado |

---

## 7. Despliegue en Fly.io (Consideraciones)
- **HTTPS:** En producción (`APP_ENV=production`), el archivo `AppServiceProvider.php` DEBE forzar el esquema HTTPS (`URL::forceScheme('https')`) para evitar problemas de mixed content o errores en la carga de assets.
- **Base de Datos Persistente:** Fly.io utiliza un volumen efímero por defecto. La BD (`database.sqlite`) se monta a través de un volumen persistente (`storage_vol` -> `/var/www/html/storage`).
- [INMUTABLE] El archivo `.dockerignore` DEBE excluir `database/database.sqlite` y `storage/database/database.sqlite` para asegurar que la base de datos de desarrollo local **nunca** sobrescriba la base de datos de producción durante el despliegue (`flyctl deploy`).

```env
GEMINI_API_KEY=clave_valida_sin_comillas
APP_ENV=local   # permite withoutVerifying() en desarrollo; en producción debe usarse SSL real
```

**Comando de verificación:**  
`php artisan route:list --path=api` → debe mostrar `api/correct-text` y `api/ai-status`

---

## 8. Ejemplo de Prompt para la IA (copia y pega antes de pedir un cambio)

> Voy a solicitar una modificación en el sistema Report Notebook. Por favor, respeta las siguientes reglas del skills JSANTUR:
> - No alterar la lógica de borradores (clave `reporte_draft_*` ni el debounce).
> - Mantener el evento `beforeunload`.
> - Usar `triggerNotification` para todos los toasts.
> - Respetar el reseteo de `ubicacion` al cambiar `tipo_patrullaje`.
> - Mantener el fallback local de IA funcional aunque falle Gemini.
> - Después de proponer el código, indica qué pruebas de humo debería ejecutar.

---

## 9. Lista de Verificación Rápida (antes de commitear)
- [ ] ¿Los nuevos campos se guardan y restauran desde localStorage?
- [ ] ¿Los nombres van en mayúsculas?
- [ ] ¿Al eliminar algo hay confirmación con Swal?
- [ ] ¿Las notificaciones usan `triggerNotification`?
- [ ] ¿Se probó el cambio tanto en modo edición como en creación?
- [ ] ¿Se verificó que el fallback de IA funciona sin conexión a internet?

---

## 10. Estrategia de Despliegue (Fly.io)

El despliegue en infraestructuras efímeras como Fly.io requiere precauciones críticas, especialmente al usar **SQLite**.

- [CRÍTICO] **Volúmenes Persistentes:** Los servidores de Fly.io tienen un sistema de archivos efímero. Cada despliegue o reinicio borra todo. Para usar SQLite y guardar backups, **SE DEBE** montar un volumen persistente apuntando al directorio `/var/www/html/storage/`.
- [INMUTABLE] **Directorio Base de la BD:** La base de datos debe residir en `storage/database/database.sqlite` en producción, no en `database/database.sqlite`. Esto asegura que sobreviva a los reinicios (ya que `storage/` es el volumen).
- [CRÍTICO] **Inicialización en el Entrypoint:** Debido a que montar un volumen sobrescribe el directorio original dejándolo vacío, el `entrypoint.sh` de Docker DEBE recrear toda la estructura de carpetas (`storage/framework/views`, `storage/logs`, etc.) y **copiar** la base de datos inicial desde la imagen hacia el volumen si ésta no existe todavía.
- [INMUTABLE] **`BackupController` Dinámico:** El controlador de backups nunca debe usar una ruta dura o quemada (`database_path()`). Debe leer la ruta desde `config('database.connections.sqlite.database')` para asegurar compatibilidad tanto en local como en la nube.

---

## 11. Registro de Cambios y Mejoras Recientes (UI/UX y Manual)

### 11.1 Emoji para Unidades Vehiculares por Rango de Número
- **Fecha**: 2026-06-12 (actualización definitiva)
- **Regla**: [INMUTABLE] El emoji de las unidades HALCÓN en el Reporte de Patrullaje (`modal-unidades.blade.php`) se determina **exclusivamente por el número de unidad**, NO por el tipo registrado en la BD:
  - **Unidades 1–8** → 🚙 (auto patrullero)
  - **Unidades 9–13** → 🚘 (camioneta)
- **Archivo de lógica**: `scripts-nuevo.blade.php` → función `buildHalcon` → `const nroUnidad = parseInt(unit.unidad, 10); let emoji = nroUnidad >= 9 ? '🚘' : '🚙';`
- **Archivo de visualización**: `modal-unidades.blade.php` → solo muestra `p.display` (generado por `buildHalcon`)
- **Motivación**: El campo `tipo` en la tabla `vehiculos` de la BD no es confiable como fuente de verdad para el emoji; la numeración de unidades es más estable y predecible.

### 11.2 Scroll Automático al Top al Abrir Modales
- **Fecha**: 2026-06-04
- **Cambio**: Añadir scroll automático al principio de la página y del modal cada vez que se abre un modal.
- **Archivos modificados**:
  - `resources/views/components/modal-visualizaciones.blade.php`: Añadido $watch('show') con scrollTo
  - `resources/views/components/modal-unidades.blade.php`: Añadido $watch('show') con scrollTo
  - `resources/views/components/modal-campo.blade.php`: Añadido $watch('show') con scrollTo
  - `resources/views/components/modal-ocurrencias.blade.php`: Añadido $watch('show') con scrollTo
  - `resources/views/components/modal-operadores.blade.php`: Modificado $watch('show') para añadir scrollTo
  - `resources/views/reportes/partials/scripts-nuevo.blade.php`: Añadidos $watch para 'showCamarasAsignacionModal' y 'showCampoListaCompletaModal' con scrollTo
- **Motivación**: Mejorar la experiencia de usuario, asegurando que el modal se vea desde el principio, evitando que quede desplazado hacia abajo.

### 11.3 Validación y Visualización de Requisitos de Contraseña en Tiempo Real
- **Fecha**: 2026-06-08
- **Cambio**: Implementar validación de contraseñas más estricta y visualización en tiempo real de los requisitos cumplidos, además de habilitar campos de contraseña solo cuando la respuesta de seguridad es correcta (para el flujo de recuperación) y validación de coincidencia entre nueva contraseña y confirmación.
- **Nuevos requisitos de contraseña**:
  - Al menos 8 caracteres
  - Al menos una letra mayúscula
  - Al menos una letra minúscula
  - Al menos un número
  - Al menos un carácter especial (!@#$%^&*(),.?":{}|<>)
- **Archivos modificados**:
  - `app/Http/Requests/StoreUserRequest.php`: Actualizadas reglas de validación de contraseña y mensajes de error
  - `app/Http/Controllers/UserController.php`: Actualizadas reglas de validación en el método `validateRecovery`
  - `resources/views/auth/login.blade.php`: Añadidas validaciones visuales de requisitos de contraseña y lógica para deshabilitar botón hasta que todos los requisitos se cumplan, además de verificación de coincidencia de contraseñas
  - `resources/views/usuarios/index.blade.php`: Añadidas validaciones visuales de requisitos de contraseña, campo de confirmación de contraseña, y lógica para deshabilitar botón de envío hasta que se cumplan todos los requisitos (si es que se está estableciendo una nueva contraseña)
- **Motivación**: Mejorar la seguridad del sistema mediante requisitos de contraseña más robustos, y mejorar la experiencia de usuario al mostrar claramente qué requisitos ya se han cumplido a medida que el usuario escribe la contraseña.

### 11.4 Actualización de .dockerignore para Despliegue Optimizado en Fly.io
- **Fecha**: 2026-06-08
- **Cambio**: Actualizar el archivo `.dockerignore` con exclusiones más exhaustivas para evitar incluir archivos innecesarios y sensibles en el despliegue a Fly.io
- **Archivos modificados**:
  - `.dockerignore`: Actualizado con exclusiones organizadas por categorías (archivos sensibles, dependencias, control de versiones, archivos temporales, configuraciones de despliegue, documentación y bases de datos locales
- **Exclusiones clave**:
  - Archivos de entorno sensibles (`.env*`)
  - Dependencias locales (`node_modules`, `vendor`)
  - Control de versiones (`.git`, `.github`)
  - Archivos temporales y de prueba (`scratch`, `test*.php`, etc.)
  - Bases de datos locales (`*.sqlite`, `*.db`)
- **Motivación**: Mejorar la seguridad del despliegue al evitar incluir archivos innecesarios, reducir el tamaño de la imagen Docker y prevenir la sobrescritura accidental de la base de datos de producción con la base de datos local.

### 11.5 Cambio de Etiqueta "N° de Puesto / Cantidad" a "N° de Puesto"
- **Fecha**: 2026-06-08
- **Cambio**: Modificar la etiqueta del campo y el mensaje de validación de "N° de Puesto / Cantidad" a solo "N° de Puesto" en el modal de personal de campo para las modalidades "A pie", "Cecom" y "Prevención".
- **Archivos modificados**:
  - `resources/views/components/modal-campo.blade.php`: Actualizada la etiqueta del campo (línea 464) y el mensaje de validación (línea 266).
- **Motivación**: Simplificar la terminología del formulario para que sea más concisa y clara para los usuarios.

### 11.6 Estado Vacio para Búsqueda en Kilometrajes
- **Fecha**: 2026-06-08
- **Cambio**: Añadir un estado vacío para cuando la búsqueda en la página de kilometrajes no devuelva resultados, incluyendo un botón para limpiar la búsqueda.
- **Archivos modificados**:
  - `resources/views/kilometrajes/index.blade.php`: Añadido el template para el estado vacío de búsqueda.
- **Motivación**: Mejorar la experiencia de usuario al proporcionar retroalimentación clara cuando la búsqueda no encuentra resultados.

### 11.7 Validación de DNI y Celular en Formularios
- **Fecha**: 2026-06-08
- **Cambio**: Implementar restricción de entrada en los campos de DNI y Celular para aceptar solo números, con límites de longitud (8 dígitos para DNI, 9 para Celular).
- **Archivos modificados**:
  - `resources/views/serenazgo/index.blade.php`: Añadido `oninput="this.value = this.value.replace(/[^0-9]/g, '')"` y ajustado `maxlength` en campos de DNI y Celular.
  - `resources/views/components/modal-campo.blade.php`: Añadido `maxlength="9"` en campo de Celular de Contacto.
- **Motivación**: Mejorar la calidad de los datos al prevenir la entrada de caracteres no válidos en campos de identificación y contacto, complementando la validación del backend.

### 11.8 Cambio de API de Consulta de DNI (Graph Perú → Decolecta)
- **Fecha**: 2026-06-08
- **Cambio**: Migrar la API de consulta de DNI de Graph Perú a Decolecta, incluyendo:
  - Agregar variable de entorno `DECOLECTA_API_KEY`
  - Actualizar `DniController.php` para usar la nueva API y el token
  - Actualizar `serenazgo/index.blade.php` para usar la ruta del backend en lugar de la API directa
  - Eliminar referencias a "Graph Perú" en los textos de la UI
- **Archivos modificados**:
  - `.env`: Añadido `DECOLECTA_API_KEY`
  - `.env.example`: Añadido placeholder para `DECOLECTA_API_KEY`
  - `config/app.php`: Añadido `decolecta_api_key` para acceder a la variable de entorno
  - `app/Http/Controllers/DniController.php`: Actualizado para usar la API de Decolecta
  - `resources/views/serenazgo/index.blade.php`: Actualizado para usar la ruta `api.consultar.dni`
  - `resources/views/components/modal-campo.blade.php`: Eliminadas referencias a "Graph Perú"
- **Motivación**: Mejorar la fiabilidad y disponibilidad del servicio de consulta de DNI, y proteger el token de API manteniéndolo en el backend.

### 11.9 Habilitar Campos Fecha, Hora y Turno al Desbloquear
- **Fecha**: 2026-06-08
- **Cambio**: Modificar la funcionalidad de desbloqueo para que al introducir la contraseña correcta no solo se habilite el botón de finalizar reporte, sino también los campos de Fecha, Hora y Turno. Al hacer clic en "Bloquear" se vuelven a deshabilitar estos campos.
- **Archivos modificados**:
  - `resources/views/components/report-header.blade.php`: Añadido dispatch de eventos `unlock-status-changed` al desbloquear y bloquear, y en `init()` para el estado inicial guardado en localStorage.
  - `resources/views/reportes/partials/scripts-nuevo.blade.php`: Añadida variable `camposFechaHoraTurnoDesbloqueados` y listener del evento `unlock-status-changed` para actualizarla.
  - `resources/views/reportes/nuevo.blade.php`: Actualizados campos Fecha, Hora y Turno para usar `:readonly="!camposFechaHoraTurnoDesbloqueados"`.
- **Motivación**: Mejorar la flexibilidad del formulario permitiendo editar la fecha, hora y turno del reporte incluso fuera de la ventana horaria de finalización, siempre que se introduzca la contraseña correcta.

### 11.10 Mejoras en Fecha, Hora y Turno con Flatpickr y Dropdown
- **Fecha**: 2026-06-08
- **Cambio**:
  - Agregar librería Flatpickr para selección amigable de fecha y hora.
  - Convertir Turno en una lista desplegable con opciones "Mañana", "Tarde" y "Noche" (solo visible al desbloquear).
  - Detener la actualización automática de la hora al desbloquear y restaurarla al bloquear.
  - Al bloquear, restablecer fecha, hora y turno a valores actuales.
  - Usar flag `intervaloActivo` para controlar manualmente si el contador corre o no.
  - Reordenar init() para primero escuchar el evento y chequear localStorage.
  - **Corrección 1**: Modificar `initPickers` para que primero DESTRUYA cualquier picker existente antes de crear uno nuevo, evitando pickers duplicados que se "peleen" por el valor.
  - **Corrección 2**: Cambiar `x-model` por `x-bind:value` en los campos de fecha y hora para que solo Flatpickr actualice el valor cuando estamos desbloqueados.
  - **Corrección 3 (CAUSA RAÍZ!!!)**: ¡La condición del intervalo estaba AL REVÉS! Había `if (!this.camposFechaHoraTurnoDesbloqueados)` que significaba "si NO está desbloqueado → no actualizar", y ahora está correctamente `if (this.camposFechaHoraTurnoDesbloqueados)` que significa "si SÍ está desbloqueado → NO actualizar"
  - **Corrección 4 (Reversión a x-model)**: Se descubrió que la *Corrección 2* (usar `x-bind:value`) rompía la entrada manual, ya que al escribir en el input el valor del DOM cambiaba pero la variable `this.horaActual` no. Al ocurrir cualquier renderizado en Alpine, se sobrescribía lo que el usuario estaba tipeando con el valor anterior. Se restauró el uso de `x-model` en `nuevo.blade.php` para asegurar reactividad bidireccional inmediata.
  - Añadir `allowInput: true` a la configuración de Flatpickr para permitir escritura manual en los campos.
- **Archivos modificados**:
  - `resources/views/reportes/partials/styles-nuevo.blade.php`: Incluir CSS de Flatpickr.
  - `resources/views/reportes/partials/scripts-nuevo.blade.php`: Incluir JS de Flatpickr y localización en español, añadir lógica para manejar pickers, actualización automática de hora y restablecimiento de valores al bloquear; y correcciones (incluso la condición invertida del intervalo).
  - `resources/views/reportes/nuevo.blade.php`: Modificar campos para usar `x-bind:value` (en lugar de `x-model`), Flatpickr y dropdown para Turno.
- **Motivación**: Mejorar la UX para editar fecha y hora con pickers dedicados, y selección de Turno más intuitiva.

Durante la actualización y pulido del manual y la interfaz se consolidaron las siguientes reglas y mejoras:

- **Manual de Usuario Interactivo (UI/UX):** 
  - [INMUTABLE] El manual cuenta con un **Modo Oscuro/Claro** persistente usando `localStorage` (`manual_theme`). No remover la funcionalidad en `public/manual/js/script.js`.
  - [INMUTABLE] Se implementó un sistema de **"Flash Highlight"** (destello amarillo de 1.5s) que se activa al navegar desde el menú lateral hacia una sección para guiar la atención del usuario.
  - [INMUTABLE] **Efecto Lightbox (Lupa) en Imágenes:** Todas las capturas del manual tienen funcionalidad de zoom. Al hacer clic se amplían en un modal (`#imageModal`), y se pueden cerrar con `Escape`, clic fuera o botón `X`.
  
- **Lógica Condicional en Formularios (Personal de Campo):**
  - [INMUTABLE] En `modal-campo.blade.php`, la opción **"Comisión"** en el campo *Zona / Sector* solo debe mostrarse y ser elegible si la modalidad (`tipo_patrullaje`) es **"Vehicular"** o **"Motorizado"**. No debe ser elegible para "A pie", "Cecom", o "Prevención". El texto debe tener capitalización estándar ("Comisión").
  - [INMUTABLE] Cuando la modalidad es **"Vehicular"** y la Zona / Sector seleccionada es **"Comisión"**, el campo **Operador** (y Lince) deja de ser obligatorio en la validación, exigiendo únicamente el **Chofer**.

- **Semántica y Clarificaciones del Negocio (Reflejado en el Manual):**
  - **Nuevo Reporte:** La *Contraseña* (`password&clave&contrasena`) es estricta para **habilitar el botón y permitir registrar/almacenar en la BD antes del tiempo**.
  - **Configuración:** Administra exclusivamente las notificaciones/recordatorios (Día, Tarde, Noche) según los intervalos de hora.
  - **Usuarios:** La pregunta y respuesta de seguridad sirven específicamente para **recuperar la contraseña de manera manual**. El formato de usuario predeterminado es `GSCxxxMPT`.
  - **Kilometrajes:** Integra métricas de "KM 90", "AP 230", "PO 3:10", cálculo automático y botones "Wialon" (ubicación GPS en tiempo real), botón para megáfonos "SIPCOP-M", reportes WhatsApp directos, y la captura de pantalla nativa de la tabla. El "ojo" se usa para desactivar unidades temporalmente del monitoreo.

### 11.11 Eliminación de Reloj Global Conflictivo
- **Fecha**: 2026-06-11
- **Cambio**: Se eliminó la función global `updateClock()` y su `setInterval` en `scripts-nuevo.blade.php` que manipulaba el DOM directamente y sobrescribía los inputs de fecha y hora independientemente del estado de Alpine.JS.
- **Archivos modificados**:
  - `resources/views/reportes/partials/scripts-nuevo.blade.php`: Eliminación de `updateClock()`.
- **Motivación**: Permitir la edición libre y manual de la fecha y la hora cuando el reporte se encuentra desbloqueado por contraseña, eliminando la sobrescritura forzada segundo a segundo que anulaba los cambios introducidos por el usuario o la lógica de AlpineJS.

---

## 12. Gestión de Ubicaciones y Cámaras (CSV como Fuente Única de Verdad)

### 12.1 Flujo Operativo
- **Origen de Datos Único**: Archivo físico `storage/app/cameras.csv`. Este CSV define **exactamente** el nombre de cada ubicación, su IP, puerto, estado y el **ORDEN en el que deben aparecer** en el sistema.
- **Mecanismo de Verificación**: Conexiones directas por Sockets TCP (`fsockopen`) al puerto de datos de cada dispositivo en la red local.
- **Reglas de Negocio Obligatorias (Filtros)**:
  - [INMUTABLE] **Solo se muestran dispositivos ONLINE**: Dispositivos que no responden al escaneo (OFFLINE) se ELIMINAN automáticamente del listado.
  - [INMUTABLE] **Exclusión estricta**: Si el alias del dispositivo contiene "LPR" o "Control de Acceso", se omite del listado visual y del conteo de dispositivos activos.
  - [INMUTABLE] **Orden EXACTO del CSV**: El sistema **NO ORDENA ALFABÉTICAMENTE**, sino que mantiene el orden exacto en que aparecen las ubicaciones en el archivo `cameras.csv`. Esto garantiza que la lista siempre coincida con la estructura real de la ciudad.
  - [INMUTABLE] **Actualización limpia**: Cada vez que se abre el modal o se hace clic en "Refrescar", se LIMPIA COMPLETAMENTE la lista anterior antes de cargar las nuevas ubicaciones activas.
  - **Regla de Sanitización de Datos [INMUTABLE]**: Siempre se debe aplicar `array_values()` al array de ubicaciones antes de emitir la respuesta JSON, para asegurar un array secuencial y evitar que el navegador reordene los elementos automáticamente.
  - **Actualización del estado en CSV**: Cada vez que se escanea las cámaras (via API o comando CLI), el archivo `cameras.csv` se actualiza automáticamente con el estado (ONLINE/OFFLINE) de cada dispositivo.

### 12.2 Componentes
- **Controlador**: `app/Http/Controllers/HikvisionCameraController.php` -> Método `getStatus()`:
  - Lee el CSV, aplica filtros (LPR/Control de Acceso y ON/OFF), actualiza el estado en el CSV y devuelve solo ubicaciones ONLINE **en el mismo orden que el CSV**.
- **Comando CLI**: `app/Console/Commands/TestLocalCameras.php` -> `php artisan camaras:escanear`:
  - Escanea y muestra un reporte DETALLADO en la terminal (excluidas, inactivas, activas) **en el mismo orden que el CSV** y actualiza el estado en el archivo CSV.
- **Ruta**: `GET /api/hikcentral/status` (Retorna payload estructurado en JSON).
- **Archivo de Datos Maestro**: `storage/app/cameras.csv` con formato: `Alias,IP,Puerto,Estado`. Este es el único lugar donde se deben editar nombres, IPs y el orden de las ubicaciones. El campo `Estado` se actualiza automáticamente cada vez que se escanea las cámaras.
- **Modal**: `resources/views/components/modal-gestion-camaras.blade.php`:
  - Muestra solo ubicaciones ONLINE, con botón de refrescar y debugging en consola.
- **Menú Lateral**: La opción "Cámaras" se ha **comentado y ocultado** en `resources/views/layouts/partials/sidebar.blade.php`, ya que la gestión se hace exclusivamente a través del CSV y el modal dentro de "Nuevo Reporte".

### 12.3 Estructura del Payload de API
```json
{
  "resumen": {
    "total_csv": 38,
    "omitidos": 0,
    "activos_validos": 35,
    "inactivos": 3
  },
  "cameras": [
    {
      "nombre": "Aeropuerto",
      "ip": "172.16.1.70",
      "puerto": 8000,
      "estado": "ONLINE"
    },
    {
      "nombre": "Av - H - Colegios",
      "ip": "172.16.1.6",
      "puerto": 8000,
      "estado": "ONLINE"
    }
  ]
}
```

### 12.4 Integración en el Modal y Store Alpine
- **Funcionalidad del modal**:
  - [INMUTABLE] Se mantienen intactos todos los estilos y directivas de Alpine.js originales.
  - **Botón de "Refrescar" verde con ícono giratorio para actualizar manualmente la lista desde el CSV.
  - Indicador de carga (spinner azul) mientras se escanean las ubicaciones.
  - Lógica de limpieza COMPLETA del store de ubicaciones antes de agregar las nuevas.
  - Console.logs para debugging en el navegador para ver qué está pasando.
  - Mensaje informativo en el footer: "Solo se muestran las ubicaciones que responden al escaneo (ONLINE). Las ubicaciones OFFLINE se eliminan automáticamente del listado."
  - Detecta automáticamente cuándo se abre el modal y carga los datos desde el CSV.

- **Integración en el Store Alpine Principal (`resources/views/reportes/partials/scripts-nuevo.blade.php`)**:
  - **Variable**: `cargandoCamarasCSV` para controlar el estado de carga.
  - **Método**: `cargarCamarasDesdeCSV()` que se encarga de:
    1. Llamar a la API para obtener las ubicaciones ONLINE desde el CSV.
    2. Limpiar completamente la lista `camarasList` existente.
    3. Convertir los datos al formato del sistema y actualizar la lista **en el mismo orden que el CSV**.
  - **Watcher**: `showCamarasAsignacionModal` actualizado para que automáticamente llame a `cargarCamarasDesdeCSV()` cuando el modal se abre.

### 12.5 Uso del Sistema
1. **Actualizar datos/orden/nombres**: **Solo editar el archivo `storage/app/camaras.csv`**. Esto es la fuente única de verdad. Asegúrate de:
   - Usar el formato `Alias,IP,Puerto` (sin espacios extra entre comas).
   - Mantener el orden exacto en que quieres que aparezcan las ubicaciones.
   - Incluir todas las ubicaciones, incluso las que estén OFFLINE (el sistema las filtrará automáticamente).
2. **Probar desde terminal**: Ejecutar `php artisan camaras:escanear` para ver un reporte DETALLADO de todas las ubicaciones (excluidas, inactivas, activas) **en el mismo orden que el CSV**.
3. **Ver en el modal**: Abrir el modal "Gestión de Cámaras" dentro de "Nuevo Reporte" — se cargarán automáticamente los datos. Si hiciste cambios en el CSV, haz clic en "Refrescar".
4. **Depurar**: Abre la consola del navegador (F12) para ver logs detallados de la carga.

### 12.6 Túnel Ngrok para Escaneo en Producción (Fly.io)
- **Problema**: La aplicación en Fly.io no puede acceder directamente a la red local para escanear las cámaras.
- **Solución**: Usar un túnel Ngrok desde una máquina local que tenga acceso a la red de cámaras.
- **Implementación**:
  - **Controlador**: `app/Http/Controllers/HikvisionCameraController.php` → Actualizado para usar la variable de entorno `NGROK_URL` para conectarse al túnel local.
  - **Variable de Entorno**: `NGROK_URL` añadida a `.env.example` y configurada en Fly.io.
  - **URL Personalizada**: Se utiliza la URL personalizada `goal-amply-skinhead.ngrok-free.dev` para evitar cambios frecuentes.
  - **Scripts de Automatización**:
    - `iniciar_servicios.vbs`: Script de Windows para ejecutar `php artisan serve` y Ngrok en segundo plano (sin ventanas visibles).
    - `iniciar_servicios.bat`: Script para ejecutar manualmente los servicios.
  - **Autoinicio con Windows**: Se puede agregar un acceso directo a `iniciar_servicios.vbs` en la carpeta de inicio de Windows (`shell:startup`).
- **Uso**:
  1. En la máquina local: Ejecutar `iniciar_servicios.bat` (o configurar autoinicio).
  2. En Fly.io: La aplicación llamará automáticamente al túnel Ngrok cuando se haga clic en "Refrescar" en el modal de gestión de cámaras.

### 12.7 Archivos Modificados/Creados
- Creado: `storage/app/cameras.csv` (archivo de datos maestro con la lista final de 38 ubicaciones, con columna Estado).
- Creado: `app/Console/Commands/TestLocalCameras.php` (comando detallado para terminal, actualiza el estado en el CSV).
- Modificado: `app/Http/Controllers/HikvisionCameraController.php` (lógica de filtrado y preservación de orden del CSV; soporte para túnel Ngrok via variable de entorno `NGROK_URL`; actualiza el estado en el CSV).
- Modificado: `resources/views/components/modal-gestion-camaras.blade.php` (integración completa con limpieza).
- Modificado: `resources/views/reportes/partials/scripts-nuevo.blade.php` (integración en el store Alpine principal).
- Modificado: `resources/views/layouts/partials/sidebar.blade.php` (opción "Cámaras" comentada y ocultada).
- Modificado: `routes/web.php` (ruta actualizada para usar `getStatus()`).
- Mantenido: `app/Console/Commands/TestHikvisionConnection.php` (para compatibilidad).
- Creado: `iniciar_servicios.vbs` (script para iniciar servicios en segundo plano).
- Creado: `iniciar_servicios.bat` (script para ejecutar manualmente los servicios).
- Creado: `NGROK_SETUP.md` (guía detallada de configuración del túnel Ngrok).
- Modificado: `.env.example` (añadida variable `NGROK_URL`).

---

## 13. Migración a InfinityFree (Hosting Compartido)

### 13.1 Contexto
Debido a costos en Fly.io, se migra el proyecto a InfinityFree, un hosting compartido gratuito con soporte para PHP y MySQL.

### 13.2 Componentes y Archivos
- **Guía de Migración**: `INFINITYFREE_MIGRACION.md` → Pasos detallados para migrar el proyecto a InfinityFree.
- **Plantilla de Configuración**: `.env.infinityfree` → Plantilla de variables de entorno para InfinityFree (configuración de MySQL, APP_URL, claves API, etc.).
- **Archivo .htaccess para htdocs**: `.htaccess-htdocs` → Archivo para configurar URL limpias en el directorio público de InfinityFree.
- **Index para Raíz**: `index.php-htdocs-root` → Archivo index.php para poner en la raíz de htdocs (si se desea poner todo el proyecto en htdocs).

### 13.3 Pasos de Migración
1. **Configurar Base de Datos**: Crear una base de datos MySQL en InfinityFree y copiar las credenciales.
2. **Preparar Archivos**:
   - Opcion A (Recomendado):
     - Carpeta `laravel/`: Contiene todo el proyecto excepto `public/`.
     - Carpeta `htdocs/`: Contiene el contenido de la carpeta `public/`.
   - Opcion B (Más Simple): Poner todo el proyecto en `htdocs/`.
3. **Subir Archivos**: Usar el File Manager de InfinityFree o FTP para subir los archivos.
4. **Configurar .env**: Copiar la plantilla `.env.infinityfree`, editar las credenciales de MySQL y claves API, generar una APP_KEY nueva.
5. **Configurar Permisos**: Establecer permisos 775 para `storage/` y `bootstrap/cache/`.
6. **Ejecutar Migraciones**: Exportar la base de datos SQLite a MySQL y ejecutar las migraciones y seeders.
7. **Configurar Ngrok**: Mantener el túnel Ngrok corriendo en la máquina local para que la aplicación en InfinityFree pueda escanear las cámaras.

### 13.4 Archivos Modificados/Creados (Migración)
- Creado: `INFINITYFREE_MIGRACION.md` (guía detallada de migración).
- Creado: `.env.infinityfree` (plantilla de configuración para InfinityFree).
- Creado: `.htaccess-htdocs` (archivo para htdocs de InfinityFree).
- Creado: `index.php-htdocs-root` (index.php para raíz de htdocs).
- Actualizado: `storage/app/cameras.csv` (columna Estado añadida y actualizada).

**Fin del Skills Robustecido**
