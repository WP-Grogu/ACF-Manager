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
class EloquentPublishedPost extends EloquentPost
{
    /**
     * Query the post in database.
     * 
     * @return OP\Framework\Models\Post|null
     */
    protected function query(int $post_id)
    {
        return Post::published()->find($post_id);
    }
}
