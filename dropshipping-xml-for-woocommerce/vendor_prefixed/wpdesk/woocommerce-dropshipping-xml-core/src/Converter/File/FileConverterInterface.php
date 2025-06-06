<?php

namespace DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Converter\File;

use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\File\FileObject;
/**
 * Interface FileConverterInterface, file converter interface.
 *
 * @package WPDesk\Library\DropshippingXmlCore\Converter\File
 */
interface FileConverterInterface
{
    public function convert(FileObject $source_file, string $save_location): FileObject;
    /**
     * @param array $options
     *
     * @return void
     */
    public function set_parameters(array $options);
    public static function get_supported_data_format(): string;
}
