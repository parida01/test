{*
* 2016 WeeTeam
*
* @author    WeeTeam <info@weeteam.net>
* @copyright 2016 WeeTeam
* @license   http://www.gnu.org/philosophy/categories.html (Shareware)
*}

<div class="item {if isset($product.status) && $product.status < 1}disabled{/if}" data-product-id="{$product.product_id|escape:'html':'UTF-8'}" data-id-attribute="{$product.id_product_attribute|escape:'html':'UTF-8'}" data-key="{$key|escape:'html':'UTF-8'}" data-rp-id="{$id|escape:'html':'UTF-8'}">
    <div class="inner">
        <div class="image">
            <img src="{$product.image|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}" />
        </div>
        <div class="info">
            <span class="caption">{$product.name|escape:'html':'UTF-8'} ({l s='Qty' mod='wtrecurringorders'}: {$product.quantity|escape:'html':'UTF-8'})</span>
            <span class="data">
                {$product.recurring|escape:'html':'UTF-8'}
            </span>
        </div>
        <div class="remove">
            <a href="#" title="{l s='Remove' mod='wtrecurringorders'}">{l s='Remove' mod='wtrecurringorders'}</a>
        </div>
    </div>
</div>
