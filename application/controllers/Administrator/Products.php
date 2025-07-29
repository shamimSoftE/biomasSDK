<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Products extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->brunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model("Model_myclass", "mmc", TRUE);
        $this->load->model('Model_table', "mt", TRUE);
        $this->load->model('Billing_model');
    }

    public function index()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Product";
        $data['productId'] = 0;
        $data['productCode'] = $this->mt->generateProductCode();
        $data['content'] = $this->load->view('Administrator/products/add_product', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function productEdit($id = null) {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Product";
        $data['productId'] = $id;
        $data['productCode'] = $this->mt->generateProductCode();
        $data['content'] = $this->load->view('Administrator/products/add_product', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function addProduct()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $productObj = json_decode($this->input->raw_input_stream);

            $productNameCount = $this->db->query("select * from tbl_product where Product_Name = ?", $productObj->Product_Name)->num_rows();
            if ($productNameCount > 0) {
                $res = ['success' => false, 'message' => 'Product name already exists'];
                echo json_encode($res);
                exit;
            }

            $productCodeCount = $this->db->query("select * from tbl_product where Product_Code = ?", $productObj->Product_Code)->num_rows();
            if ($productCodeCount > 0) {
                $res = ['success' => false, 'message' => 'Product code already exists'];
                echo json_encode($res);
                exit;
            }

            $product                   = (array)$productObj;
            $product['is_service']     = $productObj->is_service == true ? 'true' : 'false';
            $product['status']         = 'a';
            $product['AddBy']          = $this->session->userdata("userId");
            $product['AddTime']        = date('Y-m-d H:i:s');
            $product['last_update_ip'] = get_client_ip();
            $product['branch_id']      = $this->brunch;

            $this->db->insert('tbl_product', $product);

            $res = ['success' => true, 'message' => 'Product added successfully', 'productId' => $this->mt->generateProductCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateProduct()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $productObj = json_decode($this->input->raw_input_stream);

            $productNameCount = $this->db->query("select * from tbl_product where Product_Name = ? and Product_SlNo != ?", [$productObj->Product_Name, $productObj->Product_SlNo])->num_rows();
            if ($productNameCount > 0) {
                $res = ['success' => false, 'message' => 'Product name already exists'];
                echo json_encode($res);
                exit;
            }

            $productCodeCount = $this->db->query("select * from tbl_product where Product_Code = ? and Product_SlNo != ?", [$productObj->Product_Code, $productObj->Product_SlNo])->num_rows();
            if ($productCodeCount > 0) {
                $res = ['success' => false, 'message' => 'Product code already exists'];
                echo json_encode($res);
                exit;
            }

            $product = (array)$productObj;
            unset($product['Product_SlNo']);
            $product['is_service']     = $productObj->is_service == true ? 'true' : 'false';
            $product['UpdateBy']       = $this->session->userdata("userId");
            $product['UpdateTime']     = date('Y-m-d H:i:s');
            $product['last_update_ip'] = get_client_ip();

            $this->db->where('Product_SlNo', $productObj->Product_SlNo)->update('tbl_product', $product);

            $res = ['success' => true, 'message' => 'Product updated successfully', 'productId' => $this->mt->generateProductCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function deleteProduct()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $rules = array(
                'status'         => 'd',
                'DeletedBy'      => $this->session->userdata("userId"),
                'DeletedTime'    => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip()
            );
            $this->db->set($rules)->where('Product_SlNo', $data->productId)->update('tbl_product');

            $res = ['success' => true, 'message' => 'Product deleted successfully'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function getProducts()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        $limit = "";
        $status = "a";
        if (isset($data->status) && $data->status != '') {
            $status = $data->status;
        }

        if (isset($data->categoryId) && $data->categoryId != '') {
            $clauses .= " and p.ProductCategory_ID = '$data->categoryId'";
        }

        if (isset($data->productId) && $data->productId != '') {
            $clauses .= " and p.Product_SlNo = '$data->productId'";
        }

        if (isset($data->isService) && $data->isService != null && $data->isService != '') {
            $clauses .= " and p.is_service = '$data->isService'";
        }

        if (isset($data->forSearch) && $data->forSearch != '') {
            $limit .= "limit 20";
        }
        if (isset($data->name) && $data->name != '') {
            $clauses .= " and p.Product_Code like '$data->name%'";
            $clauses .= " or p.Product_Name like '$data->name%'";
        }

        $products = $this->db->query("
            select
                p.*,
                concat(p.Product_Name, ' - ', p.Product_Code) as display_text,
                pc.ProductCategory_Name,
                br.brand_name,
                u.Unit_Name,
                ua.User_Name as added_by,
                ud.User_Name as deleted_by
            from tbl_product p
            left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
            left join tbl_brand br on br.brand_SiNo = p.brand
            left join tbl_unit u on u.Unit_SlNo = p.Unit_ID
            left join tbl_user ua on ua.User_SlNo = p.AddBy
            left join tbl_user ud on ud.User_SlNo = p.DeletedBy
            where p.status = '$status'
            $clauses
            order by p.Product_SlNo desc
            $limit
        ")->result();

        echo json_encode($products);
    }

    public function getTransferProductStock()
    {
        $inputs = json_decode($this->input->raw_input_stream);
        $stock = $this->mt->transferBranchStock($inputs->productId, $inputs->branchId);
        echo $stock;
    }
    public function getProductStock()
    {
        $inputs = json_decode($this->input->raw_input_stream);
        $stock = $this->mt->productStock($inputs->productId);
        echo $stock;
    }

    public function getCurrentStock()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->stockType) && $data->stockType == 'low') {
            $clauses .= " and current_quantity <= Product_ReOrederLevel";
        }

        if (isset($data->categoryId) && $data->categoryId != '') {
            $clauses .= " and ProductCategory_ID = '$data->categoryId'";
        }

        $stock = $this->mt->currentStock($clauses);
        $res['stock'] = $stock;
        $res['totalValue'] = array_sum(
            array_map(function ($product) {
                return $product->stock_value;
            }, $stock)
        );

        echo json_encode($res);
    }

    public function getTotalStock()
    {
        $data = json_decode($this->input->raw_input_stream);

        $branchId = $this->session->userdata('BRANCHid');
        $clauses = "";
        if (isset($data->categoryId) && $data->categoryId != null) {
            $clauses .= " and p.ProductCategory_ID = '$data->categoryId'";
        }

        if (isset($data->productId) && $data->productId != null) {
            $clauses .= " and p.Product_SlNo = '$data->productId'";
        }

        if (isset($data->brandId) && $data->brandId != null) {
            $clauses .= " and p.brand = '$data->brandId'";
        }
        // PurchaseMaster_SlNo
        $stock = $this->db->query("
            select
                p.*,
                pc.ProductCategory_Name,
                b.brand_name,
                u.Unit_Name,
                (select ifnull(sum(pd.PurchaseDetails_TotalQuantity), 0) 
                    from tbl_purchasedetails pd 
                    join tbl_purchasemaster pm on pm.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo
                    where pd.Product_IDNo = p.Product_SlNo
                    and pd.branch_id = '$branchId'
                    and pd.status = 'a'
                    " . (isset($data->date) && $data->date != null ? " and pm.PurchaseMaster_OrderDate <= '$data->date'" : "") . "
                ) as purchased_quantity,
                 
                (select ifnull(sum(pd.PurchaseDetails_TotalQuantity), 0) 
                    from tbl_lcpurchasedetails pd 
                    join tbl_lcpurchasemaster pm on pm.purchase_id = pd.PurchaseMaster_IDNo
                    where pd.Product_IDNo = p.Product_SlNo
                    and pd.branch_id = '$branchId'
                    and pd.status = 'a'
                    " . (isset($data->date) && $data->date != null ? " and pm.PurchaseMaster_OrderDate <= '$data->date'" : "") . "
                ) as lc_purchased_quantity,
                        
                (select ifnull(sum(prd.PurchaseReturnDetails_ReturnQuantity), 0) 
                    from tbl_purchasereturndetails prd 
                    join tbl_purchasereturn pr on pr.PurchaseReturn_SlNo = prd.PurchaseReturn_SlNo
                    where prd.PurchaseReturnDetailsProduct_SlNo = p.Product_SlNo
                    and prd.branch_id= '$branchId'
                    and prd.status = 'a'
                    " . (isset($data->date) && $data->date != null ? " and pr.PurchaseReturn_ReturnDate <= '$data->date'" : "") . "
                ) as purchase_returned_quantity,
                        
                (select ifnull(sum(sd.SaleDetails_TotalQuantity), 0) 
                    from tbl_saledetails sd
                    join tbl_salesmaster sm on sm.SaleMaster_SlNo = sd.SaleMaster_IDNo
                    where sd.Product_IDNo = p.Product_SlNo
                    and sd.branch_id  = '$branchId'
                    and sd.status = 'a'
                    " . (isset($data->date) && $data->date != null ? " and sm.SaleMaster_SaleDate <= '$data->date'" : "") . "
                ) as sold_quantity,
                        
                (select ifnull(sum(srd.SaleReturnDetails_ReturnQuantity), 0)
                    from tbl_salereturndetails srd 
                    join tbl_salereturn sr on sr.SaleReturn_SlNo = srd.SaleReturn_IdNo
                    where srd.SaleReturnDetailsProduct_SlNo = p.Product_SlNo
                    and srd.branch_id = '$branchId'
                    and srd.status = 'a'
                    
                    " . (isset($data->date) && $data->date != null ? " and sr.SaleReturn_ReturnDate <= '$data->date'" : "") . "
                ) as sales_returned_quantity,
                        
                (select ifnull(sum(dmd.DamageDetails_DamageQuantity), 0) 
                    from tbl_damagedetails dmd
                    join tbl_damage dm on dm.Damage_SlNo = dmd.Damage_SlNo
                    where dmd.Product_SlNo = p.Product_SlNo
                    and dmd.status = 'a'
                    and dm.branch_id = '$branchId'
                    " . (isset($data->date) && $data->date != null ? " and dm.Damage_Date <= '$data->date'" : "") . "
                ) as damaged_quantity,
            
                (select ifnull(sum(trd.quantity), 0)
                    from tbl_transferdetails trd
                    join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
                    where trd.product_id = p.Product_SlNo
                    and tm.transfer_from = '$branchId'
                    and tm.status != 'd'
                    " . (isset($data->date) && $data->date != null ? " and tm.transfer_date <= '$data->date'" : "") . "
                ) as transferred_from_quantity,

                (select ifnull(sum(trd.quantity), 0)
                    from tbl_transferdetails trd
                    join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
                    where trd.product_id = p.Product_SlNo
                    and tm.transfer_to = '$branchId'
                    and tm.status = 'a'
                    " . (isset($data->date) && $data->date != null ? " and tm.transfer_date <= '$data->date'" : "") . "
                ) as transferred_to_quantity,
                        
                (select (purchased_quantity + lc_purchased_quantity + sales_returned_quantity + transferred_to_quantity) - (sold_quantity + purchase_returned_quantity + damaged_quantity + transferred_from_quantity)) as current_quantity,
                (select p.Product_Purchase_Rate * current_quantity) as stock_value
            from tbl_product p
            left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
            left join tbl_brand b on b.brand_SiNo = p.brand
            left join tbl_unit u on u.Unit_SlNo = p.Unit_ID
            where p.status = 'a' and p.is_service = 'false' 
            $clauses
            order by p.Product_Name asc
        ")->result();

        $res['stock'] = $stock;
        $res['totalValue'] = array_sum(
            array_map(function ($product) {
                return $product->stock_value;
            }, $stock)
        );

        echo json_encode($res);
    }

    public function current_stock()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Current Stock";
        $data['categories'] = $this->Other_model->branch_wise_category();
        $data['brands'] = $this->Other_model->branch_wise_brand();
        $data['products'] = $this->Product_model->products_by_brunch();
        $data['content'] = $this->load->view('Administrator/stock/current_stock', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function productlist()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title']  = 'Product';
        $data['allproduct'] =  $this->Billing_model->select_all_Product_list();

        $this->load->view('Administrator/products/productList', $data);
        // $this->load->view('Administrator/index', $data);
    }

    public function product_name()
    {
        $data['allproduct'] = $allproduct =  $this->Billing_model->get_product_name();
        $this->load->view('Administrator/products/product_name', $data);
    }


    public function barcodeGenerate($Product_SlNo)
    {
        $data['title'] = "Barcode Generate";
        $data['product'] = $this->Billing_model->select_Product_by_id($Product_SlNo);
        $data['content'] = $this->load->view('Administrator/products/barcode/barcode', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function multibarcodeGenerate()
    {
        $data['title'] = "Multi Barcode Generate";
        $data['content'] = $this->load->view('Administrator/products/barcode/multibarcode', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function multibarcodeStore()
    {
        $data = json_decode($this->input->raw_input_stream);
        if ($this->session->has_userdata('products')) {
            $this->session->unset_userdata('products');
            $this->session->unset_userdata('xAxis');
            $this->session->unset_userdata('yAxis');
            $this->session->unset_userdata('single');
        }

        $this->session->set_userdata('products', $data->products);
        $this->session->set_userdata('xAxis', $data->xAxis);
        $this->session->set_userdata('yAxis', $data->yAxis);
        $this->session->set_userdata('single', $data->single);

        $res = ['status' => true];
        echo json_encode($res);
    }

    public function multibarcodePrint()
    {
        if ($this->session->has_userdata('products')) {
            $data['title'] = "Multi Barcode Generate";
            $data['products'] = $this->session->userdata('products');
            $data['content'] = $this->load->view('Administrator/products/barcode/multibarcodePrint', $data, TRUE);
            $this->load->view('Administrator/index', $data);
        } else {
            redirect("/module/dashboard");
        }
    }

    public function productLedger()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title']  = 'Product Ledger';

        $data['content'] = $this->load->view('Administrator/products/product_ledger', $data, true);
        $this->load->view('Administrator/index', $data);
    }

    public function getProductLedger()
    {
        $data = json_decode($this->input->raw_input_stream);
        $result = $this->db->query("
            select
                'a' as sequence,
                pd.PurchaseDetails_SlNo as id,
                pm.PurchaseMaster_OrderDate as date,
                concat('Purchase - ', pm.PurchaseMaster_InvoiceNo, ' - ', ifnull(s.Supplier_Name, pm.supplierName)) as description,
                pd.PurchaseDetails_Rate as rate,
                pd.PurchaseDetails_TotalQuantity as in_quantity,
                0 as out_quantity
            from tbl_purchasedetails pd
            join tbl_purchasemaster pm on pm.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo
            left join tbl_supplier s on s.Supplier_SlNo = pm.Supplier_SlNo
            where pd.status = 'a'
            and pd.Product_IDNo = " . $data->productId . "
            and pd.branch_id = " . $this->brunch . "
            
            UNION
            select 
                'b' as sequence,
                sd.SaleDetails_SlNo as id,
                sm.SaleMaster_SaleDate as date,
                concat('Sale - ', sm.SaleMaster_InvoiceNo, ' - ', ifnull(c.Customer_Name, sm.customerName)) as description,
                sd.SaleDetails_Rate as rate,
                0 as in_quantity,
                sd.SaleDetails_TotalQuantity as out_quantity
            from tbl_saledetails sd
            join tbl_salesmaster sm on sm.SaleMaster_SlNo = sd.SaleMaster_IDNo
            left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            where sd.status = 'a'
            and sd.Product_IDNo = " . $data->productId . "
            and sd.branch_id = " . $this->brunch . "
            
            UNION
            select 
                'c' as sequence,
                prd.PurchaseReturnDetails_SlNo as id,
                pr.PurchaseReturn_ReturnDate as date,
                concat('Purchase Return - ', pr.PurchaseMaster_InvoiceNo, ' - ', s.Supplier_Name) as description,
                (prd.PurchaseReturnDetails_ReturnAmount / prd.PurchaseReturnDetails_ReturnQuantity) as rate,
                0 as in_quantity,
                prd.PurchaseReturnDetails_ReturnQuantity as out_quantity
            from tbl_purchasereturndetails prd
            join tbl_purchasereturn pr on pr.PurchaseReturn_SlNo = prd.PurchaseReturn_SlNo
            left join tbl_supplier s on s.Supplier_SlNo = pr.Supplier_IDdNo
            where prd.status = 'a'
            and prd.PurchaseReturnDetailsProduct_SlNo = " . $data->productId . "
            and prd.branch_id= " . $this->brunch . "
            
            UNION
            select
                'd' as sequence, 
                srd.SaleReturnDetails_SlNo as id,
                sr.SaleReturn_ReturnDate as date,
                concat('Sale Return - ', sr.SaleMaster_InvoiceNo, ' - ', c.Customer_Name) as description,
                (srd.SaleReturnDetails_ReturnAmount / srd.SaleReturnDetails_ReturnQuantity) as rate,
                srd.SaleReturnDetails_ReturnQuantity as in_quantity,
                0 as out_quantity
            from tbl_salereturndetails srd
            join tbl_salereturn sr on sr.SaleReturn_SlNo = srd.SaleReturn_IdNo
            join tbl_salesmaster sm on sm.SaleMaster_InvoiceNo = sr.SaleMaster_InvoiceNo
            left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            where srd.status = 'a'
            and srd.SaleReturnDetailsProduct_SlNo = " . $data->productId . "
            and srd.branch_id = " . $this->brunch . "
            
            UNION
            select
                'e' as sequence, 
                trd.transferdetails_id as id,
                tm.transfer_date as date,
                concat('Transferred From: ', b.Branch_name, ' - ', tm.note) as description,
                0 as rate,
                trd.quantity as in_quantity,
                0 as out_quantity
            from tbl_transferdetails trd
            join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
            join tbl_branch b on b.branch_id = tm.transfer_from
            where trd.product_id = " . $data->productId . "
            and tm.transfer_to = " . $this->brunch . "
            
            UNION
            select 
                'f' as sequence,
                trd.transferdetails_id as id,
                tm.transfer_date as date,
                concat('Transferred To: ', b.Branch_name, ' - ', tm.note) as description,
                0 as rate,
                0 as in_quantity,
                trd.quantity as out_quantity
            from tbl_transferdetails trd
            join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
            join tbl_branch b on b.branch_id = tm.transfer_to
            where trd.product_id = " . $data->productId . "
            and tm.transfer_from = " . $this->brunch . "
            
            UNION
            select 
                'g' as sequence,
                dmd.DamageDetails_SlNo as id,
                d.Damage_Date as date,
                concat('Damaged - ', d.Damage_Description) as description,
                0 as rate,
                0 as in_quantity,
                dmd.DamageDetails_DamageQuantity as out_quantity
            from tbl_damagedetails dmd
            join tbl_damage d on d.Damage_SlNo = dmd.Damage_SlNo
            where dmd.Product_SlNo = " . $data->productId . "
            and d.branch_id = " . $this->brunch . "

            order by date, sequence, id
        ")->result();

        $ledger = array_map(function ($key, $row) use ($result) {
            $row->stock = $key == 0 ? $row->in_quantity - $row->out_quantity : ($result[$key - 1]->stock + ($row->in_quantity - $row->out_quantity));
            return $row;
        }, array_keys($result), $result);

        $previousRows = array_filter($ledger, function ($row) use ($data) {
            return $row->date < $data->dateFrom;
        });

        $previousStock = empty($previousRows) ? 0 : end($previousRows)->stock;

        $ledger = array_filter($ledger, function ($row) use ($data) {
            return $row->date >= $data->dateFrom && $row->date <= $data->dateTo;
        });

        echo json_encode(['ledger' => $ledger, 'previousStock' => $previousStock]);
    }
}
