<?php

namespace Grogu\Acf\Transformers;

use OP\Lib\WpEloquent\Model\Post;
use Illuminate\Support\Collection;

class EloquentPosts extends Transformer
{
    /**
     * The transformer action to execute.
     *
     * @return mixed
     */
    public function execute()
    {
        $post_ids = $this->value;

        if (!$post_ids || !is_array($post_ids)) {
            return new Collection();
        }

        return Post::ids($post_ids)->get();
    }
}
