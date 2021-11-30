<?php

namespace Grogu\Acf\Contracts;

/**
 * @package wp-grogu/acf-manager
 * @author Thomas <thomas@hydrat.agency>
 */
interface TransformerContract
{
    /**
     * The transformer class initier.
     *
     * @return mixed
     */
    public function transform($definition, $value);

    /**
     * The transformer action to execute.
     *
     * @return mixed
     */
    public function execute();
}
