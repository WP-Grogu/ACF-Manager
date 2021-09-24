<?php

namespace Grogu\Acf\Contracts;

interface AcfGroupContract
{
    /**
     * The fields configuration
     *
     * @return array
     */
    public function fields(): array;

    /**
     * The location configuration
     *
     * @return array
     */
    public function location(): array;
}
