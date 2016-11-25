{*
* 2016 WeeTeam
*
* @author    WeeTeam <info@weeteam.net>
* @copyright 2016 WeeTeam
* @license   http://www.gnu.org/philosophy/categories.html (Shareware)
*}

{foreach $wt_list_products as $product}
    <tr>
        <td style="border:1px solid #D6D4D4;">
            <table class="table">
                <tr>
                    <td width="10">&nbsp;</td>
                    <td>
                        <font size="2" face="Open-sans, sans-serif" color="#555454">
                            {$product['reference']|escape:'htmlall':'UTF-8'}
                        </font>
                    </td>
                    <td width="10">&nbsp;</td>
                </tr>
            </table>
        </td>
        <td style="border:1px solid #D6D4D4;">
            <table class="table">
                <tr>
                    <td width="10">&nbsp;</td>
                    <td>
                        <font size="2" face="Open-sans, sans-serif" color="#555454">
                            <strong>{$product['name']|escape:'htmlall':'UTF-8'}</strong>
                        </font>
                    </td>
                    <td width="10">&nbsp;</td>
                </tr>
            </table>
        </td>
        <td style="border:1px solid #D6D4D4;">
            <table class="table">
                <tr>
                    <td width="10">&nbsp;</td>
                    <td align="right">
                        <font size="2" face="Open-sans, sans-serif" color="#555454">
                            {$product['unit_price']|escape:'htmlall':'UTF-8'}
                        </font>
                    </td>
                    <td width="10">&nbsp;</td>
                </tr>
            </table>
        </td>
        <td style="border:1px solid #D6D4D4;">
            <table class="table">
                <tr>
                    <td width="10">&nbsp;</td>
                    <td align="right">
                        <font size="2" face="Open-sans, sans-serif" color="#555454">
                            {$product['quantity']|escape:'htmlall':'UTF-8'}
                        </font>
                    </td>
                    <td width="10">&nbsp;</td>
                </tr>
            </table>
        </td>
        <td style="border:1px solid #D6D4D4;">
            <table class="table">
                <tr>
                    <td width="10">&nbsp;</td>
                    <td align="right">
                        <font size="2" face="Open-sans, sans-serif" color="#555454">
                            {$product['price']|escape:'htmlall':'UTF-8'}
                        </font>
                    </td>
                    <td width="10">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>
{/foreach}