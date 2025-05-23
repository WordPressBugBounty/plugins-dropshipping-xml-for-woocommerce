<?php

namespace DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Infrastructure\Provider\Abstraction;

use DropshippingXmlFreeVendor\Psr\Container\ContainerInterface;
use DropshippingXmlFreeVendor\WPDesk\Forms\ContainerForm;
use DropshippingXmlFreeVendor\WPDesk\Persistence\AllDataAccessContainer;
/**
 * Class DataProvider, abstraction layer for data provider class. Allows to store and access data.
 *
 * @package WPDesk\Library\DropshippingXmlCore\Infrastructure\Provider
 */
abstract class DataProvider implements ContainerInterface
{
    /**
     * @see ContainerInterface::get()
     */
    public function get($id)
    {
        return $this->persistance_container()->get($id);
    }
    /**
     * @see ContainerInterface::has()
     */
    public function has($id)
    {
        return $this->persistance_container()->has($id);
    }
    public function set($id, $value)
    {
        return $this->persistance_container()->set($id, $value);
    }
    public function get_all()
    {
        return $this->persistance_container()->get_all();
    }
    public function update(ContainerForm $form)
    {
        $form->put_data($this->persistance_container());
        $this->persistance_container()->save();
    }
    public function save()
    {
        $this->persistance_container()->save();
    }
    /**
     * Persistance container, stores and accesses persisted data.
     */
    abstract protected function persistance_container(): AllDataAccessContainer;
    abstract public static function get_id(): string;
}
