<?php
/**
 * User: zach
 * Date: 5/31/13
 * Time: 9:49 AM
 */

namespace Elasticsearch\Endpoints;


use Elasticsearch\Common\Exceptions\UnexpectedValueException;
use Elasticsearch\Transport;

/**
 * Class AbstractEndpoint
 * @package Elasticsearch\Endpoints
 */
abstract class AbstractEndpoint
{
    /** @var array  */
    protected $params = array();

    /** @var  string */
    protected $index = null;

    /** @var  string */
    protected $type = null;

    /** @var  string|int */
    protected $id = null;

    /** @var  string */
    protected $method = null;

    /** @var  array */
    protected $body = null;

    /** @var \Elasticsearch\Transport  */
    private $transport = null;


    /**
     * @return string[]
     */
    abstract protected function getParamWhitelist();


    /**
     * @return string
     */
    abstract protected function getURI();


    /**
     * @return string
     */
    abstract protected function getMethod();

    /**
     * @param Transport $transport
     */
    public function __construct($transport)
    {
        $this->transport = $transport;
    }


    /**
     *
     * @return array
     */
    public function performRequest()
    {
        return $this->transport->performRequest(
            $this->getMethod(),
            $this->getURI(),
            $this->params,
            $this->getBody()
        );
    }

    /**
     * Set the parameters for this endpoint
     *
     * @param string[] $params Array of parameters
     * @return $this
     */
    public function setParams($params)
    {
        $this->checkUserParams($params);
        $this->params = $params;
        return $this;
    }


    /**
     * @param string $index
     *
     * @return $this
     */
    public function setIndex($index)
    {
        if ($index === null) {
            return $this;
        }

        $this->index = urlencode($index);
        return $this;
    }


    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        if ($type === null) {
            return $this;
        }

        $this->type = urlencode($type);
        return $this;
    }


    /**
     * @param int|string $docID
     *
     * @return $this
     */
    public function setID($docID)
    {
        if ($docID === null) {
            return $this;
        }

        $this->id = urlencode($docID);
        return $this;
    }


    /**
     * @return array
     */
    protected function getBody()
    {
        return $this->body;
    }

    /**
     * @param array $params
     *
     * @throws \Elasticsearch\Common\Exceptions\UnexpectedValueException
     */
    private function checkUserParams($params)
    {
        try {
            $this->ifParamsInvalidThrowException($params);
        } catch (UnexpectedValueException $exception) {
            throw $exception;
        }
    }

    /**
     * Check if param is in whitelist
     *
     * @param array $params    Assoc array of parameters
     *
     * @throws \Elasticsearch\Common\Exceptions\UnexpectedValueException
     *
     */
    private function ifParamsInvalidThrowException($params)
    {
        if (isset($params) !== true) {
            return; //no params, just return.
        }

        $whitelist = $this->getParamWhitelist();

        foreach ($params as $key => $value) {
            if (array_search($key, $whitelist) === false) {
                throw new UnexpectedValueException($key . ' is not a valid parameter');
            }
        }

    }

}