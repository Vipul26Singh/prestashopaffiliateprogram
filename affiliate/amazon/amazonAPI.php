<?php

class amazonAPI
{
	private $public_key;
	private $private_key;
	private $associate_tag;
	private $region;

	public function __construct($a, $b, $c)
	{
		$this->public_key = $a;
		$this->private_key = $b;
		$this->associate_tag = $c;
		$this->region = "com";
	}

	private function verifyXmlResponse($response)
	{
		if (isset($response) && isset($response->Items) && isset($response->Items->Item) && isset($response->Items->Item->ItemAttributes) && isset($response->Items->Item->ItemAttributes->Title))
		{
			return ($response);
		}
		else
		{
			return false;
		}
	}

	private function queryAmazon($parameters)
	{
		return $this->awsSignedRequest($parameters);
	}


	public function searchProducts($search, $category, $product_count){
		$arr = array();
		$page_count = $product_count/10;
		if($page_count == 0){
			$page_count = 1;
		}

		for($i=1; $i<=$page_count; $i++){
			$arr = array_merge($arr, $this->searchProductHelper($search, $category, $i));
		}
		return $arr;
	}	

	public function searchProductHelper($search, $category, $page)
	{
		$arr = array();

		$parameters = array("Operation"     	=> "ItemSearch",
				"Keywords"      	=> $search,
				"SearchIndex"   	=> $category,
				"MerchantId"		=> "Amazon",
				"ItemPage"			=> $page,
				"ResponseGroup" 	=> "ItemAttributes,OfferFull,Images");


		$xml_response = $this->queryAmazon($parameters);
		
		$raw = $this->verifyXmlResponse($xml_response);


		if (isset($raw) && isset($raw->Items) && !is_null($raw->Items->Item))
		{
			foreach ($raw->Items->Item as $p)
			{

				$price = ((double)$p->Offers->Offer->OfferListing->Price->Amount)/100;
				$reg_price = ((double)$p->ItemAttributes->ListPrice->Amount)/100;


				if ($price != 0 && $reg_price != 0)
				{
					$arr[] = array(
							"vendor" => "amazon.".$this->region,
							"upc" => (string)$p->ItemAttributes->UPC,
							"asin" => (string)$p->ASIN,
							"ean" => (string)$p->ItemAttributes->EAN,
							"mfg_part_no" => (string)$p->ItemAttributes->Model,
							"name" => (string)$p->ItemAttributes->Title,
							"price" => $price,
							"reg_price" => $reg_price,
							"category" => (string)$p->ItemAttributes->ProductGroup,
							"description" => (array)$p->ItemAttributes->Feature,
							"images" => (string)$p->LargeImage->URL,
							"link" => (string)$p->DetailPageURL
						      );
				}
			}
		}

		return $arr;
	}

	private function awsSignedRequest($params)
	{
		if($this->region == 'jp'){
			$host = "ecs.amazonaws.".$this->region;
		}else{
			$host = "webservices.amazon.".$this->region;
		}

		$method = "GET";
		$uri = "/onca/xml";

		$params["Service"]          = "AWSECommerceService";
		$params["AWSAccessKeyId"]   = $this->public_key;
		$params["AssociateTag"]     = $this->associate_tag;
		$params["Timestamp"]        = gmdate("Y-m-d\TH:i:s\Z");
		$params["Version"]          = "2011-08-01";

		ksort($params);

		$canonicalized_query = array();

		foreach ($params as $param=>$value)
		{
			$param = str_replace("%7E", "~", rawurlencode($param));
			$value = str_replace("%7E", "~", rawurlencode($value));
			$canonicalized_query[] = $param."=".$value;
		}

		$canonicalized_query = implode("&", $canonicalized_query);

		$string_to_sign = $method."\n".$host."\n".$uri."\n".
			$canonicalized_query;

		$signature = base64_encode(hash_hmac("sha256", 
					$string_to_sign, $this->private_key, true));

		$signature = str_replace("%7E", "~", rawurlencode($signature));

		$request = "http://".$host.$uri."?".$canonicalized_query."&Signature=".$signature;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$request);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		$xml_response = curl_exec($ch);

		if ($xml_response === false)
		{
			return false;
		}
		else
		{
			$parsed_xml = @simplexml_load_string($xml_response);
			return ($parsed_xml);
		}
	}
}

?>
