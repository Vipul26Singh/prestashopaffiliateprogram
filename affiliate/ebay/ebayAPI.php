<?php

class ebayAPI
{
	private $endpoint;
	private $per_page;
	private $app_id;
	private $campaign_id;
	private $region;

	public function __construct($a, $b)
	{
		$this->endpoint = "http://svcs.ebay.com/services/search/FindingService/v1";
		$this->per_page = 10;
		$this->app_id = $a;
		$this->campaign_id = $b;
		$this->region = "com";
	}

	public function searchProductHelper($search, $category, $page)
	{
		$arr = array();

		$resp = simplexml_load_string($this->constructPostCallAndGetResponse($search, $page, $category));

		if ($resp->ack == "Success") {
			$results = ''; 

			foreach($resp->searchResult->item as $item) {
				$pic   = $item->galleryURL;
				$link  = $item->viewItemURL;
				$title = $item->title;

				$arr[] = array(
                                                        "vendor" => "ebay.".$this->region,
                                                        "name" => $item->title,
                                                        "price" => $item->sellingStatus->currentPrice,
                                                        "reg_price" => $item->sellingStatus->currentPrice,
                                                        "description" => "",
							"asin" => $item->itemId,
                                                        "images" => $item->galleryURL,
                                                        "link" => $item->viewItemURL
                                                      );
			}
		}

		return $arr;
	}

	private function constructPostCallAndGetResponse($query, $pageNum, $categoryName)
	{
		if($categoryName == -1 || $categoryName == 0){
			$categoryName = '';
		}

		$xmlrequest  = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
		$xmlrequest  .= "<findItemsAdvancedRequest xmlns=\"http://www.ebay.com/marketplace/search/v1/services\">";
		$xmlrequest  .= "<categoryId> {$categoryName} </categoryId>";
		$xmlrequest .= "<keywords>{$query}</keywords>";
		$xmlrequest .= "<affiliate> Affiliate";
		$xmlrequest .= "<networkId>9</networkId>";
		$xmlrequest .= "<trackingId>{$this->campaign_id}</trackingId>";
		$xmlrequest .= "</affiliate>";
		$xmlrequest .= "<paginationInput> PaginationInput";
		$xmlrequest .= "<entriesPerPage> {$this->per_page} </entriesPerPage>";
		$xmlrequest .= "<pageNumber> {$pageNum} </pageNumber>";
		$xmlrequest .= "</paginationInput>";
		$xmlrequest .= "</findItemsAdvancedRequest>";


		// Set up the HTTP headers
		$headers = array(
				'X-EBAY-SOA-OPERATION-NAME: findItemsByKeywords',
				'X-EBAY-SOA-SERVICE-VERSION: 1.3.0',
				'X-EBAY-SOA-REQUEST-DATA-FORMAT: XML',
				'X-EBAY-SOA-GLOBAL-ID: EBAY-US',
				"X-EBAY-SOA-SECURITY-APPNAME: {$this->app_id}",
				'Content-Type: text/xml;charset=utf-8',
				);

		$session  = curl_init($this->endpoint);                       // create a curl session
		curl_setopt($session, CURLOPT_POST, true);              // POST request type
		curl_setopt($session, CURLOPT_HTTPHEADER, $headers);    // set headers using $headers array
		curl_setopt($session, CURLOPT_POSTFIELDS, $xmlrequest); // set the body of the POST
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);    // return values as a string, not to std out

		$responsexml = curl_exec($session);                     // send the request
		curl_close($session);                                   // close the session
		return $responsexml;                                    // returns a string
	}
}
?>
