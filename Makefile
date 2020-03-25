VERSION := 1.0.0
SLUG := woocommerce-discounts

bin/linux/amd64/github-release:
	wget https://github.com/aktau/github-release/releases/download/v0.7.2/linux-amd64-github-release.tar.bz2
	tar -xvf linux-amd64-github-release.tar.bz2
	chmod +x bin/linux/amd64/github-release
	rm linux-amd64-github-release.tar.bz2

vendor: composer install --dev
	composer dump-autoload -a

clover.xml: vendor test

unit: test

test: vendor
	bin/phpunit --coverage-html=./reports

release:
	git stash
	git fetch -p
	git checkout master
	git pull -r
	git tag v$(VERSION)
	git push origin v$(VERSION)
	git pull -r

fmt: ensure
	bin/phpcbf --standard=WordPress src --ignore=src/vendor
	bin/phpcbf --standard=WordPress tests --ignore=vendor

lint: ensure
	bin/phpcs --standard=WordPress src --ignore=src/vendor
	bin/phpcs --standard=WordPress tests --ignore=vendor

psr:
	composer dump-autoload -a

i18n:
	wp i18n make-pot ./ i18n/languages/$(SLUG).pot

cover: vendor
	bin/coverage-check clover.xml 100

clean:
	rm -rf vendor/