<?php

namespace Grogu\Acf\Contracts;

/**
 * @package wp-grogu/acf-manager
 * @author Thomas <thomas@hydrat.agency>
 */
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
