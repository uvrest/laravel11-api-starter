## Requisitos

* PHP 8.2 ou superior
* Mysql 8 ou superior
* Composer
* Node/npm

## Laravel 11 Setup

<p>Veja mais em: <a href="https://laravel.com/docs/11.x/installation" target="_blank">Documentação de instalação do Laravel 11</a></p>

* Instalar o Laravel Installer globalmente através do composer
```
composer global require laravel/installer
```
* Depois de instalar o Laravel Installer, rodar no terminal:
```
laravel new example-app
```
* Depois da aplicação instalada:
```
cd example-app
npm install && npm run build
composer run dev
```

## Application Setup

* Faça um clone deste repositório
* Garanta que as dependências do Auth Sanctum foram instaladas corretamente
```
php artisan install:api
```
* Configure o seu arquivo ".env" com as informações de autenticação do banco de dados
* Rode as migrations com:
```
php artisan migrate
```
* Para que o upload de arquivos funcione corretamente é necessário ciar o link simbólico na pasta public:
```
php artisan storage:link
```
* Configure as rotas no arquivo /routes/api.php
* Se você quiser desabilitar o sistema de avatar nos colaboradores, basta remover o contract HasAvatarInterface e a trait HasInterface da Model User e edite o Controller AuthController.
* Caso você queira utilizar a tabela de usuários padrão do Laravel (sem username e avatar), exlua a migration "database/migrations/xxxx_xx_xx_xxxxxx_update_users_table.php"
