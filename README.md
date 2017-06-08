# Magento 2 Message Queue MySQL Backend
MySQL message queue backend implementation for [Rcason_Mq](https://github.com/renatocason/magento2-module-mq).

## Installation
1. Require the module via Composer
```bash
$ composer require renatocason/magento2-module-mq-mysql
```

2. Enable the module
```bash
$ bin/magento module:enable Rcason_MqMysql
$ bin/magento setup:upgrade
```

## Configuration
1. Configure the Mq module as explained [here](https://github.com/renatocason/magento2-module-mq)
2. Specify _mysql_ as broker when configuring a queue in your module's _etc/ce_mq.xml_ file
```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Rcason_Mq:etc/ce_mq.xsd">
    <ceQueue name="product.updates" broker="mysql"
        messageSchema="int"
        consumerInterface="Rcason\MqExample\Model\ExampleConsumer"/>
</config>
```

## Authors, contributors and maintainers

Author:
- [Renato Cason](https://github.com/renatocason)

## License
Licensed under the Open Software License version 3.0
