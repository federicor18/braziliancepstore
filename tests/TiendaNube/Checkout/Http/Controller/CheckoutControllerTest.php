<?php

declare(strict_types=1);

namespace TiendaNube\Checkout\Http\Controller;

use TiendaNube\Checkout\Model\Store;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use TiendaNube\Checkout\Http\Request\RequestStackInterface;
use TiendaNube\Checkout\Http\Response\ResponseBuilderInterface;
use TiendaNube\Checkout\Service\Shipping\AddressService;
use Psr\Log\LoggerInterface;

class CheckoutControllerTest extends TestCase
{

    public function testGetAddressValidInBetaStore()
    {
        // getting controller instance
        $controller = $this->getControllerInstance();

        // expected address
        $address = [
            'altitude' => 7.0,
            'cep' => '40010000',
            'latitude' => '-12.967192',
            'longitude' => '-38.5101976',
            'address' => 'Avenida da França',
            'neighborhood' => 'Comércio',
            'city' => [
                        'name' => 'Salvador',
                        'ddd' => 71,
                        'ibge' => "2927408"
                      ],
            'state' => [
                        'acronym' => 'BA'
                       ]
        ];

        // mocking pdo
        $pdo = $this->createMock(\PDO::class);

        // mocking logger
        $logger = $this->createMock(LoggerInterface::class);

        // create store mock
        $store = $this->createMock(Store::class);
        
        // define the store as betatesting one
        $store->name = 'Federicos Store';
        $store->email = 'federicor@tiendanube.com';
        $store->betaTester = true;
       
        // test method
        $result = $controller->getAddressAction($store,'40010000',$pdo,$logger);

        // asserts
        $this->assertEquals(200,$result->getStatusCode());
    }

    public function testGetAddressValidInNotBetaStore()
    {
        // getting controller instance
        $controller = $this->getControllerInstance();

        // expected address
        $address = [
            'altitude' => 7.0,
            'cep' => '40010000',
            'latitude' => '-12.967192',
            'longitude' => '-38.5101976',
            'address' => 'Avenida da França',
            'neighborhood' => 'Comércio',
            'city' => [
                        'name' => 'Salvador',
                        'ddd' => 71,
                        'ibge' => "2927408"
                      ],
            'state' => [
                        'acronym' => 'BA'
                       ]
        ];

        // mocking pdo
        $pdo = $this->createMock(\PDO::class);

        // mocking logger
        $logger = $this->createMock(LoggerInterface::class);

        // create store mock
        $store = $this->createMock(Store::class);
        
        // define the store as betatesting one
        $store->name = 'Federicos Store';
        $store->email = 'federicor@tiendanube.com';
        $store->betaTester = false;
       
        // test method
        $result = $controller->getAddressAction($store,'40010000',$pdo,$logger);
        
        // asserts
        $this->assertEquals(500,$result->getStatusCode());
    }

    public function testGetAddressInvalid()
    {
        // getting controller instance
        $controller = $this->getControllerInstance();

        // mocking pdo
        $pdo = $this->createMock(\PDO::class);

        // mocking logger
        $logger = $this->createMock(LoggerInterface::class);
        
        // create store mock
        $store = $this->createMock(Store::class);
        
        // define the store as betatesting one
        $store->name = 'Federicos Store';
        $store->email = 'federicor@tiendanube.com';
        $store->betaTester = true;

        // test method
        $result = $controller->getAddressAction($store, '400100001',$pdo,$logger);
    
        // assert
        $this->assertEquals(404,$result->getStatusCode());
    }

    /**
     * Get a RequestStack mock object
     *
     * @param ServerRequestInterface|null $expectedRequest
     * @return MockObject
     */
    private function getRequestStackInstance(?ServerRequestInterface $expectedRequest = null)
    {
        $requestStack = $this->createMock(RequestStackInterface::class);
        $expectedRequest = $expectedRequest ?: $this->createMock(ServerRequestInterface::class);
        $requestStack->method('getCurrentRequest')->willReturn($expectedRequest);

        return $requestStack;
    }

    /**
     * Get a ResponseBuilder mock object
     *
     * @param ResponseInterface|callable|null $expectedResponse
     * @return MockObject
     */
    private function getResponseBuilderInstance($expectedResponse = null)
    {
        $responseBuilder = $this->createMock(ResponseBuilderInterface::class);

        if (is_null($expectedResponse)) {
            $expectedResponse = function ($body, $status, $headers) {
                $stream = $this->createMock(StreamInterface::class);
                $stream->method('getContents')->willReturn($body);

                $response = $this->createMock(ResponseInterface::class);
                $response->method('getBody')->willReturn($stream);
                $response->method('getStatusCode')->willReturn($status);
                $response->method('getHeaders')->willReturn($headers);

                return $response;
            };
        }

        if ($expectedResponse instanceof ResponseInterface) {
            $responseBuilder->method('buildResponse')->willReturn($expectedResponse);
        } else if (is_callable($expectedResponse)) {
            $responseBuilder->method('buildResponse')->willReturnCallback($expectedResponse);
        } else {
            throw new Exception(
                'The expectedResponse argument should be an instance (or mock) of ResponseInterface or callable.'
            );
        }

        return $responseBuilder;
    }

    /**
     * Get an instance of the controller
     *
     * @param null|RequestStackInterface $requestStack
     * @param null|ResponseBuilderInterface $responseBuilder
     * @return CheckoutController
     */
    private function getControllerInstance(
        ?RequestStackInterface $requestStack = null,
        ?ResponseBuilderInterface $responseBuilder = null
    ) {
        // mocking units
        $container = $this->createMock(ContainerInterface::class);
        $requestStack = $requestStack ?: $this->getRequestStackInstance();
        $responseBuilder = $responseBuilder ?: $this->getResponseBuilderInstance();

        return new CheckoutController($container,$requestStack,$responseBuilder);
    }
}
