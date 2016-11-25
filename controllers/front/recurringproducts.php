<?php
/**
 * 2016 WeeTeam
 *
 * @author    WeeTeam <info@weeteam.net>
 * @copyright 2016 WeeTeam
 * @license   http://www.gnu.org/philosophy/categories.html (Shareware)
 */

class WtRecurringOrdersRecurringProductsModuleFrontController extends ModuleFrontController
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
        parent::init();

        $this->display_column_left = false;
    }

    public function setMedia()
    {
        parent::setMedia();
    }

    public function initContent()
    {
        parent::initContent();
        if (isset($_REQUEST['error_code'])) {
            $this->errors[] = Tools::displayError('You must be logged in to request a custom order');
        }

        $user_id = (int)$this->context->customer->id;

        if ($user_id) {
            $products = $this->module->getRecurringProducts($user_id);
        }

        $this->context->smarty->assign(array(
            'meta_title' => $this->module->l('My recurring products') . ' - ' . Configuration::get('PS_SHOP_NAME'),
            'products'   => $products
        ));

        $this->setTemplate('user_recurring_products.tpl');
    }
}
