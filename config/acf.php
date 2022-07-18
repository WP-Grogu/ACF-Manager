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
        // App\Acf\Layouts\SinglePost::class,
        // App\Acf\Layouts\Templates\FlexibleSection::class,

        # Options
        // App\Acf\Options\General::class,
    ],


    /*
    |--------------------------------------------------------------------------
    | The registred ACF Gutemberg blocks
    |--------------------------------------------------------------------------
    |
    | Here you can set your ACF Gutemberg blocks.
    |
    */

    'blocks' => [
        // App\Acf\Gutemberg\Numbers::class,
    ],


    /*
    |--------------------------------------------------------------------------
    | The ACF groups configuration defaults
    |--------------------------------------------------------------------------
    |
    | Here you can set ACF fields configuration defaults, which will be applied
    | automatically to each fields if you didn't explicitly set a config
    | value for the concerned field.
    |
    */

    'defaults' => [
        // 'wpml_cf_preferences' => 3, // copy once
    ],


    /*
    |--------------------------------------------------------------------------
    | The ACF Flexible field names
    |--------------------------------------------------------------------------
    |
    | Here you can set your flexible field names so they
    | get recursively parsed into FieldSet classes.
    |
    | @deprecated use the Flexible transformer instead.
    |
    */

    'flexibles' => [],


    /*
    |--------------------------------------------------------------------------
    | The ACF fields casts (FieldSet)
    |--------------------------------------------------------------------------
    |
    | Here you can set specify which fields should be casted to something.
    | For exemple, you may use the `Grogu\Acf\Transformers\EloquentPost`
    | transformer to cast a relationnal field into a single post model
    | Please note that in that case, your relationnal field MUST return ids.
    |
    */

    'casts' => [
        # Flexibles
        'components' => Grogu\Acf\Transformers\Flexible::class,

        # Single post model (Eloquent ORM)
        'post'       => Grogu\Acf\Transformers\EloquentPost::class,
        'page'       => Grogu\Acf\Transformers\EloquentPost::class,
        'product'    => Grogu\Acf\Transformers\EloquentPost::class,
        'image'      => Grogu\Acf\Transformers\EloquentPost::class,
        'picto'      => Grogu\Acf\Transformers\EloquentPost::class,

        # Multiple post models (Eloquent ORM, keeps the relationnal field order)
        'posts'      => Grogu\Acf\Transformers\EloquentPosts::class,

        # Video link (parses a classic YouTube/Vimeo link into an array with embed link, video id, platform name)
        'video_link' => Grogu\Acf\Transformers\VideoLink::class,

        # Theme casts..
        // 'field_name' => App\Acf\Transformers\MyField::class,
    ],


    /*
    |--------------------------------------------------------------------------
    | Gutemberg configuration
    |--------------------------------------------------------------------------
    |
    | Here you can set your Gutemberg configuration.
    | You may specify a stylesheet & define categories.
    |
    */

    'gutemberg' => [
        'categories' => [
            // 'theme-blocks' => 'Theme Blocks',
        ],

        'stylesheets' => [
            // '*'    => 'dist/css/editor.css',
            // 'post' => 'dist/css/editor-post.css',
        ],
    ],
];
