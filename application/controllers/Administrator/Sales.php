<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sales extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->sbrunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model('Billing_model');
        $this->load->library('cart');
        $this->load->model('Model_table', "mt", TRUE);
        $this->load->helper('form');
        $this->load->model('SMS_model', 'sms', true);
    }

    public function index()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }

        $data['title'] = "Product Sales";

        $invoice = $this->mt->generateSalesInvoice();
        $data['salesId'] = 0;
        $data['invoice'] = $invoice;
        $data['content'] = $this->load->view('Administrator/sales/product_sales', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function orders()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }

        $data['title'] = "Product Orders";

        $invoice = $this->mt->generateSalesInvoice();
        $data['salesId'] = 0;
        $data['invoice'] = $invoice;
        $data['content'] = $this->load->view('Administrator/sales/product_orders', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    //Designation
    public function addSales()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);

            // check stock
            foreach ($data->cart as $cartProduct) {
                $checkStock = $this->mt->productStock($cartProduct->productId);
                if (($cartProduct->quantity > $checkStock) && $cartProduct->is_service == 'false') {
                    $res = ['success' => false, 'message' => "({$cartProduct->name} - {$cartProduct->productCode}) stock unavailable"];
                    echo json_encode($res);
                    exit;
                }
            }

            if ($data->sales->salesFrom != $this->session->userdata("BRANCHid")) {
                $res = ['success' => false, 'message' => 'You have already changed your branch.', 'branch_status' => false];
                echo json_encode($res);
                exit;
            }
            $invoice = $data->sales->invoiceNo;
            $invoiceCount = $this->db->query("select * from tbl_salesmaster where SaleMaster_InvoiceNo = ?", $invoice)->num_rows();
            if ($invoiceCount != 0) {
                $invoice = $this->mt->generateSalesInvoice();
            }

            $customerId = $data->sales->customerId;

            if (isset($data->customer)) {
                $customer = (array)$data->customer;
                unset($customer['Customer_SlNo']);
                unset($customer['display_name']);
                unset($customer['Customer_Type']);
                unset($customer['display_text']);

                $mobile_count = $this->db->query("select * from tbl_customer where Customer_Mobile = ? and branch_id = ?", [$data->customer->Customer_Mobile, $this->session->userdata("BRANCHid")]);
                if (
                    $data->customer->Customer_Mobile != '' &&
                    $data->customer->Customer_Mobile != null &&
                    $mobile_count->num_rows() > 0
                ) {
                    $duplicateCustomer = $mobile_count->row();
                    unset($customer['Customer_Code']);
                    unset($customer['Customer_Type']);
                    unset($customer['District_Name']);
                    unset($customer['added_by']);
                    unset($customer['deleted_by']);
                    $customer["UpdateBy"]   = $this->session->userdata("userId");
                    $customer["UpdateTime"] = date("Y-m-d H:i:s");
                    $customer["status"]     = 'a';
                    if ($duplicateCustomer->Customer_Type == 'G') {
                        $customer["Customer_Type"] = 'retail';
                    }
                    $this->db->where('Customer_SlNo', $duplicateCustomer->Customer_SlNo)->update('tbl_customer', $customer);
                    $customerId = $duplicateCustomer->Customer_SlNo;
                } else {
                    if ($data->customer->Customer_Type == 'N') {
                        
                        $customer['Customer_Code'] = $this->mt->generateCustomerCode();
                        $customer['Customer_Type'] = $data->sales->salesType;
                        $customer['status'] = 'a';
                        $customer['AddBy'] = $this->session->userdata("userId");
                        $customer['AddTime'] = date("Y-m-d H:i:s");
                        $customer['last_update_ip'] = get_client_ip();
                        $customer['branch_id'] = $this->session->userdata("BRANCHid");

                        $this->db->insert('tbl_customer', $customer);
                        $customerId = $this->db->insert_id();
                    }
                }
            }

            $sales = array( 
                'SaleMaster_InvoiceNo'           => $invoice,
                'employee_id'                    => $data->sales->employeeId,
                'SaleMaster_SaleDate'            => $data->sales->salesDate,
                'SaleMaster_SaleType'            => $data->sales->salesType,
                'sale_type'                      => "Sale",
                'SaleMaster_TotalSaleAmount'     => $data->sales->total,
                'SaleMaster_TotalDiscountAmount' => $data->sales->discount,
                'SaleMaster_TaxAmount'           => $data->sales->vat,
                'SaleMaster_Freight'             => $data->sales->transportCost,
                'cash_paid'                      => $data->sales->cash_paid,
                'account_id'                     => $data->sales->accountId??null,
                'bank_paid'                      => $data->sales->bank_paid,
                'payment_type'                   => $data->sales->payment_type,
                'SaleMaster_SubTotalAmount'      => $data->sales->subTotal,
                'SaleMaster_PaidAmount'          => $data->sales->paid,
                'SaleMaster_DueAmount'           => $data->sales->due,
                'SaleMaster_Previous_Due'        => $data->sales->previousDue,
                'SaleMaster_Description'         => $data->sales->note,
                'status'                         => 'a',
                "AddBy"                          => $this->session->userdata("userId"),
                'AddTime'                        => date("Y-m-d H:i:s"),
                'last_update_ip'                 => get_client_ip(),
                'branch_id'                      => $this->session->userdata("BRANCHid")
            );
            if ($data->customer->Customer_Type == 'G') {
                $sales['SalseCustomer_IDNo']    = Null;
                $sales['customerType']    = "G";
                $sales['customerName']    = $data->customer->Customer_Name;
                $sales['customerMobile']  = $data->customer->Customer_Mobile;
                $sales['customerAddress'] = $data->customer->Customer_Address;
            } else {
                $sales['customerType'] = $data->customer->Customer_Type == 'N' ? "retail" : 'retail';
                $sales['SalseCustomer_IDNo'] = $customerId;
            }
            $this->db->insert('tbl_salesmaster', $sales);

            $salesId = $this->db->insert_id();

            if ($data->sales->payment_type=='Bank' && $data->sales->accountId != 0) {
                $salesBank = array(
                    'sales_id'         => $salesId,
                    'account_id'       => $data->sales->accountId,
                    'transaction_date' => $data->sales->salesDate,
                    'amount'           => $data->sales->bank_paid,
                    'transaction_type' => 'deposit',
                    'form_type'        => 'Sale',
                    'note'             => 'Sales Invoice Payment in Bank',
                    'AddBy'            => $this->session->userdata('userId'),
                    'AddTime'          => date('Y-m-d H:i:s'),
                    'branch_id'        => $this->session->userdata('BRANCHid'),
                    'status'           => 1,
                );
                $this->db->insert('tbl_bank_transactions', $salesBank);
            }

            foreach ($data->cart as $cartProduct) {
                $saleDetails = array(
                    'SaleMaster_IDNo'           => $salesId,
                    'Product_IDNo'              => $cartProduct->productId,
                    'SaleDetails_TotalQuantity' => $cartProduct->quantity,
                    'Purchase_Rate'             => $cartProduct->purchaseRate,
                    'SaleDetails_Rate'          => $cartProduct->salesRate,
                    'SaleDetails_Tax'           => $cartProduct->vat,
                    'SaleDetails_TotalAmount'   => $cartProduct->total,
                    'is_service'                => $cartProduct->is_service,
                    'discountAmount'            => $cartProduct->discountAmount, 
                    'discountPercent'           => $cartProduct->discountPercent, 
                    'note'                      => $cartProduct->note ?? null,
                    'status'                    => 'a',
                    'AddBy'                     => $this->session->userdata("userId"),
                    'AddTime'                   => date('Y-m-d H:i:s'),
                    'last_update_ip'            => get_client_ip(),
                    'branch_id'                 => $this->session->userdata('BRANCHid')
                );
                $this->db->insert('tbl_saledetails', $saleDetails);
                if ($cartProduct->is_service == 'false') {
                    //update stock
                    $this->db->query("
                        update tbl_currentinventory 
                        set sales_quantity = sales_quantity + ? 
                        where product_id = ?
                        and branch_id = ?
                    ", [$cartProduct->quantity, $cartProduct->productId, $this->session->userdata('BRANCHid')]);
                }
            }
            //Send sms
            
          
            if (!empty($data->customer->Customer_Mobile) && preg_match("/(^(01){1}[3456789]{1}(\d){8})$/", trim($data->customer->Customer_Mobile)) == 1) {
                $currentDue = $data->sales->previousDue + ($data->sales->total - $data->sales->paid);
                $customerInfo = $this->db->query("select * from tbl_customer where Customer_SlNo = ?", $customerId)->row();
                    if(!empty($customerInfo)){
                        $sendToName = $customerInfo->owner_name != '' ? $customerInfo->owner_name : $customerInfo->Customer_Name;
                        $recipient = $customerInfo->Customer_Mobile;
                    }else{
                        $sendToName = $data->customer->Customer_Name;
                        $recipient = $data->customer->Customer_Mobile;
                    }
                $currency = $this->session->userdata('Currency_Name');
                $message = "Dear {$sendToName},\nYour invoice No. {$invoice}\nBill is {$currency} {$data->sales->total}\nReceived {$currency} {$data->sales->paid}\nCurrent due {$currency} {$currentDue}";
                $this->sms->sendSms($recipient, $message);
            }

            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'Sales Success', 'salesId' => $salesId];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }
    public function addOrder()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);

            // check stock
            // foreach ($data->cart as $cartProduct) {
            //     $checkStock = $this->mt->productStock($cartProduct->productId);
            //     if (($cartProduct->quantity > $checkStock) && $cartProduct->is_service == 'false') {
            //         $res = ['success' => false, 'message' => "({$cartProduct->name} - {$cartProduct->productCode}) stock unavailable"];
            //         echo json_encode($res);
            //         exit;
            //     }
            // }

            if ($data->sales->salesFrom != $this->session->userdata("BRANCHid")) {
                $res = ['success' => false, 'message' => 'You have already changed your branch.', 'branch_status' => false];
                echo json_encode($res);
                exit;
            }
            $invoice = $data->sales->invoiceNo;
            $invoiceCount = $this->db->query("select * from tbl_salesmaster where SaleMaster_InvoiceNo = ?", $invoice)->num_rows();
            if ($invoiceCount != 0) {
                $invoice = $this->mt->generateSalesInvoice();
            }

            $customerId = $data->sales->customerId;

            if (isset($data->customer)) {
                $customer = (array)$data->customer;
                unset($customer['Customer_SlNo']);
                unset($customer['display_name']);
                unset($customer['display_text']);
                unset($customer['Customer_Type']);

                $mobile_count = $this->db->query("select * from tbl_customer where Customer_Mobile = ? and branch_id = ?", [$data->customer->Customer_Mobile, $this->session->userdata("BRANCHid")]);
                if (
                    $data->customer->Customer_Mobile != '' &&
                    $data->customer->Customer_Mobile != null &&
                    $mobile_count->num_rows() > 0
                ) {
                    $duplicateCustomer = $mobile_count->row();
                    unset($customer['Customer_Code']);
                    unset($customer['Customer_Type']);
                    unset($customer['District_Name']);
                    unset($customer['added_by']);
                    unset($customer['deleted_by']);
                    $customer["UpdateBy"]   = $this->session->userdata("userId");
                    $customer["UpdateTime"] = date("Y-m-d H:i:s");
                    $customer["status"]     = 'a';
                    if ($duplicateCustomer->Customer_Type == 'G') {
                        $customer["Customer_Type"] = 'retail';
                    }
                    $this->db->where('Customer_SlNo', $duplicateCustomer->Customer_SlNo)->update('tbl_customer', $customer);
                    $customerId = $duplicateCustomer->Customer_SlNo;
                } else {
                    if ($data->customer->Customer_Type == 'N') {
                        
                        $customer['Customer_Code'] = $this->mt->generateCustomerCode();
                        $customer['Customer_Type'] = $data->sales->salesType;
                        $customer['status'] = 'a';
                        $customer['AddBy'] = $this->session->userdata("userId");
                        $customer['AddTime'] = date("Y-m-d H:i:s");
                        $customer['last_update_ip'] = get_client_ip();
                        $customer['branch_id'] = $this->session->userdata("BRANCHid");

                        $this->db->insert('tbl_customer', $customer);
                        $customerId = $this->db->insert_id();
                    }
                }
            }

            $sales = array(
                'SaleMaster_InvoiceNo'           => $invoice,
                'employee_id'                    => $data->sales->employeeId,
                'SaleMaster_SaleDate'            => $data->sales->salesDate,
                'SaleMaster_SaleType'            => $data->sales->salesType,
                'SaleMaster_TotalSaleAmount'     => $data->sales->total,
                'SaleMaster_TotalDiscountAmount' => $data->sales->discount,
                'SaleMaster_TaxAmount'           => $data->sales->vat,
                'SaleMaster_Freight'             => $data->sales->transportCost,
                'cash_paid'                      => $data->sales->cash_paid,
                'account_id'                     => $data->sales->accountId??null,
                'bank_paid'                      => $data->sales->bank_paid,
                'payment_type'                   => $data->sales->payment_type,
                'SaleMaster_SubTotalAmount'      => $data->sales->subTotal,
                'SaleMaster_PaidAmount'          => $data->sales->paid,
                'SaleMaster_DueAmount'           => $data->sales->due,
                'SaleMaster_Previous_Due'        => $data->sales->previousDue,
                'SaleMaster_Description'         => $data->sales->note,
                'status'                         => $data->sales->status,
                'sale_type'                      => $data->sales->sale_type,
                "AddBy"                          => $this->session->userdata("userId"),
                'AddTime'                        => date("Y-m-d H:i:s"),
                'last_update_ip'                 => get_client_ip(),
                'branch_id'                      => $this->session->userdata("BRANCHid")
            );
            if ($data->customer->Customer_Type == 'G') {
                $sales['SalseCustomer_IDNo']    = Null;
                $sales['customerType']    = "G";
                $sales['customerName']    = $data->customer->Customer_Name;
                $sales['customerMobile']  = $data->customer->Customer_Mobile;
                $sales['customerAddress'] = $data->customer->Customer_Address;
            } else {
                $sales['customerType'] = $data->customer->Customer_Type == 'N' ? "retail" : 'retail';
                $sales['SalseCustomer_IDNo'] = $customerId;
            }
            $this->db->insert('tbl_salesmaster', $sales);

            $salesId = $this->db->insert_id();

    

            if ($data->sales->payment_type=='Bank' && $data->sales->accountId != 0) {
                
                $salesBank = array(
                    'sales_id'         => $salesId,
                    'account_id'       => $data->sales->accountId,
                    'transaction_date' => $data->sales->salesDate,
                    'amount'           => $data->sales->bank_paid,
                    'transaction_type' => 'deposit',
                    'form_type'        => 'Sale',
                    'note'             => 'Sales Invoice Payment in Bank',
                    'AddBy'            => $this->session->userdata('userId'),
                    'AddTime'          => date('Y-m-d H:i:s'),
                    'branch_id'        => $this->session->userdata('BRANCHid'),
                    'status'           => 1,
                );

                $this->db->insert('tbl_bank_transactions', $salesBank);
                
            }




            foreach ($data->cart as $cartProduct) {
                $saleDetails = array(
                    'SaleMaster_IDNo'           => $salesId,
                    'Product_IDNo'              => $cartProduct->productId,
                    'SaleDetails_TotalQuantity' => $cartProduct->quantity,
                    'Purchase_Rate'             => $cartProduct->purchaseRate,
                    'SaleDetails_Rate'          => $cartProduct->salesRate,
                    'SaleDetails_Tax'           => $cartProduct->vat,
                    'SaleDetails_TotalAmount'   => $cartProduct->total,
                    'is_service'                => $cartProduct->is_service,
                    'status'                    => $data->sales->status,
                    'AddBy'                     => $this->session->userdata("userId"),
                    'AddTime'                   => date('Y-m-d H:i:s'),
                    'last_update_ip'            => get_client_ip(),
                    'branch_id'                 => $this->session->userdata('BRANCHid')
                );
                $this->db->insert('tbl_saledetails', $saleDetails);
              
            }
            //Send sms
            
          
            if (!empty($data->customer->Customer_Mobile) && preg_match("/(^(01){1}[3456789]{1}(\d){8})$/", trim($data->customer->Customer_Mobile)) == 1) {
                $currentDue = $data->sales->previousDue + ($data->sales->total - $data->sales->paid);
                $customerInfo = $this->db->query("select * from tbl_customer where Customer_SlNo = ?", $customerId)->row();
                    if(!empty($customerInfo)){
                        $sendToName = $customerInfo->owner_name != '' ? $customerInfo->owner_name : $customerInfo->Customer_Name;
                        $recipient = $customerInfo->Customer_Mobile;
                    }else{
                        $sendToName = $data->customer->Customer_Name;
                        $recipient = $data->customer->Customer_Mobile;
                    }
                $currency = $this->session->userdata('Currency_Name');
                $message = "Dear {$sendToName},\nYour invoice No. {$invoice}\nBill is {$currency} {$data->sales->total}\nReceived {$currency} {$data->sales->paid}\nCurrent due {$currency} {$currentDue}";
                $this->sms->sendSms($recipient, $message);
            }

            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'Order Success', 'salesId' => $salesId];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function salesEdit($salesId)
    {
        $data['title'] = "Sales Update";
        $sales = $this->db->query("select * from tbl_salesmaster where SaleMaster_SlNo = ?", $salesId)->row();
        $data['salesId'] = $salesId;
        $data['invoice'] = $sales->SaleMaster_InvoiceNo;
        $data['content'] = $this->load->view('Administrator/sales/product_sales', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function ordersEdit($salesId)
    {
        $data['title'] = "Orders Update";
        $sales = $this->db->query("select * from tbl_salesmaster where SaleMaster_SlNo = ?", $salesId)->row();
        $data['salesId'] = $salesId;
        $data['invoice'] = $sales->SaleMaster_InvoiceNo;
        $data['content'] = $this->load->view('Administrator/sales/product_orders', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getSaleDetails()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->customerId) && $data->customerId != '') {
            $clauses .= " and c.Customer_SlNo = '$data->customerId'";
        }

        if (isset($data->productId) && $data->productId != '') {
            $clauses .= " and p.Product_SlNo = '$data->productId'";
        }

        if (isset($data->categoryId) && $data->categoryId != '') {
            $clauses .= " and pc.ProductCategory_SlNo = '$data->categoryId'";
        }

        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and sm.SaleMaster_SaleDate between '$data->dateFrom' and '$data->dateTo'";
        }
        if (isset($data->sale_type) && $data->sale_type != '') {
            $clauses .= " and sm.sale_type = '$data->sale_type'";
        }

        $saleDetails = $this->db->query("
            select 
                sd.*,
                p.Product_Code,
                p.Product_Name,
                p.ProductCategory_ID,
                pc.ProductCategory_Name,
                sm.SaleMaster_InvoiceNo,
                sm.SaleMaster_SaleDate,
                c.Customer_Code,
                c.Customer_Name
            from tbl_saledetails sd
            join tbl_product p on p.Product_SlNo = sd.Product_IDNo
            join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
            join tbl_salesmaster sm on sm.SaleMaster_SlNo = sd.SaleMaster_IDNo
            join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            where sd.status != 'd'
            and sm.branch_id = ?
            $clauses
        ", $this->sbrunch)->result();

        echo json_encode($saleDetails);
    }

    public function getSalesRecord()
    {
        $data = json_decode($this->input->raw_input_stream);
        $branchId = $this->session->userdata("BRANCHid");
        $clauses = "";
        $status = "a";
        if (isset($data->status) && $data->status != '') {
            $status = $data->status;
        }
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and sm.SaleMaster_SaleDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->userId) && $data->userId != '') {
            $clauses .= " and sm.AddBy = '$data->userId'";
        }
        if (isset($data->sale_type) && $data->sale_type != '') {
            $clauses .= " and sm.sale_type = '$data->sale_type'";
        }

        if (isset($data->customerId) && $data->customerId != '') {
            $clauses .= " and sm.SalseCustomer_IDNo = '$data->customerId'";
        }

        if (isset($data->employeeId) && $data->employeeId != '') {
            $clauses .= " and sm.employee_id = '$data->employeeId'";
        }

        $sales = $this->db->query("
            select 
                sm.*,
                c.Customer_Code,
                c.Customer_Name,
                c.Customer_Mobile,
                c.Customer_Address,
                e.Employee_Name,
                br.Branch_name,
                ua.User_Name as added_by,
                ud.User_Name as deleted_by
            from tbl_salesmaster sm
            left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            left join tbl_employee e on e.Employee_SlNo = sm.employee_id
            left join tbl_branch br on br.branch_id = sm.branch_id
            left join tbl_user ua on ua.User_SlNo = sm.AddBy
            left join tbl_user ud on ud.User_SlNo = sm.DeletedBy
            where sm.branch_id = '$branchId'
            and sm.status = '$status'
            $clauses
            order by sm.SaleMaster_SlNo desc
        ")->result();

        foreach ($sales as $sale) {
            $sale->saleDetails = $this->db->query("
                select 
                    sd.*,
                    p.Product_Name,
                    pc.ProductCategory_Name,
                    u.Unit_Name
                from tbl_saledetails sd
                join tbl_product p on p.Product_SlNo = sd.Product_IDNo
                join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
                left join tbl_unit u on u.Unit_SlNo = p.Unit_ID
                where sd.SaleMaster_IDNo = ?
                and sd.status = '$status'
            ", $sale->SaleMaster_SlNo)->result();

            $sale->saleDetails = array_map(function ($item) {
                $item->stock = $this->mt->productStock($item->Product_IDNo);
                return $item;
            }, $sale->saleDetails);
        }

        echo json_encode($sales);
    }

    public function getSales()
    {
        $data = json_decode($this->input->raw_input_stream);
        $branchId = $this->session->userdata("BRANCHid");
        $clauses = "";
        $limit = "";
        $status = "a";
        if (isset($data->status) && $data->status != '') {
            $status = $data->status;
        }

        if (isset($data->name) && $data->name != '') {
            $clauses .= " or sm.SaleMaster_InvoiceNo like '$data->name%'";
        }
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and sm.SaleMaster_SaleDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->userId) && $data->userId != '') {
            $clauses .= " and sm.AddBy = '$data->userId'";
        }

        if (isset($data->customerId) && $data->customerId != '') {
            $clauses .= " and sm.SalseCustomer_IDNo = '$data->customerId'";
        }

        if (isset($data->sale_type) && $data->sale_type != '') {
            $clauses .= " and sm.sale_type = '$data->sale_type'";
        }

        if (isset($data->employeeId) && $data->employeeId != '') {
            $clauses .= " and sm.employee_id = '$data->employeeId'";
        }

        if (isset($data->customerType) && $data->customerType != '') {
            $clauses .= " and sm.customerType = '$data->customerType'";
        }

        if (isset($data->forSearch) && $data->forSearch != '') {
            $limit .= "limit 20";
        }

        if (isset($data->salesId) && $data->salesId != 0 && $data->salesId != '') {
            $clauses .= " and sm.SaleMaster_SlNo = '$data->salesId'";
            $saleDetails = $this->db->query("
                select 
                    sd.*,
                    p.Product_Code,
                    p.Product_Name,
                    pc.ProductCategory_Name,
                    u.Unit_Name
                from tbl_saledetails sd
                join tbl_product p on p.Product_SlNo = sd.Product_IDNo
                left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
                left join tbl_unit u on u.Unit_SlNo = p.Unit_ID
                where sd.SaleMaster_IDNo = ?
                and sd.status = '$status'
            ", $data->salesId)->result();

            $res['saleDetails'] = $saleDetails;
        }
        $sales = $this->db->query("
            select 
            concat(sm.SaleMaster_InvoiceNo, ' - ', ifnull(c.Customer_Name, sm.customerName)) as invoice_text,
            sm.*,
            ifnull(c.Customer_Code, 'Cash Customer') as Customer_Code,
            ifnull(c.Customer_Name, sm.customerName) as Customer_Name,
            ifnull(c.Customer_Mobile, sm.customerMobile) as Customer_Mobile,
            ifnull(c.Customer_Address, sm.customerAddress) as Customer_Address,
            c.Customer_Type,
            e.Employee_Name,
            e.Employee_ID,
            ba.account_name,
            ba.account_number,
            ba.branch_name,
            ba.bank_name,
            br.Branch_name,
            ua.User_Name as added_by,
            ud.User_Name as deleted_by
            from tbl_salesmaster sm
            left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            left join tbl_employee e on e.Employee_SlNo = sm.employee_id
            left join tbl_bank_accounts ba on ba.account_id = sm.account_id
            left join tbl_user ua on ua.User_SlNo = sm.AddBy
            left join tbl_user ud on ud.User_SlNo = sm.DeletedBy
            left join tbl_branch br on br.branch_id = sm.branch_id
            where sm.branch_id = '$branchId'
            and sm.status = '$status'
            $clauses
            order by sm.SaleMaster_SlNo desc
            $limit
        ")->result();

        $res['sales'] = $sales;

        echo json_encode($res);
    }

    public function updateSales()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);
            $salesId = $data->sales->salesId;
            $customerId = $data->sales->customerId;

            if (isset($data->customer)) {
                $customer = (array)$data->customer;
                unset($customer['Customer_SlNo']);
                unset($customer['display_name']);
                unset($customer['display_text']);
                unset($customer['Customer_Type']);
                $mobile_count = $this->db->query("select * from tbl_customer where Customer_Mobile = ? and branch_id = ?", [$data->customer->Customer_Mobile, $this->session->userdata("BRANCHid")]);
                if (
                    $data->customer->Customer_Mobile != '' &&
                    $data->customer->Customer_Mobile != null &&
                    $mobile_count->num_rows() > 0
                ) {
                    $duplicateCustomer = $mobile_count->row();
                    unset($customer['Customer_Code']);
                    unset($customer['Customer_Type']);
                    if (isset($customer['District_Name'])) {
                        unset($customer['District_Name']);
                        unset($customer['added_by']);
                        unset($customer['deleted_by']);
                    }
                    $customer["UpdateBy"]   = $this->session->userdata("userId");
                    $customer["UpdateTime"] = date("Y-m-d H:i:s");
                    $customer["status"]     = 'a';
                    if ($duplicateCustomer->Customer_Type == 'G') {
                        $customer["Customer_Type"] = 'retail';
                    }
                    $this->db->where('Customer_SlNo', $duplicateCustomer->Customer_SlNo)->update('tbl_customer', $customer);
                    $customerId = $duplicateCustomer->Customer_SlNo;
                } else {
                    if ($data->customer->Customer_Type == 'N') {
                        $customer['Customer_Code'] = $this->mt->generateCustomerCode();
                        $customer['Customer_Type'] = $data->sales->salesType;
                        $customer['status'] = 'a';
                        $customer['AddBy'] = $this->session->userdata("userId");
                        $customer['AddTime'] = date("Y-m-d H:i:s");
                        $customer['last_update_ip'] = get_client_ip();
                        $customer['branch_id'] = $this->session->userdata("BRANCHid");

                        $this->db->insert('tbl_customer', $customer);
                        $customerId = $this->db->insert_id();
                    }
                }
            }

            $sales = array(
                'employee_id'                    => $data->sales->employeeId,
                'SaleMaster_SaleDate'            => $data->sales->salesDate,
                'SaleMaster_SaleType'            => $data->sales->salesType,
                'SaleMaster_TotalSaleAmount'     => $data->sales->total,
                'SaleMaster_TotalDiscountAmount' => $data->sales->discount,
                'SaleMaster_TaxAmount'           => $data->sales->vat,
                'SaleMaster_Freight'             => $data->sales->transportCost,
                'cash_paid'                      => $data->sales->cash_paid,
                'account_id'                    => $data->sales->accountId??null,
                'bank_paid'                      => $data->sales->bank_paid,
                'payment_type'                   => $data->sales->payment_type,
                'SaleMaster_SubTotalAmount'      => $data->sales->subTotal,
                'SaleMaster_PaidAmount'          => $data->sales->paid,
                'SaleMaster_DueAmount'           => $data->sales->due,
                'SaleMaster_Previous_Due'        => $data->sales->previousDue,
                'SaleMaster_Description'         => $data->sales->note,
                "UpdateBy"                       => $this->session->userdata("userId"),
                'UpdateTime'                     => date("Y-m-d H:i:s"),
                'last_update_ip'                 => get_client_ip(),
                "branch_id"                      => $this->session->userdata("BRANCHid")
            );
            if ($data->customer->Customer_Type == 'G') {
                $sales['SalseCustomer_IDNo']    = Null;
                $sales['customerType']    = "G";
                $sales['customerName']    = $data->customer->Customer_Name;
                $sales['customerMobile']  = $data->customer->Customer_Mobile;
                $sales['customerAddress'] = $data->customer->Customer_Address;
            } else {
                $sales['customerType'] = $data->customer->Customer_Type == 'N' ? "retail" : 'retail';
                $sales['SalseCustomer_IDNo'] = $customerId;
                $sales['customerName']    = NULL;
                $sales['customerMobile']  = NULL;
                $sales['customerAddress'] = NULL;
            }

            $this->db->where('SaleMaster_SlNo', $salesId);
            $this->db->update('tbl_salesmaster', $sales);

            $currentSaleDetails = $this->db->query("select * from tbl_saledetails where SaleMaster_IDNo = ?", $salesId)->result();
            $this->db->query("delete from tbl_saledetails where SaleMaster_IDNo = ?", $salesId);



            if ($data->sales->payment_type == 'Bank' && $data->sales->accountId != 0) {
               
                $query = $this->db->query("select * from tbl_bank_transactions where sales_id = ? AND account_id = ? ", [$salesId, $data->sales->accountId]);

                if ($query->num_rows() > 0) {
                    $bank = $query->row();
                    $dataBank = array(
                        'amount' => $data->sales->bank_paid,
                        'note' => 'Sales Invoice Payment in Bank',
                        'status' => 1,
                    );
                    $this->db->where('transaction_id', $bank->transaction_id)->update('tbl_bank_transactions', $dataBank);
                } else {

                    $tcount = $this->db->query("select transaction_id from tbl_bank_transactions where sales_id = ? ", $salesId);

                    if ($tcount->num_rows() > 0) {
                        $oldTrans = $tcount->row();
                        $this->db->query("DELETE FROM tbl_bank_transactions WHERE transaction_id = ?", $oldTrans->transaction_id);
                    }

                    $salesBank = array(
                        'sales_id'         => $salesId,
                        'account_id'       => $data->sales->accountId,
                        'transaction_date' => $data->sales->salesDate,
                        'amount'           => $data->sales->bank_paid,
                        'transaction_type' => 'deposit',
                        'form_type'        => 'Sale',
                        'note'             => 'Sales Invoice Payment in Bank',
                        'saved_by'         => $this->session->userdata('userId'),
                        'saved_datetime'   => date('Y-m-d H:i:s'),
                        'branch_id'        => $this->session->userdata('BRANCHid'),
                        'status'           => 1,
                    );
    
                    $this->db->insert('tbl_bank_transactions', $salesBank);
                }
            } else {
                $count = $this->db->query("select * from tbl_bank_transactions where sales_id = ?",  $salesId);
                if ($count->num_rows() > 0) {
                    $this->db->query("update tbl_bank_transactions set status = 0 where sales_id = ?", $salesId);
                }
            }


            foreach ($currentSaleDetails as $product) {
                if ($product->is_service == 'false') {
                    $this->db->query("
                        update tbl_currentinventory 
                        set sales_quantity = sales_quantity - ? 
                        where product_id = ?
                        and branch_id = ?
                    ", [$product->SaleDetails_TotalQuantity, $product->Product_IDNo, $this->session->userdata('BRANCHid')]);
                }
            }
            
            // check stock
            foreach ($data->cart as $cartProduct) {
                $checkStock = $this->mt->productStock($cartProduct->productId);
                if (($cartProduct->quantity > $checkStock) && $cartProduct->is_service == 'false') {
                    $res = ['success' => false, 'message' => "({$cartProduct->name} - {$cartProduct->productCode}) stock unavailable"];
                    echo json_encode($res);
                    exit;
                }
            }

            

            foreach ($data->cart as $cartProduct) {
                $saleDetails = array(
                    'SaleMaster_IDNo'           => $salesId,
                    'Product_IDNo'              => $cartProduct->productId,
                    'SaleDetails_TotalQuantity' => $cartProduct->quantity,
                    'Purchase_Rate'             => $cartProduct->purchaseRate,
                    'SaleDetails_Rate'          => $cartProduct->salesRate,
                    'SaleDetails_Tax'           => $cartProduct->vat,
                    'SaleDetails_TotalAmount'   => $cartProduct->total,
                    'is_service'                => $cartProduct->is_service,
                    'discountAmount'            => $cartProduct->discountAmount, 
                    'discountPercent'           => $cartProduct->discountPercent, 
                    'note'                      => $cartProduct->note ?? null,
                    'status'                    => 'a',
                    'UpdateBy'                  => $this->session->userdata("userId"),
                    'UpdateTime'                => date('Y-m-d H:i:s'),
                    'last_update_ip'            => get_client_ip(),
                    'branch_id'                 => $this->session->userdata("BRANCHid")
                );

                $this->db->insert('tbl_saledetails', $saleDetails);
                if ($cartProduct->is_service == 'false') {
                    $this->db->query("
                        update tbl_currentinventory 
                        set sales_quantity = sales_quantity + ? 
                        where product_id = ?
                        and branch_id = ?
                    ", [$cartProduct->quantity, $cartProduct->productId, $this->session->userdata('BRANCHid')]);
                }
            }
            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'Sales Updated', 'salesId' => $salesId];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }
    public function updateOrder()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);
            $salesId = $data->sales->salesId;
            $customerId = $data->sales->customerId;

            // var_dump();

            if (isset($data->customer)) {
                $customer = (array)$data->customer;
                unset($customer['Customer_SlNo']);
                unset($customer['display_text']);
                unset($customer['display_name']);
                unset($customer['Customer_Type']);
                $mobile_count = $this->db->query("select * from tbl_customer where Customer_Mobile = ? and branch_id = ?", [$data->customer->Customer_Mobile, $this->session->userdata("BRANCHid")]);
                if (
                    $data->customer->Customer_Mobile != '' &&
                    $data->customer->Customer_Mobile != null &&
                    $mobile_count->num_rows() > 0
                ) {
                    $duplicateCustomer = $mobile_count->row();
                    unset($customer['Customer_Code']);
                    unset($customer['Customer_Type']);
                    if (isset($customer['District_Name'])) {
                        unset($customer['District_Name']);
                        unset($customer['added_by']);
                        unset($customer['deleted_by']);
                    }
                    $customer["UpdateBy"]   = $this->session->userdata("userId");
                    $customer["UpdateTime"] = date("Y-m-d H:i:s");
                    $customer["status"]     = 'a';
                    if ($duplicateCustomer->Customer_Type == 'G') {
                        $customer["Customer_Type"] = 'retail';
                    }
                    $this->db->where('Customer_SlNo', $duplicateCustomer->Customer_SlNo)->update('tbl_customer', $customer);
                    $customerId = $duplicateCustomer->Customer_SlNo;
                } else {
                    if ($data->customer->Customer_Type == 'N') {
                        $customer['Customer_Code'] = $this->mt->generateCustomerCode();
                        $customer['Customer_Type'] = $data->sales->salesType;
                        $customer['status'] = 'a';
                        $customer['AddBy'] = $this->session->userdata("userId");
                        $customer['AddTime'] = date("Y-m-d H:i:s");
                        $customer['last_update_ip'] = get_client_ip();
                        $customer['branch_id'] = $this->session->userdata("BRANCHid");

                        $this->db->insert('tbl_customer', $customer);
                        $customerId = $this->db->insert_id();
                    }
                }
            }

            $sales = array(
                'employee_id'                    => $data->sales->employeeId,
                'SaleMaster_SaleDate'            => $data->sales->salesDate,
                'SaleMaster_SaleType'            => $data->sales->salesType,
                'SaleMaster_TotalSaleAmount'     => $data->sales->total,
                'SaleMaster_TotalDiscountAmount' => $data->sales->discount,
                'SaleMaster_TaxAmount'           => $data->sales->vat,
                'SaleMaster_Freight'             => $data->sales->transportCost,
                'cash_paid'                      => $data->sales->cash_paid,
                'account_id'                     => $data->sales->accountId??null,
                'bank_paid'                      => $data->sales->bank_paid,
                'payment_type'                   => $data->sales->payment_type,
                'sale_type'                      => $data->sales->sale_type,
                'SaleMaster_SubTotalAmount'      => $data->sales->subTotal,
                'SaleMaster_PaidAmount'          => $data->sales->paid,
                'SaleMaster_DueAmount'           => $data->sales->due,
                'SaleMaster_Previous_Due'        => $data->sales->previousDue,
                'SaleMaster_Description'         => $data->sales->note,
                "UpdateBy"                       => $this->session->userdata("userId"),
                'UpdateTime'                     => date("Y-m-d H:i:s"),
                'last_update_ip'                 => get_client_ip(),
                "branch_id"                      => $this->session->userdata("BRANCHid")
            );
            if ($data->customer->Customer_Type == 'G') {
                $sales['SalseCustomer_IDNo']    = Null;
                $sales['customerType']    = "G";
                $sales['customerName']    = $data->customer->Customer_Name;
                $sales['customerMobile']  = $data->customer->Customer_Mobile;
                $sales['customerAddress'] = $data->customer->Customer_Address;
            } else {
                $sales['customerType'] = $data->customer->Customer_Type == 'N' ? "retail" : 'retail';
                $sales['SalseCustomer_IDNo'] = $customerId;
                $sales['customerName']    = NULL;
                $sales['customerMobile']  = NULL;
                $sales['customerAddress'] = NULL;
            }

            $this->db->where('SaleMaster_SlNo', $salesId);
            $this->db->update('tbl_salesmaster', $sales);

            $this->db->query("delete from tbl_saledetails where SaleMaster_IDNo = ?", $salesId);



            if ($data->sales->payment_type == 'Bank' && $data->sales->accountId != 0) {
               
                $query = $this->db->query("select * from tbl_bank_transactions where sales_id = ? AND account_id = ? ", [$salesId, $data->sales->accountId]);

                if ($query->num_rows() > 0) {
                    $bank = $query->row();
                    $dataBank = array(
                        'UpdateBy'   => $this->session->userdata('userId'),
                        'UpdateTime' => date('Y-m-d H:i:s'),
                        'amount'     => $data->sales->bank_paid,
                        'note'       => 'Sales Invoice Payment in Bank'
                    );
                    $this->db->where('transaction_id', $bank->transaction_id)->update('tbl_bank_transactions', $dataBank);
                } else {

                    $tcount = $this->db->query("select transaction_id from tbl_bank_transactions where sales_id = ? ", $salesId);

                    if ($tcount->num_rows() > 0) {
                        $oldTrans = $tcount->row();
                        $this->db->query("DELETE FROM tbl_bank_transactions WHERE transaction_id = ?", $oldTrans->transaction_id);
                    }

                    $salesBank = array(
                        'sales_id'         => $salesId,
                        'account_id'       => $data->sales->accountId,
                        'transaction_date' => $data->sales->salesDate,
                        'amount'           => $data->sales->bank_paid,
                        'transaction_type' => 'deposit',
                        'form_type'        => 'Sale',
                        'note'             => 'Sales Invoice Payment in Bank',
                        'UpdateBy'         => $this->session->userdata('userId'),
                        'UpdateTime'       => date('Y-m-d H:i:s'),
                        'branch_id'        => $this->session->userdata('BRANCHid')
                    );
    
                    $this->db->insert('tbl_bank_transactions', $salesBank);
                }
            } else {
                $count = $this->db->query("select * from tbl_bank_transactions where sales_id = ?",  $salesId);
                if ($count->num_rows() > 0) {
                    $this->db->query("update tbl_bank_transactions set status = 0 where sales_id = ?", $salesId);
                }
            }


            foreach ($data->cart as $cartProduct) {
                $saleDetails = array(
                    'SaleMaster_IDNo'           => $salesId,
                    'Product_IDNo'              => $cartProduct->productId,
                    'SaleDetails_TotalQuantity' => $cartProduct->quantity,
                    'Purchase_Rate'             => $cartProduct->purchaseRate,
                    'SaleDetails_Rate'          => $cartProduct->salesRate,
                    'SaleDetails_Tax'           => $cartProduct->vat,
                    'SaleDetails_TotalAmount'   => $cartProduct->total,
                    'status'                    => $data->sales->status,
                    'is_service'                => $cartProduct->is_service,
                    'AddBy'                     => $this->session->userdata("userId"),
                    'AddTime'                   => date('Y-m-d H:i:s'),
                    'UpdateBy'                  => $this->session->userdata("userId"),
                    'UpdateTime'                => date('Y-m-d H:i:s'),
                    'last_update_ip'            => get_client_ip(),
                    'branch_id'                 => $this->session->userdata("BRANCHid")
                );
                $this->db->insert('tbl_saledetails', $saleDetails);
            }
            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'Order Updated', 'salesId' => $salesId];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function getSaleDetailsForReturn()
    {
        $data = json_decode($this->input->raw_input_stream);
        $saleDetails = $this->db->query("
            select
                sd.*,
                sd.SaleDetails_Rate as return_rate,
                p.Product_Name,
                p.Product_Code,
                pc.ProductCategory_Name,
                (
                    select ifnull(sum(srd.SaleReturnDetails_ReturnQuantity), 0)
                    from tbl_salereturndetails srd
                    join tbl_salereturn sr on sr.SaleReturn_SlNo = srd.SaleReturn_IdNo
                    where sr.status = 'a'
                    and srd.SaleReturnDetailsProduct_SlNo = sd.Product_IDNo
                    and sr.SaleMaster_InvoiceNo = sm.SaleMaster_InvoiceNo
                ) as returned_quantity,
                (
                    select ifnull(sum(srd.SaleReturnDetails_ReturnAmount), 0)
                    from tbl_salereturndetails srd
                    join tbl_salereturn sr on sr.SaleReturn_SlNo = srd.SaleReturn_IdNo
                    where sr.status = 'a'
                    and srd.SaleReturnDetailsProduct_SlNo = sd.Product_IDNo
                    and sr.SaleMaster_InvoiceNo = sm.SaleMaster_InvoiceNo
                ) as returned_amount
            from tbl_saledetails sd
            join tbl_salesmaster sm on sm.SaleMaster_SlNo = sd.SaleMaster_IDNo
            join tbl_product p on p.Product_SlNo = sd.Product_IDNo
            left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
            where sm.SaleMaster_SlNo = ?
        ", $data->salesId)->result();

        echo json_encode($saleDetails);
    }

    public function addSalesReturn()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();

            $data = json_decode($this->input->raw_input_stream);
            $salesReturn = array(
                'SaleMaster_InvoiceNo'    => $data->invoice->SaleMaster_InvoiceNo,
                'SaleReturn_ReturnDate'   => $data->salesReturn->returnDate,
                'SaleReturn_ReturnAmount' => $data->salesReturn->total,
                'SaleReturn_Description'  => $data->salesReturn->note,
                'status'                  => 'a',
                'AddBy'                   => $this->session->userdata("userId"),
                'AddTime'                 => date('Y-m-d H:i:s'),
                'last_update_ip'          => get_client_ip(),
                'branch_id'               => $this->session->userdata("BRANCHid")
            );

            $this->db->insert('tbl_salereturn', $salesReturn);
            $salesReturnId = $this->db->insert_id();

            $totalReturnAmount = 0;
            foreach ($data->cart as $product) {
                $returnDetails = array(
                    'SaleReturn_IdNo'                  => $salesReturnId,
                    'SaleReturnDetailsProduct_SlNo'    => $product->Product_IDNo,
                    'SaleReturnDetails_ReturnQuantity' => $product->return_quantity,
                    'SaleReturnDetails_ReturnAmount'   => $product->return_amount,
                    'status'                           => 'a',
                    'AddBy'                            => $this->session->userdata("userId"),
                    'AddTime'                          => date('Y-m-d H:i:s'),
                    'last_update_ip'                   => get_client_ip(),
                    'branch_id'                        => $this->session->userdata("BRANCHid")
                );

                $this->db->insert('tbl_salereturndetails', $returnDetails);

                $totalReturnAmount += $product->return_amount;

                $this->db->query("
                    update tbl_currentinventory 
                    set sales_return_quantity = sales_return_quantity + ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$product->return_quantity, $product->Product_IDNo, $this->session->userdata('BRANCHid')]);
            }

            $customerInfo = $this->db->query("select * from tbl_customer where Customer_SlNo = ?", $data->invoice->SalseCustomer_IDNo)->row();
            if (empty($customerInfo)) {
                $customerPayment = array(
                    'CPayment_date'            => $data->salesReturn->returnDate,
                    'CPayment_invoice'         => $data->invoice->SaleMaster_InvoiceNo,
                    'CPayment_customerID'      => $data->invoice->customerType == 'G' ? NULL : $data->invoice->SalseCustomer_IDNo,
                    'CPayment_TransactionType' => 'CP',
                    'CPayment_Paymentby'       => 'cash',
                    'CPayment_amount'          => $totalReturnAmount,
                    'CPayment_previous_due'    => 0,
                    'status'                   => 'a',
                    'AddBy'                    => $this->session->userdata("userId"),
                    'AddTime'                  => date('Y-m-d H:i:s'),
                    'last_update_ip'           => get_client_ip(),
                    'branch_id'                => $this->session->userdata("BRANCHid"),
                );

                $this->db->insert('tbl_customer_payment', $customerPayment);
            }

            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'Return Success', 'id' => $salesReturnId];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateSalesReturn()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();

            $data = json_decode($this->input->raw_input_stream);
            $salesReturnId = $data->salesReturn->returnId;

            $oldReturn = $this->db->query("select * from tbl_salereturn where SaleReturn_SlNo = ?", $salesReturnId)->row();
            $oldDetails = $this->db->query("select * from tbl_salereturndetails sr where sr.SaleReturn_IdNo = ?", $salesReturnId)->result();

            $salesReturn = array(
                'SaleMaster_InvoiceNo' => $data->invoice->SaleMaster_InvoiceNo,
                'SaleReturn_ReturnDate' => $data->salesReturn->returnDate,
                'SaleReturn_ReturnAmount' => $data->salesReturn->total,
                'SaleReturn_Description' => $data->salesReturn->note,
                'status' => 'a',
                'UpdateBy' => $this->session->userdata("userId"),
                'UpdateTime' => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
                'branch_id' => $this->session->userdata("BRANCHid")
            );

            $this->db->where('SaleReturn_SlNo', $salesReturnId)->update('tbl_salereturn', $salesReturn);

            foreach ($oldDetails as $product) {
                $this->db->query("
                    update tbl_currentinventory 
                    set sales_return_quantity = sales_return_quantity - ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$product->SaleReturnDetails_ReturnQuantity, $product->SaleReturnDetailsProduct_SlNo, $this->session->userdata('BRANCHid')]);
            }

            $this->db->query("delete from tbl_salereturndetails where SaleReturn_IdNo = ?", $salesReturnId);

            $totalReturnAmount = 0;
            foreach ($data->cart as $product) {
                $returnDetails = array(
                    'SaleReturn_IdNo' => $salesReturnId,
                    'SaleReturnDetailsProduct_SlNo' => $product->Product_IDNo,
                    'SaleReturnDetails_ReturnQuantity' => $product->return_quantity,
                    'SaleReturnDetails_ReturnAmount' => $product->return_amount,
                    'status' => 'a',
                    'UpdateBy' => $this->session->userdata("userId"),
                    'UpdateTime' => date('Y-m-d H:i:s'),
                    'last_update_ip' => get_client_ip(),
                    'branch_id' => $this->session->userdata("BRANCHid")
                );

                $this->db->insert('tbl_salereturndetails', $returnDetails);

                $totalReturnAmount += $product->return_amount;

                $this->db->query("
                    update tbl_currentinventory 
                    set sales_return_quantity = sales_return_quantity + ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$product->return_quantity, $product->Product_IDNo, $this->session->userdata('BRANCHid')]);
            }

            $customerInfo = $this->db->query("select * from tbl_customer where Customer_SlNo = ?", $data->invoice->SalseCustomer_IDNo)->row();
            if (empty($customerInfo)) {
                $this->db->query("
                    delete from tbl_customer_payment 
                    where CPayment_invoice = ?
                    and CPayment_amount = ?
                    and CPayment_customerID is null
                    limit 1
                ", [
                    $data->invoice->SaleMaster_InvoiceNo,
                    $oldReturn->SaleReturn_ReturnAmount
                ]);

                $customerPayment = array(
                    'CPayment_date' => $data->salesReturn->returnDate,
                    'CPayment_invoice' => $data->invoice->SaleMaster_InvoiceNo,
                    'CPayment_customerID' => NULL,
                    'CPayment_TransactionType' => 'CP',
                    'CPayment_amount' => $totalReturnAmount,
                    'CPayment_Paymentby' => 'cash',
                    'CPayment_previous_due' => 0,
                    'status' => 'a',
                    'AddBy' => $this->session->userdata("userId"),
                    'AddTime' => date('Y-m-d H:i:s'),
                    'last_update_ip' => get_client_ip(),
                    'branch_id' => $this->session->userdata("BRANCHid")
                );

                $this->db->insert('tbl_customer_payment', $customerPayment);
            }

            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'Return Updated', 'id' => $salesReturnId];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function deleteSaleReturn()
    {
        $data = json_decode($this->input->raw_input_stream);

        $res = ['success' => false, 'message' => ''];

        try {
            $data = json_decode($this->input->raw_input_stream);

            $oldReturn = $this->db->query("
                select 
                    sr.*,
                    c.Customer_SlNo,
                    c.Customer_Code,
                    c.Customer_Name,
                    c.Customer_Type,
                    sm.customerType
                from tbl_salereturn sr
                join tbl_salesmaster sm on sm.SaleMaster_InvoiceNo = sr.SaleMaster_InvoiceNo
                left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
                where sr.SaleReturn_SlNo = ?
            ", $data->id)->row();

            $returnDetails = $this->db->query("select * from tbl_salereturndetails srd where srd.SaleReturn_IdNo = ?", $data->id)->result();

            if ($oldReturn->customerType == 'G') {
                $this->db->query("
                    delete from tbl_customer_payment 
                    where CPayment_invoice = ? 
                    and CPayment_customerID = ?
                    and CPayment_amount = ?
                    limit 1
                ", [
                    $oldReturn->SaleMaster_InvoiceNo,
                    $oldReturn->Customer_SlNo,
                    $oldReturn->SaleReturn_ReturnAmount
                ]);
            }

            foreach ($returnDetails as $product) {
                $this->db->query("
                    update tbl_currentinventory set 
                    sales_return_quantity = sales_return_quantity - ? 
                    where product_id = ? 
                    and branch_id = ?
                ", [$product->SaleReturnDetails_ReturnQuantity, $product->SaleReturnDetailsProduct_SlNo, $this->sbrunch]);
            }
            $saleReturn = array(
                'status' => 'd',
                'DeletedBy' => $this->session->userdata('userId'),
                'DeletedTime' => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
            );

            $this->db->set($saleReturn)->where('SaleReturn_IdNo', $data->id)->update('tbl_salereturndetails');
            $this->db->set($saleReturn)->where('SaleReturn_SlNo', $data->id)->update('tbl_salereturn');

            $res = ['success' => true, 'message' => 'Sale return deleted'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function getSaleReturns()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if ((isset($data->fromDate) && $data->fromDate != '') && (isset($data->toDate) && $data->toDate != '')) {
            $clauses .= " and sr.SaleReturn_ReturnDate between '$data->fromDate' and '$data->toDate'";
        }

        if (isset($data->customerId) && $data->customerId != '') {
            $clauses .= " and sm.SalseCustomer_IDNo = '$data->customerId'";
        }

        if (isset($data->id) && $data->id != '') {
            $clauses .= " and sr.SaleReturn_SlNo = '$data->id'";

            $res['returnDetails'] = $this->db->query("
                SELECT
                    srd.*,
                    p.Product_Code,
                    p.Product_Name
                from tbl_salereturndetails srd
                join tbl_product p on p.Product_SlNo = srd.SaleReturnDetailsProduct_SlNo
                where srd.SaleReturn_IdNo = ?
            ", $data->id)->result();
        }

        $res['returns'] = $this->db->query("
            select  
                sr.*,
                c.Customer_SlNo,
                c.Customer_Code,
                ifnull(c.Customer_Name, sm.customerName) as Customer_Name,
                ifnull(c.Customer_Address, sm.customerAddress) as Customer_Address,
                ifnull(c.Customer_Mobile, sm.customerMobile) as Customer_Mobile,
                ifnull(c.Customer_Type, sm.customerType) as Customer_Type,
                c.owner_name,
                sm.SaleMaster_TotalDiscountAmount,
                sm.SaleMaster_SlNo
            from tbl_salereturn sr
            join tbl_salesmaster sm on sm.SaleMaster_InvoiceNo = sr.SaleMaster_InvoiceNo
            left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            where sr.status = 'a'
            and sr.branch_id= '$this->sbrunch'
            $clauses
        ")->result();

        echo json_encode($res);
    }

    public function getSaleReturnDetails()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and sr.SaleReturn_ReturnDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->customerId) && $data->customerId != '') {
            $clauses .= " and sm.SalseCustomer_IDNo = '$data->customerId'";
        }

        if (isset($data->productId) && $data->productId != '') {
            $clauses .= " and srd.SaleReturnDetailsProduct_SlNo = '$data->productId'";
        }

        $returnDetails = $this->db->query("
            select
                srd.*,
                p.Product_Code,
                p.Product_Name,
                sr.SaleMaster_InvoiceNo,
                sr.SaleReturn_ReturnDate,
                sr.SaleReturn_Description,
                sm.SalseCustomer_IDNo,
                ifnull(c.Customer_Code, 'Cash Customer') as Customer_Code,
                ifnull(c.Customer_Name, sm.customerName) as Customer_Name
            from tbl_salereturndetails srd
            join tbl_product p on p.Product_SlNo = srd.SaleReturnDetailsProduct_SlNo
            join tbl_salereturn sr on sr.SaleReturn_SlNo = srd.SaleReturn_IdNo
            join tbl_salesmaster sm on sm.SaleMaster_InvoiceNo = sr.SaleMaster_InvoiceNo
            left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            where sr.branch_id= ?
            $clauses
        ", $this->session->userdata('BRANCHid'))->result();

        echo json_encode($returnDetails);
    }

    public function saleReturnInvoice($id)
    {
        $data['title'] = "Sale return Invoice";
        $data['id'] = $id;
        $data['content'] = $this->load->view('Administrator/sales/sale_return_invoice', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    function salesreturn()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }

        $data['returnId'] = 0;
        $data['title'] = " Sales Return";
        $data['content'] = $this->load->view('Administrator/sales/salseReturn', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    function salesReturnEdit($id)
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }

        $data['returnId'] = $id;
        $data['title'] = " Sales Return";
        $data['content'] = $this->load->view('Administrator/sales/salseReturn', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    function salesreturnSearch()
    {
        $invoice = $this->input->post('invoiceno');
        $sql = $this->db->query("SELECT * FROM tbl_salesmaster WHERE SaleMaster_SlNo = '$invoice'");
        $row = $sql->row();
        $data['proID'] = $row->SaleMaster_SlNo;
        $data['invoices'] = $row->SaleMaster_InvoiceNo;
        $da['Store'] = $row->SaleMaster_SaleType;
        $this->session->set_userdata($da);
        $this->load->view('Administrator/sales/salesReturnList', $data);
    }

    public function sales_invoice()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Sales Invoice";
        $data['content'] = $this->load->view('Administrator/sales/sales_invoice', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    
    public function sales_invoice_search()
    {
        $id = $this->input->post('SaleMasteriD');
        $datas['SalesID'] = $SalesID = $this->input->post('SaleMasteriD');
        $this->session->set_userdata('SalesID', $SalesID);


        $this->db->select('tbl_salesmaster.*, tbl_salesmaster.AddBy as served, tbl_customer.*,genaral_customer_info.*');
        $this->db->from('tbl_salesmaster');
        $this->db->join('tbl_customer', 'tbl_salesmaster.SalseCustomer_IDNo =tbl_customer.Customer_SlNo', 'left');
        $this->db->join('genaral_customer_info', 'tbl_salesmaster.SaleMaster_SlNo =genaral_customer_info.G_Sale_Mastar_SiNO', 'left');
        $datas['selse'] = $this->db->where('tbl_salesmaster.SaleMaster_SlNo', $SalesID)->get()->row();



        $this->load->view('Administrator/sales/sales_invoice_search', $datas);
    }
    function sales_record()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Sales Record";
        $data['content'] = $this->load->view('Administrator/sales/sales_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    function orders_record()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Orders Record";
        $data['content'] = $this->load->view('Administrator/sales/orders_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function orderInvoicePrint($saleId)
    {
        $data['title'] = "Order Invoice";
        $data['salesId'] = $saleId;
        $data['content'] = $this->load->view('Administrator/sales/orderAndreport', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function saleInvoicePrint($saleId)
    {
        $data['title'] = "Sales Invoice";
        $data['salesId'] = $saleId;
        $data['content'] = $this->load->view('Administrator/sales/sellAndreport', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    function return_list()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Sales Return List";
        $data['content'] = $this->load->view('Administrator/sales/sales_return_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    function saleReturnDetails()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Sales Return Details";
        $data['content'] = $this->load->view('Administrator/sales/sale_return_details', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    function sales_return_record()
    {
        $datas['searchtype'] = $searchtype = $this->input->post('searchtype');
        $datas['productID'] = $productID = $this->input->post('productID');
        $datas['startdate'] = $startdate = $this->input->post('startdate');
        $datas['enddate'] = $enddate = $this->input->post('enddate');
        $this->session->set_userdata($datas);
        //echo "<pre>";print_r($datas);exit;
        $this->load->view('Administrator/sales/return_list', $datas);
    }
    function craditlimit()
    {
        $cid = $this->input->post('custID');
        $sql = mysql_query("SELECT *  FROM tbl_customer_payment  where CPayment_customerID = '$cid' ");
        $sell = '';
        $paid = '';
        while ($rox = mysql_fetch_array($sql)) {
            $paid = $paid + $rox['CPayment_amount'];
        }
        $sqlx = mysql_query("SELECT * FROM tbl_salesmaster  where SalseCustomer_IDNo = '$cid' ");
        while ($rox = mysql_fetch_array($sqlx)) {
            $sell = $sell + $rox['SaleMaster_SubTotalAmount'];
        }

        //echo  $sell.'<br>';echo $paid;
        $data['totaldue'] = $sell - $paid;
        $sqll = mysql_query("SELECT * FROM tbl_customer WHERE Customer_SlNo = '$cid'");
        $rol = mysql_fetch_array($sqll);
        $data['craditlimit'] = $rol['Customer_Credit_Limit'];
        $this->load->view('Administrator/sales/craditlimit', $data);
    }

    function customerwise_sales()
    {
        $data['title'] = "Customerwise Sales";
        $data['content'] = $this->load->view('Administrator/sales/customerwise_sales', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }



    /*Delete Sales Record*/
    public function  deleteSales()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $saleId = $data->saleId;

            $sale = $this->db->select('*')->where('SaleMaster_SlNo', $saleId)->get('tbl_salesmaster')->row();
            if ($sale->status != 'a') {
                $res = ['success' => false, 'message' => 'Sale not found'];
                echo json_encode($res);
                exit;
            }

            $returnCount = $this->db->query("select * from tbl_salereturn sr where sr.SaleMaster_InvoiceNo = ? and sr.status = 'a'", $sale->SaleMaster_InvoiceNo)->num_rows();

            if ($returnCount != 0) {
                $res = ['success' => false, 'message' => 'Unable to delete. Sale return found'];
                echo json_encode($res);
                exit;
            }

            /*Get Sale Details Data*/
            $saleDetails = $this->db->select('Product_IDNo, SaleDetails_TotalQuantity')->where('SaleMaster_IDNo', $saleId)->get('tbl_saledetails')->result();

            foreach ($saleDetails as $detail) {
                /*Get Product Current Quantity*/
                $totalQty = $this->db->where(['product_id' => $detail->Product_IDNo, 'branch_id' => $sale->branch_id])->get('tbl_currentinventory')->row()->sales_quantity;

                /* Subtract Product Quantity form  Current Quantity  */
                $newQty = $totalQty - $detail->SaleDetails_TotalQuantity;

                /*Update Sales Inventory*/
                $this->db->set('sales_quantity', $newQty)->where(['product_id' => $detail->Product_IDNo, 'branch_id' => $sale->branch_id])->update('tbl_currentinventory');
            }
            $sale = array(
                'status' => 'd',
                'DeletedBy' => $this->session->userdata('userId'),
                'DeletedTime' => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
            );
            /*Delete Sale Details*/
            $this->db->set($sale)->where('SaleMaster_IDNo', $saleId)->update('tbl_saledetails');
            /*Delete Sale Master Data*/
            $this->db->set($sale)->where('SaleMaster_SlNo', $saleId)->update('tbl_salesmaster');

            $accountcount = $this->db->query("select * from tbl_bank_transactions where sales_id = ?",  $saleId);
                if ($accountcount->num_rows() > 0) {
                    $this->db->query("update tbl_bank_transactions set status = 0 where sales_id = ?", $saleId);
                }

            $res = ['success' => true, 'message' => 'Sale deleted'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }
    /*Delete Order Record*/
    public function  deleteOrders()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $saleId = $data->saleId;

            $sale = $this->db->select('*')->where('SaleMaster_SlNo', $saleId)->get('tbl_salesmaster')->row();
            if ($sale->status != 'p') {
                $res = ['success' => false, 'message' => 'Order not found'];
                echo json_encode($res);
                exit;
            }
         
            $sale = array(
                'status' => 'd',
                'DeletedBy' => $this->session->userdata('userId'),
                'DeletedTime' => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
            );
            /*Delete Sale Details*/
            $this->db->set($sale)->where('SaleMaster_IDNo', $saleId)->update('tbl_saledetails');
            /*Delete Sale Master Data*/
            $this->db->set($sale)->where('SaleMaster_SlNo', $saleId)->update('tbl_salesmaster');

            $accountcount = $this->db->query("select * from tbl_bank_transactions where sales_id = ?",  $saleId);
                if ($accountcount->num_rows() > 0) {
                    $this->db->query("update tbl_bank_transactions set status = 0 where sales_id = ?", $saleId);
                }

            $res = ['success' => true, 'message' => 'Sale deleted'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }
    public function  ordersToSale()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $saleId = $data->saleId;

         
            $saleStockCheck = $this->db->query("
                select 
                    sd.*,
                    p.Product_Code,
                    p.Product_Name
                from tbl_saledetails sd
                join tbl_product p on p.Product_SlNo = sd.Product_IDNo
                where sd.SaleMaster_IDNo = ?
            ", $saleId)->result();

            foreach ($saleStockCheck as $productId) {
                $checkStock = $this->mt->productStock($productId->Product_IDNo);
                if (($productId->SaleDetails_TotalQuantity > $checkStock) && $productId->is_service == 'false') {
                    $res = ['success' => false, 'message' => "({$productId->Product_Name} - {$productId->Product_Code}) stock unavailable"];
                    echo json_encode($res);
                    exit;
                }

                

            }


         
            $saleMaster = array(
                'sale_type' => 'Sale',
                'status' => 'a',
                'DeletedBy' => $this->session->userdata('userId'),
                'DeletedTime' => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
            );
            $saleDetails = array(
                'status'         => 'a',
                'UpdateBy'       => $this->session->userdata('userId'),
                'UpdateTime'     => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
            );
            /*Delete Sale Details*/
            $this->db->set($saleDetails)->where('SaleMaster_IDNo', $saleId)->update('tbl_saledetails');
            /*Delete Sale Master Data*/
            $this->db->set($saleMaster)->where('SaleMaster_SlNo', $saleId)->update('tbl_salesmaster');


            foreach ($saleStockCheck as $product) {
                if ($product->is_service == 'false') {
                    //update stock
                    $this->db->query("
                        update tbl_currentinventory 
                        set sales_quantity = sales_quantity + ? 
                        where product_id = ?
                        and branch_id = ?
                    ", [$product->SaleDetails_TotalQuantity, $product->Product_IDNo, $this->session->userdata('BRANCHid')]);
                }
            }

            $accountcount = $this->db->query("select * from tbl_bank_transactions where sales_id = ?",  $saleId);
                if ($accountcount->num_rows() > 0) {
                    $this->db->query("update tbl_bank_transactions set status = 0 where sales_id = ?", $saleId);
                }

            $res = ['success' => true, 'message' => 'Sale deleted'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    function profitLoss()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Profit & Loss ";
        $data['products'] = $this->Product_model->products_by_brunch();
        $data['content'] = $this->load->view('Administrator/sales/profit_loss', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getProfitLoss()
    {
        $data = json_decode($this->input->raw_input_stream);

        $customerClause = "";
        if ($data->customer != null && $data->customer != '') {
            $customerClause = " and sm.SalseCustomer_IDNo = '$data->customer'";
        }

        $dateClause = "";
        if (($data->dateFrom != null && $data->dateFrom != '') && ($data->dateTo != null && $data->dateTo != '')) {
            $dateClause = " and sm.SaleMaster_SaleDate between '$data->dateFrom' and '$data->dateTo'";
        }


        $sales = $this->db->query("
            select 
                sm.*,
                c.Customer_Code,
                c.Customer_Name,
                c.Customer_Mobile
            from tbl_salesmaster sm
            left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            where sm.branch_id = ? 
            and sm.status = 'a'
            $customerClause $dateClause
        ", $this->session->userdata('BRANCHid'))->result();

        foreach ($sales as $sale) {
            $sale->saleDetails = $this->db->query("
                select
                    sd.*,
                    p.Product_Code,
                    p.Product_Name,
                    (sd.Purchase_Rate * sd.SaleDetails_TotalQuantity) as purchased_amount,
                    (select sd.SaleDetails_TotalAmount - purchased_amount) as profit_loss
                from tbl_saledetails sd 
                join tbl_product p on p.Product_SlNo = sd.Product_IDNo
                where sd.SaleMaster_IDNo = ?
            ", $sale->SaleMaster_SlNo)->result();
        }

        echo json_encode($sales);
    }

    public function chalan($saleId)
    {
        $data['title'] = "Chalan Invoice";
        $data['saleId'] = $saleId;
        $data['content'] = $this->load->view('Administrator/sales/chalan', $data, true);
        $this->load->view('Administrator/index', $data);
    }

    public function checkSaleReturn($invoice)
    {
        $res = ['found' => false];
        $returnCount = $this->db->query("select * from tbl_salereturn where SaleMaster_InvoiceNo = ? and status = 'a'", $invoice)->num_rows();

        if ($returnCount != 0) {
            $res = ['found' => true];
        }

        echo json_encode($res);
    }
}
