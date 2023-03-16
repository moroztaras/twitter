# Install project Symfony6-PHP8-&-Docker in docker

### Things you need
* composer
* npm
* docker
* php-cs-fixer (brew install php-cs-fixer)

### Clone repository to your local machine
```bash
% git clone git@github.com:moroztaras/symfony6-php8-docker-2023.git
```

### Create project config
```bash
% cd symfony6-php8-docker-2023
% cp .env .env.local
% cp ./docker/.env.dist ./docker/.env
```
### Quick start of the project

Adjust .env.local line 9.

It's credentials to database.

### Build a project in the docker
```bash
% make dc_build
```
### Run a project with the docker
```bash
% make dc_up
```
### Stop a project with the docker
```bash
% make dc_stop
```

### Rerun a container with project in the docker
```bash
% make dc_restart
```
or
```bash
% make dc_restart
```

### Go to the link at

http://127.0.0.1:888

### Run tests from docker container
```bash
% make app_bash
```
### Load data fixtures
```bash
% php bin/console doctrine:fixtures:load --env=test
```

### Run tests
```bash
% php ./vendor/bin/phpunit
```

### Run PHP CS Fixer
```bash
% php-cs-fixer fix src
```
