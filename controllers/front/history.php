<?php
/**
 * 2016 WeeTeam
 *
 * @author    WeeTeam <info@weeteam.net>
 * @copyright 2016 WeeTeam
 * @license   http://www.gnu.org/philosophy/categories.html (Shareware)
 */

class WtRecurringOrdersHistoryModuleFrontController extends ModuleFrontController
{

    public function __construct()
    {
        parent::__construct();

        $this->context = Context::getContext();

        $this->context->smarty->assign(array(
            'prefix' => $this->module->wt_prefix
        ));
    }

    public function init()
    {
        $this->display_column_left = false;
        parent::init();
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS($this->module->wt_urls['css_front'] . 'history.css');
        $this->addCSS($this->module->wt_urls['css_front'] . 'addresses.css');

        $this->addJS($this->module->wt_urls['js_front'] . 'history.js');

        $this->addJqueryPlugin(array('scrollTo', 'footable', 'footable-sort'));
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $r_orders = $this->module->getCustomerRecurringOrders($this->context->customer->id);

        $this->context->smarty->assign(array(
            'orders'            => $r_orders,
            'invoiceAllowed'    => (int)Configuration::get('PS_INVOICE'),
            'reorderingAllowed' => !(bool)Configuration::get('PS_DISALLOW_HISTORY_REORDERING'),
            'slowValidation'    => Tools::isSubmit('slowvalidation'),
        ));

        $this->setTemplate('history.tpl');
    }
}
