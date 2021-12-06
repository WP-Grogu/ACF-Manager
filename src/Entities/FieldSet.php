<?php

namespace Grogu\Acf\Entities;

use Closure;
use Grogu\Acf\Core\Config;
use Illuminate\Support\Collection;
use JanPantel\LaravelFluentPlus\FluentPlus;
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
    /**
     * Get the cast definitions
     */
    protected function getCasts()
    {
        $casts = new Collection();
        
        $conf  = Config::getInstance();
        $defs  = $conf->get('acf.casts');

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
}
