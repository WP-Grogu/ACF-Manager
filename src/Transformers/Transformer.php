<?php

namespace Grogu\Acf\Transformers;

use Grogu\Acf\Contracts\TransformerContract;

/**
 * @package wp-grogu/acf-manager
 * @author Thomas <thomas@hydrat.agency>
 */
abstract class Transformer implements TransformerContract
{
    protected $definition;
    protected $value;

    /**
     * Initiate the transformer data and call the execute method.
     *
     * @return mixed
     */
    public function transform($definition, $value)
    {
        $this->definition = $definition;
        $this->value      = $value;

        return $this->execute();
    }
}
