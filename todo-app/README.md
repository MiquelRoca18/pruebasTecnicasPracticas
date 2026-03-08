# ✅ TODO App

Mini gestor de tareas construido con Laravel 11, Livewire Volt, Alpine.js y Tailwind CSS.

## Requisitos

- PHP 8.2+
- Composer
- Node.js y npm

## Instalación

1. Clona el repositorio
```bash
git clone https://github.com/tu-usuario/todo-app.git
cd todo-app
```

2. Instala las dependencias
```bash
composer install
npm install
```

3. Configura el entorno
```bash
cp .env.example .env
php artisan key:generate
```

4. Crea la base de datos si no esta el archivo creado y ejecuta las migraciones
```bash
touch database/database.sqlite
php artisan migrate
```

5. Compila los assets y arranca el servidor
```bash
npm run dev
php artisan serve
```

Abre [http://localhost:8000](http://localhost:8000) y ya está.

## Funcionalidades

- Crear, editar y eliminar tareas
- Marcar tareas como completadas
- Filtrar por todas / pendientes / completadas
- Búsqueda en tiempo real
- Categorías con colores personalizables
- Drag & drop para reordenar tareas
- Exportar a PDF y CSV
- Dark mode
- Notificaciones toast

## Tests
```bash
./vendor/bin/pest
```