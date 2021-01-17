phpcs:
	vendor/bin/phpcs --cache=./.phpcs.cache.php --standard=phpcs.xml -sp

phpcbf:
	vendor/bin/phpcbf --standard=phpcs.xml -p

phpstan:
	vendor/bin/phpstan analyse --level 5 --configuration phpstan.neon src

rector:
	vendor/bin/rector process src
