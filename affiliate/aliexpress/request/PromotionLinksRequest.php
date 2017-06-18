<?php

require_once 'Request.php';

class PromotionLinksRequest extends Request
{
	private $fields;
	private $trackingId;
	private $urls;

	public function setFields($fields)
	{
		$this->fields = $fields;
	}

	public function setTrackingId($trackingId)
	{
		$this->trackingId = $trackingId;
	}

	public function setUrls($urls)
	{
		if (is_array($urls)) {
			$urls = implode(",", $urls);
		}
		$this->urls = $urls;
	}

	public function getApiRequestName()
	{
		return 'api.getPromotionLinks';
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
				'20030070' => 'Tracking ID input parameter error',
				'20030080' => 'URL input parameter error or beyond the maximum number of the URLs',
			       );

		if (isset($aError[$error])) {
			return $aError[$error];
		} else {
			return 'Unknown error.';
		}
	}

	private function setDefaultFields()
	{
		return 'totalResults,trackingId,publisherId,url,promotionUrl';
	}
}
