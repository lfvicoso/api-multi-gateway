.PHONY: install up down restart test migrate seed

install:
	docker-compose exec app composer install
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan migrate
	docker-compose exec app php artisan db:seed

up:
	docker-compose up -d

down:
	docker-compose down

restart:
	docker-compose restart

test:
	docker-compose exec app php artisan test

migrate:
	docker-compose exec app php artisan migrate

migrate-fresh:
	docker-compose exec app php artisan migrate:fresh

seed:
	docker-compose exec app php artisan db:seed

migrate-seed:
	docker-compose exec app php artisan migrate:fresh --seed

shell:
	docker-compose exec app bash

log:
	docker-compose logs -f app

