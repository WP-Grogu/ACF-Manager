<?php

namespace Grogu\Acf;

use Grogu\Acf\Core\Config;
use Illuminate\Support\Arr;
use Grogu\Acf\Entities\FieldSet;
use Illuminate\Support\Collection;

/**
 * Transforms stored fields to groups, which contains FieldSet.
 *
 * @package wp-grogu/acf-manager
 * @author Thomas <thomas@hydrat.agency>
 */
class AcfGroups
{
    protected Collection $fields;
    protected Collection $groups;

    /**
     * The flexible content fields names.
     */
    protected array $flexible_names;

    /**
     * Prevent external initiation.
     */
    private function __construct()
    {
        $conf  = Config::getInstance();
        $defs  = $conf->get('acf.flexibles', [
            'components',
        ]);

        $this->flexible_names = $defs;
    }

    protected function buildGroups()
    {
        $arr       = [];
        $flexibles = $this->flexible_names;

        foreach ($this->fields as $key => $data) {
            # Parse flexibles
            if (in_array($key, $flexibles) && $data) {
                $arr[$key] = $this->parseFlexibles($data);
                continue;
            }

            # Parse static blocks
            $names      = explode('-', (string) $key);
            $field_name = array_pop($names);
            $group_name = implode('-', $names);

            $key = empty($names) ? $field_name : $group_name;

            if (!array_key_exists($key, $arr)) {
                $arr[$key] = [];
            }

            if (empty($names)) {
                $arr[$field_name] = $data;
            } else {
                $arr[$group_name][$field_name] = $data;
            }
        }

        $this->groups = collect($arr)->map(function ($group, $name) use ($flexibles) {
            if (!in_array($name, $flexibles) && is_array($group)) {
                return $this->recurse($group);
            }
            return $group;
        });
    }

    /**
     * Recursively iterate into fields, looking for arrays.
     * If the array is associative, returns a new FieldSet, else a Collection.
     *
     * @return mixed
     */
    protected function recurse(array $values)
    {
        if (Arr::isAssoc($values)) {
            return new FieldSet($values);
        }

        return collect($values)->map(function ($new) {
            return !is_array($new) ? $new : $this->recurse($new);
        });
    }

    /**
     * Parse flexible content groups.
     * Replace the acf_fc_layout key by __layout key.
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

    /**
     * Get a group by name, or all groups
     *
     * @return mixed|Collection
     */
    public function get(string $key = '', $default = null)
    {
        return $key ? $this->groups->get($key, $default) : $this->groups;
    }

    /**
     * Return the field groups as a JSON string.
     *
     * @return string
     */
    public function toJson()
    {
        // return $this->groups->map(function ($group) {
        //     return json_decode(json_encode($group));
        // })->toJson();
        return $this->groups->toJson();
    }

    /**
     * Return the field groups as array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->groups->toArray();
    }


    /****************************************/
    /*                                      */
    /*              Builders                */
    /*                                      */
    /****************************************/

    /**
     * @deprecated
     */
    public static function fromFields(array $fields)
    {
        return static::makeWith($fields);
    }

    /**
     * Creates an AcfGroups instance from an array of fields
     *
     * @return AcfGroups
     */
    public static function makeWith(array $fields)
    {
        $instance = new static();

        $instance->fields = collect($fields);
        $instance->buildGroups();

        return $instance;
    }
}
