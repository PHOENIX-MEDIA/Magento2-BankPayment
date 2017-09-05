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


namespace Phoenix\BankPayment\Model;

/**
 * Class BankPayment
 * @package Phoenix\BankPayment\Model
 */
class BankPayment extends \Magento\Payment\Model\Method\AbstractMethod
{

    const PAYMENT_METHOD_PHOENIX_BANKPAYMENT_CODE = 'phoenix_bankpayment';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_PHOENIX_BANKPAYMENT_CODE;

    /**
     * @var \Phoenix\BankPayment\Model\Serialized $_serialized
     */
    protected $_serialized;

    /**
     * @var mixed
     */
    protected $_accounts;

    /**
     * Bank Transfer payment block paths
     *
     * @var string
     */
    protected $_formBlockType = 'Phoenix\BankPayment\Block\Form';

    /**
     * Instructions block path
     *
     * @var string
     */
    protected $_infoBlockType = 'Phoenix\BankPayment\Block\Info';

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;

    /**
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * @var bool
     */
    protected $_canCapturePartial = true;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $_dataObjectFactory;

    public function __construct(
        \Phoenix\BankPayment\Model\Serialized $serialized,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );

        $this->_serialized = $serialized;
        $this->_dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @return mixed
     */
    public function getPayWithinXDays()
    {
        return $this->getConfigData('paywithinxdays');
    }

    /**
     * @param bool $addNl2Br
     * @return mixed|string
     */
    public function getCustomText($addNl2Br = true)
    {
        $customText = $this->getConfigData('customtext');
        if ($addNl2Br) {
            $customText = nl2br($customText);
        }
        return $customText;
    }

    /**
     * @return array|mixed
     */
    public function getAccounts()
    {
        if (!$this->_accounts) {
            $paymentInfo = $this->getInfoInstance();
            $storeId = null;
            if ($currentOrder = $this->_registry->registry('current_order')) {
                $storeId = $currentOrder->getStoreId();
            } elseif ($paymentInfo instanceof \Magento\Sales\Model\Order\Payment) {
                $storeId = $paymentInfo->getOrder()->getStoreId();
            } else {
                $storeId = $paymentInfo->getQuote()->getStoreId();
            }

            $accounts = $this->_serialized->unserialize($this->_scopeConfig->getValue('payment/phoenix_bankpayment/bank_accounts', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId));

            $this->_accounts = [];
            $fields = is_array($accounts) ? array_keys($accounts) : null;
            if (!empty($fields)) {
                foreach ($accounts[$fields[0]] as $i => $k) {
                    if ($k) {
                        $account = $this->_dataObjectFactory->create();
                        foreach ($fields as $field) {
                            $account->setData($field, $accounts[$field][$i]);
                        }
                        $this->_accounts[] = $account;
                    }
                }
            }
        }
        return $this->_accounts;
    }
}
