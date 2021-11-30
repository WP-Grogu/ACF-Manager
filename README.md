# ACF Manager

This librairy helps you manage your ACF Fields Groups, layouts, blocks (gutemberg). 
It uses a fork of [extended-acf](https://github.com/wordplate/extended-acf) on the background.

## Getting started

To get started, grab the library thru composer in your theme directory : 

```
composer require wp-grogu/acf-manager
```

Then, create an `Acf` folder inside your `app/` folder. You should use a PSR autoload logic.  

## Create your groups

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