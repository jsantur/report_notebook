<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual de Usuario - Report Notebook</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Sidebar de Navegación -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h1>Report Notebook</h1>
            <p style="font-size: 0.8rem; opacity: 0.8; margin-bottom: 10px;">Manual del Sistema v2.0</p>
            <div class="search-container">
                <svg viewBox="0 0 24 24">
                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
                <input type="text" id="searchInput" placeholder="Buscar en el manual...">
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="#introduccion" class="nav-item active">1. Introducción</a>
            <a href="#inicio" class="nav-item">2. Inicio (Dashboard)</a>
            <a href="#nuevo-reporte" class="nav-item">3. Nuevo Reporte</a>
            <a href="#gestion-personal" class="nav-subitem" data-parent="#nuevo-reporte">3.1 Gestión de Personal</a>
            <a href="#control-unidades" class="nav-subitem" data-parent="#nuevo-reporte">3.2 Control de Unidades</a>
            <a href="#registro-ocurrencias" class="nav-subitem" data-parent="#nuevo-reporte">3.3 Ocurrencias e IA</a>
            <a href="#trabajo-colaborativo" class="nav-subitem" data-parent="#nuevo-reporte">3.4 Trabajo Colaborativo</a>
            
            <a href="#kilometrajes" class="nav-item">4. Kilometrajes</a>
            <a href="#megafonos" class="nav-item">5. Megáfonos</a>
            <a href="#buscar-reporte" class="nav-item">6. Buscar Reporte</a>
            
            <a href="#modulos-admin" class="nav-item" style="color:var(--text-light); pointer-events:none; font-size:0.8rem; margin-top:10px;">— GESTIÓN (ADMIN)</a>
            <a href="#serenazgo" class="nav-item">7. Serenazgo</a>
            <a href="#usuarios" class="nav-item">8. Usuarios</a>
            <a href="#vehiculos" class="nav-item">9. Vehículos</a>
            <a href="#camaras" class="nav-item">10. Cámaras</a>
            <a href="#modulo-backups" class="nav-item">11. Backups</a>
            <a href="#configuracion" class="nav-item">12. Configuración</a>
        </nav>
    </aside>

    <!-- Contenido Principal -->
    <main class="main-content">
        <div class="content-container">

            <!-- Sección 1: Introducción -->
            <section id="introduccion">
                <h2>1. Introducción</h2>
                <p>Bienvenido al manual de usuario de <strong>Report Notebook</strong>, el Sistema Institucional de Reportes de Seguridad Ciudadana. Esta plataforma ha sido diseñada para optimizar, centralizar y digitalizar el registro del cuaderno de ocurrencias diario del cuerpo de serenazgo.</p>
                <p>Este manual está redactado paso a paso y de manera sencilla para que <strong>cualquier usuario nuevo</strong> (Supervisor o Administrador) pueda comprender el uso completo del sistema sin ayuda externa.</p>
                
                <div class="callout info">
                    <svg class="callout-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    <p><strong>Dato Importante:</strong> El sistema cuenta con guardado automático continuo. Si tu conexión a internet falla o cierras el navegador por accidente, tus avances se recuperarán automáticamente al volver a ingresar a tu reporte.</p>
                </div>
            </section>

            <!-- Sección 2: Inicio -->
            <section id="inicio">
                <h2>2. Inicio (Panel Principal)</h2>
                <p>Al iniciar sesión en la plataforma, la primera vista es el Panel Principal (Dashboard). Aquí encontrarás un resumen del estado actual del sistema y el acceso rápido a la operatividad diaria.</p>
                
                <div class="img-placeholder">
                    <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zM7 10h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg>
                    <div class="caption">[AQUÍ: Inserta una captura de pantalla del Dashboard "Inicio"]</div>
                </div>

                <h3>¿Qué encontrarás aquí?</h3>
                <ul>
                    <li><strong>Gráficos Estadísticos:</strong> Barras con la cantidad de reportes generados recientemente.</li>
                    <li><strong>Menú Lateral Izquierdo:</strong> Es tu principal medio de transporte dentro de la aplicación. Desde aquí saltarás al registro de reportes, kilometrajes o administración.</li>
                    <li><strong>Perfil de Usuario:</strong> Arriba a la izquierda, puedes ver bajo qué rol has iniciado sesión (Administrador o Usuario Estándar).</li>
                </ul>
            </section>

            <!-- Sección 3: Creación de Reportes -->
            <section id="nuevo-reporte">
                <h2>3. Nuevo Reporte (Cuaderno de Turno)</h2>
                <p>El núcleo del sistema. Para iniciar tu turno, haz clic en <strong>"Nuevo Reporte"</strong> en el menú lateral. El sistema te guiará a través de un formulario interactivo.</p>
                
                <h3>Datos Generales</h3>
                <p>En la primera sección, los datos básicos se llenan solos:</p>
                <ul>
                    <li><strong>Responsable:</strong> Se coloca automáticamente tu nombre de usuario.</li>
                    <li><strong>Fecha y Hora:</strong> Capturadas automáticamente del sistema.</li>
                    <li><strong>Turno:</strong> Seleccionado según la configuración de la institución (Mañana, Tarde, Noche).</li>
                </ul>

                <h3 id="gestion-personal">3.1 Gestión de Personal</h3>
                <p>Es la distribución de quiénes trabajarán hoy y dónde.</p>
                
                <div class="img-placeholder">
                    <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                    <div class="caption">[AQUÍ: Captura de la sección de asignación de Operadores y Personal de Campo]</div>
                </div>

                <ol>
                    <li><strong>Operadores de Cámaras:</strong> Puedes buscar a los operadores que vigilarán los monitores hoy escribiendo sus apellidos. Una vez agregados, haz clic en el botón mágico <strong>"Distribuir Equitativamente"</strong> para que el sistema asigne automáticamente qué cámaras o postes cuidará cada uno.</li>
                    <li><strong>Personal de Campo (Serenazgo):</strong> Busca a los efectivos de serenazgo y agrégalos a la lista de patrullaje.</li>
                </ol>

                <h3 id="control-unidades">3.2 Control de Unidades de Patrullaje</h3>
                <p>Al agregar al Personal de Campo, debes definir en qué modalidad trabajarán:</p>
                <ul>
                    <li><strong>Halcones (Vehicular):</strong> Asignas una Camioneta, e indicas quién será el Chofer, el Operador y el Lince (apoyo).</li>
                    <li><strong>Cazadores (Motorizado):</strong> Asignación de motocicletas.</li>
                    <li><strong>Sierra Bravo (A Pie):</strong> Personal caminante, se les asigna una zona o cuadrante.</li>
                </ul>
                
                <div class="callout warning">
                    <svg class="callout-icon" viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                    <p><strong>Aviso:</strong> Si intentas abrir las unidades vehiculares y recibes una alerta de que "No hay unidades", significa que aún no has agregado a ningún sereno en modalidad de Halcones o Cazadores.</p>
                </div>

                <h3 id="registro-ocurrencias">3.3 Registro de Ocurrencias y Asistencia IA</h3>
                <p>Durante el turno, deberás documentar las incidencias, robos, auxilios y visualizaciones destacadas de las cámaras.</p>
                <p>Para no perder tiempo pensando en la gramática policial, el sistema cuenta con un <strong>Botón de Inteligencia Artificial (Mago 🪄)</strong>:</p>
                <ol>
                    <li>Redacta la incidencia de manera natural, rápida y con tus propias palabras (ej: <em>"vimos a un ladron corriendo en la calle 2 y lo agarramos rapido"</em>).</li>
                    <li>Presiona el botón de Corrección Mágica.</li>
                    <li>La IA convertirá tu texto a un formato policial profesional, respetuoso y con excelente ortografía, listo para guardarse.</li>
                </ol>

                <h3 id="trabajo-colaborativo">3.4 Trabajo Colaborativo (Jefatura)</h3>
                <p>Si eres un <strong>Administrador</strong>, cuando un Supervisor esté redactando su reporte, verás un botón de <strong>"Monitoreo en Tiempo Real"</strong>.</p>
                <ul>
                    <li>Podrás entrar a ver en vivo, como un modo "solo lectura", lo que el supervisor escribe. La pantalla se pondrá translúcida para que no toques nada por error.</li>
                    <li>Si presionas "Editar Colaborativamente", podrás ayudar al supervisor a llenar el reporte. Lo que tú escribas, le aparecerá a él instantáneamente sin recargar la página.</li>
                </ul>
            </section>

            <!-- Sección 4: Kilometrajes -->
            <section id="kilometrajes">
                <h2>4. Kilometrajes</h2>
                <p>Módulo exclusivo para el cierre operativo de las unidades de patrullaje, accesible desde el menú lateral.</p>
                
                <div class="img-placeholder">
                    <svg viewBox="0 0 24 24"><path d="M19 17h2l.62-3.41A2 2 0 0019.65 11H18M5 17H3l-.62-3.41A2 2 0 014.35 11H6m12 0V9a2 2 0 00-2-2H8a2 2 0 00-2 2v2m12 0H6M9 17a2 2 0 100-4 2 2 0 000 4zm6 0a2 2 0 100-4 2 2 0 000 4z"></path></svg>
                    <div class="caption">[AQUÍ: Captura de pantalla del módulo Kilometrajes]</div>
                </div>

                <p>Una vez que en "Nuevo Reporte" asignaste quién manejaba qué unidad, aquí controlarás sus métricas:</p>
                <ul>
                    <li><strong>KM Recorridos:</strong> Ingresa el kilometraje patrullado. El sistema pintará la casilla de <span style="color:green;font-weight:bold;">Verde</span> si se cumplió la meta institucional (ej. 90KM).</li>
                    <li><strong>A.P. (Auxilio Público):</strong> Ingresa los minutos dedicados a estas actividades.</li>
                    <li><strong>P.O. (Puntos Observados):</strong> Cantidad de intervenciones.</li>
                </ul>
                <p><strong>Herramientas extra:</strong> Desde esta pantalla puedes acceder directo al sistema GPS (SIPCOP-M o Wialon) y utilizar el botón verde de <strong>"Reporte WhatsApp"</strong> para generar un texto automático listo para enviar a tus grupos de chat institucionales.</p>
            </section>

            <!-- Sección 5: Megáfonos -->
            <section id="megafonos">
                <h2>5. Megáfonos</h2>
                <p>Módulo de respuesta rápida para lanzar mensajes de alerta auditiva y disuasión a través del sistema de perifoneo IP.</p>
                
                <div class="img-placeholder">
                    <svg viewBox="0 0 24 24"><path d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                    <div class="caption">[AQUÍ: Captura de pantalla de la ventana de Megáfonos mostrando los códigos]</div>
                </div>

                <p>Al hacer clic en este botón, se desplegará la lista de equipos disponibles. El sistema te muestra el <strong>código del megáfono</strong> asignado a cada poste de emergencia o cámara de la ciudad.</p>
                
                <div class="callout info">
                    <svg class="callout-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    <p><strong>Disuasión Inmediata:</strong> Utilizando el código del megáfono, el operador puede enlazarse rápidamente al dispositivo en la calle para advertir y disuadir a personas sospechosas o emitir avisos de seguridad comunitaria en tiempo real.</p>
                </div>
            </section>

            <!-- Sección 6: Buscar Reporte -->
            <section id="buscar-reporte">
                <h2>6. Buscar Reporte (Historial)</h2>
                <p>Este es el archivo histórico de la institución. Aquí quedan guardados, de manera inalterable, todos los reportes de turnos finalizados.</p>
                
                <div class="img-placeholder">
                    <svg viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <div class="caption">[AQUÍ: Captura del buscador de reportes]</div>
                </div>

                <p>Puedes filtrar por fecha exacta o por el responsable del turno.</p>
                <h3>Descargas Profesionales</h3>
                <p>Al entrar a ver un reporte antiguo, arriba a la derecha verás dos opciones vitales para la jefatura:</p>
                <ul>
                    <li><strong>Descargar PDF:</strong> Genera un documento sumamente formal, paginado y con espacios de firma. Se usa para entregar a la gerencia física.</li>
                    <li><strong>Descargar Excel:</strong> Genera una hoja de cálculo unificada (una sola pestaña) con todos los incidentes y métricas de patrullaje para facilitar cruces estadísticos.</li>
                </ul>
            </section>

            <!-- MÓDULOS ADMINISTRATIVOS -->
            <h2 id="modulos-admin" style="font-size: 2rem; margin-top: 60px; color: var(--primary-color);">Configuración y Módulos Maestros</h2>
            <div class="callout warning">
                <svg class="callout-icon" viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                <p><strong>Exclusivo Administradores:</strong> Los módulos siguientes sirven para nutrir las opciones que ven los supervisores. Mantén estas tablas actualizadas.</p>
            </div>

            <!-- Sección 7: Serenazgo -->
            <section id="serenazgo">
    <h2>7. Serenazgo (Recursos Humanos)</h2>
    <p>Este módulo gestiona el directorio del personal operativo del municipio.</p>
    <div class="img-placeholder">
        <svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>
        <div class="caption">[AQUÍ: Captura de pantalla del formulario y tabla de Serenazgo]</div>
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
    <div class="callout warning">
        <svg class="callout-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6z"/></svg>
        <p><strong>Atención Administradores:</strong> Desactivar un agente no elimina su historial. Los supervisores ya no verán a los agentes desactivados al crear reportes.</p>
    </div>
</section>

            <!-- Sección 8: Usuarios -->
            <section id="usuarios">
                <h2>8. Usuarios</h2>
                <p>Define quién puede hacer login en el sistema.</p>
                <p>Al crear un usuario, le das un correo, una contraseña y eliges su Rol: <strong>Supervisor</strong> (operador del cuaderno) o <strong>Administrador</strong> (acceso total).</p>
            </section>

            <!-- Sección 9: Vehículos -->
            <section id="vehiculos">
                <h2>9. Vehículos (Parque Automotor)</h2>
                <p>Este módulo permite a los administradores llevar el registro y control de todas las unidades móviles (camionetas, motocicletas, etc.) asignadas al patrullaje.</p>
                
                <div class="img-placeholder">
                    <svg viewBox="0 0 24 24"><path d="M19 17h2l.62-3.41A2 2 0 0019.65 11H18M5 17H3l-.62-3.41A2 2 0 014.35 11H6m12 0V9a2 2 0 00-2-2H8a2 2 0 00-2 2v2m12 0H6M9 17a2 2 0 100-4 2 2 0 000 4zm6 0a2 2 0 100-4 2 2 0 000 4z"></path></svg>
                    <div class="caption">[AQUÍ: Captura de pantalla del formulario y lista del Parque Automotor]</div>
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
                
                <div class="callout warning">
                    <svg class="callout-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    <p><strong>Atención Administradores:</strong> Si una moto o camioneta se malogra o entra a taller, debes usar el botón de "check" en sus acciones para cambiar su estado a INOPERATIVO o MANTENIMIENTO. <strong>Solo los vehículos con estado verde "OPERATIVO" le aparecerán a los supervisores en la lista al momento de crear un Nuevo Reporte.</strong></p>
                </div>
            </section>

            <!-- Sección 10: Cámaras -->
            <section id="camaras">
    <h2>10. Cámaras (Sistema de Videovigilancia)</h2>
    <p>Gestión y registro de los equipos de vigilancia instalados en la ciudad. Cada cámara debe asociarse a un sector o zona geográfica para que el módulo de “Distribuir Equitativamente” pueda asignar automáticamente las cámaras a los operadores.</p>

    <div class="img-placeholder">
        <svg viewBox="0 0 24 24"><path d="M4 4h16v16H4V4z"/></svg>
        <div class="caption">[AQUÍ: Captura de pantalla del listado de cámaras y formulario de registro]</div>
    </div>

    <h3>¿Cómo registrar una nueva cámara?</h3>
    <p>En la parte superior del módulo encontrarás un formulario rápido. Completa los siguientes campos:</p>
    <ul>
        <li><strong>Nombre:</strong> Identificador interno (ej. “Cámara Centro 1”).</li>
        <li><strong>IP / URL:</strong> Dirección de acceso al streaming.</li>
        <li><strong>Sector:</strong> Área o distrito al que pertenece.</li>
        <li><strong>Tipo:</strong> Fijo, PTZ, Dome, etc.</li>
        <li><strong>Estado:</strong> Activo (por defecto). Si la cámara está fuera de servicio, selecciona “Inactiva”.</li>
    </ul>
    <p>Una vez completado, pulsa el botón azul <strong>"+ REGISTRAR CÁMARA"</strong>.</p>

    <h3>Gestión y control de operatividad</h3>
    <p>En la tabla inferior “Cámaras Registradas” observarás la lista completa. En la columna <strong>ACCIONES</strong> aparecen íconos para editar, desactivar o eliminar.</p>

    <div class="callout warning">
        <svg class="callout-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 13h-2v-6h2v6z"/></svg>
        <p><strong>Atención Administradores:</strong> Desactivar una cámara no elimina su historial de grabaciones. Los operadores no la verán en el listado al crear reportes.</p>
    </div>
</section>

            <!-- Sección 11: Backups -->
            <section id="modulo-backups">
                <h2>11. Backups (Copias de Seguridad)</h2>
                <p>El seguro de vida de la información de tu municipio.</p>
                <p>Al hacer clic en "Nuevo Backup", se congelará una copia de toda la base de datos de ese momento. La puedes descargar en formato seguro a tu USB o disco duro.</p>
                
                <div class="img-placeholder">
                    <svg viewBox="0 0 24 24"><path d="M4 4h16v16H4V4z"/></svg>
                    <div class="caption">[AQUÍ: Captura de pantalla del proceso de crear y descargar backup]</div>
                </div>
                
                <div class="callout danger">
                    <svg class="callout-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    <p><strong>Cuidado con Restaurar:</strong> La opción de Restaurar requiere contraseña. Si restauras un backup de hace 1 semana, perderás TODO lo que se escribió esta última semana. Úsalo solo en casos de desastre.</p>
                </div>
            </section>

            <!-- Sección 12: Configuración -->
            <section id="configuracion">
                <h2>12. Configuración General</h2>
                <p>Ajustes técnicos de la aplicación web, como la franja horaria de los turnos de trabajo, nombres de dependencias o tokens de integración con Inteligencia Artificial.</p>
            </section>

        </div>
    </main>

    <!-- Botón toggle para móviles -->
    <button class="mobile-toggle" id="mobileToggle">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
            <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
        </svg>
    </button>

    <script src="js/script.js"></script>
</body>
</html>
