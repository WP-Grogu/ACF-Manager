<?php

namespace Grogu\Acf\Transformers;

use OP\Framework\Models\Post;

/**
 * Transform an ACF relation field output (array of IDs) into a Collection of Eloquent Posts.
 *
 * /!\ This requires ObjectPress library.
 *
 * @see http://docs.objectpress.hydrat.agency
 * @package wp-grogu/acf-manager
 * @author Thomas <thomas@hydrat.agency>
 */
class EloquentPublishedPosts extends EloquentPosts
{
    /**
     * Query the posts in database.
     * 
     * @return OP\Framework\Models\Post|null
     */
    protected function query(array $post_ids)
    {
        return Post::published()->ids($post_ids)->get();
    }
}
