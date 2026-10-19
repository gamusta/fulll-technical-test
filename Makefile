export UID := $(shell id -u)
export GID := $(shell id -g)

.PHONY: build composer sh up down install test qa

PHP = docker compose run --rm php

DIR ?= algo

build:
	docker compose build

up:
	docker compose up -d --build --wait db
	$(PHP) composer db:schema -d backend

down:
	docker compose down -v

composer:
	$(PHP) composer -d $(DIR) $(c)

sh:
	$(PHP) sh

install:
	$(PHP) composer install -d algo
	$(PHP) composer install -d backend

test:
	$(PHP) composer test -d algo
	$(PHP) composer test -d backend

qa:
	$(PHP) composer qa -d algo
	$(PHP) composer qa -d backend
