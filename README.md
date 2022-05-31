# Grogu ACF Manager

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/wp-grogu/acf-manager.svg?style=flat-square)](https://packagist.org/packages/wp-grogu/acf-manager)
[![Total Downloads](https://img.shields.io/packagist/dt/wp-grogu/acf-manager.svg?style=flat-square)](https://packagist.org/packages/wp-grogu/acf-manager)

- [Introduction](#introduction)
- [Installation](#installation)
- [Usage](#usage)
    - [Create a field group](#usage-create-field-group)
- [Contribute](#contribute)

<a name="introduction"></a>

## Introduction

This package brings an object-oriented approach to Wordpress Advanced Custom Fields plugin. It helps you create field groups, gutemberg blocks, options pages and flexible content very easily and keep a structured app folder.

You also receive back a `FieldSet` class when retreiving your fields from the database, which enable field parsing and casting into whatever you need.

Behing the scene, acf-manager uses a fork of `wordplate/extended-acf` package, coming with an explicit documentation, so make sure to checkout [the official repositiory](https://github.com/wordplate/extended-acf) to see which fields you may create.

At it's most basic usage, the plugin may be used this way to create a field group :  

```php
<?php

use Grogu\Acf\Entities\FieldGroup;

class Header extends FieldGroup
{
    public function fields(): array
    {
        return [
            Text::make('Titre', 'title')
                ->required(),
            Image::make('Image')
                ->previewSize('medium')
                ->returnFormat('id')
                ->required(),
        ];
    }

    public function location(): array
    {
        return [
            Location::where('post_type', 'page'),
        ];
    }
}

add_action(
    'acf/init', 
    fn () => Header::make()->boot()
);
```

However, this package make use of config files to manage your blocks registragion without the need to use hooks :  

```php
// config/acf.php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | The registred ACF field groups & layouts
    |--------------------------------------------------------------------------
    |
    | Here you can set your ACF fields. Please note that flexible content groups
    | doesn't need to get registred this way, you only need to register the
    | flexible content group as you're cloning the group fields inside.
    |
    */

    'groups' => [
        # Layouts
        App\Acf\Header::class,
    ],

    [...]
```

It's also very easy to build a beautiful Free Section using Flexible content powers :  

```php
<?php

use Grogu\Acf\Entities\FieldGroup;

class FreeSection extends FieldGroup
{
    /**
     * @var string
     */
    public string $title = 'Section libre';

    /**
     * @var int
     */
    public int $order = 15;

    /**
     * The fields configuration
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            FlexibleContent::make('Composants', 'components')
                    ->buttonLabel('Add a new section')
                    ->thumbnails(true)
                    ->modal([
                        'enabled' => true,
                        'col'     => '4',
                    ])
                    ->layouts([
                        Blocks\Header::make()->toLayout(),
                        Blocks\Services::make()->toLayout(),
                        Blocks\Worldwide::make()->toLayout(),
                    ]),
        ];
    }
```

Ready to get started ? Set.. go !

<a name="installation"></a>

## Installation

1. Use composer to install the package in your theme directory :  

```bash
composer require wp-grogu/acf-manager
```

2. Create an `Acf` namespace inside your autoloaded `app/` folder, which may look like this :

```
- theme
    - app
        - Acf
            - Groups
            - Blocks
            - Templates
            - Options
        - Helpers
        - Models
        [...]
```

3. Create the `config/acf.php` file which will hold your configuration using the wp CLI
```bash
wp grogu-acf make:config
```

4. Enjoy.

> You should be using a PSR-4 autoload logic.


<a name="usage"></a>

## Usage

<a name="usage-create-field-group"></a>

## Create a field group

Groups are represented by classes. Each Field Groups has fields and location(s).  
Fields and locations are using the great `wordplate/extended-acf` package, coming with a explicit documentation, so make sure to checkout [the official repositiory](https://github.com/wordplate/extended-acf).

### Simple field group definition

To create a group, simply create a file inside the `app/Acf` directory. For example, let's create a `Header` field group.



```php
<?php /* app/Acf/Header.php */

namespace App\Acf;

use Grogu\Acf\Entities\FieldGroup;
use WordPlate\Acf\Location;
use WordPlate\Acf\Fields\Text;
use WordPlate\Acf\Fields\Image;

class Header extends FieldGroup
{
    /**
     * The group name to be displayed in back office. Required.
     *
     * @var string
     */
    public string $title = 'Header';

    /**
     * The group fields definition.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            Text::make('Titre', 'title')
                ->required(),
            Image::make('Image')
                ->previewSize('medium')
                ->returnFormat('id')
                ->required(),
        ];
    }

    /**
     * The group location configuration.
     *
     * @return array
     */
    public function location(): array
    {
        return [
            Location::if('post_type', 'page'),
            Location::if('post_type', 'post'),
        ];
    }
}
```

Then, add your class inside the `config/acf.php` config file.

```php
'groups' => [
    App\Acf\Header::class,
],
```

### Flexible field group definition
### Gutemberg block field group definition

## Register the groups into ACF/Wordpress

### Using ObjectPress
### Manually
## Retreive your groups

### The Fieldset class
### Casting returned values using Transformers

#### Using transformers
#### Creating transformers