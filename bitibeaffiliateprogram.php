<?php

if(file_exists(__DIR__."/affiliate/amazonAPI.php")){
                        require_once(__DIR__."/affiliate/amazonAPI.php");
              }


if (!defined('_PS_VERSION_'))
exit;


class ImageAdd extends AdminImportController
{

	public function insertImageInPrestashop($id_product, $url, $name_photo)
	{
		$shops = Shop::getShops(true, null, true);
		$image = new ImageCore();
		$image->id_product = $id_product;
		$image->position = Image::getHighestPosition($id_product) + 1;
		$image->cover = true;
		$tmp = explode(".", $name_photo);
		$name_photo_product = "";
		$name_for_legend = "";
		if (count($tmp) == 1) {
			$name_photo_product = trim($url) . $name_photo . ".jpg";
			$name_for_legend = $name_photo . ".jpg";
		} else {
			$name_photo_product = trim($url) . $name_photo;
			$name_for_legend = $name_photo;
		}
		$image->legend = array('1' => trim($name_for_legend));
		if ($image->validateFields(false, true) === true && $image->validateFieldsLang(false, true) === true && $image->add()) {
			$image->associateTo($shops);
			if (!$this->copyImg($id_product, $image->id, $name_photo_product, 'products')) {
				$image->delete();
			}
		}
		return $image->id;
	}
}

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

		if(file_exists(__DIR__."/affiliate/amazonAPI.php")){
			$this->amazon_allowed = true;
		}

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



		if ($this->amazon_allowed == true && Tools::isSubmit('submit'.$this->name))
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

		if ($this->amazon_allowed == true && Tools::isSubmit('amazonsubmit'.$this->name))
		{
			$access_key = NULL;
			$secret_key = NULL;
			$affiliate_id = NULL;
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
				$access_key = Configuration::get('Bitibe_amazon_access_key');
				$secret_key = Configuration::get('Bitibe_amazon_secret_key');
				$affiliate_id = Configuration::get('Bitibe_amazon_affiliate_id');


				if(empty($access_key) || empty($secret_key) || empty($affiliate_id)){
					$output .= $this->displayError($this->l('Missing configuration. Please set Amazon setting'));
					$is_valid = false;
				}
			}


			if($is_valid){
				$amazon = new amazonAPI($access_key, $secret_key, $affiliate_id);

				$arr = array();
				$page_count = $amazon_count/10;
				if($page_count == 0){
					$page_count = 1;
				}

				for($i=1; $i<=$page_count; $i++){
					$arr = $amazon->searchProductHelper($search_keyword, $amazon_category, $i);

					foreach($arr as $p){

						if($this->fetchAsin($p['asin']) == 0){
							$short_description = "<ul>";			
							foreach($p['description'] as $desc){
								$desc = trim($desc);
								$short_description .= "<li>{$desc}</li>";
							}
							$short_description .="</ul><br><br>";
							$short_description .= "<p><a href={$p['link']} target='_blank' class='btn btn-default'>BUY NOW</a></p>";

							$product_id = $this->addProduct($p['name'], $prestashop_category, $p['price'], $short_description);

							if($product_id != 0){
								$this->updateProduct("amazon_asin", $p['asin'], $product_id);
								$this->updateProduct("affiliate_website", "amazon.com", $product_id);

								$imageAdd = new ImageAdd();

								try{
									$imageAdd->insertImageInPrestashop($product_id, $p['images'], $p['name']);
								}catch(Exception $e){

								}
							}
						}
					}
				}
			}
		}


		if($this->amazon_allowed == true){ 
			$output.=$this->amazonConfig().$this->amazonForm();
		}
		return $output;
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
						'desc' => $this->l('Choose multiple of 10'),
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

		$helper->fields_value['Bitibe_amazon_fetch_count'] = 20;
		$helper->fields_value['amazon_product'] = 'true';

		return $helper->generateForm($fields_form);
	}

	public function amazonConfig()
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

	public function addProduct($name, $category_id, $price, $short_description){
		$id_product = 0;

		$product = new Product();
		$product->ean13 = 0;
		$product->name = array((int)Configuration::get('PS_LANG_DEFAULT') =>  $name);;
		//$product->link_rewrite = 'amazon_'.$name;
		$product->id_category = $category_id;
		$product->id_category_default = $category_id;
		$product->redirect_type = '404';
		$product->price = $price;
		$product->quantity = 1;
		$product->minimal_quantity = 1;
		$product->show_price = 1;
		$product->on_sale = 0;
		$product->online_only = 1;
		$product->is_virtual=0;
		$product->available_for_order = 0;
		//$product->description = $short_description;
		$product->description_short = $short_description;
		$product->available_now = 0;	

		try{
			$product->add();
			$product->addToCategories(array($category_id));

			$id_product = $this->maxProductId();	
		}catch(Exception $e){
			$id_product = 0;
		}

		return $id_product;
	}

	private function maxProductId()
	{
		$sql = new DbQuery();
		$sql->from('product');
		$sql->select('max(id_product) as product_id');

		return Db::getInstance()->executeS($sql)[0]['product_id'];
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

	private function fetchAsin($asin){
		$sql = new DbQuery();
		$sql->from('product');
		$sql->select('count(*) as count');
		$sql->where("amazon_asin = '".$asin."'");

		return Db::getInstance()->executeS($sql)[0]['count'];		
	}

	private function updateProduct($column, $val, $product_id){
		$sql = "update "._DB_PREFIX_."product set  {$column} = '{$val}' WHERE id_product = '{$product_id}'";
		Db::getInstance()->execute($sql);
	}

	private function insertImageInPrestashop($id_product, $url, $name_photo)
	{
		$shops = Shop::getShops(true, null, true);
		$image = new ImageCore();
		$image->id_product = $id_product;
		$image->position = Image::getHighestPosition($id_product) + 1;
		$image->cover = true;
		$tmp = explode(".", $name_photo);
		$name_photo_product = "";
		$name_for_legend = "";
		if (count($tmp) == 1) {
			$name_photo_product = trim($url) . $name_photo . ".jpg";
			$name_for_legend = $name_photo . ".jpg";
		} else {
			$name_photo_product = trim($url) . $name_photo;
			$name_for_legend = $name_photo;
		}
		$image->legend = array('1' => trim($name_for_legend));
		if ($image->validateFields(false, true) === true && $image->validateFieldsLang(false, true) === true && $image->add()) {
			$image->associateTo($shops);
			if (!$this->copyImg($id_product, $image->id, $name_photo_product, 'products')) {
				$image->delete();
			}
		}
		return $image->id;
	}
}
