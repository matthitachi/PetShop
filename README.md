# PET SHOP Platform
## Note
   


PHP 8.3 
Laravel Framework 11.9

## Installation

1. **Clone the repository:**

   ```bash
   git clone https://github.com/matthitachi/petshop.git

   ```

2. **Creeate Environment varialbe file:**

    Run:
   ```bash
   cp .env.example .env
   cp .env.example.testing .env.testing
   ```
   
2. **Docker setup:**
Setup Docker Compose and run the following command within the project folder:
   ```bash
   docker-compose up -d
   ```

3. **Code setup**
The Laravel project starts automatically within Docker Compose. 
After it's up and running, execute the following command to migrate the database and seed it:       

    ```bash
       docker-compose exec app openssl genrsa -out key.pem 2048
       docker-compose exec app composer install  
       docker-compose exec app php artisan key:generate
       docker-compose exec app php artisan key:generate --env=testing
       docker-compose exec app php artisan migrate:refresh --seed
   
    ```
4. **Generate Documentation**
The command generates  the swagger documentation.       

    ```bash
       docker-compose exec app php artisan l5-swagger:generate
   
    ```
   

