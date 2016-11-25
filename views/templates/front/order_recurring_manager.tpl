{*
* 2016 WeeTeam
*
* @author    WeeTeam <info@weeteam.net>
* @copyright 2016 WeeTeam
* @license   http://www.gnu.org/philosophy/categories.html (Shareware)
*}

<div id="{$prefix|escape:'html':'UTF-8'}manager" class="box">
    <h3 class="page-subheading">{l s='Products recurring options' mod='wtrecurringorders' }</h3>

    <div class="cart_products_list">
        {if count($cart_products)}
            <ul class="cart-products-list">
                {foreach from=$cart_products item=product}
                    <li class="item" data-id="{$product.id|escape:'html':'UTF-8'}" data-id-attribute="{$product.id_product_attribute|escape:'html':'UTF-8'}">
                        <div class="image">
                            <img src="{$product.image|escape:'html':'UTF-8'}" />
                        </div>
                        <div class="data">
                            <span class="name">{$product.name|escape:'html':'UTF-8'}</span>
                            {if $product.attributes}<small>{$product.attributes|escape:'html':'UTF-8'}</small>{/if}
                        </div>
                    </li>
                {/foreach}
            </ul>
        {/if}
    </div>
    {*<p>
        <a href="#" class="product_recurring_options button button-small btn btn-default"
           title="{l s='Choose a product for recurring options' mod='wtrecurringorders' }">
            <span>
                {l s='Choose a product for recurring options' mod='wtrecurringorders' }
                <i class="icon-chevron-right right"></i></span>
        </a>
    </p>*}
    <div class="products_recurring_list"></div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function () {
        window.WTRECURRINGORDERS_manager.init.main();
        window.WTRECURRINGORDERS_manager.actions.session_products_recurring_get();
    });
</script>