Antes de nada quiero comentar una cosa que no se muy bien porqué pasa de esa manera, y es que al intentar acceder a una ruta que esté protegida sin tener un token puesto,
genera un error interno del servidor del tipo 500 en vez de dar un error 403 de que no tienes permisos.
He intentado solucionarlo pero no se de donde viene el problema, pero al menos no deja acceder a una ruta protegida sin token.

Pasos para iniciar el proyecto:

1.Clonar el proyecto mediante "git clone https://github.com/PabloAriasTorres/PruebaNitsNets.git".
2.Ejecutar el comando "composer install".
3.Cambiar el archivo ".env.example" por ".env".
4.Luego cambiar la conexión de la base de datos de tal manera que quede así:
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=cms
    DB_USERNAME=root
    DB_PASSWORD=
5.Ejecutar el comando "php artisan migrate --seed".
6.Generar la clave de encriptación con  "php artisan key:generate".
7.Ejecutar "php artisan serve" y abrir la url.
8.Añadir a la url /api/documentation para abrir Swagger.
