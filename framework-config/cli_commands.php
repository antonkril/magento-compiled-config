<?php
if (PHP_SAPI == 'cli') {
    \Magento\Framework\Console\CommandLocator::register(\Akril\Config\CommandList::class);
}
