<?php

namespace DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Action\Process\Form;

use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\DataProvider\ImportOptionsDataProvider;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Factory\DataProviderFactory;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Form\ImportOptionsForm;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Infrastructure\Service\Listener\Items\Conditional\Conditional;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Infrastructure\Service\Listener\Items\Initable\Initable;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Infrastructure\Request\Request;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Helper\PluginHelper;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\DAO\ImportDAO;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Service\Scheduler\ImportSchedulerService;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Action\View\ImportManagerViewAction;
use DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Action\View\ImportStatusViewAction;
/**
 * Class ImportOptionsFormProcessAction, import options form process.
 *
 * @package WPDesk\Library\DropshippingXmlCore\Action\Process\Form
 */
class ImportOptionsFormProcessAction implements Conditional, Initable
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var ImportOptionsForm
     */
    private $form;
    /**
     * @var PluginHelper
     */
    private $plugin_helper;
    /**
     * @var ImportDAO
     */
    private $import_dao;
    /**
     * @var ImportSchedulerService
     */
    private $import_scheduler;
    /**
     * @var DataProviderFactory
     */
    private $data_provider_factory;
    public function __construct(Request $request, ImportOptionsForm $form, DataProviderFactory $data_provider_factory, ImportDAO $import_dao, ImportSchedulerService $import_scheduler, PluginHelper $helper)
    {
        $this->request = $request;
        $this->form = $form;
        $this->plugin_helper = $helper;
        $this->import_dao = $import_dao;
        $this->import_scheduler = $import_scheduler;
        $this->data_provider_factory = $data_provider_factory;
    }
    public function isActive(): bool
    {
        $settings = $this->request->get_param('post.' . ImportOptionsForm::get_id());
        return $settings->isArray() && !$settings->isEmpty();
    }
    public function init()
    {
        $this->save_form_data();
    }
    private function change_schedule()
    {
        $uid = $this->request->get_param('get.uid')->get();
        if ($this->import_dao->is_uid_exists($uid)) {
            $file_import = $this->import_dao->find_by_uid($uid);
            $file_import->set_next_import($this->import_scheduler->estimate_time($uid));
            $file_import->set_cron_schedule($this->import_scheduler->get_formated_schedule($uid));
            $this->import_dao->update($file_import);
        }
    }
    private function save_form_data()
    {
        $uid = $this->request->get_param('get.uid')->getAsString();
        $form_fields = $this->request->get_param('post.' . ImportOptionsForm::get_id())->get();
        $form_fields = array_map('stripslashes_deep', $form_fields);
        $this->form->handle_request($form_fields);
        if ($this->form->is_valid() && current_user_can('manage_options')) {
            $data_provider = $this->data_provider_factory->create_by_class_name(ImportOptionsDataProvider::class, ['postfix' => $uid]);
            $data_provider->update($this->form);
            $this->change_schedule();
            $edit = $this->request->get_param('get.mode')->get() === 'edit';
            $mode = $this->request->get_param('get.mode')->getAsString();
            if (!empty($mode)) {
                $args = ['uid' => $uid, 'mode' => $mode];
            } else {
                $args = ['uid' => $uid];
            }
            $url = $edit ? $this->plugin_helper->generate_url_by_view(ImportManagerViewAction::class) : $this->plugin_helper->generate_url_by_view(ImportStatusViewAction::class, $args);
            $this->redirect($url);
        }
    }
    private function redirect(string $url)
    {
        wp_redirect($url);
        exit;
    }
}
