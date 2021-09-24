<?php

use WordPlate\Acf\Fields\Concerns\IsTranslatable;

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
        // App\Acf\Layouts\Templates\FreeSection::class,
        // App\Acf\Layouts\Blocks\Header::class,

        # Options
        // App\Acf\Options\General::class,
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
        // IsTranslatable::WPML_CONFIG_KEY => IsTranslatable::COPY_ONCE,
    ],
    
    
    
    /*
    |--------------------------------------------------------------------------
    | The ACF fields casts (FieldSet)
    |--------------------------------------------------------------------------
    |
    | Here you can set specify which fields should be casted to something.
    | For exemple, you may use the `Grogu\Acf\Transformers\EloquentPost`
    | transformer to cast a Relationnal field into a single post model
    | Please note that your Relationnal field MUST return ids
    |
    */

    'casts' => [
        # Single post model (Eloquent ORM)
        'image'      => Grogu\Acf\Transformers\EloquentPost::class,
        'image_1'    => Grogu\Acf\Transformers\EloquentPost::class,
        'image_2'    => Grogu\Acf\Transformers\EloquentPost::class,
        'image_3'    => Grogu\Acf\Transformers\EloquentPost::class,
        'img'        => Grogu\Acf\Transformers\EloquentPost::class,
        'picto'      => Grogu\Acf\Transformers\EloquentPost::class,
        'post'       => Grogu\Acf\Transformers\EloquentPost::class,
        'page'       => Grogu\Acf\Transformers\EloquentPost::class,
        'product'    => Grogu\Acf\Transformers\EloquentPost::class,

        # Multiple post models (Eloquent ORM, keeps the relationnal field order)
        'images'     => Grogu\Acf\Transformers\EloquentPosts::class,
        'posts'      => Grogu\Acf\Transformers\EloquentPosts::class,
        'mesh_posts' => Grogu\Acf\Transformers\EloquentPosts::class,

        # Video link (parses a class youtube/vimeo link into an array with embed link, video if, platform..)
        'video_link' => Grogu\Acf\Transformers\VideoLink::class,
    ],
];
