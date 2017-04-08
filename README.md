What is this?
=
This is the URL shortener which uses self-written algorithm for shorten the link and sustain time spans to determine shortened link lifespan

The main algorithm is located in src\AppBundle\Utils\Numbers\Converters\BasisConverter.php

Installation
=
Follow steps (skip if prefixed with "#" sign):
- Run from console "git clone git@github.com:bfday/url-shortener.git"
- Fill properly DB conn in "url-shortener\app\config\parameters.yml"
- Go to folder "url-shortener"
- Run from console "bower install \
    && composer install \
    && bin/console doctrine:database:create \
    && bin/console doctrine:schema:update --force \
    && bin/console server:run"
- Open link in browser http://localhost:8000/app_dev.php/shorten
- #bin/console doctrine:generate:entities AppBundle/Entity/Link

How to run tests
=
php phpunit.phar