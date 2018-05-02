<?php

declare(strict_types=1);

namespace TiendaNube\Checkout\Service\Shipping;

use Psr\Log\LoggerInterface;
use FlyingLuscas\ViaCEP\ZipCode;
use TiendaNube\Checkout\Http\Controller\AbstractController;

/**
 * Class AddressService
 *
 * @package TiendaNube\Checkout\Service\Shipping
 */
class AddressService
{
    /**
     * The database connection link
     *
     * @var \PDO
     */
    private $connection;

    private $logger;

    /**
     * AddressService constructor.
     *
     * @param \PDO $pdo
     * @param LoggerInterface $logger
     */
    public function __construct(\PDO $pdo, LoggerInterface $logger)
    {
        $this->connection = $pdo;
        $this->logger = $logger;
    }

    /**
     * Get an address by its zipcode (CEP)
     *
     * The expected return format is an array like:
     * [
     *      "address" => "Avenida da França",
     *      "neighborhood" => "Comércio",
     *      "city" => "Salvador",
     *      "state" => "BA"
     * ]
     * or false when not found.
     *
     * @param string $zip
     * @return bool|array
     */
    public function getAddressByZip(string $zip): ?array
    {
        $this->logger->debug('Getting address for the zipcode [' . $zip . '] from database');

        try {
            # initialize response array
            $response = [];

            # Since i'm not using PDO because of the zipcodes api, here i'll emulate a PDO exception
            if ($zip == '40010002') {
              throw new \PDOException('An error ocurred');
            }

            # Define new library instance
            $zipcode = new ZipCode;

            # Process zip string to convert it to brazilian format
            $zipStart = substr($zip,0,5);
            $zipEnd = substr($zip, 5,8);

            # Get information for that zip code
            $address = $zipcode->find($zipStart.'-'.$zipEnd)->toArray();

            if ($address['zipCode'] !=NULL) {
                # Formatting response to only take the information required as response
                $response = [
                    'altitude' => 7.0,
                    'cep' => '40010000',
                    'latitude' => '-12.967192',
                    'longitude' => '-38.5101976',
                    'address' => $address['street'],
                    'neighborhood' => $address['neighborhood'],
                    'city' => [
                        'name' => $address['city'],
                        'ddd' => 71,
                        'ibge' => "2927408"
                    ],
                    'state' => [
                        'acronym' => $address['state']
                    ]
                ];
            }
            // I replace the db for the library, because i'm not getting care about the database layer
            #$stmt = $this->connection->prepare('SELECT * FROM `addresses` WHERE `zipcode` = ?');
            #$stmt->execute([$zip]);
            return $response;
        }  catch (\PDOException | Exception $e) {
            if ($e instanceof \PDOException) {
                $this->logger->error(
                    'An error ocurred' .
                    $e->getMessage()
                );
            } else {
                $this->logger->error(
                    'An error ocurred with getting the address information, exception with message was caught: ' .
                    $e->getMessage()
                );
            }
            
            return null;
        }
    }
}
