<?php
if (!defined('_PS_VERSION_'))
exit;

require_once(__DIR__."/imageHelper.php");

if(file_exists(__DIR__."/affiliate/amazon/amazonAPI.php")){
	require_once(__DIR__."/affiliate/amazon/amazonAPI.php");
	require_once(__DIR__."/affiliate/amazon/amazonConfig.php");
}

if(file_exists(__DIR__."/affiliate/ebay/ebayAPI.php")){
	require_once(__DIR__."/affiliate/ebay/ebayAPI.php");
	require_once(__DIR__."/affiliate/ebay/ebayConfig.php");
}


class BitibeAffiliateProgram extends Module
{
	public $amazon_allowed = false;
	public $ebay_allowed = false;
	public $class_module_name = 'bitibeaffiliateprogram'; 
	public $amazonConfig;
	public $ebayConfig;

	public function __construct()
	{
		$this->name = $this->class_module_name;
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'Bitibe';
		$this->need_instance = 0;
		$this->bootstrap = true;

		parent::__construct();

		if(file_exists(__DIR__."/affiliate/amazon/amazonAPI.php")){
			$this->amazon_allowed = true;
		}

                if(file_exists(__DIR__."/affiliate/ebay/ebayAPI.php")){
                        $this->ebay_allowed = true;
                } 

		$this->displayName = $this->l('BitibeAffiliateProgram');
		$this->description = $this->l('Affilite programs wirh e-commerce');

		$this->confirmUninstall = $this->l('Are you sure... you will not be able to add affiliaate program');


		if($this->amazon_allowed == true){
			$this->amazonConfig = new AmazonConfig($this);
			$this->amazonConfig->checkConfiguration();
		}


		if($this->ebay_allowed == true){
			$this->ebayConfig = new EbayConfig($this);
			$this->ebayConfig->checkConfiguration();
                }

	}

	public function install()
	{
		if (Shop::isFeatureActive())
			Shop::setContext(Shop::CONTEXT_ALL);

		$result = parent::install() && $this->installTables() && $this->installData();


		if($this->amazon_allowed == true){
			$result = $result && Configuration::updateValue('Bitibe_amazon_access_key', '') &&
				Configuration::updateValue('Bitibe_amazon_secret_key', '') &&
				Configuration::updateValue('Bitibe_amazon_affiliate_id', '');
		}

		if($this->ebay_allowed == true){
			$result = $result && Configuration::updateValue('Bitibe_ebay_app_id', '') &&
                                Configuration::updateValue('Bitibe_ebay_compaign_id', '');

		}

		return $result;
	}

	private function installData(){
		if($this->amazon_allowed == true){
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
		}

		if($ebay_allwoed == true){

			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('-1'), 'category_name' => pSQL('All'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('20081'), 'category_name' => pSQL('Antiques'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('550'), 'category_name' => pSQL('Art'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('2984'), 'category_name' => pSQL('Baby'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('267'), 'category_name' => pSQL('Books'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('12576'), 'category_name' => pSQL('Business & Industrial'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('625'), 'category_name' => pSQL('Cameras & Photo'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('15032'), 'category_name' => pSQL('Cell Phones & Accessories'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('11450'), 'category_name' => pSQL('Clothing, Shoes & Accessories'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('11116'), 'category_name' => pSQL('Coins & Paper Money'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('1'), 'category_name' => pSQL('Collectibles'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('58058'), 'category_name' => pSQL('Computers/Tablets & Networking'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('293'), 'category_name' => pSQL('Consumer Electronics'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('14339'), 'category_name' => pSQL('Crafts'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('237'), 'category_name' => pSQL('Dolls & Bears'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('11232'), 'category_name' => pSQL('DVDs & Movies'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('45100'), 'category_name' => pSQL('Entertainment Memorabilia'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('172008'), 'category_name' => pSQL('Gift Cards & Coupons'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('26395'), 'category_name' => pSQL('Health & Beauty'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('11700'), 'category_name' => pSQL('Home & Garden'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('281'), 'category_name' => pSQL('Jewelry & Watches'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('11233'), 'category_name' => pSQL('Music'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('619'), 'category_name' => pSQL('Musical Instruments & Gear'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('1281'), 'category_name' => pSQL('Pet Supplies'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('870'), 'category_name' => pSQL('Pottery & Glass'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('10542'), 'category_name' => pSQL('Real Estate'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('316'), 'category_name' => pSQL('Specialty Services'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('888'), 'category_name' => pSQL('Sporting Goods'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('64482'), 'category_name' => pSQL('Sports Mem, Cards & Fan Shop'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('260'), 'category_name' => pSQL('Stamps'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('1305'), 'category_name' => pSQL('Tickets & Experiences'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('220'), 'category_name' => pSQL('Toys & Hobbies'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('3252'), 'category_name' => pSQL('Travel'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('1249'), 'category_name' => pSQL('Video Games & Consoles'), 'site_name'  => 'ebay.com'));
			Db::getInstance()->insert('affiliate_category', array( 'category_id' => pSQL('99'), 'category_name' => pSQL('Everything Else'), 'site_name'  => 'ebay.com'));
		}

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

		$sql_add_asin = 'ALTER TABLE `'._DB_PREFIX_.'product` ADD COLUMN `affiliate_product_id` VARCHAR(100) NULL';
		Db::getInstance()->query($sql_add_asin);

		$sql_add_site =   'ALTER TABLE `'._DB_PREFIX_.'product` ADD COLUMN `affiliate_website` VARCHAR(100) NULL';
		Db::getInstance()->query($sql_add_site);

		$sql_add_index = 'ALTER TABLE `'._DB_PREFIX_.'product` ADD INDEX `bitibe_affiliate_product_id` (`affiliate_product_id`)';
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

		if($this->amazon_allowed == true){ 
			$output.=$this->amazonConfig->getAmazonContent().$this->amazonConfig->amazonConfig().$this->amazonConfig->amazonForm();
		}

		if($this->ebay_allowed == true){
                        $output.=$this->ebayConfig->getEbayContent().$this->ebayConfig->ebayConfig().$this->ebayConfig->ebayForm();
                }

		return $output;
	}


	public function addProduct($name, $category_id, $price, $short_description){
		$id_product = 0;

		$product = new Product();
		$product->ean13 = 0;
		$product->name = array((int)Configuration::get('PS_LANG_DEFAULT') =>  $name);;
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

	public function fetchAffiliateProductId($asin){
                $sql = new DbQuery();
                $sql->from('product');
                $sql->select('count(*) as count');
                $sql->where("affiliate_product_id = '".$asin."'");

                return Db::getInstance()->executeS($sql)[0]['count'];
        }

        public function updateProduct($column, $val, $product_id){
                $sql = "update "._DB_PREFIX_."product set  {$column} = '{$val}' WHERE id_product = '{$product_id}'";
                Db::getInstance()->execute($sql);
        }

        public function fetchPrestashopCategory()
        {
                $sql = new DbQuery();
                $sql->from('category_lang');
                $sql->select('distinct id_category, name');
                $sql->orderBy('name');

                return Db::getInstance()->executeS($sql);
        }

        public function fetchAffiliateCategory($site)
        {
                $sql = new DbQuery();
                $sql->select('category_name, category_id');
                $sql->from('affiliate_category', 'a');
                $sql->where("a.site_name = '". pSQL($site)."'");
                $sql->orderBy('category_name');

                return Db::getInstance()->executeS($sql);

        }

	public function maxProductId()
        {
                $sql = new DbQuery();
                $sql->from('product');
                $sql->select('max(id_product) as product_id');

                return Db::getInstance()->executeS($sql)[0]['product_id'];
        }


}

?>
