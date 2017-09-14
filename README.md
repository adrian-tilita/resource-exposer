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

4. Access OPTION ```[base-url]exposure/info``` for information

5. (Optional) - Register Transformers
Create a new file in ```config``` directory named ```expose.php```
Open ```config/expose.php``` and add:
```
<?php
return [
    'transformers' => [
        MyNameSpace\\MyModel::class => MyNameSpace\\MyTransformer::class
    ]
];
```

# Future-releases/Roadmap
- add basic auth for exposed resources
- add configuration for authentication method and credentials
