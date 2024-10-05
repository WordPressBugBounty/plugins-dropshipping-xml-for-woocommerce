<?php

namespace DropshippingXmlFreeVendor\WPDesk\Persistence;

use DropshippingXmlFreeVendor\Psr\Container\ContainerInterface;
/**
 * Container that allows to get all data stored by container.
 *
 * @package WPDesk\Persistence
 */
interface AllDataAccessContainer extends ContainerInterface
{
    /**
     * Get all values.
     *
     * @return array
     */
    public function get_all();
}