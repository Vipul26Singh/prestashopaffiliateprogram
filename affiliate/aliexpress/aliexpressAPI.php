<?php
require_once 'request/PromotionLinksRequest.php';
require_once 'request/ListProductRequest.php';

class aliexpressAPI
{
	private $per_page;
	private $region;
	private $appKey;
	private $appSecret;
	private $trackingId;

	public function __construct($a, $b, $c)
	{
		$this->per_page = 10;
		$this->region = "com";
		$this->appKey = $a;
		$this->appSecret = $b;
		$this->trackingId = $c;
	}


	public function searchProductHelper($search, $category, $page)
	{
		$arr = array();

		$resp = $this->constructPostCallAndGetResponse($search, $page, $category);

		if ($resp->errorCode == 20010000) {

		
			foreach($resp->result->products as $item) {
				$promoLink = getPromotionLinks($item->productUrl);

				$arr[] = array(
						"vendor" => "aliexpress.".$this->region,
						"name" => $item->productTitle,
						"price" => $item->originalPrice,
						"reg_price" => $item->salePrice,
						"description" => "",
						"asin" => $item->productId,
						"images" => $item->imageUrl,
						"link" => $promoLink
					      );
			}
		}

		return $arr;
	}

	private function constructPostCallAndGetResponse($query, $pageNum, $categoryName)
	{
		$request = new ListProductRequest();

		$request->setCategoryId($categoryName);
		$request->setKeywords($query);
		$request->setPageNo($pageNum);

		$responce = $this->getData($request);

		return $responce;
	}

	private function getData(Request $request)
	{
		$apiUrl = $request->getApiUrl($request->getApiRequestName(), $this->appKey);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $apiUrl);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(
					$request->getRequestInputParams($request)));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		$responce = curl_exec($ch);

		$content = json_decode($responce);

		if ($content->errorCode !== 20010000) {
			$errorMsg = $request->getError($content->errorCode);
			$error = new stdClass();
			$error->status = 'ERROR';
			$error->errorCode = $content->errorCode;
			$error->errorMsg = $errorMsg;
			$content = $error;
		}

		curl_close($ch);

		return $content;
	}

	private function getPromotionLinks($urls)
        {
                $request = new PromotionLinksRequest();
                $request->setTrackingId($this->trackingId);
                $request->setUrls($urls);

                $responce = $this->getData($request);

                return $responce;
        }
}
?>
