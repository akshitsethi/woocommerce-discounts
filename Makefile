VERSION := 1.0.1
SLUG := woocommerce-discounts

bin/linux/amd64/github-release:
	wget https://github.com/aktau/github-release/releases/download/v0.7.2/linux-amd64-github-release.tar.bz2
	tar -xvf linux-amd64-github-release.tar.bz2
	chmod +x bin/linux/amd64/github-release
	rm linux-amd64-github-release.tar.bz2

vendor:
	composer install --dev
	composer dump-autoload -a

clover.xml: vendor test

unit: test

test: vendor
	bin/phpunit --coverage-html=./reports

build: vendor
	sed -i "s/@##VERSION##@/${VERSION}/" $(SLUG).php
	mkdir -p build
	rm -rf vendor
	composer install --no-dev
	composer dump-autoload -a

	# Copy files and folders
	cp -ar assets $(SLUG)
	cp -ar i18n $(SLUG)
	cp -ar includes $(SLUG)
	cp -ar vendor $(SLUG)
	cp -ar woocommerce $(SLUG)
	cp license.txt $(SLUG)
	cp README.md $(SLUG)
	cp $(SLUG).php $(SLUG)

	zip -r $(SLUG).zip $(SLUG)
	rm -rf $(SLUG)
	mv $(SLUG).zip build/
	sed -i "s/${VERSION}/@##VERSION##@/" $(SLUG).php

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