<?php

namespace Mgrt\Model;

class Result
{
    /**
     * Hydrate an object from an array
     *
     * @param  array $data
     *
     * @return mixed
     */
    public function fromArrayWithObject(array $data, $objectName)
    {
        $className = "\\Mgrt\\Model\\".$objectName;
        $class = new $className;

        return $class->fromArray($data);
    }
}
