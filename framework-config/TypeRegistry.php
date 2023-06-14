<?php
namespace Akril\Config;

use LogicException;

class TypeRegistry 
{
    private $types = [];

    public function __construct($types = [])
    {
        $this->types = $types;
    }

    public function getTypeByFileName($fileName) : string
    {
        if (!isset($this->types[$fileName])) {
            throw new LogicException(
                sprintf('No config type registered for file "%s" in config compiler', $fileName)
            );
        }
        return $this->types[$fileName];
    }
}

