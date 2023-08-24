<?php
namespace Akril\Compiler;

interface IncrementalCompiler
{
    /**
     * @param string $modifiedFile 
     * @return string[] 
     */
    public function compile($modifiedFile);
}
