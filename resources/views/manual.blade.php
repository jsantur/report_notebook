<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual de Usuario - Report Notebook</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('manual/css/style.css') }}">
</head>
<body>

    <!-- Sidebar de Navegación -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h1>Report Notebook</h1>
                    <p style="font-size: 0.8rem; opacity: 0.8; margin-bottom: 10px;">Manual del Sistema v2.0</p>
                </div>
                <button id="themeToggle" class="theme-toggle" title="Cambiar tema oscuro/claro">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                </button>
            </div>
            <div class="search-container">
                <svg viewBox="0 0 24 24">
                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
                <input type="text" id="searchInput" placeholder="Buscar en el manual...">
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="#introduccion" class="nav-item active">1. Introducción</a>
            <a href="#panel-principal" class="nav-item">2. Panel Principal (Dashboard)</a>
            <a href="#nuevo-reporte" class="nav-item">3. Nuevo Reporte</a>
            <a href="#gestion-personal" class="nav-subitem" data-parent="#nuevo-reporte">3.1 Gestión de Personal</a>
            <a href="#control-unidades" class="nav-subitem" data-parent="#nuevo-reporte">3.2 Control de Unidades</a>
            <a href="#registro-ocurrencias" class="nav-subitem" data-parent="#nuevo-reporte">3.3 Registro de Ocurrencias e IA</a>
            <a href="#kilometrajes" class="nav-item">4. Kilometrajes</a>
            <a href="#megafonos" class="nav-item">5. Megáfonos</a>
            <a href="#historial-exportacion" class="nav-item">6. Buscar Reporte</a>
            <a href="#trabajo-colaborativo" class="nav-item">7. Trabajo Colaborativo (Admin)</a>
            
            @if(Auth::check() && Auth::user()->role === 'admin')
            <a href="#serenazgo" class="nav-item">8. Serenazgo</a>
            <a href="#usuarios" class="nav-item">9. Usuarios</a>
            <a href="#vehiculos" class="nav-item">10. Vehículos</a>
            <a href="#modulo-backups" class="nav-item">11. Backups</a>
            <a href="#configuracion" class="nav-item">12. Configuración</a>
            @endif
        </nav>
    </aside>

    <!-- Contenido Principal -->
    <main class="main-content">
        <div class="content-container">

            <!-- Sección 1: Introducción -->
            <section id="introduccion">
                <h2>1. Introducción</h2>
                <p>Bienvenido al manual de usuario de <strong>Report Notebook</strong>, el Sistema Institucional de Reportes de Seguridad Ciudadana. Esta plataforma ha sido diseñada para optimizar, centralizar y digitalizar el registro del cuaderno de ocurrencias diario del cuerpo de serenazgo.</p>
                <p>Este manual está dirigido tanto a <strong>Supervisores</strong> (encargados de llenar los reportes diarios) como a <strong>Administradores</strong> (encargados de la gestión, monitoreo en vivo y resguardo de la información).</p>
                
                <div class="callout info">
                    <svg class="callout-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    <p><strong>Dato Importante:</strong> El sistema cuenta con guardado automático (borradores). Si tu conexión a internet falla o cierras el navegador por accidente, tus avances se recuperarán automáticamente al volver a ingresar a la pantalla de Nuevo Reporte.</p>
                </div>
            </section>

            <!-- Sección 2: Panel Principal -->
            <section id="panel-principal">
                <h2>2. Panel Principal (Dashboard)</h2>
                <p>Al iniciar sesión en la plataforma, la primera vista es el Panel Principal. Aquí encontrarás un resumen del estado actual del sistema y el acceso rápido a los módulos clave.</p>
                
                <div style="text-align: center; margin: 24px 0;">
                    <img src="{{ asset('img/manual/Dashboard.png') }}" alt="Panel Principal - Dashboard" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid var(--border-color);">
                    <div class="caption" style="font-size: 0.85rem; color: var(--text-light); margin-top: 8px;">Panel Principal (Dashboard)</div>
                </div>

                <h3>Elementos del Dashboard</h3>
                <ul>
                    <li><strong>Indicadores Clave (KPIs):</strong> Tarjetas superiores que muestran la cantidad de reportes generados en el mes, unidades activas, y otras métricas relevantes.</li>
                    <li><strong>Listado de Reportes Recientes:</strong> Una tabla con los últimos reportes generados. Desde aquí puedes acceder rápidamente a ver los detalles o editar un reporte existente.</li>
                    <li><strong>Menú de Navegación:</strong> Ubicado en la parte superior, te permite saltar entre el Dashboard, Nuevo Reporte, Historial, etc.</li>
                </ul>
            </section>

            <!-- Sección 3: Creación de Reportes -->
            <section id="nuevo-reporte">
                <h2>3. Nuevo Reporte (Cuaderno Diario)</h2>
                <p>Este es el módulo principal del sistema. Aquí se registra toda la actividad operativa del turno: distribución de personal, patrullaje, ocurrencias e incidencias. Para acceder, haz clic en <strong>"Nuevo Reporte"</strong> en el menú lateral.</p>
                
                <div style="text-align: center; margin: 24px 0;">
                    <img src="{{ asset('img/manual/NuevoReporte.png') }}" alt="Nuevo Reporte Diario" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid var(--border-color);">
                    <div class="caption" style="font-size: 0.85rem; color: var(--text-light); margin-top: 8px;">Pantalla completa del formulario de Nuevo Reporte Diario</div>
                </div>

                @if(Auth::check() && Auth::user()->role === 'admin')
                <h3>🔐 Acceso con Contraseña (Solo Administradores)</h3>
                <p>El botón de <strong>"Nuevo Reporte"</strong> requiere una <strong>contraseña de seguridad</strong> para habilitarse. Esta contraseña permite registrar y almacenar el reporte en la base de datos <strong>antes de que se cumpla el tiempo</strong> del temporizador del turno.</p>
                <div class="callout danger">
                    <svg class="callout-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    <p><strong>Contraseña de acceso:</strong> <code>password&clave&contrasena</code>. Esta clave permite habilitar el botón para guardar el reporte anticipadamente. No la compartas con personal no autorizado.</p>
                </div>
                @endif

                <h3>Encabezado del Reporte</h3>
                <p>Al ingresar, la cabecera muestra el título <strong>"NUEVO REPORTE DIARIO"</strong> junto a los siguientes elementos automáticos:</p>
                <ul>
                    <li><strong>Responsable del Cuaderno:</strong> Se llena automáticamente con el nombre del usuario que inició sesión.</li>
                    <li><strong>Fecha:</strong> La fecha del día actual (ej. 2026-06-04).</li>
                    <li><strong>Hora:</strong> Hora exacta del sistema en tiempo real.</li>
                    <li><strong>Turno:</strong> Detectado automáticamente según la configuración de horarios (Mañana, Tarde o Noche).</li>
                </ul>

                <h3>⏱ Temporizador y Desbloqueo</h3>
                <p>En la parte superior derecha verás un <strong>temporizador</strong> que indica <em>"TIEMPO PARA HABILITAR"</em> con una cuenta regresiva (ej. 2h 52m 0s). Este reloj indica cuánto falta para que el reporte se cierre automáticamente al finalizar el turno.</p>
                <p>Junto al temporizador encontrarás:</p>
                <ul>
                    <li><strong>Casilla "Desbloquear":</strong> Permite extender el tiempo de edición del reporte si necesitas más tiempo para completarlo.</li>
                    <li><strong>Indicador "PROTECCIÓN ACTIVA":</strong> Señal visual (roja) que confirma que el reporte está protegido contra ediciones externas no autorizadas.</li>
                    <li><strong>Botón "EN PROCESO...":</strong> Indica que el reporte está actualmente en edición y aún no ha sido finalizado.</li>
                </ul>

                <h3>Supervisores</h3>
                <p>Debajo del encabezado encontrarás dos campos importantes:</p>
                <ul>
                    <li><strong>Supervisor de Campo:</strong> Selecciona al supervisor responsable del personal de campo (patrulleros) usando el menú desplegable.</li>
                    <li><strong>Supervisor de Cámaras:</strong> Selecciona al supervisor responsable del personal de cámaras. Puedes asignar uno o más supervisores; aparecerán como etiquetas azules que puedes eliminar con la "x".</li>
                </ul>

                <h3>💾 Borrador Guardado Automáticamente</h3>
                <p>En la esquina superior derecha de las secciones verás el indicador <strong style="color: green;">● Borrador guardado</strong>. El sistema guarda automáticamente cada cambio que realices. Si cierras el navegador por accidente o pierdes conexión, al volver a ingresar tus datos estarán intactos.</p>

                <h3>Secciones del Reporte</h3>
                <p>El formulario está dividido en <strong>5 secciones</strong> organizadas por colores. Cada sección se expande al hacer clic en su barra y tiene un botón <strong>"+"</strong> para agregar registros:</p>

                <h3 id="gestion-personal">3.1 Ocurrencias del Relevo del Personal de Cámaras (Barra Azul)</h3>
                <p>Registra las novedades del cambio de turno del personal de cámaras. Haz clic en el botón <strong>"+"</strong> para agregar una nueva ocurrencia. Documenta aquí cualquier situación que el turno anterior deje pendiente (cámaras averiadas, situaciones en curso, etc.).</p>

                <h3>3.2 Distribución de Personal de Cámaras (Barra Azul)</h3>
                <p>Aquí asignarás a los operadores de cámaras de turno. El botón <strong>"Gestionar Cámaras"</strong> te permite:</p>
                <ul>
                    <li>Ver todas las cámaras disponibles y su estado en tiempo real (solo las que responden al escaneo).</li>
                    <li>Asignar cámaras a cada operador.</li>
                    <li>Utilizar el botón <strong>"Distribuir Equitativamente"</strong> para repartir automáticamente las cámaras operativas entre los operadores presentes.</li>
                </ul>
                
                <div class="callout info">
                    <svg class="callout-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    <p><strong>Datos de Cámaras (CSV Único):</strong> La lista de cámaras y ubicaciones se carga directamente desde el archivo <code>storage/app/cameras.csv</code>. Este es el único lugar para modificar nombres, IPs y el orden de las ubicaciones. El sistema escanea en tiempo real si cada cámara está activa (ONLINE) y solo muestra las que responden.</p>
                </div>

                <h3 id="control-unidades">3.3 Distribución del Personal de Campo (Barra Verde)</h3>
                <p>Organiza al personal de serenazgo que patrullará la ciudad. La barra mostrará un contador como <strong>"1 ASIGNADOS"</strong> que indica cuántas personas están activas.</p>
                <ul>
                    <li><strong>Botón "+":</strong> Agrega un nuevo patrullero o grupo.</li>
                    <li><strong>Botón "VER LISTA COMPLETA":</strong> Despliega toda la lista de personal asignado con detalle.</li>
                </ul>
                <p>Al agregar personal, se creará una tabla con las columnas:</p>
                <ul>
                    <li><strong>PATRULLAJE / DESCRIPCIÓN:</strong> Tipo de patrullaje (Vehicular Halcón, Motorizado Cazador, A Pie Sierra Bravo), la unidad asignada, placa y los nombres del equipo (Chofer, Operador).</li>
                    <li><strong>UBICACIÓN / CELULAR:</strong> Zona asignada y número de contacto.</li>
                    <li><strong>ACCIONES:</strong> Botones de editar (lápiz ✏) y eliminar (papelera 🗑) para cada registro.</li>
                </ul>

                <div class="callout warning">
                    <svg class="callout-icon" viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                    <p><strong>Aviso:</strong> Si el sistema muestra "No hay unidades seleccionadas", primero debes agregar personal de campo y seleccionar su tipo de patrullaje.</p>
                </div>

                <h3>3.4 Reporte de Patrullaje - Unidades (Barra Morada)</h3>
                <p>Una vez asignadas las unidades en la sección anterior, aquí se generará automáticamente el formulario para registrar el estado inicial y final de cada vehículo durante el patrullaje (kilometraje de salida, kilometraje de llegada, observaciones, novedades).</p>

                <h3 id="registro-ocurrencias">3.5 Visualizaciones Resaltantes - IA (Barra Oscura)</h3>
                <p>En esta sección documentarás las incidencias y visualizaciones destacadas captadas por las cámaras durante el turno.</p>
                <p>El sistema cuenta con un <strong>Botón de Corrección con Inteligencia Artificial (Mago 🪄)</strong>. Escribe la ocurrencia con tus propias palabras, haz clic en el botón de la IA y el sistema:</p>
                <ul>
                    <li>Reescribirá el texto con un <strong>tono formal y policial</strong>.</li>
                    <li>Corregirá automáticamente <strong>faltas ortográficas</strong>.</li>
                    <li>Mejorará la <strong>redacción y estructura</strong> del texto.</li>
                </ul>

                <div style="text-align: center; margin: 24px 0;">
                    <img src="{{ asset('img/manual/VisualizacionesIA.png') }}" alt="Visualizaciones Resaltantes y Corrección IA" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid var(--border-color);">
                    <div class="caption" style="font-size: 0.85rem; color: var(--text-light); margin-top: 8px;">Sección de registro de visualizaciones con botón Mago de Inteligencia Artificial</div>
                </div>

                @if(Auth::check() && Auth::user()->role === 'admin')
                <h3>👁 Monitoreo en Tiempo Real (Solo Administradores)</h3>
                <p>Si un supervisor está redactando un reporte, aparecerá una barra azul en la parte superior con el mensaje <strong>"Monitoreo en Tiempo Real Disponible ⚡"</strong>. Desde ahí el administrador puede:</p>
                <ul>
                    <li><strong>Botón "Monitorear":</strong> Ver en tiempo real, en modo solo lectura, cómo el supervisor llena el reporte.</li>
                    <li><strong>Botón "Modificar":</strong> Intervenir directamente en el reporte del supervisor para hacer correcciones o agregar información. Los cambios se sincronizan instantáneamente.</li>
                </ul>
                @endif
            </section>

            <!-- Sección 4: Kilometrajes -->
            <section id="kilometrajes">
                <h2>4. Módulo de Kilometrajes (Sistema de Monitoreo Serenazgo)</h2>
                <p>Este módulo es el centro de control del patrullaje vehicular. Permite realizar el seguimiento detallado de los kilometrajes, auxilio público y parte de ocurrencias de cada unidad en tiempo real.</p>
                
                <div style="text-align: center; margin: 24px 0;">
                    <img src="{{ asset('img/manual/Kilometraje.png') }}" alt="Módulo de Kilometrajes" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid var(--border-color);">
                    <div class="caption" style="font-size: 0.85rem; color: var(--text-light); margin-top: 8px;">Panel de monitoreo del módulo de Kilometrajes</div>
                </div>

                <h3>Barra Superior</h3>
                <p>En la parte superior encontrarás información clave del estado actual:</p>
                <ul>
                    <li><strong>Reloj y Turno:</strong> Muestra la hora exacta, la fecha y el turno vigente (MAÑANA, TARDE o NOCHE).</li>
                    <li><strong>Contadores de Unidades:</strong> Indicadores que muestran cuántas Pick-Ups y Autos están activos sobre el total disponible (ej. 1/8 pick-ups, 0/5 autos).</li>
                    <li><strong>Estado de Sincronización:</strong> Muestra si los datos están sincronizados con el servidor.</li>
                </ul>

                <h3>Buscador Inteligente</h3>
                <p>Encima de la tabla encontrarás un campo <strong>"Filtrar por unidad o placa..."</strong>. Escribe el número de unidad o la placa del vehículo y la tabla se filtrará en tiempo real, mostrándote únicamente las coincidencias.</p>

                <h3>Tabla de Monitoreo</h3>
                <p>Cada fila de la tabla representa una unidad en la calle. Las columnas son:</p>
                <ul>
                    <li><strong>UNIDAD:</strong> Número de la unidad y placa del vehículo.</li>
                    <li><strong>TURNOS:</strong> Casillas para marcar en qué turnos participó la unidad (Noche, Día, Tarde).</li>
                    <li><strong>JURISDICCIÓN:</strong> Zona de patrullaje asignada (Sectorial, etc.).</li>
                    <li><strong>KM:</strong> Kilometraje registrado de la unidad.</li>
                    <li><strong>A.P (MIN):</strong> Minutos de Auxilio Público prestados.</li>
                    <li><strong>P.O:</strong> Parte de Ocurrencias registrados.</li>
                    <li><strong>ESTADO KM / ESTADO AP / ESTADO PO:</strong> Indicadores que muestran si el registro está <em>Completo</em> (verde) o <em>Pendiente</em> (rojo). El sistema calcula automáticamente si se alcanzaron las metas establecidas.</li>
                </ul>

                <h3>Metas Automáticas</h3>
                <p>En la esquina superior derecha de la tabla verás las <strong>METAS</strong> configuradas (ej. KM 90 | AP 230 | PO 3:10). El sistema compara los datos ingresados contra estas metas y marca automáticamente el estado como "Completo" o "Pendiente".</p>

                <h3>Botones de Acción</h3>
                <ul>
                    <li><strong>Botón "Wialon" (verde):</strong> Abre la integración con la plataforma GPS Wialon para consultar en tiempo real la ubicación y el recorrido de las unidades.</li>
                    <li><strong>Botón "SIPCOP-M" (rojo):</strong> Redirige al sistema SIPCOP-M para la gestión de megáfonos y postes inteligentes.</li>
                    <li><strong>Botón "CAPTURAR" (inferior derecho):</strong> Genera una captura de pantalla (screenshot) de la tabla actual con todos los datos visibles, ideal para compartir el estado del monitoreo o archivarlo como evidencia.</li>
                    <li><strong>Botón "REPORTE WHATSAPP" (verde, inferior izquierdo):</strong> Genera automáticamente un resumen formateado del reporte de kilometrajes y lo envía o prepara para compartir vía WhatsApp con los superiores o el grupo de coordinación.</li>
                </ul>

                <h3>Columna de Acciones</h3>
                <p>En la columna <strong>ACCIONES</strong> de cada fila encontrarás un <strong>ícono de ojo (👁)</strong>. Al hacer clic en este ícono, puedes <strong>desmarcar</strong> o desactivar temporalmente una unidad del monitoreo activo, sin eliminarla del sistema. Esto es útil cuando una unidad regresa antes de tiempo o sale de servicio durante el turno.</p>
            </section>

            <!-- Sección 5: Megáfonos -->
            <section id="megafonos">
                <h2>5. Megáfonos (Disuasión)</h2>
                <p>El módulo de megáfonos te permite activar audios pregrabados para la disuasión en áreas públicas mediante los postes inteligentes instalados en la ciudad.</p>
                
                <div style="text-align: center; margin: 24px 0;">
                    <img src="{{ asset('img/manual/Megafono.png') }}" alt="Panel de Megáfonos" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid var(--border-color);">
                    <div class="caption" style="font-size: 0.85rem; color: var(--text-light); margin-top: 8px;">Panel emergente de transmisión por Megáfonos</div>
                </div>

                <h3>¿Cómo reproducir un audio?</h3>
                <p>Al hacer clic en "Megáfonos" en el menú, se abrirá un panel lateral en el que podrás buscar el poste por su código SIPCOP o nombre de ubicación. Simplemente selecciona el audio y presiona el botón para iniciar la transmisión disuasiva.</p>
            </section>

            <!-- Sección 6: Historial y Buscar Reporte -->
            <section id="historial-exportacion">
                <h2>6. Buscar Reporte (Historial y Exportación)</h2>
                <p>Para buscar reportes pasados, dirígete al módulo de <strong>Buscar Reporte</strong>. Puedes buscar por fechas, responsables o turnos, lo que te permite acceder a los libros anteriores.</p>
                
                <div style="text-align: center; margin: 24px 0;">
                    <img src="{{ asset('img/manual/HistorialExportacion.png') }}" alt="Buscar Reporte y Exportación" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid var(--border-color);">
                    <div class="caption" style="font-size: 0.85rem; color: var(--text-light); margin-top: 8px;">Historial de reportes y botones de exportación a PDF y Excel</div>
                </div>

                <h3>Formatos de Exportación</h3>
                <p>Al visualizar el detalle de un reporte ya guardado, encontrarás botones en la parte superior derecha para exportar la información:</p>
                <ul>
                    <li><strong>Descargar PDF:</strong> Genera un documento formal, sellado y paginado, ideal para impresión o archivo físico.</li>
                    <li><strong>Descargar Excel:</strong> Genera un archivo estructurado en una <em>única hoja premium</em> con toda la información lista para ser procesada estadísticamente.</li>
                </ul>
            </section>

            <!-- Sección 7: Trabajo Colaborativo -->
            <section id="trabajo-colaborativo">
                <h2>7. Trabajo Colaborativo en Tiempo Real (Admin)</h2>
                <p>Esta es una función avanzada diseñada para la jefatura y administradores del sistema.</p>
                <p>Cuando un Supervisor está redactando un reporte nuevo, los Administradores verán una notificación de <strong>Monitoreo en Tiempo Real Disponible ⚡</strong> en su pantalla.</p>
                
                <ul>
                    <li><strong>Modo Monitoreo (Solo lectura):</strong> Permite al administrador ver en tiempo real cómo el supervisor llena el reporte. La pantalla se bloqueará con una capa translúcida protectora para evitar ediciones accidentales.</li>
                    <li><strong>Edición Colaborativa:</strong> El administrador puede intervenir en el reporte del supervisor. Los cambios realizados por el administrador se sincronizarán en la pantalla del supervisor instantáneamente.</li>
                </ul>

                <div class="callout info">
                    <svg class="callout-icon" viewBox="0 0 24 24"><path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                    <p>Si ocurre un conflicto (ambos editan el mismo campo simultáneamente), el sistema protegerá los cambios locales del supervisor y le mostrará una notificación sobre las correcciones hechas por el administrador.</p>
                </div>
            </section>

            @if(Auth::check() && Auth::user()->role === 'admin')
            <!-- MÓDULOS ADMINISTRATIVOS -->
            <h2 id="modulos-admin" style="font-size: 2rem; margin-top: 60px; color: var(--primary-color);">Configuración y Módulos Maestros</h2>
            <div class="callout warning">
                <svg class="callout-icon" viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                <p><strong>Exclusivo Administradores:</strong> Los módulos siguientes sirven para nutrir las opciones que ven los supervisores. Mantén estas tablas actualizadas.</p>
            </div>

            <!-- Sección 8: Serenazgo -->
            <section id="serenazgo">
                <h2>8. Serenazgo (Recursos Humanos)</h2>
                <p>Este módulo gestiona el directorio del personal operativo del municipio.</p>
                <div style="text-align: center; margin: 24px 0;">
                    <img src="{{ asset('img/manual/Sereno.png') }}" alt="Módulo Serenazgo" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid var(--border-color);">
                    <div class="caption" style="font-size: 0.85rem; color: var(--text-light); margin-top: 8px;">Formulario y directorio del personal de Serenazgo</div>
                </div>
                <h3>¿Cómo registrar un nuevo agente?</h3>
                <p>En la parte superior del módulo encontrarás un formulario rápido. Completa los siguientes campos:</p>
                <ul>
                    <li><strong>Nombre Completo:</strong> Escribe el nombre tal como aparecerá en los reportes.</li>
                    <li><strong>Identificación:</strong> Número de cédula o documento oficial.</li>
                    <li><strong>Rol:</strong> Selecciona "Operador" o "Supervisor" según sus funciones.</li>
                    <li><strong>Estado:</strong> Activo (por defecto). Si el agente está de vacaciones o inactivo, elige la opción correspondiente.</li>
                </ul>
                <p>Una vez completado, pulsa el botón azul <strong>"+ REGISTRAR AGENTE"</strong>.</p>
                <h3>Gestión y control del personal</h3>
                <p>En la tabla inferior "Agentes Registrados" verás la lista completa. En la columna <strong>ACCIONES</strong> encontrarás íconos para editar, desactivar o eliminar.</p>

                <h3>Búsqueda y Navegación</h3>
                <p>Encima de la tabla encontrarás un <strong>campo de búsqueda inteligente</strong>. Escribe cualquier dato (nombre, identificación, rol) y la tabla se filtrará automáticamente en tiempo real, sin necesidad de presionar "Enter". Además, si tienes muchos registros, la tabla incluye <strong>paginación</strong> en la parte inferior para navegar entre páginas de resultados cómodamente.</p>
                <div class="callout warning">
                    <svg class="callout-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6z"/></svg>
                    <p><strong>Atención Administradores:</strong> Desactivar un agente no elimina su historial. Los supervisores ya no verán a los agentes desactivados al crear reportes.</p>
                </div>
            </section>

            <!-- Sección 9: Usuarios -->
            <section id="usuarios">
                <h2>9. Usuarios</h2>
                <p>Define quién puede hacer login en el sistema.</p>
                <div style="text-align: center; margin: 24px 0;">
                    <img src="{{ asset('img/manual/Usuarios.png') }}" alt="Módulo de Usuarios" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid var(--border-color);">
                    <div class="caption" style="font-size: 0.85rem; color: var(--text-light); margin-top: 8px;">Listado y creación de Usuarios del Sistema</div>
                </div>
                <h3>¿Cómo crear un nuevo usuario?</h3>
                <p>Haz clic en el botón azul superior <strong>"+ NUEVO USUARIO"</strong>. Se abrirá un formulario donde deberás completar:</p>
                <ul>
                    <li><strong>Seleccionar Personal:</strong> Vincula la cuenta de acceso con un agente ya registrado en la base de datos de Serenazgo.</li>
                    <li><strong>Nombre de Usuario:</strong> Identificador único para el inicio de sesión (ej. GSCxxxMPT).</li>
                    <li><strong>Rol de Acceso:</strong> Elige <em>Usuario Estándar</em> (para los supervisores) o <em>Administrador</em> (acceso completo).</li>
                    <li><strong>Contraseña:</strong> Debe tener un mínimo de 8 caracteres.</li>
                    <li><strong>Pregunta y Respuesta de Seguridad:</strong> <mark>¡Muy importante!</mark> Estas opciones permiten configurar un método de recuperación de cuenta. Si el usuario olvida su contraseña en el futuro, el sistema le pedirá responder correctamente a su pregunta de seguridad para poder restablecer el acceso sin depender de un administrador.</li>
                </ul>
                <p>Al terminar, presiona el botón azul <strong>"CREAR USUARIO DEL SISTEMA"</strong>.</p>
                <h3>Gestión y control de usuarios</h3>
                <p>En la tabla inferior "Usuarios Registrados" verás la lista completa. En la columna <strong>ACCIONES</strong> aparecen íconos para editar, desactivar o eliminar.</p>

                <h3>Búsqueda y Navegación</h3>
                <p>La tabla cuenta con un <strong>buscador en tiempo real</strong>. Escribe el nombre, usuario o rol y los resultados se filtrarán al instante. También dispone de <strong>paginación</strong> en la parte inferior para navegar fácilmente cuando hay muchos usuarios registrados.</p>
                <div class="callout warning">
                    <svg class="callout-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6z"/></svg>
                    <p><strong>Atención Administradores:</strong> Desactivar un usuario no elimina su historial de auditoría. Los usuarios desactivados ya no podrán iniciar sesión, pero sus acciones permanecen registradas.</p>
                </div>
            </section>

            <!-- Sección 10: Vehículos -->
            <section id="vehiculos">
                <h2>10. Vehículos (Parque Automotor)</h2>
                <p>Este módulo permite a los administradores llevar el registro y control de todas las unidades móviles (camionetas, motocicletas, etc.) asignadas al patrullaje.</p>
                
                <div style="text-align: center; margin: 24px 0;">
                    <img src="{{ asset('img/manual/Vehiculos.png') }}" alt="Parque Automotor" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid var(--border-color);">
                    <div class="caption" style="font-size: 0.85rem; color: var(--text-light); margin-top: 8px;">Registro y control del Parque Automotor (Vehículos)</div>
                </div>

                <h3>¿Cómo registrar un nuevo vehículo?</h3>
                <p>En la parte superior encontrarás un formulario rápido. Llenar los datos es vital para la organización:</p>
                <ul>
                    <li><strong>Tipo de Vehículo:</strong> Selecciona si es Camioneta, Moto Lineal, Auto, etc.</li>
                    <li><strong>Placa:</strong> La matrícula oficial de la unidad (ej. EU-4144).</li>
                    <li><strong>Nro de Unidad:</strong> El número visible o código interno de la móvil (ej. 01, 10).</li>
                    <li><strong>Tipo de Patrullaje:</strong> Indica si será destinado al patrullaje Motorizado o Vehicular.</li>
                </ul>
                <p>Al terminar, haz clic en el botón azul <strong>"+ REGISTRAR VEHÍCULO"</strong>.</p>

                <h3>Gestión y Control de Operatividad</h3>
                <p>En la tabla inferior ("Vehículos Registrados") verás el listado de toda la flota. A la derecha, en la columna <strong>ACCIONES</strong>, encontrarás íconos para editar, eliminar o cambiar el estado del vehículo.</p>

                <h3>Búsqueda y Navegación</h3>
                <p>Utiliza el <strong>buscador en tiempo real</strong> ubicado sobre la tabla para filtrar vehículos por placa, tipo o número de unidad. Los resultados se actualizan al instante conforme escribes. La tabla también incluye <strong>paginación</strong> en la parte inferior para recorrer el listado completo de la flota.</p>
                
                <div class="callout warning">
                    <svg class="callout-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    <p><strong>Atención Administradores:</strong> Si una moto o camioneta se malogra o entra a taller, debes usar el botón de "check" en sus acciones para cambiar su estado a INOPERATIVO o MANTENIMIENTO. <strong>Solo los vehículos con estado verde "OPERATIVO" le aparecerán a los supervisores en la lista al momento de crear un Nuevo Reporte.</strong></p>
                </div>
            </section>



            <!-- Sección 11: Backups -->
            <section id="modulo-backups">
                <h2>11. Módulo de Backups (Copias de Seguridad)</h2>
                <p>Para garantizar la integridad y seguridad de la información, el sistema cuenta con un gestor interno de copias de seguridad de la base de datos.</p>
                
                <div style="text-align: center; margin: 24px 0;">
                    <img src="{{ asset('img/manual/CopiasSeguridad.png') }}" alt="Módulo de Copias de Seguridad" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid var(--border-color);">
                    <div class="caption" style="font-size: 0.85rem; color: var(--text-light); margin-top: 8px;">Módulo de gestión de Backups y Restauración</div>
                </div>

                <p><strong>¿Cómo acceder?</strong></p>
                <p>Los administradores pueden acceder mediante la opción correspondiente en el menú principal o presionando el atajo de teclado <code>Alt + A</code>.</p>

                <h3>Acciones disponibles:</h3>
                <ul>
                    <li><strong>Crear Copia:</strong> Genera un archivo local en el servidor con toda la base de datos actual.</li>
                    <li><strong>Descargar:</strong> Permite bajar el archivo de base de datos a tu computadora local.</li>
                    <li><strong>Restaurar:</strong> Permite sobreescribir la base de datos actual con un backup anterior. <em>Nota: Esta es una acción crítica y el sistema te pedirá una contraseña de seguridad antes de proceder.</em></li>
                </ul>

                <div class="callout danger">
                    <svg class="callout-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    <p><strong>Cuidado con Restaurar:</strong> La opción de Restaurar requiere la contraseña secreta <code>password&clave&contrasena</code>. Si restauras un backup antiguo, perderás TODO lo que se escribió después de esa copia. Úsalo solo en casos de desastre.</p>
                </div>
            </section>

            <!-- Sección 12: Configuración -->
            <section id="configuracion">
                <h2>12. Configuración del Sistema (Reportes y Notificaciones)</h2>
                <p>El módulo de configuración está diseñado para administrar los recordatorios automáticos del sistema. Sirve para definir en qué momentos los supervisores recibirán alertas de notificación para actualizar el reporte, dependiendo del turno actual.</p>
                
                <div style="text-align: center; margin: 24px 0;">
                    <img src="{{ asset('img/manual/Configuracion.png') }}" alt="Configuración del Sistema" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid var(--border-color);">
                    <div class="caption" style="font-size: 0.85rem; color: var(--text-light); margin-top: 8px;">Configuración de turnos y recordatorios automáticos</div>
                </div>

                <h3>Gestión de Turnos y Frecuencias</h3>
                <p>La pantalla presenta tres tarjetas correspondientes a los turnos: <strong>DÍA</strong>, <strong>TARDE</strong> y <strong>NOCHE</strong>. En cada una puedes modificar:</p>
                <ul>
                    <li><strong>Horario de Turno:</strong> La hora exacta de inicio y fin (ej. 06:00 a 13:59).</li>
                    <li><strong>Frecuencia (Minutos):</strong> Intervalo base de tiempo entre alertas.</li>
                    <li><strong>Horas de Notificación:</strong> Una lista de horas específicas separadas por comas (ej. <code>08:00, 09:00, 10:00</code>) en las que el sistema lanzará un recordatorio automático en pantalla a los supervisores.</li>
                </ul>

                <div class="callout info">
                    <svg class="callout-icon" viewBox="0 0 24 24"><path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                    <p><strong>Recordatorio Automático:</strong> Una vez guardes la configuración, el sistema detectará el turno actual por la hora del reloj y se encargará de lanzar alertas visuales para garantizar que el registro del cuaderno no se atrase.</p>
                </div>
            </section>
            @endif

        </div>
    </main>

    <!-- Botón toggle para móviles -->
    <button class="mobile-toggle" id="mobileToggle">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
            <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
        </svg>
    </button>

    <!-- Modal para Zoom de Imágenes (Lightbox) -->
    <div id="imageModal" class="image-modal">
        <span class="close-modal">&times;</span>
        <img class="modal-content" id="img01">
        <div id="modalCaption"></div>
    </div>

    <script src="{{ asset('manual/js/script.js') }}"></script>
</body>
</html>
