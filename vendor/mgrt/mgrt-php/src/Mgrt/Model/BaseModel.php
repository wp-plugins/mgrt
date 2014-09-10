<?php

namespace Mgrt\Model;

use Doctrine\Common\Inflector\Inflector;

class BaseModel
{
    /**
     * Catch all getter and setter
     *
     * @param  string $name
     * @param  array $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        // Match the getters
        if (substr($name, 0, 3) == 'get') {
            $parameter = Inflector::tableize(substr($name, 3));
            if (property_exists($this, $parameter)) {

                return $this->$parameter;
            } else {
                throw new \Exception(sprintf('The property "%s" does not exist.', $parameter));
            }
        }

        // Match the setters
        if (substr($name, 0, 3) == 'set') {
            $parameter = Inflector::tableize(substr($name, 3));
            $method = 'set'.Inflector::classify($parameter);
            if (method_exists($this, $method)) {

                return $this->$method($arguments[0]);
            } else if (property_exists($this, $parameter) && isset($arguments[0])) {
                $this->$parameter = $arguments[0];

                return $this;
            } else {
                throw new \Exception(sprintf('The property "%s" does not exist.', $parameter));
            }
        }
    }

    /**
     * Hydrate a collection from an array
     *
     * @param  array  $data
     *
     * @return Object
     */
    public function fromArray(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{'set'.Inflector::tableize($key)}($value);
            }
        }

        return $this;
    }
}
