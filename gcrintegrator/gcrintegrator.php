<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class GCRintegrator extends Module
{
    public function __construct()
    {
        $this->name = 'gcrintegrator';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Hacc';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Google Customer Reviews Integration');
        $this->description = $this->l('Google Customer Reviews Integration plugin');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        return parent::install() &&
        $this->registerHook('displayHeader') &&
        $this->registerHook('displayOrderConfirmation') &&
        $this->registerHook('displayGCRbage') &&
        Configuration::updateValue('gcrintegrator_merchant_id', '') &&
        Configuration::updateValue('gcrintegrator_EstimatedDeliveryDays', 0) &&
        Configuration::updateValue('gcrintegrator_OPT_IN_STYLE', 'CENTER_DIALOG') &&
        Configuration::updateValue('gcrintegrator_GtinProvided', '') &&
        Configuration::updateValue('gcrintegrator_GtinDataSource', 0);
    }

    public function uninstall()
    {
        Configuration::deleteByName('gcrintegrator_merchant_id');
        Configuration::deleteByName('gcrintegrator_EstimatedDeliveryDays');
        Configuration::deleteByName('gcrintegrator_OPT_IN_STYLE');
        Configuration::deleteByName('gcrintegrator_GtinProvided');
        Configuration::deleteByName('gcrintegrator_GtinDataSource');
        return parent::uninstall();
    }

    public function hookDisplayHeader($params)
    {
        return $this->context->smarty->fetch($this->local_path.'views/templates/hook/header.tpl');
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submit'.$this->name)) {
            switch ($this->postProcess()) {
                case 0:
                    $output .= $this->displayConfirmation($this->l('Settings updated'));
                    break;
                case 1:
                    $output .= $this->displayError($this->l('The entered estimated delivery days isn\'t an integer or smaller than 0'));
                    break;
                default:
                    $output .= $this->displayError($this->l('Unknown error'));
            }
        }
        return $output.$this->displayForm();
    }

    public function displayForm()
    {
        $gtinProvided = array(
            array(
                'id_option' => 'GtinProvided',
                'name' => $this->l('Send GTIN to Google')
            ),
        );
        $opt_in_style = array(
            array(
                'id_option' => 'CENTER_DIALOG',
                'name' => 'Center Dialog'
            ),
            array(
                'id_option' => 'BOTTOM_RIGHT_DIALOG',
                'name' => 'Bottom Right Dialog'
            ),
            array(
                'id_option' => 'BOTTOM_LEFT_DIALOG',
                'name' => 'Bottom Left Dialog'
            ),
            array(
                'id_option' => 'TOP_RIGHT_DIALOG',
                'name' => 'Top Right Dialog'
            ),
            array(
                'id_option' => 'TOP_LEFT_DIALOG',
                'name' => 'Top Left Dialog'
            ),
            array(
                'id_option' => 'BOTTOM_TRAY',
                'name' => 'Bottom Tray'
            ),
        );
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Google Merchant ID'),
                        'class' => 't',
                        'name' => 'gcrintegrator_merchant_id'
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Estimated Delivery Days'),
                        'class' => 't',
                        'name' => 'gcrintegrator_EstimatedDeliveryDays'
                    ),
                    array(
                        'type' => 'checkbox',
                        'name' => 'gcrintegrator',
                        'class' => 't',
                        'values' => array(
                            'query' => $gtinProvided,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Customer survey opt-in style'),
                        'desc' => $this->l('Choose a customer survey opt-in pop-up style'),
                        'name' => 'gcrintegrator_OPT_IN_STYLE',
                        'options' => array(
                            'query' => $opt_in_style,
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save')
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')
                : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit'.$this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->fields_value['gcrintegrator_merchant_id'] = Configuration::get('gcrintegrator_merchant_id');
        $helper->fields_value['gcrintegrator_EstimatedDeliveryDays'] = Configuration::get('gcrintegrator_EstimatedDeliveryDays');
        $isGtinProvided = Configuration::get('gcrintegrator_GtinProvided');
        $helper->fields_value['gcrintegrator_GtinProvided'] = $isGtinProvided;
        $helper->fields_value['gcrintegrator_OPT_IN_STYLE'] = Configuration::get('gcrintegrator_OPT_IN_STYLE');

        if ($isGtinProvided == 'on') {
            $choseDataSource = array(
                'type' => 'radio',
                'label' => $this->l('Chose GTIN Data Source'),
                'name' => 'gcrintegrator_GtinDataSource',
                'class' => 't',
                'values' => array(
                    array(
                        'id' => 'EAN-13',
                        'value' => 0,
                        'label' => $this->l('EAN-13 or JAN')
                    ),
                    array(
                        'id' => 'ISBN',
                        'value' => 1,
                        'label' => $this->l('ISBN')
                    ),
                    array(
                        'id' => 'UPC',
                        'value' => 2,
                        'label' => $this->l('UPC')
                    ),
                ),
            );
            array_splice($fields_form['form']['input'], 3, 0, array($choseDataSource));
            $helper->fields_value['gcrintegrator_GtinDataSource'] = Configuration::get('gcrintegrator_GtinDataSource');
        }

        return $helper->generateForm(array($fields_form));
    }

    protected function postProcess()
    {
        $EstimatedDeliveryDays = Tools::getValue('gcrintegrator_EstimatedDeliveryDays');
        if ( !ctype_digit($EstimatedDeliveryDays) ) {
            return 1;
        }

        Configuration::updateValue('gcrintegrator_EstimatedDeliveryDays', $EstimatedDeliveryDays);
        Configuration::updateValue('gcrintegrator_merchant_id', Tools::getValue('gcrintegrator_merchant_id'));
        $isGtinProvided_old = Configuration::get('gcrintegrator_GtinProvided');
        $isGtinProvided = Tools::getValue('gcrintegrator_GtinProvided');
        Configuration::updateValue('gcrintegrator_GtinProvided', $isGtinProvided);
        Configuration::updateValue('gcrintegrator_OPT_IN_STYLE', Tools::getValue('gcrintegrator_OPT_IN_STYLE'));

        if ($isGtinProvided_old == 'on' && $isGtinProvided == 'on') {
            Configuration::updateValue('gcrintegrator_GtinDataSource', Tools::getValue('gcrintegrator_GtinDataSource'));
        }

        return 0;
    }

    public function hookDisplayGCRbage($params)
    {
        $this->context->smarty->assign('MERCHANT_ID', Configuration::get('gcrintegrator_merchant_id'));
        return $this->context->smarty->fetch($this->local_path.'views/templates/hook/GCRbage.tpl');
    }

    public function hookDisplayOrderConfirmation($params)
    {
        $order = $params['order'];
        if ( !($order instanceof Order) ) {
            return;
        }
        $EstimatedDeliveryDate = new DateTime($order->date_add);
        $EstimatedDeliveryDays = (int)Configuration::get('gcrintegrator_EstimatedDeliveryDays');
        if ($EstimatedDeliveryDays > 0) {
            $EstimatedDeliveryDate->add(new DateInterval('P' . $EstimatedDeliveryDays . 'D'));
        }
        $formattedEstimatedDeliveryDate = $EstimatedDeliveryDate->format('Y-m-d');

        $customer = new Customer($order->id_customer);
        if ( !($customer instanceof Customer) ) {
            return;
        }

        $addressId = $order->id_address_delivery;
        $address = new Address((int)$addressId);
        $countryId = $address->id_country;
        $country = new Country((int)$countryId);

        $smartyArgs = array(
            'MERCHANT_ID' => Configuration::get('gcrintegrator_merchant_id'),
            'ORDER_ID' => $order->reference,
            'CUSTOMER_EMAIL' => $customer->email,
            'COUNTRY_CODE' => $country->iso_code,
            'EstimatedDeliveryDate' => $formattedEstimatedDeliveryDate,
            'OPT_IN_STYLE' => Configuration::get('gcrintegrator_OPT_IN_STYLE'),
            'GtinProvided' => false
        );

        $gtins = [];
        if (Configuration::get('gcrintegrator_GtinProvided') == 'on') {
            $orderDetails = $order->getOrderDetailList();
            $gtinDataSource = Configuration::get('gcrintegrator_GtinDataSource');
            foreach ($orderDetails as $detail) {
                $productId = $detail['product_id'];
                $product = new Product((int)$productId);
    
                $gtin = '';
                switch ($gtinDataSource) {
                    case 0:
                        $gtin = $product->ean13;
                        break;
                    case 1:
                        $gtin = $product->isbn;
                        break;
                    case 2:
                        $gtin = $product->upc;
                        break;
                    default:
                        $gtin = '';
                }
                if (!empty($gtin)) {
                    $gtins[] = $gtin;
                }
            }

            if (!empty($gtins)) {
                $smartyArgs['GtinProvided'] = true;
                $smartyArgs['GTINs'] = $gtins;
            }
        }

        $this->context->smarty->assign($smartyArgs);
        return $this->context->smarty->fetch($this->local_path.'views/templates/hook/OrderConfirmation.tpl');
    }

}
