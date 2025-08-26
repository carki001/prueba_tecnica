## Prueba técnica

Prueba técnica con CRUD de Empleados. El proyecto fue creado en base a https://github.com/carki001/time_keeper . Algunas clases y controladores todavía persisten de este proyecto.

## Requerimientos

-   php 8.0 - 8.2
-   node v16.20.2

## Frameworks usados

-   Laravel 9
-   Vue 2, vuetify 2

## Setup Instructions

-   Cree una base de datos vacía
-   Configure la conexión a su base de datos con el archivo _.env._ Puede usar _.env.example_ como referencia para construir el nuevo archivo _.env_
-   Puede ejecutar los comandos _composer install_ y _npm install_ para instalar las dependencias de PHP y JavaScript, respectivamente
-   Realice la migración con _php artisan migrate_
-   Revise el archivo _database\seeders\DatabaseSeeder.php_ para verificar las credenciales predeterminadas
-   Ejecute el comando _php artisan db:seed_
-   Ejecute el comando _npm run dev_ para compilar el código de javascript
-   Ejecute los siguientes comandos:
    -   php artisan passport:keys
    -   php artisan key:generate
    -   php artisan passport:install
    -   php artisan config:clear
