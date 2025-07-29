<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Page extends CI_Controller
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
        $this->load->model("Model_myclass", "mmc", TRUE);
        $this->load->model('Model_table', "mt", TRUE);
        date_default_timezone_set('Asia/Dhaka');

        if ($this->session->has_userdata('products')) {
            $this->session->unset_userdata('products');
            $this->session->unset_userdata('xAxis');
            $this->session->unset_userdata('yAxis');
            $this->session->unset_userdata('single');
        }
    }
    public function index()
    {
        $data['title'] = "Dashboard";
        $data['content'] = $this->load->view('Administrator/dashboard', $data, TRUE);
        $this->load->view('Administrator/master_dashboard', $data);
    }
    public function module($value)
    {
        $data['title'] = "Dashboard";

        $sdata['module'] = $value;
        $this->session->set_userdata($sdata);

        $data['content'] = $this->load->view('Administrator/dashboard', $data, TRUE);
        $this->load->view('Administrator/master_dashboard', $data);
    }

    // Product Category 
    public function getCategories()
    {
        $categories = $this->db->query("select * from tbl_productcategory where status = 'a' order by ProductCategory_SlNo desc")->result();
        echo json_encode($categories);
    }

    public function add_category()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Add Category";
        $data['content'] = $this->load->view('Administrator/add_prodcategory', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function insert_category()
    {
        $res = ['status' => false];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $query = $this->db->query("SELECT * from tbl_productcategory where branch_id = '$this->brunch' AND ProductCategory_Name = '$data->ProductCategory_Name'")->row();
            if (!empty($query)) {
                $category = array(
                    'status'     => 'a',
                    "UpdateBy"   => $this->session->userdata("userId"),
                    "UpdateTime" => date("Y-m-d H:i:s"),
                    "last_update_ip" => get_client_ip()
                );
                $this->db->where('ProductCategory_SlNo', $query->ProductCategory_SlNo);
                $this->db->update('tbl_productcategory', $category);
            } else {
                $category = array(
                    "ProductCategory_Name"        => $data->ProductCategory_Name,
                    "ProductCategory_Description" => $data->ProductCategory_Description,
                    "status"                      => 'a',
                    "AddBy"                       => $this->session->userdata("userId"),
                    "AddTime"                     => date("Y-m-d H:i:s"),
                    "last_update_ip"              => get_client_ip(),
                    "branch_id"                   => $this->brunch
                );
                $this->db->insert('tbl_productcategory', $category);
            }

            $res = ['status' => true, 'message' => 'Category added successfully'];
        } catch (\Throwable $th) {
            $res = ['status' => false, 'message' => $th->getMessage()];
        }

        echo json_encode($res);
    }
    public function update_category()
    {
        $res = ['status' => false];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $category = array(
                "ProductCategory_Name"        => $data->ProductCategory_Name,
                "ProductCategory_Description" => $data->ProductCategory_Description,
                "UpdateBy"                    => $this->session->userdata("userId"),
                "UpdateTime"                  => date("Y-m-d H:i:s"),
                "last_update_ip" => get_client_ip()
            );
            $this->db->where('ProductCategory_SlNo', $data->ProductCategory_SlNo);
            $this->db->update('tbl_productcategory', $category);

            $res = ['status' => true, 'message' => 'Category update successfully'];
        } catch (\Throwable $th) {
            $res = ['status' => false, 'message' => $th->getMessage()];
        }
        echo json_encode($res);
    }
    public function catdelete()
    {
        $data = json_decode($this->input->raw_input_stream);
        $category = array(
            'status'         => 'd',
            "DeletedBy"      => $this->session->userdata("userId"),
            "DeletedTime"    => date("Y-m-d H:i:s"),
            "last_update_ip" => get_client_ip()
        );
        $this->db->where('ProductCategory_SlNo', $data->categoryId);
        $this->db->update('tbl_productcategory', $category);
        echo json_encode(['status' => true, 'message' => 'Category delete successfully']);
    }
    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    // unit 
    public function unit()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Add Unit";
        $data['content'] = $this->load->view('Administrator/unit', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function insert_unit()
    {
        $res = ['status' => false];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $query = $this->db->query("select * from tbl_unit where Unit_Name = '$data->Unit_Name'")->row();
            if (!empty($query)) {
                $unit = array(
                    'status'     => 'a',
                    "UpdateBy"   => $this->session->userdata("userId"),
                    "UpdateTime" => date("Y-m-d H:i:s"),
                    "last_update_ip" => get_client_ip()
                );
                $this->db->where('Unit_SlNo', $query->Unit_SlNo);
                $this->db->update('tbl_unit', $unit);
            } else {
                $unit = array(
                    "Unit_Name"              => $data->Unit_Name,
                    "status"              => 'a',
                    "AddBy"                  => $this->session->userdata("userId"),
                    "AddTime"                => date("Y-m-d H:i:s"),
                    "last_update_ip" => get_client_ip()
                );
                $this->db->insert('tbl_unit', $unit);
            }

            $res = ['status' => true, 'message' => 'Unit added successfully'];
        } catch (\Throwable $th) {
            $res = ['status' => false, 'message' => $th->getMessage()];
        }

        echo json_encode($res);
    }
    public function unitupdate()
    {
        $res = ['status' => false];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $unit = array(
                "Unit_Name"                     => $data->Unit_Name,
                "UpdateBy"                          => $this->session->userdata("userId"),
                "UpdateTime"                        => date("Y-m-d H:i:s"),
                "last_update_ip" => get_client_ip()
            );
            $this->db->where('Unit_SlNo', $data->Unit_SlNo);
            $this->db->update('tbl_unit', $unit);

            $res = ['status' => true, 'message' => 'Unit update successfully'];
        } catch (\Throwable $th) {
            $res = ['status' => false, 'message' => $th->getMessage()];
        }
        echo json_encode($res);
    }
    public function unitdelete()
    {
        $data = json_decode($this->input->raw_input_stream);
        $unit = array(
            'status'         => 'd',
            "DeletedBy"      => $this->session->userdata("userId"),
            "DeletedTime"    => date("Y-m-d H:i:s"),
            "last_update_ip" => get_client_ip()
        );
        $this->db->where('Unit_SlNo', $data->unitId);
        $this->db->update('tbl_unit', $unit);
        echo json_encode(['status' => true, 'message' => 'Unit delete successfully']);
    }

    public function getUnits()
    {
        $units = $this->db->query("select * from tbl_unit where status = 'a' order by Unit_SlNo desc")->result();
        echo json_encode($units);
    }
    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    //Area 
    public function area()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Add Area";
        $data['content'] = $this->load->view('Administrator/add_area', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function insert_area()
    {
        $res = ['status' => false];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $query = $this->db->query("select * from tbl_district where District_Name = '$data->District_Name'")->row();
            if (!empty($query)) {
                $area = array(
                    'status'     => 'a',
                    "UpdateBy"   => $this->session->userdata("userId"),
                    "UpdateTime" => date("Y-m-d H:i:s"),
                    "last_update_ip" => get_client_ip()
                );
                $this->db->where('District_SlNo', $query->District_SlNo);
                $this->db->update('tbl_district', $area);
            } else {
                $area = array(
                    "District_Name"          => $data->District_Name,
                    "AddBy"                  => $this->session->userdata("userId"),
                    "AddTime"                => date("Y-m-d H:i:s"),
                    "last_update_ip" => get_client_ip()
                );
                $this->db->insert('tbl_district', $area);
            }

            $res = ['status' => true, 'message' => 'Area added successfully'];
        } catch (\Throwable $th) {
            $res = ['status' => false, 'message' => $th->getMessage()];
        }

        echo json_encode($res);
    }

    public function areaupdate()
    {
        $res = ['status' => false];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $area = array(
                "District_Name"                     => $data->District_Name,
                "UpdateBy"                          => $this->session->userdata("userId"),
                "UpdateTime"                        => date("Y-m-d H:i:s"),
                "last_update_ip" => get_client_ip()
            );
            $this->db->where('District_SlNo', $data->District_SlNo);
            $this->db->update('tbl_district', $area);

            $res = ['status' => true, 'message' => 'Area update successfully'];
        } catch (\Throwable $th) {
            $res = ['status' => false, 'message' => $th->getMessage()];
        }
        echo json_encode($res);
    }
    public function areadelete()
    {
        $data = json_decode($this->input->raw_input_stream);
        $area = array(
            'status'         => 'd',
            "DeletedBy"      => $this->session->userdata("userId"),
            "DeletedTime"    => date("Y-m-d H:i:s"),
            "last_update_ip" => get_client_ip()
        );
        $this->db->where('District_SlNo', $data->areaId);
        $this->db->update('tbl_district', $area);
        echo json_encode(['status' => true, 'message' => 'Area delete successfully']);
    }

    public function getDistricts()
    {
        $districts = $this->db->query("select * from tbl_district d where d.status = 'a' order by District_SlNo desc")->result();
        echo json_encode($districts);
    }

    // Country 
    public function add_country()
    {
        $data['title'] = "Add Country";
        $data['content'] = $this->load->view('Administrator/add_country', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function insert_country()
    {
        $mail = $this->input->post('Country');
        $query = $this->db->query("SELECT CountryName from tbl_country where CountryName = '$mail'");

        if ($query->num_rows() > 0) {
            echo "F";
        } else {
            $data = array(
                "CountryName"          => $this->input->post('Country', TRUE),
                "AddBy"                  => $this->session->userdata("userId"),
                "AddTime"                => date("Y-m-d H:i:s")
            );
            $this->mt->save_data('tbl_country', $data);
            $this->load->view('Administrator/ajax/Country');
        }
    }

    public function countryedit($id)
    {
        $data['title'] = "Edit Country";
        $fld = 'Country_SlNo';
        $data['selected'] = $this->mt->select_by_id('tbl_country', $id, $fld);
        $data['content'] = $this->load->view('Administrator/edit/country_edit', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function countryupdate()
    {
        $id = $this->input->post('id');
        $fld = 'Country_SlNo';
        $data = array(
            "CountryName"                     => $this->input->post('Country', TRUE),
            "UpdateBy"                          => $this->session->userdata("userId"),
            "UpdateTime"                        => date("Y-m-d H:i:s")
        );
        $this->mt->update_data("tbl_country", $data, $id, $fld);
        $this->load->view('Administrator/ajax/Country');
    }
    public function countrydelete()
    {
        $id = $this->input->post('deleted');
        $fld = 'Country_SlNo';
        $this->mt->delete_data("tbl_country", $id, $fld);
        $this->load->view('Administrator/ajax/Country');
    }
    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    //Company Profile

    public function getCompanyProfile()
    {
        $companyProfile = $this->db->query("select * from tbl_company order by Company_SlNo desc limit 1")->row();
        echo json_encode($companyProfile);
    }

    public function company_profile()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Company Profile";
        $data['selected'] = $this->db->query("
            select * from tbl_company order by Company_SlNo desc limit 1
        ")->row();
        $data['content'] = $this->load->view('Administrator/company_profile', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function company_profile_insert()
    {
        $id = $this->brunch;
        $inpt = $this->input->post('inpt', true);
        $fld = 'branch_id';
        $this->load->library('upload');
        $config['upload_path'] = './uploads/company_profile_org/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = '10000';
        $config['image_width'] = '4000';
        $config['image_height'] = '4000';
        $this->upload->initialize($config);

        $data['Company_Name'] =  $this->input->post('Company_name', true);
        $data['Repot_Heading'] =  $this->input->post('Description', true);

        $xx = $this->mt->select_by_id("tbl_company", $id, $fld);

        $image = $this->upload->do_upload('companyLogo');
        $images = $this->upload->data();

        if ($image != "") {
            if ($xx['Company_Logo_thum'] && $xx['Company_Logo_thum']) {
                unlink("./uploads/company_profile_thum/" . $xx['Company_Logo_thum']);
                unlink("./uploads/company_profile_org/" . $xx['Company_Logo_thum']);
            }
            $data['Company_Logo_thum'] = $images['file_name'];

            $config['image_library'] = 'gd2';
            $config['source_image'] = $this->upload->upload_path . $this->upload->file_name;
            $config['new_image'] = 'uploads/' . 'company_profile_thum/' . $this->upload->file_name;
            $config['maintain_ratio'] = FALSE;
            $config['width'] = 165;
            $config['height'] = 175;
            $this->load->library('image_lib', $config);
            $this->image_lib->resize();
            $data['Company_Logo_thum'] = $this->upload->file_name;
        } else {

            $data['Company_Logo_thum'] = $xx['Company_Logo_thum'];
            $data['Company_Logo_thum'] = $xx['Company_Logo_thum'];
        }
        $data['print_type'] = $inpt;
        $data['branch_id'] = $this->brunch;
        $this->mt->save_data("tbl_company", $data, $id, $fld);
        $id = '1';
        redirect('Administrator/Page/company_profile');
    }

    public function company_profile_Update()
    {
        $inpt                     = $this->input->post('inpt', true);
        $data['Company_Name']     = $this->input->post('Company_name', true);
        $data['InvoiceHeder']     = $this->input->post('InvoiceHeder', true);
        $data['Currency_Name']    = $this->input->post('Currency_Name', true);
        $data['SubCurrency_Name'] = $this->input->post('SubCurrency_Name', true);
        $data['Repot_Heading']    = $this->input->post('Description', true);
        $data['dueStatus']        = $this->input->post('dueStatus', true);
        $data['last_update_ip']   = get_client_ip();

        $xx = $this->db->query("select * from tbl_company order by Company_SlNo desc limit 1")->row();


        if (!isset($_FILES['companyLogo']) || $_FILES['companyLogo']['error'] == UPLOAD_ERR_NO_FILE) {
            $data['print_type'] = $inpt;
            $this->db->update('tbl_company', $data);
            $id = '1';
            redirect('Administrator/Page/company_profile');
        } else {
            if (file_exists($xx->Company_Logo_thum)) {
                unlink($xx->Company_Logo_thum);
            }
            if (file_exists($xx->Company_Logo_thum)) {
                unlink($xx->Company_Logo_thum);
            }
            $thumPath = $this->mt->uploadImage($_FILES, 'companyLogo', 'uploads/company_profile_thum', "");
            $data['Company_Logo_thum'] = $thumPath;
            $orgPath = $this->mt->uploadImage($_FILES, 'companyLogo', 'uploads/company_profile_org', "");
            $data['Company_Logo_org'] = $orgPath;

            $data['print_type'] = $inpt;
            $this->db->update('tbl_company', $data);
            $id = '1';
            redirect('Administrator/Page/company_profile');
        }
    }
    //^^^^^^^^^^^^^^^^^^^^^
    // Brunch Name

    public function getBranches()
    {
        $branches = $this->db->query("
            select 
            *,
            case status
                when 'a' then 'Active'
                else 'Inactive'
            end as active_status
            from tbl_branch
        ")->result();
        echo json_encode($branches);
    }

    public function getCurrentBranch()
    {
        $branch = $this->Billing_model->company_branch_profile($this->brunch);
        echo json_encode($branch);
    }

    public function changeBranchstatus()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $status = $this->db->query("select * from tbl_branch where branch_id = ?", $data->branchId)->row()->status;
            $status = $status == 'a' ? 'd' : 'a';
            $this->db->set('status', $status)->where('branch_id', $data->branchId)->update('tbl_branch');
            $res = ['success' => true, 'message' => 'status changed'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function branch()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Add Brunch";
        $data['content'] = $this->load->view('Administrator/brunch/add_branch', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function addBranch()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            // $branch = json_decode($this->input->raw_input_stream);
            $branch = json_decode($this->input->post('data'));
            // $branch = (array)$branchObj;
            // echo json_encode($branch);
            // exit;

            $nameCount = $this->db->query("select * from tbl_branch where Branch_name = ?", $branch->name)->num_rows();
            if ($nameCount > 0) {
                $res = ['success' => false, 'message' => $branch->name . ' already exists'];
                echo json_encode($res);
                exit;
            }

            $newBranch = array(
                'Branch_name'    => $branch->name,
                'Branch_title'   => $branch->title,
                'Branch_phone'   => $branch->phone,
                'Branch_address' => $branch->address,
                'Branch_sales'   => '2',
                'AddBy'          => $this->session->userdata("userId"),
                'AddTime'        => date('Y-m-d H:i:s'),
                'status'         => 'a',
                'last_update_ip' => get_client_ip(),
            );

            $this->db->insert('tbl_branch', $newBranch);
            $branch_id = $this->db->insert_id();

            if (!empty($_FILES)) {
                $imagePath = $this->mt->uploadImage($_FILES, 'image', 'uploads/branche', "");
                $this->db->query("update tbl_branch c set c.header_image = ? where c.branch_id = ?", [$imagePath, $branch_id]);

                $imagePath = $this->mt->uploadImage($_FILES, 'image2', 'uploads/branche/foot', "");
                $this->db->query("update tbl_branch c set c.footer_image = ? where c.branch_id = ?", [$imagePath, $branch_id]);
            }

            $res = ['success' => true, 'message' => 'Branch added'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateBranch()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            // $branch = json_decode($this->input->raw_input_stream);
            $branch = json_decode($this->input->post('data'));

            $nameCount = $this->db->query("select * from tbl_branch where Branch_name = ? and branch_id != ?", [$branch->name, $branch->branchId])->num_rows();
            if ($nameCount > 0) {
                $res = ['success' => false, 'message' => $branch->name . ' already exists'];
                echo json_encode($res);
                exit;
            }

            $newBranch = array(
                'Branch_name'    => $branch->name,
                'Branch_title'   => $branch->title,
                'Branch_phone' => $branch->phone,
                'Branch_address' => $branch->address,
                'UpdateBy'       => $this->session->userdata("userId"),
                'UpdateTime'     => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
            );
            $this->db->set($newBranch)->where('branch_id', $branch->branchId)->update('tbl_branch');

            $branchImage = $this->db->query("select * from tbl_branch c where c.branch_id = ?", $branch->branchId)->row();
            if (!empty($_FILES)) {
                $oldImgFile = $branchImage->header_image;
                if (file_exists($oldImgFile)) {
                    unlink($oldImgFile);
                }
                $imagePath = $this->mt->uploadImage($_FILES, 'image', 'uploads/branche', "");
                $this->db->query("update tbl_branch set header_image = ? where branch_id = ?", [$imagePath, $branch->branchId]);
            }
            if (!empty($_FILES)) {
                $oldImgFile = $branchImage->footer_image;
                if (file_exists($oldImgFile)) {
                    unlink($oldImgFile);
                }
                $imagePath = $this->mt->uploadImage($_FILES, 'image2', 'uploads/branche/foot', "");
                $this->db->query("update tbl_branch set footer_image = ? where branch_id = ?", [$imagePath, $branch->branchId]);
            }

            $res = ['success' => true, 'message' => 'Branch updated'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    //^^^^^^^^^^^^^^^^^^^^^^^^
    public function add_color()
    {
        $data['title'] = "Add color";
        $data['content'] = $this->load->view('Administrator/add_color', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function insert_color()
    {
        $colorname = $this->input->post('colorname');
        $query = $this->db->query("SELECT color_name from tbl_color where color_name = '$colorname'");

        if ($query->num_rows() > 0) {
            $exits = false;
            echo json_encode($exits);
        } else {
            $data = array(
                "color_name"      => $this->input->post('colorname', TRUE),
                "status"          => 'a'

            );
            if ($this->mt->save_data('tbl_color', $data)) {
                $msg = true;
                echo json_encode($msg);
            }
        }
    }

    public function colordelete()
    {
        $id = $this->input->post('deleted');
        $fld = 'color_SiNo';
        $this->mt->delete_data("tbl_color", $id, $fld);
        echo "Success";
    }

    public function colorupdate()
    {
        $id = $this->input->post('id');
        $colorname = $this->input->post('colorname');
        $query = $this->db->query("SELECT color_name from tbl_color where color_name = '$colorname'");

        if ($query->num_rows() > 1) {
            $exits = false;
            echo json_encode($exits);
        } else {
            $fld = 'color_SiNo';
            $data = array(
                "color_name" => $this->input->post('colorname', TRUE)
            );
            if ($this->mt->update_data("tbl_color", $data, $id, $fld)) {
                $msg = true;
                echo json_encode($msg);
            }
        }
    }
    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

    public function getBrands()
    {
        $brands = $this->db->query("select * from tbl_brand where status = 'a'")->result();
        echo json_encode($brands);
    }

    public function add_brand()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Add Brand";
        $data['brand'] =  $this->Billing_model->select_brand($this->brunch);
        $data['content'] = $this->load->view('Administrator/add_brand', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function insert_brand()
    {
        $res = ['status' => false];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $query = $this->db->query("select * from tbl_brand where brand_name = '$data->brand_name' and branch_id = ?", [$this->session->userdata('BRANCHid')])->row();
            if (!empty($query)) {
                $brand = array(
                    'status'     => 'a'
                );
                $this->db->where('brand_SiNo', $query->brand_SiNo);
                $this->db->update('tbl_brand', $brand);
            } else {
                $brand = array(
                    "brand_name" => $data->brand_name,
                    "status"     => 'a',
                    'branch_id' => $this->session->userdata('BRANCHid')
                );
                $this->db->insert('tbl_brand', $brand);
            }

            $res = ['status' => true, 'message' => 'Brand added successfully'];
        } catch (\Throwable $th) {
            $res = ['status' => false, 'message' => $th->getMessage()];
        }

        echo json_encode($res);
    }

    public function Update_brand()
    {
        $res = ['status' => false];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $brand = array(
                "brand_name"  => $data->brand_name,
            );
            $this->db->where('brand_SiNo', $data->brand_SiNo);
            $this->db->update('tbl_brand', $brand);

            $res = ['status' => true, 'message' => 'Brand update successfully'];
        } catch (\Throwable $th) {
            $res = ['status' => false, 'message' => $th->getMessage()];
        }
        echo json_encode($res);
    }
    public function branddelete()
    {
        $data = json_decode($this->input->raw_input_stream);
        $brand = array(
            'status'     => 'd'
        );
        $this->db->where('brand_SiNo', $data->brandId);
        $this->db->update('tbl_brand', $brand);
        echo json_encode(['status' => true, 'message' => 'Brand delete successfully']);
    }

    public function databaseBackup()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Database Backup";
        $data['content'] = $this->load->view('Administrator/database_backup', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getMotherApiContent()
    {
        $url = 'http://linktechbd.com/motherapi/index.php';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Set timeout in seconds (e.g., 2 seconds)
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        $response_json = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code == '200') {
            echo $response_json;
        } else {
            echo 'Welcome to Link-Up Technology Ltd.';
        }
    }
}
