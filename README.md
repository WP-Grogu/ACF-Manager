# Grogu ACF Manager

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/wp-grogu/acf-manager.svg?style=flat-square)](https://packagist.org/packages/wp-grogu/acf-manager)
[![Total Downloads](https://img.shields.io/packagist/dt/wp-grogu/acf-manager.svg?style=flat-square)](https://packagist.org/packages/wp-grogu/acf-manager)

- [Introduction](#introduction)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
    - [Create a field group](#usage-create-field-group)
    - [Create a Gutemberg block](#usage-create-gutemberg-block)
    - [Working with flexible content](#usage-flexible-content)
    - [Retreiving your fields (FieldSet class)](#usage-retreiving-fields)
    - [Field casting](#usage-casting)
- [Contribute](#contribute)

<a name="introduction"></a>

## Introduction

This package brings an object-oriented approach to Wordpress Advanced Custom Fields (ACF) plugin. ACF manager will help you to create field groups, gutemberg blocks, options pages and flexible content directly in individual PHP classes, so you keep a clean and structured app folder. Using this package will make forget about the ACF back-office interface, allowing you to version control your ACF groups and make it a *breeze* to push your changes to multiple environments.

In addition to thoses (awesome) features, you will also receive back `FieldSet` objects instead of arrays when retreiving your fields from the database, which enables fields casting into Models, classes, or any other transformed data based on the field name (eg. a field named "image" may become an `App\Models\Attachment` object, with all the corresponding attributes and methods).

Behing the scene, acf-manager uses a custom fork of `wordplate/extended-acf` package, coming with an explicit documentation, so make sure to checkout [the official repositiory](https://github.com/wordplate/extended-acf) to discover how to define fields.

To make use of Eloquent Models (and corresponding builts-in transformers) in your app, if not already included in your framework, we highly recommand having a look at the [ObjectPress](https://gitlab.com/tgeorgel/object-press) library which brings some of the best Laravel features in any Wordpress installation. 

This package comes as a standalone but is fully compatible with [Bedrock/Sage 10](https://roots.io/sage/) stack, with a native Wordpress theme (with an autoload logic setted up), and probably with many other frameworks out here.


## Basic usage  

At it's most basic usage, the plugin may be used this way to create a field group :  

```php
# Define field group
class Header extends \Grogu\Acf\Entities\FieldGroup
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

# Boot field group
add_action('acf/init', fn () => Header::make()->boot());
```

This package has a config file to manage your blocks registration without the need to use wordpress hooks :  

```php
return [

    /*
    |--------------------------------------------------------------------------
    | The registred ACF field groups & layouts
    |--------------------------------------------------------------------------
    |
    | Register here your ACF groups. Please note that only groups with
    | at least a location needs to be registred (eg: don't need to
    | register groups being cloned in other group or flexible)
    |
    */

    'groups' => [
        App\Acf\Header::class,
    ],

    [...]
];
```

It's also a breeze to create Flexible content sections :  

```php
use Grogu\Acf\Entities\FieldGroup;

class FreeSection extends FieldGroup
{
    public function fields(): array
    {
        return [
            FlexibleContent::make('Components')
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

    [...]
}
```

Finally, when receiving back your fields, you may parse them into Field sets to benefit from casting and fluent interface allowing multiple accessors on the class : 

```php
<?php

use Grogu\Acf\Entities\FieldSet;

$fields = FieldSet::make(get_field('header', $post_id));

?>

// views/header.blade.php
<div class="bloc-header-home">
    <div class="main-text">{{ $fields->title }}</div>
    <img src="{{ $fields->image->url }}" alt="{{ $fields->image->alt }}">
</div>
```

The `FieldSet` class is completely fluent and all of those methods are valid to get values :

```php
$fields = FieldSet::make([
    'sub' => [
        'field' => 'foo',
    ],
]);

$fields->sub->field
$fields->sub?->field
$fields['sub']['field']
$fields->get('sub.field')
```

Ready to get started ?

<a name="requirements"></a>

## Requirements

ACF manager has some requirements : 
  - PHP `>= 8.0`
  - Wordpress
  - ACF plugin enabled


<a name="installation"></a>

## Installation

1. Use composer to install the package in your theme directory :  

```bash
composer require wp-grogu/acf-manager
```

2. Create an `Acf` namespace inside your autoloaded `app/` folder, which may look like this :

```
- theme
    - config
    - app
        - Acf
            - Groups
            - Blocks
            - Templates
            - Options
        [...]
    [...]
```

4. Bootload the module in your `functions.php` file :

```php

[...]
/*
|--------------------------------------------------------------------------
| Load Grogu ACF Manager
|--------------------------------------------------------------------------
|
| Grogu ACF Manager module helps us create and retreive ACF field groups.
| This line starts the engine, loads the config/acf.php config file,
| and register our field groups, gutemberg blocks and layouts.
|
*/

new Grogu\Acf\Core\Bootloader;
```

5. Create the `config/acf.php` file which will hold your configuration using WP-CLI :
```bash
wp grogu-acf install:config
```

Or copy/paste it directly from the [source file](https://github.com/WP-Grogu/ACF-Manager/blob/main/config/acf.php).


<a name="usage"></a>

<a name="usage-create-field-group"></a>

## Usage

Every field group or gutemberg block has it's own individual class/file. Each of them has, obviously, some fields, and some may also have one or more locations. All of them are defined using the great [wordplate/extended-acf](https://github.com/wordplate/extended-acf) package, which provides a fluent API around ACF.


### Create a field group

To create a group, run the following WP-CLI command : 

```bash
wp grogu-acf make:group GroupName
wp grogu-acf make:group GroupName --in:Templates # specify subdir
```

A new file is created :

```php
<?php

namespace App\Acf;

use Grogu\Acf\Entities\FieldGroup;
use WordPlate\Acf\Location;

class GroupName extends FieldGroup
{
    /**
     * The group name to be displayed in back office. Required.
     *
     * @var string
     */
    public string $title = 'GroupName';
    
    /**
     * The group slug to be used when transformed into a flexible layout.
     *
     * @var string
     */
    public string $slug = 'group-name';

    /**
     * The group fields definition.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            //
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
            // Location::where('post_type', 'page'),
        ];
    }
}
```

You may manage some additionnal configurations for the group using class properties : 

```php
class GroupName extends FieldGroup
{
    [...]

    /**
     * The group style.
     *
     * @var string default|seamless
     */
    public string $style = 'default';

    /**
     * The group position.
     *
     * @var string normal
     */
    public string $position = 'normal';

    /**
     * The group menu order. Lowest value appears first.
     *
     * @var int
     */
    public int $order = 10;

    /**
     * The hidden items on screen
     *
     * @var array
     */
    public array $hide_on_screen = [
        'the_content',
    ];
}
```

Once your fields and locations are defined, you may register your new group in the `config/acf.php` config file, using the `groups` key :

```php
[...]

'groups' => [
    App\Acf\GroupName::class,
],

```

You may also combine fields from another group using the PHP splat operator : 

```php
public function fields(): array
{
    return [
        ...Groups\Header::clone(),

        Text::make('Headline', 'headline'),
    ];
}
```

<a name="usage-create-gutemberg-block"></a>

### Create a Gutemberg block

// WIP

<a name="usage-flexible-content"></a>

### Working with flexible content

Because your fields are defined in individual classes, it becomes really easy to copy your fields from one group to another, or transform them into layouts. For this example, we will assume you are defining each of your flexible layouts as separate field groups, inside a `app/Acf/Groups` directory. You would define your Flexible and it's layouts this way :  

```php
namespace App\Acf\Templates;

use App\Acf\Groups;

class FreeSection extends FieldGroup
{
    public string $title = 'Free section';

    public function fields(): array
    {
        return [
            FlexibleContent::make('Components', 'components')
                    ->thumbnails(true)
                    ->modal([
                        'enabled' => true,
                        'col'     => '4',
                    ])
                    ->layouts([
                        Groups\TextImage::make()->toLayout(),
                    ]),
        ];
    }

    public function location(): array
    {
        return [
            Location::where('page_template', 'template-freesection.blade.php'),
        ];
    }
}
```

Make sure that the `App\Acf\Templates\FreeSection` group is registred inside your acf config file, *et voilà !*

Behind the scene, the layout's FieldGroup `$title` and `$slug` properties are used to define the layout. However, if you need more control on the field registration, you can manually define your layout : 

```php
use WordPlate\Acf\Fields\Layout;

public function fields(): array
{
    return [
        FlexibleContent::make('Components', 'components')
            ->layouts([
                Layout::make('WYSIWYG section', 'wysiwyg-layout')
                    ->layout('block')
                    ->fields(
                        Groups\Wysiwyg::clone()
                    ),
                
                Layout::make('WYSIWYG section (grey background)', 'wysiwyg-grey-layout')
                    ->layout('block')
                    ->fields(
                        Groups\Wysiwyg::clone()
                    ),
            ]),
    ];
}
```


<a name="usage-retreiving-fields"></a>

### Retreiving your fields (FieldSet class)

// WIP

<a name="usage-casting"></a>

### Field casting

// WIP


<a name="contribute"></a>

## Contribute

Feel free to contribute ! You may send merge request.