<?php

namespace DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Action\Ajax;

use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Analyser\CsvAnalyser;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Factory\DataProviderFactory;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Form\Fields\ImportCsvSelectorFormFields;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Infrastructure\Data\DataFormat;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Infrastructure\Service\Listener\Items\Conditional\Conditional;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Infrastructure\Service\Listener\Items\Hookable\Hookable;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Infrastructure\Request\Request;
use RuntimeException;
use Exception;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Analyser\XmlAnalyser;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Service\Converter\FileConverterService;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Service\Locator\FileLocatorService;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Service\Validator\AjaxActionValidatorService;
/**
 * Class ConvertCsvImportAjaxAction, class that handles csv file conversion via ajax.
 */
class ConvertCsvImportAjaxAction implements Conditional, Hookable
{
    const AJAX_ACTION = 'evaluate_csv_product_data';
    const AJAX_NONCE = 'nonce_evaluate_csv_product_data';
    /**
     * @var Request
     */
    private $request;
    /**
     * @var FileLocatorService
     */
    private $file_locator;
    /**
     * @var FileConverterService
     */
    private $converter_service;
    /**
     * @var XmlAnalyser
     */
    private $xml_analyser;
    /**
     * @var CsvAnalyser
     */
    private $csv_analyser;
    /**
     * @var DataProviderFactory
     */
    private $data_provider_factory;
    /**
     * @var AjaxActionValidatorService
     */
    private $validator_service;
    public function __construct(Request $request, FileConverterService $converter_service, DataProviderFactory $data_provider_factory, CsvAnalyser $csv_analyser, XmlAnalyser $xml_analyser, FileLocatorService $file_locator, AjaxActionValidatorService $validator_service)
    {
        $this->request = $request;
        $this->file_locator = $file_locator;
        $this->converter_service = $converter_service;
        $this->xml_analyser = $xml_analyser;
        $this->csv_analyser = $csv_analyser;
        $this->data_provider_factory = $data_provider_factory;
        $this->validator_service = $validator_service;
    }
    public function isActive(): bool
    {
        return wp_doing_ajax();
    }
    public function hooks()
    {
        add_action('wp_ajax_' . self::AJAX_ACTION, [$this, 'ajax_import_data']);
    }
    public function ajax_import_data()
    {
        try {
            $this->validate_form_data();
            $form_data = $this->prepare_form_data();
            $source_file = $this->file_locator->get_source_file($form_data['uid']);
            $form_data['separator'] = '' === $form_data['separator'] ? $this->csv_analyser->resolve_separator($source_file) : $form_data['separator'];
            $form_data['source_encoding'] = $this->csv_analyser->resolve_source_encoding($source_file);
            $this->converter_service->convert_from_format(DataFormat::CSV);
            $converted_file = $this->converter_service->convert($source_file, $form_data);
            $this->xml_analyser->load_from_file($converted_file);
            $items = $this->xml_analyser->count_element(ImportCsvSelectorFormFields::NODE_ELEMENT_VALUE);
            wp_send_json(['success' => \true, 'message' => __('Preview refreshed. Make sure that all information are correct and go to next step by clicking the button below.', 'dropshipping-xml-for-woocommerce'), 'separator' => $form_data['separator'], 'file_name' => $converted_file->getFilename(), 'items' => $items]);
        } catch (Exception $e) {
            wp_send_json(['success' => \false, 'message' => $e->getMessage()]);
        }
    }
    private function validate_form_data()
    {
        $this->validator_service->is_valid($this->request->get_param('post.security')->getAsString(), self::AJAX_NONCE);
        $uid = $this->request->get_param('post.uid');
        if (!$uid->isSet()) {
            throw new RuntimeException(__('UID is not set, something is wrong with your import.', 'dropshipping-xml-for-woocommerce'));
        }
        $post_data = $this->request->get_param('post.' . ImportCsvSelectorFormFields::INPUT_SEPARATOR);
        if (!$post_data->isSet()) {
            throw new RuntimeException(__('Error: separator is empty', 'dropshipping-xml-for-woocommerce'));
        }
    }
    private function prepare_form_data(): array
    {
        return ['separator' => $this->request->get_param('post.' . ImportCsvSelectorFormFields::INPUT_SEPARATOR)->getAsString(), 'uid' => $this->request->get_param('post.uid')->getAsString()];
    }
}
