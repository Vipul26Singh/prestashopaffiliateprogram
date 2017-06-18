<?php

if(file_exists("./amazonAPI.php")){
                        include("./amazonAPI.php");
              }

if (!defined('_PS_VERSION_'))
exit;

class BitibeAffiliateProgram extends Module
{
	private $amazon_allowed = false;
	private $class_module_name = 'bitibeaffiliateprogram'; 

	public function __construct()
	{
		$this->name = $this->class_module_name;
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'Bitibe';
		$this->need_instance = 0;
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('BitibeAffiliateProgram');
		$this->description = $this->l('Affilite programs wirh e-commerce');

		$this->confirmUninstall = $this->l('Are you sure... you will not be able to add affiliaate program');

		$config = Configuration::getMultiple(array(
					'Bitibe_amazon_access_key',
					'Bitibe_amazon_secret_key',
					'Bitibe_amazon_affiliate_id'
					));

		if (array_key_exists('Bitibe_amazon_access_key', $config)  
				&& array_key_exists('Bitibe_amazon_secret_key', $config) 
				&& array_key_exists('Bitibe_amazon_affiliate_id', $config)
			)
		{
			if (empty($config['Bitibe_amazon_access_key']))	
				$this->warning = $this->l('Please provide Amazon Access Key');

			if (empty($config['Bitibe_amazon_secret_key']))
				$this->warning = $this->l('Please provide Amazon Secret Key');

			if (empty($config['Bitibe_amazon_affiliate_id']))
                                $this->warning = $this->l('Please provide Affiliate Id');


		}else{
			$this->warning = $this->l('Missing configuration. Please configure module ' . $this->class_module_name);
		}

		if(file_exists("./amazonAPI.php")){
        		$this->amazon_allowed = true;
		}


	}

	public function install()
	{
		if (Shop::isFeatureActive())
			Shop::setContext(Shop::CONTEXT_ALL);


		return parent::install() && $this->installTables() &&
			Configuration::updateValue('Bitibe_amazon_access_key', '') &&
			Configuration::updateValue('Bitibe_amazon_secret_key', '') &&
			Configuration::updateValue('Bitibe_amazon_affiliate_id', '');
	}


	private function installTables(){
		$result = Db::getInstance()->execute('
				CREATE TABLE if not exists `'._DB_PREFIX_.'affiliate_category`( 
					`category_name` VARCHAR(100) NOT NULL,
					`site_name` VARCHAR(100) NOT NULL,
					`category_id` VARCHAR(100) NULL,
					PRIMARY KEY (`site_name`, `category_name`)
					) DEFAULT CHARSET=utf8;');

		if(!$result){
			return $result;
		}

		$sql_add_asin        = 'ALTER TABLE `'._DB_PREFIX_.'product` ADD COLUMN `amazon_asin` VARCHAR(100) NULL';
		$result =  Db::getInstance()->query($sql_add_asin);

		$sql_add_site =   'ALTER TABLE `'._DB_PREFIX_.'product` ADD COLUMN `affiliate_website` VARCHAR(100) NULL';
		$result = Db::getInstance()->query($sql_add_site);

		$sql_add_index = 'ALTER TABLE `'._DB_PREFIX_.'product` ADD INDEX `bitibe_amazon_asin` (`amazon_asin`)';
		Db::getInstance()->query($sql_add_index);
		return $result;
	}

	public function uninstall()
	{
		return parent::uninstall();
	}

	public function getContent()
	{
		$output = null;

		if (Tools::isSubmit('submit'.$this->name))
		{

			if(strval(Tools::getValue('general_setting')) == 'true'){
				$access_key = strval(Tools::getValue('Bitibe_amazon_access_key'));
				$secret_key = strval(Tools::getValue('Bitibe_amazon_secret_key'));
				$affiliate_id = strval(Tools::getValue('Bitibe_amazon_affiliate_id'));

				if (!empty($access_key))
				{
					Configuration::updateValue('Bitibe_amazon_access_key', $access_key);
				}

				if (!empty($secret_key))
				{
					Configuration::updateValue('Bitibe_amazon_secret_key', $secret_key);
				}

				if (!empty($affiliate_id))
				{
					Configuration::updateValue('Bitibe_amazon_affiliate_id', $affiliate_id);
				}
			}

		}


		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$fields_form[0]['form'] = array(
				'legend' => array(
					'title' => $this->l('Amazon Setting'),
					),
				'input' => array(
					array(
                                                'type' => 'hidden',
                                                'name' => 'general_setting',
                                                'id' => 'general_setting'
                                             ),
					array(
						'type' => 'text',
						'label' => $this->l('Amazon Access Key Id (for amazon.com)'),
						'name' => 'Bitibe_amazon_access_key',
						'required' => true
					     ),
					array(
						'type' => 'text',
						'label' => $this->l('Amazon Secret Key Id (for amazon.com)'),
						'name' => 'Bitibe_amazon_secret_key',
						'required' => true
					     ),
					array(
						'type' => 'text',
						'label' => $this->l('Amazon Affiliate Id (for amazon.com)'),
						'name' => 'Bitibe_amazon_affiliate_id',
						'required' => true
					     )
						),
					'submit' => array(
							'title' => $this->l('Save'),
							'class' => 'btn btn-default pull-right'
							)
						);

		            $fields_form[1]['form'] = array(
                                'legend' => array(
                                        'title' => $this->l('Amazon Fetch Product'),
                                        ),
                                'input' => array(
					array(
                                                'type' => 'hidden',
                                                'name' => 'amazon_product',
						'id' => 'amazon_product'
                                             ),
					array(
                                                'type' => 'text',
                                                'label' => $this->l('Search in Category'),
                                                'name' => 'Bitibe_amazon_category',
                                                'required' => true
                                             ),
                                        array(
                                                'type' => 'text',
                                                'label' => $this->l('Search keyword for Amazon'),
                                                'name' => 'Bitibe_amazon_keyword',
                                                'required' => false
                                             ),
					array(
                                                'type' => 'text',
                                                'label' => $this->l('Save in Category'),
                                                'name' => 'Bitibe_amazon_prestashop_category',
                                                'required' => true
                                             )
                                        ),
                                        'submit' => array(
                                                        'title' => $this->l('Save'),
                                                        'class' => 'btn btn-default pull-right'
                                                        )
                                                );


		$helper = new HelperForm();

		// Module, token and currentIndex
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		// Language
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;

		// Title and toolbar
		$helper->title = $this->displayName;
		$helper->show_toolbar = true;        // false -> remove toolbar
		$helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
		$helper->submit_action = 'submit'.$this->name;
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

		// Load current value
		$helper->fields_value['general_setting'] = 'true';
		$helper->fields_value['Bitibe_amazon_access_key'] = Configuration::get('Bitibe_amazon_access_key');
		$helper->fields_value['Bitibe_amazon_secret_key'] = Configuration::get('Bitibe_amazon_secret_key');
		$helper->fields_value['Bitibe_amazon_affiliate_id'] = Configuration::get('Bitibe_amazon_affiliate_id');


		$helper->fields_value['amazon_product'] = 'false';


		return $helper->generateForm($fields_form);
	}

	public function addProduct($name, $category_id, $price, $short_description, $image_url){

		$product = new Product();
		$product->ean13 = 0;
		$product->name = array((int)Configuration::get('PS_LANG_DEFAULT') =>  'Test product');;
		//$product->link_rewrite = 'Test product';
		$product->id_category = 11;
		$product->id_category_default = 11;
		$product->redirect_type = '404';
		$product->price = 22;
		$product->quantity = 1;
		$product->minimal_quantity = 1;
		$product->show_price = 1;
		$product->on_sale = 0;
		$product->online_only = 1;
		$product->meta_keywords = 'test';
		$product->is_virtual=0;
		$product->available_for_order = 0;
		$product->description = "descriptiom";
		$product->description_short = "short description";
		$product->available_now = 0;	
		$product->add();
		$product->addToCategories(array(11));

		$shops = Shop::getShops(true, null, true);    
		$image = new Image();
		$id_product = $this->maxProductId();	


		$image->id_product = $id_product;
		$image->position = Image::getHighestPosition($id_product) + 1;
		$image->cover = true; // or false;

		$url = "https://images-na.ssl-images-amazon.com/images/G/01/gateway/yiyiz/81QYnUWW8OL._UX984_SX984_CB531399255_.jpg";
	//	AdminImportController::copyImg($id_product, null, $url, 'products', false);


	/**	if (($image->validateFields(false, true)) === true && ($image->validateFieldsLang(false, true)) === true && $image->add())
		{

    			$image->associateTo($shops);
			$url = "https://images-na.ssl-images-amazon.com/images/G/01/gateway/yiyiz/81QYnUWW8OL._UX984_SX984_CB531399255_.jpg";

    			if (!AdminImportController::copyImg($id_product, null, $url, 'products', false))
    			{
        			$image->delete();
    			}
		}**/
	}

	private function maxProductId()
        {
                $sql = new DbQuery();
                $sql->from('product');
                $sql->select('max(id_product) as product_id');

                return Db::getInstance()->executeS($sql)[0]['product_id'];
        }

	private function queryAmazon($searchString, $category){

		$config = Configuration::getMultiple(array(
                                        'Bitibe_amazon_access_key',
                                        'Bitibe_amazon_secret_key',
                                        'Bitibe_amazon_affiliate_id'
                                        ));

		$amazon = new amazonAPI(
				$config['Bitibe_amazon_access_key'],
				$config['Bitibe_amazon_secret_key'],
				$config['Bitibe_amazon_affiliate_id'],
				0,
				"com"
				);

		$listing = $amazon->searchProducts($searchString, $category);

		echo $item.": total products - ".count($listing)."<br />";

		foreach ($listing as $i)
		{
			echo "\n\n";
		}
	}
}
