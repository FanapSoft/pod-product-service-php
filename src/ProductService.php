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

use Pod\Base\Service\BaseService;
use Pod\Base\Service\ApiRequestHandler;
use Pod\Base\Service\Exception\ValidationException;

class ProductService extends BaseService
{
    private $header;
    private static $jsonSchema;
    private static $productApi;
    private static $serviceProductId;
    private static $baseUri;

    public function __construct($baseInfo)
    {
        parent::__construct();
        self::$jsonSchema = json_decode(file_get_contents(__DIR__ . '/../config/validationSchema.json'), true);
        $this->header = [
            '_token_issuer_'    =>  $baseInfo->getTokenIssuer(),
            '_token_'           => $baseInfo->getToken(),
        ];
        self::$productApi = require __DIR__ . '/../config/apiConfig.php';
        self::$serviceProductId = require __DIR__ . '/../config/serviceProductId.php';
        self::$serviceProductId = self::$serviceProductId[self::$serverType];
        self::$baseUri = self::$config[self::$serverType];
    }

    public function addProduct($params, $apiName = 'addProduct') {
        $apiName = ($apiName == 'addSubProduct') ? 'addSubProduct' : 'addProduct'; # because only addProduct and addSubProduct are valid
        $header = $this->header;
        array_walk_recursive($params, 'self::prepareData');
//        $paramKey = 'query'; // for request with array parameters only GET method give a valid result in pod codes!
        $relativeUri = self::$productApi[$apiName]['subUri'];

        // if apiToken is set replace it
        if (isset($params['apiToken'])) {
            $header["_token_"] = $params['apiToken'];
        }
        unset($params['apiToken']);

        $option = [
            'headers' => $header,
            'query' => $params,
        ];

        self::validateOption($option, self::$jsonSchema[$apiName], 'query');

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
        # set service call product Id
        $params['scProductId'] = self::$serviceProductId[$apiName];
        $option['withBracketParams'] = $withBracketParams;
        $option['withoutBracketParams'] = $params;
        //  unset `query` key because query string will be build in ApiRequestHandler and will be added to uri so dont need send again in query params
        unset($option['query']);
        return ApiRequestHandler::Request(
            self::$baseUri[self::$productApi[$apiName]['baseUri']],
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
        $optionHasArray = false;
        $header = $this->header;
        $header["Content-Type"] = 'application/x-www-form-urlencoded';
        $method = self::$productApi[$apiName]['method'];
        $paramKey = $method == 'GET' ? 'query' : 'form_params';
        $relativeUri = self::$productApi[$apiName]['subUri'];
        array_walk_recursive($params, 'self::prepareData');

        // if apiToken is set replace it
        if (isset($params['apiToken'])) {
            $header["_token_"] = $params['apiToken'];
        }
        unset($params['apiToken']);

        if(!isset($params['data']) || empty($params['data'])) {
            throw new ValidationException(['data' => ['The property data is required']], 'The property data is required.',ValidationException::VALIDATION_ERROR_CODE);
        }
        // prepare params to send
        foreach ($params['data'] as $dataKey => $data) {
            $optionPerData = [
                $paramKey => $data,
            ];

            self::validateOption($optionPerData, self::$jsonSchema[$apiName], $paramKey);
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

        # prepare params to send
        # set service call product Id
        $preparedParams['scProductId'] = self::$serviceProductId[$apiName];
        $preparedParams['data'] =  json_encode($params['data']);
        $option = [
            'headers' => $header,
            $paramKey => $preparedParams,
        ];

        if (isset($params['scVoucherHash'])) {
            $preparedParams['scVoucherHash'] = $params['scVoucherHash'];
            $option['withoutBracketParams'] =  $preparedParams;
            unset($option[$paramKey]);
            $optionHasArray = true;
            $method = 'GET';
        }

        return ApiRequestHandler::Request(
            self::$baseUri[self::$productApi[$apiName]['baseUri']],
            $method,
            $relativeUri,
            $option,
            false,
            $optionHasArray
        );
    }

    public function updateProduct($params) {
        $apiName = 'updateProduct';
        $header = $this->header;
//        $paramKey = self::$subscriptionApi[$apiName]['method'] == 'GET' ? 'query' : 'form_params';
        $relativeUri = self::$productApi[$apiName]['subUri'];
        array_walk_recursive($params, 'self::prepareData');

        // if apiToken is set replace it
        if (isset($params['apiToken'])) {
            $header["_token_"] = $params['apiToken'];
        }
        unset($params['apiToken']);

        $option = [
            'headers' => $header,
            'query' => $params,
        ];

        self::validateOption($option, self::$jsonSchema[$apiName], 'query');
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

        if (isset($params['categories'])){
            $withBracketParams['categories'] = $params['categories'];
            unset($params['categories']);
        }

        if(isset($params['tags']) && is_array($params['tags'])){
            $params['tags'] =  implode(',', $params['tags']);
        }

        if(isset($params['tagTrees']) && is_array($params['tagTrees'])){
            $params['tagTrees'] =  implode(',', $params['tagTrees']);
        }

        # set service call product Id
        $params['scProductId'] = self::$serviceProductId[$apiName];
        $option['withBracketParams'] = $withBracketParams;
        $option['withoutBracketParams'] = $params;
        //  unset `query` key because query string will be build in ApiRequestHandler and will be added to uri so dont need send again in query params
        unset($option['query']);
        return ApiRequestHandler::Request(
            self::$baseUri[self::$productApi[$apiName]['baseUri']],
            self::$productApi[$apiName]['method'],
            $relativeUri,
            $option,
            false,
            true
        );
    }

    public function updateProducts($params) {
        $apiName = 'updateProducts';
        $optionHasArray = false;
        $header = $this->header;
        $header["Content-Type"] = 'application/x-www-form-urlencoded';
        $method = self::$productApi[$apiName]['method'];
        $paramKey = $method == 'GET' ? 'query' : 'form_params';
        $relativeUri = self::$productApi[$apiName]['subUri'];
        array_walk_recursive($params, 'self::prepareData');

        // if apiToken is set replace it
        if (isset($params['apiToken'])) {
            $header["_token_"] = $params['apiToken'];
        }
        unset($params['apiToken']);

        // prepare params to send
        if(!isset($params['data']) || empty($params['data'])) {
            throw new ValidationException(['data' => ['The property data is required']], 'The property data is required.',ValidationException::VALIDATION_ERROR_CODE);
        }
        foreach ($params['data'] as $dataKey => $data) {
            $optionPerData = [
                $paramKey => $data,
            ];
            self::validateOption($optionPerData, self::$jsonSchema[$apiName], $paramKey);
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

        # prepare params to send
        # set service call product Id
        $preparedParams['scProductId'] = self::$serviceProductId[$apiName];
        $preparedParams['data'] =  json_encode($params['data']);

        $option = [
            'headers' => $header,
            $paramKey => $preparedParams,
        ];

        if (isset($params['scVoucherHash'])) {
            $preparedParams['scVoucherHash'] = $params['scVoucherHash'];
            $option['withoutBracketParams'] =  $preparedParams;
            unset($option[$paramKey]);
            $optionHasArray = true;
            $method = 'GET';
        }

        return ApiRequestHandler::Request(
            self::$baseUri[self::$productApi[$apiName]['baseUri']],
            $method,
            $relativeUri,
            $option,
            false,
            $optionHasArray
        );
    }

    public function getProductList($params) {
        $apiName = 'getProductList';
        $header = $this->header;

        $relativeUri = self::$productApi[$apiName]['subUri'];
        array_walk_recursive($params, 'self::prepareData');

        // for this api we can use access token
        // if token is set replace it
        if (isset($params['token'])) {
            $header["_token_"] = $params['token'];
        }
        unset($params['token']);

        $option = [
            'headers' => $header,
            'query' => $params,
        ];

        self::validateOption($option, self::$jsonSchema[$apiName], 'query');
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

        # set service call product Id
        $params['scProductId'] = self::$serviceProductId[$apiName];
        $option['withBracketParams'] = $withBracketParams;
        $option['withoutBracketParams'] = $params;
        //  unset `query` key because query string will be build in ApiRequestHandler and will be added to uri so dont need send again in query params
        unset($option['query']);
        return ApiRequestHandler::Request(
            self::$baseUri[self::$productApi[$apiName]['baseUri']],
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

        // if token is set replace it
        if (isset($params['token'])) {
            $header["_token_"] = $params['token'];
        }

        unset($params['token']);
        $option = [
            'headers' => $header,
            'query' => $params,
        ];

        self::validateOption($option, self::$jsonSchema[$apiName], 'query');
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

        if (isset($params['attributeSearchQuery']) && is_array($params['attributeSearchQuery'])) {
            $params['attributeSearchQuery'] = json_encode($params['attributeSearchQuery']);
        }
        # set service call product Id
        $params['scProductId'] = self::$serviceProductId[$apiName];
        $option['withBracketParams'] = $withBracketParams;
        $option['withoutBracketParams'] = $params;
        //  unset `query` key because query string will be build in ApiRequestHandler and will be added to uri so dont need send again in query params
        unset($option['query']);
        return ApiRequestHandler::Request(
            self::$baseUri[self::$productApi[$apiName]['baseUri']],
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
        $optionHasArray = false;

        $paramKey = self::$productApi[$apiName]['method'] == 'GET' ? 'query' : 'form_params';
        $relativeUri = self::$productApi[$apiName]['subUri'];
        array_walk_recursive($params, 'self::prepareData');

        // for this api we can use access token
        // if token is set replace it
        if (isset($params['token'])) {
            $header["_token_"] = $params['token'];
        }

        unset($params['token']);
        $option = [
            'headers' => $header,
            $paramKey => $params,
        ];

        self::validateOption($option, self::$jsonSchema[$apiName], $paramKey);

        # prepare params to send
        # set service call product Id
        $option[$paramKey]['scProductId'] = self::$serviceProductId[$apiName];

        if (isset($params['scVoucherHash'])) {
            $option['withoutBracketParams'] =  $option[$paramKey];
            $optionHasArray = true;
            unset($option[$paramKey]);
        }
        return ApiRequestHandler::Request(
            self::$baseUri[self::$productApi[$apiName]['baseUri']],
            self::$productApi[$apiName]['method'],
            $relativeUri,
            $option,
            false,
            $optionHasArray
        );
    }

    public function searchProduct($params) {
        $apiName = 'searchProduct';
        $header = $this->header;
//        $paramKey = self::$subscriptionApi[$apiName]['method'] == 'GET' ? 'query' : 'form_params';
        $relativeUri = self::$productApi[$apiName]['subUri'];
        array_walk_recursive($params, 'self::prepareData');

        // for this api we can use access token
        // if token is set replace it
        if (isset($params['token'])) {
            $header["_token_"] = $params['token'];
        }
        unset($params['token']);

        $option = [
            'headers' => $header,
            'query' => $params,
        ];

        self::validateOption($option, self::$jsonSchema[$apiName], 'query');
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

        # set service call product Id
        $params['scProductId'] = self::$serviceProductId[$apiName];
        $option['withBracketParams'] = $withBracketParams;
        $option['withoutBracketParams'] = $params;
        //  unset `query` key because query string will be build in ApiRequestHandler and will be added to uri so dont need send again in query params
        unset($option['query']);
        return ApiRequestHandler::Request(
            self::$baseUri[self::$productApi[$apiName]['baseUri']],
            self::$productApi[$apiName]['method'],
            $relativeUri,
            $option,
            false,
            true
        );
    }

    public function searchSubProduct($params) {
        $apiName = 'searchSubProduct';
        $header = $this->header;
//        $paramKey = self::$subscriptionApi[$apiName]['method'] == 'GET' ? 'query' : 'form_params';
        $relativeUri = self::$productApi[$apiName]['subUri'];
        array_walk_recursive($params, 'self::prepareData');

        // for this api we can use access token
        // if token is set replace it
        if (isset($params['token'])) {
            $header["_token_"] = $params['token'];
        }
        unset($params['token']);

        $option = [
            'headers' => $header,
            'query' => $params,
        ];

        self::validateOption($option, self::$jsonSchema[$apiName], 'query');
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

        # set service call product Id
        $params['scProductId'] = self::$serviceProductId[$apiName];
        $option['withBracketParams'] = $withBracketParams;
        $option['withoutBracketParams'] = $params;
        //  unset `query` key because query string will be build in ApiRequestHandler and will be added to uri so dont need send again in query params
        unset($option['query']);
        return ApiRequestHandler::Request(
            self::$baseUri[self::$productApi[$apiName]['baseUri']],
            self::$productApi[$apiName]['method'],
            $relativeUri,
            $option,
            false,
            true
        );
    }
}