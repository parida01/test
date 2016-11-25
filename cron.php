<?php
/**
 * 2016 WeeTeam
 *
 * @author    WeeTeam <info@weeteam.net>
 * @copyright 2016 WeeTeam
 * @license   http://www.gnu.org/philosophy/categories.html (Shareware)
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include dirname(__FILE__) . '/../../config/config.inc.php';

if (!class_exists('WtRecurringOrders')) {
    include(dirname(__FILE__) . '/wtrecurringorders.php');
}

$wtrecurringorders = Module::getInstanceByName('wtrecurringorders');

if ($wtrecurringorders && $wtrecurringorders->active) {
    if ($token = Tools::getValue('token')) {

        $wtrecurringorders->triggerCreateMustBeOrderedCarts('today');
        $wtrecurringorders->triggerCreateMustBeOrderedCarts('yesterday');

        echo '<script>';
        echo 'window.history.back();';
        echo '</script>';

        die($wtrecurringorders->l('Please wait, you will be redirected soon ...'));
    }
    die (Tools::displayError($wtrecurringorders->l('Token is wrong')));
} else {
    die (Tools::displayError($wtrecurringorders->l('Wt Recurring Orders module is disabled')));
}
