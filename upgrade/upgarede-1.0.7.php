<?php
/**
* 2007-2019 Presta-Mod.pl Rafał Zontek
*
* NOTICE OF LICENSE
*
* Poniższy kod jest kodem płatnym, rozpowszechanie bez pisemnej zgody autora zabronione
* Moduł można zakupić na stronie Presta-Mod.pl. Modyfikacja kodu jest zabroniona,
* wszelkie modyfikacje powodują utratę warancji
*
* http://presta-mod.pl
*
* DISCLAIMER
*
*
*  @author    Presta-Mod.pl Rafał Zontek <biuro@presta-mod.pl>
*  @copyright 20014-2019 Presta-Mod.pl
*  @license   Licecnja na jedną domenę
*  Presta-Mod.pl Rafał Zontek
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_7($module)
{
    $module->registerHook('displayAdminOrder');
    $module->registerHook('displayFooterProduct');
    $module->registerHook('displayHome');
    $module->registerHook('leftColumn');
    $module->registerHook('rightColumn');
    $module->registerHook('productFooter');

    $sql = 'CREATE TABLE `'._DB_PREFIX_.'order_rzetelny` (id_order int(11), date_add datetime)';
    Db::getInstance()->execute($sql);
    return true;
}
