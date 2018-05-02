<?php

namespace TiendaNube\Checkout\Service\Shipping;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use TiendaNube\Checkout\Service\Shipping\AddressService;

class AddressServiceTest extends TestCase
{
    public function testGetExistentAddressByZipcode()
    {
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

        // mocking statement
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('rowCount')->willReturn(1);
        $stmt->method('fetch')->willReturn($address);

        // mocking pdo
        $pdo = $this->createMock(\PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        // mocking logger
        $logger = $this->createMock(LoggerInterface::class);

        // creating service
        $service = new AddressService($pdo,$logger);

        // testing
        $result = $service->getAddressByZip('40010000');
    
        // asserts
        $this->assertEquals($address,$result);
    }

    public function testGetNonexistentAddressByZipcode()
    {
        // mocking statement
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('rowCount')->willReturn(0);

        // mocking pdo
        $pdo = $this->createMock(\PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        // mocking logger
        $logger = $this->createMock(LoggerInterface::class);

        // creating service
        $service = new AddressService($pdo,$logger);

        // testing
        $result = $service->getAddressByZip('40010001');

        # Update assertion because method returns an empty array when not found the address by zipcode
        $this->assertEmpty($result);
    }

    public function testGetAddressByZipcodeWithPdoException()
    {
        // mocking pdo
        $pdo = $this->createMock(\PDO::class);
        $pdo->method('prepare')->willThrowException(new \PDOException('An error occurred'));

        // mocking logger
        $logger = $this->createMock(LoggerInterface::class);

        // creating service
        $service = new AddressService($pdo,$logger);

        // testing
        $result = $service->getAddressByZip('40010002');

        // asserts
        $this->assertNull($result);
    }

}
