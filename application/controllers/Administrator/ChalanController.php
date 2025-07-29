<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class ChalanController extends CI_Controller
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
    }

    public function index()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Chalan Entry";
        $data['chalanId'] = 0;
        $data['invoice'] = $this->mt->generateFalseChalanInvoice();
        $data['content'] = $this->load->view('Administrator/chalan/chalan_entry', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function addChalan()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
           
            $invoice = $data->chalan->invoiceNo;
            $invoiceCount = $this->db->query("select * from tbl_quotation_master where SaleMaster_InvoiceNo = ?", $invoice)->num_rows();
            if ($invoiceCount != 0) {
                $invoice = $this->mt->generateFalseChalanInvoice();
            }

            $chalan = array(
                'SaleMaster_InvoiceNo'           => $invoice,
                'SaleMaster_SaleDate'            => $data->chalan->chalanDate,
                // 'SaleMaster_TotalSaleAmount'     => $data->chalan->total,
                // 'SaleMaster_TotalDiscountAmount' => $data->chalan->discount,
                // 'SaleMaster_TaxAmount'           => $data->chalan->vat,
                // 'SaleMaster_SubTotalAmount'      => $data->chalan->subTotal,
                'status'                         => 'a',
                "AddBy"                          => $this->session->userdata("userId"),
                'AddTime'                        => date("Y-m-d H:i:s"),
                'last_update_ip'                 => get_client_ip(),
                'branch_id'                      => $this->session->userdata("BRANCHid")
            );

            if ($data->customer->Customer_Type == 'G') {
                $chalan['SalseCustomer_IDNo']          = Null;
                $chalan['customerType']                = $data->customer->Customer_Type;
                $chalan['SaleMaster_customer_name']    = $data->customer->Customer_Name;
                $chalan['SaleMaster_customer_mobile']  = $data->customer->Customer_Mobile;
                $chalan['SaleMaster_customer_address'] = $data->customer->Customer_Address;
            } else {
                $chalan['customerType']       = $data->customer->Customer_Type;
                $chalan['SalseCustomer_IDNo'] = $data->customer->Customer_SlNo;
            }

            $this->db->insert('tbl_chalan_master', $chalan);

            $chalanId = $this->db->insert_id();

            foreach ($data->cart as $cartProduct) {
                $chalanDetails = array(
                    'SaleMaster_IDNo'           => $chalanId,
                    'Product_IDNo'              => $cartProduct->productId,
                    'product_note'              => $cartProduct->note,
                    'SaleDetails_TotalQuantity' => $cartProduct->quantity,
                    // 'SaleDetails_Rate'          => $cartProduct->salesRate,
                    // 'SaleDetails_TotalAmount'   => $cartProduct->total,
                    'status'                    => 'a',
                    'AddBy'                     => $this->session->userdata("userId"),
                    'AddTime'                   => date('Y-m-d H:i:s'),
                    'last_update_ip'            => get_client_ip(),
                    'branch_id'                 => $this->session->userdata('BRANCHid')
                );
                $this->db->insert('tbl_chalan_details', $chalanDetails);
            }
            $res = ['success' => true, 'message' => 'Chalan added', 'chalanId' => $chalanId];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateChalan()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $chalanId = $data->chalan->chalanId;

            $chalan = array(
                'SaleMaster_InvoiceNo' => $data->chalan->invoiceNo,
                'SaleMaster_SaleDate' => $data->chalan->chalanDate,
                'SaleMaster_customer_name' => $data->chalan->customerName,
                'SaleMaster_customer_mobile' => $data->chalan->customerMobile,
                'SaleMaster_customer_address' => $data->chalan->customerAddress,
                // 'SaleMaster_TotalSaleAmount' => $data->chalan->total,
                // 'SaleMaster_TotalDiscountAmount' => $data->chalan->discount,
                // 'SaleMaster_TaxAmount' => $data->chalan->vat,
                // 'SaleMaster_SubTotalAmount' => $data->chalan->subTotal,
                'status' => 'a',
                "AddBy" => $this->session->userdata("userId"),
                'AddTime' => date("Y-m-d H:i:s"),
                'last_update_ip' => get_client_ip(),
                'branch_id' => $this->session->userdata("BRANCHid")
            );

            $this->db->where('SaleMaster_SlNo', $chalanId)->update('tbl_chalan_master', $chalan);

            $this->db->query("delete from tbl_chalan_details where SaleMaster_IDNo = ?", $chalanId);

            foreach ($data->cart as $cartProduct) {
                $chalanDetails = array(
                    'SaleMaster_IDNo' => $chalanId,
                    'Product_IDNo' => $cartProduct->productId,
                    'product_note' => $cartProduct->note,
                    'SaleDetails_TotalQuantity' => $cartProduct->quantity,
                    // 'SaleDetails_Rate' => $cartProduct->salesRate,
                    // 'SaleDetails_TotalAmount' => $cartProduct->total,
                    'status' => 'a',
                    'AddBy' => $this->session->userdata("userId"),
                    'AddTime' => date('Y-m-d H:i:s'),
                    'AddTime' => get_client_ip(),
                    'branch_id' => $this->session->userdata('BRANCHid')
                );
                $this->db->insert('tbl_chalan_details', $chalanDetails);
            }

            $res = ['success' => true, 'message' => 'Chalan updated', 'chalanId' => $chalanId];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function chalanRecord()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Chalan Record";
        $data['content'] = $this->load->view('Administrator/chalan/chalan_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getChalans()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and cm.SaleMaster_SaleDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->chalanId) && $data->chalanId != '') {
            $clauses .= " and cm.SaleMaster_SlNo = '$data->chalanId'";
            $res['chalanDetails'] = $this->db->query("
                select 
                    cd.*,
                    p.Product_Code,
                    p.Product_Name,
                    pc.ProductCategory_Name,
                    u.Unit_Name

                from tbl_chalan_details cd
                join tbl_product p on p.Product_SlNo = cd.Product_IDNo
                join tbl_unit u on u.Unit_SlNo = p.Unit_ID
                join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
                where cd.SaleMaster_IDNo = ?
            ", $data->chalanId)->result();
        }

        $res['chalans'] = $this->db->query("
            select *,
            ifnull(c.Customer_Name, cm.SaleMaster_customer_name) as SaleMaster_customer_name, 
            ifnull(c.Customer_Mobile, cm.SaleMaster_customer_mobile) as SaleMaster_customer_mobile, 
            ifnull(c.Customer_Address, cm.SaleMaster_customer_address) as SaleMaster_customer_address,
            c.Customer_Code,
            c.owner_name
            from tbl_chalan_master cm
            left join tbl_customer c on c.Customer_SlNo = cm.SalseCustomer_IDNo 
            where cm.status = 'a'
            and cm.branch_id = ?
            $clauses
            order by cm.SaleMaster_SlNo desc
        ", $this->session->userdata('BRANCHid'))->result();
        echo json_encode($res);
    }

    public function editChalan($chalanId)
    {
        $data['title'] = "Chalan Edit";
        $data['chalanId'] = $chalanId;
        $data['invoice'] = '';
        $data['content'] = $this->load->view('Administrator/chalan/chalan_entry', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function deleteChalan()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $this->db->query("delete from tbl_chalan_master where SaleMaster_SlNo = ?", $data->chalanId);
            $this->db->query("delete from tbl_chalan_details where SaleMaster_IDNo = ?", $data->chalanId);
            $res = ['success' => true, 'message' => 'Chalan deleted'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }
        echo json_encode($res);
    }

    public function chalanInvoice($chalanId)
    {
        $data['title'] = "Chalan Invoice";
        $data['chalanId'] = $chalanId;
        $data['content'] = $this->load->view('Administrator/chalan/chalan', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
}