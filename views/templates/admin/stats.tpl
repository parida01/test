{*
* 2016 WeeTeam
*
* @author    WeeTeam <info@weeteam.net>
* @copyright 2016 WeeTeam
* @license   http://www.gnu.org/philosophy/categories.html (Shareware)
*}

<div class="panel">
    <h3><i class="icon-bar-chart"></i> {l s='Statistics' mod='wtrecurringorders'}</h3>
    <p>{l s='Detailed statistics for the last %1s days:'|sprintf:$period mod='wtrecurringorders'}</p>
    <ul style="font-size: 10px; font-weight: bold;">
        <li>{l s='Common mails sent = Number of common mails sent' mod='wtrecurringorders'}</li>
        <li>{l s='Reminders sent = Number of reminders sent' mod='wtrecurringorders'}</li>
        <li>{l s='Validated common = Number of validated orders owing to common mails' mod='wtrecurringorders'}</li>
        <li>{l s='Validated reminder = Number of validated orders owing to reminders' mod='wtrecurringorders'}</li>
        <li>{l s='Conversion common % = Conversion rate, Validated common / Common mails sent' mod='wtrecurringorders'}</li>
        <li>{l s='Conversion reminder % = Conversion rate, Validated reminder/ Reminders sent' mod='wtrecurringorders'}</li>
    </ul>
    <table class="table">
        <tr>
            <th class="center">{l s='CRON job execution date' mod='wtrecurringorders'}</th>
            <th class="center">{l s='Common mails sent' mod='wtrecurringorders'}</th>
            <th class="center">{l s='Reminders sent' mod='wtrecurringorders'}</th>
            <th class="center">{l s='Validated common' mod='wtrecurringorders'}</th>
            <th class="center">{l s='Validated reminder' mod='wtrecurringorders'}</th>
            <th class="center">{l s='Conversion common (%)' mod='wtrecurringorders'}</th>
            <th class="center">{l s='Conversion reminder (%)' mod='wtrecurringorders'}</th>
        </tr>
        {foreach from=$stats_array key='date' item='stats'}
            <tr>
                    <td class="center">{$date|escape:'htmlall':'UTF-8'}</td>
                    <td class="center">{$stats.nb_common|escape:'htmlall':'UTF-8'}</td>
                    <td class="center">{$stats.nb_reminder|escape:'htmlall':'UTF-8'}</td>
                    <td class="center">{$stats.nb_used_common|escape:'htmlall':'UTF-8'}</td>
                    <td class="center">{$stats.nb_used_reminder|escape:'htmlall':'UTF-8'}</td>
                    <td class="center"><strong>{$stats.rate_common|escape:'htmlall':'UTF-8'}</strong></td>
                    <td class="center"><strong>{$stats.rate_reminder|escape:'htmlall':'UTF-8'}</strong></td>
            </tr>
            {foreachelse}
            <tr>
                <td colspan="7" class="center"><strong>{l s='No statistics at this time. It seems that no CRON task has been created.' mod='wtrecurringorders'}</strong></td>
            </tr>
        {/foreach}
    </table>
</div>
