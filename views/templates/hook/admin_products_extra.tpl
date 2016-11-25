{*
* 2016 WeeTeam
*
* @author    WeeTeam <info@weeteam.net>
* @copyright 2016 WeeTeam
* @license   http://www.gnu.org/philosophy/categories.html (Shareware)
*}


<input type="hidden" name="submitted_tabs[]" value="Recurring_orders" />

<div class="panel">
    <h3><i class="icon-repeat"></i> {l s='Recurring orders' mod='wtrecurringorders'}</h3>
    <label for="" class="control-label col-lg-3">{l s='Product can be recurrent' mod='wtrecurringorders'}</label>
    <span class="switch prestashop-switch fixed-width-lg col-lg-9">
        <input type="radio" name="erase" id="erase_on" value="1" checked="checked">
        <label for="erase_on" class="radioCheck">
            {l s='Yes' mod='wtrecurringorders'}
        </label>
        <input type="radio" name="erase" id="erase_off" value="0">
        <label for="erase_off" class="radioCheck">
            {l s='No' mod='wtrecurringorders'}
        </label>
        <a class="slide-button btn"></a>
    </span>
    <div class="panel-footer">
        <button type="submit" name="submitRegenerate{$table|escape:'html':'UTF-8'}" class="btn btn-default pull-right" onclick="return confirm('{l s='Are you sure?' mod='wtrecurringorders'}');">
            <i class="process-icon-save"></i> {l s='Save' mod='wtrecurringorders'}
        </button>
    </div>
</div>