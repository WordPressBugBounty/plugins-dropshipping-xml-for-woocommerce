<?php

namespace DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\DataProvider;

use DropshippingXmlFreeVendor\WPDesk\Persistence\Adapter\WordPress\WordpressOptionsContainer;
use DropshippingXmlFreeVendor\WPDesk\Persistence\AllDataAccessContainer;
use DropshippingXmlFreeVendor\WPDesk\Persistence\Decorator\DelaySinglePersistentContainer;
use DropshippingXmlFreeVendor\WPDesk\Persistence\PersistentContainer;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Infrastructure\Config\Config;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Infrastructure\Provider\Abstraction\DataProvider;
/**
 * Class DropshippingDataProvider
 *
 * @package WPDesk\Library\DropshippingXmlCore\DataProvider
 */
abstract class DropshippingDataProvider extends DataProvider
{
    /**
     * @var string
     */
    private $postfix;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var AllDataAccessContainer
     */
    private $persistence_container;
    public function __construct(Config $config, $postfix = '')
    {
        $this->config = $config;
        $this->postfix = $postfix;
    }
    /**
     * @see DataProvider::persistance_container()
     */
    protected function persistance_container(): AllDataAccessContainer
    {
        if (isset($this->persistence_container)) {
            return $this->persistence_container;
        }
        $this->persistence_container = new DelaySinglePersistentContainer(new WordpressOptionsContainer($this->get_persistence_name()), $this->get_persistence_postfix());
        return $this->persistence_container;
    }
    private function get_persistence_name(): string
    {
        return $this->config->get_param('plugin.persistance_prefix')->get() . '_' . $this->get_identity();
    }
    public function get_persistence_postfix(): string
    {
        return !empty($this->postfix) ? '_' . $this->postfix : '';
    }
    abstract protected function get_identity(): string;
}
