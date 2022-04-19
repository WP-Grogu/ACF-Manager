<?php

namespace Grogu\Acf\Contracts;

/**
 * @package wp-grogu/acf-manager
 * @author Thomas <thomas@hydrat.agency>
 */
interface LoaderContract
{
    /**
     * The loader work.
     *
     * @return array
     */
    public function load(array $field): array;
}
