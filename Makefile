current_dir = $(shell pwd)

.PHONY: build run test lint-fix analyse

build:
	docker build --rm -t motomod/fee-calculator-php .
	docker run -it --rm -v "$(current_dir)":/usr/src/myapp motomod/fee-calculator-php:latest composer install
run:
	docker run -it --rm -v "$(current_dir)":/usr/src/myapp motomod/fee-calculator-php:latest php index.php $(term) $(amount)
test:
	docker run -it --rm -v "$(current_dir)":/usr/src/myapp motomod/fee-calculator-php:latest composer test
lint-fix:
	docker run -it --rm -v "$(current_dir)":/usr/src/myapp motomod/fee-calculator-php:latest composer lint-fix
analyse:
	docker run -it --rm -v "$(current_dir)":/usr/src/myapp motomod/fee-calculator-php:latest composer analyse
