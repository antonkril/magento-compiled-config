<?php

namespace Akril\Compiler;

use Magento\Setup\Module\Di\Compiler\ConstructorArgument;

class TypeList
{
    private $typeData = [];

    public function __construct($typeData = [])
    {
        $this->typeData = $typeData;
    }

    public function argumetnsChanged($type, $arguments)
    {
        return true;
    }

    public function set($type, array $parentTypes = [], array $constructorParams = [])
    {
        $newParent = array_shift($parentTypes);
        $newInterfaces = $parentTypes;
        $newData = [
            $newParent,
            $newInterfaces,
            $constructorParams,
            $this->typeData[$type][3] ?? []
        ];

        $this->typeData[$type] =  $newData;
    }

    public function addChild($type, $subtype)
    {
        $currentChildren = $this->typeData[$type][3] ?? [];
        if (!in_array($subtype, $currentChildren)) {
            array_push($currentChildren, $subtype);
            $this->typeData[$type][3] = $currentChildren;
        }
    }

    public function getArguments()
    {
        return  array_map(
            function ($item) {
                return $item[2];
            },
            $this->typeData
        );
    }

    public function __toString()
    {
        $data = array_map(function ($item) {
            $item[2] = array_map(
                function (ConstructorArgument $param) {
                    return [
                        $param->getName(),
                        $param->getType(),
                        $param->isRequired(),
                        $param->getDefaultValue(),
                    ];
                },
                $item[2]
            );
            return $item;
        }, $this->typeData);
        $export = var_export($this->typeData, TRUE);
        $patterns = [
            "/array \(/" => '[',
            "/^([ ]*)\)(,?)$/m" => '$1]$2',
            "/=>[ ]?\n[ ]+\[/" => '=> [',
            "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
        ];
        $export = preg_replace(array_keys($patterns), array_values($patterns), $export);
        return $export;
    }
}
