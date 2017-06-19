<?php

class AliexpressConfig
{

	private $this_module = false;


	public function __construct($mod)
	{
		$this->this_module = $mod;

	}


	public function checkConfiguration()
	{
		$config = Configuration::getMultiple(array(
					'Bitibe_aliexpress_app_key',
					'Bitibe_aliexpress_app_secret',
					'Bitibe_aliexpress_tracking_id'
					));

		if (array_key_exists('Bitibe_aliexpress_app_key', $config)
				&& array_key_exists('Bitibe_aliexpress_app_secret', $config)
				&& array_key_exists('Bitibe_aliexpress_tracking_id', $config)
		   )
		{
			if (empty($config['Bitibe_aliexpress_app_key']))
				$this->this_module->warning = $this->this_module->l('Please provide Aliexpress App Key');

			if (empty($config['Bitibe_aliexpress_app_secret']))
				$this->this_module->warning = $this->this_module->l('Please provide Aliexpress App Secret');
	
			if (empty($config['Bitibe_aliexpress_tracking_id']))
                                $this->this_module->warning = $this->this_module->l('Please provide Aliexpress Tracking Id');

		}else{
			$this->this_module->warning = $this->this_module->l('Missing configuration. Please configure module ' . $this->this_module->class_module_name);
		}

	}

	public function getAliexpressContent()
	{

		$output = null;

		if (Tools::isSubmit('aliexpressconfigsubmit'.$this->this_module->name))
		{

			$app_key = strval(Tools::getValue('Bitibe_aliexpress_app_key'));
			$app_secret = strval(Tools::getValue('Bitibe_aliexpress_app_secret'));
			$tracking_id = strval(Tools::getValue('Bitibe_aliexpress_tracking_id'));

			if (!empty($app_key))
			{
				Configuration::updateValue('Bitibe_aliexpress_app_key', $app_key);
			}else{
				$output .= $this->this_module->displayError($this->this_module->l('App key can not be empty'));
			}

			if (!empty($app_secret))
			{
				Configuration::updateValue('Bitibe_aliexpress_app_secret', $app_secret);
			}else{
				$output .= $this->this_module->displayError($this->this_module->l('App Secret can not be empty'));
			}

			if (!empty($tracking_id))
                        {
                                Configuration::updateValue('Bitibe_aliexpress_tracking_id', $tracking_id);
                        }else{
                                $output .= $this->this_module->displayError($this->this_module->l('Tracking Id can not be empty'));
                        }
		}

		if (Tools::isSubmit('aliexpresssearchsubmit'.$this->this_module->name))
		{
			$app_key = NULL;
                        $app_secret = NULL; 
                        $tracking_id = NULL;
			$is_valid = true;
			$aliexpress_category = strval(Tools::getValue('Bitibe_aliexpress_category'));
			$search_keyword = strval(Tools::getValue('Bitibe_aliexpress_keyword'));
			$aliexpress_count = strval(Tools::getValue('Bitibe_aliexpress_fetch_count'));
			$prestashop_category = strval(Tools::getValue('Bitibe_aliexpress_prestashop_category'));

			if(empty($aliexpress_category)){
				$output .= $this->this_module->displayError($this->this_module->l('Please select Aliexpress Category'));
				$is_valid = false;
			}

			if(empty($search_keyword)){
				$output .= $this->this_module->displayError($this->this_module->l('Search keyword can not be empty'));
				$is_valid = false;
			}

			if(empty($aliexpress_count)){
				$output .= $this->this_module->displayError($this->this_module->l('Please ennter number of products to be fetched'));
				$is_valid = false;
			}else if(!is_numeric($aliexpress_count)){
				$output .= $this->this_module->displayError($this->this_module->l('Number of products to be fetched is not numeric'));
				$is_valid = false;
			}

			if(empty($prestashop_category)){
				$output .= $this->this_module->displayError($this->this_module->l('Please select Prestashop Category'));
				$is_valid = false;
			}

			if($is_valid){
				$app_key = Configuration::get('Bitibe_aliexpress_app_key');
				$app_secret = Configuration::get('Bitibe_aliexpress_app_secret');
				$tracking_id = Configuration::get('Bitibe_aliexpress_tracking_id');

				if(empty($app_key) || empty($app_secret) || empty($tracking_id)){
					$output .= $this->this_module->displayError($this->this_module->l('Missing configuration. Please set Aliexpress setting'));
					$is_valid = false;
				}
			}

			if($is_valid){
				$aliexpress = new aliexpressAPI($app_key, $app_secret, $tracking_id);

				$arr = array();
				$page_count = $aliexpress_count/10;
				if($page_count == 0){
					$page_count = 1;
				}

				for($i=1; $i<=$page_count; $i++){
					$arr = $aaliexpress->searchProductHelper($search_keyword, $aliexpress_category, $i);

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
								$this->this_module->updateProduct("affiliate_website", "aliexpress.com", $product_id);

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


	public function aliexpressForm()
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

		$aliexpress_category = array();
		$fetched_categ = $this->this_module->fetchAffiliateCategory('aliexpress.com');

		foreach ($fetched_categ as $categ)
		{
			$aliexpress_category[] = array( 
					"id_option" => $categ['category_id'],
					"name" => $categ['category_name']
					);
		}

		$fields_form[0]['form'] = array(
				'legend' => array( 
					'title' => $this->this_module->l('Aliexpress Fetch Product'),
					),
				'input' => array(
					array(
						'type' => 'select',
						'label' => $this->this_module->l('Search in Category'),
						'desc' => $this->this_module->l('Select Aliexpress Category'),
						'name' => 'Bitibe_aliexpress_category',
						'required' => true,
						'options' => array(
							'query' => $aliexpress_category,
							'id' => 'id_option',
							'name' => 'name'
							)
					     ),
					array(
						'type' => 'text',
						'label' => $this->this_module->l('Search keyword for Aliexpress'),
						'name' => 'Bitibe_aliexpress_keyword',
						'required' => true
					     ),
					array(
						'type' => 'text',
						'label' => $this->this_module->l('Number of products to be fetched'),
						'desc' => $this->this_module->l('Choose multiple of 10'),
						'name' => 'Bitibe_aliexpress_fetch_count',
						'required' => true
					     ),
					array(
							'type' => 'select',
							'label' => $this->this_module->l('Save in Category'),
							'desc' => $this->this_module->l('Choose your store category'),
							'name' => 'Bitibe_aliexpress_prestashop_category',
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
		$helper->submit_action = 'aliexpresssearchsubmit'.$this->this_module->name;

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

		$helper->fields_value['Bitibe_aliexpress_fetch_count'] = 20;

		return $helper->generateForm($fields_form);
	}

	public function aliexpressConfig()
	{
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$fields_form[0]['form'] = array(
				'legend' => array(
					'title' => $this->this_module->l('Aliexpress Setting'),
					),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->this_module->l('Aliexpress App Key (for portals.aliexpress.com)'),
						'name' => 'Bitibe_aliexpress_app_key',
						'required' => true
					     ),
					array(
						'type' => 'text',
						'label' => $this->this_module->l('Aliexpress App Key (for portals.aliexpress.com)'),
						'name' => 'Bitibe_aliexpress_app_secret',
						'required' => true
					     ),
					array(
                                                'type' => 'text',
                                                'label' => $this->this_module->l('Aliexpress Tracking Id (for portals.aliexpress.com)'),
                                                'name' => 'Bitibe_aliexpress_tracking_id',
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
		$helper->submit_action = 'aliexpressconfigsubmit'.$this->this_module->name;
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
		$helper->fields_value['Bitibe_aliexpress_app_key'] = Configuration::get('Bitibe_aliexpress_app_key');
		$helper->fields_value['Bitibe_aliexpress_app_secret'] = Configuration::get('Bitibe_aliexpress_app_secret');
                $helper->fields_value['Bitibe_aliexpress_tracking_id'] = Configuration::get('Bitibe_aliexpress_tracking_id');

		return $helper->generateForm($fields_form);
	}

}

?>
