{*
* 2016 WeeTeam
*
* @author    WeeTeam <info@weeteam.net>
* @copyright 2016 WeeTeam
* @license   http://www.gnu.org/philosophy/categories.html (Shareware)
*}

<div id="{$prefix|escape:'htmlall':'UTF-8'}wrapper">

    <div class="panel" id="fieldset_0">
        <div class="panel-heading">
            <i class="icon-cogs"></i> {l s='Configuration' mod='wtrecurringorders'}
        </div>
        <div class="wt-msg alert-info">
            {l s='To run the check for recurring orders autonomously you need to add a cron task.' mod='wtrecurringorders'}<br/>
            {l s='Please note that if value of variable _COOKIE_KEY_ will be ever changed you will have to re-create CRON task' mod='wtrecurringorders'}
        </div>
        <h4>{l s='Easy Solution (use cronjobs module)' mod='wtrecurringorders'}</h4>
        {if $cronjobs_installed}
            <div class="wt-msg alert-success">
                {l s='"Cronjobs" module is installed and active. You can add the cron task with this target link : ' mod='wtrecurringorders'}
                "{$cron_url|escape:'quotes':'UTF-8'}"
                <a href="{$module_cronjobs_url|escape:'quotes':'UTF-8'}" target="_blank" class="btn btn-default">{l s='Add / Edit CRON Task' mod='wtrecurringorders'}</a>
            </div>
        {else}
            <div class="wt-msg alert-warning">
                {l s='The "cronjobs" module developped by PrestaShop is not installed or disabled. Search for the module and install it in your back office.' mod='wtrecurringorders'}
                <a href="{$admin_module_url|escape:'quotes':'UTF-8'}&module_name=cronjobs" target="_blank" class="btn btn-default">{l s='Search' mod='wtrecurringorders'}</a>
            </div>
        {/if}
        <br/>
        <h4>{l s='OR' mod='wtrecurringorders'} {l s='Advanced Solution (edit your server crontab)' mod='wtrecurringorders'}</h4>
        {l s='Add the following lines to the crontab of your server :' mod='wtrecurringorders'}
        <p style="padding:5px;margin-top:10px;border-radius:5px;border:1px solid #cecece;">
            * */1 * * * curl "{$cron_url|escape:'quotes':'UTF-8'}"
        </p>
        <p>
            {l s='With this line the module will run the check for recurring orders every day. If these are found, messages with the invitation to finalize the payment will be sent to the appropriate customers.' mod='wtrecurringorders'}
        </p>
        <br/>
        <h4>{l s='OR' mod='wtrecurringorders'} {l s='Manual Solution (use browser URL)' mod='wtrecurringorders'}</h4>
        <p>
            {l s='Copy link into your browser or just click on it' mod='wtrecurringorders'}<br/>
            <a href="{$cron_url|escape:'quotes':'UTF-8'}">{$cron_url|escape:'quotes':'UTF-8'}}</a>
        </p>
    </div>

</div>