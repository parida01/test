<?php
/**
 * 2016 WeeTeam
 *
 * @author    WeeTeam <info@weeteam.net>
 * @copyright 2016 WeeTeam
 * @license   http://www.gnu.org/philosophy/categories.html (Shareware)
 */

$wtro = new WtRecurringOrders();

if (isset($_REQUEST['wt_ro_hash']) && Tools::strlen($_REQUEST['wt_ro_hash']) == 32) {
    ini_set('max_execution_time', -1);
    ini_set('set_time_limit', -1);
    ini_set('memory_limit', -1);

    $cart = $wtro->getCartByHash($_REQUEST['wt_ro_hash']);
    if ($cart instanceof Cart) {
        $wtro->loginCustomer($cart->id_customer);
        $wtro->context->cookie->id_cart = $cart->id;
        $wtro->context->cookie->write();

        Tools::redirect('index.php?controller=order&wt_order_hash=' . $_REQUEST['wt_ro_hash']);
    } else {
        Tools::redirect('index.php');
    }
}
