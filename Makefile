# https://docs.docker.com/engine/reference/run/

ROOT_DIR:=$(shell dirname $(realpath $(lastword $(MAKEFILE_LIST))))

include $(ROOT_DIR)/.env

.PHONY: help dependencies up start stop restart status ps clean
.DEFAULT_GOAL:=help

help: ## This help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

up: ## Start all or c=<name> containers in foreground
	@$(DOCKER_COMPOSE) -f $(DOCKER_COMPOSE_FILE) up $(c)

start: ## Start all or c=<name> containers in background
	@$(DOCKER_COMPOSE) -f $(DOCKER_COMPOSE_FILE) up -d $(c)

start-b: ## Start all or c=<name> containers in background with --build
	@$(DOCKER_COMPOSE) -f $(DOCKER_COMPOSE_FILE) up --build -d $(c)

stop: ## Stop all or c=<name> containers
	@$(DOCKER_COMPOSE) -f $(DOCKER_COMPOSE_FILE) stop $(c)

restart: ## Restart all or c=<name> containers
	@$(DOCKER_COMPOSE) -f $(DOCKER_COMPOSE_FILE) stop $(c)
	@$(DOCKER_COMPOSE) -f $(DOCKER_COMPOSE_FILE) up $(c) -d

logs: ## Show logs for all or c=<name> containers
	@$(DOCKER_COMPOSE) -f $(DOCKER_COMPOSE_FILE) logs --tail=100 -f $(c)

status: ## Show status of containers
	@$(DOCKER_COMPOSE) -f $(DOCKER_COMPOSE_FILE) ps

ps: status ## Alias of status

down: ## Stops containers and removes containers, networks, volumes, and images created by up
	@$(DOCKER_COMPOSE) -f $(DOCKER_COMPOSE_FILE) down

console: ## Enter the c=<name> container
	@$(DOCKER_COMPOSE) exec $(c) bash -l

system: ## Show docker disk usage
	@$(DOCKER) system df --verbose

stats: ## Display a live stream of container(s) resource usage statistics
	@$(DOCKER) stats

composer: ## Run composer and exec command c=<command>
	@$(DOCKER) run --rm --interactive --tty --volume ${PWD}/app:$(WORKDIR) --workdir $(WORKDIR) composer composer $(c)