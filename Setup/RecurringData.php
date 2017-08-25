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
use Magento\Framework\DB\AggregatedFieldDataConverter;
use Magento\Framework\DB\Select\QueryModifierFactory;
use Magento\Framework\DB\FieldToConvert;
use Magento\Framework\DB\DataConverter\SerializedToJson;

/**
 * Class RecurringData
 * @package Phoenix\BankPayment\Setup
 */
class RecurringData implements InstallDataInterface
{
    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var ConfigInterface
     */
    protected $resourceConfig;

    /**
     * @var \Magento\Framework\DB\AggregatedFieldDataConverter
     */
    protected $aggregatedFieldConverter;

    /**
     * @var \Magento\Framework\DB\Select\QueryModifierFactory
     */
    protected $queryModifierFactory;

    /**
     * @var \Magento\Framework\DB\Query\Generator
     */
    protected $queryGenerator;

    /**
     * @var array
     */
    private $configPathsToConvert = array(
        'payment/phoenix_bankpayment/bank_accounts'
    );


    /**
     * RecurringData constructor.
     * @param ProductMetadataInterface $productMetadata
     * @param ConfigInterface $resourceConfig
     * @param AggregatedFieldDataConverter $aggregatedFieldConverter
     * @param QueryModifierFactory $queryModifierFactory
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        ConfigInterface  $resourceConfig,
        AggregatedFieldDataConverter $aggregatedFieldConverter,
        QueryModifierFactory $queryModifierFactory
    ) {
        $this->productMetadata = $productMetadata;
        $this->resourceConfig = $resourceConfig;
        $this->aggregatedFieldConverter = $aggregatedFieldConverter;
        $this->queryModifierFactory = $queryModifierFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($this->productMetadata->getVersion(), '2.2.0', '>=')) {
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

        $fields = array();
        foreach ($this->configPathsToConvert as $path) {
            $queryModifier = $this->queryModifierFactory->create(
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
                SerializedToJson::class,
                $setup->getTable('core_config_data'),
                'config_id',
                'value',
                $queryModifier
            );
        }

        $this->aggregatedFieldConverter->convert($fields, $setup->getConnection());
    }
}
