<?php

class EbayConfig extends ObjectModel
{

	private $this_module = false;


        public function __construct($mod)
        {
                $this->this_module = $mod;

        }


	public function checkConfiguration()
        {
                $config = Configuration::getMultiple(array(
                                                'Bitibe_ebay_app_id',
                                                'Bitibe_ebay_campaign_id'
                                                ));

                        if (array_key_exists('Bitibe_ebay_app_id', $config)
                                        && array_key_exists('Bitibe_ebay_campaign_id', $config)
                           )
                        {
                                if (empty($config['Bitibe_ebay_app_id']))
                                        $this->this_module->warning = $this->this_module->l('Please provide Ebay App Id');

                                if (empty($config['Bitibe_ebay_campaign_id']))
                                        $this->this_module->warning = $this->this_module->l('Please provide Ebay Campaign Id');


                        }else{
                                $this->this_module->warning = $this->this_module->l('Missing configuration. Please configure module ' . $this->this_module->class_module_name);
                        }

        }

	public function getEbayContent()
	{

		$output = null;

		if (Tools::isSubmit('ebayconfigsubmit'.$this->this_module->name))
		{

			$app_id = strval(Tools::getValue('Bitibe_ebay_app_id'));
			$campaign_id = strval(Tools::getValue('Bitibe_ebay_campaign_id'));

			if (!empty($app_id))
			{
				Configuration::updateValue('Bitibe_ebay_app_id', $app_id);
			}else{
				$output .= $this->this_module->displayError($this->this_module->l('App Id can not be empty'));
			}

			if (!empty($campaign_id))
			{
				Configuration::updateValue('Bitibe_ebay_campaign_id', $campaign_id);
			}else{
				$output .= $this->this_module->displayError($this->this_module->l('Campaign id can not be empty'));
			}
		}

		if (Tools::isSubmit('ebaysearchsubmit'.$this->this_module->name))
		{
			$app_id = NULL;
			$campaign_id = NULL;
			$is_valid = true;
			$ebay_category = strval(Tools::getValue('Bitibe_ebay_category'));
			$search_keyword = strval(Tools::getValue('Bitibe_ebay_keyword'));
			$ebay_count = strval(Tools::getValue('Bitibe_ebay_fetch_count'));
			$prestashop_category = strval(Tools::getValue('Bitibe_ebay_prestashop_category'));

			if(empty($ebay_category)){
				$output .= $this->this_module->displayError($this->this_module->l('Please select Ebay Category'));
				$is_valid = false;
			}

			if(empty($search_keyword)){
				$output .= $this->this_module->displayError($this->this_module->l('Search keyword can not be empty'));
				$is_valid = false;
			}

			if(empty($ebay_count)){
				$output .= $this->this_module->displayError($this->this_module->l('Please ennter number of products to be fetched'));
				$is_valid = false;
			}else if(!is_numeric($ebay_count)){
				$output .= $this->this_module->displayError($this->this_module->l('Number of products to be fetched is not numeric'));
				$is_valid = false;
			}

			if(empty($prestashop_category)){
				$output .= $this->this_module->displayError($this->this_module->l('Please select Prestashop Category'));
				$is_valid = false;
			}

			if($is_valid){
				$app_id = Configuration::get('Bitibe_ebay_app_id');
				$campaign_id = Configuration::get('Bitibe_ebay_campaign_id');

				if(empty($app_id) || empty($campaign_id)){
					$output .= $this->this_module->displayError($this->this_module->l('Missing configuration. Please set Ebay setting'));
					$is_valid = false;
				}
			}

			if($is_valid){
				$ebay = new ebayAPI($access_key, $secret_key, $affiliate_id);

				$arr = array();
				$page_count = $ebay_count/10;
				if($page_count == 0){
					$page_count = 1;
				}

				for($i=1; $i<=$page_count; $i++){
					$arr = $ebay->searchProductHelper($search_keyword, $ebay_category, $i);

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
								$this->this_module->updateProduct("affiliate_website", "ebay.com", $product_id);

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
		return $output;
	}


	public function ebayForm()
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

		$ebay_category = array();
		$fetched_categ = $this->this_module->fetchAffiliateCategory('ebay.com');

		foreach ($fetched_categ as $categ)
		{
			$ebay_category[] = array( 
					"id_option" => $categ['category_id'],
					"name" => $categ['category_name']
					);
		}

		$fields_form[0]['form'] = array(
				'legend' => array( 
					'title' => $this->this_module->l('Ebay Fetch Product'),
					),
				'input' => array(
					array(
						'type' => 'select',
						'label' => $this->this_module->l('Search in Category'),
						'desc' => $this->this_module->l('Select Ebay Category'),
						'name' => 'Bitibe_ebay_category',
						'required' => true,
						'options' => array(
							'query' => $ebay_category,
							'id' => 'id_option',
							'name' => 'name'
							)
					     ),
					array(
						'type' => 'text',
						'label' => $this->this_module->l('Search keyword for Ebay'),
						'name' => 'Bitibe_ebay_keyword',
						'required' => true
					     ),
					array(
						'type' => 'text',
						'label' => $this->this_module->l('Number of products to be fetched'),
						'desc' => $this->this_module->l('Choose multiple of 10'),
						'name' => 'Bitibe_ebay_fetch_count',
						'required' => true
					     ),
					array(
							'type' => 'select',
							'label' => $this->this_module->l('Save in Category'),
							'desc' => $this->this_module->l('Choose your store category'),
							'name' => 'Bitibe_ebay_prestashop_category',
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
		$helper->submit_action = 'ebaysearchsubmit'.$this->this_module->name;

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

		$helper->fields_value['Bitibe_ebay_fetch_count'] = 20;

		return $helper->generateForm($fields_form);
	}

	public function ebayConfig()
	{
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$fields_form[0]['form'] = array(
				'legend' => array(
					'title' => $this->this_module->l('Ebay Setting'),
					),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->this_module->l('Ebay App Id (for ebay.com)'),
						'name' => 'Bitibe_ebay_app_id',
						'required' => true
					     ),
					array(
						'type' => 'text',
						'label' => $this->this_module->l('Ebay Campaign Id (for ebay.com)'),
						'name' => 'Bitibe_ebay_campaign_id',
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
		$helper->submit_action = 'ebayconfigsubmit'.$this->this_module->name;
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
		$helper->fields_value['Bitibe_ebay_app_id'] = Configuration::get('Bitibe_ebay_app_id');
		$helper->fields_value['Bitibe_ebay_campaign_id'] = Configuration::get('Bitibe_ebay_campaign_id');


		return $helper->generateForm($fields_form);
	}

}

?>
