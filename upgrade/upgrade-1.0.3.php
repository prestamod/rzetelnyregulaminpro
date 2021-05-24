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

function upgrade_module_1_0_3($module)
{
    $module->registerHook('rzetelnaFirmaRegulamin');
    $module->registerHook('rzetelnaFirmaPolityka');
    return true;
}
