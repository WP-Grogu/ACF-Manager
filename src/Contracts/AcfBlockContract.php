<?php

namespace Grogu\Acf\Contracts;

interface AcfBlockContract
{
    /**
     * The data sent to the view
     *
     * @return array
     */
    public function with(): array;

    /**
     * The block acf fields
     *
     * @return array
     */
    public function fields(): array;
}
