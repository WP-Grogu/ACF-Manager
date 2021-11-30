<?php

namespace Grogu\Acf;

use Grogu\Acf\Entities\FieldSet;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * A single group of fields.
 *
 * @package wp-grogu/acf-manager
 * @author Thomas <thomas@hydrat.agency>
 */
class AcfGroup
{
    /**
     * The parsed group
     *
     * @var Collection|FieldSet
     */
    protected $group;


    /**
     * The class constructor
     */
    public function __construct(array $fields)
    {
        $this->group = $this->recurse($fields);
    }


    /**
     * Recursively iterate into sub-arrays to parse them into Collection or FieldSet.
     *
     * @return FieldSet|Collection
     */
    protected function recurse(array $values)
    {
        if (Arr::isAssoc($values)) {
            return new FieldSet($values);
        }

        return collect($values)->map(function ($new) {
            if (is_array($new)) {
                return $this->recurse($new);
            }
            return $new;
        });
    }


    /**
     * Get the group
     *
     * @return Collection|FieldSet
     */
    public function get()
    {
        return $this->group;
    }


    /**
     * Shorthand method for function chaining.
     *
     * @return self
     */
    public static function make(array $fields): self
    {
        return new static($fields);
    }
}
