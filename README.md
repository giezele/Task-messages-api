This application is currently work in progress (WIP)

# Task-messages API
A simple Laravel application to allow users manage their tasks. Tasks can have messages attached to them. The scope of this app is API only.

## Usage

- Clone the project with ``` git clone ```
- Create "new Schema" in your database
- Copy ```.env.example``` file to ```.env``` and edit database credentials there
```
DB_DATABASE={your db name}
DB_USERNAME={your username}
DB_PASSWORD={your password}
```
- Run  ```composer install```
- Run ```php artisan migrate --seed```
- Run ```php artisan serve```


Check APIspec for API endpoints

