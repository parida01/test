{*
* 2016 WeeTeam
*
* @author    WeeTeam <info@weeteam.net>
* @copyright 2016 WeeTeam
* @license   http://www.gnu.org/philosophy/categories.html (Shareware)
*}

<div class="alert alert-info">
    <p>
        <strong>{l s='This module allows your customers to add recurrent options for products.' mod='wtrecurringorders'}<br/></strong>
    </p>
    <p>
        {l s='When the date of recurrence for a particular product will be reached, appropriate customer will obtain a notification by mail with the invitation to finalize the payment.'  mod='wtrecurringorders'}
    </p>
    <p>
        {l s='Generally, the check for recurring orders must be made every day.' mod='wtrecurringorders'}
    </p>
    <p>
        {l s='For this operation, the module provides three possibilities : use Cronjobs Prestashop module, an Automated CRON Task Solution or a Manual Solution. That said, you can choose the most appropriate solution from the ones shown below.' mod='wtrecurringorders'}<br/>
    </p>
    <p>
        {l s='Also you can edit an email template of e-mail notification' mod='wtrecurringorders'} <a href="{$email_tpl_url|escape:'quotes':'UTF-8'}"><strong>{l s='here' mod='wtrecurringorders'}</strong></a>.
    </p>
</div>
