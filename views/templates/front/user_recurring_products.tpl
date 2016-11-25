{*
* 2016 WeeTeam
*
* @author    WeeTeam <info@weeteam.net>
* @copyright 2016 WeeTeam
* @license   http://www.gnu.org/philosophy/categories.html (Shareware)
*}
{include file="$tpl_dir./errors.tpl"}
<div id="{$prefix|escape:'html':'UTF-8'}products" class="box">
    <h1 class="page-subheading">{l s='My recurring products' mod='wtrecurringorders'}</h1>
    <div class="wtrecurringorder-tabs">
        <a class="add-shopping-list btn btn-default button button-medium" href="{$link->getModuleLink('wtrecurringorders', 'history')|escape:'html':'UTF-8'}">
            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
            <span>{l s='Recurring orders history' mod='wtrecurringorders'}<i class="icon-chevron-right right"></i></span>
        </a>
        <a class="add-shopping-list btn btn-default button button-medium active" href="{$link->getModuleLink('wtrecurringorders', 'recurringproducts')|escape:'html':'UTF-8'}">
            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
            <span>{l s='Recurring products' mod='wtrecurringorders'}<i class="icon-chevron-right right"></i></span>
        </a>
    </div>
    <div class="products_recurring_list"></div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        window.WTRECURRINGORDERS_products.init.main();
        window.WTRECURRINGORDERS_products.actions.products_recurring_get();
    });
</script>


