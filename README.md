## Laravel OpenAPI Schema Generator (WIP !)

Automatically generate your Laravel Api project OpenAPI schema.

## Installation

Laravel version >= 5.4 is required.

Require this package with composer using the following command:

```sh
$ composer require rygilles/laravel-openapi-schema-generator
```
Go to your `config/app.php` and add the service provider:

```php
Rygilles\OpenApiGenerator\OpenApiSchemaGeneratorServiceProvider::class,
```

## Publish configuration file

 This package need a configuration file in to work properly.

 ```sh
 $ php artisan vendor:publish --tag=openapischemas
 ```

 After publishing, edit the file located in your Laravel configuration folder in `config/openapischemas.php`.

 @todo : Documentation on config file content

## Usage


To generate your API JSONs schemas, use the `openApiSchemas:generate` artisan command.

@todo : README.md to complete