<?php
if (PHP_SAPI == 'cli') {
    \Magento\Framework\Console\CommandLocator::register(\Akril\Compiler\CommandList::class);
}
