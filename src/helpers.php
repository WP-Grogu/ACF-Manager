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
