# Task-messages API
This is a repository for Laravel project of Task manager API.

## Usage

- Clone the project with ``` git clone ```
- Copy ```.env.example``` file to ```.env``` and edit database credentials there
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE={your db name}
DB_USERNAME={your username}
DB_PASSWORD={your password}
```
- Run  ```composer install```
- Run ```php artisan migrate --seed```
- Run ```php artisan serve```

The API will be running on localhost:8000.

