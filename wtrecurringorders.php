<?php
/**
 * 2016 WeeTeam
 *
 * @author    WeeTeam <info@weeteam.net>
 * @copyright 2016 WeeTeam
 * @license   http://www.gnu.org/philosophy/categories.html (Shareware)
 */

class WtRecurringOrders extends Module
{
    public $wt_prefix = 'wtrecurringorder_';
    public $wt_db;
    public $wt_file = '';
    public $wt_directory = '';
    public $wt_paths = array();
    public $wt_urls = array();
    public $wt_strings = array();
    public $wt_session_products_recurring_key = '';
    public $wt_html_return = '';

    public function __construct()
    {
        $this->name = 'wtrecurringorders';
        $this->author = 'WeeTeam';
        $this->version = '1.0.0';
        $this->tab = 'front_office_features';
        $this->module_key = '9219f650a1f37263731663a1f15cf0d9';
        $this->bootstrap = true;
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6');
        $this->wt_file = __FILE__;
        $this->wt_directory = dirname($this->wt_file);
        $this->wt_session_products_recurring_key = $this->wt_prefix . 'products_recurring';
        $this->wt_mail_tpls = array(
            'ro' => 'recurring_order',
            'ro_reminder' => 'recurring_order_reminder',
            'ro_no_available' => 'recurring_oder_no_available',
        );
        $this->test_mode = true;

        parent::__construct();

        $this->displayName = $this->l('Recurring Orders');
        $this->description = $this->l('Allows your customers to add recurrent options for products');

        $this->wt_db = Db::getInstance();

        $this->init();

    }

    public function install()
    {
        if (parent::install() &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayShoppingCartFooter') &&
            $this->registerHook('displayPaymentTop') &&
            $this->registerHook('actionValidateOrder') &&
            $this->registerHook('displayCustomerAccount') &&
            $this->registerHook('displayAdminProductsExtra') &&
            $this->registerHook('displayProductPriceBlock') &&
            $this->installDB()
        ) {

            $tooltip_text = array();
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                $id_lang = (int)($language['id_lang']);
                $tooltip_text['tooltip_text_' . $id_lang] = $this->l('Could be ordered on a recurrent basis');
            }
            Configuration::updateValue($this->wt_prefix . 'tooltip_text', Tools::jsonEncode($tooltip_text));
            Configuration::updateValue($this->wt_prefix . 'tooltip_state', 1);

            return true;
        }
        return false;
    }

    public function uninstall()
    {
        if (parent::uninstall() &&
            $this->unregisterHook('displayHeader') &&
            $this->unregisterHook('displayBackOfficeHeader') &&
            $this->unregisterHook('displayShoppingCartFooter') &&
            $this->unregisterHook('displayPaymentTop') &&
            $this->unregisterHook('actionValidateOrder') &&
            $this->unregisterHook('displayCustomerAccount') &&
            $this->unregisterHook('displayAdminProductsExtra') &&
            $this->unregisterHook('displayProductPriceBlock') &&
            $this->uninstallDB()
        ) {
            Configuration::deleteByName('tooltip_text');
            Configuration::deleteByName('tooltip_state');

            return true;
        }
        return false;
    }

    public function installDB()
    {
        $return = true;

        $return &= $this->wt_db->Execute(
            "
                CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . $this->wt_prefix . "recurring_products`
                (
                    `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
                    `user_id` BIGINT(20) DEFAULT NULL,
                    `product_id` BIGINT(20) DEFAULT NULL,
                    `id_product_attribute` BIGINT(20) DEFAULT NULL,
                    `option` TINYINT(1) DEFAULT NULL,
                    `d` VARCHAR(255) DEFAULT NULL,
                    `quantity` INT(10) DEFAULT NULL,
                    `status` TINYINT(1) DEFAULT NULL,
                    `date` INT(10) DEFAULT NULL,
                    PRIMARY KEY (`id`)
                )
                ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8
            "
        );

        $return &= $this->wt_db->Execute(
            "
                CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . $this->wt_prefix . "log_email`
                (
                    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `id_customer` INT UNSIGNED NULL,
                    `id_cart` INT UNSIGNED NULL,
                    `template` VARCHAR(255) DEFAULT NULL,
                    `date_sent` DATETIME NOT NULL,
                    INDEX `id_cart`(`id_cart`),
                    INDEX `date_sent`(`date_sent`)
                )
                ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8
            "
        );

        $return &= $this->wt_db->Execute(
            "
                CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . $this->wt_prefix . "product_disabled_for_recurrence`
                (
                    `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
                    `id_product` BIGINT(20) DEFAULT NULL,
                    `date` datetime DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                )
                ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8
            "
        );

        $return &= $this->wt_db->Execute(
            "
                CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . $this->wt_prefix . "hash`
                (
                    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `id_cart` INT UNSIGNED NULL,
                    `wt_ro_hash` VARCHAR(32) NULL,
                    `date_created` DATETIME NOT NULL,
                    INDEX `id_cart`(`id_cart`),
                    INDEX `date_created`(`date_created`)
                )
                ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8
            "
        );

        return $return;
    }

    public function uninstallDB()
    {
        $return = true;

        $return &= $this->wt_db->Execute(
            "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . $this->wt_prefix . "recurring_products`"
        );
        $return &= $this->wt_db->Execute(
            "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . $this->wt_prefix . "log_email`"
        );
        $return &= $this->wt_db->Execute(
            "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . $this->wt_prefix . "hash`"
        );
        $return &= $this->wt_db->Execute(
            "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . $this->wt_prefix . "product_disabled_for_recurrence`"
        );

        return $return;
    }

    private function init()
    {
        $this->pathsInit();
        $this->assignDefaultTplVars();
        $this->actionsInit();
    }

    private function pathsInit()
    {
        $this->wt_paths['tpl_views'] = 'views/';
        $this->wt_paths['tpl_admin'] = $this->wt_paths['tpl_views'] . 'templates/admin/';
        $this->wt_paths['tpl_front'] = $this->wt_paths['tpl_views'] . 'templates/front/';
        $this->wt_paths['tpl_hook'] = $this->wt_paths['tpl_views'] . 'templates/hook/';
        $this->wt_paths['css_admin'] = $this->wt_paths['tpl_views'] . 'css/admin/';
        $this->wt_paths['css_front'] = $this->wt_paths['tpl_views'] . 'css/front/';
        $this->wt_paths['js'] = $this->wt_paths['tpl_views'] . 'js/';
        $this->wt_paths['js_admin'] = $this->wt_paths['tpl_views'] . 'js/admin/';
        $this->wt_paths['js_front'] = $this->wt_paths['tpl_views'] . 'js/front/';
        $this->wt_paths['images'] = $this->wt_paths['tpl_views'] . 'img/';
        $this->wt_paths['mails'] = 'mails/';

        $this->wt_urls['module'] = Tools::getProtocol(Tools::usingSecureMode()) .
            $_SERVER['HTTP_HOST'] . $this->getPathUri();
        $this->wt_urls['css_admin'] = $this->wt_urls['module'] . $this->wt_paths['css_admin'];
        $this->wt_urls['css_front'] = $this->wt_urls['module'] . $this->wt_paths['css_front'];
        $this->wt_urls['js'] = $this->wt_urls['module'] . $this->wt_paths['js'];
        $this->wt_urls['js_admin'] = $this->wt_urls['module'] . $this->wt_paths['js_admin'];
        $this->wt_urls['js_front'] = $this->wt_urls['module'] . $this->wt_paths['js_front'];
        $this->wt_urls['images'] = $this->wt_urls['module'] . $this->wt_paths['images'];
        $this->wt_urls['mails'] = $this->wt_urls['module'] . $this->wt_paths['mails'];
    }

    private function assignDefaultTplVars()
    {
        $this->context->smarty->assign(array(
            'prefix' => $this->wt_prefix,
        ));
    }

    public function actionsInit()
    {
        include_once('actions.php');
    }

    public function actionMethodFilter($action = '')
    {
        if ($action == '') {
            $action = Tools::getValue('action');
        }

        return 'action' . str_replace('_', '', preg_replace_callback(
            array(
                "/^[a-z]/uis",
                "/_[a-z]/uis",
            ),
            create_function(
                '$m',
                'return strtoupper($m[0]);'
            ),
            $action
        ));
    }

    public function getContent()
    {
        if (Tools::getValue('configure') && Tools::getValue('controller')) {
            $this->saveConfiguration();
        }

        if ($action = Tools::getValue('wt_action')) {
            $res = array('status' => false);
            $action = $this->actionMethodFilter($action);
            if (method_exists($this, $action)) {
                $data = (!empty($_REQUEST['data'])) ? $_REQUEST['data'] : null;
                call_user_func_array(array($this, $action), array($data, &$res));
            }
            exit(Tools::jsonEncode($res));
        }

        $this->wt_html_return = '';
        $this->wt_html_return .= $this->renderInfo();
        $this->wt_html_return .= $this->renderCronJobView();
        $this->wt_html_return .= $this->renderDisabledRoProductsForm();
        $this->wt_html_return .= $this->renderViewForm();
        $this->wt_html_return .= $this->renderStats();

        return $this->wt_html_return;
    }

    public function getFormHelper()
    {
        $languages = Language::getLanguages(false);
        foreach ($languages as $k => $language) {
            $id_lang = (int)($language['id_lang']);
            $languages[$k]['is_default'] = (int)($id_lang == (int)Configuration::get('PS_LANG_DEFAULT'));
        }

        $form = new HelperForm();
        $form->module = $this;
        $form->name_controller = 'xxx';
        $form->identifier = $this->identifier;
        $form->token = Tools::getAdminTokenLite('AdminModules');
        $form->languages = $languages;
        $form->currentIndex = '';
        $form->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $form->allow_employee_form_lang = true;

        return $form;
    }

    public function renderInfo()
    {
        $this->context->smarty->assign(array(
            'email_tpl_url' => $this->context->link->getAdminLink('AdminTranslations', false) .
                '&lang=en&type=mails&theme=&token=' . Tools::getAdminTokenLite('AdminTranslations')
        ));

        return $this->display($this->wt_file, $this->wt_paths['tpl_admin'] . 'info.tpl');
    }

    public function renderCronJobView()
    {
        $cronjobs = Module::getInstanceByName('cronjobs');
        $cronjobs_installed = ($cronjobs && $cronjobs->active) ? true : false;

        $token_am = Tools::getAdminTokenLite('AdminModules');
        $this->context->smarty->assign(array(
            'token_adm_modules' => $token_am,
            'cronjobs_installed' => $cronjobs_installed,
            'cron_url' => $this->wt_urls['module'] . 'cron.php?token=' . $token_am,
            'module_cronjobs_url' => $this->context->link->getAdminLink('AdminModules', false) .
                '&configure=cronjobs&module_name=cronjobs&token=' . $token_am,
            'admin_module_url' => $this->context->link->getAdminLink('AdminModules', false) .
                '&token=' . $token_am
        ));

        return $this->display($this->wt_file, $this->wt_paths['tpl_admin'] . 'configure.tpl');
    }

    public function renderDisabledRoProductsForm()
    {
        $root = Category::getRootCategory();
        $disabled_cats = Tools::jsonDecode(Configuration::get($this->wt_prefix . 'disabled_categories'));
        if (Configuration::get($this->wt_prefix . 'disabled_all_products')) {
            $checked_cats = array($root->id);
        } elseif (!empty($disabled_cats)) {
            $checked_cats = $disabled_cats;
        } else {
            $checked_cats = array();
        }

        $tree = new HelperTreeCategories('tree_selected_categories', $this->l('Categories'));
        $tree->setAttribute('is_category_filter', (bool)$root->id);
        $tree->setInputName('tree-id-category');
        $tree->setRootCategory($root->id);
        $tree->setUseCheckBox(true);
        $tree->setSelectedCategories($checked_cats);
        $category_select_tree = $tree->render();

        $tree = new HelperTreeCategories('tree_selected_products', $this->l('Products'));
        $tree->setAttribute('is_category_filter', (bool)$root->id);
        $tree->setInputName('tree-id-category-product');
        $tree->setRootCategory($root->id);
        $tree->setUseCheckBox(true);
        $category_filter_tree = $tree->render();

        $form_fields = array(array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Products settings'),
                    'icon' => 'icon-retweet'
                ),
                'input' => array(
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Select products which are not available for recurring orders'),
                        'name' => 'setting_disabled_products',
                        'required' => false,
                        'class' => 't',
                        'br' => true,
                        'values' => array(
                            array(
                                'id' => 'all_products',
                                'value' => 1,
                                'label' => $this->l('All products'),
                            ),
                            array(
                                'id' => 'selected_categories',
                                'value' => 2,
                                'label' => $this->l('Select by categories')
                            ),
                            array(
                                'id' => 'selected_products',
                                'value' => 3,
                                'label' => $this->l('Select products')
                            )
                        )
                    ),
                    array(
                        'type' => 'html',
                        'name' => 'category_select_tree',
                        'label' => $this->l('Select by category'),
                        'html_content' => $category_select_tree,
                    ),
                    array(
                        'type' => 'html',
                        'name' => 'category_filter_tree',
                        'label' => $this->l('Select by product'),
                        'html_content' => $category_filter_tree,
                    ),
                    array(
                        'type' => 'html',
                        'name' => 'disabled-prds',
                        'html_content' => $this->display(
                            $this->wt_file,
                            $this->wt_paths['tpl_admin'] . 'disabled_prds.tpl'
                        ),
                    )
                ),
                'submit' => array(
                    'name' => 'actionDisabledRoProductsSave',
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right'
                ),
            ))
        );

        $form = $this->getFormHelper();
        $form->fields_value['setting_disabled_products'] = 1;
        $form->fields_value['old_disabled'] = implode(
            ',',
            array_column($this->getDisabledForRecurringOrderProducts(), 'id_product')
        );

        return $form->generateForm($form_fields);
    }

    public function renderViewForm()
    {
        $form_fields = array(array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('View'),
                    'icon' => 'icon-eye-open'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show tooltips ?'),
                        'name' => 'tooltip_state',
                        'required' => false,
                        'is_bool' => 1,
                        'values' => array(
                            array(
                                'id' => 'tooltips_off',
                                'value' => 1,
                                'label' => $this->l('Blacklister'),
                            ),
                            array(
                                'id' => 'tooltips_on',
                                'value' => 0,
                                'label' => $this->l('Authoriser'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Tooltip text'),
                        'name' => 'tooltip_text',
                        'lang' => true,
                        'size' => 255
                    ),
                ),
                'submit' => array(
                    'name' => 'actionViewSave',
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right'
                ),
            ))
        );

        $form = $this->getFormHelper();
        $languages = Language::getLanguages(false);
        $data = Tools::jsonDecode(Configuration::get($this->wt_prefix . 'tooltip_text'), true);
        foreach ($languages as $language) {
            $id_lang = (int)($language['id_lang']);
            $form->fields_value['tooltip_text'][$id_lang] = $data['tooltip_text_' . $id_lang];
        }
        $form->fields_value['tooltip_state'] = (int) Configuration::get($this->wt_prefix . 'tooltip_state');

        return $form->generateForm($form_fields);

    }

    public function renderStats()
    {
        $period = 30;
        $this->context->smarty->assign(
            array(
                'period' => $period,
                'stats_array' => $this->prepareStatsDataToDisplay($period)
            )
        );

        return $this->display($this->wt_file, $this->wt_paths['tpl_admin'] . 'stats.tpl');
    }

    public function saveConfiguration()
    {
        $tooltip_text = array();
        if (Tools::isSubmit('actionViewSave')) {
            foreach ($_POST as $k => $v) {
                if (preg_match("/^tooltip_text/uis", $k)) {
                    $tooltip_text[$k] = $v;
                }
                if (preg_match("/^tooltip_state/uis", $k)) {
                    $tooltip_state = $v;
                }
            }

            Configuration::updateValue($this->wt_prefix . 'tooltip_text', Tools::jsonEncode($tooltip_text));
            Configuration::updateValue($this->wt_prefix . 'tooltip_state', $tooltip_state);
            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminModules', false) . '&configure='
                . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&token=' .
                Tools::getAdminTokenLite('AdminModules')
            );

        } elseif (Tools::isSubmit('actionDisabledRoProductsSave')) {
            Configuration::updateValue($this->wt_prefix . 'disabled_all_products', 0);
            $option = (int)Tools::getValue('setting_disabled_products');
            switch ($option) {
                case 1:
                    Configuration::updateValue($this->wt_prefix . 'disabled_all_products', 1);
                    break;
                case 2:
                    $cats = Tools::getValue('tree-id-category');
                    Configuration::updateValue(
                        $this->wt_prefix . 'disabled_categories',
                        Tools::jsonEncode($cats)
                    );
                    break;
                case 3:
                    // nothing, we work with ajax for a single product
                    break;
                default:
                    return false;
            }
        }

    }

    public function getAllRecurringProducts()
    {
        $result = $this->wt_db->executeS(
            "
                SELECT
                    rp.*
                FROM
                    `" . _DB_PREFIX_ . $this->wt_prefix . "recurring_products` rp
                WHERE
                     rp.`status` = 1
            "
        );

        return $result;
    }

    public function getRecurringProducts($user_id)
    {
        $result = $this->wt_db->executeS(
            "
                SELECT
                    rp.*
                FROM
                    `" . _DB_PREFIX_ . $this->wt_prefix . "recurring_products` rp
                WHERE
                    rp.`user_id` = '" . pSQL($user_id) . "'
            "
        );

        return $result;
    }

    public function getDisabledForRecurringOrderProducts()
    {
        $result = $this->wt_db->executeS(
            "
                SELECT
                    dp.*
                FROM
                    `" . _DB_PREFIX_ . $this->wt_prefix . "product_disabled_for_recurrence` dp
            "
        );

        return $result;
    }

    public function getCustomerRecurringOrdersIds($user_id)
    {
        $r_orders_ids = $this->wt_db->executeS(
            "
                SELECT
                    o.`id_order`
                FROM
                    " . _DB_PREFIX_ . "orders o
                    LEFT JOIN `" . _DB_PREFIX_ . $this->wt_prefix . "hash` h ON (o.`id_cart` = h.`id_cart`)
                WHERE
                    h.`wt_ro_hash` IS NOT NULL
                    AND
                    o.`id_customer` = '" . pSQL($user_id) . "'
            "
        );

        if (!empty($r_orders_ids) && is_array($r_orders_ids)) {
            $r_orders_ids = array_column($r_orders_ids, 'id_order');
        }

        return !empty($r_orders_ids) ? $r_orders_ids : false;
    }

    public function getCustomerRecurringOrders($user_id)
    {
        $r_orders = array();
        $r_orders_ids = $this->getCustomerRecurringOrdersIds($user_id);

        if (!empty($r_orders_ids)) {
            foreach ($r_orders_ids as $r_order_id) {
                $r_order = new Order((int)$r_order_id);
                if (Validate::isLoadedObject($r_order)) {
                    $r_order->virtual = $r_order->isVirtual(false);
                    $r_order->wt_state = $r_order->getCurrentOrderState();
                }

                $r_orders[] = $r_order;
            }
        }

        return $r_orders;
    }

    public function getCurrentCartProducts()
    {
        $res = array();
        $cart = new Cart($this->context->cookie->id_cart);
        $products = $cart->getProducts();
        if (count($products)) {
            foreach ($products as $v) {
                $res[] = array(
                    'id' => $v['id_product'],
                    'id_product_attribute' => $v['id_product_attribute'],
                    'name' => $v['name'],
                    'attributes' => !empty($v['attributes']) ? $v['attributes'] : null,
                    'image' => $this->context->link->getImageLink(
                        $v['link_rewrite'],
                        $v['id_image'],
                        ImageType::getFormatedName('cart')
                    )
                );
            }
        }

        return $res;
    }

    public function getProductData($id, $id_product_attribute = null)
    {
        $product = new Product($id);
        $cover = Product::getCover($product->id);
        $attributes = (!empty($id_product_attribute)) ?
            $product->getAttributeCombinationsById($id_product_attribute, $this->context->language->id) : null;

        return array(
            'id' => $product->id,
            'attributes' => $attributes,
            'name' => $product->name[$this->context->language->id],
            'image' => $this->context->link->getImageLink(
                $product->link_rewrite[$this->context->language->id],
                $cover['id_image'],
                ImageType::getFormatedName('cart')
            ),
            'link' => $this->context->link->getProductLink($product)
        );
    }

    public function getSessionProductsRecurring()
    {
        $res = Tools::jsonDecode($this->context->cookie->{$this->wt_session_products_recurring_key}, true);

        if (!is_array($res) || !count($res)) {
            $res = array();
        }

        return $res;
    }

    public function setSessionProductsRecurring($data, $key = false)
    {
        if (is_array($data) &&
            count($data) &&
            isset($data['id']) &&
            isset($data['id_product_attribute']) &&
            isset($data['option']) &&
            isset($data['d'])
        ) {
            $data['id'] = (int)$data['id'];

            if ($data['id']) {
                $prd = $this->getSessionProductsRecurring();
                $data['quantity'] = (int)$data['quantity'];
                if ($data['quantity'] < 1) {
                    $data['quantity'] = 1;
                } elseif ($data['quantity'] > 9999) {
                    $data['quantity'] = 9999;
                }

                $data['option'] = (int)$data['option'];
                if ($data['option'] < 1) {
                    $data['option'] = 1;
                } elseif ($data['option'] > 2) {
                    $data['option'] = 2;
                }

                $data['d'] = (int)$data['d'];
                if ($data['d'] < 1) {
                    $data['d'] = 1;
                } elseif ($data['d'] > 28) {
                    $data['d'] = 28;
                }

                $data = array(
                    'id' => $data['id'],
                    'id_product_attribute' => $data['id_product_attribute'],
                    'quantity' => $data['quantity'],
                    'option' => $data['option'],
                    'd' => $data['d']
                );

                if (($key === false) || ($key !== false && !isset($prd[$key]))) {
                    $prd[] = $data;
                } else {
                    $prd[$key] = $data;
                }

                $this->context->cookie->__set($this->wt_session_products_recurring_key, Tools::jsonEncode($prd));

                return true;
            }
        }

        return false;
    }

    public function removeSessionProductRecurring($key)
    {
        $prd = $this->getSessionProductsRecurring();

        if (isset($prd[$key])) {
            unset($prd[$key]);
            $this->context->cookie->__set($this->wt_session_products_recurring_key, Tools::jsonEncode($prd));

            return true;
        }

        return false;
    }

    public function getRecurringProduct($id, $user_id = null)
    {
        $id = (int)$id;
        $user_id = (int)$user_id;
        $where = '';

        if ($user_id) {
            $where .= " AND rp.`user_id` = " . pSQL($user_id);
        }

        $result = $this->wt_db->executeS(
            "
                SELECT
                    rp.*
                FROM
                    `" . _DB_PREFIX_ . $this->wt_prefix . "recurring_products` rp
                WHERE
                    rp.`id` = '" . pSQL($id) . "'
                    '{$where}'
                LIMIT
                    1
            "
        );

        return (!empty($result)) ? (array)$result[0] : array();
    }

    public function setProductsRecurring($data)
    {
        if (is_array($data) &&
            count($data) &&
            isset($data['rp_id']) &&
            isset($data['option']) &&
            isset($data['d']) &&
            isset($data['status'])
        ) {
            $data['user_id'] = (int)$data['user_id'];
            $data['rp_id'] = (int)$data['rp_id'];

            if ($data['rp_id']) {
                $data['quantity'] = (int)$data['quantity'];
                if ($data['quantity'] < 1) {
                    $data['quantity'] = 1;
                } elseif ($data['quantity'] > 9999) {
                    $data['quantity'] = 9999;
                }

                $data['option'] = (int)$data['option'];
                if ($data['option'] < 1) {
                    $data['option'] = 1;
                } elseif ($data['option'] > 2) {
                    $data['option'] = 2;
                }

                $data['d'] = (int)$data['d'];
                if ($data['d'] < 1) {
                    $data['d'] = 1;
                } elseif ($data['d'] > 28) {
                    $data['d'] = 28;
                }

                $data['status'] = (int)$data['status'];
                if ($data['status'] < 1) {
                    $data['status'] = 0;
                } else {
                    $data['status'] = 1;
                }

                $this->wt_db->update($this->wt_prefix . 'recurring_products', array(
                    'option' => pSQL($data['option']),
                    'd' => pSQL($data['d']),
                    'quantity' => pSQL($data['quantity']),
                    'status' => pSQL($data['status']),
                    'date' => time()
                ), "id = '" . pSQL($data['rp_id']) . "' AND user_id = '" . pSQL($data['user_id']) . "'");

                return true;
            }
        }

        return false;
    }

    public function removeProductRecurring($rp_id, $user_id = null)
    {
        $rp_id = (int)$rp_id;
        $user_id = (int)$user_id;

        if ($rp_id) {
            $where = "id = '" . pSQL($rp_id) . "'";

            if ($user_id) {
                $where .= " AND user_id = '" . pSQL($user_id) . "'";
            }

            $this->wt_db->delete($this->wt_prefix . 'recurring_products', $where);

            return true;
        }

        return false;
    }

    public function getCartByHash($wt_ro_hash)
    {

        $result = $this->wt_db->getRow(
            "
                SELECT
                    h.*
                FROM
                    `" . _DB_PREFIX_ . $this->wt_prefix . "hash` h
                WHERE
                    h.`wt_ro_hash` = '" . pSQL($wt_ro_hash) . "'
            "
        );

        if ($result) {
            $cartObj = new Cart($result['id_cart']);
        }

        return (!empty($cartObj)) ? $cartObj : false;
    }

    public function getCartHashById($cart_id)
    {
        $result = $this->wt_db->getRow(
            "
                SELECT
                    h.wt_ro_hash
                FROM
                    `" . _DB_PREFIX_ . $this->wt_prefix . "hash` h
                WHERE
                    h.`id_cart` = '" . pSQL($cart_id) . "'
            "
        );

        return $result['wt_ro_hash'];
    }

    public function setCartHash($cart_id, $action = "create", $value = null)
    {
        switch ($action) {
            case "create":
                $wt_ro_hash = md5(uniqid(rand(), 1));
                $request_values = array(
                    'wt_ro_hash' => pSQL($wt_ro_hash),
                    'date_created' => date('Y-m-d H:i:s')
                );
                break;
            case "update":
                $wt_ro_hash = $value;
                $request_values = array(
                    'wt_ro_hash' => pSQL($wt_ro_hash)
                );
                break;
            default:
                return false;
        }

        $this->wt_db->update($this->wt_prefix . 'hash', $request_values, "id_cart = '" . pSQL($cart_id) . "'");

        return $wt_ro_hash;
    }

    public function createCustomerCart($customer_id)
    {
        $customer = new Customer((int)$customer_id);

        $this->context->cart = new Cart();
        $this->context->cart->id_address_delivery = (int)Address::getFirstCustomerAddressId((int)($customer->id));
        $this->context->cart->id_address_invoice = $this->context->cart->id_address_delivery;
        $this->context->cart->id_lang = (int)($this->context->cookie->id_lang);
        $this->context->cart->id_currency = (int)($this->context->cookie->id_currency);
        $this->context->cart->id_carrier = 1;
        $this->context->cart->recyclable = 0;
        $this->context->cart->gift = 0;
        $this->context->cart->setDeliveryOption();
        $this->context->cart->add();
        $this->context->cart->id_customer = (int)$customer->id;
        $this->context->cart->update();
        $this->context->cart->secure_key = $customer->secure_key;
        $this->context->cart->save();

        $this->wt_db->insert($this->wt_prefix . 'hash', array('id_cart' => $this->context->cart->id));

        return $this->context->cart;
    }

    public function getStatsData($days)
    {
        $needed_carts_ids =
            "
                SELECT h.id_cart
                FROM `" . _DB_PREFIX_ . $this->wt_prefix . "hash` h
                WHERE
                    h.date_created = l.date_sent
                    AND
                    h.wt_ro_hash = 'validated'
            ";
        $carts_ids_common = $needed_carts_ids . " AND l.template ='" . $this->wt_mail_tpls['ro'] . "'";
        $carts_ids_reminder = $needed_carts_ids . " AND l.template = '" . $this->wt_mail_tpls['ro_reminder'] . "'";

        $inside_query_common =
            "
                SELECT COUNT(*)
                FROM `" . _DB_PREFIX_ . "orders` o
                WHERE
                    o.valid = 1
                    AND
                    o.`id_cart` IN ( " . $carts_ids_common . " )
            ";

        $inside_query_reminder =
            "
                SELECT COUNT(*)
                FROM `" . _DB_PREFIX_ . "orders` o
                WHERE
                    o.valid = 1
                    AND
                    o.`id_cart` IN ( " . $carts_ids_reminder . " )
            ";

        $stats = $this->wt_db->executeS(
            "
                SELECT
                    DATE_FORMAT(l.date_sent, '%Y-%m-%d') date_stat,
                    (
                        SELECT COUNT(l1.id)
                        FROM " . _DB_PREFIX_ . $this->wt_prefix . "log_email l1
                        LEFT JOIN " . _DB_PREFIX_ . $this->wt_prefix . "hash h ON h.id_cart = l1.id_cart
                        WHERE
                            l1.template = '" . $this->wt_mail_tpls['ro'] ."'
                            AND
                            h.date_created = l.date_sent
                    ) nb_common,
                    (
                        SELECT COUNT(l2.id)
                        FROM " . _DB_PREFIX_ . $this->wt_prefix . "log_email l2
                        LEFT JOIN " . _DB_PREFIX_ . $this->wt_prefix . "hash h ON h.id_cart = l2.id_cart
                        WHERE
                            l2.template = '" . $this->wt_mail_tpls['ro_reminder'] . "'
                            AND
                            h.date_created = l.date_sent
                    ) nb_reminder,
                    (" . $inside_query_common . ") nb_used_common,
                    (" . $inside_query_reminder . ") nb_used_reminder
                FROM
                    " . _DB_PREFIX_ . $this->wt_prefix . "log_email l
                WHERE
                    l.date_sent >= DATE_SUB(CURDATE(), INTERVAL '" . pSQL($days) . "' DAY)
                GROUP BY
                    DATE_FORMAT(l.date_sent, '%Y-%m-%d')
            "
        );


        return $stats;
    }

    public function prepareStatsDataToDisplay($days)
    {
        $stats_array = array();

        $stats = $this->getStatsData($days);
        if (!empty($stats)) {
            $types = array('common', 'reminder');

            foreach ($stats as $stat) {
                $stats_array[$stat['date_stat']]['nb_common'] = (int)$stat['nb_common'];
                $stats_array[$stat['date_stat']]['nb_reminder'] = (int)$stat['nb_reminder'];
                $stats_array[$stat['date_stat']]['nb_used_common'] = (int)$stat['nb_used_common'];
                $stats_array[$stat['date_stat']]['nb_used_reminder'] = (int)$stat['nb_used_reminder'];
            }

            $stats_array_keys = array_keys($stats_array);
            foreach ($stats_array_keys as $date_stat) {
                foreach ($types as $type) {
                    $rates = array();
                    if (isset($stats_array[$date_stat]['nb_' . $type]) &&
                        isset($stats_array[$date_stat]['nb_used_' . $type]) &&
                        $stats_array[$date_stat]['nb_used_' . $type] > 0
                    ) {
                        $rates[$date_stat] = number_format(($stats_array[$date_stat]['nb_used_' . $type] /
                                $stats_array[$date_stat]['nb_' . $type]) * 100, 2, '.', '');
                    }

                    $stats_array[$date_stat]['nb_' . $type] = isset($stats_array[$date_stat]['nb_' . $type]) ?
                        (int)$stats_array[$date_stat]['nb_' . $type] : 0;
                    $stats_array[$date_stat]['nb_used_' . $type] = isset($stats_array[$date_stat]['nb_used_' . $type]) ?
                        (int)$stats_array[$date_stat]['nb_used_' . $type] : 0;
                    $stats_array[$date_stat]['rate_' . $type] = isset($rates[$date_stat]) ? $rates[$date_stat] : '0.00';

                    ksort($stats_array[$date_stat]);
                }
            }
        }

        return $stats_array;
    }

    public function insert2DbSessionProductsRecurring($data)
    {
        $user_id = (int)$data['cart']->id_customer;
        $prd = $this->getSessionProductsRecurring();

        if ($user_id && count($prd)) {
            foreach ($prd as $v) {
                $this->wt_db->insert($this->wt_prefix . 'recurring_products', array(
                    'user_id' => $user_id,
                    'product_id' => $v['id'],
                    'id_product_attribute' => $v['id_product_attribute'],
                    'option' => $v['option'],
                    'd' => $v['d'],
                    'quantity' => $v['quantity'],
                    'status' => 1,
                    'date' => time()
                ));
            }
        }
    }

    public function filterProducts($recProducts, $filter)
    {
        if (!count($recProducts)) {
            return false;
        }

        $result = array();

        switch ($filter) {
            case "haveToBeOrderedToday":
                $time_stamp = ($this->test_mode) ? 1468825200 : time();
                break;
            case "haveToBeOrderedYesterday":
                $time_stamp = ($this->test_mode) ? 1468911600 - 86400 : time() - 86400;
                break;
            default:
                return false;
        }

        foreach ($recProducts as $product) {
            switch ($product['option']) {
                case "1":
                    $time_shift = ($time_stamp - (int)$product['date']) / ((int)$product['d'] * 86400);
                    if (($time_shift - floor($time_shift)) * (int)$product['d'] < 1) {
                        $result[] = $product;
                    }
                    break;

                case "2":
                    $day_index_need = date("j", $time_stamp);
                    if ((int)$product['d'] == (int)$day_index_need) {
                        $result[] = $product;
                    }
                    break;
            }
        }

        return count($result) ? $result : false;
    }

    public function isSent($when, $whom, $last_x_days = null, $template = null)
    {
        if (!is_array($whom)) {
            $whom = array($whom);
        }

        $whom = pSQL(implode(',', $whom));

        $where = 'WHERE l.id_customer IN (' . $whom . ') AND ';
        switch ($when) {
            case 'today':
                $where .= 'l.date_sent >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
                break;
            case 'this_week':
                $where .= 'l.date_sent >= DATE_SUB(CURDATE(), INTERVAL ' . date('N') . ' DAY)';
                break;
            case 'this_month':
                $where .= 'l.date_sent >= DATE_SUB(CURDATE(), INTERVAL ' . date('j') . ' DAY)';
                break;
            default:
                if ($last_x_days > 0) {
                    $where .= 'l.date_sent >= DATE_SUB(CURDATE(), INTERVAL ' . pSQL($last_x_days) . ' DAY)';
                } else {
                    $where .= '1 = 0';
                }
                break;
        }
        if (!empty($template)) {
            $where .= " AND template = '" .  pSQL($template) . "'";
        }

        $is_sent = $this->wt_db->executeS(
            "
                SELECT
                    l.id_customer
                FROM
                    " . _DB_PREFIX_ . $this->wt_prefix . "log_email l
                {$where}
            "
        );

        return (!empty($is_sent)) ? array_column($is_sent, 'id_customer') : false;
    }

    public function getMailVars($what, $data = null, $template_path = null)
    {
        switch ($what) {
            case 'site-link':
                return Tools::getProtocol(Tools::usingSecureMode()) . $_SERVER['HTTP_HOST'];

            case 'shop-url':
                return $this->context->link->getPageLink(
                    'index',
                    true,
                    $this->context->language->id,
                    null,
                    false,
                    $this->context->shop->id
                );

            case 'shop-name':
                return Tools::safeOutput(Configuration::get('PS_SHOP_NAME', null, null, $this->context->shop->id));

            case 'products-list':

                if (file_exists($template_path)) {
                    $id_customer = (int)$data['id_customer'];
                    $id_cart = (int)$data['id_cart'];
                    $id_lang = (int)$data['id_lang'];
                    $product_var_tpl = array();
                    $product_var_tpl_list = array();

                    foreach ($data['package'] as $product) {
                        $product_id = (int)$product['product_id'];
                        $id_product_attribute = (int)$product['id_product_attribute'];
                        $qty = $product['quantity'];

                        $price = Product::getPriceStatic(
                            $product_id,
                            false,
                            ($id_product_attribute ? $id_product_attribute : null),
                            6,
                            null,
                            false,
                            true,
                            $qty,
                            false,
                            $id_customer,
                            $id_cart
                        );
                        $price_wt = Product::getPriceStatic(
                            $product_id,
                            true,
                            ($product['id_product_attribute'] ? (int)$product['id_product_attribute'] : null),
                            2,
                            null,
                            false,
                            true,
                            $qty,
                            false,
                            $id_customer,
                            $id_cart
                        );
                        $product_price = Product::getTaxCalculationMethod() == PS_TAX_EXC ?
                            Tools::ps_round($price, 2) : $price_wt;

                        $currency_instance = Currency::getCurrencyInstance($this->context->cart->id_currency);

                        $product_var_tpl = array(
                            'reference' => $product['product_obj']->reference,
                            'name' => Product::getProductName($product_id, $id_product_attribute, $id_lang),
                            'unit_price' => Tools::displayPrice($product_price, $currency_instance, false),
                            'price' => Tools::displayPrice($product_price * $qty, $currency_instance, false),
                            'quantity' => $qty,
                        );

                        $product_var_tpl_list[] = $product_var_tpl;
                    }

                    $this->context->smarty->assign('wt_list_products', $product_var_tpl_list);

                    return $this->context->smarty->fetch($template_path);
                }

                return '';
        }
    }

    public function logEmail($id_customer, $id_cart, $template)
    {
        $values = array(
            'id_customer' => $id_customer,
            'id_cart' => $id_cart,
            'template' => $template,
            'date_sent' => date('Y-m-d H:i:s'),
        );

        $this->wt_db->insert($this->wt_prefix . 'log_email', $values);
    }

    public function triggerCreateMustBeOrderedCarts($when = "today")
    {
        $packages = array();
        $errors = array();
        $errors_msg = array();

        switch ($when) {
            case "today":
                $products_data = $this->filterProducts($this->getAllRecurringProducts(), "haveToBeOrderedToday");
                $template =  $this->wt_mail_tpls['ro'];
                break;
            case "yesterday":
                $products_data = $this->filterProducts($this->getAllRecurringProducts(), "haveToBeOrderedYesterday");
                $template = $this->wt_mail_tpls['ro_reminder'];
                break;
            default:
                return false;
        }

        $all_active_customers = Customer::getCustomers(true);
        if (empty($products_data) || empty($all_active_customers)) {
            return false;
        }
        $all_active_customers_ids = array_column($all_active_customers, 'id_customer');
        $all_today_customers_ids = array_intersect(
            $all_active_customers_ids,
            array_unique(array_column($products_data, 'user_id'))
        );
        $already_got_mails_customers_ids = $this->isSent('today', $all_today_customers_ids, null, $template);

        if (!empty($already_got_mails_customers_ids)) {
            $customers_ids_to_send = array_values(
                array_diff($all_today_customers_ids, $already_got_mails_customers_ids)
            );
        } else {
            $customers_ids_to_send = $all_today_customers_ids;
        }

        if (empty($customers_ids_to_send)) {
            return false;
        }

        foreach ($customers_ids_to_send as $customer_id) {
            foreach ($products_data as $k => $data) {
                if ($customer_id == (int)$data['user_id']) {
                    $packages[$customer_id][] = $data;
                }
            }
        }

        if (!count($packages)) {
            return false;
        }

        foreach ($packages as $customer_id => $package) {

            if (!count($package)) {
                continue;
            }

            $cartObj = $this->createCustomerCart($customer_id);
            $cartHash = $this->setCartHash($cartObj->id);

            $customer = new Customer($customer_id);
            $id_lang = (!empty($customer->id_lang)) ?
                (int)$customer->id_lang : (int)Configuration::get('PS_LANG_DEFAULT');
            $iso = Language::getIsoById($id_lang);
            $skiped = 0;
            foreach ($package as $k => $rp) {
                $product = new Product($rp['product_id']);
                if (empty($product->id)) {
                    $skiped += 1;
                    continue;
                }
                $stock = StockAvailable::getQuantityAvailableByProduct(
                    $rp['product_id'],
                    $rp['id_product_attribute']
                );
                $package[$k]['product_obj'] = $product;
                if ((int) $product->available_for_order == 0 || (int) $product->active == 0 || $stock == 0) {
                    $errors[] = array(
                        'message' =>  sprintf(
                            $this->l("An item (%1s) is no longer available for order. It was removed from your cart."),
                            $product->name[$id_lang]
                        ),
                        'skiped' => 1
                    );
                    continue;
                } elseif ($rp['quantity'] > $stock) {
                    $errors[] = array(
                        'message' => sprintf(
                            $this->l(
                                "An item (%1s) is no longer available in this quantity. Quantity was changed from %2s
                            to %3s."
                            ),
                            $product->name[$id_lang],
                            $rp['quantity'],
                            $stock
                        ),
                        'skiped' => 0
                    );
                    $rp['quantity'] = $stock;
                }

                $cartObj->updateQty($rp['quantity'], $rp['product_id'], $rp['id_product_attribute']);
            }

            if (!empty($errors)) {
                foreach ($errors as $e) {
                    $errors_msg[] = $e['message'];
                    $skiped += $e['skiped'];
                }
            }

            if (count($package) == $skiped) {
                if ($when == "today") {
                    $template = $this->wt_mail_tpls['ro_no_available'];
                } else {
                    continue;
                }
            }

            $is_sent = false;
            $lists_data = array(
                'package' => $package,
                'id_cart' => $cartObj->id,
                'id_customer' => $customer_id,
                'id_lang' => $id_lang,
            );

            $product_list_html = $this->getMailVars(
                'products-list',
                $lists_data,
                $this->wt_directory . '/' . $this->wt_paths['tpl_front'] . 'mails/' . $iso . '/' .
                'recurring_products_list.tpl'
            );

            $product_list_txt = $this->getMailVars(
                'products-list',
                $lists_data,
                $this->wt_directory . '/mails/' . $iso . '/' . 'recurring_products_list.txt'
            );

            $shop_url = $this->getMailVars('shop-url');
            if (count($errors_msg)) {
                array_unshift($errors_msg, $this->l('During the creation of your order some errors are occured :'));
            }
            $errors_msg = implode("<br/>", $errors_msg);
            $template_vars = array(
                '{user_id}' => $customer->id,
                '{email}' => $customer->email,
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{shop_name}' => $this->getMailVars('shop-name'),
                '{site_link}' => $this->getMailVars('site-link'),
                '{products_list}' => $product_list_html,
                '{products_list_txt}' => $product_list_txt,
                '{shop_url}' => $shop_url,
                '{order_link}' => $shop_url . '?wt_ro_hash=' . $cartHash,
                '{errors}' => $errors_msg,
            );

            if (file_exists($this->wt_directory . '/' . $this->wt_paths['mails'] . $iso . '/' . $template . '.txt') &&
                file_exists($this->wt_directory . '/' . $this->wt_paths['mails'] . $iso . '/' . $template . '.html')
            ) {

                $is_sent = Mail::Send(
                    $id_lang,
                    $template,
                    $this->l('Get your recurring order'),
                    $template_vars,
                    $customer->email,
                    null,
                    null,
                    null,
                    null,
                    null,
                    $this->wt_directory . '/' . $this->wt_paths['mails'],
                    false,
                    $this->context->shop->id
                );
            }

            if ($is_sent) {
                $this->logEmail($customer->id, $cartObj->id, $template);
            }
        }
    }

    public function loginCustomer($customer_id)
    {
        $customer = new Customer($customer_id);

        Hook::exec('actionBeforeAuthentication');

        $this->context->cookie->id_compare = isset($this->context->cookie->id_compare) ?
            $this->context->cookie->id_compare : CompareProduct::getIdCompareByIdCustomer($customer->id);
        $this->context->cookie->id_customer = (int)($customer->id);
        $this->context->cookie->customer_lastname = $customer->lastname;
        $this->context->cookie->customer_firstname = $customer->firstname;
        $this->context->cookie->is_guest = $customer->isGuest();
        $this->context->cookie->passwd = $customer->passwd;
        $this->context->cookie->email = $customer->email;
        $this->context->cookie->write();
        $customer->logged = 1;
        $this->context->customer = $customer;

        Hook::exec('actionAuthentication');
    }

    public function checkForProductsAvailability()
    {
        $cartObj = $this->context->cart;
        $products = $cartObj->getProducts();
        $errors = array();

        if (empty($cartObj->id) || !count($products)) {
            return false;
        }

        foreach ($products as $rp) {
            $stock = StockAvailable::getQuantityAvailableByProduct(
                $rp['id_product'],
                $rp['id_product_attribute']
            );
            if ((int) $rp['available_for_order'] == 0 || (int) $rp['active'] == 0 || $stock == 0) {
                $errors[] = array(
                    'message' =>  sprintf(
                        $this->l("An item (%1s) is no longer available for order. It was removed from your cart."),
                        $rp['name']
                    ),
                    'skiped' => 1
                );
                $cartObj->deleteProduct($rp['id_product'], $rp['id_product_attribute']);
            } elseif ($rp['quantity'] > $stock) {
                $errors[] = array(
                    'message' =>  sprintf(
                        $this->l(
                            "An item (%1s) is no longer available in this quantity. Quantity was changed from %2s
                            to %3s."
                        ),
                        $rp['name'],
                        $rp['quantity'],
                        $stock
                    ),
                    'skiped' => 0
                );
                $diff = $rp['quantity'] - $stock;
                $cartObj->updateQty($diff, $rp['id_product'], $rp['id_product_attribute'], false, 'down');
            }
        }

        return $errors;

    }

    public function isDisabledForReccurence($id_product)
    {
        $product = new Product((int)$id_product);
        $categories = $product->getCategories();
        $is_all_disabled = Configuration::get($this->wt_prefix . 'disabled_all_products');
        $is_in_disabled_cat = array_intersect(
            $categories,
            Tools::jsonDecode(Configuration::get($this->wt_prefix . 'disabled_categories'))
        );
        $is_disabled_by_id = in_array(
            $product->id,
            array_column($this->getDisabledForRecurringOrderProducts(), 'id_product')
        );
        if ($is_all_disabled || $is_in_disabled_cat || $is_disabled_by_id) {
            return true;
        }

        return false;
    }

    public function actionGetListProducts($data, &$res)
    {
        $category = new Category((int)Tools::getValue('id_category'), $this->context->language->id);
        $all_cat_prds = $category->getProducts($this->context->language->id, 1, 1000000, 'position');
        $disabled_ro_prds_ids = array_column($this->getDisabledForRecurringOrderProducts(), 'id_product');
        $already_presented_prds = Tools::getValue('already_presented_prds');
        foreach ($all_cat_prds as $k => &$product) {
            if (in_array($product['id_product'], $already_presented_prds)) {
                unset($all_cat_prds[$k]);
                continue;
            }
            if (in_array($product['id_product'], $disabled_ro_prds_ids)) {
                $product['disabled_for_recurring_order'] = true;
            } else {
                $product['disabled_for_recurring_order'] = false;
            }
        }

        $this->context->smarty->assign(array(
            'disabled_products' => $all_cat_prds,
            'category' => $category
        ));

        $res['html'] = $this->display($this->wt_file, $this->wt_paths['tpl_admin'] . 'select_disabled_products.tpl');
        $res['status'] = true;
        unset($data);
    }

    public function actionChangeProductRoAvailability($data, &$res)
    {
        $id_product = Tools::getValue('id_product');
        $disabled = Tools::getValue('disabled');

        if ($disabled == 1) {
            $this->wt_db->insert(
                $this->wt_prefix . 'product_disabled_for_recurrence',
                array('id_product' => pSQL($id_product))
            );
        } else {
            $this->wt_db->delete(
                $this->wt_prefix . 'product_disabled_for_recurrence',
                'id_product =' . pSQL($id_product)
            );
        }

        $res['html'] = '';
        $res['status'] = true;
        unset($data);
    }

    public function hookDisplayHeader($data)
    {
        if (isset($_REQUEST['wt_order_hash']) && Tools::strlen($_REQUEST['wt_order_hash']) == 32) {
            $errors = $this->checkForProductsAvailability();
            if (!empty($errors)) {
                foreach ($errors as $e) {
                    $this->context->controller->errors[] = Tools::displayError($e['message']);
                }
            }
        }

        $this->context->controller->addCSS(array($this->wt_urls['css_front'] . 'jquery.qtip.min.css'));
        $this->context->controller->addCSS(array($this->wt_urls['css_front'] . 'product_price_block.css'));
        $this->context->controller->addJquery();
        $this->context->controller->addJS(array($this->wt_urls['js_front'] . 'jquery.qtip.min.js'));

        switch (Tools::getValue('controller')) {
            case 'order':
            case 'recurringproducts':
            case 'history':
                $this->context->controller->addCSS(array(
                    $this->wt_urls['css_front'] . 'main.css?v=' . rand(1, 10000),
                    $this->wt_urls['css_front'] . 'order_recurring_manager.css',
                    $this->wt_urls['css_front'] . 'user_recurring_products.css'
                ));

                $this->context->controller->addJS(array(
                    $this->wt_urls['js'] . 'wtrecurringorders.js',
                    $this->wt_urls['js_front'] . 'order_recurring_manager.js?v=' . rand(1, 10000),
                    $this->wt_urls['js_front'] . 'recurring_products_manager.js'
                ));

                $this->context->smarty->assign(array(
                    'url_ajax' => $this->context->link->getModuleLink(
                        'wtrecurringorders',
                        'actions',
                        array('ajax' => $this->name)
                    ),
                ));

                return $this->display($this->wt_file, $this->wt_paths['tpl_front'] . 'head.tpl');
        }
    }

    public function hookDisplayBackOfficeHeader($data)
    {
        switch (Tools::getValue('controller')) {
            case 'AdminModules':
                switch (Tools::getValue('configure')) {
                    case $this->name:
                        $this->context->controller->addCSS(array(
                            $this->wt_urls['css_admin'] . 'configure.css'
                        ));

                        $this->context->controller->addJS(array(
                            $this->wt_urls['js'] . 'wtrecurringorders.js',
                            $this->wt_urls['js_admin'] . 'main.js?v=' . rand(1, 100000),
                        ));

                        $this->context->smarty->assign(array(
                            'url_base' => __PS_BASE_URI__,
                            'url_ajax' => $this->context->link->getAdminLink('AdminModules', true) . '&ajax=' .
                                $this->name
                        ));

                        return $this->display($this->wt_file, $this->wt_paths['tpl_admin'] . 'head.tpl');
                }
                break;
        }
    }

    public function hookDisplayShoppingCartFooter($data)
    {
        $cart_prds = $this->getCurrentCartProducts();
        foreach ($cart_prds as $k => $product) {
            if ($this->isDisabledForReccurence($product['id'])) {
                unset($cart_prds[$k]);
            }
        }
        $this->context->smarty->assign(array(
            'is_all_disabled' => Configuration::get($this->wt_prefix . 'disabled_all_products'),
            'cart_products' => $cart_prds,
        ));
        return $this->display($this->wt_file, $this->wt_paths['tpl_front'] . 'order_recurring_manager.tpl');
    }

    public function hookDisplayPaymentTop($data)
    {
        $cart_prds = $this->getCurrentCartProducts();
        foreach ($cart_prds as $k => $product) {
            if ($this->isDisabledForReccurence($product['id'])) {
                unset($cart_prds[$k]);
            }
        }
        $this->context->smarty->assign(array(
            'is_all_disabled' => Configuration::get($this->wt_prefix . 'disabled_all_products'),
            'cart_products' => $cart_prds,
        ));
        return $this->display($this->wt_file, $this->wt_paths['tpl_front'] . 'order_recurring_manager.tpl');
    }

    public function hookActionValidateOrder($data)
    {
        $cart_id = (int)$data['cart']->id;
        $wt_ro_hash = $this->getCartHashById($cart_id);
        if (Tools::strlen($wt_ro_hash) == 32) {
            $this->setCartHash($cart_id, "update", "validated");
        }

        $this->insert2DbSessionProductsRecurring($data);
        $this->context->cookie->__set($this->wt_session_products_recurring_key, '');
    }

    public function hookDisplayCustomerAccount($data)
    {
        $this->context->smarty->assign(array(
            'recurring_products_link' => $this->context->link->getModuleLink('wtrecurringorders', 'recurringproducts'),
            'module_url_images' => $this->wt_urls['images']
        ));

        return $this->display($this->wt_file, $this->wt_paths['tpl_front'] . 'recurring_products_account_button.tpl');
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        /*        $id_product = Tools::getValue('id_product');*/
        $template_path = dirname($this->wt_file) . '/' . $this->wt_paths['tpl_hook'] . 'admin_products_extra.tpl';
        $data = $this->context->smarty->createTemplate($template_path);
        /*
                $prepare_smarty_data = $this->getBackendPreparedProductData($id_product);
                $prepare_smarty_data['images'] = Belvg_360Image::getImages($id_product);
                foreach ($prepare_smarty_data['images'] as $k => $image) {
                    $prepare_smarty_data['images'][$k] = new Belvg_360Image($image['id_belvg_360_image']);
                }

                $data->assign(array(
                    'belvg_360_data' => $prepare_smarty_data,
                ));*/

        return $data->fetch();
    }

    public function hookDisplayProductPriceBlock($params)
    {

        if (!isset($params['product']) || !isset($params['type'])) {
            return;
        }

        $data = Tools::jsonDecode(Configuration::get($this->wt_prefix . 'tooltip_text'), true);

        if (count($data)) {
            $tooltip_text = $data['tooltip_text_' . $this->context->language->id];
        }
        $tooltip_text = (!empty($tooltip_text)) ?
            $tooltip_text : $this->l('You can order this product in a recurrent basis');
        $tooltip_state = Configuration::get($this->wt_prefix . 'tooltip_state');

        if ($params['type'] == 'price' && is_array($params['product'])) {
            $this->context->smarty->assign(array(
                    'tooltip_state' => $tooltip_state,
                    'tooltip_text' => $tooltip_text,
                    'is_disabled' => $this->isDisabledForReccurence($params['product']['id_product'])
            ));

            return $this->display(__FILE__, 'product_price_block.tpl');
        }
    }
}
