<?php

namespace Grogu\Acf\Entities;

use Grogu\Acf\Contracts\AcfGroupContract;
use WordPlate\Acf\FieldGroup as WordPlateFieldGroup;

/**
 * An ACF Group definition to be registred.
 *
 * @package wp-grogu/acf-manager
 * @author Thomas <thomas@hydrat.agency>
 */
abstract class FieldGroup implements AcfGroupContract
{
    /**
     * The group name to be displayed in back office
     *
     * @var string
     */
    public string $title;

    /**
     * The group style
     *
     * @var string default|seamless
     */
    public string $style = 'default';

    /**
     * The group position
     *
     * @var string normal
     */
    public string $position = 'normal';

    /**
     * The group menu order. The lowest the order is, the upper it appears.
     *
     * @var int
     */
    public int $order = 10;

    /**
     * The hidden items on screen
     *
     * @var array
     */
    public array $hide_on_screen = [
        'the_content',
    ];


    /**
     * Use the group parameters to boot and register the group into ACF.
     *
     * @return self
     */
    public function boot()
    {
        \register_field_group($this->build()->toArray());

        return $this;
    }


    /**
     * Use the group parameters to boot and register the group into ACF.
     *
     * @return WordPlateFieldGroup
     */
    public function build(): WordPlateFieldGroup
    {
        $config = [
            'title'          => $this->title,
            'style'          => $this->style,
            'position'       => $this->position,
            'hide_on_screen' => $this->hide_on_screen,
            'menu_order'     => $this->order,
            'fields'         => $this->fields(),
            'location'       => $this->location(),
        ];

        return new WordPlateFieldGroup($config);
    }


    /**
     * @inherit
     */
    public function location(): array
    {
        return [];
    }


    /**
     * Allow a shortcut method Class::clone() to easily get fields definition to clone them.
     *
     * @return array
     */
    public static function clone(): array
    {
        return (new static())->fields();
    }
}
