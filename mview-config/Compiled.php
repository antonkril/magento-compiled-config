<?php
namespace Akril\Event\Config;

use \Magento\Framework\Config\DataInterface;

class Compiled implements \Magento\Framework\Event\ConfigInterface
{
    /**
     * @var DataInterface
     */
    private $data;
    
    public function __construct(DataInterface $data)
    {
        $this->data = $data;
    }
    
    /**
     * Get observers by event name
     *
     * @param string $eventName
     * @return null|array|mixed
     */
    public function getObservers($eventName)
    {
        return $this->data->get($eventName, []);
    }
}
