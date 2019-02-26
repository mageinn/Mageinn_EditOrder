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

namespace Mageinn\EditOrder\Model\Service;

/**
 * Class FreeShippingService
 * @package Mageinn\EditOrder\Model\Service
 */
class FreeShippingService
{
    /**
     * @var \Magento\Framework\View\Element\Context
     */
    private $context;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    private $orderCollection;

    /**
     * @var \Mageinn\EditOrder\Helper\Data
     */
    private $helper;

    /**
     * List of addresses to which delivery was made earlier
     *
     * @var array
     */
    private $addressesList;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * FreeShippingService constructor.
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection
     * @param \Magento\Checkout\Model\SessionFactory $checkoutSession
     * @param \Mageinn\EditOrder\Helper\Data $helper
     */
    public function __construct
    (
        \Magento\Framework\View\Element\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        \Magento\Checkout\Model\SessionFactory $checkoutSession,
        \Mageinn\EditOrder\Helper\Data $helper
    )
    {
        $this->context = $context;
        $this->orderCollection = $orderCollection->create();
        $this->checkoutSession = $checkoutSession->create();
        $this->helper = $helper;
    }

    /**
     * The main method responsible for working out the service
     * It checks all conditions
     *
     * @return bool
     */
    public function isAvailable(){

        if(!$this->helper->isEnabled())
            return false;
        /**
         *  3 condition
         */
        if($this->getLoggedInCustomerId() && $this->checkConditionByStatuses() && $this->checkConditionByAddress())
            return true;

        return false;
    }

    /**
     * Check condition by selected order statuses
     *
     * @return bool
     */
    protected function checkConditionByStatuses(){
        //current customer_id
        $customerId = $this->getLoggedinCustomerId();
        $selectedOrderStatuses = $this->helper->getSelectedOrderStatusesAsArray();
        $orders = $this->getOrdersByCustomerId($customerId);

        foreach ($orders as $order){
            /**
             * @var $order \Magento\Sales\Model\Order
             */
            $address = $order->getShippingAddress()->getData();
            $this->addressesList[] = $this->helper->prepareArrayForComparison($address);
        }

        return (!empty($this->addressesList)) ? true : false;
    }


    /**
     * Check condition by address
     *
     * @return bool
     */
    protected function checkConditionByAddress(){
        $selectedAddress = $this->checkoutSession->getQuote()->getShippingAddress()->getData();
        $selectedAddress = $this->helper->prepareArrayForComparison($selectedAddress);

        foreach ($this->addressesList as $address){
            $result = array_diff($selectedAddress, $address);
            if(empty($result)) return true;
        }

        return false;
    }


    /**
     * Get all orders current customer by customer_id
     *
     * @param $customerId
     * @return \Magento\Framework\DataObject[]
     */
    protected function getOrdersByCustomerId($customerId){
       return  $this->orderCollection->addAttributeToFilter('customer_id', $customerId)
           ->addAttributeToFilter('status', [
               'in' => $this->helper->getSelectedOrderStatusesAsArray()
           ])
           ->load()
           ->getItems();
    }

    /**
     * Return customer_id if exist
     *
     * @return bool|int|null
     */
    protected function getLoggedInCustomerId() {
        return $this->helper->getLoggedInCustomerId();
    }
}