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
