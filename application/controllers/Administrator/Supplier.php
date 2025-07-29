<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Supplier extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $access = $this->session->userdata('userId');
        $this->brunch = $this->session->userdata('BRANCHid');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model("Model_myclass", "mmc", TRUE);
        $this->load->model('Model_table', "mt", TRUE);
    }
    public function index()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Supplier";
        $data['supplierId'] = 0;
        $data['supplierCode'] = $this->mt->generateSupplierCode();
        $data['content'] = $this->load->view('Administrator/add_supplier', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function editSupplier($id)
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Supplier";
        $data['supplierId'] = $id;
        $data['supplierCode'] = $this->mt->generateSupplierCode();
        $data['content'] = $this->load->view('Administrator/add_supplier', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function addSupplier()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $supplierObj = json_decode($this->input->post('data'));
            $supplierCodeCount = $this->db->query("select * from tbl_supplier where Supplier_Code = ?", $supplierObj->Supplier_Code)->num_rows();
            if ($supplierCodeCount > 0) {
                $supplierObj->Supplier_Code = $this->mt->generateSupplierCode();
            }

            $supplier = (array)$supplierObj;
            unset($supplier['Supplier_SlNo']);
            $supplier["branch_id"] = $this->session->userdata("BRANCHid");

            $supplierId = null;
            $res_message = "";

            $supplierMobileCount = $this->db->query("select * from tbl_supplier where Supplier_Mobile = ? and branch_id = ?", [$supplierObj->Supplier_Mobile, $this->session->userdata("BRANCHid")]);
            if ($supplierMobileCount->num_rows() > 0) {
                $duplicateSupplier = $supplierMobileCount->row();

                unset($supplier['Supplier_Code']);
                $supplier["status"]         = 'a';
                $supplier["UpdateBy"]       = $this->session->userdata("userId");
                $supplier["UpdateTime"]     = date("Y-m-d H:i:s");
                $supplier["last_update_ip"] = get_client_ip();
                $this->db->where('Supplier_SlNo', $duplicateSupplier->Supplier_SlNo)->update('tbl_supplier', $supplier);

                $supplierId = $duplicateSupplier->Supplier_SlNo;
                $supplierObj->Supplier_Code = $duplicateSupplier->Supplier_Code;
                $res_message = 'Supplier updated successfully';
            } else {

                $supplier["status"]         = 'a';
                $supplier["AddBy"] = $this->session->userdata("userId");
                $supplier["AddTime"] = date("Y-m-d H:i:s");
                $supplier["last_update_ip"] = get_client_ip();
                $this->db->insert('tbl_supplier', $supplier);

                $supplierId = $this->db->insert_id();
                $res_message = 'Supplier added successfully';
            }

            if (!empty($_FILES)) {
                $imagePath = $this->mt->uploadImage($_FILES, 'image', 'uploads/suppliers', $supplier['Supplier_Name']);
                $this->db->query("update tbl_supplier set image_name = ? where Supplier_SlNo = ?", [$imagePath, $supplierId]);
            }

            $res = ['success' => true, 'message' => $res_message, 'supplierCode' => $this->mt->generateSupplierCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateSupplier()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $supplierObj = json_decode($this->input->post('data'));
            $supplierMobileCount = $this->db->query("select * from tbl_supplier where Supplier_Mobile = ? and Supplier_SlNo != ? and branch_id = ?", [$supplierObj->Supplier_Mobile, $supplierObj->Supplier_SlNo, $this->session->userdata("BRANCHid")])->num_rows();
            if ($supplierMobileCount > 0) {
                $res = ['success' => false, 'message' => 'Mobile number already exists'];
                echo Json_encode($res);
                exit;
            }
            $supplier = (array)$supplierObj;
            $supplierId = $supplierObj->Supplier_SlNo;

            unset($supplier["Supplier_SlNo"]);
            $supplier["branch_id"] = $this->session->userdata("BRANCHid");
            $supplier["UpdateBy"] = $this->session->userdata("userId");
            $supplier["UpdateTime"] = date("Y-m-d H:i:s");
            $supplier["last_update_ip"] = get_client_ip();

            $this->db->where('Supplier_SlNo', $supplierId)->update('tbl_supplier', $supplier);

            if (!empty($_FILES)) {
                $oldImgFile = $this->db->query("select image_name from tbl_supplier where Supplier_SlNo = ?", $supplierId)->row()->image_name;
                if (file_exists($oldImgFile)) {
                    unlink($oldImgFile);
                }
                $imagePath = $this->mt->uploadImage($_FILES, 'image', 'uploads/suppliers', $supplier['Supplier_Name']);
                $this->db->query("update tbl_supplier set image_name = ? where Supplier_SlNo = ?", [$imagePath, $supplierId]);
            }

            $res = ['success' => true, 'message' => 'Supplier updated successfully', 'supplierCode' => $this->mt->generateSupplierCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function deleteSupplier()
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

            $this->db->where("Supplier_SlNo", $data->supplierId);
            $this->db->update("tbl_supplier", $rules);

            $res = ['success' => true, 'message' => 'Supplier deleted'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }
    function supplier_due()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = 'Supplier Due';
        $data['content'] = $this->load->view('Administrator/due_report/supplier_due', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }


    public function supplierPaymentPage()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Supplier Payment";

        $data['paymentHis'] = $this->Billing_model->fatch_all_supplier_payment();
        $data['suppliers'] = $this->Other_model->branch_wise_supplier_info();
        $data['content'] = $this->load->view('Administrator/due_report/supplierPaymentPage', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }


    function fatch_supplier_name($Suppid = null)
    {
        $supplier = $this->db->where('Supplier_SlNo', $Suppid)->get('tbl_supplier')->row();

        $data = array(
            'sup_name'      => $supplier->Supplier_Name,
            'due'                => $this->mt->getSupplierDueById($Suppid)
        );
        echo json_encode($data);
    }


    function supplierPaymentUpdate($Suppid = null)
    {

        $attr = array(
            "SPayment_date"          => $this->input->post('paymentDate', TRUE),
            "SPayment_invoice"       => $this->input->post('tr_id', TRUE),
            "SPayment_customerID"    => $this->input->post('SuppID', TRUE),
            "SPayment_TransactionType" => $this->input->post('tr_type', TRUE),
            "SPayment_amount"        => $this->input->post('paidAmount', TRUE),
            "SPayment_notes"         => $this->input->post('Note', TRUE),
            "SPayment_Paymentby"     => $this->input->post('Paymentby', TRUE),
            "Addby"         => $this->session->userdata("userId"),
            "branch_id"      => $this->session->userdata("BRANCHid"),
            "UpdateTime"    => date('Y-m-d'),
        );

        $this->db->where('SPayment_id', $Suppid);
        $qu = $this->db->update('tbl_supplier_payment', $attr);

        if ($this->db->affected_rows()) {
            echo json_encode(TRUE);
        } else {
            echo json_encode(FALSE);
        }
    }

    function supplier_PaymentAmount()
    {

        $data = array(
            "SPayment_date"          => $this->input->post('paymentDate', TRUE),
            "SPayment_invoice"       => $this->input->post('tr_id', TRUE),
            "SPayment_customerID"    => $this->input->post('SuppID', TRUE),
            "SPayment_TransactionType" => $this->input->post('tr_type', TRUE),
            "SPayment_amount"        => $this->input->post('paidAmount', TRUE),
            "SPayment_notes"         => $this->input->post('Note', TRUE),
            "SPayment_Paymentby"     => $this->input->post('Paymentby', TRUE),
            "Addby"         => $this->session->userdata("userId"),
            "branch_id"      => $this->session->userdata("BRANCHid"),
            "AddTime"       => date('Y-m-d'),
            "status"        => 'a',
        );
        $this->mt->save_data("tbl_supplier_payment", $data);
        echo json_encode(TRUE);
    }
    function supplier_payment_report()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Supplier Payment Reports";
        $data['content'] = $this->load->view('Administrator/payment_reports/supplier_payment_report', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    function search_supplier_payments()
    {
        $dAta['searchtype'] = $searchtype = $this->input->post('searchtype');
        $dAta['Purchase_startdate'] = $Purchase_startdate = $this->input->post('Purchase_startdate');
        $dAta['Purchase_enddate'] = $Purchase_enddate = $this->input->post('Purchase_enddate');
        $dAta['Supplierid'] = $Supplierid = $this->input->post('Supplierid');
        $this->session->set_userdata($dAta);

        if ($searchtype == "Supplier") {
            $sql = "SELECT 
                                tbl_supplier_payment.*,
                                tbl_supplier.* 
                        FROM tbl_supplier_payment 
                        left join tbl_supplier on tbl_supplier.Supplier_SlNo = tbl_supplier_payment.SPayment_customerID 
                        WHERE tbl_supplier_payment.SPayment_customerID = '$Supplierid' 
                        and  tbl_supplier_payment.SPayment_date 
                            between  '$Purchase_startdate' 
                            and '$Purchase_enddate' 
                        GROUP BY tbl_supplier_payment.SPayment_invoice
                        order by SPayment_date";
        }
        $result = $this->db->query($sql);

        $dueSql = "SELECT s.Supplier_Name,
        (select ifnull(sum(PurchaseMaster_SubTotalAmount), 0.00) from tbl_purchasemaster where Supplier_SlNo = s.Supplier_SlNo and PurchaseMaster_OrderDate < '$Purchase_startdate' ) as purchaseAmount,
        (select ifnull(sum(SPayment_amount), 0.00) from tbl_supplier_payment where SPayment_customerID = s.Supplier_SlNo and SPayment_date < '$Purchase_startdate') as paidAmount,
        (select ifnull(sum(PurchaseReturn_ReturnAmount), 0.00) from tbl_purchasereturn where Supplier_IDdNo = s.Supplier_SlNo and PurchaseReturn_ReturnDate < '$Purchase_startdate') as returnAmount,
        (select (s.previous_due + purchaseAmount) - (paidAmount + returnAmount)) as dueAmount
        from tbl_supplier s
        where Supplier_SLNo = '$Supplierid'";

        $dueResult = $this->db->query($dueSql);

        $datas["record"] = $result->result();
        $datas["recordss"] = $result->row();
        $datas["due"] = $dueResult->row();
        $this->load->view('Administrator/payment_reports/supplier_payment_report_list', $datas);
    }

    public function getSuppliers()
    {
        $data = json_decode($this->input->raw_input_stream);
        $clauses = "";
        $limit = "";
        $status = "a";
        if (isset($data->status) && $data->status != '') {
            $status = $data->status;
        }

        if (isset($data->supplierId) && $data->supplierId != '') {
            $clauses .= "and s.Supplier_SlNo = '$data->supplierId'";
        }

        if (isset($data->forSearch) && $data->forSearch != '') {
            $limit .= "limit 20";
        }
        if (isset($data->name) && $data->name != '') {
            $clauses .= "and s.Supplier_Code like '$data->name%'";
            $clauses .= "or s.Supplier_Name like '$data->name%'";
            $clauses .= "or s.Supplier_Mobile like '$data->name%'";
        }

        $suppliers = $this->db->query("
            select 
            s.*,
            concat(s.Supplier_Name, ' - ', s.Supplier_Code, ' - ', s.Supplier_Mobile) as display_name
            from tbl_supplier s
            where s.status = '$status'
            and s.Supplier_Type != 'G'
            and s.branch_id = ? or branch_id = 0
            $clauses
            order by s.Supplier_SlNo desc
            $limit
        ", $this->session->userdata('BRANCHid'))->result();

        echo json_encode($suppliers);
    }

    public function getSupplierDue()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->supplierId) && $data->supplierId != null) {
            $clauses = " and s.Supplier_SlNo = '$data->supplierId'";
        }
        $supplierDues = $this->mt->supplierDue($clauses);

        echo json_encode($supplierDues);
    }

    public function getSupplierLedger()
    {
        $data = json_decode($this->input->raw_input_stream);
        $previousDueQuery = $this->db->query("select ifnull(previous_due, 0.00) as previous_due from tbl_supplier where Supplier_SlNo = '$data->supplierId'")->row();
        $payments = $this->db->query("
            select
                'a' as sequence,
                pm.PurchaseMaster_SlNo as id,
                pm.PurchaseMaster_OrderDate date,
                concat('Purchase ', pm.PurchaseMaster_InvoiceNo) as description,
                pm.PurchaseMaster_TotalAmount as bill,
                pm.PurchaseMaster_PaidAmount as paid,
                (pm.PurchaseMaster_TotalAmount - pm.PurchaseMaster_PaidAmount) as due,
                0.00 as returned,
                0.00 as cash_received,
                0.00 as balance
            from tbl_purchasemaster pm
            where pm.Supplier_SlNo = '$data->supplierId'
            and pm.status = 'a'
            
            UNION
            select
                'b' as sequence,
                sp.SPayment_id as id,
                sp.SPayment_date as date,
                concat('Paid - ', 
                    case sp.SPayment_Paymentby
                        when 'bank' then concat('Bank - ', ba.account_name, ' - ', ba.account_number, ' - ', ba.bank_name)
                        else 'Cash'
                    end, ' ', sp.SPayment_notes
                ) as description,
                0.00 as bill,
                sp.SPayment_amount as paid,
                0.00 as due,
                0.00 as returned,
                0.00 as cash_received,
                0.00 as balance
            from tbl_supplier_payment sp 
            left join tbl_bank_accounts ba on ba.account_id = sp.account_id
            where sp.SPayment_customerID = '$data->supplierId'
            and sp.SPayment_TransactionType = 'CP'
            and sp.status = 'a'
            
            UNION
            select 
                'c' as sequence,
                sp2.SPayment_id as id,
                sp2.SPayment_date as date,
                concat('Received - ', 
                    case sp2.SPayment_Paymentby
                        when 'bank' then concat('Bank - ', ba.account_name, ' - ', ba.account_number, ' - ', ba.bank_name)
                        else 'Cash'
                    end, ' ', sp2.SPayment_notes
                ) as description,
                0.00 as bill,
                0.00 as paid,
                0.00 as due,
                0.00 as returned,
                sp2.SPayment_amount as cash_received,
                0.00 as balance
            from tbl_supplier_payment sp2
            left join tbl_bank_accounts ba on ba.account_id = sp2.account_id
            where sp2.SPayment_customerID = '$data->supplierId'
            and sp2.SPayment_TransactionType = 'CR'
            and sp2.status = 'a'
            
            UNION
            select
                'd' as sequence,
                pr.PurchaseReturn_SlNo as id,
                pr.PurchaseReturn_ReturnDate as date,
                'Purchase Return' as description,
                0.00 as bill,
                0.00 as paid,
                0.00 as due,
                pr.PurchaseReturn_ReturnAmount as returned,
                0.00 as cash_received,
                0.00 as balance
            from tbl_purchasereturn pr
            where pr.Supplier_IDdNo = '$data->supplierId'
            and pr.status = 'a'
            
            order by date, sequence, id
        ")->result();

        $previousBalance = $previousDueQuery->previous_due;

        foreach ($payments as $key => $payment) {
            $lastBalance = $key == 0 ? $previousDueQuery->previous_due : $payments[$key - 1]->balance;
            $payment->balance = ($lastBalance + $payment->bill + $payment->cash_received) - ($payment->paid + $payment->returned);
        }

        if ((isset($data->dateFrom) && $data->dateFrom != null) && (isset($data->dateTo) && $data->dateTo != null)) {
            $previousPayments = array_filter($payments, function ($payment) use ($data) {
                return $payment->date < $data->dateFrom;
            });

            $previousBalance = count($previousPayments) > 0 ? $previousPayments[count($previousPayments) - 1]->balance : $previousBalance;

            $payments = array_filter($payments, function ($payment) use ($data) {
                return $payment->date >= $data->dateFrom && $payment->date <= $data->dateTo;
            });
        }

        $res['previousBalance'] = $previousBalance;
        $res['payments'] = $payments;
        echo json_encode($res);
    }

    public function getSupplierPayments()
    {
        $data = json_decode($this->input->raw_input_stream);

        $paymentTypeClause = "";
        $status = "a";
        if (isset($data->status) && $data->status != '') {
            $status = $data->status;
        }
        if (isset($data->paymentType) && $data->paymentType != '' && $data->paymentType == 'received') {
            $paymentTypeClause = " and sp.SPayment_TransactionType = 'CR'";
        }
        if (isset($data->paymentType) && $data->paymentType != '' && $data->paymentType == 'paid') {
            $paymentTypeClause = " and sp.SPayment_TransactionType = 'CP'";
        }

        $dateClause = "";
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $dateClause = " and sp.SPayment_date between '$data->dateFrom' and '$data->dateTo'";
        }

        $payments = $this->db->query("
            select
                sp.*,
                s.Supplier_Code,
                s.Supplier_Name,
                s.Supplier_Mobile,
                ba.account_name,
                ba.account_number,
                ba.bank_name,
                case sp.SPayment_TransactionType
                when 'CR' then 'Received'
                    when 'CP' then 'Paid'
                end as transaction_type,
                case sp.SPayment_Paymentby
                    when 'bank' then concat('Bank - ', ba.account_name, ' - ', ba.account_number, ' - ', ba.bank_name)
                    else 'Cash'
                end as payment_by,
                ua.User_Name as added_by,
                ud.User_Name as deleted_by
            from tbl_supplier_payment sp
            left join tbl_bank_accounts ba on ba.account_id = sp.account_id
            left join tbl_supplier s on s.Supplier_SlNo = sp.SPayment_customerID
            left join tbl_user ua on ua.User_SlNo = sp.AddBy
            left join tbl_user ud on ud.User_SlNo = sp.DeletedBy
            where sp.status = '$status'
            and sp.branch_id = ? $paymentTypeClause $dateClause
            order by sp.SPayment_id desc
        ", $this->session->userdata('BRANCHid'))->result();

        echo json_encode($payments);
    }

    public function addSupplierPayment()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $paymentObj = json_decode($this->input->raw_input_stream);

            $payment = (array)$paymentObj;
            $payment['SPayment_invoice'] = $this->mt->generateSupplierPaymentCode();
            $payment['status'] = 'a';
            $payment['Addby'] = $this->session->userdata("userId");
            $payment['AddTime'] = date('Y-m-d H:i:s');
            $payment['last_update_ip'] = get_client_ip();
            $payment['branch_id'] = $this->session->userdata("BRANCHid");

            $this->db->insert('tbl_supplier_payment', $payment);
            $paymentId = $this->db->insert_id();

            $res = ['success' => true, 'message' => 'Payment added successfully', 'paymentId' => $paymentId];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateSupplierPayment()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $paymentObj = json_decode($this->input->raw_input_stream);
            $paymentId = $paymentObj->SPayment_id;

            $payment = (array)$paymentObj;
            unset($payment['SPayment_id']);
            $payment['UpdateBy'] = $this->session->userdata("userId");
            $payment['UpdateTime'] = date('Y-m-d H:i:s');
            $payment['last_update_ip'] = get_client_ip();

            $this->db->where('SPayment_id', $paymentObj->SPayment_id)->update('tbl_supplier_payment', $payment);

            $res = ['success' => true, 'message' => 'Payment updated successfully', 'paymentId' => $paymentId];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function deleteSupplierPayment()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $payment = array(
                'status'         => 'd',
                "DeletedBy"      => $this->session->userdata("userId"),
                "DeletedTime"    => date("Y-m-d H:i:s"),
                "last_update_ip" => get_client_ip()
            );
            $this->db->set($payment)->where('SPayment_id', $data->paymentId)->update('tbl_supplier_payment');

            $res = ['success' => true, 'message' => 'Payment deleted successfully'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }
}
