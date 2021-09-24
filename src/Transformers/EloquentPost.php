<?php

namespace Grogu\Acf\Transformers;

use OP\Lib\WpEloquent\Model\Post;

class EloquentPost extends Transformer
{
    /**
     * The transformer action to execute.
     *
     * @return mixed
     */
    public function execute()
    {
        $post_id = $this->value;

        if (is_array($post_id)) {
            $post_id = collect($post_id)->first();
        }

        return Post::find($post_id);
    }
}
