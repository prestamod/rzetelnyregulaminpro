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

if (!defined('_PS_VERSION_')) {
    exit;
}

class RzetelnyRegulaminPro extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'rzetelnyregulaminpro';
        $this->tab = 'front_office_features';
        $this->version = '1.0.7';
        $this->author = 'RzetelnyRegulamin.pl';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Rzetelny regulamin');
        $this->description = $this->l('A module that adds dedicated website rules and privacy policy');
        $this->confirmUninstall = $this->l('Do you want to uninstall the module');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        $sql = 'CREATE TABLE `'._DB_PREFIX_.'order_rzetelny` (id_order int(11), date_add datetime)';
        return parent::install() &&
            Db::getInstance()->execute($sql) &&
            $this->registerHook('displayAdminOrder') &&
            $this->registerHook('displayFooterProduct') &&
            $this->registerHook('displayHome') &&
            $this->registerHook('displayFooter') &&
            $this->registerHook('filterCmsContent') &&
            $this->registerHook('leftColumn') &&
            $this->registerHook('rightColumn') &&
            $this->registerHook('productFooter') &&
            $this->registerHook('header') &&
            $this->registerHook('moduleRoutes') &&
            $this->registerHook('orderConfirmation') &&
            $this->registerHook('rzetelnaFirmaPolityka') &&
            $this->registerHook('rzetelnaFirmaRegulamin');
    }

    public function uninstall()
    {
        $sql = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'order_rzetelny`';
        
        return $this->unregisterHook('displayFooterProduct') &&
        $this->unregisterHook('displayHome') &&
        $this->unregisterHook('displayFooter') &&
        $this->unregisterHook('filterCmsContent') &&
        $this->unregisterHook('leftColumn') &&
        $this->unregisterHook('rightColumn') &&
        $this->unregisterHook('productFooter') &&
        $this->unregisterHook('header') &&
        $this->unregisterHook('moduleRoutes') &&
        $this->unregisterHook('orderConfirmation') &&
        $this->unregisterHook('rzetelnaFirmaPolityka') &&
        $this->unregisterHook('rzetelnaFirmaRegulamin');
        Db::getInstance()->execute($sql) &&
        parent::uninstall();
    }
    
    public function getContent()
    {
        $old = Module::isInstalled('RzetelnyRegulamin');
        if (((bool)Tools::isSubmit('submitRzetelnyregulaminModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign(
            array(
                'module_dir' => $this->_path,
                'old' => $old,
                'l' => Context::getContext()->link,
                'terms' => Configuration::get('rr_user_code_'.Context::getContext()->language->id),
                'priacy' => Configuration::get('rr_user_code_pp_'.Context::getContext()->language->id),
            )
        );

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }
    
    public function hookDisplayFooter()
    {
        if (Tools::getValue('controller') == 'cms') {
            if (Tools::getValue('id_cms') == Configuration::get('PS_CONDITIONS_CMS_ID') &&
                Configuration::get('rr_user_code_'.Context::getContext()->language->id) != '') {
                if (Tools::isSubmit('content_only')) {
                    $get = array('content_only' => 1);
                } else {
                    $get = array();
                }
                Tools::redirect(Context::getContext()->link->getModuleLink('rzetelnyregulaminpro', 'terms', $get));
            } elseif (Tools::getValue('id_cms') == Configuration::get('rr_cms_policy') &&
                Configuration::get('rr_user_code_pp_'.Context::getContext()->language->id) != '') {
                if (Tools::isSubmit('content_only')) {
                    $get = array('content_only' => 1);
                } else {
                    $get = array();
                }
                Tools::redirect(Context::getContext()->link->getModuleLink('rzetelnyregulaminpro', 'privacy', $get));
            } elseif (Tools::getValue('id_cms') == Configuration::get('rr_cms_conditions') &&
                Configuration::get('rr_user_code_'.Context::getContext()->language->id) != '') {
                if (Tools::isSubmit('content_only')) {
                    $get = array('content_only' => 1);
                } else {
                    $get = array();
                }
                Tools::redirect(Context::getContext()->link->getModuleLink('rzetelnyregulaminpro', 'terms', $get));
            }
        }

        if (Configuration::get('rr_widget') && Configuration::get('rr_user_rrid')) {
            return $this->display(__FILE__, 'footer.tpl');
        }
    }
    
    public function renderForm()
    {
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $fields_form = array();
        $cms_tab = array(0 => array(
            'id' => 0,
            'name' => $this->l('None')
        ));

        foreach (CMS::listCms(Context::getContext()->language->id) as $cms_file) {
            $cms_tab[] = array('id' => $cms_file['id_cms'], 'name' => $cms_file['meta_title']);
        }

        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Configuration'),
            ),
            'tabs' => array(
                'terms' => $this->l('Terms'),
                'privacy' => $this->l('Privacy'),
                'comments' => $this->l('Comments'),
                'widgets' => $this->l('Widget'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'tab' => 'terms',
                    'label' => $this->l('User code for the regulations'),
                    'desc' => $this->l('Enter the code, if you do not have it, the page and widget will not be displayed'),
                    'name' => 'rr_user_code',
                    'lang' => true,
                ),
                array(
                    'type' => 'select',
                    'tab' => 'terms',
                    'label' => $this->l('CMS Page to conditions with privacy policy'),
                    'name' => 'rr_cms_conditions',
                    'options' => array(
                        'query' => $cms_tab,
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'text',
                    'tab' => 'privacy',
                    'label' => $this->l('User code for privacy policy'),
                    'desc' => $this->l('Enter the code, if you do not have it, the page will not be displayed'),
                    'name' => 'rr_user_code_pp',
                    'lang' => true,
                ),
                array(
                    'type' => 'select',
                    'tab' => 'privacy',
                    'label' => $this->l('CMS Page to redirect with privacy policy'),
                    'name' => 'rr_cms_policy',
                    'options' => array(
                        'query' => $cms_tab,
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select',
                    'tab' => 'widgets',
                    'label' => $this->l('The location of the widget'),
                    'name' => 'rr_side',
                    'required' => true,
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'left',
                                'name' => $this->l('Left side')
                            ),
                            array(
                                'id' => 'right',
                                'name' => $this->l('Right side')
                            )
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'text',
                    'tab' => 'comments',
                    'label' => $this->l('User code of the opinion'),
                    'desc' => $this->l('Enter the code, if you do not have it, opinions will not be sent'),
                    'name' => 'rr_user_rrid',
                    'size' => 50,
                ),
                 array(
                    'type' => 'switch',
                    'tab' => 'comments',
                    'label' => $this->l('Send comment request when customer place order'),
                    'name' => 'rr_coment_order',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    )
                ),
                array(
                    'label' => $this->l('Chose order states to send comment request'),
                    'name' => 'rr_oo_state',
                    'type' => 'checkbox',
                    'tab' => 'comments',
                    'name' => 'rr_order_states',
                    'values' => array(
                        'id' => 'id_order_state',
                        'name' => 'name',
                        'query' => OrderState::getOrderStates($this->context->language->id)
                    )
                ),
                array(
                    'type' => 'switch',
                    'tab' => 'widgets',
                    'label' => $this->l('Display the widget'),
                    'name' => 'rr_widget',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    )
                ),
                array(
                    'type' => 'select',
                    'tab' => 'comments',
                    'label' => $this->l('Number of days to send a request for review'),
                    'name' => 'rr_ro_dni',
                    'class' => 'col-md-3',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => '5',
                                'name' => $this->l('5')
                            ),
                            array(
                                'id' => '7',
                                'name' => $this->l('7')
                            ),
                            array(
                                'id' => '10',
                                'name' => $this->l('10')
                            )
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'switch',
                    'tab' => 'comments',
                    'label' => $this->l('Display comments in home footer '),
                    'name' => 'rr_home_comments',
                    'class' => 'col-md-3',
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    )
                ),
                array(
                    'type' => 'select',
                    'tab' => 'comments',
                    'label' => $this->l('Show comments widget in column'),
                    'name' => 'rr_column_widget',
                    'class' => 'col-md-3',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => '-',
                                'name' => $this->l('--')
                            ),
                            array(
                                'id' => 'left',
                                'name' => $this->l('Left column')
                            ),
                            array(
                                'id' => 'right',
                                'name' => $this->l('Right column')
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        $helper = new HelperForm();
        
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submitRzetelnyregulaminModule';
        $helper->tpl_vars = array(
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        $helper->fields_value = $this->getConfigForm();
        return $helper->generateForm($fields_form);
    }

    public function getConfigForm()
    {
        $configuration = array();
        foreach (Language::getLanguages() as $lang) {
            $configuration['rr_user_code'][$lang['id_lang']] = Configuration::get(
                'rr_user_code_'.$lang['id_lang']
            );
            $configuration['rr_user_code_pp'][$lang['id_lang']] = Configuration::get(
                'rr_user_code_pp_'.$lang['id_lang']
            );
        }
        $configuration['rr_widget'] = Configuration::get('rr_widget');
        $configuration['rr_side'] = Configuration::get('rr_side');
        $configuration['rr_offset_y'] = (int)Configuration::get('rr_offset_y');
        $configuration['rr_user_rrid'] = Configuration::get('rr_user_rrid');
        $configuration['rr_ro_dni'] = Configuration::get('rr_ro_dni', '5');
        $configuration['rr_cms_policy'] = Configuration::get('rr_cms_policy', '5');
        $configuration['rr_cms_conditions'] = Configuration::get('rr_cms_conditions');
        $configuration['rr_column_widget'] = Configuration::get('rr_column_widget');
        $configuration['rr_coment_order'] = Configuration::get('rr_coment_order');
        $configuration['rr_home_comments'] = Configuration::get('rr_home_comments');
        $configuration['rr_product_comments'] = Configuration::get('rr_product_comments');
        $order_states = explode(',', Configuration::get('rr_order_states'));
        if (is_array($order_states) && $order_states) {
            foreach ($order_states as $order_state) {
                $configuration['rr_order_states_'.$order_state] = 1;
            }
        }
        return $configuration;
    }
    
    private function postProcess()
    {
        if (Tools::isSubmit('submitRzetelnyregulaminModule')) {
            foreach (Language::getLanguages() as $lang) {
                Configuration::updateValue(
                    'rr_user_code_'.$lang['id_lang'],
                    Tools::getValue('rr_user_code_'.$lang['id_lang'], null)
                );
                Configuration::updateValue(
                    'rr_user_code_pp_'.$lang['id_lang'],
                    Tools::getValue('rr_user_code_pp_'.$lang['id_lang'], null)
                );
            }
            Configuration::updateValue('rr_user_code', Tools::getValue('rr_user_code', null));
            Configuration::updateValue('rr_user_code_pp', Tools::getValue('rr_user_code_pp', null));
            Configuration::updateValue('rr_widget', Tools::getValue('rr_widget', false));
            Configuration::updateValue('rr_side', Tools::getValue('rr_side', 'left'));
            Configuration::updateValue('rr_offset_y', Tools::getValue('rr_offset_y', '150'));
            Configuration::updateValue('rr_ro_dni', Tools::getValue('rr_ro_dni', '5'));
            Configuration::updateValue('rr_user_rrid', Tools::getValue('rr_user_rrid'));
            Configuration::updateValue('rr_cms_policy', Tools::getValue('rr_cms_policy'));
            Configuration::updateValue('rr_cms_conditions', Tools::getValue('rr_cms_conditions'));
            Configuration::updateValue('rr_column_widget', Tools::getValue('rr_column_widget'));
            Configuration::updateValue('rr_coment_order', Tools::getValue('rr_coment_order'));
            Configuration::updateValue('rr_home_comments', Tools::getValue('rr_home_comments'));
            Configuration::updateValue('rr_product_comments', Tools::getValue('rr_product_comments'));
            $order_states = array();
            foreach (OrderState::getOrderStates($this->context->language->id) as $order_state) {
                if (Tools::getValue('rr_order_states_'.$order_state['id_order_state']) == 'on') {
                    $order_states[] = $order_state['id_order_state'];
                }
            }
            Configuration::updateValue('rr_order_states', implode(',', $order_states));
        }

        return true;
    }

    public function hookModuleRoutes($params)
    {
        $my_routes = array(
            'module-'.$this->name.'-terms' => array(
                'controller' => 'terms',
                'rule' => 'regulamin-sklepu',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => $this->name,
                ),
            ),
            'module-'.$this->name.'-privacy' => array(
                'controller' => 'privacy',
                'rule' => 'polityka-prywatnosci',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => $this->name,
                ),
            ),
        );

        return $my_routes;
    }

    public function hookOrderConfirmation($params)
    {
        if (!Configuration::get('rr_coment_order')) {
            return '';
        }
        if (Configuration::get('rr_user_rrid') != '') {
            $order_id = 0;
            $email = '';
            if (isset($params['objOrder'])) {
                $order_id = $params['objOrder']->reference;
                $customer = new Customer($params['objOrder']->id_customer);
                $email = $customer->email;
            }
            if (isset($params['order'])) {
                $order_id = $params['order']->reference;
                $customer = new Customer($params['order']->id_customer);
                $email = $customer->email;
            }

            Context::getContext()->smarty->assign(
                array(
                    'order_id' => $order_id,
                    'ro_email' => $email,
                    'rr_user_rrid' => Configuration::get('rr_user_rrid'),
                    'rr_ro_dni' => Configuration::get('rr_ro_dni')
                )
            );
            if ($email != '' && $order_id && Configuration::get('rr_user_rrid') != '') {
                $this->insertOrder($params['order']->id);
                return $this->display(__FILE__, 'order-confirmation.tpl');
            }
        }
    }

    public function hookHeader()
    {
        if (Configuration::get('rr_user_code_'.Context::getContext()->language->id) != '') {
            if (version_compare(_PS_VERSION_, '1.7.0', '>') === true) {
                Context::getContext()->smarty->assign(
                    array(
                        'rr_footer_user_code' => Configuration::get(
                            'rr_user_code_'.Context::getContext()->language->id
                        ),
                        'rr_offset_y' => 150,
                        'rr_side' => Configuration::get('rr_side') ? Configuration::get('rr_side') : 'left'
                    )
                );
                return $this->display(__FILE__, '17script.tpl');
            } else {
                Media::addJsDef(
                    array(
                    'rr_footer_user_code ' => Configuration::get(
                        'rr_user_code_'.Context::getContext()->language->id
                    ),
                    'rr_offset_y' => 150,
                    'rr_side' => Configuration::get('rr_side') ? Configuration::get('rr_side') : 'left'
                    )
                );
            }
        }
    }

    public function hookRzetelnaFirmaRegulamin()
    {
        Media::addJsDef(
            array(
                'rr_user_code' => Configuration::get('rr_user_code_pp_'.Context::getContext()->language->id),
            )
        );
        if (version_compare(_PS_VERSION_, '1.7.0', '<') === true) {
            return $this->context->smarty->fetch(
                dirname(__FILE__).'/views/templates/front/terms.tpl'
            );
        } else {
            return $this->context->smarty->fetch(
                dirname(__FILE__).'/module:rzetelnyregulaminpro/views/templates/front/terms-1-7.tpl'
            );
        }
    }

    public function hookRzetelnaFirmaPolityka()
    {
        Media::addJsDef(
            array(
                'rr_user_code' => Configuration::get('rr_user_code_pp_'.Context::getContext()->language->id),
            )
        );
        if (version_compare(_PS_VERSION_, '1.7.0', '<') === true) {
            return $this->smarty->fetch(dirname(__FILE__).'/views/templates/front/privacy.tpl');
        } else {
            return $this->smarty->fetch(
                dirname(__FILE__).'/module:rzetelnyregulaminpro/views/templates/front/privacy-1-7.tpl'
            );
        }
    }

    public function hookFilterCmsContent(&$params)
    {
        if (Tools::getValue('id_cms') == Configuration::get('rr_cms_policy') &&
            Configuration::get('rr_user_code_pp_'.Context::getContext()->language->id) != ''
        ) {
            $this->context->smarty->assign(
                array(
                    'rr_user_code' => Configuration::get('rr_user_code_pp_'.Context::getContext()->language->id),
                )
            );

            $params['object']['content'] = $this->context->smarty->fetch(
                dirname(__FILE__).'/views/templates/hook/privacy-1-7.tpl'
            );
            return $params;
        } elseif (Tools::getValue('id_cms') == Configuration::get('rr_cms_conditions') &&
            Configuration::get('rr_user_code_'.Context::getContext()->language->id) != ''
         ) {
            $this->context->smarty->assign(
                array(
                    'rr_user_code' => Configuration::get('rr_user_code_'.Context::getContext()->language->id),
                )
            );
            $params['object']['content'] = $this->context->smarty->fetch(
                dirname(__FILE__).'/views/templates/hook/terms-1-7.tpl'
            );
        
            return $params;
        }
    }

    public function hookDisplayHome($params)
    {
        if (Configuration::get('rr_user_code_'.$this->context->language->id) != '') {
            $this->context->smarty->assign('rr_user_code', Configuration::get('rr_user_code_'.$this->context->language->id));
            return $this->display(__FILE__, 'home.tpl');
        }
    }

    public function displayColumn()
    {
        if (Configuration::get('rr_user_code_'.$this->context->language->id) != '') {
            $this->context->smarty->assign('rr_user_code', Configuration::get('rr_user_code_'.$this->context->language->id));
            return $this->display(__FILE__, 'column.tpl');
        }
    }

    public function hookLeftColumn($params)
    {
        if (Configuration::get('rr_column_widget') == 'left') {
            return $this->displayColumn();
        }
    }

    public function hookRightColumn($params)
    {
        if (Configuration::get('rr_column_widget') == 'right') {
            return $this->displayColumn();
        }
    }

    public function hookDisplayFooterProduct($params)
    {
        if (Configuration::get('rr_user_code_'.$this->context->language->id) != '') {
            if (Configuration::get('rr_home_comments')) {
                $this->context->smarty->assign('rr_user_code', Configuration::get('rr_user_code_'.$this->context->language->id));
                return $this->display(__FILE__, 'product_footer.tpl');
            }
        }
    }

    public function insertOrder($order_id)
    {
        $sql = 'INSERT INTO `'._DB_PREFIX_.'order_rzetelny` (id_order, date_add) VALUES ('.(int)$order_id.',now())';
        Db::getInstance()->execute($sql);
    }

    public function sendOrder($order_id)
    {
        $sql = 'SELECT count(*) FROM `'._DB_PREFIX_.'order_rzetelny` WHERE id_order = '.(int)$order_id;
        $sended = !(int)Db::getInstance()->getValue($sql);
        $order_states = explode(',', Configuration::get('rr_order_states'));
        if (is_array($order_states) && sizeof($order_states) && !$sended) {
            if (Configuration::get('rr_user_rrid') != '') {
                $order = new Order($order_id);
                if (!in_array($order->current_state, $order_states)) {
                    return '';
                }
                if (isset($order)) {
                    $order_reference = $order->reference;
                    $customer = new Customer($order->id_customer);
                    $email = $customer->email;
                }

                Context::getContext()->smarty->assign(
                    array(
                        'order_id' => $order_reference,
                        'ro_email' => $email,
                        'rr_user_rrid' => Configuration::get('rr_user_rrid'),
                        'rr_ro_dni' => Configuration::get('rr_ro_dni')
                    )
                );
                if ($email != '' && $order_id) {
                    $this->insertOrder($order_id);
                    return $this->display(__FILE__, 'order-confirmation.tpl');
                }
            }
        }
    }

    public function hookDisplayAdminOrder($params)
    {
        if (isset($params['id_order'])) {
            $id_order = $params['id_order'];
        } elseif (Tools::isSubmit('id_order')) {
            $id_order = Tools::getValue('id_order');
        } else {
            return;
        }
        return $this->sendOrder($id_order);
    }
}
