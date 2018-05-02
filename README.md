# PHP Integration Engineer Test


## Business requirements

1 - Software design is improved, tests created for new use cases when store is beta or not beta. To not take care about db layer , a library is being used to get CEP Information, and then response is formatted and different use cases (200,404,500) are take in count.
-> 200 -> Success , zipcode found and info retrived
-> 404 -> ZipCode not found on DB
-> 500 -> Trying to use a store that's not part of the beta testing program.

2 - This service is new and never went to production before, so to minimize the risks, we want to release it only for the stores that are a part of the beta testing program, and all the remaining stores should use the old implementation. Check out the **Store model** to figure out how to determine if a store matches the condition.

-> Store model has been mocked and properties set in different scenarios to check functionality of the api endpoint.

3 - The service is under development and will be released on next week, and as we need to move fast with the team projects, we cannot wait for a staging server, so, using the specification of the service API, you need to mock-up the expected behavior in your tests to make sure that it will work when we deploy it.

-> Tests has been changed, so please take a look at them because them shows new scenarios take in count, and different validations according business requirements

4 - Finally, an error (of any type) should never be shown to the users, so, error treatment is required in a end-to-end way.

-> Exceptions of different types are thrown when things are not 200 OK.

#### Available Address 

## The API

The Address API has only one endpoint and it was designed to provide resilience and scalability in some parts of our platform.

The api is available to be tested with real zip codes. Endpoint is available under /address/{zipcode}

Response has this format when zip code is found:

For this challenge application only city , neighborhood, state and address contains real data. The rest are mocked.

```
{
    "altitude":7.0,
    "cep":"40010000",
    "latitude":"-12.967192",
    "longitude":"-38.5101976",
    "address":"Avenida da França",
    "neighborhood":"Comércio",
    "city":{  
        "ddd":71,
        "ibge":"2927408",
        "name":"Salvador"
    },
    "state":{  
        "acronym":"BA"
    }
}
```

When an error is triggered the endpoint returns 404 or 500 error depending on the issue.

## Setup

This project has some dependencies and uses composer to manage this dependencies, and you can install it using:

```
$ cd /path/to/the/project
$ composer install
```

## Running the tests

To run the tests, you can use the following command line:

```
$ ./vendor/bin/phpunit
```
