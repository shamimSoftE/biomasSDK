<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Purchase extends CI_Controller
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

    public function getPurchaseRecord()
    {
        $data = json_decode($this->input->raw_input_stream);
        $branchId = $this->session->userdata("BRANCHid");
        $clauses = "";
        $status = "a";
        if (isset($data->status) && $data->status != '') {
            $status = $data->status;
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
                ud.User_Name as deleted_by
            from tbl_purchasemaster pm
            left join tbl_supplier s on s.Supplier_SlNo = pm.Supplier_SlNo
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
                from tbl_purchasedetails pd
                join tbl_product p on p.Product_SlNo = pd.Product_IDNo
                left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
                where pd.PurchaseMaster_IDNo = ?
                and pd.status != 'd'
            ", $purchase->PurchaseMaster_SlNo)->result();
        }

        echo json_encode($purchases);
    }

    public function getPurchases()
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
            $purchaseIdClause = " and pm.PurchaseMaster_SlNo = '$data->purchaseId'";

            $res['purchaseDetails'] = $this->db->query("
                select
                    pd.*,
                    p.Product_Name,
                    p.Product_Code,
                    p.ProductCategory_ID,
                    p.Product_SellingPrice,
                    pc.ProductCategory_Name,
                    u.Unit_Name
                from tbl_purchasedetails pd 
                join tbl_product p on p.Product_SlNo = pd.Product_IDNo
                left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
                join tbl_unit u on u.Unit_SlNo = p.Unit_ID
                where pd.PurchaseMaster_IDNo = '$data->purchaseId'
                and pd.status != 'd'
            ")->result();
        }
        $purchases = $this->db->query("
            select
            concat(pm.PurchaseMaster_InvoiceNo, ' - ', ifnull(s.Supplier_Name, pm.supplierName)) as invoice_text,
            pm.*,
            ifnull(pm.Supplier_SlNo, '') as Supplier_SlNo,
            ifnull(s.Supplier_Name, pm.supplierName) as Supplier_Name,
            ifnull(s.Supplier_Mobile, pm.supplierMobile) as Supplier_Mobile,
            s.Supplier_Email,
            ifnull(s.Supplier_Code, 'Cash Supplier') as Supplier_Code,
            ifnull(s.Supplier_Address, pm.supplierAddress) as Supplier_Address,
            ua.User_Name as added_by,
            ud.User_Name as deleted_by
            from tbl_purchasemaster pm
            left join tbl_supplier s on s.Supplier_SlNo = pm.Supplier_SlNo
            left join tbl_user ua on ua.User_SlNo = pm.AddBy
            left join tbl_user ud on ud.User_SlNo = pm.DeletedBy
            where pm.branch_id = '$branchId' 
            and pm.status != 'd'
            $purchaseIdClause $clauses
            order by pm.PurchaseMaster_SlNo desc
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
                s.Supplier_Name
            from tbl_purchasedetails pd
            join tbl_product p on p.Product_SlNo = pd.Product_IDNo
            left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
            join tbl_purchasemaster pm on pm.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo
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
            $purchase = $this->db->select('*')->where('PurchaseMaster_SlNo', $data->purchaseId)->get('tbl_purchasemaster')->row();
            if ($purchase->status != 'a') {
                $res = ['success' => false, 'message' => 'Purchase not found'];
                echo json_encode($res);
                exit;
            }

            $returnCount = $this->db->query("select * from tbl_purchasereturn pr where pr.PurchaseMaster_InvoiceNo = ? and pr.status = 'a'", $purchase->PurchaseMaster_InvoiceNo)->num_rows();
            if ($returnCount != 0) {
                $res = ['success' => false, 'message' => 'Unable to delete. Purchase return found'];
                echo json_encode($res);
                exit;
            }

            /*Get Purchase Details Data*/
            $purchaseDetails = $this->db->select('Product_IDNo,PurchaseDetails_TotalQuantity,PurchaseDetails_TotalAmount')->where('PurchaseMaster_IDNo', $data->purchaseId)->get('tbl_purchasedetails')->result();

            foreach ($purchaseDetails as $detail) {
                $stock = $this->mt->productStock($detail->Product_IDNo);
                if ($detail->PurchaseDetails_TotalQuantity > $stock) {
                    $res = ['success' => false, 'message' => 'Product out of stock, Purchase can not be deleted'];
                    echo json_encode($res);
                    exit;
                }
            }

            foreach ($purchaseDetails as $product) {
                $previousStock = $this->mt->productStock($product->Product_IDNo);

                $this->db->query("
                    update tbl_currentinventory 
                    set purchase_quantity = purchase_quantity - ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$product->PurchaseDetails_TotalQuantity, $product->Product_IDNo, $this->session->userdata('BRANCHid')]);

                $this->db->query("
                    update tbl_product set 
                    Product_Purchase_Rate = (((Product_Purchase_Rate * ?) - ?) / ?)
                    where Product_SlNo = ?
                ", [
                    $previousStock,
                    $product->PurchaseDetails_TotalAmount,
                    ($previousStock - $product->PurchaseDetails_TotalQuantity),
                    $product->Product_IDNo
                ]);
            }

            $purchase = array(
                'status' => 'd',
                'DeletedBy' => $this->session->userdata('userId'),
                'DeletedTime' => date("Y-m-d H:i:s"),
                'last_update_ip' => get_client_ip(),
            );
            /*Delete Purchase Details*/
            $this->db->set($purchase)->where('PurchaseMaster_IDNo', $data->purchaseId)->update('tbl_purchasedetails');
            /*Delete Purchase Master Data*/
            $this->db->set($purchase)->where('PurchaseMaster_SlNo', $data->purchaseId)->update('tbl_purchasemaster');

            $res = ['success' => true, 'message' => 'Successfully deleted'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function getPurchaseDetailsForReturn()
    {
        $data = json_decode($this->input->raw_input_stream);
        $purchaseDetails = $this->db->query("
            select 
                pd.*,
                pd.PurchaseDetails_Rate as return_rate,
                p.Product_Name,
                pc.ProductCategory_Name,
                (
                    select ifnull(sum(prd.PurchaseReturnDetails_ReturnQuantity), 0) 
                    from tbl_purchasereturndetails prd
                    join tbl_purchasereturn pr on pr.PurchaseReturn_SlNo = prd.PurchaseReturn_SlNo
                    where pr.PurchaseMaster_InvoiceNo = pm.PurchaseMaster_InvoiceNo
                    and prd.PurchaseReturnDetailsProduct_SlNo = pd.Product_IDNo
                    and prd.status = 'a'
                ) as returned_quantity,
                (
                    select ifnull(sum(prd.PurchaseReturnDetails_ReturnAmount), 0) 
                    from tbl_purchasereturndetails prd
                    join tbl_purchasereturn pr on pr.PurchaseReturn_SlNo = prd.PurchaseReturn_SlNo
                    where pr.PurchaseMaster_InvoiceNo = pm.PurchaseMaster_InvoiceNo
                    and prd.PurchaseReturnDetailsProduct_SlNo = pd.Product_IDNo
                    and prd.status = 'a'
                ) as returned_amount
            from tbl_purchasedetails pd
            join tbl_purchasemaster pm on pm.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo
            join tbl_product p on p.Product_SlNo = pd.Product_IDNo
            left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
            where pm.PurchaseMaster_SlNo = ?
        ", $data->purchaseId)->result();

        echo json_encode($purchaseDetails);
    }

    public function addPurchaseReturn()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);
            $purchaseReturn = array(
                'PurchaseMaster_InvoiceNo'    => $data->invoice->PurchaseMaster_InvoiceNo,
                'Supplier_IDdNo'              => $data->invoice->supplierType == "G" ? NULL : $data->invoice->Supplier_SlNo,
                'PurchaseReturn_ReturnDate'   => $data->purchaseReturn->returnDate,
                'PurchaseReturn_ReturnAmount' => $data->purchaseReturn->total,
                'PurchaseReturn_Description'  => $data->purchaseReturn->note,
                'status'                      => 'a',
                'AddBy'                       => $this->session->userdata("userId"),
                'AddTime'                     => date('Y-m-d H:i:s'),
                "last_update_ip"              => get_client_ip(),
                'branch_id'                   => $this->session->userdata('BRANCHid')
            );

            $this->db->insert('tbl_purchasereturn', $purchaseReturn);
            $purchaseReturnId = $this->db->insert_id();

            $totalReturnAmount = 0;
            foreach ($data->cart as $product) {
                $returnDetails = array(
                    'PurchaseReturn_SlNo' => $purchaseReturnId,
                    'PurchaseReturnDetailsProduct_SlNo' => $product->Product_IDNo,
                    'PurchaseReturnDetails_ReturnQuantity' => $product->return_quantity,
                    'PurchaseReturnDetails_ReturnAmount' => $product->return_amount,
                    'status' => 'a',
                    'AddBy' => $this->session->userdata("userId"),
                    'AddTime' => date('Y-m-d H:i:s'),
                    "last_update_ip" => get_client_ip(),
                    'branch_id' => $this->session->userdata('BRANCHid')
                );

                $this->db->insert('tbl_purchasereturndetails', $returnDetails);

                $totalReturnAmount += $product->return_amount;

                $this->db->query("
                    update tbl_currentinventory 
                    set purchase_return_quantity = purchase_return_quantity + ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$product->return_quantity, $product->Product_IDNo, $this->session->userdata('BRANCHid')]);
            }

            $supplierInfo = $this->db->query("select * from tbl_supplier where Supplier_SlNo = ?", $data->invoice->Supplier_SlNo)->row();
            if ($data->invoice->supplierType == 'G') {
                $customerPayment = array(
                    'SPayment_date' => $data->purchaseReturn->returnDate,
                    'SPayment_invoice' => $data->invoice->PurchaseMaster_InvoiceNo,
                    'SPayment_customerID' => $data->invoice->Supplier_SlNo,
                    'SPayment_TransactionType' => 'CR',
                    'SPayment_Paymentby' => 'cash',
                    'SPayment_amount' => $totalReturnAmount,
                    'status' => 'a',
                    'Addby' => $this->session->userdata("userId"),
                    'AddTime' => date('Y-m-d H:i:s'),
                    "last_update_ip" => get_client_ip(),
                    'branch_id' => $this->session->userdata("BRANCHid"),
                );

                $this->db->insert('tbl_supplier_payment', $customerPayment);
            }
            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'Purchase return success', 'id' => $purchaseReturnId];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }
    public function updatePurchaseReturn()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);
            $purchaseReturnId = $data->purchaseReturn->returnId;

            $oldReturn = $this->db->query("select * from tbl_purchasereturn where PurchaseReturn_SlNo = ?", $purchaseReturnId)->row();

            $purchaseReturn = array(
                'PurchaseMaster_InvoiceNo' => $data->invoice->PurchaseMaster_InvoiceNo,
                'Supplier_IDdNo' => $data->invoice->supplierType == 'G' ? NULL : $data->invoice->Supplier_SlNo,
                'PurchaseReturn_ReturnDate' => $data->purchaseReturn->returnDate,
                'PurchaseReturn_ReturnAmount' => $data->purchaseReturn->total,
                'PurchaseReturn_Description' => $data->purchaseReturn->note,
                'status' => 'a',
                'UpdateBy' => $this->session->userdata("userId"),
                'UpdateTime' => date('Y-m-d H:i:s'),
                "last_update_ip" => get_client_ip(),
                'branch_id' => $this->session->userdata('BRANCHid'),
            );

            $this->db->where('PurchaseReturn_SlNo', $purchaseReturnId)->update('tbl_purchasereturn', $purchaseReturn);

            $oldDetails = $this->db->query("select * from tbl_purchasereturndetails prd where prd.PurchaseReturn_SlNo = ?", $purchaseReturnId)->result();

            foreach ($oldDetails as $product) {
                $this->db->query("
                    update tbl_currentinventory 
                    set purchase_return_quantity = purchase_return_quantity - ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$product->PurchaseReturnDetails_ReturnQuantity, $product->PurchaseReturnDetailsProduct_SlNo, $this->session->userdata('BRANCHid')]);
            }

            $this->db->query("delete from tbl_purchasereturndetails where PurchaseReturn_SlNo = ?", $purchaseReturnId);
            $totalReturnAmount = 0;
            foreach ($data->cart as $product) {
                $returnDetails = array(
                    'PurchaseReturn_SlNo' => $purchaseReturnId,
                    'PurchaseReturnDetailsProduct_SlNo' => $product->Product_IDNo,
                    'PurchaseReturnDetails_ReturnQuantity' => $product->return_quantity,
                    'PurchaseReturnDetails_ReturnAmount' => $product->return_amount,
                    'status' => 'a',
                    'UpdateBy' => $this->session->userdata("userId"),
                    'UpdateTime' => date('Y-m-d H:i:s'),
                    "last_update_ip" => get_client_ip(),
                    'branch_id' => $this->session->userdata('BRANCHid')
                );

                $this->db->insert('tbl_purchasereturndetails', $returnDetails);

                $totalReturnAmount += $product->return_amount;

                $this->db->query("
                    update tbl_currentinventory 
                    set purchase_return_quantity = purchase_return_quantity + ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$product->return_quantity, $product->Product_IDNo, $this->session->userdata('BRANCHid')]);
            }

            $supplierInfo = $this->db->query("select * from tbl_supplier where Supplier_SlNo = ?", $data->invoice->Supplier_SlNo)->row();
            if ($data->invoice->supplierType == 'G') {

                $this->db->query("
                    delete from tbl_supplier_payment 
                    where SPayment_invoice = ? 
                    and SPayment_customerID = ?
                    and SPayment_amount = ?
                    limit 1
                ", [
                    $data->invoice->PurchaseMaster_InvoiceNo,
                    $data->invoice->Supplier_SlNo,
                    $oldReturn->PurchaseReturn_ReturnAmount
                ]);

                $customerPayment = array(
                    'SPayment_date'            => $data->purchaseReturn->returnDate,
                    'SPayment_invoice'         => $data->invoice->PurchaseMaster_InvoiceNo,
                    'SPayment_customerID'      => $data->invoice->supplierType == 'G' ? NULL : $data->invoice->Supplier_SlNo,
                    'SPayment_TransactionType' => 'CR',
                    'SPayment_Paymentby'       => 'cash',
                    'SPayment_amount'          => $totalReturnAmount,
                    'status'                   => 'a',
                    'Addby'                    => $this->session->userdata("userId"),
                    'AddTime'                  => date('Y-m-d H:i:s'),
                    'last_update_ip'           => get_client_ip(),
                    'branch_id'                => $this->session->userdata("BRANCHid"),
                );

                $this->db->insert('tbl_supplier_payment', $customerPayment);
            }

            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'Purchase return updated', 'id' => $purchaseReturnId];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function getPurchaseReturnDetails()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and pr.PurchaseReturn_ReturnDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->supplierId) && $data->supplierId != '') {
            $clauses .= " and pr.Supplier_IDdNo = '$data->supplierId'";
        }

        if (isset($data->productId) && $data->productId != '') {
            $clauses .= " and prd.PurchaseReturnDetailsProduct_SlNo = '$data->productId'";
        }

        $returnDetails = $this->db->query("
            select 
                prd.*,
                p.Product_Code,
                p.Product_Name,
                pr.PurchaseMaster_InvoiceNo,
                pr.PurchaseReturn_ReturnDate,
                pr.Supplier_IDdNo,
                pr.PurchaseReturn_Description,
                s.Supplier_Code,
                s.Supplier_Name
            from tbl_purchasereturndetails prd
            join tbl_product p on p.Product_SlNo = prd.PurchaseReturnDetailsProduct_SlNo
            join tbl_purchasereturn pr on pr.PurchaseReturn_SlNo = prd.PurchaseReturn_SlNo
            left join tbl_supplier s on s.Supplier_SlNo = pr.Supplier_IDdNo
            where pr.branch_id = ?
            $clauses
        ", $this->session->userdata('BRANCHid'))->result();

        echo json_encode($returnDetails);
    }

    public function order()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Purchase Order";

        $invoice = $this->mt->generatePurchaseInvoice();

        $data['purchaseId'] = 0;
        $data['invoice'] = $invoice;
        $data['content'] = $this->load->view('Administrator/purchase/purchase_order', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function purchaseEdit($purchaseId)
    {
        $data['title'] = "Purchase Update";
        $data['purchaseId'] = $purchaseId;
        $data['invoice'] = $this->db->query("select PurchaseMaster_InvoiceNo from tbl_purchasemaster where PurchaseMaster_SlNo = ?", $purchaseId)->row()->PurchaseMaster_InvoiceNo;
        $data['content'] = $this->load->view('Administrator/purchase/purchase_order', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function purchaseExcel()
    {
        $this->cart->destroy();
        $data['title'] = "Purchase Order";
        $data['content'] = $this->load->view('Administrator/purchase/purchase_order_excel', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function createProductSheet()
    {
        $this->cart->destroy();
        $data['title'] = "Create Product Sheet";
        $data['content'] = $this->load->view('Administrator/purchase/product_sheet', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function excelFileFormate()
    {
        $data['title'] = "Purchase Order";
        $data['content'] = $this->load->view('Administrator/purchase/excel_file_foramate', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function returns()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['returnId'] = 0;
        $data['title'] = "Purchase Return";
        $data['content'] = $this->load->view('Administrator/purchase/purchase_return', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function purchaseReturnEdit($returnId)
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['returnId'] = $returnId;
        $data['title'] = "Purchase Return";
        $data['content'] = $this->load->view('Administrator/purchase/purchase_return', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function damage_entry()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Damage Entry";
        $data['damageCode'] = $this->mt->generateDamageCode();
        $data['content'] = $this->load->view('Administrator/purchase/damage_entry', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function stock()
    {
        $data['title'] = "Purchase Stock List";
        $data['content'] = $this->load->view('Administrator/purchase/purchase_stock_list', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    function Selectsuplier()
    {


        $sid = $this->input->post('sid');
        $query = $this->db->query("SELECT * FROM tbl_supplier where Supplier_SlNo = '$sid'");
        $data['Supplier'] = $query->row();
        $this->load->view('Administrator/purchase/ajax_suplier', $data);
    }

    function SelectPruduct()
    {
        $ProID = $this->input->post('ProID');
        $querys = $this->db->query("
            SELECT 
            tbl_product.*,
            tbl_unit.*, 
            tbl_brand.*  
            FROM tbl_product
            left join tbl_unit on tbl_unit.Unit_SlNo=tbl_product.Unit_ID
            left join tbl_brand on tbl_brand.brand_SiNo=tbl_product.brand
            where tbl_product.Product_SlNo = '$ProID'
        ");

        $data['Product'] = $querys->row();
        $this->load->view('Administrator/purchase/ajax_product', $data);
    }

    function SelectCat()
    {
        $data['ProCat'] = $this->input->post('ProCat');
        $this->load->view('Administrator/purchase/ajax_CatWiseProduct', $data);
    }

    public function addPurchase()
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
            $invoiceCount = $this->db->query("select * from tbl_purchasemaster where PurchaseMaster_InvoiceNo = ?", $invoice)->num_rows();
            if ($invoiceCount != 0) {
                $invoice = $this->mt->generatePurchaseInvoice();
            }

            if (isset($data->supplier)) {
                $supplier = (array)$data->supplier;
                unset($supplier['Supplier_SlNo']);
                unset($supplier['display_name']);

                $mobile_count = $this->db->query("select * from tbl_supplier where Supplier_Mobile = ? and branch_id = ?", [$data->supplier->Supplier_Mobile, $this->session->userdata("BRANCHid")]);
                if (
                    $data->supplier->Supplier_Mobile != '' &&
                    $data->supplier->Supplier_Mobile != null &&
                    $mobile_count->num_rows() > 0
                ) {

                    $duplicateSupplier = $mobile_count->row();
                    unset($supplier['Supplier_Code']);
                    unset($supplier['Supplier_Type']);
                    $supplier["UpdateBy"]   = $this->session->userdata("userId");
                    $supplier["UpdateTime"] = date("Y-m-d H:i:s");
                    $supplier["status"]     = 'a';

                    if ($duplicateSupplier->Supplier_Type == 'G') {
                        $supplier["Supplier_Type"] = '';
                    }
                    $this->db->where('Supplier_SlNo', $duplicateSupplier->Supplier_SlNo)->update('tbl_supplier', $supplier);
                    $supplierId = $duplicateSupplier->Supplier_SlNo;
                } else {
                    if ($data->supplier->Supplier_Type == 'N') {
                        $supplier['Supplier_Code']     = $this->mt->generateSupplierCode();
                        $supplier['status']            = 'a';
                        $supplier['AddBy']             = $this->session->userdata("userId");
                        $supplier['AddTime']           = date('Y-m-d H:i:s');
                        $supplier['last_update_ip'] = get_client_ip();
                        $supplier['branch_id'] = $this->session->userdata('BRANCHid');

                        $this->db->insert('tbl_supplier', $supplier);
                        $supplierId = $this->db->insert_id();
                    }
                }
            }

            $purchase = array(
                'PurchaseMaster_InvoiceNo'      => $invoice,
                'PurchaseMaster_OrderDate'      => $data->purchase->purchaseDate,
                'PurchaseMaster_PurchaseFor'    => $data->purchase->purchaseFor,
                'PurchaseMaster_TotalAmount'    => $data->purchase->total,
                'PurchaseMaster_DiscountAmount' => $data->purchase->discount,
                'PurchaseMaster_Tax'            => $data->purchase->vat,
                'PurchaseMaster_Freight'        => $data->purchase->freight,
                'PurchaseMaster_SubTotalAmount' => $data->purchase->subTotal,
                'PurchaseMaster_PaidAmount'     => $data->purchase->paid,
                'PurchaseMaster_DueAmount'      => $data->purchase->due,
                'previous_due'                  => $data->purchase->previousDue,
                'PurchaseMaster_Description'    => $data->purchase->note,
                // 'status'                        => 'p', for lc 
                'is_lc_purchase'                => $data->purchase->is_lc_purchase == true,
                'status'                        => $data->purchase->is_lc_purchase == true ? 'p' : 'a',
                'AddBy'                         => $this->session->userdata("userId"),
                'AddTime'                       => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
                'branch_id'       => $this->session->userdata('BRANCHid')
            );

            if ($data->supplier->Supplier_Type == 'G') {
                $purchase['Supplier_SlNo']    = Null;
                $purchase['supplierType']    = "G";
                $purchase['supplierName']    = $data->supplier->Supplier_Name;
                $purchase['supplierMobile']  = $data->supplier->Supplier_Mobile;
                $purchase['supplierAddress'] = $data->supplier->Supplier_Address;
            } else {
                $purchase['supplierType'] = $data->supplier->Supplier_Type == 'N' ? "retail" : 'retail';
                $purchase['Supplier_SlNo'] = $supplierId;
            }

            $this->db->insert('tbl_purchasemaster', $purchase);
            $purchaseId = $this->db->insert_id();

            foreach ($data->cartProducts as $product) {
                $purchaseDetails = array(
                    'PurchaseMaster_IDNo' => $purchaseId,
                    'Product_IDNo' => $product->productId,
                    'PurchaseDetails_TotalQuantity' => $product->quantity,
                    'PurchaseDetails_Rate' => $product->purchaseRate,
                    'Currency_Name'                 => $product->currencyName ?? null,
                    'Currency_Rate'                 => $product->currencyRate ?? null,
                    'Per_Foreign_Amount'            => $product->perForeignAmount ?? 0,
                    'Total_Foreign_Amount'          => $product->totalForeignAmount ?? 0,
                    'perCBM'                        => $product->perCBM ?? 0,
                    'perCTN'                        => $product->perCTN ?? 0,
                    'totalCTN'                      => $product->totalCTN ?? 0,
                    'PurchaseDetails_TotalAmount' => $product->total, 
                    'discountAmount'            => $product->discountAmount, 
                    'discountPercent'           => $product->discountPercent, 
                    'note'                      => $product->note ?? null,
                    'status'                    => $data->purchase->is_lc_purchase == true ? 'p' : 'a',                    
                    // 'status' => 'p', for lc
                    'AddBy' => $this->session->userdata("userId"),
                    'AddTime' => date('Y-m-d H:i:s'),
                    'last_update_ip' => get_client_ip(),
                    'branch_id' => $this->session->userdata('BRANCHid')
                );

                $this->db->insert('tbl_purchasedetails', $purchaseDetails);

                if ($data->purchase->is_lc_purchase == false ) {
                    $previousStock = $this->mt->productStock($product->productId);
                    $inventoryCount = $this->db->query("select * from tbl_currentinventory where product_id = ? and branch_id = ?", [$product->productId, $this->session->userdata('BRANCHid')])->num_rows();
                    
                    if ($inventoryCount == 0) {
                        $inventory = array(
                            'product_id' => $product->productId,
                            'purchase_quantity' => $product->quantity,
                            'branch_id' => $this->session->userdata('BRANCHid')
                        );
    
                        $this->db->insert('tbl_currentinventory', $inventory);
                    } else {
                        $this->db->query("
                            update tbl_currentinventory 
                            set purchase_quantity = purchase_quantity + ? 
                            where product_id = ? 
                            and branch_id = ?
                        ", [$product->quantity, $product->productId, $this->session->userdata('BRANCHid')]);
                    }
    
                    $this->db->query("
                        update tbl_product set 
                        Product_Purchase_Rate = ?,
                        Product_SellingPrice = ?
                        where Product_SlNo = ?
                    ", [
                        $product->purchaseRate,
                        $product->salesRate,
                        $product->productId
                    ]);
    
                    $this->db->query("
                        update tbl_product set 
                        Product_Purchase_Rate = (((Product_Purchase_Rate * ?) + ?) / ?), 
                        Product_SellingPrice = ? 
                        where Product_SlNo = ?
                    ", [
                        $previousStock,
                        $product->total,
                        ($previousStock + $product->quantity),
                        $product->salesRate,
                        $product->productId
                    ]);
                }

            }
            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'Purchase Success', 'purchaseId' => $purchaseId];
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
            $purchaseId = $data->purchase->purchaseId;
            $supplierId = $data->purchase->supplierId;

            if (isset($data->supplier)) {
                $supplier = (array)$data->supplier;
                unset($supplier['Supplier_SlNo']);
                unset($supplier['display_name']);

                $mobile_count = $this->db->query("select * from tbl_supplier where Supplier_Mobile = ? and branch_id = ?", [$data->supplier->Supplier_Mobile, $this->session->userdata("BRANCHid")]);
                if (
                    $data->supplier->Supplier_Mobile != '' &&
                    $data->supplier->Supplier_Mobile != null &&
                    $mobile_count->num_rows() > 0
                ) {

                    $duplicateSupplier = $mobile_count->row();
                    unset($supplier['Supplier_Code']);
                    unset($supplier['Supplier_Type']);
                    $supplier["UpdateBy"]   = $this->session->userdata("userId");
                    $supplier["UpdateTime"] = date("Y-m-d H:i:s");
                    $supplier["status"]     = 'a';

                    if ($duplicateSupplier->Supplier_Type == 'G') {
                        $supplier["Supplier_Type"] = '';
                    }
                    $this->db->where('Supplier_SlNo', $duplicateSupplier->Supplier_SlNo)->update('tbl_supplier', $supplier);
                    $supplierId = $duplicateSupplier->Supplier_SlNo;
                } else {
                    if ($data->supplier->Supplier_Type == 'N') {
                        $supplier['Supplier_Code']  = $this->mt->generateSupplierCode();
                        $supplier['status']         = 'a';
                        $supplier['AddBy']          = $this->session->userdata("userId");
                        $supplier['AddTime']        = date('Y-m-d H:i:s');
                        $supplier['last_update_ip'] = get_client_ip();
                        $supplier['branch_id']      = $this->session->userdata('BRANCHid');

                        $this->db->insert('tbl_supplier', $supplier);
                        $supplierId = $this->db->insert_id();
                    }
                }
            }

            $purchase = array(
                'PurchaseMaster_InvoiceNo' => $data->purchase->invoice,
                'PurchaseMaster_OrderDate' => $data->purchase->purchaseDate,
                'PurchaseMaster_PurchaseFor' => $data->purchase->purchaseFor,
                'PurchaseMaster_TotalAmount' => $data->purchase->total,
                'PurchaseMaster_DiscountAmount' => $data->purchase->discount,
                'PurchaseMaster_Tax' => $data->purchase->vat,
                'PurchaseMaster_Freight' => $data->purchase->freight,
                'PurchaseMaster_SubTotalAmount' => $data->purchase->subTotal,
                'PurchaseMaster_PaidAmount' => $data->purchase->paid,
                'PurchaseMaster_DueAmount' => $data->purchase->due,
                'previous_due' => $data->purchase->previousDue,
                'PurchaseMaster_Description' => $data->purchase->note,
                // 'status' => 'p', for lc
                'UpdateBy' => $this->session->userdata("userId"),
                'UpdateTime' => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
                'branch_id' => $this->session->userdata('BRANCHid')
            );

            if ($data->supplier->Supplier_Type == 'G') {
                $purchase['Supplier_SlNo']   = Null;
                $purchase['supplierType']    = "G";
                $purchase['supplierName']    = $data->supplier->Supplier_Name;
                $purchase['supplierMobile']  = $data->supplier->Supplier_Mobile;
                $purchase['supplierAddress'] = $data->supplier->Supplier_Address;
            } else {
                $purchase['supplierType']  = $data->supplier->Supplier_Type == 'N' ? "retail" : 'retail';
                $purchase['Supplier_SlNo'] = $supplierId;
                $purchase['supplierName']    = NULL;
                $purchase['supplierMobile']  = NULL;
                $purchase['supplierAddress'] = NULL;
            }

            $this->db->where('PurchaseMaster_SlNo', $purchaseId);
            $this->db->update('tbl_purchasemaster', $purchase);

            $oldPurchaseDetails = $this->db->query("select * from tbl_purchasedetails where PurchaseMaster_IDNo = ?", $purchaseId)->result();
            $this->db->query("delete from tbl_purchasedetails where PurchaseMaster_IDNo = ?", $purchaseId);

            $purchase = $this->db->query("select * from tbl_purchasemaster where PurchaseMaster_SlNo = ? and branch_id = ?", [$purchaseId, $this->session->userdata('BRANCHid')])->row();
            
            if ($purchase->status == 'a') {
                foreach ($oldPurchaseDetails as $product) {
                    $previousStock = $this->mt->productStock($product->Product_IDNo);
    
                    $this->db->query("
                        update tbl_currentinventory 
                        set purchase_quantity = purchase_quantity - ? 
                        where product_id = ?
                        and branch_id = ?
                    ", [$product->PurchaseDetails_TotalQuantity, $product->Product_IDNo, $this->session->userdata('BRANCHid')]);
    
                    $this->db->query("
                        update tbl_product set 
                        Product_Purchase_Rate = (((Product_Purchase_Rate * ?) - ?) / ?)
                        where Product_SlNo = ?
                    ", [
                        $previousStock,
                        $product->PurchaseDetails_TotalAmount,
                        ($previousStock - $product->PurchaseDetails_TotalQuantity),
                        $product->Product_IDNo
                    ]);
                }
    
                foreach ($data->cartProducts as $product) {
                    $purchaseDetails = array(
                        'PurchaseMaster_IDNo' => $purchaseId,
                        'Product_IDNo' => $product->productId,
                        'PurchaseDetails_TotalQuantity' => $product->quantity,
                        'PurchaseDetails_Rate' => $product->purchaseRate,
                        'PurchaseDetails_TotalAmount' => $product->total,
                        'discountAmount'            => $product->discountAmount, 
                        'discountPercent'           => $product->discountPercent,
                        // for lc
                        'Currency_Name'                 => $product->currencyName ?? null,
                        'Currency_Rate'                 => $product->currencyRate ?? null,
                        'Per_Foreign_Amount'            => $product->perForeignAmount ?? 0,
                        'Total_Foreign_Amount'          => $product->totalForeignAmount ?? 0,
                        'perCBM'                        => $product->perCBM ?? 0,
                        'perCTN'                        => $product->perCTN ?? 0,
                        'totalCTN'                      => $product->totalCTN ?? 0,
                        // end lc
                        'note'                      => $product->note ?? null,
                        'status' => 'a',
                        'UpdateBy' => $this->session->userdata("userId"),
                        'UpdateTime' => date('Y-m-d H:i:s'),
                        'last_update_ip' => get_client_ip(),
                        'branch_id' => $this->session->userdata('BRANCHid')
                    );
    
                    $this->db->insert('tbl_purchasedetails', $purchaseDetails);
    
                    $previousStock = $this->mt->productStock($product->productId);
    
                    $inventoryCount = $this->db->query("select * from tbl_currentinventory where product_id = ? and branch_id = ?", [$product->productId, $this->session->userdata('BRANCHid')])->num_rows();
                    
                    if ($inventoryCount == 0) {
                        $inventory = array(
                            'product_id' => $product->productId,
                            'purchase_quantity' => $product->quantity,
                            'branch_id' => $this->session->userdata('BRANCHid')
                        );
                        $this->db->insert('tbl_currentinventory', $inventory);
                    } else {
                        $this->db->query("
                            update tbl_currentinventory 
                            set purchase_quantity = purchase_quantity + ? 
                            where product_id = ?
                            and branch_id = ?
                        ", [$product->quantity, $product->productId, $this->session->userdata('BRANCHid')]);
                    }
    
                    $this->db->query("
                        update tbl_product set 
                        Product_Purchase_Rate = (((Product_Purchase_Rate * ?) + ?) / ?), 
                        Product_SellingPrice = ? 
                        where Product_SlNo = ?
                    ", [
                        $previousStock,
                        $product->total,
                        ($previousStock + $product->quantity),
                        $product->salesRate,
                        $product->productId
                    ]);
                }
            }

            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'Purchase update success', 'purchaseId' => $purchaseId];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function purchase_bill()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Purchase Invoice";
        $data['content'] = $this->load->view('Administrator/purchase/purchase_bill', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function purchase_record()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Purchase Record";
        $data['content'] = $this->load->view('Administrator/purchase/purchase_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    function addDamage()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);

            if ($data->damage->damageFor != $this->session->userdata("BRANCHid")) {
                $res = ['success' => false, 'message' => 'You have already changed your branch.', 'branch_status' => false];
                echo json_encode($res);
                exit;
            }

            // check stock
            foreach ($data->carts as $cartProduct) {
                $checkStock = $this->mt->productStock($cartProduct->product_id);
                if (($cartProduct->quantity > $checkStock)) {
                    $res = ['success' => false, 'message' => "({$cartProduct->productName} - {$cartProduct->productCode}) stock unavailable"];
                    echo json_encode($res);
                    exit;
                }
            }

            $damage = array(
                'Damage_InvoiceNo'   => $data->damage->Damage_InvoiceNo,
                'Damage_Date'        => $data->damage->Damage_Date,
                'damage_amount'      => $data->damage->damage_amount,
                'Damage_Description' => $data->damage->Damage_Description,
                'status'             => 'a',
                'AddBy'              => $this->session->userdata("userId"),
                'AddTime'            => date('Y-m-d H:i:s'),
                'last_update_ip'     => get_client_ip(),
                'branch_id'          => $this->session->userdata('BRANCHid')
            );

            $this->db->insert('tbl_damage', $damage);
            $damageId = $this->db->insert_id();

            foreach ($data->carts as $key => $product) {
                $damageDetails = array(
                    'Damage_SlNo'                  => $damageId,
                    'Product_SlNo'                 => $product->product_id,
                    'DamageDetails_DamageQuantity' => $product->quantity,
                    'damage_rate'                  => $product->rate,
                    'damage_amount'                => $product->total,
                    'status'                       => 'a',
                    'AddBy'                        => $this->session->userdata("userId"),
                    'AddTime'                      => date('Y-m-d H:i:s'),
                    'last_update_ip'     => get_client_ip(),
                    'branch_id'              => $this->session->userdata('BRANCHid'),
                );

                $this->db->insert('tbl_damagedetails', $damageDetails);

                $this->db->query("
                    update tbl_currentinventory ci 
                    set ci.damage_quantity = ci.damage_quantity + ? 
                    where product_id = ? 
                    and ci.branch_id = ?
                ", [$product->quantity, $product->product_id, $this->session->userdata('BRANCHid')]);
            }
            $this->db->trans_commit();

            $res = ['success' => true, 'message' => 'Damage entry success', 'damageCode' => $this->mt->generateDamageCode()];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateDamage()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);
            $damageId = $data->damage->Damage_SlNo;

            $damage = array(
                'Damage_InvoiceNo'   => $data->damage->Damage_InvoiceNo,
                'Damage_Date'        => $data->damage->Damage_Date,
                'damage_amount'      => $data->damage->damage_amount,
                'Damage_Description' => $data->damage->Damage_Description,
                'UpdateBy'           => $this->session->userdata("userId"),
                'UpdateTime'         => date('Y-m-d H:i:s'),
                'last_update_ip'     => get_client_ip(),
            );
            $this->db->where('Damage_SlNo', $damageId)->update('tbl_damage', $damage);

            $oldProduct = $this->db->query("select * from tbl_damagedetails where Damage_SlNo = ?", $damageId)->result();
            foreach ($oldProduct as $key => $item) {
                $this->db->query("
                    update tbl_currentinventory ci 
                    set ci.damage_quantity = ci.damage_quantity - ? 
                    where product_id = ? 
                    and ci.branch_id = ?
                ", [$item->DamageDetails_DamageQuantity, $item->Product_SlNo, $this->session->userdata('BRANCHid')]);
            }
            $this->db->query("DELETE FROM `tbl_damagedetails` WHERE  Damage_SlNo = ?", $damageId);

            // check stock
            foreach ($data->carts as $cartProduct) {
                $checkStock = $this->mt->productStock($cartProduct->product_id);
                if (($cartProduct->quantity > $checkStock)) {
                    $res = ['success' => false, 'message' => "({$cartProduct->productName} - {$cartProduct->productCode}) stock unavailable"];
                    echo json_encode($res);
                    exit;
                }
            }

            foreach ($data->carts as $key => $product) {
                $damageDetails = array(
                    'Damage_SlNo'                  => $damageId,
                    'Product_SlNo'                 => $product->product_id,
                    'DamageDetails_DamageQuantity' => $product->quantity,
                    'damage_rate'                  => $product->rate,
                    'damage_amount'                => $product->total,
                    'status'                       => 'a',
                    'AddBy'                        => $this->session->userdata("userId"),
                    'AddTime'                      => date('Y-m-d H:i:s'),
                    'UpdateBy'                     => $this->session->userdata("userId"),
                    'UpdateTime'                   => date('Y-m-d H:i:s'),
                    'last_update_ip'               => get_client_ip(),
                    'branch_id'                    => $this->session->userdata('BRANCHid')
                );

                $this->db->insert('tbl_damagedetails', $damageDetails);

                $this->db->query("
                    update tbl_currentinventory ci 
                    set ci.damage_quantity = ci.damage_quantity + ? 
                    where product_id = ? 
                    and ci.branch_id = ?
                ", [$product->quantity, $product->product_id, $this->session->userdata('BRANCHid')]);
            }
            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'Damage updated successfully', 'damageCode' => $this->mt->generateDamageCode()];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function getDamages()
    {
        $data = json_decode($this->input->raw_input_stream);
        $clauses = "";

        if (isset($data->dateFrom) && $data->dateFrom != "") {
            $clauses .= " and d.Damage_Date between '$data->dateFrom' and '$data->dateTo'";
        }

        $damages = $this->db->query("select d.*
                        from tbl_damage d
                        where d.status = 'a'
                        and d.branch_id = ?
                        $clauses
                        order by d.Damage_SlNo desc
                    ", $this->session->userdata('BRANCHid'))->result();

        foreach ($damages as $key => $item) {
            $item->damageDetail = $this->db->query("
                select
                    dd.*,
                    p.Product_Code,
                    p.Product_Name
                from tbl_damagedetails dd
                join tbl_product p on p.Product_SlNo = dd.Product_SlNo
                where dd.status = 'a'
                and dd.branch_id = ?
                and dd.Damage_SlNo = ?
            ", [$this->session->userdata('BRANCHid'), $item->Damage_SlNo])->result();
        }
        echo json_encode($damages);
    }

    public function getDamage()
    {
        $data = json_decode($this->input->raw_input_stream);
        $damageId = $data->damageId;
        
        $damage = $this->db->get_where('tbl_damage', ['Damage_SlNo' => $damageId, 'status' => 'a'])->row();
        
        if ($damage) {
            $damageDetails = $this->db->select('dd.*, p.Product_Code, p.Product_Name')
                                    ->from('tbl_damagedetails dd')
                                    ->join('tbl_product p', 'p.Product_SlNo = dd.Product_SlNo')
                                    ->where(['dd.Damage_SlNo' => $damageId, 'dd.status' => 'a'])
                                    ->get()
                                    ->result();

            $res['damage'] = [$damage];
            $res['damageDetails'] = $damageDetails;
        } else {
            $res['damage'] = [];
            $res['damageDetails'] = [];
        }

        echo json_encode($res);
    }



    public function deleteDamage()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $damageId = $data->damageId;

            $oldProduct = $this->db->query("select * from tbl_damagedetails where Damage_SlNo = ?", $damageId)->result();
            foreach ($oldProduct as $key => $item) {
                $this->db->query("
                    update tbl_currentinventory ci 
                    set ci.damage_quantity = ci.damage_quantity - ? 
                    where product_id = ? 
                    and ci.branch_id = ?
                ", [$item->DamageDetails_DamageQuantity, $item->Product_SlNo, $this->session->userdata('BRANCHid')]);

                $this->db->where('Damage_SlNo', $damageId)->update('tbl_damage', ['status' => 'd']);
                $this->db->where('Damage_SlNo', $damageId)->update('tbl_damagedetails', ['status' => 'd']);
            }

            $res = ['success' => true, 'message' => 'Damage deleted successfully', 'newCode' => $this->mt->generateDamageCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function damage_product_list()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Product Damage List";
        $data['content'] = $this->load->view('Administrator/purchase/damage_list', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function damageInvoice($damageId)
    {
        $data['title'] = "Damage Invoice";
        $data['damageId'] = $damageId;

        $damage = $this->db->get_where('tbl_damage', ['Damage_SlNo' => $damageId, 'status' => 'a'])->row();
        
        if ($damage) {
            $data['damage'] = $damage;
            
            $damageDetails = $this->db->select('dd.*, p.Product_Code, p.Product_Name')
                                    ->from('tbl_damagedetails dd')
                                    ->join('tbl_product p', 'p.Product_SlNo = dd.Product_SlNo')
                                    ->where(['dd.Damage_SlNo' => $damageId, 'dd.status' => 'a'])
                                    ->get()
                                    ->result();
            $data['damageDetails'] = $damageDetails;
            
            $data['content'] = $this->load->view('Administrator/purchase/damage_invoice', $data, true);
            $this->load->view('Administrator/index', $data);
        } else {
            show_404();
        }
    }


    public function purchaseInvoicePrint($purchaseId)
    {
        $data['title'] = "Purchase Invoice";
        $data['purchaseId'] = $purchaseId;
        $data['content'] = $this->load->view('Administrator/purchase/purchase_to_report', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function returns_list()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Purchase Return";
        $data['content'] = $this->load->view('Administrator/purchase/purchase_return_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }


    public function getPurchaseReturns()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->supplierId) && $data->supplierId != '') {
            $clauses .= " and pr.Supplier_IDdNo = '$data->supplierId'";
        }

        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and pr.PurchaseReturn_ReturnDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->id) && $data->id != '') {
            $clauses .= " and pr.PurchaseReturn_SlNo = '$data->id'";

            $res['returnDetails'] = $this->db->query("
                select 
                    prd.*,
                    p.Product_Code,
                    p.Product_Name
                from tbl_purchasereturndetails prd
                join tbl_product p on p.Product_SlNo = prd.PurchaseReturnDetailsProduct_SlNo
                where prd.PurchaseReturn_SlNo = ?
                and prd.status = 'a'
            ", $data->id)->result();
        }

        $returns = $this->db->query("
            select 
                pr.*,
                pm.PurchaseMaster_SlNo,
                ifnull(s.Supplier_Code, 'Cash Supplier') as Supplier_Code,
                ifnull(s.Supplier_Name, pm.supplierName) as Supplier_Name,
                ifnull(s.Supplier_Mobile, pm.supplierMobile) as Supplier_Mobile,
                ifnull(s.Supplier_Address, pm.supplierAddress) as Supplier_Address,
                pm.supplierType,
                u.FullName
            from tbl_purchasereturn pr 
            join tbl_purchasemaster pm on pm.PurchaseMaster_InvoiceNo = pr.PurchaseMaster_InvoiceNo
            left join tbl_supplier s on s.Supplier_SlNo = pr.Supplier_IDdNo
            left join tbl_user u on u.User_SlNo = pr.AddBy
            where pr.status = 'a'
            and pr.branch_id = ?
            $clauses
            order by pr.PurchaseReturn_SlNo desc
        ", $this->brunch)->result();

        $res['returns'] = $returns;
        echo json_encode($res);
    }

    public function purchaseReturnInvoice($id)
    {
        $data['title'] = "Purchase return Invoice";
        $data['id'] = $id;
        $data['content'] = $this->load->view('Administrator/purchase/purchase_return_invoice', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function deletePurchaseReturn()
    {
        $res = ['success' => false, 'message' => ''];

        try {
            $data = json_decode($this->input->raw_input_stream);

            $oldReturn = $this->db->query("select * from tbl_purchasereturn where PurchaseReturn_SlNo = ?", $data->id)->row();

            $returnDetails = $this->db->query("select * from tbl_purchasereturndetails where PurchaseReturn_SlNo = ?", $data->id)->result();

            foreach ($returnDetails as $product) {
                $this->db->query("
                update tbl_currentinventory set 
                purchase_return_quantity = purchase_return_quantity - ? 
                where product_id = ? 
                and branch_id = ?
                ", [$product->PurchaseReturnDetails_ReturnQuantity, $product->PurchaseReturnDetailsProduct_SlNo, $this->brunch]);
            }


            $supplierInfo = $this->db->query("select * from tbl_supplier where Supplier_SlNo = ?", $oldReturn->Supplier_IDdNo)->row();
            if (!empty($supplierInfo)) {
                $this->db->query("
                delete from tbl_supplier_payment 
                where SPayment_invoice = ? 
                and SPayment_customerID = ?
                and SPayment_amount = ?
                limit 1
                ", [
                    $oldReturn->PurchaseMaster_InvoiceNo,
                    $oldReturn->Supplier_IDdNo,
                    $oldReturn->PurchaseReturn_ReturnAmount
                ]);
            }

            $returnPurchase = array(
                'status' => 'd',
                'DeletedBy' => $this->session->userdata('userId'),
                'DeletedTime' => date("Y-m-d H:i:s"),
                'last_update_ip' => get_client_ip(),
            );

            $this->db->set($returnPurchase)->where('PurchaseReturn_SlNo', $data->id)->update('tbl_purchasereturndetails');
            $this->db->set($returnPurchase)->where('PurchaseReturn_SlNo', $data->id)->update('tbl_purchasereturn');

            $res = ['success' => true, 'message' => 'Purchase return deleted'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function purchaseReturnDetails()
    {
        $data['title'] = "Purchase return details";
        $data['content'] = $this->load->view('Administrator/purchase/purchase_return_details', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function checkPurchaseReturn($invoice)
    {
        $res = ['found' => false];

        $returnCount = $this->db->query("select * from tbl_purchasereturn where PurchaseMaster_InvoiceNo = ? and status = 'a'", $invoice)->num_rows();

        if ($returnCount != 0) {
            $res = ['found' => true];
        }

        echo json_encode($res);
    }

    public function changePurchaseStatus()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            // Update purchase master 
            $purchaseData = array(
                'status'     => 'a',
                'UpdateBy'   => $this->session->userdata("FullName"),
                'UpdateTime' => date('Y-m-d H:i:s'),
            );
            $this->db->where('PurchaseMaster_SlNo', $data->purchaseId)->update('tbl_purchasemaster', $purchaseData);

            // Update purchase details
            $purchaseDetailsData = array(
                'status'     => 'a',
                'UpdateBy'   => $this->session->userdata("FullName"),
                'UpdateTime' => date('Y-m-d H:i:s'),
            );
            $this->db->where('PurchaseMaster_IDNo', $data->purchaseId)->update('tbl_purchasedetails', $purchaseDetailsData);

            // Update current inventory table
            $purchaseDetails = $this->db->query("select * from tbl_purchasedetails where PurchaseMaster_IDNo = ?", $data->purchaseId)->result();

            foreach ($purchaseDetails as $product) {

                $previousStock = $this->mt->productStock($product->Product_IDNo);

                $salesRate = $this->db->query("select * from tbl_product where Product_SlNo = ? ", $product->Product_IDNo)->row()->Product_SellingPrice;

                $inventoryCount = $this->db->query("select * from tbl_currentinventory where product_id = ? and branch_id = ?", [$product->Product_IDNo, $product->branch_id])->num_rows();
                if ($inventoryCount == 0) {
                    $inventory = array(
                        'product_id'        => $product->Product_IDNo,
                        'purchase_quantity' => $product->PurchaseDetails_TotalQuantity,
                        'branch_id'         => $product->branch_id
                    );
                    $this->db->insert('tbl_currentinventory', $inventory);
                } else {
                    $this->db->query("
                        update tbl_currentinventory 
                        set purchase_quantity = purchase_quantity + ?
                        where product_id = ?
                        and branch_id = ?
                    ", [$product->PurchaseDetails_TotalQuantity, $product->Product_IDNo, $product->branch_id]);
                }

                $this->db->query("
                    update tbl_product set 
                    Product_Purchase_Rate = (((Product_Purchase_Rate * ?) + ?) / ?), 
                    Product_SellingPrice = ? 
                    where Product_SlNo = ?
                ", [
                    $previousStock,
                    $product->PurchaseDetails_TotalAmount,
                    ($previousStock + $product->PurchaseDetails_TotalQuantity),
                    $salesRate,
                    $product->Product_IDNo
                ]);
            }

            $res = ['success' => true, 'message' => 'Purchase invoice approved successfully.'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }
        echo json_encode($res);
    }
}
