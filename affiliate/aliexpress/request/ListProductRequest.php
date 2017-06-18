<?php

require_once 'Request.php';

class ListProductRequest extends Request
{

	private $fields;
	private $keywords;
	private $pageNo;

	private $pageSize;
	private $categoryId;

	public function __construct(){
		$this->pageSize = 10;
	}

	public function setFields($fields)
	{
		$this->fields = $fields;
	}

	public function setKeywords($keywords)
	{
		$this->keywords = $keywords;
	}

	public function setCategoryId($categoryId)
	{
		$this->categoryId = $categoryId;
	}


	public function setPageNo($pageNo)
	{
		$this->pageNo = $pageNo;
	}

	public function setPageSize($pageSize)
	{
		$this->pageSize = $pageSize;
	}


	public function getApiRequestName()
	{
		return 'api.listPromotionProduct';
	}

	public function getRequestInputParams()
	{
		$params = array();

		foreach ($this as $key => $value) {
			if (!empty($value)) {
				$params[$key] = $value;
			}
		}
		if (!isset($params['fields']) || empty($params['fields'])) {
			$params['fields'] = $this->setDefaultFields();
		}

		return $params;
	}

	public function getError($error)
	{
		$aError = array(
				'20010000' => 'Call succeeds',
				'20020000' => 'System Error',
				'20030000' => 'Unauthorized transfer request',
				'20030010' => 'Required parameters',
				'20030020' => 'Invalid protocol format',
				'20030030' => 'API version input parameter error',
				'20030040' => 'API name space input parameter error',
				'20030050' => 'API name input parameter error',
				'20030060' => 'Fields input parameter error',
				'20030070' => 'Keyword input parameter error',
				'20030080' => 'Category ID input parameter error',
				'20030090' => 'Tracking ID input parameter error',
				'20030100' => 'Commission rate input parameter error',
				'20030110' => 'Original Price input parameter error',
				'20030120' => 'Discount input parameter error',
				'20030130' => 'Volume input parameter error',
				'20030140' => 'Page number input parameter error',
				'20030150' => 'Page size input parameter error',
				'20030160' => 'Sort input parameter error',
				'20030170' => 'Credit Score input parameter error',
				);

		if (isset($aError[$error])) {
			return $aError[$error];
		} else {
			return 'Unknown error.';
		}
	}

	private function setDefaultFields()
	{
		return 'totalResults,productId,productTitle,productUrl,imageUrl,'
			. 'originalPrice,salePrice,discount,evaluateScore,commission,'
			. 'commissionRate,30daysCommission,volume,packageType,'
			. 'lotNum,validTime';
	}
}

?>
