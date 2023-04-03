<?php

namespace Grogu\Acf\Transformers;

use OP\Framework\Models\Term;
use Illuminate\Support\Collection;

/**
 * Class EloquentTerms
 */
class EloquentTerms extends Transformer
{
    /**
     * The transformer action to execute.
     *
     * @return mixed
     */
    public function execute()
    {
        $term_ids = $this->value;

        if (!$term_ids || !is_array($term_ids)) {
            return new Collection();
        }

        $term_ids = collect($term_ids)
                        ->filter(fn ($id) => is_string($id) || is_int($id))
                        ->map(fn ($id) => intval($id))
                        ->toArray();

        return !empty($term_ids)
                    ? Term::whereIn('term_id', $term_ids)->orderByRaw(sprintf('FIELD(term_id, %s)', implode(',', $term_ids)))->get()
                    : $this->value;
    }
}
