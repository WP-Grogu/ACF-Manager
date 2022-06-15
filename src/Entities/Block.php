<?php

namespace Grogu\Acf\Entities;

use Grogu\Acf\AcfGroup;
use Grogu\Acf\AcfGroups;
use Grogu\Acf\Contracts\AcfBlockContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use WordPlate\Acf\Location;
use WordPlate\Acf\FieldGroup;

/**
 * An ACF Block definition to be registred.
 *
 * @package wp-grogu/acf-manager
 * @author Thomas <thomas@hydrat.agency>
 */
abstract class Block implements AcfBlockContract
{
    /**
     * The block properties.
     *
     * @var array|object
     */
    public $block;

    /**
     * The block content.
     *
     * @var string
     */
    public $content;

    /**
     * The block preview status.
     *
     * @var bool
     */
    public $preview;

    /**
     * The current post ID.
     *
     * @param int
     */
    public $post;

    /**
     * The block classes.
     *
     * @param string
     */
    public $classes;

    /**
     * The block prefix.
     *
     * @var string
     */
    public $prefix = 'acf/';

    /**
     * The block namespace.
     *
     * @var string
     */
    public $namespace;

    /**
     * The block name.
     *
     * @var string
     */
    public $name = '';

    /**
     * The block slug.
     *
     * @var string
     */
    public $slug = '';

    /**
     * The block view.
     *
     * @var string
     */
    public $view;

    /**
     * The block description.
     *
     * @var string
     */
    public $description = '';

    /**
     * The block category.
     *
     * @var string
     */
    public $category = '';

    /**
     * The block icon.
     *
     * @var string|array
     */
    public $icon = '';

    /**
     * The block keywords.
     *
     * @var array
     */
    public $keywords = [];

    /**
     * The parent block type allow list.
     *
     * @var array
     */
    public $parent = [];

    /**
     * The block post type allow list.
     *
     * @var array
     */
    public $post_types = [];

    /**
     * The default block mode.
     *
     * @var string
     */
    public $mode = 'preview';

    /**
     * The default block alignment.
     *
     * @var string
     */
    public $align = '';

    /**
     * The default block text alignment.
     *
     * @var string
     */
    public $align_text = '';

    /**
     * The default block content alignment.
     *
     * @var string
     */
    public $align_content = '';

    /**
     * The supported block features.
     *
     * @var array
     */
    public $supports = [
        'align' => false,
        'align_text' => false,
        'align_content' => false,
        'anchor' => false,
        'mode' => true,
        'multiple' => true,
        'jsx' => true,
    ];

    /**
     * The block styles.
     *
     * @var array
     */
    public $styles = [];

    /**
     * The block preview example data.
     *
     * @var array
     */
    public $example = [];

    /**
     * The field groups.
     *
     * @var \StoutLogic\AcfBuilder\FieldsBuilder|array
     */
    protected $fields;

    /**
     * The default field settings.
     *
     * @var \Illuminate\Support\Collection|array
     */
    protected $defaults = [];

    /**
     * Assets enqueued when rendering the block.
     *
     * @return void
     */
    public function enqueue()
    {
        //
    }

    /**
     * Data to be passed to the block before rendering.
     *
     * @return array
     */
    public function with(): array
    {
        return [
            'fields' => $this->getFields(),
        ];
    }

    /**
     * Create a new Composer instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (empty($this->name)) {
            throw new \InvalidArgumentException('You must set the block name');
        }

        if (empty($this->slug)) {
            $this->slug = Str::slug(Str::kebab($this->name));
        }

        if (empty($this->view)) {
            $this->view = Str::start($this->slug, 'blocks.');
        }

        if (empty($this->namespace)) {
            $this->namespace = Str::start($this->slug, $this->prefix);
        }

        $config = [
            'title'    => $this->name,
            'fields'   => $this->fields(),
            'location' => $this->location(),
        ];

        $this->fields = (new FieldGroup($config))->toArray();

        \register_field_group($this->fields);
    }

    /**
     * Compose the defined field group and register it
     * with Advanced Custom Fields.
     *
     * @return void
     */
    public function compose()
    {
        if (empty($this->name)) {
            return;
        }

        if (! empty($this->name) && empty($this->slug)) {
            $this->slug = Str::slug(Str::kebab($this->name));
        }

        if (empty($this->view)) {
            $this->view = Str::start($this->slug, 'blocks.');
        }

        // The matrix isn't available on WP > 5.5
        if (Arr::has($this->supports, 'align_content') && version_compare('5.5', get_bloginfo('version'), '>')) {
            if (! is_bool($this->supports['align_content'])) {
                $this->supports['align_content'] = true;
            }
        }

        if (! Arr::has($this->fields, 'location.0.0')) {
            Arr::set($this->fields, 'location.0.0', [
                'param' => 'block',
                'operator' => '==',
                'value' => $this->namespace,
            ]);
        }

        acf_register_block([
            'name'          => $this->slug,
            'title'         => $this->name,
            'description'   => $this->description,
            'category'      => $this->category,
            'icon'          => $this->icon,
            'keywords'      => $this->keywords,
            'parent'        => $this->parent ?: null,
            'post_types'    => $this->post_types,
            'mode'          => $this->mode,
            'align'         => $this->align,
            'align_text'    => $this->align_text ?? $this->align,
            'align_content' => $this->align_content,
            'styles'        => $this->styles,
            'supports'      => $this->supports,
            'example'       => [
                'attributes' => [
                    'mode' => 'preview',
                    'data' => $this->example,
                ]
            ],
            'enqueue_assets' => function () {
                return $this->enqueue();
            },
            'render_callback' => function ($block, $content = '', $preview = false, $post_id = 0) {
                echo $this->render($block, $content, $preview, $post_id);
            }
        ]);

        return $this;
    }

    /**
     * Render the ACF block.
     *
     * @param  array $block
     * @param  string $content
     * @param  bool $preview
     * @param  int $post_id
     * @return string
     */
    public function render($block, $content = '', $preview = false, $post_id = 0)
    {
        $this->block = (object) $block;
        $this->content = $content;
        $this->preview = $preview;

        $this->post = get_post($post_id);
        $this->post_id = $post_id;

        $this->classes = collect([
            'slug' => Str::start(
                Str::slug($this->slug),
                'wp-block-'
            ),
            'align' => ! empty($this->block->align) ?
                Str::start($this->block->align, 'align') :
                false,
            'align_text' => ! empty($this->supports['align_text']) ?
                Str::start($this->block->align_text, 'align-text-') :
                false,
            'align_content' => ! empty($this->supports['align_content']) ?
                Str::start($this->block->align_content, 'is-position-') :
                false,
            'classes' => $this->block->className ?? false,
        ])->filter()->implode(' ');

        return $this->view($this->view, ['block' => $this]);
    }

    /**
     * Build up the associated view
     *
     * @return View
     */
    public function view($view, $with = [])
    {
        if (
            isset($this->block) &&
            ! empty($this->preview)
        ) {
            $preview = str_replace(
                $name = Str::afterLast($view, '.'),
                Str::start($name, 'preview-'),
                $view
            );

            $view = view()->exists($preview) ? $preview : $view;
        }

        return view($view, $with, $this->with())->render();
    }

    /**
     * The block associated fields.
     *
     * @return array
     */
    public function fields(): array
    {
        return [];
    }

    /**
     * The block location(s).
     *
     * @return array
     */
    public function location(): array
    {
        return [
            Location::if('block', $this->namespace),
        ];
    }

    /**
     * Get the parsed fields for displaying
     *
     * @return Collection
     */
    protected function getFields()
    {
        if ($fields = get_fields()) {
            return empty($fields) ? new Collection() : (new AcfGroup($fields))->get();
        }

        return new Collection();
    }
}
