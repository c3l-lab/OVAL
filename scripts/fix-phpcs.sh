for var in "$@"
do
    ./vendor/bin/php-cs-fixer fix $var
done
