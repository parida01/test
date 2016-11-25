{*
* 2016 WeeTeam
*
* @author    WeeTeam <info@weeteam.net>
* @copyright 2016 WeeTeam
* @license   http://www.gnu.org/philosophy/categories.html (Shareware)
*}

{if $is_disabled == false}
    <p class='rec-box'>
        <i class='icon-repeat' aria-hidden='true'></i>
    </p>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery('.rec-box').each(function () {
                jQuery(this).closest('.product-container').find('.product-image-container').prepend(this);
            });
            {if $tooltip_state == 1}
                jQuery('.rec-box').qtip({
                    content: {
                        text: "{$tooltip_text|escape:'html':'UTF-8'}"
                    },
                    position: {
                        my: 'bottom left',
                        at: 'top right',
                    },
                    style: {
                        classes: 'qtip-dark qtip-shadow'
                    }
                });
            {/if}
        });
    </script>
{/if}