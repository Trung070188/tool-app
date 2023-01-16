##Cài đặt môi trường cho PLS 
1 - Yêu cầu phần mềm: <br>
  ```
    - PHP 8.1 trở lên với extension: php-curl, php-pdo, php-redis
    - Redis server (yum install redis tren centos)
    - Composer 2.x trở lên (https://getcomposer.org/download/)
    - NodeJS v16 trở lên (https://nodejs.org/en/)
    - yarn (gõ lệnh npm install -g yarn nếu đã cài nodejs)
    - pm2 (npm install -g pm2)
    
```

2 - Copy .env.example thành .env và set up info db
   ```
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=test
    DB_USERNAME=root
    DB_PASSWORD=quantm

```

3 Cài đặt cho  cms
```
composer install
yarn install
php artisan key:gen
yarn prod
    
```

    
