<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Customer extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->cbrunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model("Model_myclass", "mmc", TRUE);
        $this->load->model('Model_table', "mt", TRUE);
        $this->load->model('SMS_model', 'sms', true);
    }

    public function index()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Customer";
        $data['customerId'] = 0;
        $data['customerCode'] = $this->mt->generateCustomerCode();
        $data['content'] = $this->load->view('Administrator/add_customer', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function customeredit($id){
         $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Customer";
        $data['customerId'] = $id;
        $data['customerCode'] = $this->mt->generateCustomerCode();
        $data['content'] = $this->load->view('Administrator/add_customer', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function customerlist()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Customer List";
        $data['content'] = $this->load->view("Administrator/reports/customer_list", $data, true);
        $this->load->view("Administrator/index", $data);
    }

    public function getCustomers()
    {
        $data = json_decode($this->input->raw_input_stream);

        $customerTypeClause = "";
        $limit = "";
        $status = "a";
        if (isset($data->status) && $data->status != '') {
            $status = $data->status;
        }

        if (isset($data->customerType) && $data->customerType != null) {
            $customerTypeClause = " and Customer_Type = '$data->customerType'";
        }

        if (isset($data->customerId) && $data->customerId != null) {
            $customerTypeClause = " and c.Customer_SlNo = '$data->customerId'";
        }

        if (isset($data->forSearch) && $data->forSearch != '') {
            $limit .= "limit 20";
        }

        if (isset($data->name) && $data->name != '') {
            $customerTypeClause .= " and c.Customer_Code like '$data->name%'";
            $customerTypeClause .= " or c.Customer_Name like '$data->name%'";
            $customerTypeClause .= " or c.Customer_Mobile like '$data->name%'";
        }

        $customers = $this->db->query("
            select
                c.*,
                d.District_Name,
                concat_ws(' - ', c.Customer_Name, c.Customer_Code, c.Customer_Mobile) as display_name,
                concat(ea.Employee_Name, ' - ', ea.Employee_ID) as display_text,
                ua.User_Name as added_by,
                ud.User_Name as deleted_by
            from tbl_customer c
            left join tbl_district d on d.District_SlNo = c.area_ID
            left join tbl_user ua on ua.User_SlNo = c.AddBy
            left join tbl_employee ea on ea.Employee_SlNo = c.employee_id
            left join tbl_user ud on ud.User_SlNo = c.DeletedBy
            where c.status = '$status'
            and c.Customer_Type != 'G'
            $customerTypeClause
            and (c.branch_id = ? or c.branch_id = 0)
            order by c.Customer_SlNo desc
            $limit
        ", $this->session->userdata('BRANCHid'))->result();
        echo json_encode($customers);
    }

    public function getCustomerDue()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->customerId) && $data->customerId != null) {
            $clauses .= " and c.Customer_SlNo = '$data->customerId'";
        }
        if (isset($data->districtId) && $data->districtId != null) {
            $clauses .= " and c.area_ID = '$data->districtId'";
        }

        $dueResult = $this->mt->customerDue($clauses);

        echo json_encode($dueResult);
    }

    public function get_customer_due_remainder()
    {
        $date = date("Y-m-d");
        $reminders = $this->db->query("
                        select * from( select 
                        c.Customer_SlNo,
                        c.Customer_Name,
                        c.Customer_Code,
                        c.Customer_Address,
                        c.Customer_Mobile,
                    (select ifnull(sum(sm.SaleMaster_TotalSaleAmount), 0.00) + ifnull(c.previous_due, 0.00)
                        from tbl_salesmaster sm 
                        where sm.SalseCustomer_IDNo = c.Customer_SlNo
                        " . ($date == null ? "" : " and sm.SaleMaster_SaleDate < '$date'") . "
                        and sm.status = 'a') as billAmount,

                    (select ifnull(sum(sm.SaleMaster_PaidAmount), 0.00)
                        from tbl_salesmaster sm
                        where sm.SalseCustomer_IDNo = c.Customer_SlNo
                        " . ($date == null ? "" : " and sm.SaleMaster_SaleDate < '$date'") . "
                        and sm.status = 'a') as invoicePaid,

                    (select ifnull(sum(cp.CPayment_amount), 0.00) 
                        from tbl_customer_payment cp 
                        where cp.CPayment_customerID = c.Customer_SlNo 
                        and cp.CPayment_TransactionType = 'CR'
                        " . ($date == null ? "" : " and cp.CPayment_date < '$date'") . "
                        and cp.status = 'a') as cashReceived,

                    (select ifnull(sum(cp.CPayment_amount), 0.00) 
                        from tbl_customer_payment cp 
                        where cp.CPayment_customerID = c.Customer_SlNo 
                        and cp.CPayment_TransactionType = 'CP'
                        " . ($date == null ? "" : " and cp.CPayment_date < '$date'") . "
                        and cp.status = 'a') as paidOutAmount,

                    (select ifnull(sum(sr.SaleReturn_ReturnAmount), 0.00) 
                        from tbl_salereturn sr 
                        join tbl_salesmaster smr on smr.SaleMaster_InvoiceNo = sr.SaleMaster_InvoiceNo 
                        where smr.SalseCustomer_IDNo = c.Customer_SlNo 
                        " . ($date == null ? "" : " and sr.SaleReturn_ReturnDate < '$date'") . "
                    ) as returnedAmount,

                    (select invoicePaid + cashReceived) as paidAmount,

                    (select (billAmount + paidOutAmount) - (paidAmount + returnedAmount)) as dueAmount
                    
                    from tbl_customer c
                    where c.branch_id = ?
                    and c.Customer_remainder_day  = ?
                    ) as tbl
                        where 1=1
                        and dueAmount > 0

        ", [$this->session->userdata('BRANCHid'),$date])->result();
        echo json_encode($reminders);
    }

    function due_reminder()  {
        $access = $this->mt->userAccess();
        if(!$access){
            redirect(base_url());
        }
        $data['title'] = "Due Reminder List";  
        $data['content'] = $this->load->view('Administrator/due_reminder', $data, TRUE);
        $this->load->view('Administrator/index', $data); 
    }

    public function getCustomerPayments()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        $status = "a";
        if (isset($data->status) && $data->status != '') {
            $status = $data->status;
        }
        if (isset($data->paymentType) && $data->paymentType != '' && $data->paymentType == 'received') {
            $clauses .= " and cp.CPayment_TransactionType = 'CR'";
        }
        if (isset($data->paymentType) && $data->paymentType != '' && $data->paymentType == 'paid') {
            $clauses .= " and cp.CPayment_TransactionType = 'CP'";
        }

        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and cp.CPayment_date between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->customerId) && $data->customerId != '' && $data->customerId != null) {
            $clauses .= " and cp.CPayment_customerID = '$data->customerId'";
        }

        if (isset($data->paymentId) && $data->paymentId != '' && $data->paymentId != null) {
            $clauses .= " and cp.CPayment_id = '$data->paymentId'";
        }

        $payments = $this->db->query("
            select
                cp.*,
                c.Customer_Code,
                c.Customer_Name,
                c.Customer_Mobile,
                ba.account_name,
                ba.account_number,
                ba.bank_name,
                case cp.CPayment_TransactionType
                    when 'CR' then 'Received'
                    when 'CP' then 'Paid'
                end as transaction_type,
                case cp.CPayment_Paymentby
                    when 'bank' then concat('Bank - ', ba.account_name, ' - ', ba.account_number, ' - ', ba.bank_name)
                    when 'By Cheque' then 'Cheque'
                    else 'Cash'
                end as payment_by,
                ua.User_Name as added_by,
                ud.User_Name as deleted_by
            from tbl_customer_payment cp
            left join tbl_customer c on c.Customer_SlNo = cp.CPayment_customerID
            left join tbl_bank_accounts ba on ba.account_id = cp.account_id
            left join tbl_user ua on ua.User_SlNo = cp.AddBy
            left join tbl_user ud on ud.User_SlNo = cp.DeletedBy
            where cp.status = '$status'
            and cp.branch_id = ? $clauses
            order by cp.CPayment_id desc
        ", $this->session->userdata('BRANCHid'))->result();

        echo json_encode($payments);
    }

    public function addCustomerPayment()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $paymentObj = json_decode($this->input->raw_input_stream);

            $payment = (array)$paymentObj;
            $payment['CPayment_invoice'] = $this->mt->generateCustomerPaymentCode();
            $payment['status'] = 'a';
            $payment['AddBy'] = $this->session->userdata("userId");
            $payment['AddTime'] = date('Y-m-d H:i:s');
            $payment['last_update_ip'] = get_client_ip();
            $payment['branch_id'] = $this->session->userdata("BRANCHid");

            $this->db->insert('tbl_customer_payment', $payment);
            $paymentId = $this->db->insert_id();

            if ($paymentObj->CPayment_TransactionType == 'CR') {
                $currentDue = $paymentObj->CPayment_TransactionType == 'CR' ? $paymentObj->CPayment_previous_due - $paymentObj->CPayment_amount : $paymentObj->CPayment_previous_due + $paymentObj->CPayment_amount;
                //Send sms
                $customerInfo = $this->db->query("select * from tbl_customer where Customer_SlNo = ?", $paymentObj->CPayment_customerID)->row();
                $sendToName = $customerInfo->owner_name != '' ? $customerInfo->owner_name : $customerInfo->Customer_Name;
                $currency = $this->session->userdata('Currency_Name');
                $paymentInvoice = $payment['CPayment_invoice'];

                $message = "Dear {$sendToName},\nThanks for your payment\nInvoice No.{$paymentInvoice}\nReceived {$currency} {$paymentObj->CPayment_amount}\nCurrent due {$currency} {$currentDue}";
                $recipient = $customerInfo->Customer_Mobile;
                $this->sms->sendSms($recipient, $message);
            }

            $res = ['success' => true, 'message' => 'Payment added successfully', 'paymentId' => $paymentId];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateCustomerPayment()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $paymentObj = json_decode($this->input->raw_input_stream);
            $paymentId = $paymentObj->CPayment_id;

            $payment = (array)$paymentObj;
            unset($payment['CPayment_id']);
            $payment['UpdateBy'] = $this->session->userdata("userId");
            $payment['UpdateTime'] = date('Y-m-d H:i:s');
            $payment['last_update_ip'] = get_client_ip();

            $this->db->where('CPayment_id', $paymentObj->CPayment_id)->update('tbl_customer_payment', $payment);

            $res = ['success' => true, 'message' => 'Payment updated successfully', 'paymentId' => $paymentId];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function deleteCustomerPayment()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $payment = array(
                'status' => 'd',
                'DeletedBy' => $this->session->userdata('userId'),
                'DeletedTime' => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
            );
            $this->db->set($payment)->where('CPayment_id', $data->paymentId)->update('tbl_customer_payment');

            $res = ['success' => true, 'message' => 'Payment deleted successfully'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function addCustomer()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $customerObj = json_decode($this->input->post('data'));

            $customerCodeCount = $this->db->query("select * from tbl_customer where Customer_Code = ?", $customerObj->Customer_Code)->num_rows();
            if ($customerCodeCount > 0) {
                $customerObj->Customer_Code = $this->mt->generateCustomerCode();
            }

            $customer = (array)$customerObj;
            unset($customer['Customer_SlNo']);
            $customer["branch_id"] = $this->session->userdata("BRANCHid");

            $customerId = null;
            $res_message = "";

            $duplicateMobileQuery = $this->db->query("select * from tbl_customer where Customer_Mobile = ? and branch_id = ?", [$customerObj->Customer_Mobile, $this->session->userdata("BRANCHid")]);

            if ($duplicateMobileQuery->num_rows() > 0) {
                $duplicateCustomer = $duplicateMobileQuery->row();

                unset($customer['Customer_Code']);
                $customer["status"]     = 'a';
                $customer["UpdateBy"]   = $this->session->userdata("userId");
                $customer["UpdateTime"] = date("Y-m-d H:i:s");
                $customer["last_update_ip"] = get_client_ip();
                $this->db->where('Customer_SlNo', $duplicateCustomer->Customer_SlNo)->update('tbl_customer', $customer);

                $customerId = $duplicateCustomer->Customer_SlNo;
                $customerObj->Customer_Code = $duplicateCustomer->Customer_Code;
                $res_message = 'Customer updated successfully';
            } else {
                $customer["status"]     = 'a';
                $customer["AddBy"] = $this->session->userdata("userId");
                $customer["AddTime"] = date("Y-m-d H:i:s");
                $customer["last_update_ip"] = get_client_ip();

                $this->db->insert('tbl_customer', $customer);
                $customerId = $this->db->insert_id();
                $res_message = 'Customer added successfully';
            }

            if (!empty($_FILES)) {
                $imagePath = $this->mt->uploadImage($_FILES, 'image', 'uploads/customers', $customerObj->Customer_Code);
                $this->db->query("update tbl_customer c set c.image_name = ? where c.Customer_SlNo = ?", [$imagePath, $customerId]);
            }

            $res = ['success' => true, 'message' => $res_message, 'customerCode' => $this->mt->generateCustomerCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateCustomer()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $customerObj = json_decode($this->input->post('data'));

            $customerMobileCount = $this->db->query("select * from tbl_customer where Customer_Mobile = ? and Customer_SlNo != ? and branch_id = ?", [$customerObj->Customer_Mobile, $customerObj->Customer_SlNo, $this->session->userdata("BRANCHid")]);

            if ($customerMobileCount->num_rows() > 0) {
                $res = ['success' => false, 'message' => 'Mobile number already exists'];
                echo Json_encode($res);
                exit;
            }
            $customer = (array)$customerObj;
            $customerId = $customerObj->Customer_SlNo;

            unset($customer["Customer_SlNo"]);
            $customer["branch_id"] = $this->session->userdata("BRANCHid");
            $customer["UpdateBy"] = $this->session->userdata("userId");
            $customer["UpdateTime"] = date("Y-m-d H:i:s");
            $customer["last_update_ip"] = get_client_ip();

            $this->db->where('Customer_SlNo', $customerId)->update('tbl_customer', $customer);
            $customerImage = $this->db->query("select * from tbl_customer c where c.Customer_SlNo = ?", $customerId)->row();
            if (!empty($_FILES)) {
                $oldImgFile = $customerImage->image_name;
                if (file_exists($oldImgFile)) {
                    unlink($oldImgFile);
                }
                $imagePath = $this->mt->uploadImage($_FILES, 'image', 'uploads/customers', $customerObj->Customer_Code);
                $this->db->query("update tbl_customer set image_name = ? where Customer_SlNo = ?", [$imagePath, $customerId]);
            }

            $res = ['success' => true, 'message' => 'Customer updated successfully', 'customerCode' => $this->mt->generateCustomerCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }
        echo json_encode($res);
    }

    public function deleteCustomer()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $rules = array(
                'status'         => 'd',
                "DeletedBy"      => $this->session->userdata("userId"),
                "DeletedTime"    => date("Y-m-d H:i:s"),
                "last_update_ip" => get_client_ip()
            );
            $this->db->where("Customer_SlNo", $data->customerId);
            $this->db->update("tbl_customer", $rules);

            $res = ['success' => true, 'message' => 'Customer deleted'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    function customer_due()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = 'Customer Due';
        $data['content'] = $this->load->view('Administrator/due_report/customer_due', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function customerPaymentPage()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Customer Payment";
        $data['paymentId'] = 0;
        $data['paymentHis'] = $this->Billing_model->fatch_all_payment();
        $query0 = $this->db->query("SELECT * FROM tbl_customer_payment ORDER BY CPayment_id DESC LIMIT 1");
        $row = $query0->row();

        @$invoice = $row->CPayment_invoice;
        $previousinvoice = substr($invoice, 3, 11);
        if (!empty($invoice)) {
            if ($previousinvoice < 10) {
                $purchInvoice = 'TR-00' . ($previousinvoice + 1);
            } else if ($previousinvoice < 100) {
                $purchInvoice = 'TR-0' . ($previousinvoice + 1);
            } else {
                $purchInvoice = 'TR-' . ($previousinvoice + 1);
            }
        } else {
            $purchInvoice = 'TR-001';
        }
        $data['purchInvoice'] = $purchInvoice;
        $data['customers'] = $this->Customer_model->get_customer_name_code_brunch_wise();
        $data['content'] = $this->load->view('Administrator/due_report/customerPaymentPage', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function customerPaymentEdit($id)
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Customer Payment";
        $data['paymentId'] = $id;
        $data['paymentHis'] = $this->Billing_model->fatch_all_payment();
        $query0 = $this->db->query("SELECT * FROM tbl_customer_payment ORDER BY CPayment_id DESC LIMIT 1");
        $row = $query0->row();

        @$invoice = $row->CPayment_invoice;
        $previousinvoice = substr($invoice, 3, 11);
        if (!empty($invoice)) {
            if ($previousinvoice < 10) {
                $purchInvoice = 'TR-00' . ($previousinvoice + 1);
            } else if ($previousinvoice < 100) {
                $purchInvoice = 'TR-0' . ($previousinvoice + 1);
            } else {
                $purchInvoice = 'TR-' . ($previousinvoice + 1);
            }
        } else {
            $purchInvoice = 'TR-001';
        }
        $data['purchInvoice'] = $purchInvoice;
        $data['customers'] = $this->Customer_model->get_customer_name_code_brunch_wise();
        $data['content'] = $this->load->view('Administrator/due_report/customerPaymentPage', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    

    function paymentAndReport($id = Null)
    {
        $data['title'] = "Customer Payment Reports";
        if ($id != 'pr') {
            $pid["PamentID"] = $id;
            $this->session->set_userdata($pid);
        }
        $data['content'] = $this->load->view('Administrator/due_report/paymentAndReport', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    function customer_payment_report()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Customer Payment Reports";
        $branch_id = $this->session->userdata('BRANCHid');

        $data['content'] = $this->load->view('Administrator/payment_reports/customer_payment_report', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    function getCustomerLedger()
    {
        $data = json_decode($this->input->raw_input_stream);
        $previousDueQuery = $this->db->query("select ifnull(previous_due, 0.00) as previous_due from tbl_customer where Customer_SlNo = '$data->customerId'")->row();

        $payments = $this->db->query("
            select 
                'a' as sequence,
                sm.SaleMaster_SlNo as id,
                sm.SaleMaster_SaleDate as date,
                concat('Sales ', sm.SaleMaster_InvoiceNo) as description,
                concat('/sale_invoice_print/', sm.SaleMaster_SlNo) as invoice_url,
                sm.SaleMaster_TotalSaleAmount as bill,
                sm.SaleMaster_PaidAmount as paid,
                0.00 as discount,
                sm.SaleMaster_DueAmount as due,
                0.00 as returned,
                0.00 as paid_out,
                0.00 as balance
            from tbl_salesmaster sm
            where sm.SalseCustomer_IDNo = '$data->customerId'
            and sm.status = 'a'
            
            UNION
            select
                'b' as sequence,
                cp.CPayment_id as id,
                cp.CPayment_date as date,
                concat('Received - ', 
                    case cp.CPayment_Paymentby
                        when 'bank' then concat('Bank - ', ba.account_name, ' - ', ba.account_number, ' - ', ba.bank_name)
                        when 'By Cheque' then 'Cheque'
                        else 'Cash'
                    end, ' ', cp.CPayment_notes
                ) as description,
                concat('/paymentAndReport/', cp.CPayment_id) as invoice_url,
                0.00 as bill,
                cp.CPayment_amount as paid,
                cp.CPayment_discount as discount,
                0.00 as due,
                0.00 as returned,
                0.00 as paid_out,
                0.00 as balance
            from tbl_customer_payment cp
            left join tbl_bank_accounts ba on ba.account_id = cp.account_id
            where cp.CPayment_TransactionType = 'CR'
            and cp.CPayment_customerID = '$data->customerId'
            and cp.status = 'a'

            UNION
            select
                'c' as sequence,
                cp.CPayment_id as id,
                cp.CPayment_date as date,
                concat('Paid - ', 
                    case cp.CPayment_Paymentby
                        when 'bank' then concat('Bank - ', ba.account_name, ' - ', ba.account_number, ' - ', ba.bank_name)
                        else 'Cash'
                    end, ' ', cp.CPayment_notes
                ) as description,
                concat('/paymentAndReport/', cp.CPayment_id) as invoice_url,
                0.00 as bill,
                0.00 as paid,
                0.00 as discount,
                0.00 as due,
                0.00 as returned,
                cp.CPayment_amount as paid_out,
                0.00 as balance
            from tbl_customer_payment cp
            left join tbl_bank_accounts ba on ba.account_id = cp.account_id
            where cp.CPayment_TransactionType = 'CP'
            and cp.CPayment_customerID = '$data->customerId'
            and cp.status = 'a'
            
            UNION
            select
                'd' as sequence,
                sr.SaleReturn_SlNo as id,
                sr.SaleReturn_ReturnDate as date,
                'Sales return' as description,
                '#' as invoice_url,
                0.00 as bill,
                0.00 as paid,
                0.00 as discount,
                0.00 as due,
                sr.SaleReturn_ReturnAmount as returned,
                0.00 as paid_out,
                0.00 as balance
            from tbl_salereturn sr
            join tbl_salesmaster smr on smr.SaleMaster_InvoiceNo  = sr.SaleMaster_InvoiceNo
            where smr.SalseCustomer_IDNo = '$data->customerId'
            and sr.status = 'a'
            
            order by date, sequence, id
        ")->result();

        $previousBalance = $previousDueQuery->previous_due;

        foreach ($payments as $key => $payment) {
            $lastBalance = $key == 0 ? $previousDueQuery->previous_due : $payments[$key - 1]->balance;
            $payment->balance = ($lastBalance + $payment->bill + $payment->paid_out) - ($payment->paid + $payment->discount + $payment->returned);
        }

        if ((isset($data->dateFrom) && $data->dateFrom != null) && (isset($data->dateTo) && $data->dateTo != null)) {
            $previousPayments = array_filter($payments, function ($payment) use ($data) {
                return $payment->date < $data->dateFrom;
            });

            $previousBalance = count($previousPayments) > 0 ? $previousPayments[count($previousPayments) - 1]->balance : $previousBalance;

            $payments = array_filter($payments, function ($payment) use ($data) {
                return $payment->date >= $data->dateFrom && $payment->date <= $data->dateTo;
            });

            $payments = array_values($payments);
        }

        $res['previousBalance'] = $previousBalance;
        $res['payments'] = $payments;
        echo json_encode($res);
    }

    public function customerPaymentHistory()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Customer Payment History";
        $data['content'] = $this->load->view('Administrator/reports/customer_payment_history', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function customerPaymentReport() {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Customer Payment Report";
        $data['content'] = $this->load->view('Administrator/reports/customer_payment_report', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
}
