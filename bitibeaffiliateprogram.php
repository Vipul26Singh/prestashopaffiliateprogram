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


		return parent::install() && $this->installTables() && $this->installData() &&
			Configuration::updateValue('Bitibe_amazon_access_key', '') &&
			Configuration::updateValue('Bitibe_amazon_secret_key', '') &&
			Configuration::updateValue('Bitibe_amazon_affiliate_id', '');
	}

	private function installData(){
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('All'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Wine'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Wireless'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('ArtsAndCrafts'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Miscellaneous'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Electronics'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Jewelry'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('MobileApps'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Photo'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Shoes'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('KindleStore'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Automotive'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Vehicles'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Pantry'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('MusicalInstruments'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('DigitalMusic'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('GiftCards'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('FashionBaby'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('FashionGirls'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('GourmetFood'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('HomeGarden'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('MusicTracks'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('UnboxVideo'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('FashionWomen'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('VideoGames'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('FashionMen'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Kitchen'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Video'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Software'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Beauty'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Grocery'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('FashionBoys'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Industrial'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('PetSupplies'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('OfficeProducts'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Magazines'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Watches'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Luggage'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('OutdoorLiving'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Toys'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('SportingGoods'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('PCHardware'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Movies'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Books'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Collectibles'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Handmade'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('VHS'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('MP3Downloads'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Fashion'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Tools'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Baby'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Apparel'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Marketplace'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('DVD'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Appliances'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Music'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('LawnAndGarden'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('WirelessAccessories'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Blended'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('HealthPersonalCare'), 'site_name'  => 'amazon.com'));
		Db::getInstance()->insert('affiliate_category', array( 'category_name' => pSQL('Classical'), 'site_name'  => 'amazon.com'));

		return true;
	}


	private function installTables(){

		Db::getInstance()->execute('DROP table '._DB_PREFIX_.'affiliate_category');
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

		$sql_add_asin = 'ALTER TABLE `'._DB_PREFIX_.'product` ADD COLUMN `amazon_asin` VARCHAR(100) NULL';
		Db::getInstance()->query($sql_add_asin);

		$sql_add_site =   'ALTER TABLE `'._DB_PREFIX_.'product` ADD COLUMN `affiliate_website` VARCHAR(100) NULL';
		Db::getInstance()->query($sql_add_site);

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

				$access_key = strval(Tools::getValue('Bitibe_amazon_access_key'));
				$secret_key = strval(Tools::getValue('Bitibe_amazon_secret_key'));
				$affiliate_id = strval(Tools::getValue('Bitibe_amazon_affiliate_id'));

				if (!empty($access_key))
				{
					Configuration::updateValue('Bitibe_amazon_access_key', $access_key);
				}else{
					$output .= $this->displayError($this->l('Access key can not be empty'));
				}

				if (!empty($secret_key))
				{
					Configuration::updateValue('Bitibe_amazon_secret_key', $secret_key);
				}else{
                                        $output .= $this->displayError($this->l('Secret key can not be empty'));
                                }

				if (!empty($affiliate_id))
				{
					Configuration::updateValue('Bitibe_amazon_affiliate_id', $affiliate_id);
				}else{
                                        $output .= $this->displayError($this->l('Affiliate key can not be empty'));
                                }
		}

		if (Tools::isSubmit('amazonsubmit'.$this->name))
                {
				$is_valid = true;
                                $amazon_category = strval(Tools::getValue('Bitibe_amazon_category'));
                                $search_keyword = strval(Tools::getValue('Bitibe_amazon_keyword'));
                                $amazon_count = strval(Tools::getValue('Bitibe_amazon_fetch_count'));
				$prestashop_category = strval(Tools::getValue('Bitibe_amazon_prestashop_category'));

				if(empty($amazon_category)){
                                        $output .= $this->displayError($this->l('Please select Amazon Category'));
					$is_valid = false;
				}

				if(empty($search_keyword)){
                                        $output .= $this->displayError($this->l('Search keyword can not be empty'));
					$is_valid = false;
                                }

				if(empty($amazon_count)){
                                        $output .= $this->displayError($this->l('Please ennter number of products to be fetched'));
					$is_valid = false;
                                }else if(!is_numeric($amazon_count)){
					$output .= $this->displayError($this->l('Number of products to be fetched is not numeric'));
					$is_valid = false;
				}

				if(empty($prestashop_category)){
					$output .= $this->displayError($this->l('Please select Prestashop Category'));
					$is_valid = false;
				}

				if($is_valid){


				}
                }

		return $output.$this->displayForm().$this->amazonForm();
	}

	public function amazonForm()
        {
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$presta_category = array();
                $fetched_categ = $this->fetchPrestashopCategory();

                foreach ($fetched_categ as $categ)
                {
                        $presta_category[] = array(
                                        "id_option" => (int)$categ['id_category'],
                                        "name" => $categ['name']
                                        );
                }

                $amazon_category = array();
                $fetched_categ = $this->fetchAffiliateCategory('amazon.com');

                foreach ($fetched_categ as $categ)
                {
                        $amazon_category[] = array(
                                        "id_option" => $categ['category_name'],
                                        "name" => $categ['category_name']
                                        );
                }

		$fields_form[0]['form'] = array(
                                'legend' => array(
                                        'title' => $this->l('Amazon Fetch Product'),
                                        ),
                                'input' => array(
                                        array(
                                                'type' => 'select',
                                                'label' => $this->l('Search in Category'),
                                                'desc' => $this->l('Select Amazon Category'),
                                                'name' => 'Bitibe_amazon_category',
                                                'required' => true,
                                                'options' => array(
                                                        'query' => $amazon_category,
                                                        'id' => 'id_option',
                                                        'name' => 'name'
                                                        )
                                             ),
                                        array(
                                                'type' => 'text',
                                                'label' => $this->l('Search keyword for Amazon'),
                                                'name' => 'Bitibe_amazon_keyword',
                                                'required' => true
                                             ),
                                        array(
                                                'type' => 'text',
                                                'label' => $this->l('Number of products to be fetched'),
                                                'name' => 'Bitibe_amazon_fetch_count',
                                                'required' => true
                                             ),
                                        array(
                                                        'type' => 'select',
                                                        'label' => $this->l('Save in Category'),
                                                        'desc' => $this->l('Choose your store category'),
                                                        'name' => 'Bitibe_amazon_prestashop_category',
                                                        'required' => true,
                                                        'options' => array(
                                                                'query' => $presta_category,
                                                                'id' => 'id_option',
                                                                'name' => 'name'
                                                                )
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
                $helper->submit_action = 'amazonsubmit'.$this->name;
                $helper->toolbar_btn = array(
                                'save' =>
                                array(
                                        'desc' => $this->l('Fetch'),
                                        'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                                        '&token='.Tools::getAdminTokenLite('AdminModules'),
                                     ),
                                'back' => array(
                                        'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                                        'desc' => $this->l('Back to list')
                                        )
                                );

                $helper->fields_value['Bitibe_amazon_fetch_count'] = 1000;
                $helper->fields_value['amazon_product'] = 'true';

                return $helper->generateForm($fields_form);
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
		$helper->fields_value['Bitibe_amazon_access_key'] = Configuration::get('Bitibe_amazon_access_key');
		$helper->fields_value['Bitibe_amazon_secret_key'] = Configuration::get('Bitibe_amazon_secret_key');
		$helper->fields_value['Bitibe_amazon_affiliate_id'] = Configuration::get('Bitibe_amazon_affiliate_id');


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

	private function fetchPrestashopCategory(){
		$lang_shop = (int) $this->context->language->id;

		$sql = new DbQuery();
		$sql->from('category_lang');
		$sql->select('distinct id_category, name');
		$sql->orderBy('name');

		return Db::getInstance()->executeS($sql);
	}

	private function fetchAffiliateCategory($site){
		$sql = new DbQuery();
                $sql->select('category_name, category_id');
		$sql->from('affiliate_category', 'a');
		$sql->where("a.site_name = '". pSQL($site)."'");
                $sql->orderBy('category_name');

                return Db::getInstance()->executeS($sql);

	}
}
