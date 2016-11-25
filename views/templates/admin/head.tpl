{*
* 2016 WeeTeam
*
* @author    WeeTeam <info@weeteam.net>
* @copyright 2016 WeeTeam
* @license   http://www.gnu.org/philosophy/categories.html (Shareware)
*}

<script type="text/javascript">
    if (typeof(window.WTRECURRINGORDERS) == 'object')
    {
        window.WTRECURRINGORDERS.attr.prefix = '{$prefix|escape:'html':'UTF-8'}';
        window.WTRECURRINGORDERS.url.base = '{$url_base|escape:'html':'UTF-8'}';
        window.WTRECURRINGORDERS.url.ajax = '{$url_ajax|escape:'html':'UTF-8'}';

        jQuery(document).ready(function(){
            window.WTRECURRINGORDERS.init.rich_editor();
        });
    }
</script>
