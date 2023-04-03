<?php

namespace Grogu\Acf\Transformers;

use OP\Framework\Models\Post;

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
        $post_id = is_array($this->value)
                    ? (array_values($this->value)[0] ?? null)
                    : $this->value;

        return is_string($post_id) || is_int($post_id)
                    ? Post::published()->find(intval($post_id))
                    : $this->value;
    }
}
