<?php

namespace DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Form;

use DropshippingXmlFreeVendor\WPDesk\Forms\Form\FormWithFields;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Infrastructure\Form\Abstraction\FormIdentity;
/**
 * Class SettingsForm, settings form.
 *
 * @package WPDesk\Library\DropshippingXmlCore\Form
 */
class SettingsForm extends FormWithFields implements FormIdentity
{
    const ID = 'settings';
    public function __construct(Fields\SettingsFormFields $fields)
    {
        parent::__construct($fields->get_fields(), self::ID);
    }
    public static function get_id(): string
    {
        return self::ID;
    }
}
