{*
* 2016 WeeTeam
*
* @author    WeeTeam <info@weeteam.net>
* @copyright 2016 WeeTeam
* @license   http://www.gnu.org/philosophy/categories.html (Shareware)
*}

{if count($disabled_products)}
    <div class="single-cat cat-id-{$category->id|intval}">
        {foreach from=$disabled_products item=product}
            <div class="col-md-4">
                <div class="checkbox">
                    <label for="product_{$product.id_product|intval}">
                        <input type="checkbox" id="product_{$product.id_product|intval}" name="product[]" value="{$product.id_product|intval}" data-id="{$product.id_product|intval}" {if $product.disabled_for_recurring_order == true} checked {/if}>
                        {$product.name|escape:'html':'UTF-8'}
                    </label>
                </div>
            </div>
        {/foreach}
    </div>
{/if}