<?php
/**
 * Phoenix Bank Prepayment module for Magento 2
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Mage
 * @package    Phoenix_BankPayment
 * @copyright  Copyright (c) 2017 Phoenix Media GmbH (http://www.phoenix-media.eu)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


namespace Phoenix\BankPayment\Setup;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\DB\FieldToConvert;

use Magento\Framework\App\ObjectManager;

/**
 * Class RecurringData
 * @package Phoenix\BankPayment\Setup
 */
class RecurringData implements InstallDataInterface
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var ConfigInterface
     */
    private $resourceConfig;

    /**
     * @var array
     */
    private $configPathsToConvert = [
        'payment/phoenix_bankpayment/bank_accounts'
    ];

    /**
     * RecurringData constructor.
     * @param ProductMetadataInterface $productMetadata
     * @param ConfigInterface $resourceConfig
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        ConfigInterface $resourceConfig
    )
    {
        $this->productMetadata = $productMetadata;
        $this->resourceConfig = $resourceConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($this->productMetadata->getVersion(), '2.2.0-r20', '>=')) {
            $this->convertDataSerializedToJson($setup);
        }
    }

    /**
     * Convert serialized data into JSON-encoded
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function convertDataSerializedToJson(ModuleDataSetupInterface $setup)
    {
        /*
         * Note: we have to use the objectManager to create some class instances because DI does not work below
         * Magento 2.2 due to the fact that these classes do not exist.
         */
        $queryModifierFactory = ObjectManager::getInstance()->get(\Magento\Framework\DB\Select\QueryModifierFactory::class);

        $fields = array();
        foreach ($this->configPathsToConvert as $path) {
            $queryModifier = $queryModifierFactory->create(
                'in',
                [
                    'values' => [
                        'path' => [
                            $path,
                        ]
                    ]
                ]
            );

            $fields[] = new FieldToConvert(
                \Magento\Framework\DB\DataConverter\SerializedToJson::class,
                $setup->getTable('core_config_data'),
                'config_id',
                'value',
                $queryModifier
            );
        }

        $aggregatedFieldConverter = ObjectManager::getInstance()->get(\Magento\Framework\DB\AggregatedFieldDataConverter::class);
        $aggregatedFieldConverter->convert($fields, $setup->getConnection());
    }
}
