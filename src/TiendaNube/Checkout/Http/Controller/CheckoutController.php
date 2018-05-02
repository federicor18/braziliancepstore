<?php

declare(strict_types=1);

namespace TiendaNube\Checkout\Http\Controller;

use Psr\Http\Message\ResponseInterface;
use TiendaNube\Checkout\Service\Shipping\AddressService;
use Psr\Log\LoggerInterface;
use TiendaNube\Checkout\Model\Store;

class CheckoutController extends AbstractController
{
    /**
     * Returns the address to be auto-fill the checkout form
     *
     * Expected JSON:
     * {
     *     "address": "Avenida da França",
     *     "neighborhood": "Comércio",
     *     "city": "Salvador",
     *     "state": "BA"
     * }
     *
     * @Route /address/{zipcode}
     *
     * @param string $zipcode
     * @param AddressService $addressService
     * @return ResponseInterface
     */
    public function getAddressAction(Store  $store, string $zipcode, \PDO $pdo, LoggerInterface $logger):ResponseInterface {
        try {
            if ($store->betaTester) {
                // filtering and sanitizing input
                $rawZipcode = preg_replace("/[^\d]/","",$zipcode);

                $addressService = new AddressService($pdo, $logger);

                // getting address by zipcode
                $address = $addressService->getAddressByZip($rawZipcode);

                // if the zip code is invalid will return the exception, if valid always will have an array inside it 
                return $this->json($address);
            } else {
                return $this->json(['error' => 'This service is only available for beta testing stores'],500);
            }
        } catch (\Exception $e) {
            $logger->error(
                'An error occurred with code: ' . $e->getCode() . 'and with description of error: '.
                $e->getMessage()
            );
            return $this->json(['error'=>'The requested zipcode was not found.'],404);
        }
    }
}
