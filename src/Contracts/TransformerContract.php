<?php

namespace Grogu\Acf\Contracts;

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
