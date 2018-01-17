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
 * @copyright  Copyright (c) 2018 Phoenix Media GmbH (http://www.phoenix-media.eu)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


namespace Phoenix\BankPayment\Block;

/**
 * Class Form
 * @package Phoenix\BankPayment\Block
 */
class Form extends \Magento\Payment\Block\Form
{
    use \Phoenix\BankPayment\Helper\Account;

    /**
     * Bank transfer template
     *
     * @var string
     */
    protected $_template = 'form.phtml';

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $cmsPageFactory;

    /**
     * @var \Magento\Cms\Helper\Page
     */
    protected $cmsPageHelper;

    /**
     * Form constructor.
     * @param \Magento\Cms\Model\PageFactory $cmsPageFactory
     * @param \Magento\Cms\Helper\Page $cmsPageHelper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Cms\Model\PageFactory $cmsPageFactory,
        \Magento\Cms\Helper\Page $cmsPageHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->cmsPageFactory = $cmsPageFactory;
        $this->cmsPageHelper = $cmsPageHelper;

        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getCustomFormBlockType()
    {
        return $this->getMethod()->getConfigData('form_block_type');
    }

    /**
     * @return null|string
     */
    public function getFormCmsUrl()
    {
        $pageUrl = null;
        $pageCode = $this->getMethod()->getConfigData('form_cms_page');
        if (!empty($pageCode)) {
            if ($pageId = $this->cmsPageFactory->create()->checkIdentifier($pageCode, $this->_storeManager->getStore()->getId())) {
                $pageUrl = $this->cmsPageHelper->getPageUrl($pageId);
            }
        }
        return $pageUrl;
    }

    /**
     * @return mixed
     */
    public function getAccounts()
    {
        return $this->getMethod()->getAccounts();
    }

    /**
     * @param bool $addNl2Br
     * @return mixed
     */
    public function getCustomText($addNl2Br = true)
    {
        return $this->getMethod()->getCustomText($addNl2Br);
    }
}
