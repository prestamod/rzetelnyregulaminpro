{*
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script type="text/javascript">
    var rr_footer_user_code = '{$rr_footer_user_code|escape:'htmlall':'UTF-8'}';
    var rr_offset_y = '{$rr_offset_y|escape:'htmlall':'UTF-8'}';
    var rr_side = '{$rr_side|escape:'htmlall':'UTF-8'}';
    (function () {
        var rrid = rr_footer_user_code;
        {literal}
        _rrConfig = {'yOffset': rr_offset_y, 'xSide': rr_side};
        var _rr = document.createElement('script');
        _rr.type = 'text/javascript';
        _rr.src = '//www.rzetelnyregulamin.pl/pl/widget,kod-' + rrid + '';
        var __rr = document.getElementsByTagName('script')[0];
        __rr.parentNode.insertBefore(_rr, __rr);
        {/literal}
    })();
</script>