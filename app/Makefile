# include variables
include .env

.DEFAULT_GOAL:=help

help: ## Эта справка
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

# tests
run-tests: ## Запуск тестов
	./vendor/bin/phpunit --colors --testdox ./tests/

#
to-work: ## Получить данные из Яндекс.Такси "дом -> работа"
	php cli yandextaxi:get-data --direction=to_work

to-home: ## Получить данные из Яндекс.Такси "работа -> дом"
	php cli yandextaxi:get-data --direction=to_home