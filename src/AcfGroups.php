<?php

namespace Grogu\Acf;

use App\Models\Post;
use Illuminate\Support\Arr;
use App\Acf\Helpers\FieldSet;
use Illuminate\Support\Collection;
use \OP\Framework\Factories\ModelFactory;
use OP\Lib\WpEloquent\Model\Contract\WpEloquentPost;

class AcfGroups
{
    protected Collection $fields;
    protected Collection $groups;

    protected array $flexible_names = [
        'components',
    ];

    private function __construct()
    {
    }

    public function buildGroups()
    {
        $arr       = [];
        $flexibles = $this->flexible_names;

        foreach ($this->fields as $key => $data) {
            # Parse flexibles
            if (in_array($key, $flexibles)) {
                $arr[$key] = $this->parseFlexibles($data);
                continue;
            }

            # Parse static blocks
            $names      = explode('-', $key);
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


    public function recurse(array $values)
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


    public function parseFlexibles($flexibles)
    {
        foreach ($flexibles as $index => $flexible) {
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

    public function toJson()
    {
        return $this->groups->map(function ($group) {
            return json_decode(json_encode($group));
        });
    }

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
    public static function fromPost($post = null)
    {
        return static::make($post);
    }


    /**
     * @deprecated
     */
    public static function fromFields(array $fields)
    {
        return static::makeWith($fields);
    }

    /**
     * @return AcfGroups|array
     */
    public static function make($post = null)
    {
        if (!$post || !($post instanceof WpEloquentPost)) {
            $post = $post ? Post::findOrFail($post) : ModelFactory::currentPost();
        }

        if (!$post || !method_exists($post, 'getFields')) {
            return null;
            throw new \Exception('Could\'n find a data source. Please provide a valid post_id.');
        }

        return static::makeWith($post->getFields() ?: []);
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
