<?php

namespace Grogu\Acf\Helpers;

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

        return is_array($data) ? new FieldSet($data) : $data;
    }
}
