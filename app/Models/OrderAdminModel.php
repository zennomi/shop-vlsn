<?php namespace App\Models;

use CodeIgniter\Model;

/*
 * STATUS
 * processing  : 0
 * completed   : 1
 * cancelled   : 2
 */

/*
 * ORDER STATUS FOR ORDER PRODUCTS
 *
 * 1. awaiting_payment
 * 2. payment_received
 * 3. order_processing
 * 4. shipped
 * 5. completed
 * 6. cancelled
 */

class OrderAdminModel extends BaseModel
{
    protected $builder;
    protected $builderOrderProducts;
    protected $builderInvoices;
    protected $builderDigitalSales;
    protected $builderTransactions;
    protected $builderPromotedTransactions;
    protected $builderBankTransfers;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('orders');
        $this->builderOrderProducts = $this->db->table('order_products');
        $this->builderInvoices = $this->db->table('invoices');
        $this->builderDigitalSales = $this->db->table('digital_sales');
        $this->builderTransactions = $this->db->table('transactions');
        $this->builderPromotedTransactions = $this->db->table('promoted_transactions');
        $this->builderBankTransfers = $this->db->table('bank_transfers');
    }

    //update order payment as received
    public function updateOrderPaymentReceived($orderId)
    {
        $order = $this->getOrder($orderId);
        if (!empty($order)) {
            //update product payment status
            $dataOrder = [
                'payment_status' => 'payment_received',
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->builder->where('id', $order->id)->update($dataOrder);
            //update order products payment status
            $orderProducts = $this->getOrderProducts($orderId);
            if (!empty($orderProducts)) {
                foreach ($orderProducts as $orderProduct) {
                    $data = [
                        'order_status' => 'payment_received',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                    if ($orderProduct->product_type == 'digital') {
                        $data['order_status'] = 'completed';
                        //add digital sale
                        $orderModel = new OrderModel();
                        $orderModel->addDigitalSale($orderProduct->product_id, $orderId);
                        //add seller earnings
                        $earningsModel = new EarningsModel();
                        $earningsModel->addSellerEarnings($orderProduct);
                    }
                    $this->builderOrderProducts->where('id', $orderProduct->id)->update($data);
                }
            }
        }
    }

    //update order product status
    public function updateOrderProductStatus($orderProductId)
    {
        $orderProduct = $this->getOrderProduct($orderProductId);
        if (!empty($orderProduct)) {
            $data = [
                'order_status' => inputPost('order_status'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            if ($data["order_status"] == 'completed' || $data['order_status'] == 'cancelled') {
                $data['is_approved'] = 1;
            } else {
                $data['is_approved'] = 0;
            }
            return $this->builderOrderProducts->where('id', $orderProduct->id)->update($data);
        }
        return false;
    }

    //check order products status / update if all suborders completed
    public function updateOrderStatusIfCompleted($orderId)
    {
        $allComplated = true;
        $orderProducts = $this->getOrderProducts($orderId);
        if (!empty($orderProducts)) {
            foreach ($orderProducts as $orderProduct) {
                if ($orderProduct->order_status == 'awaiting_payment' || $orderProduct->order_status == 'payment_received' || $orderProduct->order_status == 'order_processing' || $orderProduct->order_status == 'shipped') {
                    $allComplated = false;
                }
            }
            $data = [
                'status' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            if ($allComplated == true) {
                $data['status'] = 1;
            }
            $this->builder->where('id', clrNum($orderId))->update($data);
        }
    }

    //check order payment status / update if all payments received
    public function updatePaymentStatusIfAllReceived($orderId)
    {
        $allReceived = true;
        $orderProducts = $this->getOrderProducts($orderId);
        if (!empty($orderProducts)) {
            foreach ($orderProducts as $orderProduct) {
                if ($orderProduct->order_status == 'awaiting_payment') {
                    $allReceived = false;
                }
            }
            $data = [
                'payment_status' => 'awaiting_payment',
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            if ($allReceived == true) {
                $data['payment_status'] = 'payment_received';
            }
            $this->builder->where('id', clrNum($orderId))->update($data);
        }
    }

    //approve guest order product
    public function approveGuestOrderProduct($orderProductId)
    {
        $orderProduct = $this->getOrderProduct($orderProductId);
        if (!empty($orderProduct)) {
            $data = [
                'is_approved' => 1,
                'order_status' => 'completed',
                'updated_at' => date('Y-m-d H:i:s')
            ];
            return $this->builderOrderProducts->where('id', $orderProduct->id)->update($data);
        }
        return false;
    }

    //delete order product
    public function deleteOrderProduct($orderProductId)
    {
        $orderProduct = $this->getOrderProduct($orderProductId);
        if (!empty($orderProduct)) {
            return $this->builderOrderProducts->where('id', $orderProduct->id)->delete();
        }
        return false;
    }

    //filter by values
    public function filterOrders()
    {
        $data = [
            'status' => inputGet('status'),
            'payment_status' => inputGet('payment_status'),
            'q' => inputGet('q'),
        ];
        if (!empty($data['status'])) {
            if ($data['status'] == 'completed') {
                $this->builder->where('orders.status', 1);
            } elseif ($data['status'] == 'cancelled') {
                $this->builder->where('orders.status', 2);
            } elseif ($data['status'] == 'processing') {
                $this->builder->where('orders.status', 0);
            }
        }
        if (!empty($data['payment_status'])) {
            $this->builder->where('orders.payment_status', $data['payment_status']);
        }
        $data['q'] = trim($data['q'] ?? '');
        if (!empty($data['q'])) {
            $data['q'] = str_replace('#', '', $data['q']);
            $this->builder->where('orders.order_number', $data['q']);
        }
    }

    //get orders count
    public function getOrdersCount()
    {
        $this->filterOrders();
        return $this->builder->countAllResults();
    }

    //get all orders count
    public function getAllOrdersCount()
    {
        return $this->builder->countAllResults();
    }

    //get orders limited
    public function getOrdersLimited($limit)
    {
        return $this->builder->orderBy('orders.id DESC')->get(clrNum($limit))->getResult();
    }

    //get paginated orders
    public function getOrdersPaginated($perPage, $offset)
    {
        $this->filterOrders();
        return $this->builder->orderBy('orders.id DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //get order products
    public function getOrderProducts($orderId)
    {
        return $this->builderOrderProducts->where('order_id', clrNum($orderId))->get()->getResult();
    }

    //get order
    public function getOrder($id)
    {
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    //get order product
    public function getOrderProduct($id)
    {
        return $this->builderOrderProducts->where('id', clrNum($id))->get()->getRow();
    }

    //delete order
    public function deleteOrder($id)
    {
        $order = $this->getOrder($id);
        if (!empty($order)) {
            //delete order products
            $orderProducts = $this->getOrderProducts($id);
            if (!empty($orderProducts)) {
                foreach ($orderProducts as $orderProduct) {
                    $this->builderOrderProducts->where('id', $orderProduct->id)->delete();
                }
            }
            //delete invoice
            $this->builderInvoices->where('order_id', $order->id)->delete();
            //delete order
            return $this->builder->where('id', $order->id)->delete();
        }
        return false;
    }

    //get digital sale
    public function getDigitalSale($id)
    {
        return $this->builderDigitalSales->where('id', clrNum($id))->get()->getRow();
    }

    //get digital sales count
    public function getDigitalSalesCount()
    {
        $q = inputGet('q');
        if (!empty($q)) {
            $this->builderDigitalSales->like('purchase_code', removeSpecialCharacters($q));
        }
        return $this->builderDigitalSales->countAllResults();
    }

    //get digital sales
    public function getDigitalSalesPaginated($perPage, $offset)
    {
        $q = inputGet('q');
        if (!empty($q)) {
            $this->builderDigitalSales->like('purchase_code', removeSpecialCharacters($q));
        }
        return $this->builderDigitalSales->orderBy('purchase_date DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //delete digital sale
    public function deleteDigitalSale($id)
    {
        $sale = $this->getDigitalSale($id);
        if (!empty($sale)) {
            return $this->builderDigitalSales->where('id', $sale->id)->delete();
        }
        return false;
    }

    //get bank transfer notifications
    public function getBankTransfersCount()
    {
        $this->filterBankTransfers();
        return $this->builderBankTransfers->countAllResults();
    }

    //get paginated bank transfer notifications
    public function getBankTransfersPaginated($perPage, $offset)
    {
        $this->filterBankTransfers();
        return $this->builderBankTransfers->orderBy('created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //filter bank transfers
    public function filterBankTransfers()
    {
        $status = inputGet('status');
        $q = inputGet('q');
        if (!empty($status)) {
            $this->builderBankTransfers->where('status', $status);
        }
        if (!empty($q)) {
            $q = urldecode($q);
            $q = str_replace('#', '', $q);
            $this->builderBankTransfers->where('order_number', $q);
        }
    }

    //get bank transfer
    public function getBankTransfer($id)
    {
        return $this->builderBankTransfers->where('id', clrNum($id))->get()->getRow();
    }

    //get bank transfer by order number
    public function getBankTransferByOrderNumber($orderNumber)
    {
        return $this->builderBankTransfers->where('order_number', cleanStr($orderNumber))->get()->getRow();
    }

    //update bank transfer status
    public function updateBankTransferStatus($id, $option)
    {
        $transfer = $this->getBankTransfer($id);
        if (!empty($transfer)) {
            return $this->builderBankTransfers->where('id', $transfer->id)->update(['status' => $option]);
        }
        return false;
    }

    //delete bank transfer
    public function deleteBankTransfer($id)
    {
        $transfer = $this->getBankTransfer($id);
        if (!empty($transfer)) {
            deleteFile($transfer->receipt_path);
            return $this->builderBankTransfers->where('id', $transfer->id)->delete();
        }
        return false;
    }

    /*
     * --------------------------------------------------------------------
     * Sales Statistics
     * --------------------------------------------------------------------
     */

    //get active sales count by seller
    public function getActiveSalesCountBySeller($sellerId)
    {
        return $this->builder->select('orders.id')->join('order_products', 'order_products.order_id = orders.id')
            ->where('order_products.seller_id', clrNum($sellerId))->where('order_status !=', 'completed')->where('order_status !=', 'cancelled')->distinct()->countAllResults();
    }

    //get completed sales count by seller
    public function getCompletedSalesCountBySeller($sellerId)
    {
        return $this->builder->select('orders.id')->join('order_products', 'order_products.order_id = orders.id')
            ->where('order_products.seller_id', clrNum($sellerId))->where('order_status', 'completed')->distinct()->countAllResults();
    }

    //get sales sum by month
    public function getSalesSumByMonth($sellerId)
    {
       return $this->db->query('SELECT SUM(product_total_price) AS total_amount, MONTH(created_at) AS month FROM order_products WHERE seller_id = ? AND YEAR(created_at) = YEAR(CURDATE()) GROUP BY MONTH(created_at)', [clrNum($sellerId)])->getResult();
    }

    /*
     * --------------------------------------------------------------------
     * Transactions
     * --------------------------------------------------------------------
     */

    //get transactions count
    public function getTransactionsCount()
    {
        $this->filterTransactions();
        return $this->builderTransactions->countAllResults();
    }

    //get paginated transactions
    public function getTransactionsPaginated($perPage, $offset)
    {
        $this->filterTransactions();
        return $this->builderTransactions->orderBy('transactions.created_at DESC')->limit($perPage, $offset)->get()->getResult();
    }

    //filter transactions
    public function filterTransactions()
    {
        $q = inputGet('q');
        if (!empty($q)) {
            $order = getOrderByOrderNumber($q);
            if (!empty($order)) {
                $this->builderTransactions->where('transactions.order_id', $order->id);
            } else {
                $this->builderTransactions->where('transactions.order_id', 0);
            }
        }
    }

    //get transactions limited
    public function getTransactionsLimited($limit)
    {
        return $this->builderTransactions->orderBy('created_at DESC')->get(clrNum($limit))->getResult();
    }

    //get transaction
    public function getTransaction($id)
    {
        return $this->builderTransactions->where('id', clrNum($id))->get()->getRow();
    }

    //get transaction by order id
    public function getTransactionByOrderId($orderId)
    {
        return $this->builderTransactions->where('order_id', clrNum($orderId))->get()->getRow();
    }

    //delete transaction
    public function deleteTransaction($id)
    {
        $transaction = $this->getTransaction($id);
        if (!empty($transaction)) {
            return $this->builderTransactions->where('id', $transaction->id)->delete();
        }
        return false;
    }

    //get promoted transactions limited
    public function getPromotedTransactionsLimited($limit)
    {
        return $this->builderPromotedTransactions->orderBy('created_at DESC')->get(clrNum($limit))->getResult();
    }
}
