<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## DataHubAPI

Guía de instalación y uso de la API construida con Laravel. Incluye autenticación JWT, control de roles y endpoints para gestión de usuarios y productos.

## Requisitos
- PHP 8.2+
- Composer
- Base de datos (MySQL/MariaDB, PostgreSQL, etc.)
- Laragon/XAMPP o servidor web equivalente

## Instalación del proyecto
1. Clonar el repositorio:
	 ```powershell
	 git clone https://github.com/jrsanz/DataHubAPI.git;
     cd DataHubAPI;
	 ```
2. Instalar dependencias PHP:
	 ```powershell
	 composer install
	 ```
3. Copiar variables de entorno y generar key:
	 ```powershell
	 copy .env.example .env
	 php artisan key:generate
	 ```
4. Configurar `.env`:
	 - `APP_URL=http://127.0.0.1:8000`
	 - `DB_CONNECTION=mysql`
	 - `DB_HOST=127.0.0.1`
	 - `DB_PORT=3306`
	 - `DB_DATABASE=<tu_db>`
	 - `DB_USERNAME=<tu_usuario>`
	 - `DB_PASSWORD=<tu_password>`

5. Generar token secreto JWT:
	 ```powershell
	 php artisan jwt:secret
	 ```
6. Generar documentación OpenAPI/Swagger:
	 ```powershell
	 php artisan l5-swagger:generate
	 ```
7. Ejecutar migraciones y seeders:
	 ```powershell
	 php artisan migrate:fresh --seed
	 ```
8. Levantar servidor de desarrollo:
	 ```powershell
	 php artisan serve
	 ```

## Autenticación y roles
- Middleware de autenticación: `auth:api` (JWT)
- Roles por middleware: `role:user,admin` y `role:admin`
- Obtén un token al iniciar sesión y úsalo en `Authorization: Bearer <TOKEN>`

## Documentación OpenAPI/Swagger
### Importar Swagger en Postman
- Ubica el archivo generado: `storage/api-docs/api-docs.json`.
- En Postman:
	- Ve a `File` > `Import`.
	- Usa `Upload Files` y selecciona `api-docs.json` (o pestaña `Raw text` para pegar el contenido).
	- Elige el `Workspace` y confirma; se creará una colección con los endpoints.
- Configura el token JWT en Postman:
	- Crea un `Environment` con variable `token`.
	- En la colección, `Authorization` = `Bearer Token` y valor `{{token}}`.
	- O añade el header `Authorization: Bearer {{token}}` por request.

### Redirección a documentación Swagger en el sitio
- La UI de Swagger suele publicarse en `http://127.0.0.1:8000/api/documentation`.
- Asegúrate de ejecutar `php artisan l5-swagger:generate` tras cambios en las anotaciones.

## Endpoints principales (API v1)
Base: `http://127.0.0.1:8000/api/v1`

### Usuarios
- `POST /users/register`: Registro
	```json
	{
		"name": "Juan Pérez",
		"email": "juan.perez@example.com",
		"password": "password123",
		"role": "user"
	}
	```
- `POST /users/login`: Login
	```json
	{
		"email": "juan.perez@example.com",
		"password": "password123",
	}
	```
- `POST /users/logout` (auth)
- `GET /users/me` (auth)

### Productos
- `GET /products` (roles: user, admin)
- `GET /products/search?data=<texto>` (roles: user, admin)
- `GET /products/{product}` (roles: user, admin)
- `POST /products` (rol: admin)
- `PUT /products/{product}` (rol: admin)
- `DELETE /products/{product}` (rol: admin)

## Resolución de problemas
- 401: verifica `Authorization: Bearer <TOKEN>` y que el token no esté expirado.
- Error JWT: vuelve a generar con `php artisan jwt:secret` y limpia cachés (`php artisan config:clear`).

## Licencia
Uso interno. Consulta al propietario del repo para permisos de distribución.