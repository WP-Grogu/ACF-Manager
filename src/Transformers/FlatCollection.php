<?php

namespace Grogu\Acf\Transformers;

class FlatCollection extends Transformer
{
    /**
     * The transformer action to execute.
     *
     * @return string
     */
    public function execute()
    {
        return collect($this->value)->flatten(1);
    }
}
