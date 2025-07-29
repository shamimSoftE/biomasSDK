<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class TrashData extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->brunchId = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
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
        $data['sales'] = $this->db->query("select * from tbl_salesmaster where status = 'd' and branch_id = ?", $this->brunchId)->result();
        $data['purchases'] = $this->db->query("select * from tbl_purchasemaster where status = 'd' and branch_id = ?", $this->brunchId)->result();
        $data['products'] = $this->db->query("select * from tbl_product where status = 'd' and branch_id = ?", $this->brunchId)->result();
        $data['customers'] = $this->db->query("select * from tbl_customer where status = 'd' and branch_id = ?", $this->brunchId)->result();
        $data['suppliers'] = $this->db->query("select * from tbl_supplier where status = 'd' and branch_id = ?", $this->brunchId)->result();
        $data['customer_payment'] = $this->db->query("select * from tbl_customer_payment where status = 'd' and branch_id = ?", $this->brunchId)->result();
        $data['supplier_payment'] = $this->db->query("select * from tbl_supplier_payment where status = 'd' and branch_id = ?", $this->brunchId)->result();
        $data['cashtransactions'] = $this->db->query("select * from tbl_cashtransaction where status = 'd' and branch_id = ?", $this->brunchId)->result();
        $data['title'] = "Deleted Data List";
        $data['content'] = $this->load->view('Administrator/trash/index', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function deletedSale()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Deleted Sale List";
        $data['content'] = $this->load->view('Administrator/trash/deleted_sale', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function deletedPurchase()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Deleted Purchase List";
        $data['content'] = $this->load->view('Administrator/trash/deleted_purchase', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function deletedProduct()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Deleted Product List";
        $data['content'] = $this->load->view('Administrator/trash/deleted_product', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function deletedCustomer()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Deleted Customer List";
        $data['content'] = $this->load->view('Administrator/trash/deleted_customer', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function deletedSupplier()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Deleted Supplier List";
        $data['content'] = $this->load->view('Administrator/trash/deleted_supplier', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function deletedCustomerPayment()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Deleted Customer Payment";
        $data['content'] = $this->load->view('Administrator/trash/deleted_customerpayment', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function deletedSupplierPayment()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Deleted Supplier Payment";
        $data['content'] = $this->load->view('Administrator/trash/deleted_supplierpayment', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function deletedCashtransaction()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Deleted Cashtransaction List";
        $data['content'] = $this->load->view('Administrator/trash/deleted_cashtransaction', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    // restore section
    public function reStoreProduct()
    {
        $data = json_decode($this->input->raw_input_stream);

        foreach ($data->products as $key => $item) {
            $productData = array(
                'status' => 'a',
                'DeletedBy' => NULL,
                'DeletedTime' => NULL
            );
            $this->db->where("Product_SlNo", $item->Product_SlNo)->update('tbl_product', $productData);
        }
        echo "Product restore successful";
    }
    public function reStoreSupplier()
    {
        $data = json_decode($this->input->raw_input_stream);

        foreach ($data->suppliers as $key => $item) {
            $supplierData = array(
                'status' => 'a',
                'DeletedBy' => NULL,
                'DeletedTime' => NULL
            );
            $this->db->where("Supplier_SlNo", $item->Supplier_SlNo)->update('tbl_supplier', $supplierData);
        }
        echo "Supplier restore successful";
    }
    public function reStoreCustomer()
    {
        $data = json_decode($this->input->raw_input_stream);

        foreach ($data->customers as $key => $item) {
            $customerData = array(
                'status' => 'a',
                'DeletedBy' => NULL,
                'DeletedTime' => NULL
            );
            $this->db->where("Customer_SlNo", $item->Customer_SlNo)->update('tbl_customer', $customerData);
        }
        echo "Customer restore successful";
    }

    //invoice

    public function deletedSaleInvoice($saleId)
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['saleId'] = $saleId;
        $data['title'] = "Deleted Sale Invoice";
        $data['content'] = $this->load->view('Administrator/trash/deletedSaleInvoice', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function deletedPurchaseInvoice($purchaseId)
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['purchaseId'] = $purchaseId;
        $data['title'] = "Deleted Purchase Invoice";
        $data['content'] = $this->load->view('Administrator/trash/deletedPurchaseInvoice', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
}
