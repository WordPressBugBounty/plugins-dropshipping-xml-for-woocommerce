<?php

namespace DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Action\Process;

use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Form\Fields\ImportCsvSelectorFormFields;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Helper\PluginHelper;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Infrastructure\Service\Listener\Items\Conditional\Conditional;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Infrastructure\Service\Listener\Items\Initable\Initable;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Infrastructure\Request\Request;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\DAO\ImportDAO;
use Exception;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Action\View\ImportManagerViewAction;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Entity\Import;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Service\Scheduler\ImportSchedulerService;
/**
 * Class ImportManagerDeleteProcessAction, import delete process over import manager.
 */
class ImportManagerActivateProcessAction implements Conditional, Initable
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var ImportDAO
     */
    private $import_dao;
    /**
     * @var PluginHelper
     */
    private $plugin_helper;
    /**
     * @var ImportSchedulerService
     */
    private $scheduler;
    public function __construct(Request $request, ImportDAO $import_dao, PluginHelper $plugin_helper, ImportSchedulerService $scheduler)
    {
        $this->request = $request;
        $this->import_dao = $import_dao;
        $this->plugin_helper = $plugin_helper;
        $this->scheduler = $scheduler;
    }
    public function isActive(): bool
    {
        return $this->request->get_param('get.activate')->isNumeric() && $this->plugin_helper->is_plugin_page($this->request->get_param('get.page')->getAsString());
    }
    public function init()
    {
        $this->validate_form_data();
        $this->activate_import();
    }
    private function activate_import()
    {
        $id = (int) $this->request->get_param('get.activate')->get();
        $import = $this->import_dao->find_by_id($id);
        $import->set_next_import($this->scheduler->estimate_time($import->get_uid()));
        $import->set_last_position(0);
        $import->set_updated(0);
        $import->set_skipped(0);
        $import->set_created(0);
        $import->set_products_count(0);
        $import->set_status(Import::STATUS_WAITING);
        $this->import_dao->update($import);
        $url = $this->plugin_helper->generate_url_by_view(ImportManagerViewAction::class);
        wp_redirect($url);
        exit;
    }
    private function validate_form_data()
    {
        if (!wp_verify_nonce($this->request->get_param('get.nonce')->getAsString(), ImportManagerViewAction::MANAGER_NONCE)) {
            wp_die('Error, security code is not valid');
        }
        if (!current_user_can('manage_options')) {
            wp_die('Error, you are not allowed to do this action');
        }
    }
}