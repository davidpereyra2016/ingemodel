# Proyecto de Reservas de Salón de Eventos y Cancha de Fútbol

Este proyecto es una aplicación web desarrollada en PHP bajo el patrón de arquitectura MVC (Modelo-Vista-Controlador). Permite la gestión de reservas para salones de eventos y canchas de fútbol, ofreciendo funcionalidades tanto para usuarios como para administradores.

## Características principales
- **Reserva de salones de eventos y canchas de fútbol**
- **Gestión de usuarios y autenticación**
- **Panel de administración y dashboard con estadísticas**
- **Envío de notificaciones por correo electrónico (PHPMailer)**
- **Separación clara de lógica de negocio, acceso a datos y presentación (MVC)**

## Estructura del Proyecto

- **/controladores/**: Lógica de negocio y manejo de peticiones.
  - `controlador_reservas.php`: Control de reservas (salones y canchas).
  - `controlador_usuarios.php`: Gestión y autenticación de usuarios.
  - `controlador_paginas.php`: Navegación y páginas generales.

- **/modelos/**: Acceso y manipulación de datos.
  - `modelo_reservas.php`: Operaciones CRUD de reservas.
  - `modelo_usuarios.php`: Gestión de datos de usuarios.
  - `modelo_dashboard.php`: Datos para el dashboard.

- **/vistas/**: Presentación y componentes visuales.
  - `/reservas/`: Vistas para reservas.
  - `/usuarios/`: Vistas para usuarios.
  - `/partials/`: Headers, footers y fragmentos reutilizables.
  - `/paginas/`: Páginas generales.
  - `template.php`: Plantilla principal y ruteador MVC.

- **Ruteo:**
  - El archivo `vistas/template.php` actúa como ruteador principal, cargando vistas y controladores según la petición.
  - El archivo `ruteador.php` en la raíz puede servir como punto de entrada o ruteo alternativo.

- **Base de datos:**
  - El archivo `conexion.php` gestiona la conexión a la base de datos.
  - El directorio `/sql/` contiene el archivo `tablas.sql` con la estructura necesaria para crear las tablas y poblar la base de datos.

- **Librerías externas:**
  - PHPMailer para envío de correos, ubicado en `utils/lib/phpMailer/`.

- **Otros recursos:**
  - `/assets/`: Recursos estáticos (imágenes, CSS, JS).
  - `/utils/`: Utilidades y librerías auxiliares.
  - `/middleware/`: Lógica intermedia (validaciones, autenticaciones, etc.).

## Instalación y configuración

1. **Clona o descarga el repositorio en tu servidor local.**
2. **Configura la base de datos:**
   - Crea una base de datos en tu gestor MySQL/MariaDB.
   - Importa el archivo `sql/tablas.sql` para crear las tablas necesarias:
     ```
     IMPORTANTE: Puedes usar phpMyAdmin o el siguiente comando en terminal:
     mysql -u TU_USUARIO -p TU_BASE_DE_DATOS < sql/tablas.sql
     ```
3. **Configura la conexión a la base de datos:**
   - Renombra `conexion.example.php` a `conexion.php` si es necesario y ajusta los parámetros de conexión (`host`, `usuario`, `contraseña`, `base de datos`).
4. **Asegúrate de que la carpeta `utils/lib/phpMailer/` contenga los archivos `PHPMailer.php`, `SMTP.php` y `Exception.php`**
5. **Configura los permisos adecuados para las carpetas de recursos y temporales si es necesario.**

## Uso
- Accede a la aplicación desde tu navegador apuntando a la carpeta del proyecto (por ejemplo, `http://localhost/ingemodel/`).
- Regístrate o inicia sesión como usuario/administrador.
- Realiza reservas, consulta el dashboard y utiliza las funcionalidades disponibles según tu rol.

## Créditos y dependencias
- [PHPMailer](https://github.com/PHPMailer/PHPMailer) para el envío de correos electrónicos.

---

Este README está orientado a desarrolladores y administradores que deseen instalar, configurar o contribuir al sistema de reservas. Para dudas o mejoras, consulta la documentación interna del código o contacta al responsable del proyecto.
