SHELL = /bin/bash
ENV ?= development

DC_FILE = -f docker-compose.yml
ifeq ($(ENV),production)
  DC_PROFILES = --profile app --profile production
  ENV_FILE = --env-file ./.env.production
else
  DC_PROFILES = --profile development
  ENV_FILE = --env-file ./.env.development
endif

DC_RUN_ARGS = $(ENV_FILE) $(DC_PROFILES) $(DC_FILE)

HOST_UID=$(shell id -u)
HOST_GID=$(shell id -g)

PACKAGE_DIRS := $(wildcard packages/*/database/migrations)

.PHONY : help migrate up down shell\:app stop-all ps update build restart down-up images\:list images\:clean logs\:app logs containers\:health command\:app
.DEFAULT_GOAL : help

# This will output the help for each task. thanks to https://marmelab.com/blog/2016/02/29/auto-documented-makefile.html
help: ## Show this help
	@printf "\033[33m%s:\033[0m\n" 'Available commands'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z0-9_-]+:.*?## / {printf "  \033[32m%-18s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

migrate: ## Run all migrations
	php artisan migrate
	php artisan migrate --path=/packages/Article/database/migrations
	php artisan migrate --path=/packages/Category/database/migrations
	php artisan migrate --path=/packages/Entry/database/migrations
	php artisan migrate --path=/packages/News/database/migrations
	php artisan migrate --path=/packages/Page/database/migrations
	php artisan migrate --path=/packages/React/database/migrations
	php artisan migrate --path=/packages/Recommend/database/migrations
	php artisan migrate --path=/packages/Search/database/migrations
	php artisan migrate --path=/packages/Tag/database/migrations

seed: ## Run all seeders with fake data
	php artisan db:seed

create-permissions: ## Create all permissions
	php artisan shield:generate --all --option=permissions --panel=admin

up: ## Up containers
	docker compose ${DC_RUN_ARGS} up -d --remove-orphans

logs: ## Tail all containers logs
	docker compose ${DC_RUN_ARGS} logs -f

logs\:app: ## app container logs
	docker compose ${DC_RUN_ARGS} logs app

down: ## Stop containers
	docker compose ${DC_RUN_ARGS} down

down\:with-volumes: ## Stop containers and remove volumes
	docker compose ${DC_RUN_ARGS} down -v

shell\:app: ## Start shell into app container
	docker compose ${DC_RUN_ARGS} exec app sh

command\:app: ## Run a command in the app container
	docker compose ${DC_RUN_ARGS} exec app sh -c "$(command)"

stop-all: ## Stop all containers
	docker stop $(shell docker ps -a -q)

ps: ## Containers status
	docker compose ${DC_RUN_ARGS} ps

build: ## Build images
	docker compose ${DC_RUN_ARGS} build

update: ## Update containers
	docker compose ${DC_RUN_ARGS} up -d --no-deps --build --remove-orphans

restart: ## Restart all containers
	docker compose ${DC_RUN_ARGS} restart

down-up: down up ## Down all containers, then up

images\:list: ## Sort Docker images by size
	docker images --format "{{.ID}}\t{{.Size}}\t{{.Repository}}" | sort -k 2 -h

images\:clean: ## Remove all dangling images and images not referenced by any container
	docker image prune -a

containers\:health: ## Check all containers health
	docker compose ${DC_RUN_ARGS} ps --format "table {{.Name}}\t{{.Service}}\t{{.Status}}"