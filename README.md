[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/adrian-tilita/resource-exposer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/adrian-tilita/resource-exposer/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/adrian-tilita/resource-exposer/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/adrian-tilita/resource-exposer/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/adrian-tilita/resource-exposer/badges/build.png?b=master)](https://scrutinizer-ci.com/g/adrian-tilita/resource-exposer/build-status/master)


# Resource exposure
An automatic Laravel resource (model) API expose system.

# Usage
1. Add library to composer:
```
{
    "require": {
        [..]
        "adrian-tilita/resource-exposer": "dev-develop"
        [..]
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/adrian-tilita/resource-exposer"
        }
    ]
```
2. Run composer update/install in your project
3. Register the provider in our ```config/app.php```
```
<?php

return [
    // [...]
    'providers' => [

        // [...]

        /*
         * Package Service Provider...
         */
        AdrianTilita\ResourceExposer\Provider\ApplicationServiceProvider::class,

        // [...]
    ]
    // [...]
];
```
3. For the first setup, run in CLI:
```
php artisan exposer:search-models
```

4. Configuration

By default the library uses defaults for BasicAuth and has no transformers.
Default BasicAuth credentials: ```foo:bar```

If you wish to extend the configuration, create a new file ```exposer.php``` inside your laravel's config directory.
Example configuration:
```
<?php
return [
    'authorization' => [
        'username' => 'foo',
        'password' => 'bar'
    ],
    'transformers' => [
        MyNameSpace\\MyModel::class => MyNameSpace\\MyTransformer::class
    ]
];
```
