<?php

namespace DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Infrastructure\Menu;

/**
 * Class MainMenu, main menu class.
 *
 * @package WPDesk\Library\DropshippingXmlCore\Infrastructure\Menu
 */
class MainMenu extends Abstraction\AbstractMenu implements Abstraction\MainMenuInterface
{
    /**
     * @var string
     */
    private $icon = '';
    /**
     * @var array
     */
    private $submenus = [];
    public function get_icon(): string
    {
        return $this->icon;
    }
    public function set_icon(string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }
    public function get_submenus(): array
    {
        return $this->submenus;
    }
    public function add_submenus(array $submenus): self
    {
        foreach ($submenus as $submenu) {
            $this->add_submenu($submenu);
        }
        return $this;
    }
    public function add_submenu(Abstraction\SubMenuInterface $sub_menu): self
    {
        $sub_menu->set_parent_slug($this->get_slug());
        $this->submenus[] = $sub_menu;
        return $this;
    }
}
