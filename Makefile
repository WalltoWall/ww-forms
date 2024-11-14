#!make
include .env

all: clean build/${PLUGIN_NAME}.zip

setup:
	@docker compose exec wordpress /scripts/setup.sh ${ADMIN_USER} ${ADMIN_PASS}

clean:
	rm -rf build/

build/${PLUGIN_NAME}.zip:
	./bin/build.sh ${PLUGIN_NAME}

vite-dev:
	@npx vite build --watch

vite-prod:
	@npx vite build

docker-up:
	@docker compose up -d --wait wordpress && make setup

docker-down:
	@docker compose down

up:
	@make -j4 vite-dev docker-up browsersync

down:
	@make -j4 docker-down
