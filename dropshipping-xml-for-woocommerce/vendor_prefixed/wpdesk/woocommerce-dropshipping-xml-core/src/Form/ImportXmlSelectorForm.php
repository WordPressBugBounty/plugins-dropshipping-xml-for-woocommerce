<?php

namespace DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Form;

use DropshippingXmlFreeVendor\WPDesk\Forms\Form\FormWithFields;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Infrastructure\Form\Abstraction\FormIdentity;
/**
 * Class ImportXmlSelectorForm, import xml selector form.
 *
 * @package WPDesk\Library\DropshippingXmlCore\Form
 */
class ImportXmlSelectorForm extends FormWithFields implements FormIdentity
{
    const ID = 'item_selector_xml';
    public function __construct(Fields\ImportXmlSelectorFormFields $fields)
    {
        parent::__construct($fields->get_fields(), self::ID);
    }
    public static function get_id(): string
    {
        return self::ID;
    }
}
