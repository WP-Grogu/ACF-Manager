# Grogu ACF Manager

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/wp-grogu/acf-manager.svg?style=flat-square)](https://packagist.org/packages/wp-grogu/acf-manager)
[![Total Downloads](https://img.shields.io/packagist/dt/wp-grogu/acf-manager.svg?style=flat-square)](https://packagist.org/packages/wp-grogu/acf-manager)

- [Introduction](#introduction)
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

This package brings an object-oriented approach to Wordpress Advanced Custom Fields plugin. It helps you create field groups, gutemberg blocks, options pages and flexible content very easily directly in PHP Classes, and keep a structured app folder. Using this package will make you stop using the ACF interface, and allow you to git your ACF fields and make it a breeze to push your changes to multiple environments.

In addition to that, you also receive back a `FieldSet` class when retreiving your fields from the database, which enable field parsing and allow you to cast fields into Models, classes, or any other transformed data based on the field name (eg. a field named "image" becomes an `Attachment` model with all the corresponding methods).

Behing the scene, acf-manager uses a fork of `wordplate/extended-acf` package, coming with an explicit documentation, so make sure to checkout [the official repositiory](https://github.com/wordplate/extended-acf) to see which fields you may create.

To make use of Eloquent Models in your app if not included in your framework, we highly recommand having a look at the [ObjectPress](https://gitlab.com/tgeorgel/object-press) library which brings some of the best Laravel features in any Wordpress installation. 

This package is compatible with [Bedrock/Sage 10](https://roots.io/sage/) stack, or even with a native Wordpress theme with autoload logic.

At it's most basic usage, the plugin may be used this way to create a field group :  

```php
<?php

namespace App\Acf\Groups;

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
    | Register here your ACF groups. Please note that only groups with
    | at least a location needs to be registred (eg: don't need to
    | register groups being cloned in other group or flexible)
    |
    */

    'groups' => [
        App\Acf\Groups\Header::class,
    ],

    [...]
```

It's also very easy to build a beautiful Free Section using Flexible content powers :  

```php
<?php

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
// views/header.blade.php

<div class="bloc-header-home">
    <div class="main-text">{{ $fields->title }}</div>
    <img src="{{ $fields['image']['url'] }}" alt="{{ $fields->get('image')->alt }}">
</div>
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
    - resources
    - config
    - app
        - Helpers
        - Providers
        - Api
        - Models
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

3. Create the `config/acf.php` file which will hold your configuration using the wp CLI :
```bash
wp grogu-acf make:config
```

Or copy/paste it directly from the [source file](https://github.com/WP-Grogu/ACF-Manager/blob/main/config/acf.php).


> 🚀 You should be using a PSR-4 autoload logic to make the best out of this module.


<a name="usage"></a>

<a name="usage-create-field-group"></a>

## Usage

Every field group or gutemberg block has it's own file and individual class. Each of them has fields, obviously, and may have one or more location(s). Those are defined using the great [wordplate/extended-acf](https://github.com/wordplate/extended-acf) package, which provides a fluent API wrapping up ACF fields definitions.


### Create a field group

To create a group, run the folowing WP Cli command : 

```bash
wp grogu-acf make:group GroupName
```

You may also specify a sub directory : 

```bash
wp grogu-acf make:group GroupName --templates
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
     * The group name to be displayed in back office.
     *
     * @var string
     */
    public string $title;
    
    /**
     * The group slug to be used when transformed into a flexible layout.
     *
     * @var string
     */
    public string $slug = '';

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
'groups' => [
    App\Acf\GroupName::class,
],
```

You may also combine fields from another group using the PHP splat operator : 

```php
/**
 * The group fields definition.
 *
 * @return array
 */
public function fields(): array
{
    return [
        ...App\Acf\Groups\Header::clone(),

        Text::make('Headline', 'headline'),
    ];
}
```

<a name="usage-create-gutemberg-block"></a>

### Create a Gutemberg block

// WIP

<a name="usage-flexible-content"></a>

### Working with flexible content

Because your fields are defined in individual classes, it becomes really easy to copy your fields from one group to another, or transform them into layouts. For example, let's assume you are defining each of your flexible layouts as field groups, inside a `app/Acf/Groups` directory :

```php
<?php

namespace App\Acf\Groups;

use Grogu\Acf\Entities\FieldGroup;
use WordPlate\Acf\Fields\WysiwygEditor;

class Wysiwyg extends FieldGroup
{
    /**
     * The group name to be displayed in back office. Required.
     *
     * @var string
     */
    public string $title = 'Wysiwyg';
    
    /**
     * The group slug to be used when transformed into a flexible layout.
     *
     * @var string
     */
    public string $slug = 'wysiwyg';

    /**
     * The group fields definition.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            WysiwygEditor::make('Text')
                ->toolbar('simple')
                ->mediaUpload(false),
        ];
    }
}
```

You can now add this group as a layout inside your flexible content : 

```php
<?php

namespace App\Acf\Templates;

use App\Acf\Blocks;
use WordPlate\Acf\Location;
use Grogu\Acf\Entities\FieldGroup;
use WordPlate\Acf\Fields\FlexibleContent;

class FreeSection extends FieldGroup
{
    /**
     * The group name to be displayed in back office
     *
     * @var string
     */
    public string $title = 'Free section';

    /**
     * The fields configuration
     *
     * @return array
     */
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
                        Groups\Wysiwyg::make()->toLayout(),
                    ]),
        ];
    }

    /**
     * The location configuration
     *
     * @return array
     */
    public function location(): array
    {
        return [
            Location::where('page_template', 'template-freesection.blade.php'),
        ];
    }
}
```

Make sure that the `App\Acf\Templates\FreeSection` group is registred inside your acf config file, *et voilà !*

Behind the scene, the field groups `$title` and `$slug` properties are used to define the layout. However, if you need more control on the field registration, you can manually define your layout : 

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