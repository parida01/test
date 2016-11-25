{*
* 2016 WeeTeam
*
* @author    WeeTeam <info@weeteam.net>
* @copyright 2016 WeeTeam
* @license   http://www.gnu.org/philosophy/categories.html (Shareware)
*}

{capture name=path}
    <a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
        {l s='My account' mod='wtrecurringorders'}
    </a>
    <span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>
    <span class="navigation_page">{l s='Recurring Orders history' mod='wtrecurringorders'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<h1 class="page-heading bottom-indent">{l s='Recurring Orders history' mod='wtrecurringorders'}</h1>
<p class="info-title">{l s='Here are the recurring orders you\'ve placed since your account was created.' mod='wtrecurringorders'}</p>
<div class="wtrecurringorder-tabs">
    <a class="add-shopping-list btn btn-default button button-medium active" href="{$link->getModuleLink('wtrecurringorders', 'history')|escape:'html':'UTF-8'}">
        <i class="fa fa-shopping-cart" aria-hidden="true"></i>
        <span>{l s='Recurring Orders history' mod='wtrecurringorders'}<i class="icon-chevron-right right"></i></span>
    </a>
    <a class="add-shopping-list btn btn-default button button-medium" href="{$link->getModuleLink('wtrecurringorders', 'recurringproducts')|escape:'html':'UTF-8'}">
        <i class="fa fa-shopping-cart" aria-hidden="true"></i>
        <span>{l s='Recurring products' mod='wtrecurringorders'}<i class="icon-chevron-right right"></i></span>
    </a>
</div>
{if $slowValidation}
    <p class="alert alert-warning">{l s='If you have just placed an order, it may take a few minutes for it to be validated. Please refresh this page if your order is missing.' mod='wtrecurringorders'}</p>
{/if}
<div class="block-center table-responsive" id="block-history">
    {if $orders && count($orders)}
        <table id="order-list" class="table table-bordered footab ">
            <thead>
            <tr>
                <th class="first_item" data-sort-ignore="true">{l s='Order reference' mod='wtrecurringorders'}</th>
                <th class="item">{l s='Date' mod='wtrecurringorders'}</th>
                <th data-hide="phone" class="item">{l s='Total price' mod='wtrecurringorders'}</th>
                <th data-sort-ignore="true" data-hide="phone,tablet" class="item">{l s='Payment' mod='wtrecurringorders'}</th>
                <th class="item">{l s='Status' mod='wtrecurringorders'}</th>
                <th data-sort-ignore="true" data-hide="phone,tablet" class="item">{l s='Invoice' mod='wtrecurringorders'}</th>
                <th data-sort-ignore="true" data-hide="phone,tablet" class="last_item">&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$orders item=order name=myLoop}
                <tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{/if}">
                    <td class="history_link bold">
                        {if isset($order->invoice) && $order->invoice && isset($order->virtual) && $order->virtual}
                            <img class="icon" src="{$img_dir|escape:'html':'UTF-8'}icon/download_product.gif"	alt="{l s='Products to download' mod='wtrecurringorders'}" title="{l s='Products to download' mod='wtrecurringorders'}" />
                        {/if}
                        <a class="color-myaccount" href="javascript:showOrder(1, {$order->id|intval}, '{$link->getPageLink('order-detail', true)|escape:'html':'UTF-8'}');">
                            {Order::getUniqReferenceOf($order->id|intval)}
                        </a>
                    </td>
                    <td data-value="{$order->date_add|regex_replace:"/[\-\:\ ]/":""|escape:'html':'UTF-8'}" class="history_date bold">
                        {dateFormat date=$order->date_add full=0}
                    </td>
                    <td class="history_price" data-value="{$order->total_paid|escape:'html':'UTF-8'}">
							<span class="price">
								{displayPrice price=$order->total_paid currency=$order->id_currency no_utf8=false convert=false}
							</span>
                    </td>
                    <td class="history_method">{$order->payment|escape:'html':'UTF-8'}</td>
                    <td{if isset($order->wt_state)} data-value="{$order->wt_state->id|escape:'html':'UTF-8'}"{/if} class="history_state">
                        {if isset($order->wt_state)}
                            <span class="label{if isset($order->wt_state->color) && Tools::getBrightness($order->wt_state->color) > 128} dark{/if}"{if isset($order->wt_state->color) && $order->wt_state->color} style="background-color:{$order->wt_state->color|escape:'html':'UTF-8'}; border-color:{$order->wt_state->color|escape:'html':'UTF-8'};"{/if}>
									{$order->wt_state->name[{$cookie->id_lang|escape:'html':'UTF-8'}]|escape:'html':'UTF-8'}
								</span>
                        {/if}
                    </td>
                    <td class="history_invoice">
                        {if (isset($order->invoice) && $order->invoice && isset($order->invoice_number) && $order->invoice_number) && isset($invoiceAllowed) && $invoiceAllowed == true}
                            <a class="link-button" href="{$link->getPageLink('pdf-invoice', true, NULL, "id_order={$order->id}")|escape:'html':'UTF-8'}" title="{l s='Invoice' mod='wtrecurringorders'}" target="_blank">
                                <i class="fa fa-file-text large"></i>{l s='PDF' mod='wtrecurringorders'}
                            </a>
                        {else}
                            -
                        {/if}
                    </td>
                    <td class="history_detail">
                        <a class="btn btn-default button button-small btn-sm" href="javascript:showOrder(1, {$order->id|intval}, '{$link->getPageLink('order-detail', true)|escape:'html':'UTF-8'}');">
								<span>
									{l s='Details' mod='wtrecurringorders'}
								</span>
                        </a>
                        {if isset($opc) && $opc}
                        <a class="link-button" href="{$link->getPageLink('order-opc', true, NULL, "submitReorder&id_order={$order->id}")|escape:'html':'UTF-8'}" title="{l s='Reorder' mod='wtrecurringorders'}">
                            {else}
                            <a class="link-button" href="{$link->getPageLink('order', true, NULL, "submitReorder&id_order={$order->id}")|escape:'html':'UTF-8'}" title="{l s='Reorder' mod='wtrecurringorders'}">
                                {/if}
                                {if $reorderingAllowed}
                                    <i class="fa fa-refresh"></i>{l s='Reorder' mod='wtrecurringorders'}
                                {/if}
                            </a>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        <div id="block-order-detail" class="unvisible">&nbsp;</div>
    {else}
        <p class="alert alert-warning">{l s='You have not placed any orders with your recurring products. When the date of recurrence for a particular product will be reached, you will be notified by mail and you could place your first recurring order' mod='wtrecurringorders'}</p>
    {/if}
</div>
<ul class="footer_links clearfix">
    <li class="pull-left">
        <a class="btn btn-default button button-small btn-sm" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
			<span>
				<i class="fa fa-user"></i> {l s='Back to Your Account' mod='wtrecurringorders'}
			</span>
        </a>
    </li>
    <li class="pull-right">
        <a class="btn btn-default button button-small btn-sm" href="{$base_dir|escape:'html':'UTF-8'}">
            <span><i class="fa fa-home"></i> {l s='Home' mod='wtrecurringorders'}</span>
        </a>
    </li>
</ul>