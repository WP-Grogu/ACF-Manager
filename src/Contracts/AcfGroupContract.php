<?php

namespace Grogu\Acf\Contracts;

/**
 * @package wp-grogu/acf-manager
 * @author Thomas <thomas@hydrat.agency>
 */
interface AcfGroupContract
{
    /**
     * The group fields definitions.
     *
     * @return array
     */
    public function fields(): array;

    /**
     * The group location configuration.
     *
     * @return array
     */
    public function location(): array;
}
