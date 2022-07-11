<?php

namespace Grogu\Acf\Transformers;

use Grogu\Acf\Entities\FieldSet;
use Illuminate\Support\Collection;

/**
 * Transforms a given field into a flexible fluent.
 * This allow us to parse some data to read them easily.
 *
 * @package wp-grogu/acf-manager
 * @author Thomas <thomas@hydrat.agency>
 */
class Flexible extends Transformer
{
    /**
     * The transformer action to execute.
     *
     * @return Grogu\Acf\Entities\FieldSet
     */
    public function execute()
    {
        if (!$this->value) {
            return null;
        }

        return new Collection(
            $this->parseFlexibles($this->value)
        );
    }

    /**
     * Parse flexible content groups.
     * Replace the acf_fc_layout key by __layout key.
     * Adds a __index key to each group.
     * Setup a new FieldSet.
     *
     * @return array
     */
    public function parseFlexibles($flexibles)
    {
        foreach ($flexibles as $index => $flexible) {
            $flexible['__index']  = $index;
            $flexible['__layout'] = $flexible['acf_fc_layout'];
            unset($flexible['acf_fc_layout']);
            $flexibles[$index] = new FieldSet($flexible);
        }

        return $flexibles;
    }
}
