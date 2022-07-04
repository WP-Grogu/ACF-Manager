<?php

if (!function_exists('fieldset')) :
    /**
     * Builds a new FieldSet from a given array.
     *
     * @return \Grogu\Acf\Entities\FieldSet
     */
    function fieldset(array $fields = [])
    {
        return new \Grogu\Acf\Entities\FieldSet($fields);
    }
endif;

if (!function_exists('option_fieldset')) :
    /**
     * Parse the given option into corresponding data.
     *
     * @return mixed
     */
    function fieldset(string $key, bool $format_value = true)
    {
        return \Grogu\Acf\Helpers\AcfOption::get($key, $format_value);
    }
endif;
