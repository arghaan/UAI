 # Инструкция по сборке/запуску проекта
 
### Установка окружения

Клонируем репозиторий

```shell
git clone https://github.com/arghaan/UAI.git ./UAI
cd UAI
```
Копируем настройки проекта 
```
cp ./app/.env.dist ./app/.env
```
_Для отправки писем нужно настроить переменные MAILER__*

---
Стартуем Docker контейнер с php_fpm
```shell
docker-compose -f .docker/docker-compose.yml up -d php8_fpm
```

Запускаем composer
```shell
docker exec -it php8_fpm composer install
```

Стартуем остальные контейнеры
```shell
docker-compose -f .docker/docker-compose.yml up -d
```
Запускаем миграции и грузим фикстуры
```shell
docker exec -it php8_fpm bin/console doctrine:migrations:migrate
docker exec -it php8_fpm bin/console doctrine:fixture:load
```
## System Requirements
<details>
Данная сборка docker-compose тестировалась на следующей конфигурации:

```
$ lsb_release -a
LSB Version:    n/a
Distributor ID: ManjaroLinux
Description:    Manjaro Linux
Release:        21.0.6
Codename:       Ornara

$ uname -r
5.10.41-1-MANJARO

$ docker -v
Docker version 20.10.6, build 370c28948e

$ docker-compose version
docker-compose version 1.29.2, build unknown
docker-py version: 5.0.0
CPython version: 3.9.5
OpenSSL version: OpenSSL 1.1.1k  25 Mar 2021
```
</details>