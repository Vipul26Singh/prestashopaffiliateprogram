<?php

abstract class Request
{
    private $apiUrl = 'http://gw.api.alibaba.com/openapi/param2/2/portals.open/[api_request_name]/[app_key]';
    protected abstract function getApiRequestName();
    protected abstract function getError($error);
    protected abstract function getRequestInputParams();

    public function getApiUrl($apiRequestName, $appKey)
    {
        $apiUrl = str_replace('[app_key]', $appKey, 
                str_replace('[api_request_name]', $apiRequestName, $this->apiUrl));
        
        return $apiUrl;
    }
}
