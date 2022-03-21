#Aplicação desenvolvida com framework Symfony e PHP 8, está com o .env configurado para banco de dados MySql 5.7


#Como executar:

# (1) Instale as dependências do projeto com o comando:
composer install

# (2) Edite o .env com os dados do seu banco de dados

# (3) Para criar o banco de dados execute o comando:
php bin/console doctrine:database:create

# (4) Para criar as tabelas execute o comando:
php bin/console doctrine:migrations:migrate

# (5) Para popular o banco de dados execute o comando:
php bin/console doctrine:fixtures:load

# (6) para executar o projeto execute o comando:
php -S localhost:8080 -t public

# (7) Segue junto com o projeto o arquivo Rotas.postman_collection.json para testar a API no postman