init:
	docker-compose up -d --build --remove-orphans
	docker-compose run -d shipmonk-packing-app
	docker-compose exec shipmonk-packing-app composer install
start: stop init
stop:
	docker-compose down
bash:
	docker-compose exec shipmonk-packing-app bash
runSample:
	docker-compose exec shipmonk-packing-app composer runSample
codestyle:
	docker-compose exec shipmonk-packing-app composer codestyle
codestyle-fix:
	docker-compose exec shipmonk-packing-app composer codestyle-fix
cs: codestyle
cs-fix: codestyle-fix
phpstan:
	docker-compose exec shipmonk-packing-app composer phpstan
phpstan-update-baseline:
	docker-compose exec shipmonk-packing-app composer phpstan-update-baseline
test:
	docker-compose exec shipmonk-packing-app composer test
test-coverage:
	docker-compose exec shipmonk-packing-app composer test-coverage
verify: codestyle phpstan test
