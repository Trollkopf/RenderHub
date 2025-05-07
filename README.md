# 🖥️ RenderHub

RenderHub es una plataforma de gestión de trabajos creativos, diseñada para facilitar la comunicación entre clientes y administradores (renderistas, diseñadores, etc.). Permite solicitar trabajos, hacer seguimiento, gestionar modificaciones y recibir notificaciones tanto para entregas como para eventos importantes.

---

## 🚀 Características principales

- Gestión completa de trabajos por parte de clientes y admins.
- Hasta **3 solicitudes de cambios gratuitas** por trabajo.
- Asignación de responsables y fechas límite.
- **Calendario integrado** con eventos únicos o recurrentes.
- **Notificaciones** automáticas para avisar de entregas, cambios y eventos.
- Subida y descarga de archivos asociados.
- Panel de configuración para ajustar aspectos clave del sistema.
- Interfaz dividida por roles: `Admin` y `Cliente`.

---

## 🛠️ Requisitos

- PHP >= 8.2
- Composer
- Laravel 12
- Node.js & npm
- MySQL / SQLite

---

## ⚙️ Instalación

1. Clona este repositorio:
   ```bash
   git clone https://github.com/trollkopf/renderhub.git
   cd renderhub
   ```

2. Instala dependencias:
   ```bash
   composer install
   npm install && npm run dev
   ```

3. Copia el archivo `.env.example` y configura tu entorno:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configura tu base de datos en `.env` y ejecuta las migraciones:
   ```bash
   php artisan migrate
   ```

5. Si usas SQLite, asegúrate de que el archivo exista:
   ```bash
   touch database/database.sqlite
   ```

6. Inicia el servidor:
   ```bash
   php artisan serve
   ```

---

## 🔔 Configurar notificaciones programadas

RenderHub envía notificaciones automáticamente 24 horas antes de:

- La **fecha límite de un trabajo asignado**.
- Un **evento de calendario** donde el administrador esté involucrado.

### 🕒 Añadir cron job

Para que Laravel ejecute tareas programadas, debes agregar este *cron job* a tu servidor (ej. en `/etc/crontab` o mediante `crontab -e`):

```bash
* * * * * cd /ruta/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

Esto ejecutará el planificador de Laravel cada minuto.

> 🧠 **Nota:** Laravel detecta automáticamente los comandos programados en Laravel 12, como `notifications:upcoming-events`, gracias a la anotación `#[AsScheduled('hourly')]`.

---

## 📁 Estructura relevante

- `app/Models/Work.php` → Modelo principal de trabajos.
- `app/Models/ChangeRequest.php` → Modelo para solicitudes de cambio.
- `app/Models/CalendarEvent.php` → Eventos del calendario.
- `app/Helpers/NotificationHelper.php` → Envío de notificaciones.
- `app/Console/Commands/SendUpcomingEventNotifications.php` → Tarea programada.
- `resources/js/Pages/Admin/` → Vistas del panel de administración.
- `resources/js/Pages/Client/` → Vistas para los clientes.
- `resources/js/Components/` → Componentes reutilizables (toasts, modales, etc.).

---

## 🧑‍💻 Autores

- Desarrollado por [Maximiliano Serratosa](https://maxserratosa.es)
