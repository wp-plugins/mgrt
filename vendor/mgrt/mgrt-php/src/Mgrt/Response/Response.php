<?php

namespace Mgrt\Response;

use Doctrine\Common\Inflector\Inflector;
use Mgrt\Model\ResultCollection;
use Mgrt\Model\Result;

class Response
{
    /**
     * Get the result array and check if the root key is available
     *
     * @param  string $content The reponse body
     * @param  string $rootKey The root key
     *
     * @return array
     */
    public static function getResultForRootKey($content, $rootKey)
    {
        if (null === $content || !isset($content[$rootKey])) {
            throw new \RuntimeException('Unable to parse body content');
        }

        return $content[$rootKey];
    }

    /**
     * Check the response status code
     *
     * @param  Response $response The request response
     * @param  integer $expectedStatusCode The expected status code
     *
     * @return boolean
     */
    public static function checkStatusCode($response, $expectedStatusCode)
    {
        return $response->getStatusCode() === $expectedStatusCode;
    }

    /**
     * Get the routing key of an array ie : array('foo' => array('bar' =>'foobar'))
     * The rootKey will be foo
     *
     * @param array $data
     *
     * @return string the root key
     */
    public static function getRootKey(array $data)
    {
        $rootKey = array_keys($data);

        return array_shift($rootKey);
    }

    /**
     * Parse a response body into a collection
     *
     * @param  string $data
     *
     * @return ResultCollection
     */
    public static function parseResultCollection($data)
    {
        $responseBody = json_decode($data, true);
        $rootKey = self::getRootKey($responseBody);
        $data = self::getResultForRootKey($responseBody, $rootKey);
        $className = Inflector::singularize(Inflector::classify($rootKey));
        $resultCollection = new ResultCollection();

        return $resultCollection->fromArrayWithObjects($data, $className);
    }

    /**
     * Parse a response body into a collection
     *
     * @param  string $data
     *
     * @return ResultCollection
     */
    public static function parseResult($data)
    {
        $responseBody = json_decode($data, true);
        $rootKey = self::getRootKey($responseBody);
        $data = self::getResultForRootKey($responseBody, $rootKey);
        $className = Inflector::classify($rootKey);
        $result = new Result();

        return $result->fromArrayWithObject($data, $className);
    }
}
