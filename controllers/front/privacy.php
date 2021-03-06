<?php
/**
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
*/

class RzetelnyRegulaminProPrivacyModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        if (Configuration::get('rr_user_code_pp_'.Context::getContext()->language->id) != '') {
            parent::initContent();

            Media::addJsDef(
                array(
                    'rr_user_code' => Configuration::get('rr_user_code_pp_'.Context::getContext()->language->id),
                )
            );
            if (version_compare(_PS_VERSION_, '1.7.0', '<') === true) {
                $this->setTemplate('terms.tpl');
            } else {
                $this->setTemplate('module:rzetelnyregulaminpro/views/templates/front/privacy-1-7.tpl');
            }
        } else {
            Tools::redirect('?controller=404');
        }
    }
}
