<?php

namespace Grogu\Acf\Entities;

use Closure;
use Grogu\Acf\Core\Config;
use Grogu\FluentPlus\FluentPlus;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use Grogu\Acf\Contracts\TransformerContract;
use Grogu\Acf\Exceptions\InvalidTransformerException;

/**
 * A parsed fieldset which comes from ACF.
 * Manage Casts with transformers, and allows fluent class access.
 *
 * @package wp-grogu/acf-manager
 * @author Thomas <thomas@hydrat.agency>
 */
class FieldSet extends FluentPlus
{
    use Macroable;

    /**
     * Get the cast definitions
     */
    protected function getCasts()
    {
        $casts = new Collection();

        $conf  = Config::getInstance();
        $defs  = $conf->get('acf.casts', []);

        foreach ($defs as $field_name => $class) {
            if (!class_exists($class)) {
                throw new InvalidTransformerException(
                    sprintf('Grogu\Acf : The transformer class `%s` does not exists.', $class)
                );
            }
            if (!in_array(TransformerContract::class, class_implements($class))) {
                throw new InvalidTransformerException(
                    sprintf('Grogu\Acf : The transformer class `%s` must implements the `%s` interface.', $class, TransformerContract::class)
                );
            }

            $casts->put($field_name, $this->closure('transform', new $class));
        }

        return $casts->toArray();
    }


    /**
     * Creates a closure from a class instance and a internal method.
     *
     * @return Closure
     */
    protected function closure($function, $object = null)
    {
        return Closure::fromCallable([$object ?: $this, $function]);
    }

    public static function make(array $fields = [])
    {
        return new static($fields);
    }

    public function toCollection()
    {
        return new Collection($this->toArray());
    }
}
