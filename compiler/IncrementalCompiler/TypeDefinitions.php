<?php

namespace Akril\Compiler\IncrementalCompiler;

use Akril\Compiler\IncrementalCompiler;
use Akril\Compiler\TypeList;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager\ConfigWriter\Filesystem;
use Magento\Setup\Module\Di\Code\Reader\ClassReaderDecorator;
use Magento\Setup\Module\Di\Code\Reader\FileClassScanner;
use Magento\Setup\Module\Di\Compiler\Config\ModificationChain;
use Magento\Setup\Module\Di\Compiler\Config\Reader;
use Magento\Setup\Module\Di\Definition\Collection;

class TypeDefinitions implements IncrementalCompiler
{
    public function __construct(
        private ClassReaderDecorator $classReader,
        private DirectoryList $directoryList,
        private AreaList $areaList,
        private ModificationChain $modificationChain,
        private Reader $configReader,
        private Filesystem $configWriter,
    ) {
    }

    public function compile($modifiedFile)
    {
        $types = $this->resolveTypes($modifiedFile);
        $metadataDir = $this->directoryList->getPath('metadata');
        $typeMetadataFile = $metadataDir . DIRECTORY_SEPARATOR . 'types.php';
        $typeMetadata = file_exists($typeMetadataFile) ? include $typeMetadataFile : [];
        $typeList = new TypeList($typeMetadata);
        foreach ($types as $type) {
            $typeList = $this->updateTypeMetadata($type, $typeList);
        }

        $definitionsCollection = new Collection();
        $definitionsCollection->initialize($typeList->getArguments());
        $areaCodes = array_merge([Area::AREA_GLOBAL], $this->areaList->getCodes());
        foreach ($areaCodes as $areaCode) {
            $config = $this->configReader->generateCachePerScope($definitionsCollection, $areaCode);
            $config = $this->modificationChain->modify($config);

            // sort configuration to have it in the same order on every build
            ksort($config['arguments']);
            ksort($config['preferences']);
            ksort($config['instanceTypes']);

            $this->configWriter->write($areaCode, $config);
        }
        file_put_contents($typeMetadataFile, sprintf('<?php return %s;', $typeList));
    }

    private function updateTypeMetadata(string $type, TypeList $typeList)
    {
        $parentTypes = $this->classReader->getParents($type);
        $constructorParams = $this->classReader->getConstructor($type);
        $typeList->set($type, $parentTypes, $constructorParams);
        return $typeList;
    }

    private function resolveTypes($file)
    {
        $classScanner = new FileClassScanner($file);
        return [$classScanner->getClassName()];
    }
}
