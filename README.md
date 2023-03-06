<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>
<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>
<p align="center"><a href="https://filamentphp.com" target="_blank"><img src="https://user-images.githubusercontent.com/41773797/131910226-676cb28a-332d-4162-a6a8-136a93d5a70f.png" width="400" alt="Laravel Logo"></a></p>
<p align="center">
    <a href="https://github.com/filamentphp/filament/actions"><img alt="Tests passing" src="https://img.shields.io/badge/Tests-passing-green?style=for-the-badge&logo=github"></a>
    <a href="https://laravel.com"><img alt="Laravel v8.x" src="https://img.shields.io/badge/Laravel-v8.x-FF2D20?style=for-the-badge&logo=laravel"></a>
    <a href="https://laravel-livewire.com"><img alt="Livewire v2.x" src="https://img.shields.io/badge/Livewire-v2.x-FB70A9?style=for-the-badge"></a>
    <a href="https://php.net"><img alt="PHP 8.0" src="https://img.shields.io/badge/PHP-8.0-777BB4?style=for-the-badge&logo=php"></a>
</p>

# Docker 

If you are not using docker jump to [Instalation](#instalation) part.
This use docker compose system. In this project you have to build container and them up them. Check more [here](https://www.digitalocean.com/community/tutorials/how-to-install-and-set-up-laravel-with-docker-compose-on-ubuntu-22-04)
```
docker-compose build app
docker-compose up -d
docker-compose ps #to check if containers are running 
```

After that to execute any command inside container you need <b>docker-compose exec app <i>$command</i></b> 

Example
```
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
```
To stop all containers 
```
docker-compose down
```

***
# Instalation

```
composer install
```
Configure file .env 
```
php artisan key:generate
php artisan migrate
```
Generate premission/roles and super-admin. More info [here](https://github.com/bezhanSalleh/filament-shield)

```
php artisan shield:generate --all

php artisan shield:super-admi
```

Now instalation is <b>complete</b>
***

### Create Migration
Example for table pets
```
php artisan make:migration create_pets_table
```
### Rerun a migration; 

Example

* drop the table(s)
* php artisan migrate:refresh --path=database\migrations\2023_02_07_215605_create_pets_table.php 

### Create Model
<a href="https://github.com/reliese/laravel">https://github.com/reliese/laravel</a>
You can scaffold a specific table like this:
```
php artisan code:models --table=pets
```

### Create resource 
Example

Crud
```
php artisan make:filament-resource Pet --generate --soft-deletes
```

Crud com modals (edit/create is a modal)
```
php artisan make:filament-resource Pet --generate --simple --soft-deletes
```
Add a view to existing resource 
```
php artisan make:filament-page ViewPet --resource=PetResource --type=ViewRecord
```
## Security Vulnerabilities

## License


