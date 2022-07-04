<?php

namespace Grogu\Acf\Helpers;

use Illuminate\Support\Arr;
use Grogu\Acf\Entities\FieldSet;

/**
 * @package wp-grogu/acf-manager
 * @author Thomas <thomas@hydrat.agency>
 */
class AcfOption
{
    /**
     * Get an acf option from database, and parse value into a FieldSet if it's an array.
     *
     * @return mixed
     */
    public static function get(string $key, bool $format_value = true)
    {
        $data = get_field($key, 'options', $format_value);

        if (!is_array($data)) {
            return $data;
        }

        return Arr::isAssoc($data)
                ? new FieldSet($data)
                : collect($data)->map(fn ($i) => new FieldSet($i));
    }
}
