<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class LcPurchase extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->brunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model('Billing_model');
        $this->load->library('cart');
        $this->load->model('Model_table', "mt", TRUE);
        $this->load->helper('form');
    }

    public function order()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "LC Entry";

        $invoice = $this->mt->generatelcPurchaseInvoice();

        $data['purchaseId'] = 0;
        $data['invoice'] = $invoice;
        $data['content'] = $this->load->view('Administrator/lc_purchase/lc_purchase_order', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getPurchaseRecord()
    {
        $data = json_decode($this->input->raw_input_stream);
        $branchId = $this->session->userdata("BRANCHid");
        $clauses = "";
        $status = " and pd.status != 'd'";
        if (isset($data->status) && $data->status != '') {
            $status = " and pd.status = '$data->status'"; ;
        }

        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and pm.PurchaseMaster_OrderDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->userFullName) && $data->userFullName != '') {
            $clauses .= " and pm.AddBy = '$data->userFullName'";
        }

        if (isset($data->supplierId) && $data->supplierId != '') {
            $clauses .= " and pm.Supplier_SlNo = '$data->supplierId'";
        }

        $purchases = $this->db->query("
            select 
                pm.*,
                s.Supplier_Code,
                s.Supplier_Name,
                s.Supplier_Mobile,
                s.Supplier_Address,
                br.Branch_name,
                ua.User_Name as added_by,
                concat_ws(' - ', ba.account_name, ba.account_number , ba.bank_name) as bank_text,
                ud.User_Name as deleted_by
            from tbl_lcpurchasemaster pm
            left join tbl_supplier s on s.Supplier_SlNo = pm.Supplier_SlNo
            left join tbl_bank_accounts ba on ba.account_id = pm.account_id
            left join tbl_branch br on br.branch_id = pm.branch_id
            left join tbl_user ua on ua.User_SlNo = pm.AddBy
            left join tbl_user ud on ud.User_SlNo = pm.DeletedBy
            where pm.branch_id = '$branchId'
            and pm.status != 'd'
            $clauses
        ")->result();

        foreach ($purchases as $purchase) {
            $purchase->purchaseDetails = $this->db->query("
                select 
                    pd.*,
                    p.Product_Name,
                    pc.ProductCategory_Name
                from tbl_lcpurchasedetails pd
                join tbl_product p on p.Product_SlNo = pd.Product_IDNo
                left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
                where pd.PurchaseMaster_IDNo = ?
                and pd.status != 'd'
            ", $purchase->lc_purchase_master_id)->result();
        }

        foreach ($purchases as $item) {
            $item->expDetails = $this->db->query("
                select
                    pd.*,
                    pd.id as eid,
                    exp.name
                from tbl_lcpurchaseexpense pd 
                left join tbl_lc_expense exp on exp.id = pd.exp_id
                where pd.lc_purchase_id = '$item->lc_purchase_master_id'
                and pd.status != 'd'
            ")->result();
        }
        echo json_encode($purchases);
    }

    public function getPendingPurchaseRecord()
    {
        $data = json_decode($this->input->raw_input_stream);
        $branchId = $this->session->userdata("BRANCHid");
        $clauses = "";
        // $status = " and pd.status = 'p'";
        // if (isset($data->status) && $data->status != '') {
        //     $status = " and pd.status = '$data->status'"; ;
        // }

        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and pm.PurchaseMaster_OrderDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->userFullName) && $data->userFullName != '') {
            $clauses .= " and pm.AddBy = '$data->userFullName'";
        }

        if (isset($data->supplierId) && $data->supplierId != '') {
            $clauses .= " and pm.Supplier_SlNo = '$data->supplierId'";
        }

        $purchases = $this->db->query("
            select 
                pm.*,
                s.Supplier_Code,
                s.Supplier_Name,
                s.Supplier_Mobile,
                s.Supplier_Address,
                br.Branch_name,
                ua.User_Name as added_by,
                concat_ws(' - ', ba.account_name, ba.account_number , ba.bank_name) as bank_text,
                ud.User_Name as deleted_by
            from tbl_lcpurchasemaster pm
            left join tbl_supplier s on s.Supplier_SlNo = pm.Supplier_SlNo
            left join tbl_bank_accounts ba on ba.account_id = pm.account_id
            left join tbl_branch br on br.branch_id = pm.branch_id
            left join tbl_user ua on ua.User_SlNo = pm.AddBy
            left join tbl_user ud on ud.User_SlNo = pm.DeletedBy
            where pm.branch_id = '$branchId'
            and pm.status = 'p'
            $clauses
        ")->result();

        foreach ($purchases as $purchase) {
            $purchase->purchaseDetails = $this->db->query("
                select 
                    pd.*,
                    p.Product_Name,
                    pc.ProductCategory_Name
                from tbl_lcpurchasedetails pd
                join tbl_product p on p.Product_SlNo = pd.Product_IDNo
                left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
                where pd.PurchaseMaster_IDNo = ?
                and pd.status = 'p'
            ", $purchase->lc_purchase_master_id)->result();
        }
        foreach ($purchases as $item) {
            $item->expDetails = $this->db->query("
                select
                    pd.*,
                    pd.id as eid,
                    exp.name
                from tbl_lcpurchaseexpense pd 
                left join tbl_lc_expense exp on exp.id = pd.exp_id
                where pd.lc_purchase_id = '$item->lc_purchase_master_id'
                and pd.status != 'd'
            ")->result();
        }
        echo json_encode($purchases);
    }

    public function getLcPurchases()
    {
        $data = json_decode($this->input->raw_input_stream);
        $branchId = $this->session->userdata('BRANCHid');

        $clauses = "";
        $limit = "";
        $status = "a";
        if (isset($data->status) && $data->status != '') {
            $status = $data->status;
        }

        if (isset($data->name) && $data->name != '') {
            $clauses .= " or pm.PurchaseMaster_InvoiceNo like '$data->name%'";
        }
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and pm.PurchaseMaster_OrderDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->supplierId) && $data->supplierId != '') {
            $clauses .= " and pm.Supplier_SlNo = '$data->supplierId'";
        }

        if (isset($data->forSearch) && $data->forSearch != '') {
            $limit .= "limit 20";
        }

        $purchaseIdClause = "";
        if (isset($data->purchaseId) && $data->purchaseId != null) {
            $purchaseIdClause = " and pm.lc_purchase_master_id = '$data->purchaseId'";

            $res['purchaseDetails'] = $this->db->query("
                select
                    pd.*,
                    p.Product_Name,
                    p.Product_Code,
                    p.ProductCategory_ID,
                    p.Product_SellingPrice,
                    pc.ProductCategory_Name,
                    u.Unit_Name
                from tbl_lcpurchasedetails pd 
                left join tbl_product p on p.Product_SlNo = pd.Product_IDNo
                left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
                left join tbl_unit u on u.Unit_SlNo = p.Unit_ID
                where pd.PurchaseMaster_IDNo = '$data->purchaseId'
                and pd.status != 'd'
            ")->result();

            $res['expDetails'] = $this->db->query("
                select
                    pd.*,
                    pd.id as eid,
                    exp.name
                from tbl_lcpurchaseexpense pd 
                left join tbl_lc_expense exp on exp.id = pd.exp_id
                where pd.lc_purchase_id = '$data->purchaseId'
                and pd.status != 'd'
            ")->result();

            $res['cbmCosting'] = $this->db->query("
                select
                    cc.product_coast,
                    pr.Product_Name,
                    pr.Product_Code
                from tbl_cbm_costing cc
                left join tbl_product pr on pr.Product_SlNo  = cc.Product_SlNo
                where cc.Lcc_SlNo = '$data->purchaseId'
                and cc.status != 'd'
            ")->result();

            // $res['lcDutyCosting'] = $this->db->query("
            //     SELECT 
            //         dc.*, p.Product_Name
            //     from tbl_duty_costing dc
            //     left join tbl_product p on p.Product_SlNo = dc.Product_SlNo
            //     where dc.Lcc_SlNo = '$data->purchaseId'
            //     and dc.Status = 'a'
            //     and dc.Costing_BranchId = ?
            // ", $this->brunch)->result();
        }

        $purchases = $this->db->query("
            select
            pm.*,
            ba.account_name,
            ba.account_number,
            ba.bank_name,
            concat(pm.PurchaseMaster_InvoiceNo, ' - ', s.Supplier_Name) as invoice_text,
            concat_ws(' - ', ba.account_name, ba.account_number , ba.bank_name) as bank_text,
            concat(e.Employee_Name, ' - ', e.Employee_ID) as display_emp,
            pm.Supplier_SlNo as Supplier_SlNo,
            pm.Employee_SlNo as empId,
            pur.PurchaseMaster_InvoiceNo as p_purchaseInvoice,
            ps.Supplier_Name as p_supplierName,
            s.Supplier_Name,
            s.Supplier_Mobile,
            s.Supplier_Email,
            s.Supplier_Code,
            s.Supplier_Address,
            ua.User_Name as added_by,
            ud.User_Name as deleted_by
            from tbl_lcpurchasemaster pm
            left join tbl_supplier s on s.Supplier_SlNo = pm.Supplier_SlNo
            left join tbl_purchasemaster pur on pur.PurchaseMaster_SlNo = pm.purchase_id
            left join tbl_supplier ps on ps.Supplier_SlNo = pur.Supplier_SlNo
            left join tbl_employee e on e.Employee_SlNo = pm.Employee_SlNo
            left join tbl_bank_accounts ba on ba.account_id = pm.account_id
            left join tbl_user ua on ua.User_SlNo = pm.AddBy
            left join tbl_user ud on ud.User_SlNo = pm.DeletedBy
            where pm.branch_id = '$branchId'
            and pm.status != 'd'
            $purchaseIdClause $clauses
            order by pm.lc_purchase_master_id desc
            $limit
        ")->result();

        $res['purchases'] = $purchases;
        echo json_encode($res);
    }

    public function getPurchaseDetails()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->supplierId) && $data->supplierId != '') {
            $clauses .= " and s.Supplier_SlNo = '$data->supplierId'";
        }

        if (isset($data->productId) && $data->productId != '') {
            $clauses .= " and p.Product_SlNo = '$data->productId'";
        }

        if (isset($data->categoryId) && $data->categoryId != '') {
            $clauses .= " and pc.ProductCategory_SlNo = '$data->categoryId'";
        }

        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and pm.PurchaseMaster_OrderDate between '$data->dateFrom' and '$data->dateTo'";
        }

        $saleDetails = $this->db->query("
            select 
                pd.*,
                p.Product_Name,
                pc.ProductCategory_Name,
                pm.PurchaseMaster_InvoiceNo,
                pm.PurchaseMaster_OrderDate,
                s.Supplier_Code,
                concat_ws(' - ', ba.account_name, ba.account_number , ba.bank_name) as bank_text,
                s.Supplier_Name
            from tbl_lcpurchasedetails pd
            join tbl_product p on p.Product_SlNo = pd.Product_IDNo
            left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
            join tbl_lcpurchasemaster pm on pm.lc_purchase_master_id = pd.PurchaseMaster_IDNo
            join tbl_bank_accounts ba on ba.account_id = pm.account_id
            join tbl_supplier s on s.Supplier_SlNo = pm.Supplier_SlNo
            where pd.status != 'd'
            and pd.branch_id = '$this->brunch'
            $clauses
        ")->result();

        echo json_encode($saleDetails);
    }

    /*Delete Purchase Record*/
    public function deletePurchase()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $purchase = $this->db->select('*')->where('lc_purchase_master_id', $data->purchaseId)->get('tbl_lcpurchasemaster')->row();
            if ($purchase->status == 'd') {
                $res = ['success' => false, 'message' => 'Purchase not found'];
                echo json_encode($res);
                exit;
            }

            // $returnCount = $this->db->query("select * from tbl_purchasereturn pr where pr.PurchaseMaster_InvoiceNo = ? and pr.status = 'a'", $purchase->PurchaseMaster_InvoiceNo)->num_rows();
            // if ($returnCount != 0) {
            //     $res = ['success' => false, 'message' => 'Unable to delete. Purchase return found'];
            //     echo json_encode($res);
            //     exit;
            // }

            /*Get Purchase Details Data*/
            if ($purchase->status == 'a') {
                $purchaseDetails = $this->db->select('Product_IDNo,PurchaseDetails_TotalQuantity,PurchaseDetails_TotalAmount')->where('PurchaseMaster_IDNo', $data->purchaseId)->get('tbl_lcpurchasedetails')->result();

                foreach ($purchaseDetails as $detail) {
                    $stock = $this->mt->productStock($detail->Product_IDNo);
                    if ($detail->PurchaseDetails_TotalQuantity > $stock) {
                        $res = ['success' => false, 'message' => 'Product out of stock, Purchase can not be deleted'];
                        echo json_encode($res);
                        exit;
                    }
                }
            
                foreach ($purchaseDetails as $product) {
                    $this->db->query("
                        update tbl_currentinventory 
                        set lc_purchase_quantity = lc_purchase_quantity - ? 
                        where product_id = ?
                        and branch_id = ?
                    ", [$product->PurchaseDetails_TotalQuantity, $product->Product_IDNo, $this->session->userdata('BRANCHid')]);
                }
            }

            $purchase = array(
                'status' => 'd',
                'DeletedBy' => $this->session->userdata('userId'),
                'DeletedTime' => date("Y-m-d H:i:s"),
                'last_update_ip' => get_client_ip(),
            );
            /*Delete LC Details*/
            $this->db->set($purchase)->where('PurchaseMaster_IDNo', $data->purchaseId)->update('tbl_lcpurchasedetails');
            /*Delete LC Master Data*/
            $this->db->set($purchase)->where('lc_purchase_master_id', $data->purchaseId)->update('tbl_lcpurchasemaster');

            $this->db->set($purchase)->where('lc_purchase_id', $data->purchaseId)->update('tbl_lcpurchaseexpense');

            $res = ['success' => true, 'message' => 'Successfully deleted'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }
        echo json_encode($res);
    }

    public function approveLcPurchase()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $purchase = $this->db->select('*')->where('lc_purchase_master_id', $data->lcPurchaseId)->get('tbl_lcpurchasemaster')->row();
            if ($purchase->status == 'd') {
                $res = ['success' => false, 'message' => 'Purchase not found'];
                echo json_encode($res);
                exit;
            }

            /*Get Purchase Details Data*/
            // $purchaseDetails = $this->db->select('Product_IDNo,PurchaseDetails_TotalQuantity,PurchaseDetails_TotalAmount')->where('PurchaseMaster_IDNo', $data->lcPurchaseId)->get('tbl_lcpurchasedetails')->result();
            // foreach ($purchaseDetails as $product) {
            //     $inventoryCount = $this->db->query("select * from tbl_currentinventory where product_id = ? and branch_id = ?", [$product->Product_IDNo, $this->session->userdata('BRANCHid')])->num_rows();
            //         if ($inventoryCount == 0) {
            //             $inventory = array(
            //                 'product_id'           => $product->Product_IDNo,
            //                 'lc_purchase_quantity' => $product->PurchaseDetails_TotalQuantity,
            //                 'branch_id'            => $this->session->userdata('BRANCHid')
            //             );
            //             $this->db->insert('tbl_currentinventory', $inventory);
            //         } else {
            //             $this->db->query("
            //                 update tbl_currentinventory 
            //                 set lc_purchase_quantity = lc_purchase_quantity + ? 
            //                 where product_id = ? 
            //                 and branch_id = ?
            //             ", [$product->PurchaseDetails_TotalQuantity, $product->Product_IDNo, $this->session->userdata('BRANCHid')]);
            //         }
            // }
            
            $this->db->query("
                update tbl_lcpurchasemaster 
                    set paid = paid + ?,
                    due = 0.00,
                    paidPercentage = 100
                    where lc_purchase_master_id = ?
            ", [$data->due, $data->lcPurchaseId]);

            $purchaseData = array(
                'status'         => 'a',
                'UpdateBy'       => $this->session->userdata('userId'),
                'UpdateTime'     => date("Y-m-d H:i:s"),
                'last_update_ip' => get_client_ip(),
            );
            
            /*Delete LC Details*/
            // $this->db->set($purchase)->where('PurchaseMaster_IDNo', $data->lcPurchaseId)->update('tbl_lcpurchasedetails');
            /*Delete LC Master Data*/
            $this->db->set($purchaseData)->where('lc_purchase_master_id', $data->lcPurchaseId)->update('tbl_lcpurchasemaster');
            $this->db->set($purchaseData)->where('lc_purchase_id', $data->lcPurchaseId)->update('tbl_lcpurchaseexpense');

            $res = ['success' => true, 'message' => 'Successfully Approved'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }
        echo json_encode($res);
    }

    public function addLCPurchase()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);

            if ($data->purchase->purchaseFor != $this->session->userdata("BRANCHid")) {
                $res = ['success' => false, 'message' => 'You have already changed your branch.', 'branch_status' => false];
                echo json_encode($res);
                exit;
            }

            $invoice = $data->purchase->invoice;
            $invoiceCount = $this->db->query("select * from tbl_lcpurchasemaster where PurchaseMaster_InvoiceNo = ?", $invoice)->num_rows();
            if ($invoiceCount != 0) {
                $invoice = $this->mt->generatelcPurchaseInvoice();
            }

            $purchase = array(
                'PurchaseMaster_InvoiceNo'      => $invoice,
                'Supplier_SlNo'                 => $data->purchase->supplierId,
                'purchase_id'                   => $data->purchase->PurchaseMaster_SlNo,
                // 'purchase_id'                   => $data->purchase->lc_purchase_master_id,
                'lc_no'                         => $data->purchase->lc_no,
                'pi_no'                         => $data->purchase->pi_no,
                'paid'                          => $data->purchase->paid,
                'due'                           => $data->purchase->due,
                'cbm'                           => $data->purchase->cbm,
                'account_id'                    => $data->purchase->account_id,
                'Employee_SlNo'                 => $data->purchase->empId ?? null,
                'paidPercentage'                => $data->purchase->paidPercentage ?? 0,
                'supplierType'                  => 'retail',
                'PurchaseMaster_OrderDate'      => $data->purchase->purchaseDate,
                'PurchaseMaster_PurchaseFor'    => $data->purchase->purchaseFor,
                'PurchaseMaster_TotalAmount'    => $data->purchase->total,
                'PurchaseMaster_Freight'        => $data->purchase->freight,
                'currency_name'                 => $data->purchase->currency_name,
                'currency_rate'                 => $data->purchase->currency_rate,
                'freight_qty'                   => $data->purchase->freight_qty,
                'PurchaseMaster_SubTotalAmount' => $data->purchase->subTotal,
                'PurchaseMaster_Description'    => $data->purchase->note,
                'status'                        => 'p',
                'AddBy'                         => $this->session->userdata("userId"),
                'AddTime'                       => date('Y-m-d H:i:s'),
                'last_update_ip'                => get_client_ip(),
                'branch_id'                     => $this->session->userdata('BRANCHid')
            );
            $this->db->insert('tbl_lcpurchasemaster', $purchase);
            $purchaseId = $this->db->insert_id();

            $bankDetails = array(
                'lc_id'            => $purchaseId,
                'form_type'        => 'LC',
                'amount'           => $data->purchase->total,
                'account_id'       => $data->purchase->account_id,
                'transaction_date' => $data->purchase->purchaseDate,
                'transaction_type' => 'withdraw',
                'note'             => 'LC Payment By Bank',
                'status'           => 1,
                'AddBy'            => $this->session->userdata("userId"),
                'AddTime'          => date('Y-m-d H:i:s'),
                'last_update_ip'   => get_client_ip(),
                'branch_id'        => $this->session->userdata('BRANCHid')
            );
            $this->db->insert('tbl_bank_transactions', $bankDetails);
            
            foreach ($data->cartProducts as $product) {
                $purchaseDetails = array(
                    'PurchaseMaster_IDNo'           => $purchaseId,
                    'Product_IDNo'                  => $product->productId,
                    'unit_id'                       => $product->Unit_ID, 
                    'category_id'                   => $product->categoryId,
                    'PurchaseDetails_TotalQuantity' => $product->quantity,
                    'PurchaseDetails_Rate'          => $product->purchaseRate,
                    'PurchaseDetails_TotalAmount'   => $product->total,
                    'currency_name'                 => $product->currencyName,
                    'currency_value'                => $product->totalForeignAmount ?? 0,
                    'currency_rate'                 => $product->ProductCurrencyRate,
                    'status'                        => 'p',
                    'AddBy'                         => $this->session->userdata("userId"),
                    'AddTime'                       => date('Y-m-d H:i:s'),
                    'last_update_ip'                => get_client_ip(),
                    'branch_id'                     => $this->session->userdata('BRANCHid')
                );
                $this->db->insert('tbl_lcpurchasedetails', $purchaseDetails);
            }

            // if($data->cartExp){
            //     foreach ($data->cartExp as $cartexps) {
            //         $expDetails = array(
            //             'lc_purchase_id' => $purchaseId,
            //             'exp_id'         => $cartexps->expId,
            //             'amount'         => $cartexps->total,
            //             'status'         => 'p',
            //             'AddBy'          => $this->session->userdata("userId"),
            //             'AddTime'        => date('Y-m-d H:i:s'),
            //             'last_update_ip' => get_client_ip(),
            //             'branch_id'      => $this->session->userdata('BRANCHid')
            //         );
            //         $this->db->insert('tbl_lcpurchaseexpense', $expDetails);
            //     }
            // }

            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'LC Added Success', 'purchaseId' => $purchaseId];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }
        echo json_encode($res);
    }

    public function updatePurchase()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();

            $data = json_decode($this->input->raw_input_stream);
            // echo json_encode($data);
            // exit;

            $purchaseId = $data->purchase->purchaseId;

            $purchase = array(
                'PurchaseMaster_InvoiceNo'      => $data->purchase->invoice,
                'Supplier_SlNo'                 => $data->purchase->supplierId,
                'lc_no'                         => $data->purchase->lc_no,
                'pi_no'                         => $data->purchase->pi_no,
                'paid'                          => $data->purchase->paid,
                'due'                           => $data->purchase->due,
                'cbm'                           => $data->purchase->cbm,
                'account_id'                    => $data->purchase->account_id,
                'Employee_SlNo'                 => $data->purchase->empId??null,
                'supplierType'                  => 'retail',
                'PurchaseMaster_OrderDate'      => $data->purchase->purchaseDate,
                'PurchaseMaster_PurchaseFor'    => $data->purchase->purchaseFor,
                'PurchaseMaster_TotalAmount'    => $data->purchase->total,
                'PurchaseMaster_Freight'        => $data->purchase->freight,
                'currency_name'                 => $data->purchase->currency_name,
                'currency_rate'                 => $data->purchase->currency_rate,
                'freight_qty'                   => $data->purchase->freight_qty,
                'PurchaseMaster_SubTotalAmount' => $data->purchase->subTotal,
                'PurchaseMaster_Description'    => $data->purchase->note,
                'UpdateBy' => $this->session->userdata("userId"),
                'UpdateTime' => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
                'branch_id' => $this->session->userdata('BRANCHid')
            );

            $this->db->where('lc_purchase_master_id', $purchaseId)->update('tbl_lcpurchasemaster', $purchase);
            // $purchaseCheckStatus = $this->db->query("select * from tbl_lcpurchasemaster where lc_purchase_master_id = ?", $purchaseId)->row();
            $this->db->query("delete from tbl_lcpurchasedetails where PurchaseMaster_IDNo = ?", $purchaseId);

            foreach ($data->cartProducts as $product) {
                $purchaseDetails = array(
                    'PurchaseMaster_IDNo'           => $purchaseId,
                    'Product_IDNo'                  => $product->productId,
                    'unit_id'                       => $product->Unit_ID, 
                    'category_id'                   => $product->categoryId,
                    'PurchaseDetails_TotalQuantity' => $product->quantity,
                    'PurchaseDetails_Rate'          => $product->purchaseRate,
                    'PurchaseDetails_TotalAmount'   => $product->total,
                    'currency_name'                 => $product->currencyName,
                    'currency_value'                => $product->totalForeignAmount,
                    'currency_rate'                 => $product->ProductCurrencyRate,
                    // 'status'                        => 'p',
                    'AddBy'                         => $this->session->userdata("userId"),
                    'AddTime'                       => date('Y-m-d H:i:s'),
                    'last_update_ip'                => get_client_ip(),
                    'branch_id'                     => $this->session->userdata('BRANCHid')
                );
                $this->db->insert('tbl_lcpurchasedetails', $purchaseDetails);
            }

            $bankDetails = array(
                'amount'           => $data->purchase->total,
                'account_id'       => $data->purchase->account_id,
                'transaction_date' => $data->purchase->purchaseDate,
                'transaction_type' => 'withdraw',
                'note'             => 'LC Payment By Bank',
                'UpdateBy'         => $this->session->userdata("userId"),
                'UpdateTime'       => date('Y-m-d H:i:s'),
                'last_update_ip'   => get_client_ip(),
                'branch_id'        => $this->session->userdata('BRANCHid')
            );

            $this->db->where('lc_id', $purchaseId);
            $this->db->update('tbl_bank_transactions', $bankDetails);

            $this->db->query("delete from tbl_lcpurchaseexpense where lc_purchase_id = ?", $purchaseId);

            // if($data->cartExp) {  
            //     foreach ($data->cartExp as $cartexps) {
            //         $expDetails = array(
            //             'lc_purchase_id' => $purchaseId,
            //             'exp_id'         => $cartexps->expId,
            //             'amount'         => $cartexps->total,
            //             'status'         => $purchaseCheckStatus->status,
            //             'AddBy'          => $this->session->userdata("userId"),
            //             'AddTime'        => date('Y-m-d H:i:s'),
            //             'last_update_ip' => get_client_ip(),
            //             'branch_id'      => $this->session->userdata('BRANCHid')
            //         );
            //         $this->db->insert('tbl_lcpurchaseexpense', $expDetails);
            //     }
            // }

            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'LC update success', 'purchaseId' => $purchaseId];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }
        echo json_encode($res);
    }

    public function lc_purchase_record()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Lc Record";
        $data['content'] = $this->load->view('Administrator/lc_purchase/lc_purchase_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    
    public function purchaseEdit($purchaseId)
    {
        $data['title'] = "LC Update";
        $data['purchaseId'] = $purchaseId;
        $data['invoice'] = $this->db->query("select PurchaseMaster_InvoiceNo from tbl_lcpurchasemaster where lc_purchase_master_id = ?", $purchaseId)->row()->PurchaseMaster_InvoiceNo;
        $data['content'] = $this->load->view('Administrator/lc_purchase/lc_purchase_order', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function lcPurchaseInvoicePrint($purchaseId)
    {
        $data['title'] = "LC Invoice";
        $data['purchaseId'] = $purchaseId;
        $data['content'] = $this->load->view('Administrator/lc_purchase/lc_purchase_to_report', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

     // Product Category 
     public function get_lc_expanses()
     {
         $exps = $this->db->query("select * from tbl_lc_expense where status = 'a' order by id desc")->result();
         echo json_encode($exps);
     }
 
     public function lc_expanse()
     {
         $access = $this->mt->userAccess();
         if (!$access) {
             redirect(base_url());
         }
         $data['title'] = "Add Expense";
         $data['content'] = $this->load->view('Administrator/lc_purchase/add_expense', $data, TRUE);
         $this->load->view('Administrator/index', $data);
     }
     public function insert_lc_expanse()
     {
         $res = ['status' => false];
         try {
             $data = json_decode($this->input->raw_input_stream);
             $query = $this->db->query("SELECT * from tbl_lc_expense where branch_id = '$this->brunch' AND name = '$data->name'")->row();
             if (!empty($query)) {
                 $exp = array(
                     'status'         => 'a',
                     "UpdateBy"       => $this->session->userdata("userId"),
                     "UpdateTime"     => date("Y-m-d H:i:s"),
                     "last_update_ip" => get_client_ip()
                 );
                 $this->db->where('id', $query->id);
                 $this->db->update('tbl_lc_expense', $exp);
             } else {
                 $exp = array(
                     "name"           => $data->name,
                     "note"           => $data->note,
                     "status"         => 'a',
                     "AddBy"          => $this->session->userdata("userId"),
                     "AddTime"        => date("Y-m-d H:i:s"),
                     "last_update_ip" => get_client_ip(),
                     "branch_id"      => $this->brunch
                 );
                 $this->db->insert('tbl_lc_expense', $exp);
             }
 
             $res = ['status' => true, 'message' => 'Expense added successfully'];
         } catch (\Throwable $th) {
             $res = ['status' => false, 'message' => $th->getMessage()];
         }
 
         echo json_encode($res);
     }
     public function update_lc_expanse()
     {
         $res = ['status' => false];
         try {
             $data = json_decode($this->input->raw_input_stream);
             
             $query = $this->db->query("SELECT * from tbl_lc_expense 
                where branch_id = '$this->brunch' 
                AND name = '$data->name' 
                AND id != '$data->id' AND status = 'a'")->row();
             if (!empty($query)) {
                 $res = ['status' => true, 'message' => 'Expense Name Already Exits'];
                 echo json_encode($res);
                 exit;
             }

             $exp = array(
                 "name"           => $data->name,
                 "note"           => $data->note,
                 "UpdateBy"       => $this->session->userdata("userId"),
                 "UpdateTime"     => date("Y-m-d H:i:s"),
                 "last_update_ip" => get_client_ip()
             );
             $this->db->where('id', $data->id);
             $this->db->update('tbl_lc_expense', $exp);
 
             $res = ['status' => true, 'message' => 'Expense update successfully'];
         } catch (\Throwable $th) {
             $res = ['status' => false, 'message' => $th->getMessage()];
         }

         echo json_encode($res);
     }
    public function delete_lc_expanse()
    {
        $data = json_decode($this->input->raw_input_stream);
        $exp = array(
            'status'         => 'd',
            "DeletedBy"      => $this->session->userdata("userId"),
            "DeletedTime"    => date("Y-m-d H:i:s"),
            "last_update_ip" => get_client_ip()
        );
        $this->db->where('id', $data->expId);
        $this->db->update('tbl_lc_expense', $exp);
        echo json_encode(['status' => true, 'message' => 'Expense delete successfully']);
    }

    public function getPurchaseProducts()
    {
        $data = json_decode($this->input->raw_input_stream);

        $pclauses = "";
        if (isset($data->PurchaseId) && $data->PurchaseId != '') {
            $pclauses = " and pd.PurchaseMaster_IDNo = '$data->PurchaseId'";
        }

        $products = $this->db->query("
            select
                pd.PurchaseDetails_TotalQuantity,
                pd.PurchaseDetails_TotalAmount,
                pd.perCBM,
                pd.totalCTN,
                pd.perCTN,
                p.Product_SlNo,
                p.Product_Code,
                p.Product_Name,
                concat(p.Product_Code, ' - ', p.Product_Name) as display_text
            from tbl_purchasedetails pd
            left join tbl_product p on p.Product_SlNo = pd.Product_IDNo
            where pd.status = 'p'
            and pd.branch_id = ?
            $pclauses
            group by pd.Product_IDNo, pd.PurchaseDetails_TotalAmount, pd.PurchaseDetails_TotalQuantity, pd.perCBM, pd.totalCTN, pd.perCTN, pd.status
        ", $this->brunch)->result();       

        $res['products']  = $products;
        echo json_encode($res);
    }

    //  cbm costing -------------------------------
    public function cbmCostingEntry()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Costing";
        $data['content'] = $this->load->view('Administrator/lc_purchase/cbm_costing', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    function addCBMCosting()
    {
        $res = ['success' => false, 'message' => ''];
         try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);

            $productCount = $this->db->query("select * from tbl_cbm_costing where Lcc_SlNo = ? and Item_Type = 'Product' and Product_SlNo = ?", [$data->Lcc_SlNo, $data->Product_SlNo])->num_rows();

            if ($productCount > 0) {
                $cbmCosting = array(
                    'Item_Type'     => $data->Item_Type,
                    'Product_SlNo'  => $data->Product_SlNo,
                    'PurchaseMaster_SlNo' => isset($data->PurchaseMaster_SlNo) && $data->PurchaseMaster_SlNo != null ? $data->PurchaseMaster_SlNo : null,
                    'Quantity'      => $data->Quantity,
                    'expense_coast' => $data->expense_coast,
                    'product_coast' => $data->product_coast,
                    'product_value' => $data->product_value,
                    'total_expense' => $data->total_expense,
                    'total_value'   => $data->total_value,
                    'Status'        => 'a',
                    'UpdateBy'      => $this->session->userdata("FullName"),
                    'UpdateTime'    => date('Y-m-d H:i:s')
                );
                $this->db->where('Product_SlNo', $data->Product_SlNo)->where('Lcc_SlNo', $data->Lcc_SlNo)->update('tbl_cbm_costing', $cbmCosting);
            } else {
                $cbmCosting = array(
                    'Costing_Date'     => $data->Costing_Date,
                    'Lcc_SlNo'         => $data->Lcc_SlNo,
                    'Item_Type'        => $data->Item_Type,
                    'Product_SlNo'     => $data->Product_SlNo,
                    'PurchaseMaster_SlNo' => isset($data->PurchaseMaster_SlNo) && $data->PurchaseMaster_SlNo != null ? $data->PurchaseMaster_SlNo : null,
                    'Quantity'      => $data->Quantity,
                    'expense_coast' => $data->expense_coast,
                    'product_coast' => $data->product_coast,
                    'product_value' => $data->product_value,
                    'total_expense' => $data->total_expense,
                    'total_value'   => $data->total_value,
                    'Status'           => 'a',
                    'AddBy'            => $this->session->userdata("FullName"),
                    'AddTime'          => date('Y-m-d H:i:s'),
                    'Costing_BranchId' => $this->session->userdata('BRANCHid')
                );
                $this->db->insert('tbl_cbm_costing', $cbmCosting);
            }

            // update product purchase price
            $this->db->query("
                update tbl_product
                set Product_Purchase_Rate = ?
                where Product_SlNo = ?
                and branch_id = ?
            ", [$data->product_coast, $data->Product_SlNo, $this->session->userdata('BRANCHid')]);

            // $this->db->query("
            // ")

            // update lc details
            $this->db->query("
                update tbl_lcpurchasedetails
                set status = ?
                where Product_IDNo = ?
                and PurchaseMaster_IDNo = ?
                and branch_id = ?
            ", ['a', $data->Product_SlNo, $data->Lcc_SlNo, $this->session->userdata('BRANCHid')]);

          
            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'Costing inserted successfully.'];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }
        echo json_encode($res);
    }

    public function updateCBMCosting()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $productCount = $this->db->query("SELECT * FROM tbl_cbm_costing WHERE Lcc_SlNo = ? AND Product_SlNo = ? AND Item_Type = 'Product' AND Costing_SlNo != ?", [$data->Lcc_SlNo, $data->Product_SlNo, $data->Costing_SlNo])->num_rows();
            if ($productCount > 0) {
                $cbmCosting = array(
                    'Item_Type'     => $data->Item_Type,
                    'Product_SlNo'  => $data->Product_SlNo,
                    'PurchaseMaster_SlNo' => isset($data->PurchaseMaster_SlNo) && $data->PurchaseMaster_SlNo != null ? $data->PurchaseMaster_SlNo : null,
                    // 'Per_CBM'       => $data->Per_CBM,
                    // 'Per_Ctn'       => $data->Per_Ctn,
                    // 'Total_Ctn'     => $data->Total_Ctn,
                    // 'LC_Cost'       => $data->LC_Cost,
                    // 'CBM_Cost'      => $data->CBM_Cost,
                    // 'Per_CBM_Cost'  => $data->Per_CBM_Cost,
                    // 'Per_Pcs_Cost'  => $data->Per_Pcs_Cost,
                    'Quantity'      => $data->Quantity,
                    'expense_coast' => $data->expense_coast,
                    'product_coast' => $data->product_coast,
                    'product_value' => $data->product_value,
                    'total_expense' => $data->total_expense,
                    'total_value'   => $data->total_value,
                    'Status'        => 'a',
                    'UpdateBy'      => $this->session->userdata("FullName"),
                    'UpdateTime'    => date('Y-m-d H:i:s')
                );
                $this->db->where('Lcc_SlNo', $data->Lcc_SlNo)->where('Product_SlNo', $data->Product_SlNo)->update('tbl_cbm_costing', $cbmCosting);
            } else {
                $cbmCosting = array(
                    'Lcc_SlNo'      => $data->Lcc_SlNo,
                    'Item_Type'     => $data->Item_Type,
                    'Product_SlNo'  => $data->Product_SlNo,
                    'PurchaseMaster_SlNo' => isset($data->PurchaseMaster_SlNo) && $data->PurchaseMaster_SlNo != null ? $data->PurchaseMaster_SlNo : null,
                    // 'Per_CBM'       => $data->Per_CBM,
                    // 'Per_Ctn'       => $data->Per_Ctn,
                    // 'Total_Ctn'     => $data->Total_Ctn,
                    // 'LC_Cost'       => $data->LC_Cost,
                    // 'CBM_Cost'      => $data->CBM_Cost,
                    // 'Per_CBM_Cost'  => $data->Per_CBM_Cost,
                    // 'Per_Pcs_Cost'  => $data->Per_Pcs_Cost,
                    'Quantity'      => $data->Quantity,
                    'expense_coast' => $data->expense_coast,
                    'product_coast' => $data->product_coast,
                    'product_value' => $data->product_value,
                    'total_expense' => $data->total_expense,
                    'total_value'   => $data->total_value,
                    'Status'        => 'a',
                    'UpdateBy'      => $this->session->userdata("FullName"),
                    'UpdateTime'    => date('Y-m-d H:i:s')
                );
                $this->db->where('Costing_SlNo', $data->Costing_SlNo)->update('tbl_cbm_costing', $cbmCosting);
            }

            $res = ['success' => true, 'message' => 'Costing updated successfully.'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }
        echo json_encode($res);
    }

    public function getCBMCostings()
    {
        $costings = $this->db->query("
            SELECT
                cc.*,
                p.Product_Code,
                p.Product_Name,
                CONCAT(p.Product_Code, ' - ', p.Product_Name) AS display_name,
                pm.PurchaseMaster_InvoiceNo,
                s.Supplier_Name,
                concat(pm.PurchaseMaster_InvoiceNo, ' - ', s.Supplier_Name) as invoice_text,
                lc.PurchaseMaster_InvoiceNo as Lcc_No
            FROM tbl_cbm_costing cc
            LEFT JOIN tbl_lcpurchasemaster lc ON lc.lc_purchase_master_id = cc.Lcc_SlNo
            LEFT JOIN tbl_product p ON p.Product_SlNo = cc.Product_SlNo
            LEFT JOIN tbl_purchasemaster pm ON pm.PurchaseMaster_SlNo = cc.PurchaseMaster_SlNo
            LEFT JOIN tbl_supplier s ON s.Supplier_SlNo = pm.Supplier_SlNo
            WHERE cc.Status = 'a'
            AND cc.Costing_BranchId = ?
            ORDER BY cc.Costing_SlNo DESC
        ", $this->brunch)->result();
        echo json_encode($costings);
    }

    public function deleteCBMCosting()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $costingId = $data->costingId;

            $updateData = [
                'Status'     => 'd',
                'UpdateBy'   => $this->session->userdata("FullName"),
                'UpdateTime' => date('Y-m-d H:i:s')
            ];

            $this->db->where('Costing_SlNo', $costingId)->update('tbl_cbm_costing', $updateData);

            $res = ['success' => true, 'message' => 'CBM costing deleted successfully'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }
        echo json_encode($res);
    }

    // Duty Costing------------------
    public function dutyCostingEntry()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Add Duty Costing";
        $data['content'] = $this->load->view('Administrator/lc_purchase/duty_costing', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    function addDutyCosting()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $productCount = $this->db->query("SELECT * FROM tbl_duty_costing WHERE PurchaseMaster_SlNo = ? and Item_Type = 'Product' AND Product_SlNo = ?", [$data->PurchaseMaster_SlNo, $data->Product_SlNo])->num_rows();
            if ($productCount > 0) {
                $dutyCosting = array(
                    'Item_Type'     => $data->Item_Type,
                    'Product_SlNo'  => $data->Product_SlNo,
                    'PurchaseMaster_SlNo' => isset($data->PurchaseMaster_SlNo) && $data->PurchaseMaster_SlNo != null ? $data->PurchaseMaster_SlNo : null,
                    'Pcs_Kg'        => $data->Pcs_Kg,
                    'Per_USD'       => $data->Per_USD,
                    'Total_Dollar'  => $data->Total_Dollar,
                    'Currency_Rate' => $data->Currency_Rate,
                    'Total_BDT'     => $data->Total_BDT,
                    'Percentage'    => $data->Percentage,
                    'Total_Amount'  => $data->Total_Amount,
                    'quantity'      => $data->quantity,
                    'perPcsCosting' => $data->perPcsCosting,
                    'Status'        => 'a',
                    'UpdateBy'      => $this->session->userdata("FullName"),
                    'UpdateTime'    => date('Y-m-d H:i:s')
                );
                $this->db->where('PurchaseMaster_SlNo', $data->PurchaseMaster_SlNo)->where('Product_SlNo', $data->Product_SlNo)->update('tbl_duty_costing', $dutyCosting);
            } else {
                $dutyCosting = array(
                    'Costing_Date'     => $data->Costing_Date,
                    'Lcc_SlNo'         => $data->Lcc_SlNo,
                    'Item_Type'        => $data->Item_Type,
                    'Product_SlNo'     => $data->Product_SlNo,
                    'PurchaseMaster_SlNo' => isset($data->PurchaseMaster_SlNo) && $data->PurchaseMaster_SlNo != null ? $data->PurchaseMaster_SlNo : null,
                    'Pcs_Kg'           => $data->Pcs_Kg,
                    'Per_USD'          => $data->Per_USD,
                    'Total_Dollar'     => $data->Total_Dollar,
                    'Currency_Rate'    => $data->Currency_Rate,
                    'Total_BDT'        => $data->Total_BDT,
                    'Percentage'       => $data->Percentage,
                    'Total_Amount'     => $data->Total_Amount,
                    'quantity'         => $data->quantity,
                    'perPcsCosting'    => $data->perPcsCosting,
                    'Status'           => 'a',
                    'AddBy'            => $this->session->userdata("FullName"),
                    'AddTime'          => date('Y-m-d H:i:s'),
                    'Costing_BranchId' => $this->session->userdata('BRANCHid')
                );
                $this->db->insert('tbl_duty_costing', $dutyCosting);
            }
            

            $res = ['success' => true, 'message' => 'Duty costing inserted successfully.'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateDutyCosting()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            // return;

            $productCount = $this->db->query("SELECT * FROM tbl_duty_costing WHERE PurchaseMaster_SlNo = ? AND Item_Type = 'Product' AND Product_SlNo = ? AND Costing_SlNo != ?", [$data->PurchaseMaster_SlNo, $data->Product_SlNo, $data->Costing_SlNo])->num_rows();
            if ($productCount > 0) {
                $dutyCosting = array(
                    'Item_Type'     => $data->Item_Type,
                    'Product_SlNo'  => $data->Product_SlNo,
                    'PurchaseMaster_SlNo' => isset($data->PurchaseMaster_SlNo) && $data->PurchaseMaster_SlNo != null ? $data->PurchaseMaster_SlNo : null,
                    'Pcs_Kg'        => $data->Pcs_Kg,
                    'Per_USD'       => $data->Per_USD,
                    'Total_Dollar'  => $data->Total_Dollar,
                    'Currency_Rate' => $data->Currency_Rate,
                    'Total_BDT'     => $data->Total_BDT,
                    'Percentage'    => $data->Percentage,
                    'Total_Amount'  => $data->Total_Amount,
                    'quantity'      => $data->quantity,
                    'perPcsCosting' => $data->perPcsCosting,
                    'Status'        => 'a',
                    'UpdateBy'      => $this->session->userdata("FullName"),
                    'UpdateTime'    => date('Y-m-d H:i:s')
                );
                $this->db->where('PurchaseMaster_SlNo', $data->PurchaseMaster_SlNo)->where('Product_SlNo', $data->Product_SlNo)->update('tbl_duty_costing', $dutyCosting);
            } else {
                $dutyCosting = array(
                    'Lcc_SlNo'      => $data->Lcc_SlNo,
                    'Item_Type'     => $data->Item_Type,
                    'Product_SlNo'  => $data->Product_SlNo,
                    'PurchaseMaster_SlNo' => isset($data->PurchaseMaster_SlNo) && $data->PurchaseMaster_SlNo != null ? $data->PurchaseMaster_SlNo : null,
                    'Pcs_Kg'        => $data->Pcs_Kg,
                    'Per_USD'       => $data->Per_USD,
                    'Total_Dollar'  => $data->Total_Dollar,
                    'Currency_Rate' => $data->Currency_Rate,
                    'Total_BDT'     => $data->Total_BDT,
                    'Percentage'    => $data->Percentage,
                    'Total_Amount'  => $data->Total_Amount,
                    'quantity'      => $data->quantity,
                    'perPcsCosting' => $data->perPcsCosting,
                    'Status'        => 'a',
                    'UpdateBy'      => $this->session->userdata("FullName"),
                    'UpdateTime'    => date('Y-m-d H:i:s')
                );
                $this->db->where('Costing_SlNo', $data->Costing_SlNo)->update('tbl_duty_costing', $dutyCosting);
            }                
            
            $res = ['success' => true, 'message' => 'Duty costing updated successfully.'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }
        echo json_encode($res);
    }

    public function getDutyCostings()
    {
        $clauses = "";
        
        if (isset($data->lotId) && $data->lotId != '') {
            $clauses .= " and pm.lot_id = '$data->lotId'";
        }
        if (isset($data->productId) && $data->productId != '') {
            $clauses .= " and dc.Product_SlNo = '$data->productId'";
        }

        $costings = $this->db->query("
            SELECT
                dc.*,
                p.Product_Code,
                p.Product_Name,
                concat(p.Product_Code, ' - ', p.Product_Name) as display_name,
                pm.PurchaseMaster_InvoiceNo,
                s.Supplier_Name,
                concat_ws(' - ', pm.PurchaseMaster_InvoiceNo, s.Supplier_Name) as invoice_text,
                lc.PurchaseMaster_InvoiceNo as Lcc_No
            FROM tbl_duty_costing dc
            LEFT JOIN tbl_lcpurchasemaster lc ON lc.lc_purchase_master_id = dc.Lcc_SlNo
            LEFT JOIN tbl_product p ON p.Product_SlNo = dc.Product_SlNo
            LEFT JOIN tbl_purchasemaster pm ON pm.PurchaseMaster_SlNo = dc.PurchaseMaster_SlNo
            LEFT JOIN tbl_supplier s ON s.Supplier_SlNo = pm.Supplier_SlNo
            WHERE dc.Status = 'a'
            AND dc.Costing_BranchId = ?
            $clauses
            ORDER BY dc.Costing_SlNo DESC
        ", $this->brunch)->result();

        echo json_encode($costings);
    }

    public function getDutyCostingProduct()
    {
        $data = json_decode($this->input->raw_input_stream);
        $clauses = "";
        
        if (isset($data->lotId) && $data->lotId != '') {
            $clauses .= " and pm.lot_id = '$data->lotId'";
        }
        if (isset($data->productId) && $data->productId != '') {
            $clauses .= " and pd.Product_IDNo = '$data->productId'";
        }

        $costings = $this->db->query("
            select
                pd.*,
                p.Product_Name,
                p.Product_Code,
                p.ProductCategory_ID,
                p.Product_SellingPrice,
                pc.ProductCategory_Name,
                u.Unit_Name,

                (select ifnull(sum(cc.Per_Pcs_Cost), 0) from tbl_cbm_costing cc
                    where cc.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo 
                    and cc.Product_SlNo = p.Product_SlNo 
                    and cc.Costing_BranchId = '$this->brunch'
                ) as total_cbm_costing,

                (select ifnull(sum(dc.perPcsCosting), 0) from tbl_duty_costing dc
                    where dc.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo
                    and dc.Product_SlNo = p.Product_SlNo
                    and dc.Costing_BranchId = '$this->brunch'
                ) as total_duty_costing,

                (select (pd.PurchaseDetails_Rate + total_cbm_costing + total_duty_costing)) as total_purchase_price
            from tbl_purchasedetails pd 
            left join tbl_purchasemaster pm on pm.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo
            left join tbl_product p on p.Product_SlNo = pd.Product_IDNo
            left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
            left join tbl_unit u on u.Unit_SlNo = p.Unit_ID
            where pd.PurchaseDetails_branchID = '$this->brunch'
            $clauses
        ", $this->brunch)->result();

        echo json_encode($costings);
    }

    public function deleteDutyCosting()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $costingId = $data->costingId;

            $updateData = [
                'Status'     => 'd',
                'UpdateBy'   => $this->session->userdata("FullName"),
                'UpdateTime' => date('Y-m-d H:i:s')
            ];

            $this->db->where('Costing_SlNo', $costingId)->update('tbl_duty_costing', $updateData);

            $res = ['success' => true, 'message' => 'Duty costing deleted successfully'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function costingInvoice()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Costing Invoice";
        $data['content'] = $this->load->view('Administrator/lc_purchase/costing_invoice', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getCostingDetails()
    {
        $data = json_decode($this->input->raw_input_stream);
        $branchId = $this->session->userdata('BRANCHid');

        $clauses = "";
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and pm.PurchaseMaster_OrderDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->supplierId) && $data->supplierId != '') {
            $clauses .= " and pm.Supplier_SlNo = '$data->supplierId'";
        }

        $purchaseIdClause = "";
        if (isset($data->purchaseId) && $data->purchaseId != null) {
            $purchaseIdClause = " and pm.PurchaseMaster_SlNo = '$data->purchaseId'";

            $query = $this->db->query("
                    select
                        pd.*,
                        p.Product_Name,
                        p.Product_Code,
                        p.ProductCategory_ID,
                        p.Product_SellingPrice,
                        pc.ProductCategory_Name,
                        u.Unit_Name,

                        (select ifnull(sum(cc.Per_Pcs_Cost), 0) from tbl_cbm_costing cc
                         where cc.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo 
                         and cc.Product_SlNo = p.Product_SlNo 
                         and cc.Costing_BranchId = '$branchId'
                        ) as total_cbm_costing,

                        (select ifnull(sum(dc.perPcsCosting), 0) from tbl_duty_costing dc
                         where dc.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo
                         and dc.Product_SlNo = p.Product_SlNo
                         and dc.Costing_BranchId = '$branchId'
                        ) as total_duty_costing,

                        (select (pd.PurchaseDetails_Rate + total_cbm_costing + total_duty_costing)) as total_purchase_price
                    from tbl_purchasedetails pd 
                    left join tbl_product p on p.Product_SlNo = pd.Product_IDNo
                    left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
                    left join tbl_unit u on u.Unit_SlNo = p.Unit_ID
                    where pd.PurchaseMaster_IDNo = '$data->purchaseId'
                    and pd.branch_id = '$branchId'
            ");

            if (!$query) {
                // Output the error if query fails
                echo $this->db->error();
                return;
            }

            $res['purchaseDetails'] = $query->result();
        }

        $purchases = $this->db->query("
            select
            concat(pm.PurchaseMaster_InvoiceNo, ' - ', s.Supplier_Name) as invoice_text,
            pm.*,
            s.Supplier_Name,
            s.Supplier_Mobile,
            s.Supplier_Email,
            s.Supplier_Code,
            s.Supplier_Address,
            s.Supplier_Type
            from tbl_purchasemaster pm
            join tbl_supplier s on s.Supplier_SlNo = pm.Supplier_SlNo
            where pm.branch_id = '$branchId' 
            and pm.status != 'd'
            $purchaseIdClause $clauses
            order by pm.PurchaseMaster_SlNo desc
        ")->result();

        $res['purchases'] = $purchases;
        echo json_encode($res);
    }

    // LC purchase expense -----------
    public function lcExpanseEntry() {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "LC Expense";
        $data['content'] = $this->load->view('Administrator/lc_purchase/expense_entry', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getLCPurchaseExpanses(){
        $branchId = $this->session->userdata('BRANCHid');
        $query = $this->db->query("
                select 
                pex.*,
                lc.PurchaseMaster_InvoiceNo,
                ex.name
                from tbl_lcpurchaseexpense pex 
                left join tbl_lcpurchasemaster lc on lc.lc_purchase_master_id = pex.lc_purchase_id
                left join tbl_lc_expense ex on ex.id = pex.exp_id
                where pex.status != 'd'
                and pex.branch_id = '$branchId'
        ");
        $res['LCpurchaseExpense'] = $query->result();
        echo json_encode($res);
    }

    public function addLCPurchaseExpanse() {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);

            $expDetails = array(
                'lc_purchase_id' => $data->lc_purchase_id,
                'exp_id'         => $data->exp_id,
                'amount'         => $data->amount,
                'status'         => 'p',
                'AddBy'          => $this->session->userdata("userId"),
                'AddTime'        => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
                'branch_id'      => $this->session->userdata('BRANCHid')
            );
            $this->db->insert('tbl_lcpurchaseexpense', $expDetails);

            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'LC Purchase Expense Added Success'];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }
        echo json_encode($res);
    }

    public function updateLCPurchaseExpanse() {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);

            $expDetails = array(
                'lc_purchase_id' => $data->lc_purchase_id,
                'exp_id'         => $data->exp_id,
                'amount'         => $data->amount,
                // 'status'         => $data->status,
                'UpdateBy'       => $this->session->userdata("userId"),
                'UpdateTime'     => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
                'branch_id'      => $this->session->userdata('BRANCHid')
            );

            $this->db->where('id', $data->id)->update('tbl_lcpurchaseexpense', $expDetails);
            
            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'LC Purchase Expense Updated Success'];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }
        echo json_encode($res);
    }

    public function deleteLCPurchaseExpnese()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $updateData = [
                'status'     => 'd',
                'DeletedBy'   =>  $this->session->userdata("userId"),
                'last_update_ip' => get_client_ip(),
                'DeletedTime' => date('Y-m-d H:i:s')
            ];
            $this->db->where('id', $data->expId)->update('tbl_lcpurchaseexpense', $updateData);
            $res = ['success' => true, 'message' => 'LC Purchase Expense Deleted Successfully'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex ->getMessage()];
        }
        echo json_encode($res);
    }
}
