# Install project Symfony6-PHP8-&-Docker in docker

## Things you need
* composer
* npm
* docker

## Clone repository to your local machine
```bash
git clone https://github.com/moroztaras/symfony6-php8-docker-2022
```

##Create project config
```bash
cd symfony6-php8-docker-2022
cp .env .env.local
cp ./docker/.env.dist ./docker/.env
```

##Build a project in the docker
```bash
make dc_build
```
##Run a project with the docker
```bash
make dc_up
```
##Stop a project with the docker
```bash
make dc_stop
```

##Rerun a container with project in the docker
```bash
make dc_stop dc_build dc_up
```
##Go to the link at
```bash
http://127.0.0.1:8000
```
