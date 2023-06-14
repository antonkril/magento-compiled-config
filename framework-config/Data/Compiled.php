<?php
namespace Akril\Config\Data;

use Magento\Framework\Config\DataInterface;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\Filesystem\DirectoryList;

class Compiled implements DataInterface
{
    /**
     * Configuration scope resolver
     *
     * @var \Magento\Framework\Config\ScopeInterface
     */
    private $configScope;

    /**
     * @var string
     */
    private $currentScope;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var string
     */
    private $configType;

    public function __construct(ScopeInterface $configScope, DirectoryList $directoryList, $type)
    {
        $this->configScope = $configScope;
        $this->directoryList = $directoryList;
        $this->configType = $type;
    }
    
    public function merge(array $config)
    {
        $this->data = array_merge_recursive($this->data, $config);
    }

    public function get($key, $default = null)
    {
        if ($this->currentScope !== $this->configScope->getCurrentScope()) {
            $this->currentScope = $this->configScope->getCurrentScope();
            $pathParts = [
                $this->directoryList->getPath('var'),
                'config',
                $this->configType,
                $this->currentScope . '.php'
            ];
            $configPath = join(DIRECTORY_SEPARATOR, $pathParts);
            $this->data = include($configPath);
        }
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }
}
