<?php

class AmazonConfig 
{

	private $this_module = false;


	public function __construct($mod)
        {	
		$this->this_module = $mod;
         
	}

	public function getAmazonContent()
	{

		$output = null;

		if (Tools::isSubmit('amazonconfigsubmit'.$this->this_module->name))
		{

			$access_key = strval(Tools::getValue('Bitibe_amazon_access_key'));
			$secret_key = strval(Tools::getValue('Bitibe_amazon_secret_key'));
			$affiliate_id = strval(Tools::getValue('Bitibe_amazon_affiliate_id'));

			if (!empty($access_key))
			{
				Configuration::updateValue('Bitibe_amazon_access_key', $access_key);
			}else{
				$output .= $this->this_module->displayError($this->this_module->l('Access key can not be empty'));
			}

			if (!empty($secret_key))
			{
				Configuration::updateValue('Bitibe_amazon_secret_key', $secret_key);
			}else{
				$output .= $this->this_module->displayError($this->this_module->l('Secret key can not be empty'));
			}

			if (!empty($affiliate_id))
			{
				Configuration::updateValue('Bitibe_amazon_affiliate_id', $affiliate_id);
			}else{
				$output .= $this->this_module->displayError($this->this_module->l('Affiliate key can not be empty'));
			}
		}

		if (Tools::isSubmit('amazonsearchsubmit'.$this->this_module->name))
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
				$output .= $this->this_module->displayError($this->this_module->l('Please select Amazon Category'));
				$is_valid = false;
			}

			if(empty($search_keyword)){
				$output .= $this->this_module->displayError($this->this_module->l('Search keyword can not be empty'));
				$is_valid = false;
			}

			if(empty($amazon_count)){
				$output .= $this->this_module->displayError($this->this_module->l('Please ennter number of products to be fetched'));
				$is_valid = false;
			}else if(!is_numeric($amazon_count)){
				$output .= $this->this_module->displayError($this->this_module->l('Number of products to be fetched is not numeric'));
				$is_valid = false;
			}

			if(empty($prestashop_category)){
				$output .= $this->this_module->displayError($this->this_module->l('Please select Prestashop Category'));
				$is_valid = false;
			}

			if($is_valid){
				$access_key = Configuration::get('Bitibe_amazon_access_key');
				$secret_key = Configuration::get('Bitibe_amazon_secret_key');
				$affiliate_id = Configuration::get('Bitibe_amazon_affiliate_id');


				if(empty($access_key) || empty($secret_key) || empty($affiliate_id)){
					$output .= $this->this_module->displayError($this->this_module->l('Missing configuration. Please set Amazon setting'));
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

						if($this->this_module->fetchAffiliateProductId($p['asin']) == 0){
							$short_description = "<ul>";
							foreach($p['description'] as $desc){
								$desc = trim($desc);
								$short_description .= "<li>{$desc}</li>";
							}
							$short_description .="</ul><br><br>";
							$short_description .= "<p><a href={$p['link']} target='_blank' class='btn btn-default'>BUY NOW</a></p>";

							$product_id = $this->this_module->addProduct($p['name'], $prestashop_category, $p['price'], $short_description);

							if($product_id != 0){
								$this->this_module->updateProduct("affiliate_product_id", $p['asin'], $product_id);
								$this->this_module->updateProduct("affiliate_website", "amazon.com", $product_id);

								$imageAdd = new ImageAdd();
								try{
									$image_id = $imageAdd->insertImageInPrestashop($product_id, $p['images'], $p['name']);
				
									if($image_id == 0){
										$this->this_module->deleteProduct($product_id);
									}
								}catch(Exception $e){
										$this->this_module->deleteProduct($product_id);
								}
							}
						}
					}
				}
			}
		}
		return $output;
	}

	public function checkConfiguration()
	{
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
				$this->this_module->warning = $this->this_module->l('Please provide Amazon Access Key');

			if (empty($config['Bitibe_amazon_secret_key']))
				$this->this_module->warning = $this->this_module->l('Please provide Amazon Secret Key');

			if (empty($config['Bitibe_amazon_affiliate_id']))
				$this->this_module->warning = $this->this_module->l('Please provide Affiliate Id');


		}else{
			$this->this_module->warning = $this->this_module->l('Missing configuration. Please configure module ' . $this->this_module->class_module_name);
		}
	}

	public function amazonForm()
	{
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$presta_category = array();
		$fetched_categ = $this->this_module->fetchPrestashopCategory();

		foreach ($fetched_categ as $categ)
		{
			$presta_category[] = array( 
					"id_option" => (int)$categ['id_category'],
					"name" => $categ['name']
					);
		}

		$amazon_category = array();
		$fetched_categ = $this->this_module->fetchAffiliateCategory('amazon.com');

		foreach ($fetched_categ as $categ)
		{
			$amazon_category[] = array( 
					"id_option" => $categ['category_name'],
					"name" => $categ['category_name']
					);
		}

		$fields_form[0]['form'] = array(
				'legend' => array( 
					'title' => $this->this_module->l('Amazon Fetch Product'),
					),
				'input' => array(
					array(
						'type' => 'select',
						'label' => $this->this_module->l('Search in Category'),
						'desc' => $this->this_module->l('Select Amazon Category'),
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
						'label' => $this->this_module->l('Search keyword for Amazon'),
						'name' => 'Bitibe_amazon_keyword',
						'required' => true
					     ),
					array(
						'type' => 'text',
						'label' => $this->this_module->l('Number of products to be fetched'),
						'desc' => $this->this_module->l('Choose multiple of 10'),
						'name' => 'Bitibe_amazon_fetch_count',
						'required' => true
					     ),
					array(
							'type' => 'select',
							'label' => $this->this_module->l('Save in Category'),
							'desc' => $this->this_module->l('Choose your store category'),
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
							'title' => $this->this_module->l('Save'),
							'class' => 'btn btn-default pull-right'
							)
						);


		$helper = new HelperForm();

		// Module, token and currentIndex
		$helper->module = $this->this_module;
		$helper->name_controller = $this->this_module->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->this_module->name;
		// Language
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;

		// Title and toolbar
		$helper->title = $this->this_module->displayName;
		$helper->show_toolbar = true;        // false -> remove toolbar
		$helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
		$helper->submit_action = 'amazonsearchsubmit'.$this->this_module->name;

		$helper->toolbar_btn = array(
				'save' =>
				array(
					'desc' => $this->this_module->l('Fetch'),
					'href' => AdminController::$currentIndex.'&configure='.$this->this_module->name.'&save'.$this->this_module->name.
					'&token='.Tools::getAdminTokenLite('AdminModules'),
				     ),
				'back' => array(
					'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
					'desc' => $this->this_module->l('Back to list')
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
					'title' => $this->this_module->l('Amazon Setting'),
					),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->this_module->l('Amazon Access Key Id (for amazon.com)'),
						'name' => 'Bitibe_amazon_access_key',
						'required' => true
					     ),
					array(
						'type' => 'text',
						'label' => $this->this_module->l('Amazon Secret Key Id (for amazon.com)'),
						'name' => 'Bitibe_amazon_secret_key',
						'required' => true
					     ),
					array(
						'type' => 'text',
						'label' => $this->this_module->l('Amazon Affiliate Id (for amazon.com)'),
						'name' => 'Bitibe_amazon_affiliate_id',
						'required' => true
					     )
					),
				'submit' => array(
						'title' => $this->this_module->l('Save'),
						'class' => 'btn btn-default pull-right'
						)
					);


		$helper = new HelperForm();

		// Module, token and currentIndex
		$helper->module = $this->this_module;
		$helper->name_controller = $this->this_module->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->this_module->name;
		// Language
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;

		// Title and toolbar
		$helper->title = $this->this_module->displayName;
		$helper->show_toolbar = true;        // false -> remove toolbar
		$helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
		$helper->submit_action = 'amazonconfigsubmit'.$this->this_module->name;
		$helper->toolbar_btn = array(
				'save' =>
				array(
					'desc' => $this->this_module->l('Save'),
					'href' => AdminController::$currentIndex.'&configure='.$this->this_module->name.'&save'.$this->this_module->name.
					'&token='.Tools::getAdminTokenLite('AdminModules'),
				     ),
				'back' => array(
					'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
					'desc' => $this->this_module->l('Back to list')
					)
				);

		// Load current value
		$helper->fields_value['Bitibe_amazon_access_key'] = Configuration::get('Bitibe_amazon_access_key');
		$helper->fields_value['Bitibe_amazon_secret_key'] = Configuration::get('Bitibe_amazon_secret_key');
		$helper->fields_value['Bitibe_amazon_affiliate_id'] = Configuration::get('Bitibe_amazon_affiliate_id');


		return $helper->generateForm($fields_form);
	}

}

?>
