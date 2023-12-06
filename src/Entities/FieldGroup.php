<?php

namespace Grogu\Acf\Entities;

use WordPlate\Acf\Fields\Layout;
use Grogu\Acf\Contracts\AcfGroupContract;
use Illuminate\Support\Traits\Macroable;

/**
 * An ACF Group definition to be registred.
 *
 * @package wp-grogu/acf-manager
 * @author Thomas <thomas@hydrat.agency>
 */
abstract class FieldGroup implements AcfGroupContract
{
    use Macroable;

    /**
     * The group name to be displayed in back office.
     *
     * @var string
     */
    public string $title;

    /**
     * The group slug to be used when transformed into a flexible layout.
     *
     * @var string
     */
    public string $slug = '';

    /**
     * The group style.
     *
     * @var string default|seamless
     */
    public string $style = 'default';

    /**
     * The group position.
     *
     * @var string normal|side|acf_after_title
     */
    public string $position = 'normal';

    /**
     * The group menu order. Lowest value appears first.
     *
     * @var int
     */
    public int $order = 10;

    /**
     * When used as a Layout, configure the minimum amount of time this layout must be used.
     *
     * @var int
     */
    public ?int $minOccursLayout = null;

    /**
     * When used as a Layout, configure the maximum amount of time this layout must be used.
     *
     * @var int
     */
    public ?int $maxOccursLayout = null;

    /**
     * The hidden items on screen
     *
     * @var array
     */
    public array $hide_on_screen = [
        'the_content',
    ];

    /**
     * Allow overriding groups properties to easily extend groups.
     *
     * @param  string  $title           Optional.
     * @param  string  $slug            Optional.
     * @param  array   $hide_on_screen  Optional.
     */
    public function __construct(?string $title = null, ?string $slug = null, ?array $hide_on_screen = null)
    {
        if ($title) {
            $this->title = $title;
        }
        if ($slug) {
            $this->slug = $slug;
        }
        if ($hide_on_screen) {
            $this->hide_on_screen = $hide_on_screen;
        }
    }

    /**
     * Use the group parameters to boot and register the group into ACF.
     *
     * @return static
     */
    public function boot()
    {
        \register_extended_field_group(
            $this->build()
        );

        return $this;
    }

    /**
     * Build the field group parameters array in order to register it.
     *
     * @return array
     */
    protected function build(): array
    {
        return [
            'title'          => $this->title,
            'style'          => $this->style,
            'position'       => $this->position,
            'hide_on_screen' => $this->hide_on_screen,
            'menu_order'     => $this->order,
            'fields'         => $this->fields(),
            'location'       => $this->location(),
        ];
    }

    /**
     * @inherit
     */
    public function location(): array
    {
        return [];
    }

    /**
     * Transform the FieldGroup into a Layout instance, to use in flexible content.
     * You may override the slug using this class slug property.
     *
     * @param string $layout block, row or table
     * @return \WordPlate\Acf\Fields\Layout
     */
    public function toLayout(string $layout = 'block', ?int $min = null, ?int $max = null)
    {
        $layout = Layout::make($this->title, $this->slug ?: null)
                        ->layout($layout)
                        ->when($this->minOccursLayout, fn ($layout, $min) => $layout->min($min))
                        ->when($this->maxOccursLayout, fn ($layout, $max) => $layout->max($max))
                        ->fields(
                            $this->fields()
                        );

        return $layout;
    }

    /**
     * Static method to create a new instance, allowing method chaining.
     *
     * @param  string  $title           Optional.
     * @param  string  $slug            Optional.
     * @param  array   $hide_on_screen  Optional.
     *
     * @return self
     */
    public static function make(?string $title = null, ?string $slug = null, ?array $hide_on_screen = null)
    {
        return new static($title, $slug, $hide_on_screen);
    }

    /**
     * Allow a shortcut method Class::clone() to easily get fields definition to clone them.
     *
     * @return array
     */
    public static function clone(): array
    {
        return static::make()->fields();
    }
}
