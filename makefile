up:
	@./vendor/bin/sail up -d

down:
	@./vendor/bin/sail down

serve:
	@./vendor/bin/sail artisan serve

start:
	@./vendor/bin/sail up -d
	@./vendor/bin/sail artisan migrate --seed
	@./vendor/bin/sail artisan serve

queue:
	@./vendor/bin/sail artisan queue:work --tries=3

start-fresh:
	@./vendor/bin/sail down
	@./vendor/bin/sail up -d
	@./vendor/bin/sail artisan migrate:fresh --seed
	@./vendor/bin/sail artisan serve

test:
	@./vendor/bin/sail pest
