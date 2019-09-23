<?php
/**
 * Created by PhpStorm.
 * User :  keshtgar
 * Date :  6/28/19
 * Time : 10:29 AM
 *
 * $baseInfo BaseInfo
 */
namespace Pod\Product\Service;
require __DIR__ . '/../vendor/autoload.php';

use Pod\Base\Service\BaseService;
use Pod\Base\Service\ApiRequestHandler;

class ProductService extends BaseService
{
    private $header;
    private static $productApi;

    public function __construct($baseInfo)
    {
        parent::__construct();
        self::$jsonSchema = json_decode(file_get_contents(__DIR__. '/../jsonSchema.json'), true);
        $this->header = [
            '_token_issuer_'    =>  $baseInfo->getTokenIssuer(),
            '_token_'           => $baseInfo->getToken(),
        ];
        self::$productApi = require __DIR__ . '/../config/apiConfig.php';
    }

    public function addProduct($params, $apiName = 'addProduct') {
        $apiName = ($apiName == 'addSubProduct') ? 'addSubProduct' : 'addProduct'; # because only addProduct and addSubProduct are valid
        $header = $this->header;
        array_walk_recursive($params, 'self::prepareData');
//        $paramKey = 'query'; // for request with array parameters only GET method give a valid result in pod codes!
        $relativeUri = self::$productApi[$apiName]['subUri'];
        $option = [
            'headers' => $header,
            'query' => $params,
        ];

        self::validateOption($apiName, $option, 'query');

        // prepare params to send
        $withBracketParams = [];
        if (isset($params['attributes'])) {
            foreach ($params['attributes'] as $list){
                foreach ($list as $key => $value) {
                    $withBracketParams[$key][] = $value;
                }
            }
            unset($params['attributes']);
        }

        if(isset($params['tags']) && is_array($params['tags'])){
            $params['tags'] =  implode(',', $params['tags']);
        }

        if(isset($params['tagTrees']) && is_array($params['tagTrees'])){
            $params['tagTrees'] =  implode(',', $params['tagTrees']);
        }

        $option['withBracketParams'] = $withBracketParams;
        $option['withoutBracketParams'] = $params;
        //  unset `query` key because query string will be build in ApiRequestHandler and will be added to uri so dont need send again in query params
        unset($option['query']);
        return ApiRequestHandler::Request(
            self::$config[self::$serverType][self::$productApi[$apiName]['baseUri']],
            self::$productApi[$apiName]['method'],
            $relativeUri,
            $option,
            false,
            true
        );
    }

    public function addSubProduct($params) {
        return $this->addProduct($params, 'addSubProduct');
    }

    public function addProducts($params) {
        $apiName = 'addProducts';
        $header = $this->header;
        $header["Content-Type"] = 'application/x-www-form-urlencoded';
        $paramKey = 'form_params';
        $relativeUri = self::$productApi[$apiName]['subUri'];
        array_walk_recursive($params, 'self::prepareData');

        // prepare params to send
        foreach ($params['data'] as $dataKey => $data) {
            $optionPerData = [
                $paramKey => $data,
            ];
            self::validateOption($apiName, $optionPerData, $paramKey);
            if (isset($data['attributes'])) {
                foreach ($data['attributes'] as $list) {
                    foreach ($list as $key => $value) {
                        $params['data'][$dataKey][$key][] = $value;
                    }
                }
                unset($params['data'][$dataKey]['attributes']);
            }

            if(isset($data['tags']) && is_array($data['tags'])){
                $params['data'][$dataKey]['tags'] =  implode(',', $data['tags']);
            }

            if(isset($data['tagTrees']) && is_array($data['tagTrees'])){
                $params['data'][$dataKey]['tagTrees'] =  implode(',', $data['tagTrees']);
            }
        }

        $option = [
            'headers' => $header,
            $paramKey => ['data' => json_encode($params['data'])],
        ];
        return ApiRequestHandler::Request(
            self::$config[self::$serverType][self::$productApi[$apiName]['baseUri']],
            self::$productApi[$apiName]['method'],
            $relativeUri,
            $option
        );
    }

    public function updateProduct($params) {
        $apiName = 'updateProduct';
        $header = $this->header;
//        $paramKey = self::$subscriptionApi[$apiName]['method'] == 'GET' ? 'query' : 'form_params';
        $relativeUri = self::$productApi[$apiName]['subUri'];
        array_walk_recursive($params, 'self::prepareData');
        $option = [
            'headers' => $header,
            'query' => $params,
        ];

        self::validateOption($apiName, $option, 'query');
        // prepare params to send
        $withBracketParams = [];
        if (isset($params['attributes'])) {
            foreach ($params['attributes'] as $list) {
                foreach ($list as $key => $value) {
                    $withBracketParams[$key][] = $value;
                }
            }
            unset($params['attributes']);
        }

        if(isset($params['tags']) && is_array($params['tags'])){
            $params['tags'] =  implode(',', $params['tags']);
        }

        if(isset($params['tagTrees']) && is_array($params['tagTrees'])){
            $params['tagTrees'] =  implode(',', $params['tagTrees']);
        }

        $option['withBracketParams'] = $withBracketParams;
        $option['withoutBracketParams'] = $params;
        //  unset `query` key because query string will be build in ApiRequestHandler and will be added to uri so dont need send again in query params
        unset($option['query']);
        return ApiRequestHandler::Request(
            self::$config[self::$serverType][self::$productApi[$apiName]['baseUri']],
            self::$productApi[$apiName]['method'],
            $relativeUri,
            $option,
            false,
            true
        );
    }

    public function updateProducts($params) {
        $apiName = 'updateProducts';
        $header = $this->header;
        $header["Content-Type"] = 'application/x-www-form-urlencoded';
        $paramKey = 'form_params';
        $relativeUri = self::$productApi[$apiName]['subUri'];
        array_walk_recursive($params, 'self::prepareData');

        // prepare params to send
        foreach ($params['data'] as $dataKey => $data) {
            $optionPerData = [
                $paramKey => $data,
            ];
            self::validateOption($apiName, $optionPerData, $paramKey);
            if (isset($data['attributes'])) {
                foreach ($data['attributes'] as $list) {
                    foreach ($list as $key => $value) {
                        $params['data'][$dataKey][$key][] = $value;
                    }
                }
                unset($params['data'][$dataKey]['attributes']);
            }
            if(isset($data['tags']) && is_array($data['tags'])){
                $params['data'][$dataKey]['tags'] =  implode(',', $data['tags']);
            }

            if(isset($data['tagTrees']) && is_array($data['tagTrees'])){
                $params['data'][$dataKey]['tagTrees'] =  implode(',', $data['tagTrees']);
            }
        }

        $option = [
            'headers' => $header,
            $paramKey => ['data' => json_encode($params['data'])],
        ];
        return ApiRequestHandler::Request(
            self::$config[self::$serverType][self::$productApi[$apiName]['baseUri']],
            self::$productApi[$apiName]['method'],
            $relativeUri,
            $option,
            false,
            true
        );
    }

    public function getProductList($params) {
        $apiName = 'getProductList';
        $header = $this->header;
        // for this api we can use access token
        if (isset($params['accessToken'])) {
            $header["_token_"] = $params['accessToken'];
        }
        unset($params['accessToken']);

        $relativeUri = self::$productApi[$apiName]['subUri'];
        array_walk_recursive($params, 'self::prepareData');
        $option = [
            'headers' => $header,
            'query' => $params,
        ];

        self::validateOption($apiName, $option, 'query');
        // prepare params to send
        $withBracketParams = [];
        if (isset($params['attributes'])) {
            foreach ($params['attributes'] as $list) {
                foreach ($list as $key => $value) {
                    $withBracketParams[$key][] = $value;
                }
            }
            unset($params['attributes']);
        }

        if(isset($params['tags']) && is_array($params['tags'])){
            $params['tags'] =  implode(',', $params['tags']);
        }

        if(isset($params['tagTrees']) && is_array($params['tagTrees'])){
            $params['tagTrees'] =  implode(',', $params['tagTrees']);
        }

        $option['withBracketParams'] = $withBracketParams;
        $option['withoutBracketParams'] = $params;
        //  unset `query` key because query string will be build in ApiRequestHandler and will be added to uri so dont need send again in query params
        unset($option['query']);
        return ApiRequestHandler::Request(
            self::$config[self::$serverType][self::$productApi[$apiName]['baseUri']],
            self::$productApi[$apiName]['method'],
            $relativeUri,
            $option,
            false,
            true
        );
    }

    public function getBusinessProductList($params) {
        $apiName = 'getBusinessProductList';
        $header = $this->header;

//        $paramKey = self::$subscriptionApi[$apiName]['method'] == 'GET' ? 'query' : 'form_params';
        $relativeUri = self::$productApi[$apiName]['subUri'];
        array_walk_recursive($params, 'self::prepareData');
        $option = [
            'headers' => $header,
            'query' => $params,
        ];

        self::validateOption($apiName, $option, 'query');
        // prepare params to send
        $withBracketParams = [];
        if (isset($params['attributes'])) {
            foreach ($params['attributes'] as $list) {
                foreach ($list as $key => $value) {
                    $withBracketParams[$key][] = $value;
                }
            }
            unset($params['attributes']);
        }

        if(isset($params['tags']) && is_array($params['tags'])){
            $params['tags'] =  implode(',', $params['tags']);
        }

        if(isset($params['tagTrees']) && is_array($params['tagTrees'])){
            $params['tagTrees'] =  implode(',', $params['tagTrees']);
        }

        $option['withBracketParams'] = $withBracketParams;
        $option['withoutBracketParams'] = $params;
        //  unset `query` key because query string will be build in ApiRequestHandler and will be added to uri so dont need send again in query params
        unset($option['query']);
        return ApiRequestHandler::Request(
            self::$config[self::$serverType][self::$productApi[$apiName]['baseUri']],
            self::$productApi[$apiName]['method'],
            $relativeUri,
            $option,
            false,
            true
        );
    }

    public function getAttributeTemplateList($params) {
        $apiName = 'getAttributeTemplateList';
        $header = $this->header;
        // for this api we can use access token
        if (isset($params['accessToken'])) {
            $header["_token_"] = $params['accessToken'];
        }
        unset($params['accessToken']);

        $paramKey = self::$productApi[$apiName]['method'] == 'GET' ? 'query' : 'form_params';
        $relativeUri = self::$productApi[$apiName]['subUri'];
        array_walk_recursive($params, 'self::prepareData');
        $option = [
            'headers' => $header,
            $paramKey => $params,
        ];

        self::validateOption($apiName, $option, $paramKey);
        return ApiRequestHandler::Request(
            self::$config[self::$serverType][self::$productApi[$apiName]['baseUri']],
            self::$productApi[$apiName]['method'],
            $relativeUri,
            $option
        );
    }

    public function searchProduct($params) {
        $apiName = 'searchProduct';
        $header = $this->header;
        // for this api we can use access token
        if (isset($params['accessToken'])) {
            $header["_token_"] = $params['accessToken'];
        }
        unset($params['accessToken']);
//        $paramKey = self::$subscriptionApi[$apiName]['method'] == 'GET' ? 'query' : 'form_params';
        $relativeUri = self::$productApi[$apiName]['subUri'];
        array_walk_recursive($params, 'self::prepareData');
        $option = [
            'headers' => $header,
            'query' => $params,
        ];

        self::validateOption($apiName, $option, 'query');
        // prepare params to send
        if (isset($params['query'])) {
            $params['q'] = $params['query'];
            unset($params['query']);
        }
        $withBracketParams = [];
        if (isset($params['attributes'])) {
            foreach ($params['attributes'] as $list) {
                foreach ($list as $key => $value) {
                    $withBracketParams[$key][] = $value;
                }
            }
            unset($params['attributes']);
        }

        if(isset($params['tags']) && is_array($params['tags'])){
            $params['tags'] =  implode(',', $params['tags']);
        }

        if(isset($params['tagTrees']) && is_array($params['tagTrees'])){
            $params['tagTrees'] =  implode(',', $params['tagTrees']);
        }
        $option['withBracketParams'] = $withBracketParams;
        $option['withoutBracketParams'] = $params;
        //  unset `query` key because query string will be build in ApiRequestHandler and will be added to uri so dont need send again in query params
        unset($option['query']);
        return ApiRequestHandler::Request(
            self::$config[self::$serverType][self::$productApi[$apiName]['baseUri']],
            self::$productApi[$apiName]['method'],
            $relativeUri,
            $option,
            false,
            true
        );
    }
}