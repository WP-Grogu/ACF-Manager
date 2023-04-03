<?php

namespace Grogu\Acf\Transformers;

use OP\Framework\Models\Term;

/**
 * Class EloquentTerm
 */
class EloquentTerm extends Transformer
{
    /**
     * The transformer action to execute.
     *
     * @return mixed
     */
    public function execute()
    {
        $term_id = is_array($this->value)
                    ? (array_values($this->value)[0] ?? null)
                    : $this->value;

        return is_string($term_id) || is_int($term_id)
                    ? Term::find(intval($term_id))
                    : $this->value;
    }
}
