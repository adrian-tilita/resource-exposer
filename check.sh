echo "Checking PSR for tests"
php vendor/bin/phpcs --standard=PSR2 tests/ --colors

echo "Checking PSR for tests"
php vendor/bin/phpcs --standard=PSR2 src/ --colors

echo "Checking MD for src"
php vendor/bin/phpmd src/ text phpmd.xml

echo "Run tests"
php vendor/bin/phpunit
