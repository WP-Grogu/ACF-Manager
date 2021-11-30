<?php

namespace Grogu\Acf\Transformers;

use OP\Lib\WpEloquent\Model\Post;

/**
 * Transform an ACF relation field output (array of IDs) into a single Eloquent Post.
 * Only the first value will be taken.
 *
 * /!\ This requires ObjectPress library.
 *
 * @see http://docs.objectpress.hydrat.agency
 * @package wp-grogu/acf-manager
 * @author Thomas <thomas@hydrat.agency>
 */
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
