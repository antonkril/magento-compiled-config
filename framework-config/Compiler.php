<?php
namespace Akril\Config;

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
        if (!is_a($readerClass, \Magento\Framework\Config\ReaderInterface::class, true)) {
            throw new LogicException(sprintf('Wrong configuration reader registered for type "%s"', $type));
        }
        $reader = $this->objectManager->get($readerClass);
        $metadataDir = $this->directoryList->getPath('metadata');
        foreach ($this->scopes as $scope) {
            $scopeConfig = $reader->read($scope);
            $pathParts = [ $metadataDir, 'config', $type, $scope . '.php' ];
            $configPath = join(DIRECTORY_SEPARATOR, $pathParts);
            if (!file_exists(dirname($configPath))) {
                mkdir(dirname($configPath), 0744, true);
            }
            file_put_contents($configPath, sprintf('<?php return %s;', var_export($scopeConfig, true)));
        }
    }
}
