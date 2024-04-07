# Social Network - Twitter

Project on Symfony 6 using docker

## Install project

### Things you need
* composer
* npm
* docker
* php-cs-fixer (brew install php-cs-fixer)

### Clone repository to your local machine
```bash
% git clone git@github.com:moroztaras/twitter.git
```

### Create project config
```bash
% cd twitter
% cp .env .env.local
% cp ./docker/.env.dist ./docker/.env
```
### Quick start of the project

Adjust .env.local line 6.

It's credentials to database.

### Run a project with the docker
```bash
% make dc_up
```
### Download all dependency packages
```bash
composer install
```

### Execute a migration to the latest available version
```bash
./bin/console doctrine:migrations:migrate
```

### Load data fixtures to database
```bash
./bin/console doctrine:fixtures:load
```

### Run server
```bash
% symfony serve:start
```

### Go to the link at
```bash
http://127.0.0.1:8000
```

### Login as admin

email: admin@twitter.ua

pass: qwerty