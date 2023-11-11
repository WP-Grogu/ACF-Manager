<?php

namespace Grogu\Acf\Transformers;

class Collection extends Transformer
{
    /**
     * The transformer action to execute.
     *
     * @return string
     */
    public function execute()
    {
        return collect($this->value);
    }
}
