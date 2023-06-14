# Config pre-compilation for Magento 2

## Installation

1. Install the components as your dependencies (as 'path'-type repository)
2. Add component DI configuration to the application `app/etc/di.xml` 
```xml
    <preference for="Magento\Framework\Event\ConfigInterface" type="Akril\Event\Config\Compiled" />
    <type name="Akril\Config\Compiler">
        <arguments>
            <argument name="readers" xsi:type="array">
                <item name="events" xsi:type="string">Akril\Event\Config\Reader</item>
            </argument>
        </arguments>
    </type>
    <virtualType name="eventConfigData" type="Akril\Config\Data\Compiled">
        <arguments>
            <argument name="type" xsi:type="string">events</argument>
        </arguments>
    </virtualType>
    <type name="Akril\Event\Config\Compiled">
        <arguments>
            <argument name="data" xsi:type="object">eventConfigData</argument>
        </arguments>
    </type>
```

## Usage

With the components installed Magento will start reading event configuration from `var/config/events/*.php` instead of generating it and storing in cache.
These files contain event configuration merged from all installed modules. You can use them to troubleshoot magento event configuration.

To generate the compiled event configuration files run `bin/magento config:compile`
