include .env
export

DOCKER_COMP = docker-compose
PHP_FPM = php-fpm

build: down main-build ssl-gen up init-migrations faker-migrations
restart: down up

up:
	$(DOCKER_COMP) up --detach

down:
	$(DOCKER_COMP) down --remove-orphans

sh:
	@$(DOCKER_COMP) exec php-fpm sh

logs:
	@$(DOCKER_COMP) logs --tail=0 --follow

main-build:
	$(DOCKER_COMP) build --pull --no-cache

ssl-gen:
	if test -f $$PWD/.docker/nginx/ssl/cert.pem; then echo "cert.pem exists, skipping certificate's generating"; else docker run -v $$PWD/.docker/nginx/ssl/:/ssl -it nginx openssl req -subj '/CN=localhost' -x509 -newkey rsa:4096 -nodes -keyout /ssl/key.pem -out /ssl/cert.pem -days 365; fi

init-migrations:
	sleep 5;
	$(DOCKER_COMP) run --rm $(PHP_FPM) php ./migrations/init-migrations.php;

faker-migrations:
	$(DOCKER_COMP) run --rm $(PHP_FPM) php ./migrations/faker-migrations.php;