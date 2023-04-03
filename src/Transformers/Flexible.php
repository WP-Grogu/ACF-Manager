<?php

namespace Grogu\Acf\Transformers;

use Illuminate\Support\Arr;
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
        return $this->parseFlexibles($this->value);
    }

    /**
     * Parse flexible content into subsequent FieldSet.
     * Replace the 'acf_fc_layout' key by '__layout'
     * & add '__index' to know layout's position
     *
     * @return array
     */
    public function parseFlexibles($flexibles = [])
    {
        $flexibles = is_array($flexibles) ? $flexibles : [];

        return collect($flexibles)
                    ->values()
                    ->map(
                        fn ($flex, $index) => new FieldSet(
                            array_merge($flex, [
                                '__index'  => $index,
                                '__layout' => Arr::pull($flex, 'acf_fc_layout'),
                            ])
                        )
                    );
    }
}
