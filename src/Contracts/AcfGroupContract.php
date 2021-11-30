<?php

namespace Grogu\Acf\Contracts;

/**
 * @package wp-grogu/acf-manager
 * @author Thomas <thomas@hydrat.agency>
 */
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
