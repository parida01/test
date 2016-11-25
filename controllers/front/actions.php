<?php
/**
 * 2016 WeeTeam
 *
 * @author    WeeTeam <info@weeteam.net>
 * @copyright 2016 WeeTeam
 * @license   http://www.gnu.org/philosophy/categories.html (Shareware)
 */

class WtRecurringOrdersActionsModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
    }

    public function init()
    {
        parent::init();
    }

    public function initContent()
    {
        if (Tools::getValue('ajax') == $this->module->name) {
            $res = array('status' => false);
            $action = $this->module->actionMethodFilter();

            if (method_exists($this, $action)) {
                $data = (!empty($_REQUEST['data'])) ? $_REQUEST['data'] : null;
                call_user_func_array(array($this, $action), array($data, &$res));
            }

            exit(Tools::jsonEncode($res));
        }
    }

    public function actionSessionProductsRecurringGet($data, &$res)
    {
        $res['html'] = '';
        $prd = $this->module->getSessionProductsRecurring();

        if (count($prd)) {
            foreach ($prd as $k => $v) {
                $product = $this->module->getProductData($v['id']);
                $recurring = '';

                switch ($v['option']) {
                    case 1:
                        $recurring = sprintf($this->module->l('Order every %1$s day(s)'), $v['d']);
                        break;
                    case 2:
                        $recurring = sprintf($this->module->l('Order on %1$s day of the month'), $v['d']);
                        break;
                    default:
                        continue;
                }

                $this->context->smarty->assign(array(
                    'key' => $k,
                    'id'  => 0,
                    'product' => array(
                        'product_id'           => $product['id'],
                        'id_product_attribute' => $v['id_product_attribute'],
                        'quantity'             => $v['quantity'],
                        'name'                 => $product['name'],
                        'image'                => $product['image'],
                        'link'                 => $product['link'],
                        'recurring'            => $recurring
                    ),
                ));

                $res['html'] .= $this->module->display(
                    $this->module->wt_file,
                    $this->module->wt_paths['tpl_front'] . 'order_recurring_manager_item.tpl'
                );
            }
        }

        $res['status'] = true;
        unset($data);
    }

    public function actionSessionProductsRecurringManager($data, &$res)
    {
        $values_default = array(
            'quantity' => 1,
            'option'   => array(
                1 => array(
                    'checked' => true,
                    'd'       => 21
                ),
                2 => array(
                    'checked' => false,
                    'd'       => 1
                )
            )
        );

        if (empty($res['html'])) {
            $res['html'] = '';
        }

        $caption = $this->module->l('Apply recurrence option for product');

        if (isset($data['data']['key'])) {
            $prd = $this->module->getSessionProductsRecurring();

            if (isset($prd[$data['data']['key']])) {
                $prd = $prd[$data['data']['key']];

                $values_default['quantity'] = $prd['quantity'];

                if (count($values_default['option'])) {
                    $values_default_keys = array_keys($values_default['option']);
                    foreach ($values_default_keys as $k) {
                        $values_default['option'][$k]['checked'] = false;
                    }
                }

                $values_default['option'][$prd['option']]['checked'] = true;
                $values_default['option'][$prd['option']]['d'] = $prd['d'];

                $res['key'] = $data['data']['key'];
                $caption = $this->module->l('Edit product recurrence options');
            }

            unset($prd);
        }

        $this->context->smarty->assign(array(
            'step'           => $data['step'],
            'data'           => $data['data'],
            'caption'        => $caption,
            'values_default' => $values_default
        ));

        switch ((int)$data['step']) {
            case 1:
                $prds = $this->module->getCurrentCartProducts();
                foreach ($prds as $k => $product) {
                    if ($this->module->isDisabledForReccurence($product['id'])) {
                        unset($prds[$k]);
                    }
                }

                $this->context->smarty->assign('cart_products', $prds);

                $res['html'] = $this->module->display(
                    $this->module->wt_file,
                    $this->module->wt_paths['tpl_front'] . 'order_recurring_manager_steps.tpl'
                );
                $res['status'] = true;
                break;
            case 2:
                $this->context->smarty->assign(
                    'product',
                    $this->module->getProductData($data['data']['id'], $data['data']['id_product_attribute'])
                );

                $res['html'] = $this->module->display(
                    $this->module->wt_file,
                    $this->module->wt_paths['tpl_front'] . 'order_recurring_manager_steps.tpl'
                );

                $res['status'] = true;
                break;
            case 3:
                if (isset($data['data']['key'])) {
                    $key = $data['data']['key'];
                } else {
                    $key = false;
                }

                $data['data']['id_product_attribute'] = (!empty($data['data']['id_product_attribute'])) ?
                    $data['data']['id_product_attribute'] : '';

                if ($this->module->setSessionProductsRecurring(array(
                    'id'                   => $data['data']['id'],
                    'id_product_attribute' => $data['data']['id_product_attribute'],
                    'quantity'             => $data['data']['quantity'],
                    'option'               => $data['data']['recurring']['option'],
                    'd'                    => $data['data']['recurring']['params']['d']
                ), $key)
                ) {
                    $product = $this->module->getProductData($data['data']['id']);
                    $res['css'] = 'message success';
                    $res['ok'] = true;
                } else {
                    $res['css'] = 'message error';
                    $res['ok'] = false;
                }

                $res['product'] = $product;
                $res['status'] = true;
                break;
        }
    }

    public function actionSessionProductRecurringRemove($data, &$res)
    {
        $res['status'] = $this->module->removeSessionProductRecurring($data['key']);
    }

    public function actionProductsRecurringGet($data, &$res)
    {
        $user_id = (int)$this->context->customer->id;
        $res['html'] = '';

        $prd = $this->module->getRecurringProducts($user_id);

        if ($user_id && count($prd)) {

            foreach ($prd as $k => $v) {
                $product = $this->module->getProductData($v['product_id']);
                $recurring = '';

                switch ($v['option']) {
                    case 1:
                        $recurring = sprintf($this->module->l('Order every %1$s day(s)'), $v['d']);
                        break;
                    case 2:
                        $recurring = sprintf($this->module->l('Order on %1$s day of the month'), $v['d']);
                        break;
                    default:
                        continue;
                }

                $this->context->smarty->assign(array(
                    'key' => $k,
                    'id'  => $v['id'],
                    'product' => array(
                        'product_id'           => $product['id'],
                        'id_product_attribute' => $v['id_product_attribute'],
                        'quantity'             => $v['quantity'],
                        'name'                 => $product['name'],
                        'image'                => $product['image'],
                        'link'                 => $product['link'],
                        'recurring'            => $recurring,
                        'status'               => (int)$v['status']
                    ),
                ));

                $res['html'] .= $this->module->display(
                    $this->module->wt_file,
                    $this->module->wt_paths['tpl_front'] . 'order_recurring_manager_item.tpl'
                );
            }
        }

        $res['status'] = true;
        unset($data);
    }

    public function actionProductsRecurringManager($data, &$res)
    {
        $user_id = (int)$this->context->customer->id;

        $values_default = array(
            'quantity' => 1,
            'option'   => array(
                1 => array(
                    'checked' => true,
                    'd'       => 21
                ),
                2 => array(
                    'checked' => false,
                    'd'       => 1
                )
            )
        );

        if (empty($res['html'])) {
            $res['html'] = '';
        }

        $caption = $this->module->l('Edit product recurrence options');

        if ($user_id && isset($data['data']['key'])) {
            $prd = $this->module->getRecurringProduct($data['data']['rp_id'], $user_id);

            if (isset($prd['id'])) {
                $values_default['quantity'] = $prd['quantity'];
                if (count($values_default['option'])) {
                    $values_default_keys = array_keys($values_default['option']);
                    foreach ($values_default_keys as $k) {
                        $values_default['option'][$k]['checked'] = false;
                    }
                }

                $values_default['option'][$prd['option']]['checked'] = true;
                $values_default['option'][$prd['option']]['d'] = $prd['d'];

                $values_default['status'] = $prd['status'];

                $res['key'] = $data['data']['key'];
            }

            unset($prd);
        }

        $this->context->smarty->assign(array(
            'step'           => $data['step'],
            'data'           => $data['data'],
            'caption'        => $caption,
            'values_default' => $values_default
        ));

        switch ((int)$data['step']) {
            case 2:
                $this->context->smarty->assign(
                    'product',
                    $this->module->getProductData($data['data']['id'], $data['data']['id_product_attribute'])
                );

                $res['html'] = $this->module->display(
                    $this->module->wt_file,
                    $this->module->wt_paths['tpl_front'] . 'order_recurring_manager_steps.tpl'
                );
                $res['status'] = true;
                break;
            case 3:
                if ($this->module->setProductsRecurring(array(
                    'user_id'  => $user_id,
                    'rp_id'    => $data['data']['rp_id'],
                    'quantity' => $data['data']['quantity'],
                    'option'   => $data['data']['recurring']['option'],
                    'd'        => $data['data']['recurring']['params']['d'],
                    'status'   => $data['data']['status']
                ))
                ) {
                    $product = $this->module->getProductData(
                        $data['data']['id'],
                        $data['data']['id_product_attribute']
                    );

                    $res['css'] = 'message success';
                    $res['product'] = $product;
                    $res['ok'] = true;
                } else {
                    $res['css'] = 'message error';
                    $res['ok'] = false;
                }

                $res['status'] = true;
                break;
        }
    }

    public function actionProductRecurringRemove($data, &$res)
    {
        $user_id = (int)$this->context->customer->id;

        $res['status'] = $this->module->removeProductRecurring($data['rp_id'], $user_id);
    }
}
