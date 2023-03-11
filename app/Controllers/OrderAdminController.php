<?php

namespace App\Controllers;

use App\Models\EarningsModel;
use App\Models\OrderAdminModel;
use App\Models\OrderModel;

class OrderAdminController extends BaseAdminController
{
    protected $orderAdminModel;
    protected $orderModel;
    protected $earningsModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->orderAdminModel = new OrderAdminModel();
        $this->orderModel = new OrderModel();
        $this->earningsModel = new EarningsModel();
    }

    /**
     * Orders
     */
    public function orders()
    {
        checkPermission('orders');
        $data['title'] = trans("orders");
        $numRows = $this->orderAdminModel->getOrdersCount();
        $pager = paginate($this->perPage, $numRows);
        $data['orders'] = $this->orderAdminModel->getOrdersPaginated($this->perPage, $pager->offset);
        
        echo view('admin/includes/_header', $data);
        echo view('admin/order/orders', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Order Details
     */
    public function orderDetails($id)
    {
        checkPermission('orders');
        $data['title'] = trans("order");

        $data['order'] = getOrder($id);
        if (empty($data['order'])) {
            return redirect()->to(adminUrl('orders'));
        }
        $data['orderProducts'] = $this->orderAdminModel->getOrderProducts($id);
        $data['transaction'] = $this->orderAdminModel->getTransactionByOrderId($id);

        echo view('admin/includes/_header', $data);
        echo view('admin/order/order_details', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Order Payment Received Post
     */
    public function orderPaymentReceivedPost()
    {
        checkPermission('orders');
        $orderId = inputPost('id');
        $option = inputPost('option');
        $this->orderAdminModel->updateOrderPaymentReceived($orderId);
        $this->orderAdminModel->updatePaymentStatusIfAllReceived($orderId);
        $this->orderAdminModel->updateOrderStatusIfCompleted($orderId);
        setSuccessMessage(trans("msg_updated"));
        redirectToBackUrl();
    }

    /**
     * Delete Order Post
     */
    public function deleteOrderPost()
    {
        checkPermission('orders');
        $id = inputPost('id');
        if ($this->orderAdminModel->deleteOrder($id)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /**
     * Update Order Product Status Post
     */
    public function updateOrderProductStatusPost()
    {
        checkPermission('orders');
        $id = inputPost('id');
        $orderProduct = $this->orderAdminModel->getOrderProduct($id);
        if (!empty($orderProduct)) {
            if ($this->orderAdminModel->updateOrderProductStatus($orderProduct->id)) {
                $orderStatus = inputPost('order_status');
                if ($orderStatus == 'refund_approved') {
                    $this->earningsModel->refundProduct($orderProduct);
                } else {
                    if ($orderProduct->product_type == 'digital') {
                        if ($orderStatus == 'completed' || $orderStatus == 'payment_received') {
                            $this->orderModel->addDigitalSale($orderProduct->product_id, $orderProduct->order_id);
                            //add seller earnings
                            $this->earningsModel->addSellerEarnings($orderProduct);
                        }
                    } else {
                        if ($orderStatus == 'completed') {
                            //add seller earnings
                            $this->earningsModel->addSellerEarnings($orderProduct);
                        }
                    }
                }
                setSuccessMessage(trans("msg_updated"));
            } else {
                setErrorMessage(trans("msg_error"));
            }
            $this->orderAdminModel->updatePaymentStatusIfAllReceived($orderProduct->order_id);
            $this->orderAdminModel->updateOrderStatusIfCompleted($orderProduct->order_id);
            return redirect()->to(adminUrl('order-details/' . $orderProduct->order_id . '#t_product'));
        }
        return redirect()->to(adminUrl('orders'));
    }

    /**
     * Delete Order Product Post
     */
    public function deleteOrderProductPost()
    {
        checkPermission('orders');
        $id = inputPost('id');
        if ($this->orderAdminModel->deleteOrderProduct($id)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /**
     * Transactions
     */
    public function transactions()
    {
        checkPermission('orders');
        $data['title'] = trans("transactions");
        $numRows = $this->orderAdminModel->getTransactionsCount();
        $pager = paginate($this->perPage, $numRows);
        $data['transactions'] = $this->orderAdminModel->getTransactionsPaginated($this->perPage, $pager->offset);
        
        echo view('admin/includes/_header', $data);
        echo view('admin/order/transactions', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Delete Transaction Post
     */
    public function deleteTransactionPost()
    {
        checkPermission('orders');
        $id = inputPost('id');
        if ($this->orderAdminModel->deleteTransaction($id)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /**
     * Bank Transfer Notifications
     */
    public function orderBankTransfers()
    {
        checkPermission('orders');
        $data['title'] = trans("bank_transfer_notifications");
        $numRows = $this->orderAdminModel->getBankTransfersCount();
        $pager = paginate($this->perPage, $numRows);
        $data['bankTransfers'] = $this->orderAdminModel->getBankTransfersPaginated($this->perPage, $pager->offset);

        echo view('admin/includes/_header', $data);
        echo view('admin/order/bank_transfers', $data);
        echo view('admin/includes/_footer');
    }

    /**f
     * Bank Transfer Options Post
     */
    public function bankTransferOptionsPost()
    {
        checkPermission('orders');
        $id = inputPost('id');
        $orderId = inputPost('order_id');
        $option = inputPost('option');
        if ($this->orderAdminModel->updateBankTransferStatus($id, $option)) {
            if ($option == 'approved') {
                $this->orderAdminModel->updateOrderPaymentReceived($orderId);
            }
            $this->orderAdminModel->updateOrderStatusIfCompleted($orderId);
            setSuccessMessage(trans("msg_updated"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
        if ($option != 'approved') {
            redirectToBackUrl();
        }
    }

    /**
     * Approve Guest Order Product
     */
    public function approveGuestOrderProduct()
    {
        checkPermission('orders');
        $orderProductId = inputPost('order_product_id');
        if ($this->orderAdminModel->approveGuestOrderProduct($orderProductId)) {
            $orderProduct = $this->orderAdminModel->getOrderProduct($orderProductId);
            //add seller earnings
            $this->earningsModel->addSellerEarnings($orderProduct);
            //update order status
            $this->orderAdminModel->updateOrderStatusIfCompleted($orderProduct->order_id);
        }
        redirectToBackUrl();
    }

    /**
     * Delete Bank Transfer Post
     */
    public function deleteBankTransferPost()
    {
        checkPermission('orders');
        $id = inputPost('id');
        if ($this->orderAdminModel->deleteBankTransfer($id)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /**
     * Digital Sales
     */
    public function digitalSales()
    {
        checkPermission('digital_sales');
        $data['title'] = trans("digital_sales");
        $numRows = $this->orderAdminModel->getDigitalSalesCount();
        $pager = paginate($this->perPage, $numRows);
        $data['digitalSales'] = $this->orderAdminModel->getDigitalSalesPaginated($this->perPage, $pager->offset);
        
        echo view('admin/includes/_header', $data);
        echo view('admin/order/digital_sales', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Delete Digital Sale Post
     */
    public function deleteDigitalSalePost()
    {
        checkPermission('digital_sales');
        $id = inputPost('id');
        if ($this->orderAdminModel->deleteDigitalSale($id)) {
            setSuccessMessage(trans("msg_deleted"));
        } else {
            setErrorMessage(trans("msg_error"));
        }
    }

    /*
     * --------------------------------------------------------------------
     * Refund Requests
     * --------------------------------------------------------------------
     */

    /**
     * Refund Requests
     */
    public function refundRequests()
    {
        checkPermission('refund_requests');
        $data['title'] = trans("refund_requests");
        $data['numRows'] = $this->orderModel->getRefundRequestCount(user()->id, 'admin');
        $pager = paginate($this->perPage, $data['numRows']);
        $data['refundRequests'] = $this->orderModel->getRefundRequestsPaginated(user()->id, 'admin', $this->perPage, $pager->offset);
        
        echo view('admin/includes/_header', $data);
        echo view('admin/refund/refund_requests', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Refund
     */
    public function refund($id)
    {
        checkPermission('refund_requests');
        $data['title'] = trans("refund");
        $data['refundRequest'] = $this->orderModel->getRefundRequest($id);
        if (empty($data['refundRequest'])) {
            return redirect()->to(adminUrl('refund-requests'));
        }
        $data['product'] = getOrderProduct($data['refundRequest']->order_product_id);
        if (empty($data['product'])) {
            return redirect()->to(adminUrl('refund-requests'));
        }
        $data['messages'] = $this->orderModel->getRefundMessages($id);

        echo view('admin/includes/_header', $data);
        echo view('admin/refund/refund', $data);
        echo view('admin/includes/_footer');
    }

    /**
     * Approve Refund Post
     */
    public function approveRefundPost()
    {
        checkPermission('refund_requests');
        $orderProductId = inputPost('order_product_id');
        $orderProduct = $this->orderAdminModel->getOrderProduct($orderProductId);
        if (!empty($orderProduct)) {
            $this->earningsModel->refundProduct($orderProduct);
            $this->orderAdminModel->updateOrderStatusIfCompleted($orderProduct->order_id);
        }
        redirectToBackUrl();
    }
}
