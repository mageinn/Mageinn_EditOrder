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

/**
 * @category   Mageinn
 * @package    Mageinn_EditOrder
 * @author     Mageinn
 */

namespace Mageinn\EditOrder\Observer;

use Mageinn\EditOrder\Helper\Data;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;


class SetFlagOrderIsCombined implements ObserverInterface
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected $orderCollection;
    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $orderRepository;
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * SetFlagOrderIsCombined constructor.
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Framework\App\ResourceConnection $connection
     */
    public function __construct
    (
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\App\ResourceConnection $connection
    )
    {
        $this->orderRepository = $orderRepository;
        $this->orderCollection = $orderCollectionFactory->create();
        $this->connection = $connection->getConnection();
    }

    public function execute(Observer $observer)
    {
        $orderIds = $observer->getData('order_ids');
        $orderId = $orderIds[0];

        $order = $this->orderRepository->get($orderId);
        $shippingMethod = $order->getShippingMethod();

        if($shippingMethod === Data::SHIPPING_METHOD)
        {
            $customerId = $order->getCustomerId();

            /**
             * @var $previousOrder \Magento\Sales\Model\Order
             */
            $previousOrder = $this->orderCollection->addAttributeToFilter('customer_id', $customerId)
                ->addAttributeToFilter('entity_id',  array('neq' => $orderId))
                ->setOrder('entity_id', 'DESC')
                ->load()
                ->getFirstItem();

            $tableName = $this->connection->getTableName('sales_order_grid');
            //Update Data into table
            $sql = "UPDATE " . $tableName . " SET combined_order_id = '" . $previousOrder->getRealOrderId() . "' WHERE entity_id = " . $orderId;
            $this->connection->query($sql);
        }
    }
}