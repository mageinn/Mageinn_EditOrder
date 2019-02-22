<?php
/**
 * Mageinn_EditOrder extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Mageinn
 * @package     Mageinn_EditOrder
 * @copyright   Copyright (c) 2017 Mageinn. (http://mageinn.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mageinn\EditOrder\Helper;


use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const ORDER_STATUSES =  'mageinn_editorder_configuration/general/order_statuses';
    const MODULE_ENABLE = 'mageinn_editorder_configuration/general/enable';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Context $context
    )
    {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Return string of selected order statuses
     *
     * @return string
     */
    public function getSelectedOrderStatusesAsString(){
        return $this->scopeConfig->getValue(self::ORDER_STATUSES, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Return array of selected order statuses
     *
     * @return array
     */
    public function getSelectedOrderStatusesAsArrat(){
        $statuses = $this->scopeConfig->getValue(self::ORDER_STATUSES, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return explode(',', $statuses);
    }
}