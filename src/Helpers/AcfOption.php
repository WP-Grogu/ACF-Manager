<?php

namespace Grogu\Acf\Helpers;

class Option
{
    /**
     * Get an acf option from database, and parde value into a FieldSet it's an array.
     *
     * @return mixed
     */
    public static function get(string $key, bool $format_value = true)
    {
        $data = get_field($key, 'options', $format_value);

        return is_array($data) ? new FieldSet($data) : $data;
    }
}
