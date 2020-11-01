# include variables
include .env

# tests
run-tests:
	./vendor/bin/phpunit --colors --testdox ./tests/

#
to-work:
	php cli yandextaxi:get-data --direction=to_work

to-home:
	php cli yandextaxi:get-data --direction=to_home