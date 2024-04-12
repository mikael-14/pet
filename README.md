<p align="center">
   <a href="https://filamentphp.com/">
   <img src="https://github.com/filamentphp/filament/assets/41773797/8d5a0b12-4643-4b5c-964a-56f0db91b90a" alt="Banner" style="width: 100%; max-width: 800px;" />
   </a>
</p>

<p align="center">
    <a href="https://github.com/filamentphp/filament/actions"><img alt="Tests passing" src="https://img.shields.io/badge/Tests-passing-green?style=for-the-badge&logo=github"></a>
    <a href="https://laravel.com"><img alt="Laravel v10.x" src="https://img.shields.io/badge/Laravel-v10.x-FF2D20?style=for-the-badge&logo=laravel"></a>
    <a href="https://livewire.laravel.com"><img alt="Livewire v3.x" src="https://img.shields.io/badge/Livewire-v3.x-FB70A9?style=for-the-badge"></a>
    <a href="https://php.net"><img alt="PHP 8.1" src="https://img.shields.io/badge/PHP-8.1-777BB4?style=for-the-badge&logo=php"></a>
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
docker-compose exec app php artisan migrate
docker-compose exec app php artisan shield:generate --all
docker-compose exec app php artisan shield:super-admin
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

php artisan shield:super-admin
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
* php artisan migrate:refresh --path=database/migrations/2023_02_07_215605_create_pets_table.php 

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
### Relationships 
See more in https://filamentphp.com/docs/2.x/admin/resources/relation-managers 
```
php artisan make:filament-relation-manager CategoryResource posts title
```
- CategoryResource is the name of the resource class for the parent model.
- posts is the name of the relationship you want to manage.
- title is the name of the attribute that will be used to identify posts.

Relation pages see more in https://filamentphp.com/docs/3.x/panels/resources/relation-managers#relation-pages 
```
php artisan make:filament-page ManageCustomerAddresses --resource=CustomerResource --type=ManageRelatedRecords
```

### Template
Icons https://blade-ui-kit.com/blade-icons (installed herocions; tabler; unicons)

## Security Vulnerabilities

## License


