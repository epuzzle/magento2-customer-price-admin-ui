# Admin UI for [Customer Prices](https://github.com/epuzzle/magento2-customer-price) module

# Builds Status
[![Magento 2.4.3 Actions Status](https://github.com/epuzzle/magento2-customer-price-admin-ui/workflows/magento243/badge.svg)](https://github.com/epuzzle/magento2-customer-price-admin-ui/actions)
[![Magento 2.4.4 Actions Status](https://github.com/epuzzle/magento2-customer-price-admin-ui/workflows/magento244/badge.svg)](https://github.com/epuzzle/magento2-customer-price-admin-ui/actions)

# Supported Magento 2 versions
| **Version** | **Status** | **Note**                                                         |
|-------------|------------|------------------------------------------------------------------|
| **2.3.***   | ?          | Deprecated module [here](https://github.com/jeysmook/magento2-customer-prices) |
| **2.4.3**   | &check;    |                                                                  |
| **2.4.4**   | &check;    |                                                                  |

# Installation
1. Open Magento 2 project and go to the root directory.
2. `composer require epuzzle/magento2-customer-price-admin-ui`
3. Run needed commands: `php bin/magento setup:upgrade && php bin/magento setup:di:compile` if you need please run these commands also: `php bin/magento module:enable EPuzzle_CustomerPriceAdminUi`, `php bin/magento setup:static-content:deploy`
4. After installation please run the reindex command: `php bin/magento indexer:reindex`

# License
Copyright (c) 2022 ePuzzle contributors.
The customer price admin UI module is [MIT licensed](./LICENSE).
Project coordinator: &lt;dkaplin1994@gmail.com&gt;
