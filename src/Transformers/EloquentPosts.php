<?php

namespace Grogu\Acf\Transformers;

use OP\Framework\Models\Post;
use Illuminate\Support\Collection;

/**
 * Transform an ACF relation field output (array of IDs) into a Collection of Eloquent Posts.
 *
 * /!\ This requires ObjectPress library.
 *
 * @see http://docs.objectpress.hydrat.agency
 * @package wp-grogu/acf-manager
 * @author Thomas <thomas@hydrat.agency>
 */
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

        $post_ids = collect($post_ids)
                        ->filter(fn ($id) => is_string($id) || is_int($id))
                        ->map(fn ($id) => intval($id))
                        ->toArray();

        return !empty($post_ids)
                    ? Post::published()->ids($post_ids)->get()
                    : $this->value;
    }
}
