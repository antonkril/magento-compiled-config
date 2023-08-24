<?php
namespace Akril\Compiler\IncrementalCompiler;

use Akril\Compiler\IncrementalCompiler;
use Magento\Framework\ObjectManagerInterface;

class Composite implements IncrementalCompiler
{
    private ObjectManagerInterface $objectManager;

    private $filePatterns = [
        '@^/app/etc/di.xml@' => [\Akril\Config\Compiler::class],
        '@^/vendor/.*\.php@' => [
            \Akril\Compiler\IncrementalCompiler\TypeDefinitions::class,
            \Akril\Compiler\IncrementalCompiler\ConstructorArguments::class
        ],
        '@^/vendor/.*/.*/etc(/.*)?/(.*)\.xml@' => [\Akril\Config\Compiler::class]
    ];

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    private function getProcessorsByFile($fileName)
    {
        $result = [];
        foreach ($this->filePatterns as $pattern => $procesors) {
            if (preg_match($pattern, $fileName, $matches)) {
                $result = array_merge($result, $procesors);
            }
        }
        return $result;
    }

    public function compile($modifiedFile)
    {
        if (substr($modifiedFile, 0, strlen(BP)) !== BP) {
            return ['The file is not in Magento root directory'];
        }
        $strippedFileName = substr($modifiedFile, strlen(BP));
        $processorsToRun = $this->getProcessorsByFile($strippedFileName);
        $messages = [];
        foreach ($processorsToRun as $compilerClass) {
            /** @var \Akril\Compiler\IncCompiler $compilerClass */
            $processor = $this->objectManager->get($compilerClass);
            $messages += $processor->compile($modifiedFile) ?? [];
        }
        return $messages;
    }
}
