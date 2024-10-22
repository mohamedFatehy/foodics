up:
	@./vendor/bin/sail up -d

down:
	@./vendor/bin/sail down

start:
	@./vendor/bin/sail up -d
	@./vendor/bin/sail artisan migrate --seed
	@./vendor/bin/sail artisan serve

queue:
	@./vendor/bin/sail artisan queue:work --tries=3

reset:
	@./vendor/bin/sail artisan migrate:fresh --seed
	@./vendor/bin/sail artisan serve
