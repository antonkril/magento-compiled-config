# Config pre-compilation for Magento 2

## Installation

1. Install the components as your dependencies (as 'path'-type repository)
2. Add component DI configuration to the application `app/etc/di.xml` 
```xml
    <preference for="Magento\Framework\Event\ConfigInterface" type="Magento\Framework\Event\Config\Compiled" />
    <type name="Magento\Framework\Config\Compiler">
        <arguments>
            <argument name="readers" xsi:type="array">
                <item name="events" xsi:type="string">Magento\Framework\Event\Config\Reader</item>
            </argument>
        </arguments>
    </type>
    <virtualType name="eventConfigData" type="Magento\Framework\Config\Data\Compiled">
        <arguments>
            <argument name="type" xsi:type="string">events</argument>
        </arguments>
    </virtualType>
    <type name="Magento\Framework\Event\Config\Compiled">
        <arguments>
            <argument name="data" xsi:type="object">eventConfigData</argument>
        </arguments>
    </type>
```

