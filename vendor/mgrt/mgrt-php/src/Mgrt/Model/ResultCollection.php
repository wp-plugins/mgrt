<?php

namespace Mgrt\Model;

use Mgrt\Model\Template;
use Mgrt\Model\Sender;
use Mgrt\Model\Contact;

class ResultCollection implements \Iterator, \Countable
{
    /**
     * @var integer
     */
    protected $page = null;

    /**
     * @var integer
     */
    protected $limit = null;

    /**
     * @var integer
     */
    protected $total = null;

    /**
     * @var integer
     */
    private $position = 0;

    /**
     * @var array Store object in the collection
     */
    private $collection = array();

    /**
     * @var array
     */
    private $metadata = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->position = 0;
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return $this->collection[$this->position];
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * {@inheritDoc}
     */
    public function valid()
    {
        return isset($this->collection[$this->position]);
    }

    /**
     * Add an object to the end of the collection
     */
    public function add($value)
    {
        $this->collection[count($this->collection)] = $value;
    }

    /**
     * Set an object to the given offset of the collection
     */
    public function set($key, $value)
    {
        $this->collection[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->collection);
    }

    /**
     * Get the limit
     *
     * @return integer
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Get the total
     *
     * @return integer
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Get the page number
     *
     * @return integer
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Get the collection
     *
     * @return array
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Hydrate a collection from an array
     *
     * @param  array  $data
     *
     * @return ResultCollection
     */
    public function fromArrayWithObjects(array $data, $objectName)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        $className = "\\Mgrt\\Model\\".$objectName;
        foreach ($data['results'] as $result) {
            $class = new $className();
            $object = $class->fromArray($result);
            $this->add($object);
        }

        return $this;
    }
}
