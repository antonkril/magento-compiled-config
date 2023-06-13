<?php
namespace Magento\Framework\Config;

use InvalidArgumentException;
use LogicException;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\ObjectManagerInterface;

class Compiler
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    private $readers = []; 

    private $scopes = ['primary', 'frontend', 'adminhtml', 'web_api'];
    
    public function __construct(
        ObjectManagerInterface $objectManager,
        DirectoryList $directoryList,
        $readers = []
    ) {
        $this->objectManager = $objectManager;
        $this->directoryList = $directoryList;
        $this->readers = $readers;
    }

    public function compile($type, $changedFile = null)
    {
        if (!isset($this->readers[$type])) {
            throw new InvalidArgumentException(sprintf('No reader configuration for config type "%s"', $type));
        }
        $readerClass = $this->readers[$type];
        if (!is_a($readerClass, ReaderInterface::class, true)) {
            throw new LogicException(sprintf('Wrong configuration reader registered for type "%s"', $type));
        }
        $reader = $this->objectManager->get($readerClass);
        foreach ($this->scopes as $scope) {
            $scopeConfig = $reader->read($scope);
            $pathParts = [
                $this->directoryList->getPath('var'),
                'config',
                $type,
                $scope . '.php'
            ];
            $configPath = join(DIRECTORY_SEPARATOR, $pathParts);
            file_put_contents($configPath, var_export($scopeConfig, true));
        }
    }
}
