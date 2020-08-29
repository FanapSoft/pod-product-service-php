<?php
use PHPUnit\Framework\TestCase;
use Pod\Product\Service\ProductService;
use Pod\Base\Service\BaseInfo;
use Pod\Base\Service\Exception\ValidationException;
use Pod\Base\Service\Exception\PodException;

final class ProductServiceTest extends TestCase
{
//    public static $apiToken;
    public static $productService;
    const TOKEN_ISSUER = 1;
    const API_TOKEN = '{Put API Token}';
    const API_TOKEN_12582 = '{Put API Token2}';
    const API_TOKEN_3 = '{Put API Token3}';
    const ACCESS_TOKEN = '{Put ACCESS Token}';
    const CLIENT_ID = '{Put Client ID}';
    const CLIENT_SECRET = '{Put Client Secret}';
    const CONFIRM_CODE = '{Put Confirmation Code}';
    const INFORMATION_TECHNOLOGY_GUILD = 'INFORMATION_TECHNOLOGY_GUILD';
    const TOILETRIES_GUILD = 'TOILETRIES_GUILD';

    public function setUp(): void
    {
        parent::setUp();
        # set serverType to SandBox or Production
        BaseInfo::initServerType(BaseInfo::SANDBOX_SERVER);

        $baseInfo = new BaseInfo();
        $baseInfo->setTokenIssuer(self::TOKEN_ISSUER);
        $baseInfo->setToken(self::API_TOKEN);

        self::$productService = new ProductService($baseInfo);
    }

    public function testAddProductAllParameters()
    {
        $productName = uniqid('testProduct');

        $params =
            [
                # ================ *Required Parameters  ==================
                'name'                  => $productName,
                'canComment'            => true,
                'canLike'               => true,
                'enable'                => true,
                'availableCount'        => 100,
//                'unlimited'             => true,     # default : false
                'price'                 => 1000,
                'discount'              => 0,
            'description'           => 'Unit Test Production',
            # ============= Optional Parameters  =======================
                'apiToken'              => self::API_TOKEN,
                'guildCode'             => self::INFORMATION_TECHNOLOGY_GUILD,
                'parentProductId'       => 42098,
                'uniqueId'              => uniqid(),
                'metaData'              => '{"test":"true"}',
                'businessId'            => 12121,
                'allowUserInvoice'      =>  true,    # default : false
                'allowUserPrice'        =>  true,    # default : false
                'currencyCode'          => 'IRR',
                'attTemplateCode'       => 'مانتو',
                'attributes'            =>
                [
                    [
                        "attCode"       => "gender",
                        "attValue"      => "زن",
                        "attGroup"      => false,
                    ],
                    [
                        "attCode"       => "color",
                        "attValue"      => "سبز",
                        "attGroup"      => false,
                    ],
                    [
                        "attCode"       => "size",
                        "attValue"      => "M",
                        "attGroup"      => true,
                    ],
                ],
                "lat"                   => 43.787568,
                "lng"                   => 74.9890685,
                "tags"                  => ["tag1", "tag2"],
                'content'               => 'Unit test product content',
                'previewImage'          => 'Unit test product image address',
                "tagTrees"              => ['Tag tree level 01'],
                "tagTreeCategoryName"   => "TestTagCategory5dd13cef16902",
                'preferredTaxRate'      => 0.09,
                'quantityPrecision'     => 2,
                'scVoucherHash'     => ['Put Service Call Voucher Hashes'],
                'scApiKey'           => 'Put service call Api Key',
        ];;

        try {
            $result = self::$productService->addProduct($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testAddProductRequiredParameters()
    {

        $productName = uniqid('addProductTest');
        $params =
            [
                # ================ *Required Parameters  ==================
                'name'                  => $productName,
                'canComment'            => true,
                'canLike'               => true,
                'enable'                => true,
                'availableCount'        => 100,
//                'unlimited'             => true,     # default : false
                'price'                 => 1000,
                'discount'              => 0,
                'description'           => 'Unit Test Production',
        ];

        try {
            $result = self::$productService->addProduct($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testAddProductValidationError()
    {
        $paramsWithoutRequired = [];
        $paramsWrongValue = [
# ================ *Required Parameters  ==================
                'name'                  => 123,
                'canComment'            => 1,
                'canLike'               => 1,
                'enable'                => 1,
                'availableCount'        => '100',
                'unlimited'             => true,     # default : false
                'price'                 => '1000',
                'discount'              => '0',
                'description'           => 123,
        ];
        try {
            self::$productService->addProduct($paramsWithoutRequired);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();

            $this->assertArrayHasKey('name', $validation);
            $this->assertEquals('The property name is required', $validation['name'][0]);

            $this->assertArrayHasKey('canComment', $validation);
            $this->assertEquals('The property canComment is required', $validation['canComment'][0]);

            $this->assertArrayHasKey('canLike', $validation);
            $this->assertEquals('The property canLike is required', $validation['canLike'][0]);

            $this->assertArrayHasKey('enable', $validation);
            $this->assertEquals('The property enable is required', $validation['enable'][0]);

            $this->assertArrayHasKey('availableCount', $validation);
            $this->assertEquals('The property availableCount is required', $validation['availableCount'][0]);

            $this->assertArrayHasKey('price', $validation);
            $this->assertEquals('The property price is required', $validation['price'][0]);

            $this->assertArrayHasKey('discount', $validation);
            $this->assertEquals('The property discount is required', $validation['discount'][0]);

            $this->assertArrayHasKey('description', $validation);
            $this->assertEquals('The property description is required', $validation['description'][0]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
        try {
            self::$productService->addProduct($paramsWrongValue);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();

            $this->assertArrayHasKey('name', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['name'][1]);

            $this->assertArrayHasKey('canComment', $validation);
            $this->assertEquals('Integer value found, but a string or a boolean is required', $validation['canComment'][1]);

            $this->assertArrayHasKey('canLike', $validation);
            $this->assertEquals('Integer value found, but a string or a boolean is required', $validation['canLike'][1]);

            $this->assertArrayHasKey('enable', $validation);
            $this->assertEquals('Integer value found, but a string or a boolean is required', $validation['enable'][1]);

            $this->assertArrayHasKey('availableCount', $validation);
            $this->assertEquals('String value found, but a number is required', $validation['availableCount'][1]);

            $this->assertArrayHasKey('price', $validation);
            $this->assertEquals('String value found, but a number is required', $validation['price'][1]);

            $this->assertArrayHasKey('discount', $validation);
            $this->assertEquals('String value found, but a number is required', $validation['discount'][1]);

            $this->assertArrayHasKey('oneOf', $validation);
            $this->assertEquals('Failed to match exactly one schema', $validation['oneOf'][1]);


            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testAddSubProductAllParameters()
    {
        $productName = uniqid('testSubProduct');

        $params = [
            ## ============================ *Required Parameters  =============================
            "name"                  => $productName,
            "availableCount"        => 100,
            "price"                 => 50000,
            "discount"              => 10,
            "groupId"               => 3684,
            ## =========================== Optional Parameters  ================================
            'apiToken'                 => self::API_TOKEN,
            "guildCode"             => 'TOILETRIES_GUILD',
            "parentProductId"       => 24877,
            "description"           => "زیرمحصول",
            "uniqueId"              => "tttttttttttttte444",
            "metaData"              => "{'name':'pro'}",
        "businessId"                => 12121,
//            "unlimited"             => true,
            "allowUserInvoice"      => true,
            "allowUserPrice"        => true,
            "currencyCode"          => "EUR",
            "attributes"            =>
                [
                    [
                        "attCode"       => "gender",
                        "attValue"      => "زن",
                        "attGroup"      => false,
                    ],
                    [
                        "attCode"       => "color",
                        "attValue"      => "صورتی",
                        "attGroup"      => false,
                    ],

                    [
                        "attCode"       => "size",
                        "attValue"      => "XL",
                        "attGroup"      => true,
                    ],

                ],
            "tags"                  => ["tag1", "tag3"],
            "content"               => "5456454",
            "previewImage"          => true,
            "tagTrees"              => "Tag tree level 1",
            "tagTreeCategoryName"   => "TestTagCategory5dd13cef16902",
            "preferredTaxRate"      => 0.08,
            "quantityPrecision"     => 3,

        ];

        try {
            $result = self::$productService->addSubProduct($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testAddSubProductRequiredParameters()
    {
        $productName = uniqid('addSubProductTest');
        $params =
            [
                # ================ *Required Parameters  ==================
                "name"                  => $productName,
                "availableCount"        => 100,
                "price"                 => 50000,
                "discount"              => 10,
                "groupId"               => 3684,
                "attributes"            =>
                    [
                        [
                            "attCode"       => "size",
                            "attValue"      => "XL",
                            "attGroup"      => true,
                        ],

                    ],
        ];

        try {
            $result = self::$productService->addSubProduct($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testAddSubProductValidationError()
    {
        $paramsWithoutRequired = [];
        try {
            self::$productService->addSubProduct($paramsWithoutRequired);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $result = $e->getResult();

            $this->assertArrayHasKey('name', $validation);
            $this->assertEquals('The property name is required', $validation['name'][0]);

            $this->assertArrayHasKey('groupId', $validation);
            $this->assertEquals('The property groupId is required', $validation['groupId'][0]);

            $this->assertArrayHasKey('availableCount', $validation);
            $this->assertEquals('The property availableCount is required', $validation['availableCount'][0]);

            $this->assertArrayHasKey('price', $validation);
            $this->assertEquals('The property price is required', $validation['price'][0]);

            $this->assertArrayHasKey('discount', $validation);
            $this->assertEquals('The property discount is required', $validation['discount'][0]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testAddProductsAllParameters()
    {
        $productName1 = uniqid('addProducts1Test');
        $productName2 = uniqid('addProducts2Test');

        $params =
            [
                'scVoucherHash'     => ['{Put Service Call Voucher Hashes}'],
                'scApiKey'           => '{Put service call Api Key}',
                'apiToken'                 => self::API_TOKEN,
                'data' => [
                    [
                        # ================ *Required Parameters  ==================
                        'name'                  => $productName1,
                        'canComment'            => true,
                        'canLike'               => true,
                        'enable'                => true,
                        'availableCount'        => 100,
//                'unlimited'             => true,     # default : false
                        'price'                 => 1000,
                        'discount'              => 0,
                        'description'           => 'Unit Test Production',
                        # ============= Optional Parameters  =======================
                        'guildCode'             => self::INFORMATION_TECHNOLOGY_GUILD,
                        'parentProductId'       => 42098,
                        'uniqueId'              => uniqid(),
                        'metaData'              => '{"test":"true"}',
                        'businessId'            => 12121,
                        'allowUserInvoice'      =>  true,    # default : false
                        'allowUserPrice'        =>  true,    # default : false
                        'currencyCode'          => 'IRR',
                        'attTemplateCode'       => 'مانتو',
                        'attributes'            =>
                            [
                                [
                                    "attCode"       => "gender",
                                    "attValue"      => "زن",
                                    "attGroup"      => false,
                                ],
                                [
                                    "attCode"       => "color",
                                    "attValue"      => "سبز",
                                    "attGroup"      => false,
                                ],
                                [
                                    "attCode"       => "size",
                                    "attValue"      => "M",
                                    "attGroup"      => true,
                                ],
                            ],
                        "lat"                   => 43.787568,
                        "lng"                   => 74.9890685,
                        "tags"                  => ["tag1", "tag2"],
                        'content'               => 'Unit test product content',
                        'previewImage'          => 'Unit test product image address',
                        "tagTrees"              => ['Tag tree level 01'],
                        "tagTreeCategoryName"   => "TestTagCategory5dd13cef16902",
                        'preferredTaxRate'      => 0.09,
                        'quantityPrecision'     => 2,
                        'scVoucherHash'     => ['Put Service Call Voucher Hashes'],
                        'scApiKey'           => 'Put service call Api Key',
                    ],
                    [
                        # ================ *Required Parameters  ==================
                        'name'                  => $productName2,
                        'canComment'            => true,
                        'canLike'               => true,
                        'enable'                => true,
                        'availableCount'        => 100,
//                'unlimited'             => true,     # default : false
                        'price'                 => 1000,
                        'discount'              => 0,
                        'description'           => 'Unit Test Production',
                        # ============= Optional Parameters  =======================
                        'guildCode'             => self::INFORMATION_TECHNOLOGY_GUILD,
                        'parentProductId'       => 42098,
                        'uniqueId'              => uniqid(),
                        'metaData'              => '{"test":"true"}',
                        'businessId'            => 12121,
                        'allowUserInvoice'      =>  true,    # default : false
                        'allowUserPrice'        =>  true,    # default : false
                        'currencyCode'          => 'IRR',
                        'attTemplateCode'       => 'مانتو',
                        'attributes'            =>
                            [
                                [
                                    "attCode"       => "gender",
                                    "attValue"      => "زن",
                                    "attGroup"      => false,
                                ],
                                [
                                    "attCode"       => "color",
                                    "attValue"      => "سبز",
                                    "attGroup"      => false,
                                ],
                                [
                                    "attCode"       => "size",
                                    "attValue"      => "M",
                                    "attGroup"      => true,
                                ],
                            ],
                        "lat"                   => 43.787568,
                        "lng"                   => 74.9890685,
                        "tags"                  => ["tag1", "tag2"],
                        'content'               => 'Unit test product content',
                        'previewImage'          => 'Unit test product image address',
                        "tagTrees"              => ['Tag tree level 01'],
                        "tagTreeCategoryName"   => "TestTagCategory5dd13cef16902",
                        'preferredTaxRate'      => 0.09,
                        'quantityPrecision'     => 2,
                        'scVoucherHash'     => ['Put Service Call Voucher Hashes'],
                        'scApiKey'           => 'Put service call Api Key',
                    ],
            ]
        ];

        try {
            $result = self::$productService->addProducts($params);
            $this->assertFalse($result['hasError']);

        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testAddProductsRequiredParameters()
    {
        $productName1 = uniqid('addProducts1Test');
        $productName2 = uniqid('addProducts2Test');
        $params =
            [
                'scVoucherHash'     => ['{Put Service Call Voucher Hashes}'],
                'scApiKey'           => '{Put service call Api Key}',
                'apiToken'                 => self::API_TOKEN,
                'data' => [
                    [
                        # ================ *Required Parameters  ==================
                        'name'                  => $productName1,
                        'canComment'            => true,
                        'canLike'               => true,
                        'enable'                => true,
                        'availableCount'        => 100,
//                'unlimited'             => true,     # default : false
                        'price'                 => 1000,
                        'discount'              => 0,
                        'description'           => 'Unit Test Production',
                    ],
                    [
                        # ================ *Required Parameters  ==================
                        'name'                  => $productName2,
                        'canComment'            => true,
                        'canLike'               => true,
                        'enable'                => true,
                        'availableCount'        => 100,
//                'unlimited'             => true,     # default : false
                        'price'                 => 1000,
                        'discount'              => 0,
                        'description'           => 'Unit Test Production',
                    ],
                ]
            ];

        try {
            $result = self::$productService->addProducts($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testAddProductsValidationError()
    {
        $params1 = [];
        $params2 = [
            'data' =>  [
                [
                    'content'               => 'Unit test product content',
            ]
        ]];
        $wrongValueParams = [
            'scVoucherHash'     => ['{Put Service Call Voucher Hashes}'],
            'scApiKey'           => '{Put service call Api Key}',
            'apiToken'                 => self::API_TOKEN,
            'data' => [
                [
                    # ================ *Required Parameters  ==================
                    'name'                  => 123,
                    'canComment'            => 1,
                    'canLike'               => 1,
                    'enable'                => 1,
                    'availableCount'        => '100',
                    'unlimited'             => true,     # default : false
                    'price'                 => '1000',
                    'discount'              => '0',
                    'description'           => 123,
                ]
            ]
        ];
        try {
            self::$productService->addProducts($params1);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);
            $result = $e->getResult();
            $this->assertArrayHasKey('data', $validation);
            $this->assertEquals('The property data is required', $validation['data'][0]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }

        try {
            self::$productService->addProducts($params2);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);
            $result = $e->getResult();
            $this->assertArrayHasKey('name', $validation);
            $this->assertEquals('The property name is required', $validation['name'][0]);

            $this->assertArrayHasKey('canComment', $validation);
            $this->assertEquals('The property canComment is required', $validation['canComment'][0]);

            $this->assertArrayHasKey('canLike', $validation);
            $this->assertEquals('The property canLike is required', $validation['canLike'][0]);

            $this->assertArrayHasKey('enable', $validation);
            $this->assertEquals('The property enable is required', $validation['enable'][0]);

            $this->assertArrayHasKey('availableCount', $validation);
            $this->assertEquals('The property availableCount is required', $validation['availableCount'][0]);

            $this->assertArrayHasKey('price', $validation);
            $this->assertEquals('The property price is required', $validation['price'][0]);

            $this->assertArrayHasKey('discount', $validation);
            $this->assertEquals('The property discount is required', $validation['discount'][0]);

            $this->assertArrayHasKey('description', $validation);
            $this->assertEquals('The property description is required', $validation['description'][0]);


            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }

        try {
            self::$productService->addProducts($wrongValueParams);
        } catch (ValidationException $e) {
            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);
            $result = $e->getResult();
            $this->assertArrayHasKey('name', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['name'][1]);

            $this->assertArrayHasKey('description', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['description'][1]);

            $this->assertArrayHasKey('canComment', $validation);
            $this->assertEquals('Integer value found, but a string or a boolean is required', $validation['canComment'][1]);

            $this->assertArrayHasKey('canLike', $validation);
            $this->assertEquals('Integer value found, but a string or a boolean is required', $validation['canLike'][1]);

            $this->assertArrayHasKey('enable', $validation);
            $this->assertEquals('Integer value found, but a string or a boolean is required', $validation['enable'][1]);

            $this->assertArrayHasKey('availableCount', $validation);
            $this->assertEquals('String value found, but a number is required', $validation['availableCount'][1]);

            $this->assertArrayHasKey('price', $validation);
            $this->assertEquals('String value found, but a number is required', $validation['price'][1]);

            $this->assertArrayHasKey('discount', $validation);
            $this->assertEquals('String value found, but a number is required', $validation['discount'][1]);

            $this->assertArrayHasKey('oneOf', $validation);
            $this->assertEquals('Failed to match exactly one schema', $validation['oneOf'][1]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testUpdateProductAllParameters()
    {
        $params =
            [
            ## ============================ *Required Parameters  =============================
            "entityId"              => 29990,
            "name"                  => "ویرایش محصول تست کیس",
            "description"           => "ویرایش محصول",
            "canComment"            => true,
            "canLike"               => true,
            "enable"                => false,
            "price"                 => 40000,
            "discount"              => 10,
            "changePreview"         => true,
//            "availableCount"        => 200,  # تعداد موجود از محصول درصورت بدون محدودیت نبودن اجباری است
            "unlimited"             => true, # بدون محدودیت بودن محصول true/false
            ## =========================== Optional Parameters  ================================
            'apiToken'              => self::API_TOKEN_3,
            "guildCode"             => 'CLOTHING_GUILD',
//            "version"               => 55,
            "metaData"              => "{'name':'pro'}",
            "allowUserInvoice"      => false,
            "allowUserPrice"        => false,
           "attTemplateCode"       => "پیراهن مردانه",
            "attributes"            =>
                [
                    [
                        "attCode"       => "gender",
                        "attValue"      => "مرد",
                        "attGroup"      => false,
                    ],
                    [
                        "attCode"       => "color",
                        "attValue"      => "سفید",
                        "attGroup"      => false,
                    ],
                    [
                        "attCode"       => "size",
                        "attValue"      => "XL",
                        "attGroup"      => true,
                    ],

                ],
//            "groupId"               => 3684,
            "lat"                   => 45.78756867,
            "lng"                   => 76.989068567,
            "tags"                  => ["شلوار", "شلوار لی"],
            "content"               => "unitTest",
            "previewImage"          => true,
            "tagTrees"              => ["تگ سوم", "تگ دوم - اول"],
//            "tagTreeCategoryName"   => "ویژه",
            'preferredTaxRate'      => 0.09,
            'quantityPrecision'     => 2,
        ];
        try {
            $result = self::$productService->UpdateProduct($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testUpdateProductRequiredParameters()
    {
        $params =
            [
                ## ============================ *Required Parameters  =============================
                "entityId"              => 29990,
                "name"                  => "ویرایش محصول تست کیس",
                "description"           => "ویرایش محصول",
                "canComment"            => true,
                "canLike"               => true,
                "enable"                => false,
                "price"                 => 40000,
                "discount"              => 10,
                "changePreview"         => true,
//            "availableCount"        => 200,  # تعداد موجود از محصول درصورت بدون محدودیت نبودن اجباری است
                "unlimited"             => true, # بدون محدودیت بودن محصول true/false
               ## =========================== Optional Parameters  ================================
            'apiToken'              => self::API_TOKEN_3,
        ];
        try {
            $result = self::$productService->UpdateProduct($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testUpdateProductRequiredParametersValidation()
    {
        $paramsWithoutRequired = [];
        try {
            self::$productService->UpdateProduct($paramsWithoutRequired);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);
            $result = $e->getResult();
            $this->assertArrayHasKey('name', $validation);
            $this->assertEquals('The property name is required', $validation['name'][0]);

            $this->assertArrayHasKey('description', $validation);
            $this->assertEquals('The property description is required', $validation['description'][0]);

            $this->assertArrayHasKey('canComment', $validation);
            $this->assertEquals('The property canComment is required', $validation['canComment'][0]);

            $this->assertArrayHasKey('canLike', $validation);
            $this->assertEquals('The property canLike is required', $validation['canLike'][0]);

            $this->assertArrayHasKey('enable', $validation);
            $this->assertEquals('The property enable is required', $validation['enable'][0]);

            $this->assertArrayHasKey('entityId', $validation);
            $this->assertEquals('The property entityId is required', $validation['entityId'][0]);

            $this->assertArrayHasKey('price', $validation);
            $this->assertEquals('The property price is required', $validation['price'][0]);

            $this->assertArrayHasKey('discount', $validation);
            $this->assertEquals('The property discount is required', $validation['discount'][0]);

            $this->assertArrayHasKey('changePreview', $validation);
            $this->assertEquals('The property changePreview is required', $validation['changePreview'][0]);

            $this->assertArrayHasKey('unlimited', $validation);
            $this->assertEquals('The property unlimited is required', $validation['unlimited'][0]);

            $this->assertArrayHasKey('availableCount', $validation);
            $this->assertEquals('The property availableCount is required', $validation['availableCount'][0]);

            $this->assertArrayHasKey('oneOf', $validation);
            $this->assertEquals('Failed to match exactly one schema', $validation['oneOf'][0]);


            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
        $wrongValueParams = [
            ## ============================ *Required Parameters  =============================
            "entityId"              => '123',
            "name"                  => 123,
            "description"           => 123,
            "canComment"            => 123,
            "canLike"               => 123,
            "enable"                => 123,
            "price"                 => '123',
            "discount"              => '123',
            "changePreview"         => 123,
            "availableCount"        => '123',  # تعداد موجود از محصول درصورت بدون محدودیت نبودن اجباری است
            "unlimited"             => 123, # بدون محدودیت بودن محصول true/false
        ];
        try {
            self::$productService->UpdateProduct($wrongValueParams);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);
            $result = $e->getResult();
            $this->assertArrayHasKey('name', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['name'][1]);

            $this->assertArrayHasKey('description', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['description'][1]);

            $this->assertArrayHasKey('canComment', $validation);
            $this->assertEquals('Integer value found, but a string or a boolean is required', $validation['canComment'][1]);

            $this->assertArrayHasKey('canLike', $validation);
            $this->assertEquals('Integer value found, but a string or a boolean is required', $validation['canLike'][1]);

            $this->assertArrayHasKey('enable', $validation);
            $this->assertEquals('Integer value found, but a string or a boolean is required', $validation['enable'][1]);

            $this->assertArrayHasKey('entityId', $validation);
            $this->assertEquals('String value found, but an integer is required', $validation['entityId'][1]);

            $this->assertArrayHasKey('price', $validation);
            $this->assertEquals('String value found, but a number is required', $validation['price'][1]);

            $this->assertArrayHasKey('discount', $validation);
            $this->assertEquals('String value found, but a number is required', $validation['discount'][1]);

            $this->assertArrayHasKey('changePreview', $validation);
            $this->assertEquals('Integer value found, but a string or a boolean is required', $validation['changePreview'][1]);

            $this->assertArrayHasKey('unlimited', $validation);
            $this->assertEquals('Integer value found, but a string or a boolean is required', $validation['unlimited'][1]);

            $this->assertArrayHasKey('availableCount', $validation);
            $this->assertEquals('String value found, but a number is required', $validation['availableCount'][1]);

            $this->assertArrayHasKey('oneOf', $validation);
            $this->assertEquals('Failed to match exactly one schema', $validation['oneOf'][1]);


            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testUpdateProductsAllParameters()
    {
        $params =
            [
//            'scVoucherHash'     => ['{Put Service Call Voucher Hashes}'],
//            'scApiKey'           => '{Put service call Api Key}',
            'apiToken'                 => self::API_TOKEN_3,
            "data" =>
                [
                    [
                    ## ============================ *Required Parameters  =============================
                    "entityId"              => 29990,
                    "name"                  => "ویرایش محصول تست کیس",
                    "description"           => "ویرایش محصول",
                    "canComment"            => true,
                    "canLike"               => true,
                    "enable"                => false,
                    "price"                 => 40000,
                    "discount"              => 10,
                    "changePreview"         => true,
//            "availableCount"        => 200,  # تعداد موجود از محصول درصورت بدون محدودیت نبودن اجباری است
                    "unlimited"             => true, # بدون محدودیت بودن محصول true/false
                    ## =========================== Optional Parameters  ================================
                    "guildCode"             => 'CLOTHING_GUILD',
//            "version"               => 55,
                    "metaData"              => "{'name':'pro'}",
                    "allowUserInvoice"      => false,
                    "allowUserPrice"        => false,
                    "attTemplateCode"       => "پیراهن مردانه",
                    "attributes"            =>
                        [
                            [
                                "attCode"       => "gender",
                                "attValue"      => "مرد",
                                "attGroup"      => false,
                            ],
                            [
                                "attCode"       => "color",
                                "attValue"      => "سفید",
                                "attGroup"      => false,
                            ],
                            [
                                "attCode"       => "size",
                                "attValue"      => "XL",
                                "attGroup"      => true,
                            ],

                        ],
//            "groupId"               => 3684,
                    "lat"                   => 45.78756867,
                    "lng"                   => 76.989068567,
                    "tags"                  => ["شلوار", "شلوار لی"],
                    "content"               => "unitTest",
                    "previewImage"          => true,
                    "tagTrees"              => ["تگ سوم", "تگ دوم - اول"],
//            "tagTreeCategoryName"   => "ویژه",
                    'preferredTaxRate'      => 0.09,
                    'quantityPrecision'     => 2,
                ]
                ]
            ];
        try {
            $result = self::$productService->UpdateProducts($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testUpdateProductsRequiredParameters()
    {
        $params =
            [
            'apiToken'                 => self::API_TOKEN_3,
            "data" =>
                [
                    [
                        ## ============================ *Required Parameters  =============================
                        "entityId"              => 29990,
                        "name"                  => "ویرایش محصول تست کیس",
                        "description"           => "ویرایش محصول",
                        "canComment"            => true,
                        "canLike"               => true,
                        "enable"                => false,
                        "price"                 => 40000,
                        "discount"              => 10,
                        "changePreview"         => true,
//            "availableCount"        => 200,  # تعداد موجود از محصول درصورت بدون محدودیت نبودن اجباری است
                        "unlimited"             => true, # بدون محدودیت بودن محصول true/false
                    ]
                ]
            ];
        try {
            $result = self::$productService->UpdateProducts($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testUpdateProductsRequiredValidationError()
    {
        $paramsWithoutRequired1 = [];
        try {
            $result = self::$productService->updateProducts($paramsWithoutRequired1);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $this->assertArrayHasKey('data', $validation);
            $this->assertEquals('The property data is required', $validation['data'][0]);

        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }

        $paramsWithoutRequired2 = [
            'data' =>  [
                [
                    'content'               => 'Unit test product content',
                ]
            ]];
        try {
            self::$productService->updateProducts($paramsWithoutRequired2);
        }
        catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);
            $result = $e->getResult();
            $this->assertArrayHasKey('name', $validation);
            $this->assertEquals('The property name is required', $validation['name'][0]);

            $this->assertArrayHasKey('description', $validation);
            $this->assertEquals('The property description is required', $validation['description'][0]);

            $this->assertArrayHasKey('canComment', $validation);
            $this->assertEquals('The property canComment is required', $validation['canComment'][0]);

            $this->assertArrayHasKey('canLike', $validation);
            $this->assertEquals('The property canLike is required', $validation['canLike'][0]);

            $this->assertArrayHasKey('enable', $validation);
            $this->assertEquals('The property enable is required', $validation['enable'][0]);

            $this->assertArrayHasKey('entityId', $validation);
            $this->assertEquals('The property entityId is required', $validation['entityId'][0]);

            $this->assertArrayHasKey('price', $validation);
            $this->assertEquals('The property price is required', $validation['price'][0]);

            $this->assertArrayHasKey('discount', $validation);
            $this->assertEquals('The property discount is required', $validation['discount'][0]);

            $this->assertArrayHasKey('changePreview', $validation);
            $this->assertEquals('The property changePreview is required', $validation['changePreview'][0]);

            $this->assertArrayHasKey('unlimited', $validation);
            $this->assertEquals('The property unlimited is required', $validation['unlimited'][0]);

            $this->assertArrayHasKey('availableCount', $validation);
            $this->assertEquals('The property availableCount is required', $validation['availableCount'][0]);

            $this->assertArrayHasKey('oneOf', $validation);
            $this->assertEquals('Failed to match exactly one schema', $validation['oneOf'][0]);


            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }

        $wrongValueParams = [
            'scVoucherHash'     => ['{Put Service Call Voucher Hashes}'],
            'scApiKey'           => '{Put service call Api Key}',
            'apiToken'                 => self::API_TOKEN,
            'data' => [
                [
                    # ============== *Required Parameters  ==================
                    'entityId'              => '123',
                    'name'                  => 123,
                    'canComment'            => 123,
                    'canLike'               => 123,
                    'enable'                => 123,
                    'availableCount'        => '100',
                    'unlimited'             => 123,     # default : false
                    'price'                 => '123',
                    'discount'              => '100',
                    'description'           => 123,
                    "changePreview"         => 123,
                ]
            ]
        ];

        try {
            self::$productService->updateProducts($wrongValueParams);
        } catch (ValidationException $e) {
            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);
            $result = $e->getResult();
            $this->assertArrayHasKey('name', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['name'][1]);

            $this->assertArrayHasKey('description', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['description'][1]);

            $this->assertArrayHasKey('canComment', $validation);
            $this->assertEquals('Integer value found, but a string or a boolean is required', $validation['canComment'][1]);

            $this->assertArrayHasKey('canLike', $validation);
            $this->assertEquals('Integer value found, but a string or a boolean is required', $validation['canLike'][1]);

            $this->assertArrayHasKey('enable', $validation);
            $this->assertEquals('Integer value found, but a string or a boolean is required', $validation['enable'][1]);

            $this->assertArrayHasKey('entityId', $validation);
            $this->assertEquals('String value found, but an integer is required', $validation['entityId'][1]);

            $this->assertArrayHasKey('price', $validation);
            $this->assertEquals('String value found, but a number is required', $validation['price'][1]);

            $this->assertArrayHasKey('discount', $validation);
            $this->assertEquals('String value found, but a number is required', $validation['discount'][1]);

            $this->assertArrayHasKey('changePreview', $validation);
            $this->assertEquals('Integer value found, but a string or a boolean is required', $validation['changePreview'][1]);

            $this->assertArrayHasKey('unlimited', $validation);
            $this->assertEquals('Integer value found, but a string or a boolean is required', $validation['unlimited'][1]);

            $this->assertArrayHasKey('availableCount', $validation);
            $this->assertEquals('String value found, but a number is required', $validation['availableCount'][1]);

            $this->assertArrayHasKey('oneOf', $validation);
            $this->assertEquals('Failed to match exactly one schema', $validation['oneOf'][1]);

            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testGetProductListAllParameters()
    {
        $params = [
            ## ============================ *Required Parameters  =============================
            "size"                  => 50,
            "offset"                => 0,
            ## ============================= Optional Parameters  ==============================
//            "token"                 => "{Put AccessToken | ApiToken}", # for this service you can use AccessToken
//            "id"                    => [31653,31654],
            "businessId"            => 12121, # خطا میده
            "uniqueId"              => ["123"],
//            "categoryCode"          => [],
            "guildCode"             => ["CLOTHING_GUILD", "FOOD_GUILD"],
            "currencyCode"          => "",
//            "firstId"               => 24472, # خطا میده
//            "lastId"                => 31650,
            "attributeTemplateCode" => "",
            "attributes"            => [
                [
                    "attributeCode"   => "gender",
                    "attributeValue"  => "زن",
                ],
             ],
            "orderByLike"           => "asc", # خطا میده
            "orderByPrice"          => "asc", # خطا میده
            "tags"                  => ["tag1"],
//            "tagTrees"              => [],
            'scVoucherHash'     => ['{Put Service Call Voucher Hashes}'],
            'scApiKey'           => '{Put service call Api Key}',

        ];

        try {
            $result = self::$productService->getProductList($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testGetProductListRequiredParameters()
    {
        $params = [
            ## ============================ *Required Parameters  =============================
            "size"                  => 50,
            "offset"                => 0,
        ];

        try {
            $result = self::$productService->getProductList($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testGetProductListRequiredValidationError()
    {
        $params = [];
        try {
            $result = self::$productService->getProductList($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {

            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $this->assertArrayHasKey('offset', $validation);
            $this->assertEquals('The property offset is required', $validation['offset'][0]);

            $this->assertArrayHasKey('size', $validation);
            $this->assertEquals('The property size is required', $validation['size'][0]);

            $this->assertArrayHasKey('id', $validation);
            $this->assertEquals('The property id is required', $validation['id'][0]);

            $this->assertArrayHasKey('firstId', $validation);
            $this->assertEquals('The property firstId is required', $validation['firstId'][0]);

            $this->assertArrayHasKey('lastId', $validation);
            $this->assertEquals('The property lastId is required', $validation['lastId'][0]);

            $this->assertArrayHasKey('oneOf', $validation);
            $this->assertEquals('Failed to match exactly one schema', $validation['oneOf'][0]);

        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }

        $wrongParamsValue = [
            ## ============================ *Required Parameters  =============================
            "size"                  => '50',
            "offset"                => '0',
            ## ============================= Optional Parameters  ==============================
            "id"                    => [31653,31654],
            "businessId"            => '3612',
            "uniqueId"              => '12345',
//            "categoryCode"          => ['test'],
            "guildCode"             => "CLOTHING_GUILD",
            "currencyCode"          => 123,
            "firstId"               => '20000',
            "lastId"                => '31653',
            "attributeTemplateCode" => 123,
            "attributes"            =>
                [
                    "attributeCode"   => 123,
                    "attributeValue"  => 123,
                ],
            "orderBySale"           => true,
            "orderByLike"           => true,
            "orderByPrice"          => true,
            "tags"                  => "tag1",
//            "tagTrees"              => ['Tag tree level 01'],
            'scVoucherHash'     => '{Put Service Call Voucher Hashes}',
            'scApiKey'           => 123,
        ];
        try {
            $result = self::$productService->getProductList($wrongParamsValue);
        } catch (ValidationException $e) {
            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);
            $this->assertArrayHasKey('offset', $validation);
            $this->assertEquals('String value found, but an integer is required', $validation['offset'][1]);

            $this->assertArrayHasKey('size', $validation);
            $this->assertEquals('String value found, but an integer is required', $validation['size'][1]);

            $this->assertArrayHasKey('businessId', $validation);
            $this->assertEquals('String value found, but an integer is required', $validation['businessId'][0]);

            $this->assertArrayHasKey('uniqueId', $validation);
            $this->assertEquals('String value found, but an array is required', $validation['uniqueId'][0]);

            $this->assertArrayHasKey('guildCode', $validation);
            $this->assertEquals('String value found, but an array is required', $validation['guildCode'][0]);

            $this->assertArrayHasKey('currencyCode', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['currencyCode'][0]);

            $this->assertArrayHasKey('firstId', $validation);
            $this->assertEquals('String value found, but an integer is required', $validation['firstId'][1]);

            $this->assertArrayHasKey('lastId', $validation);
            $this->assertEquals('String value found, but an integer is required', $validation['lastId'][1]);

            $this->assertArrayHasKey('attributes.attributeCode', $validation);
            $this->assertEquals('Integer value found, but an array is required', $validation['attributes.attributeCode'][0]);

            $this->assertArrayHasKey('attributes.attributeValue', $validation);
            $this->assertEquals('Integer value found, but an array is required', $validation['attributes.attributeValue'][0]);

            $this->assertArrayHasKey('orderBySale', $validation);
            $this->assertEquals('Does not have a value in the enumeration ["asc","desc"]', $validation['orderBySale'][0]);

            $this->assertArrayHasKey('orderByLike', $validation);
            $this->assertEquals('Does not have a value in the enumeration ["asc","desc"]', $validation['orderByLike'][0]);

            $this->assertArrayHasKey('orderByPrice', $validation);
            $this->assertEquals('Does not have a value in the enumeration ["asc","desc"]', $validation['orderByPrice'][0]);

            $this->assertArrayHasKey('tags', $validation);
            $this->assertEquals('String value found, but an array is required', $validation['tags'][0]);

            $this->assertArrayHasKey('scVoucherHash', $validation);
            $this->assertEquals('String value found, but an array is required', $validation['scVoucherHash'][0]);

            $this->assertArrayHasKey('scApiKey', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['scApiKey'][0]);


            $result = $e->getResult();
            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testGetBusinessProductListAllParameters()
    {
        $params = [
            ## ============================ *Required Parameters  =============================
            "size"                  => 50,
            "offset"                => 0,
            ## ============================= Optional Parameters  ==============================
//            "id"                    => [31653,31654],
            "businessId"            => 3612,
            "uniqueId"              => ['1234', '12345'],
//            "categoryCode"          => ['test'],
            "guildCode"             => ["CLOTHING_GUILD", "FOOD_GUILD"],
            "currencyCode"          => "IRR",
//            "firstId"               => 20000,
//            "lastId"                => 31653,
            "attributeTemplateCode" => "مانتو",
        "attributes"            => [
            [
                "attributeCode"   => "gender",
                "attributeValue"  => "زن",
            ],
        ],
            "orderBySale"           => "asc",
            "orderByLike"           => "asc",
            "orderByPrice"          => "desc",
            "tags"                  => ["tag1"],
//            "tagTrees"              => ['Tag tree level 01'],
            "scope"                 => "DEALER_PRODUCT_PERMISSION",
            "attributeSearchQuery"  => '{"field":"test","is":"true"}',
            'scVoucherHash'     => ['{Put Service Call Voucher Hashes}'],
            'scApiKey'           => '{Put service call Api Key}',

        ];

        try {
            $result = self::$productService->getBusinessProductList($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testGetBusinessProductListRequiredParameters()
    {
        $params = [
            ## ============== *Required Parameters  ==================
            'offset' => 0,
            'size' => 10
        ];
        try {
            $result = self::$productService->getBusinessProductList($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testGetBusinessProductListRequiredParametersValidation()
    {
        $params = [];
        try {
            $result = self::$productService->getBusinessProductList($params);
        }
        catch (ValidationException $e) {
            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $this->assertArrayHasKey('offset', $validation);
            $this->assertEquals('The property offset is required', $validation['offset'][0]);

            $this->assertArrayHasKey('size', $validation);
            $this->assertEquals('The property size is required', $validation['size'][0]);

            $this->assertArrayHasKey('lastId', $validation);
            $this->assertEquals('The property lastId is required', $validation['lastId'][0]);

            $this->assertArrayHasKey('firstId', $validation);
            $this->assertEquals('The property firstId is required', $validation['firstId'][0]);

            $this->assertArrayHasKey('id', $validation);
            $this->assertEquals('The property id is required', $validation['id'][0]);

            $this->assertArrayHasKey('oneOf', $validation);
            $this->assertEquals('Failed to match exactly one schema', $validation['oneOf'][0]);

            $result = $e->getResult();
            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }

        $wrongParamsValue = [
            ## ============================ *Required Parameters  =============================
            "size"                  => '50',
            "offset"                => '0',
            ## ============================= Optional Parameters  ==============================
            "id"                    => [31653,31654],
            "businessId"            => '3612',
            "uniqueId"              => '12345',
//            "categoryCode"          => ['test'],
            "guildCode"             => "CLOTHING_GUILD",
            "currencyCode"          => 123,
            "firstId"               => '20000',
            "lastId"                => '31653',
            "attributeTemplateCode" => 123,
            "attributes"            =>
                [
                    "attributeCode"   => 123,
                    "attributeValue"  => 123,
                ],
            "orderBySale"           => true,
            "orderByLike"           => true,
            "orderByPrice"          => true,
            "tags"                  => "tag1",
//            "tagTrees"              => ['Tag tree level 01'],
            "scope"                 => "unknownScope",
            "attributeSearchQuery"  => 123,
            'scVoucherHash'     => '{Put Service Call Voucher Hashes}',
            'scApiKey'           => 123,
        ];
        try {
            $result = self::$productService->getBusinessProductList($wrongParamsValue);
        } catch (ValidationException $e) {
            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);
            $this->assertArrayHasKey('offset', $validation);
            $this->assertEquals('String value found, but an integer is required', $validation['offset'][1]);

            $this->assertArrayHasKey('size', $validation);
            $this->assertEquals('String value found, but an integer is required', $validation['size'][1]);

            $this->assertArrayHasKey('businessId', $validation);
            $this->assertEquals('String value found, but an integer is required', $validation['businessId'][0]);

            $this->assertArrayHasKey('uniqueId', $validation);
            $this->assertEquals('String value found, but an array is required', $validation['uniqueId'][0]);

            $this->assertArrayHasKey('guildCode', $validation);
            $this->assertEquals('String value found, but an array is required', $validation['guildCode'][0]);

            $this->assertArrayHasKey('currencyCode', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['currencyCode'][0]);

            $this->assertArrayHasKey('firstId', $validation);
            $this->assertEquals('String value found, but an integer is required', $validation['firstId'][1]);

            $this->assertArrayHasKey('lastId', $validation);
            $this->assertEquals('String value found, but an integer is required', $validation['lastId'][1]);

            $this->assertArrayHasKey('attributes.attributeCode', $validation);
            $this->assertEquals('Integer value found, but an array is required', $validation['attributes.attributeCode'][0]);

            $this->assertArrayHasKey('attributes.attributeValue', $validation);
            $this->assertEquals('Integer value found, but an array is required', $validation['attributes.attributeValue'][0]);

            $this->assertArrayHasKey('orderBySale', $validation);
            $this->assertEquals('Does not have a value in the enumeration ["asc","desc"]', $validation['orderBySale'][0]);

            $this->assertArrayHasKey('orderByLike', $validation);
            $this->assertEquals('Does not have a value in the enumeration ["asc","desc"]', $validation['orderByLike'][0]);

            $this->assertArrayHasKey('orderByPrice', $validation);
            $this->assertEquals('Does not have a value in the enumeration ["asc","desc"]', $validation['orderByPrice'][0]);

            $this->assertArrayHasKey('tags', $validation);
            $this->assertEquals('String value found, but an array is required', $validation['tags'][0]);

            $this->assertArrayHasKey('scope', $validation);
            $this->assertEquals('Does not have a value in the enumeration ["PARENT_PRODUCT","BUSINESS_PRODUCT","DEALER_PRODUCT_PERMISSION"]', $validation['scope'][0]);

            $this->assertArrayHasKey('attributeSearchQuery', $validation);
            $this->assertEquals('Integer value found, but an array or a string is required', $validation['attributeSearchQuery'][0]);

            $this->assertArrayHasKey('scVoucherHash', $validation);
            $this->assertEquals('String value found, but an array is required', $validation['scVoucherHash'][0]);

            $this->assertArrayHasKey('scApiKey', $validation);
            $this->assertEquals('Integer value found, but a string is required', $validation['scApiKey'][0]);


            $result = $e->getResult();
            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testGetAttributeTemplateListAllParameters()
    {
        $params = [
            ## ============================ *Required Parameters  =============================
            "size"                  => '50',
            "offset"                => '0',
            ## =========================== Optional Parameters  ================================
//            "token"                 => "{Put AccessToken | ApiToken}", # for this service you can use AccessToken
            "firstId"               => '40',
            "lastId"                => '100',  # id هارو نمایش نمیده که بر اساس اونها مرتب کنیم',
        ];

        try {
            $result = self::$productService->getAttributeTemplateList($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testGetAttributeTemplateListRequiredParameters()
    {
        $params = [
            ## ============================ *Required Parameters  =============================
            "size"                  => 50,
            "offset"                => 0,

        ];
        try {
            $result = self::$productService->getAttributeTemplateList($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testGetAttributeTemplateListRequiredParametersValidation()
    {
        $params = [];
        try {
            $result = self::$productService->getAttributeTemplateList($params);
        } catch (ValidationException $e) {
            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $this->assertArrayHasKey('offset', $validation);
            $this->assertEquals('The property offset is required', $validation['offset'][0]);

            $this->assertArrayHasKey('size', $validation);
            $this->assertEquals('The property size is required', $validation['size'][0]);

             $this->assertArrayHasKey('firstId', $validation);
            $this->assertEquals('The property firstId is required', $validation['firstId'][0]);

            $this->assertArrayHasKey('lastId', $validation);
            $this->assertEquals('The property lastId is required', $validation['lastId'][0]);

            $result = $e->getResult();
            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }

        $wrongValueParams =  [
            ## ============================ *Required Parameters  =============================
            "size"                  => '50',
            "offset"                => '0',
            ## =========================== Optional Parameters  ================================
//            "token"                 => "{Put AccessToken | ApiToken}", # for this service you can use AccessToken
            "firstId"               => '40',
            "lastId"                => '100',  # id هارو نمایش نمیده که بر اساس اونها مرتب کنیم',
        ];
        try {
            $result = self::$productService->getAttributeTemplateList($wrongValueParams);
        } catch (ValidationException $e) {
            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $this->assertArrayHasKey('offset', $validation);
            $this->assertEquals('String value found, but an integer is required', $validation['offset'][1]);

            $this->assertArrayHasKey('size', $validation);
            $this->assertEquals('String value found, but an integer is required', $validation['size'][1]);

            $this->assertArrayHasKey('firstId', $validation);
            $this->assertEquals('String value found, but an integer is required', $validation['firstId'][1]);

            $this->assertArrayHasKey('lastId', $validation);
            $this->assertEquals('String value found, but an integer is required', $validation['lastId'][1]);


            $result = $e->getResult();
            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testSearchSubProductAllParameters()
    {
        $params = [
            ## ============================ *Required Parameters  =============================
            "productGroupId"                    => [3684],
            ## ============================= Optional Parameters  ==============================
//            "token"                 => "{Put AccessToken | ApiToken}", # for this service you can use AccessToken
            "size"                  => 50,
            "offset"                => 0,
//            "query"                    => '"scProvider","nam"',
            "attributes"            => [
                [
                    "attributeCode"   => "size",
                    "attributeValue"  => "M",
                ],
            ],
//          "orderByAttributeCode"           => "asc",
          "orderByDirection"          => "asc",
          "tags"                  => ["tag1"],
          "tagTrees"              => ['test'],
            'scVoucherHash'     => ['{Put Service Call Voucher Hashes}'],
            'scApiKey'           => '{Put service call Api Key}',
        ];

        try {
            $result = self::$productService->searchSubProduct($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testSearchSubProductRequiredParameters()
    {
        $params = [
            ## ============================ *Required Parameters  =============================
            "productGroupId"                    => [3684],
        ];
        try {
            $result = self::$productService->searchSubProduct($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testSearchSubProductRequiredParametersValidation()
    {
        $params = [];
        try {
            $result = self::$productService->searchSubProduct($params);
        } catch (ValidationException $e) {
            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $this->assertArrayHasKey('productGroupId', $validation);
            $this->assertEquals('The property productGroupId is required', $validation['productGroupId'][0]);

            $result = $e->getResult();
            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testSearchProductAllParameters()
    {
        $params = [
            ## ============================ *Required Parameters  =============================
            "size"                  => 50,
            "offset"                => 0,
            ## ============================= Optional Parameters  ==============================
//            "token"                 => "{Put AccessToken | ApiToken}", # for this service you can use AccessToken
            "query"                    => '"scProvider","nam"',
////            "id"                    => [24741],
            "businessId"            => 3612, # خطا میده
//            "uniqueId"              => ["123"],
            "guildCode"             => ["CLOTHING_GUILD", "FOOD_GUILD"],
//            "currencyCode"          => "IRR",
////            "firstId"               => 24472, # خطا میده
////            "lastId"                => 31650,
            "attributeTemplateCode" => "مانتو",
            "attributes"            => [
                [
                    "attributeCode"   => "size",
                    "attributeValue"  => "XL",
                ],
            ],
            "orderByLike"           => "asc",
            "orderByPrice"          => "asc",
            "orderBySale"           => "asc",
            "tags"                  => ["tag1"],
            "tagTrees"              => ['test'],
            "tagTreeCategoryName"  => ['tagTreeCategoryName'],
            'scVoucherHash'     => ['{Put Service Call Voucher Hashes}'],
            'scApiKey'           => '{Put service call Api Key}',
        ];

        try {
            $result = self::$productService->searchProduct($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testSearchProductRequiredParameters()
    {
        $params = [
            ## ============================ *Required Parameters  =============================
            "size"                  => 50,
            "offset"                => 0,
        ];
        try {
            $result = self::$productService->searchProduct($params);
            $this->assertFalse($result['hasError']);
        } catch (ValidationException $e) {
            $this->fail('ValidationException: ' . $e->getErrorsAsString());
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

    public function testSearchProductRequiredParametersValidation()
    {
        $params = [];
        try {
            $result = self::$productService->getAttributeTemplateList($params);
        } catch (ValidationException $e) {
            $validation = $e->getErrorsAsArray();
            $this->assertNotEmpty($validation);

            $this->assertArrayHasKey('offset', $validation);
            $this->assertEquals('The property offset is required', $validation['offset'][0]);

            $this->assertArrayHasKey('size', $validation);
            $this->assertEquals('The property size is required', $validation['size'][0]);

            $result = $e->getResult();
            $this->assertEquals(887, $result['code']);
        } catch (PodException $e) {
            $error = $e->getResult();
            $this->fail('PodException: ' . $error['message']);
        }
    }

}