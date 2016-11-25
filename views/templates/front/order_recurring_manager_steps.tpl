{*
* 2016 WeeTeam
*
* @author    WeeTeam <info@weeteam.net>
* @copyright 2016 WeeTeam
* @license   http://www.gnu.org/philosophy/categories.html (Shareware)
*}


<div id="{$prefix|escape:'html':'UTF-8'}manager_steps">
    {if $step == 1}
        {if count($cart_products)}
            <h3 class="page-subheading">{l s='Apply recurrence option for product' mod='wtrecurringorders'}</h3>
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
    {elseif $step == 2}
        <h3 class="page-subheading">{l s='Edit product recurrence options' mod='wtrecurringorders'}</h3>
        <div class="cart-product" data-id="{$data.id|escape:'html':'UTF-8'}" data-id-attribute="{$data.id_product_attribute|escape:'html':'UTF-8'}" {if !empty($data.rp_id)}data-rp-id="{$data.rp_id|escape:'html':'UTF-8'}"{/if}>
            <a href="{$product.link|escape:'html':'UTF-8'}" target="_blank">
                <span class="image">
                    <img src="{$product.image|escape:'html':'UTF-8'}" />
                </span>
                <span class="data">
                    <span class="name">{$product.name|escape:'html':'UTF-8'}</span>
                    {if count($product.attributes)}
                        <small>
                            {foreach from=$product.attributes item=attribute name="attrs"}
                                {$attribute.group_name|escape:'html':'UTF-8'} : {$attribute.attribute_name|escape:'html':'UTF-8'}{if !$smarty.foreach.attrs.last}, {/if}
                            {/foreach}
                        </small>
                    {/if}
                </span>
            </a>
        </div>
        <div class="product-quantity">
            <label>{l s='Quantity' mod='wtrecurringorders'} <input type="text" name="{$prefix|escape:'html':'UTF-8'}manager[quantity]" value="{$values_default.quantity|escape:'html':'UTF-8'}" /></label>
        </div>

        <div class="options">
            <span class="caption">{l s='Select a type of recurrence for this product' mod='wtrecurringorders'}:</span>
            <div class="option">
                <label><input type="radio" name="{$prefix|escape:'html':'UTF-8'}manager[option]" value="1" {if $values_default.option[1].checked}checked="checked"{/if} /> {l s='Get order every X days' mod='wtrecurringorders'}</label>
            </div>
            <div class="option">
                <label><input type="radio" name="{$prefix|escape:'html':'UTF-8'}manager[option]" value="2" {if $values_default.option[2].checked}checked="checked"{/if} /> {l s='Get orders on certain day of the month' mod='wtrecurringorders'}</label>
            </div>
        </div>
        <div class="option-params op-1">
            <label>{l s='Every' mod='wtrecurringorders'} <input type="text" name="{$prefix|escape:'html':'UTF-8'}manager[params][1][d]" value="{$values_default.option[1].d|escape:'html':'UTF-8'}" /> {l s='days' mod='wtrecurringorders'}</label>
        </div>
        <div class="option-params op-2">
            <label>{l s='Day' mod='wtrecurringorders'} <input type="text" name="{$prefix|escape:'html':'UTF-8'}manager[params][2][d]" value="{$values_default.option[2].d|escape:'html':'UTF-8'}" /> {l s='of each month' mod='wtrecurringorders'}</label>
        </div>
        {if !empty($data.rp_id)}
            <div class="options">
                <label><input type="checkbox" name="{$prefix|escape:'html':'UTF-8'}manager[status]" value="1" {if $values_default.status < 1}checked="checked"{/if} /> {l s='Deactivate recurrence actions for this product' mod='wtrecurringorders'}</label>
            </div>
        {/if}
        <div class="actions">
            <a href="#" class="button button-small btn btn-default prm_cancel" title="{l s='Cancel' mod='wtrecurringorders'}">
                <span>{l s='Cancel' mod='wtrecurringorders'}</span>
            </a>
            <a href="#" class="product_recurring_options button button-small btn btn-default prm_apply" title="{l s='Apply to this product' mod='wtrecurringorders'}">
                <span>{l s='Apply to this product' mod='wtrecurringorders'}<i class="icon-chevron-right right"></i></span>
            </a>
        </div>
    {/if}
</div>
