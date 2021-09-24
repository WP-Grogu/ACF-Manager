<?php

namespace Grogu\Acf;

use Closure;
use JanPantel\LaravelFluentPlus\FluentPlus;
use Grogu\Acf\Exceptions\InvalidTransformerException;
use Grogu\Acf\Contracts\TransformerContract;
use Illuminate\Support\Collection;

class FieldSet extends FluentPlus
{
    /**
     * Get the cast definitions
     */
    protected function getCasts()
    {
        $casts = new Collection();

        # TODO : Get casts from config
        $definitions = [
            'image'      => Grogu\Acf\Transformers\EloquentPost::class,
            'image_1'    => Grogu\Acf\Transformers\EloquentPost::class,
            'image_2'    => Grogu\Acf\Transformers\EloquentPost::class,
            'image_3'    => Grogu\Acf\Transformers\EloquentPost::class,
            'img'        => Grogu\Acf\Transformers\EloquentPost::class,
            'picto'      => Grogu\Acf\Transformers\EloquentPost::class,
            'post'       => Grogu\Acf\Transformers\EloquentPost::class,
            'page'       => Grogu\Acf\Transformers\EloquentPost::class,
            'product'    => Grogu\Acf\Transformers\EloquentPost::class,

            'images'     => Grogu\Acf\Transformers\EloquentPosts::class,
            'posts'      => Grogu\Acf\Transformers\EloquentPosts::class,
            'mesh_posts' => Grogu\Acf\Transformers\EloquentPosts::class,

            'video_link' => Grogu\Acf\Transformers\VideoLink::class,
        ];

        foreach ($definitions as $field_name => $transformer_class) {
            if (!class_exists($transformer_class)) {
                throw new InvalidTransformerException(
                    sprintf('Grogu\Acf : The transformer class `%s` does not exists.', $transformer_class)
                );
            }
            if (!in_array(TransformerContract::class, class_implements($transformer_class))) {
                throw new InvalidTransformerException(
                    sprintf('Grogu\Acf : The transformer class `%s` must implements the `%s` interface.', $transformer_class, TransformerContract::class)
                );
            }

            $casts->put($field_name, $this->closure('transform', $transformer_class));
        }

        return $casts->toArray();
    }


    /**
     * Creates a closure from a methods and a class
     *
     * @return Closure
     */
    protected function closure($function, $object = null)
    {
        return Closure::fromCallable([$object ?: $this, $function]);
    }
}
