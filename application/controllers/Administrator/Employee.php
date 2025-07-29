<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Employee extends CI_Controller
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

        $vars['branch_info'] = $this->Billing_model->company_branch_profile($this->brunch);
        $this->load->vars($vars);
    }

    public function index()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Employee";
        $data['content'] = $this->load->view('Administrator/employee/add_employee', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getEmployees()
    {
        $data = json_decode($this->input->raw_input_stream);

        $statusClause = " and e.status = 'a'";
        if (isset($data->with_deactive)) {
            $statusClause .= " and e.status != 'd'";
        }
        $month_id = $data->month_id ?? null;

        $employees = $this->db->query("
            SELECT 
                e.*,
                dp.Department_Name,
                ds.Designation_Name,

                (
                    select ifnull(sum(sa.advance), 0) from tbl_salary_advance sa
                    where sa.status = 'a'
                    and sa.employee_id = e.Employee_SlNo
                    and sa.branch_id = " . $this->session->userdata('BRANCHid') . "
                    " . ($month_id == null ? "" : " and sa.month_id  = '$month_id'") . "
                ) as advance,

                (
                    select ifnull(sum(cs.conveyance), 0) from tbl_conveyance_salary cs
                    where cs.transaction_type = 'receive'
                    and cs.employee_id = e.Employee_SlNo
                    and cs.status = 'a'
                    and cs.branch_id= " . $this->session->userdata('BRANCHid') . "
                   " . ($month_id == null ? "" : " and cs.month_id  = '$month_id'") . "
                ) as conveyance,

                concat(e.Employee_Name, ' - ', e.Employee_ID) as display_name
            from tbl_employee e 
            join tbl_department dp on dp.Department_SlNo = e.Department_ID
            left join tbl_designation ds on ds.Designation_SlNo = e.Designation_ID
            where e.branch_id = ?
            $statusClause
        ", $this->session->userdata('BRANCHid'))->result();

        echo json_encode($employees);
    }

    public function getMonths()
    {
        $months = $this->db->query(
            "SELECT * from tbl_month
            order by month_id desc
        "
        )->result();

        echo json_encode($months);
    }

    public function getEmployeePayments()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->employeeId) && $data->employeeId != '') {
            $clauses .= " and e.Employee_SlNo = '$data->employeeId'";
        }

        if (isset($data->month) && $data->month != '') {
            $clauses .= " and ep.month_id = '$data->month'";
        }

        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and ep.payment_date between '$data->dateFrom' and '$data->dateTo'";
        }

        $payments = $this->db->query("
            select 
                ep.*,
                e.Employee_Name,
                e.Employee_ID,
                e.salary_range,
                dp.Department_Name,
                ds.Designation_Name,
                m.month_name

            from tbl_employee_payment ep
            join tbl_employee e on e.Employee_SlNo = ep.Employee_SlNo
            join tbl_department dp on dp.Department_SlNo = e.Department_ID
            join tbl_designation ds on ds.Designation_SlNo = e.Designation_ID
            join tbl_month m on m.month_id = ep.month_id

            where ep.branch_id = ?
            and ep.status = 'a'
            $clauses
            order by ep.employee_payment_id desc
        ", $this->session->userdata('BRANCHid'))->result();

        echo json_encode($payments);
    }

    public function getSalarySummary()
    {

        $data = json_decode($this->input->raw_input_stream);

        $yearMonth = date("Ym", strtotime($data->monthName));

        $summary = $this->db->query("
            select 
                e.*,
                dp.Department_Name,
                ds.Designation_Name,
                (
                    select ifnull(sum(ep.payment_amount), 0) from tbl_employee_payment ep
                    where ep.Employee_SlNo = e.Employee_SlNo
                    and ep.status = 'a'
                    and ep.month_id = " . $data->monthId . "
                    and ep.branch_id = " . $this->session->userdata('BRANCHid') . "
                ) as paid_amount,
                
                (
                    select ifnull(sum(ep.deduction_amount), 0) from tbl_employee_payment ep
                    where ep.Employee_SlNo = e.Employee_SlNo
                    and ep.status = 'a'
                    and ep.month_id = " . $data->monthId . "
                    and ep.branch_id = " . $this->session->userdata('BRANCHid') . "
                ) as deducted_amount,
                
                (
                    select e.salary_range - (paid_amount + deducted_amount)
                ) as due_amount
                
            from tbl_employee e 
            join tbl_department dp on dp.Department_SlNo = e.Department_ID
            join tbl_designation ds on ds.Designation_SlNo = e.Designation_ID
            where e.status = 'a'
            and " . $yearMonth . " >= extract(YEAR_MONTH from e.Employee_JoinDate)
            and e.branch_id = " . $this->session->userdata('BRANCHid') . "
        ")->result();

        echo json_encode($summary);
    }

    public function getPayableSalary()
    {
        $data = json_decode($this->input->raw_input_stream);

        $payableAmount = $this->db->query("
            select 
            (e.salary_range - ifnull(sum(ep.payment_amount + ep.deduction_amount), 0)) as payable_amount
            from tbl_employee_payment ep
            join tbl_employee e on e.Employee_SlNo = ep.Employee_SlNo
            where ep.status = 'a'
            and ep.month_id = ?
            and ep.Employee_SlNo = ?
            and ep.branch_id = ?        
        ", [$data->monthId, $data->employeeId, $this->brunch])->row()->payable_amount;

        echo $payableAmount;
    }

    //Designation
    public function designation()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Add Designation";
        $data['content'] = $this->load->view('Administrator/employee/designation', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getDesignations()
    {
        $designations = $this->db->query("select * from tbl_designation where status = 'a'")->result();
        echo json_encode($designations);
    }

    public function insert_designation()
    {
        $res = ['status' => false];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $designation = array(
                "Designation_Name" => $data->Designation_Name,
                "status"          => 'a',
                "AddBy"           => $this->session->userdata("userId"),
                "AddTime"         => date("Y-m-d H:i:s"),
                "last_update_ip"         => get_client_ip(),

            );
            $this->db->insert('tbl_designation', $designation);
            $res = ['status' => true, 'message' => "Designation added successfull"];
        } catch (\Throwable $th) {
            $res = ['status' => false, 'message' => $th->getMessage()];
        }
        echo json_encode($res);
    }


    public function designationupdate()
    {
        $res = ['status' => false];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $designation = array(
                "Designation_Name" => $data->Designation_Name,
                "UpdateBy"         => $this->session->userdata("userId"),
                "UpdateTime"       => date("Y-m-d H:i:s"),
                "last_update_ip"         => get_client_ip(),
            );
            $this->db->where('Designation_SlNo', $data->Designation_SlNo);
            $this->db->update('tbl_designation', $designation);
            $res = ['status' => true, 'message' => "Designation Update"];
        } catch (\Throwable $th) {
            $res = ['status' => false, 'message' => $th->getMessage()];
        }
        echo json_encode($res);
    }

    public function designationdelete()
    {
        $data = json_decode($this->input->raw_input_stream);
        $designation = array(
            'status'         => 'd',
            "DeletedBy"      => $this->session->userdata("userId"),
            "DeletedTime"    => date("Y-m-d H:i:s"),
            "last_update_ip" => get_client_ip(),
        );
        $this->db->where('Designation_SlNo', $data->designationId);
        $this->db->update('tbl_designation', $designation);
        echo json_encode(['status' => true, 'message' => 'Designation delete successfully']);
    }

    public function depertment()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Add Depertment";
        $data['content'] = $this->load->view('Administrator/employee/depertment', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }


    public function getDepartments()
    {
        $departments = $this->db->query("select * from tbl_department where status = 'a'")->result();
        echo json_encode($departments);
    }

    public function insert_depertment()
    {
        $res = ['status' => false];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $department = array(
                "Department_Name" => $data->Department_Name,
                "status"          => 'a',
                "AddBy"           => $this->session->userdata("userId"),
                "AddTime"         => date("Y-m-d H:i:s"),
                "last_update_ip"         => get_client_ip(),

            );
            $this->db->insert('tbl_department', $department);
            $res = ['status' => true, 'message' => "Department added successfull"];
        } catch (\Throwable $th) {
            $res = ['status' => false, 'message' => $th->getMessage()];
        }
        echo json_encode($res);
    }

    public function depertmentupdate()
    {
        $res = ['status' => false];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $department = array(
                "Department_Name" => $data->Department_Name,
                "UpdateBy"                    => $this->session->userdata("userId"),
                "UpdateTime"                  => date("Y-m-d H:i:s"),
                "last_update_ip"         => get_client_ip(),
            );
            $this->db->where('Department_SlNo', $data->Department_SlNo);
            $this->db->update('tbl_department', $department);
            $res = ['status' => true, 'message' => "Department Update"];
        } catch (\Throwable $th) {
            $res = ['status' => false, 'message' => $th->getMessage()];
        }
        echo json_encode($res);
    }

    public function depertmentdelete()
    {
        $data = json_decode($this->input->raw_input_stream);
        $department = array(
            'status'         => 'd',
            "DeletedBy"      => $this->session->userdata("userId"),
            "DeletedTime"    => date("Y-m-d H:i:s"),
            "last_update_ip" => get_client_ip(),
        );
        $this->db->where('Department_SlNo', $data->departmentId);
        $this->db->update('tbl_department', $department);
        echo json_encode(['status' => true, 'message' => 'Department delete successfully']);
    }

    //^^^^^^^^^^^^^^^^^^^^
    public function emplists($status = 'all')
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Employee List";
        $data['employes'] = $this->HR_model->get_all_employee_list($status);
        $data['content'] = $this->load->view('Administrator/employee/list', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    // fancybox add 
    public function month()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = 'Month';
        $data['content'] = $this->load->view('Administrator/employee/month', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function insert_month()
    {
        $res = ['status' => false];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $check = $this->db->query("select * from tbl_month where month_name = ? ", $data->month_name)->row();
            if (!empty($check)) {
                $res = ['status' => false, 'message' => "Month already exists"];
            } else {
                $month = array(
                    "month_name" => $data->month_name,
                );
                $this->db->insert('tbl_month', $month);
                $res = ['status' => true, 'message' => "Month added successfull"];
            }
        } catch (\Throwable $th) {
            $res = ['status' => false, 'message' => $th->getMessage()];
        }
        echo json_encode($res);
    }


    public function updateMonth()
    {
        $res = ['status' => false];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $check = $this->db->query("select * from tbl_month where month_id != ? and month_name = ? ", [$data->month_id, $data->month_name])->row();
            if (!empty($check)) {
                $res = ['status' => false, 'message' => "Month already exists"];
            } else {
                $month = array(
                    "month_name" => $data->month_name,
                );
                $this->db->where('month_id', $data->month_id);
                $this->db->update('tbl_month', $month);
                $res = ['status' => true, 'message' => "Month Update"];
            }
        } catch (\Throwable $th) {
            $res = ['status' => false, 'message' => $th->getMessage()];
        }
        echo json_encode($res);
    }

    // Employee Insert
    public function employee_insert()
    {
        $employee_code  = $this->input->post('Employeer_id', true);
        $designation_id = $this->input->post('em_Designation', true);
        $department_id  = $this->input->post('em_Depertment', true);
        $employee_name  = $this->input->post('em_name', true);
        $bio_id         = $this->input->post('bio_id', true);

        if ($bio_id) {
            $bio_id_count = $this->db->query("SELECT * from tbl_employee where bio_id = '$bio_id' and branch_id = '$this->brunch'")->num_rows();
            if ($bio_id_count != 0) {
                echo json_encode(['success' => false, 'message' => 'Bio ID Already Exist!']);
                exit;
            }
        }

        $employee_count = $this->db->query(
            "SELECT * from tbl_employee 
            where Designation_ID = '$designation_id'
            and Department_ID = '$department_id'
            and Employee_Name = '$employee_name'
            and branch_id = '$this->brunch'
        "
        )->num_rows();


        if ($employee_count != 0) {
            echo json_encode(['success' => false, 'message' => 'Duplicate Employee!']);
            exit;
        }

        $data = array();
        $this->load->library('upload');
        $config['upload_path'] = './uploads/employeePhoto_org/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = '10000';
        $config['image_width'] = '4000';
        $config['image_height'] = '4000';
        $config['file_name'] = $employee_code;
        $this->upload->initialize($config);

        $data['Designation_ID']            = $designation_id;
        $data['Department_ID']             = $department_id;
        $data['Employee_ID']               = $employee_code;
        $data['Employee_Name']             = $employee_name;
        $data['bio_id']                    = $bio_id;
        $data['Employee_JoinDate']         = $this->input->post('em_Joint_date');
        $data['Employee_Gender']           = $this->input->post('Gender', true);
        $data['Employee_BirthDate']        = $this->input->post('em_dob', true);
        $data['Employee_ContactNo']        = $this->input->post('em_contact', true);
        $data['em_reporting_boss_id']      = $this->input->post('em_reporting_boss_id', true);
        $data['Employee_Email']            = $this->input->post('ec_email', true);
        $data['Employee_Maritalstatus']    = $this->input->post('Marital', true);
        $data['Employee_FatherName']       = $this->input->post('em_father', true);
        $data['Employee_MotherName']       = $this->input->post('mother_name', true);
        $data['Employee_PrasentAddress']   = $this->input->post('em_Present_address', true);
        $data['Employee_Reference']        = $this->input->post('em_reference', true);
        $data['Employee_PermanentAddress'] = $this->input->post('em_Permanent_address', true);
        $data['salary_range']              = $this->input->post('salary_range', true);
        $data['status']                    = $this->input->post('status', true);

        $data['AddBy'] = $this->session->userdata("userId");
        $data['AddTime'] = date("Y-m-d H:i:s");
        $data['last_update_ip'] = get_client_ip();
        $data['branch_id'] = $this->session->userdata("BRANCHid");

        $this->upload->do_upload('em_photo');
        $images = $this->upload->data();
        if ($images['orig_name']) {
            $data['Employee_Pic_org'] = $images['file_name'];

            $config['image_library'] = 'gd2';
            $config['source_image'] = $this->upload->upload_path . $this->upload->file_name;
            $config['new_image'] = 'uploads/' . 'employeePhoto_thum/' . $this->upload->file_name;
            $config['maintain_ratio'] = FALSE;
            $config['width'] = 165;
            $config['height'] = 175;
            $this->load->library('image_lib', $config);
            $this->image_lib->resize();
            $data['Employee_Pic_thum'] = $this->upload->file_name;
        } else {
            $data['Employee_Pic_org'] = '';
            $data['Employee_Pic_thum'] = '';
        }

        $this->mt->save_data('tbl_employee', $data);

        echo json_encode(['success' => true, 'message' => 'Save Success!']);
        exit;
    }

    public function employee_edit($id)
    {
        $data['title'] = "Edit Employee";
        $query = $this->db->query("SELECT tbl_employee.* FROM tbl_employee  where Employee_SlNo = '$id'");
        $data['employee'] = $query->row();
        $data['content'] = $this->load->view('Administrator/employee/edit_employee', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function employee_Update()
    {
        $id = $this->input->post('iidd');
        $employee_code = $this->input->post('Employeer_id', true);
        $designation_id = $this->input->post('em_Designation', true);
        $department_id = $this->input->post('em_Depertment', true);
        $employee_name = $this->input->post('em_name', true);
        $bio_id = $this->input->post('bio_id', true);

        if ($bio_id) {
            $bio_id_count = $this->db->query("SELECT * from tbl_employee where bio_id = '$bio_id' and branch_id = '$this->brunch' and Employee_SlNo != '$id'")->num_rows();
            if ($bio_id_count != 0) {
                echo json_encode(['success' => false, 'message' => 'Bio ID Already Exist!']);
                exit;
            }
        }

        $employee_count = $this->db->query(
            "SELECT * from tbl_employee 
            where Designation_ID = '$designation_id'
            and Department_ID = '$department_id'
            and Employee_Name = '$employee_name'
            and branch_id = '$this->brunch'
            and Employee_SlNo != '$id'
        "
        )->num_rows();


        if ($employee_count != 0) {
            echo json_encode(['success' => false, 'message' => 'Duplicate Employee!']);
            exit;
        }

        $fld = 'Employee_SlNo';
        $this->load->library('upload');
        $config['upload_path'] = './uploads/employeePhoto_org/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = '10000';
        $config['image_width'] = '4000';
        $config['image_height'] = '4000';
        $config['file_name'] = $employee_code;
        $this->upload->initialize($config);

        $data['Designation_ID']            = $designation_id;
        $data['Department_ID']             = $department_id;
        $data['Employee_ID']               = $employee_code;
        $data['Employee_Name']             = $employee_name;
        $data['bio_id']                    = $bio_id;
        $data['Employee_JoinDate']         = $this->input->post('em_Joint_date');
        $data['Employee_Gender']           = $this->input->post('Gender', true);
        $data['Employee_BirthDate']        = $this->input->post('em_dob', true);
        $data['Employee_ContactNo']        = $this->input->post('em_contact', true);
        $data['em_reporting_boss_id']      = $this->input->post('em_reporting_boss_id', true);
        $data['Employee_Email']            = $this->input->post('ec_email', true);
        $data['Employee_Maritalstatus']    = $this->input->post('Marital', true);
        $data['Employee_FatherName']       = $this->input->post('em_father', true);
        $data['Employee_MotherName']       = $this->input->post('mother_name', true);
        $data['Employee_PrasentAddress']   = $this->input->post('em_Present_address', true);
        $data['Employee_Reference']        = $this->input->post('em_reference', true);
        $data['Employee_PermanentAddress'] = $this->input->post('em_Permanent_address', true);
        $data['branch_id']                 = $this->session->userdata("BRANCHid");
        $data['salary_range']              = $this->input->post('salary_range', true);
        $data['status']                    = $this->input->post('status', true);

        $data['UpdateBy'] = $this->session->userdata("userId");
        $data['UpdateTime'] = date("Y-m-d H:i:s");
        $data['last_update_ip'] = get_client_ip();;

        $xx = $this->mt->select_by_id("tbl_employee", $id, $fld);

        $image = $this->upload->do_upload('em_photo');
        $images = $this->upload->data();

        if ($image != "") {
            if ($xx['Employee_Pic_thum'] && $xx['Employee_Pic_org']) {
                unlink("./uploads/employeePhoto_thum/" . $xx['Employee_Pic_thum']);
                unlink("./uploads/employeePhoto_org/" . $xx['Employee_Pic_org']);
            }
            $data['Employee_Pic_org'] = $images['file_name'];

            $config['image_library'] = 'gd2';
            $config['source_image'] = $this->upload->upload_path . $this->upload->file_name;
            $config['new_image'] = 'uploads/' . 'employeePhoto_thum/' . $this->upload->file_name;
            $config['maintain_ratio'] = FALSE;
            $config['width'] = 165;
            $config['height'] = 175;
            $this->load->library('image_lib', $config);
            $this->image_lib->resize();
            $data['Employee_Pic_thum'] = $this->upload->file_name;
        } else {

            $data['Employee_Pic_org'] = $xx['Employee_Pic_org'];
            $data['Employee_Pic_thum'] = $xx['Employee_Pic_thum'];
        }

        $this->mt->update_data("tbl_employee", $data, $id, $fld);

        echo json_encode(['success' => true, 'message' => 'Update Success!']);
        exit;
    }

    public function employee_Delete()
    {
        $id = $this->input->post('deleted');
        $rules = array(
            'status'         => 'd',
            "DeletedBy"      => $this->session->userdata("userId"),
            "DeletedTime"    => date("Y-m-d H:i:s"),
            "last_update_ip" => get_client_ip()
        );
        $this->db->set($rules)->where('Employee_SlNo', $id)->update('tbl_employee');
    }

    public function active()
    {
        $fld = 'Employee_SlNo';
        $id = $this->input->post('deleted');
        $this->mt->active("tbl_employee", $id, $fld);
        // $this->load->view('Administrator/ajax/employee_list');
    }

    public function employeesalarypayment()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Employee Salary Payment";
        $data['content'] = $this->load->view('Administrator/employee/employee_salary', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function selectEmployee()
    {
        $data['title'] = "Employee Salary Payment";
        $employee_id = $this->input->post('employee_id');
        $query = $this->db->query("SELECT `salary_range` FROM tbl_employee where Employee_SlNo='$employee_id'");
        $data['employee'] = $query->row();
        $this->load->view('Administrator/employee/ajax_employeey', $data);
    }

    public function addEmployeePayment()
    {
        $res = ['success' => false, 'message' => 'Nothing happened'];
        try {
            $paymentObj = json_decode($this->input->raw_input_stream);
            $payment = (array)$paymentObj;
            unset($payment['employee_payment_id']);
            $payment['status']         = 'a';
            $payment['AddBy']          = $this->session->userdata('userId');
            $payment['AddTime']        = date('Y-m-d H:i:s');
            $payment['last_update_ip'] = get_client_ip();
            $payment['branch_id']      = $this->brunch;

            $this->db->insert('tbl_employee_payment', $payment);
            $res = ['success' => true, 'message' => 'Employee payment added'];
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }

        echo json_encode($res);
    }

    public function employeesalaryreport()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Employee Salary Report";
        $data['content'] = $this->load->view('Administrator/employee/employee_salary_report', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function EmployeeSalary_list()
    {
        $datas['employee_id'] = $employee_id = $this->input->post('employee_id');
        $datas['month'] = $month = $this->input->post('month');

        $this->session->set_userdata($datas);

        $BRANCHid = $this->session->userdata("BRANCHid");

        if ($employee_id == 'All') {

            $employeequery = $this->db
                ->join('tbl_designation', 'tbl_designation.Designation_SlNo=tbl_employee.Designation_ID', 'left')
                ->where('tbl_employee.branch_id', $BRANCHid)
                ->get('tbl_employee')->result();
            $data['employee_list'] = $employeequery;
        } else {


            $employeequery = $this->db
                ->join('tbl_designation', 'tbl_designation.Designation_SlNo=tbl_employee.Designation_ID', 'left')
                ->where('tbl_employee.branch_id', $BRANCHid)
                ->where('tbl_employee.Employee_SlNo	', $employee_id)
                ->get('tbl_employee')->result();
            $data['employee_list'] = $employeequery;
        }

        $data['month'] = $month;
        $this->load->view('Administrator/employee/employee_salary_report_list', $data);
    }

    public function EmploeePaymentReportPrint()
    {
        $BRANCHid = $this->session->userdata("BRANCHid");

        $employee_id = $this->session->userdata('employee_id');
        $month = $this->session->userdata('month');

        if ($employee_id == 'All') {

            $employeequery = $this->db
                ->join('tbl_designation', 'tbl_designation.Designation_SlNo=tbl_employee.Designation_ID', 'left')
                ->where('tbl_employee.branch_id', $BRANCHid)
                ->get('tbl_employee')->result();
            $data['employee_list'] = $employeequery;
        } else {

            $employeequery = $this->db
                ->join('tbl_designation', 'tbl_designation.Designation_SlNo=tbl_employee.Designation_ID', 'left')
                ->where('tbl_employee.branch_id', $BRANCHid)
                ->where('tbl_employee.Employee_SlNo	', $employee_id)
                ->get('tbl_employee')->result();
            $data['employee_list'] = $employeequery;
        }

        $data['month'] = $month;
        $this->load->view('Administrator/employee/employee_salary_report_print', $data);
    }

    public function edit_employee_salary($id)
    {
        $data['title'] = "Edit Employee Salary";
        $BRANCHid = $this->session->userdata("BRANCHid");
        $query = $this->db->query("SELECT tbl_employee.*,tbl_employee_payment.*,tbl_month.*,tbl_designation.* FROM tbl_employee left join tbl_employee_payment on tbl_employee_payment.Employee_SlNo=tbl_employee.Employee_SlNo left join tbl_month on tbl_employee_payment.month_id=tbl_month.month_id left join tbl_designation on tbl_designation.Designation_SlNo=tbl_employee.Designation_ID where tbl_employee_payment.employee_payment_id='$id' AND tbl_employee_payment.branch_id='$BRANCHid'");
        $data['selected'] = $query->row();
        //echo "<pre>";print_r($data['selected']);exit;
        $data['content'] = $this->load->view('Administrator/employee/edit_employee_salary', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function updateEmployeePayment()
    {
        $res = ['success' => false, 'message' => 'Nothing happened'];
        try {
            $paymentObj = json_decode($this->input->raw_input_stream);
            $payment = (array)$paymentObj;
            unset($payment['employee_payment_id']);
            $payment['UpdateBy'] = $this->session->userdata('userId');
            $payment['UpdateTime'] = Date('Y-m-d H:i:s');
            $payment['last_update_ip'] = get_client_ip();

            $this->db->where('employee_payment_id', $paymentObj->employee_payment_id)->update('tbl_employee_payment', $payment);
            $res = ['success' => true, 'message' => 'Employee payment updated'];
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }

        echo json_encode($res);
    }

    public function deleteEmployeePayment()
    {
        $res = ['success' => false, 'message' => 'Nothing happened'];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $rules = array(
                'status'         => 'd',
                "DeletedBy"      => $this->session->userdata("userId"),
                "DeletedTime"    => date("Y-m-d H:i:s"),
                "last_update_ip" => get_client_ip()
            );
            $this->db->set($rules)->where('employee_payment_id', $data->paymentId)->update('tbl_employee_payment');
            $res = ['success' => true, 'message' => 'Employee payment deleted'];
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }

        echo json_encode($res);
    }


    //salary Payment
    public function employeePayment()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Employee Salary Payment";
        $data['content'] = $this->load->view('Administrator/employee/salary/payment', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function checkPaymentMonth()
    {
        $data = json_decode($this->input->raw_input_stream);
        $monthId = $data->month_id;

        $query = $this->db->query("SELECT month_id FROM tbl_employee_payment WHERE month_id = ? and branch_id = ? and status = 'a'", [$monthId, $this->session->userdata("BRANCHid")]);
        if ($query->num_rows() > 0) {
            echo json_encode(['success' => true]);
            exit();
        }
        echo json_encode(['success' => false]);
        exit();
    }

    public function getPayments()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";

        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and ep.payment_date between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->user_id) && $data->user_id != '') {
            $clauses .= " and ep.AddBy = '$data->user_id'";
        }

        if (isset($data->month_id) && $data->month_id != '') {
            $clauses .= " and ep.month_id = '$data->month_id'";
        }

        $payments = $this->db->query("
            SELECT ep.*,
            m.month_name,
            u.User_Name

            from tbl_employee_payment ep
            join tbl_month m on m.month_id = ep.month_id
            left join tbl_user u on u.User_SlNo = ep.AddBy

            where ep.status = 'a'
            and ep.branch_id = ?
            $clauses
            order by ep.id desc
        ", $this->session->userdata("BRANCHid"))->result();

        if (isset($data->details)) {
            foreach ($payments as $payment) {
                $payment->details = $this->db->query("
                    SELECT pd.*,
                    e.Employee_ID,
                    e.Employee_Name,
                    d.Department_Name,
                    ba.account_name,
                    ba.account_number,
                    ba.bank_name,

                    (
                        select ifnull(sum(sa.advance), 0) from tbl_salary_advance sa
                        where sa.status = 'a'
                        and sa.employee_id = e.Employee_SlNo
                        and sa.branch_id = " . $this->session->userdata('BRANCHid') . "
                        " . ($data->month_id == null ? "" : " and sa.month_id  = '$data->month_id'") . "
                    ) as advance,

                    (
                        select ifnull(sum(cs.conveyance), 0) from tbl_conveyance_salary cs
                        where cs.transaction_type = 'receive'
                        and cs.employee_id = e.Employee_SlNo
                        and cs.status = 'a'
                        and cs.branch_id= " . $this->session->userdata('BRANCHid') . "
                    " . ($data->month_id == null ? "" : " and cs.month_id  = '$data->month_id'") . "
                    ) as conveyance,

                    de.Designation_Name

                    from tbl_employee_payment_details pd
                    join tbl_employee e on e.Employee_SlNo = pd.employee_id
                    left join tbl_department d on d.Department_SlNo = e.Department_ID
                    left join tbl_designation de on de.Designation_SlNo = e.Designation_ID
                    left join tbl_bank_accounts ba on ba.account_id = pd.account_id

                    where pd.status = 'a'
                    and pd.payment_id = '$payment->id'
                ")->result();
            }
        }

        echo json_encode($payments);
    }

    public function getSalaryDetails()
    {
        $data = json_decode($this->input->raw_input_stream);
        $clauses = "";

        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and ep.payment_date between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->month_id) && $data->month_id != '') {
            $clauses .= " and ep.month_id = '$data->month_id'";
        }

        if (isset($data->employee_id) && $data->employee_id != '') {
            $clauses .= " and pd.employee_id = '$data->employee_id'";
        }

        $payments = $this->db->query(
            "SELECT pd.*,
            e.Employee_ID,
            e.Employee_Name,
            d.Department_Name,
            de.Designation_Name,
            m.month_name,
            ep.payment_date

            from tbl_employee_payment_details pd
            join tbl_employee_payment ep on ep.id = pd.payment_id
            join tbl_month m on m.month_id = ep.month_id
            join tbl_employee e on e.Employee_SlNo = pd.employee_id
            left join tbl_designation de on de.Designation_SlNo = e.Designation_ID
            left join tbl_department d on d.Department_SlNo = e.Department_ID
            
            where pd.status = 'a'
            and pd.branch_id = ?
            $clauses
        ",
            $this->session->userdata("BRANCHid")
        )->result();

        echo json_encode($payments);
    }

    public function saveSalaryPayment()
    {
        $res = ["success" => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $paymentObj = $data->payment;
            $employees = $data->employees;

            $payment = (array)$paymentObj;
            unset($payment['id']);
            $payment['AddBy'] = $this->session->userdata("userId");
            $payment['AddTime'] = date("Y-m-d H:i:s");
            $payment['branch_id'] = $this->session->userdata("BRANCHid");
            $payment['status'] = 'a';

            $this->db->insert('tbl_employee_payment', $payment);
            $payment_id = $this->db->insert_id();

            $total_payment_amount = 0;

            foreach ($employees as $emp) {

                // payment with bank----------------------------------------
                if ($emp->payment_type == 'bank' && $emp->selectBank->account_id != 0) {
                    $bankTransaction = array(
                        'transaction_date' => $paymentObj->payment_date ?? date('Y-m-d'),
                        'account_id'       => $emp->selectBank->account_id,
                        'employee_id'      => $emp->Employee_SlNo ?? 0,
                        'transaction_type' => 'withdraw',
                        'amount'           => $emp->payment,
                        'note'             => $emp->comment ?? 'Employee Salary Payment',
                        'status'           => 1,
                        'form_type'        => 'employee_payment',
                        'AddBy'            => $this->session->userdata('userId'),
                        'AddTime'          => date('Y-m-d H:i:s'),
                        'payment_id'       => $payment_id,
                        'branch_id'        => $this->session->userdata('BRANCHid')
                    );
                    $this->db->insert('tbl_bank_transactions', $bankTransaction);
                }

                $employee = [
                    'payment_id'     => $payment_id,
                    'employee_id'    => $emp->Employee_SlNo,
                    'salary'         => $emp->salary,
                    'benefit'        => $emp->benefit,
                    'deduction'      => $emp->deduction,
                    'net_payable'    => $emp->net_payable,
                    'payment'        => $emp->payment,
                    'payment_type'   => $emp->payment_type,
                    'account_id'     => $emp->payment_type == 'bank' ? $emp->selectBank->account_id : null,
                    'comment'        => $emp->comment,
                    'AddBy'          => $this->session->userdata("userId"),
                    'AddTime'        => date("Y-m-d H:i:s"),
                    'last_update_ip' => get_client_ip(),
                    'branch_id'      => $this->session->userdata("BRANCHid"),
                    'status'         => 'a',
                ];
                $this->db->insert('tbl_employee_payment_details', $employee);
                $total_payment_amount += $emp->payment;
            }

            $this->db->where('id', $payment_id)->update('tbl_employee_payment', ['total_payment_amount' => $total_payment_amount]);

            $res = ["success" => true, 'message' => 'Payment Success'];

            echo json_encode($res);
        } catch (\Exception $e) {
            $res = ["success" => false, 'message' => $e->getMessage()];
        }
    }

    public function updateSalaryPayment()
    {
        $res = ["success" => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $paymentObj = $data->payment;
            $employees = $data->employees;
            $payment_id = $paymentObj->id;

            $payment = (array)$paymentObj;
            unset($payment['id']);
            $payment['UpdateBy'] = $this->session->userdata("userId");
            $payment['UpdateTime'] = date("Y-m-d H:i:s");

            $this->db->where('id', $payment_id);
            $this->db->update('tbl_employee_payment', $payment);

            $total_payment_amount = 0;

            foreach ($employees as $emp) {
                // bank or cash transaction
                if ($emp->payment_type == "bank" && $emp->selectBank->account_id != '0') {
                    $query = $this->db->query("select * from tbl_bank_transactions where payment_id = ? AND account_id = ? and employee_id = ? ", [$payment_id, $emp->selectBank->account_id,  $emp->employee_id]);

                    if ($query->num_rows() > 0) {
                        $bank = $query->row();
                        $dataBank = array(
                            'transaction_date' => $paymentObj->payment_date,
                            'amount' => $emp->payment,
                            'note' => $emp->comment,
                            'status' => 1,
                        );
                        $this->db->where('transaction_id', $bank->transaction_id)->update('tbl_bank_transactions', $dataBank);
                    } else {
                        $tcount = $this->db->query("select transaction_id from tbl_bank_transactions where payment_id = ? and employee_id = ?", [$payment_id, $emp->employee_id ]);

                        if ($tcount->num_rows() > 0) {
                            $oldTrans = $tcount->row();
                            $this->db->query("DELETE FROM tbl_bank_transactions WHERE transaction_id = ?", $oldTrans->transaction_id);
                        }

                        $bankTransaction = array(
                            'transaction_date' => $paymentObj->payment_date ?? date('Y-m-d'),
                            'account_id'       => $emp->selectBank->account_id,
                            'employee_id'      => $emp->Employee_SlNo ?? 0,
                            'transaction_type' => 'withdraw',
                            'amount'           => $emp->payment,
                            'note'             => $emp->comment ?? 'Employee Salary Payment',
                            'status'           => 1,
                            'form_type'        => 'employee_payment',
                            'AddBy'            => $this->session->userdata('userId'),
                            'AddTime'          => date('Y-m-d H:i:s'),
                            'payment_id'       => $payment_id,
                            'branch_id'        => $this->session->userdata('BRANCHid')
                        );
                        $this->db->insert('tbl_bank_transactions', $bankTransaction);
                    }
                } else {
                    $count = $this->db->query("select * from tbl_bank_transactions where payment_id = ? and employee_id = ?", [ $payment_id, $emp->employee_id ]);
                    if ($count->num_rows() > 0) {
                        $this->db->query("update tbl_bank_transactions set status = 0 where payment_id = and employee_id = ?", [ $payment_id, $emp->employee_id ]);
                    }
                }
                // end bank or cash transaction

                $employee = [
                    'payment_id'  => $payment_id,
                    'employee_id' => $emp->employee_id,
                    'salary'      => $emp->salary,
                    'benefit'     => $emp->benefit,
                    'deduction'   => $emp->deduction,
                    'net_payable' => $emp->net_payable,
                    'payment'     => $emp->payment,
                    'payment_type'    => $emp->payment_type,
                    'account_id'      => $emp->payment_type == 'bank' ? $emp->selectBank->account_id : null,
                    'comment'     => $emp->comment,
                    'UpdateBy'    => $this->session->userdata("userId"),
                    'UpdateTime'  => date("Y-m-d H:i:s"),
                ];

                $this->db->where('id', $emp->id);
                $this->db->update('tbl_employee_payment_details', $employee);

                $total_payment_amount += $emp->payment;
            }

            $this->db->where('id', $payment_id)->update('tbl_employee_payment', ['total_payment_amount' => $total_payment_amount]);

            $res = ["success" => true, 'message' => 'Update Success'];

            echo json_encode($res);
        } catch (\Exception $e) {
            $res = ["success" => false, 'message' => $e->getMessage()];
        }
    }

    public function employeePaymentReport()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Employee Salary Payment Report";
        $data['content'] = $this->load->view('Administrator/employee/salary/payment_report', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function deletePayment()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $details = $this->db->query(
                "SELECT pd.id from tbl_employee_payment_details pd
                    where pd.payment_id = '$data->paymentId'
                "
            )->result();

            $rules = array(
                'status'         => 'd',
                "DeletedBy"      => $this->session->userdata("userId"),
                "DeletedTime"    => date("Y-m-d H:i:s"),
                "last_update_ip" => get_client_ip()
            );
            foreach ($details as $detail) {
                $this->db->set($rules)->where('id', $detail->id)->update('tbl_employee_payment_details');
            }

            $this->db->set($rules)->where('id', $data->paymentId)->update('tbl_employee_payment');
            $res = ['success' => true, 'message' => 'Salary Payment deleted'];
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
        echo json_encode($res);
    }

    // advance salary------------------------------
    public function advanceSalary() {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Employee Advance Salary";
        $data['content'] = $this->load->view('Administrator/employee/advance_salary', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getSalaryAdvance(){
        $data = json_decode($this->input->raw_input_stream);
        $statusClause = " and sa.status = 'a'";
        $advance = $this->db->query("
            SELECT
                sa.*,
                m.month_name,
                e.Employee_Name,
                e.Employee_ID
            from tbl_salary_advance sa
            left join tbl_employee e on e.Employee_SlNo = sa.employee_id 
            left join tbl_month m on m.month_id = sa.month_id
            where sa.branch_id = ?
            $statusClause
        ", $this->session->userdata('BRANCHid'))->result();
        echo json_encode($advance);
    }

    public function addAdvanceSalary(){
        $res = ["success" => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $paymentObj = $data;
            
            $salaryAdvance = (array)$paymentObj;
          
            unset($salaryAdvance['advanceSalaryId']);
            $salaryAdvance['AddBy'] = $this->session->userdata("userId");
            $salaryAdvance['AddTime'] = date("Y-m-d H:i:s");
            $salaryAdvance['branch_id'] = $this->session->userdata("BRANCHid");
            $salaryAdvance['status'] = 'a';
           
            $this->db->insert('tbl_salary_advance', $salaryAdvance);

            $res = ["success" => true, 'message' => 'Salary Advance Payment Success'];

            echo json_encode($res);
        } catch (\Exception $e) {
            $res = ["success" => false, 'message' => $e->getMessage()];
        }
    }

    public function updateAdvanceSalary(){
        $res = ["success" => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $paymentObj = $data;
            
            $salaryAdvance = (array)$paymentObj;
            
            $advance_id = $salaryAdvance['advanceSalaryId'];
            unset($salaryAdvance['advanceSalaryId']);
            $salaryAdvance['UpdateBy'] = $this->session->userdata("userId");
            $salaryAdvance['UpdateTime'] = date("Y-m-d H:i:s");
            $salaryAdvance['last_update_ip'] = get_client_ip();
            $this->db->where('id', $advance_id);
            $this->db->update('tbl_salary_advance', $salaryAdvance);
            $res = ["success" => true, 'message' => 'Salary Advance Payment Updated Success'];

            echo json_encode($res);
        } catch (\Exception $e) {
            $res = ["success" => false, 'message' => $e->getMessage()];
        }
    }

    public function deleteAdvanceSalary(){
        $res = ["success" => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $salaryAdvance = [
                'status'      => 'd',
                'DeletedBy'   => $this->session->userdata("userId"),
                'DeletedTime' => date("Y-m-d H:i:s"),
                'last_update_ip' => get_client_ip()
            ];
           
            $this->db->where('id', $data->advanceId)->update('tbl_salary_advance', $salaryAdvance);
            $res = ["success" => true, 'message' => 'Salary Advance Payment Deleted Success'];
            echo json_encode($res);
        } catch (\Exception $e) {
            $res = ["success" => false, 'message' => $e->getMessage()];
        }
    }

    // conveyance salary ------------------------------------------------------
    public function conveyanceSalary() {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Employee Conveyance Salary";
        $data['content'] = $this->load->view('Administrator/employee/conveyance_salary', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getConveyanceSalary(){
        $data = json_decode($this->input->raw_input_stream);
        $statusClause = " and cv.status = 'a'";
        $conveyance = $this->db->query("
            SELECT
                cv.*,
                m.month_name,
                e.Employee_Name,
                e.Employee_ID,
                ac.account_name,
                ac.branch_name,
                ac.bank_name,
                ac.account_number
            from tbl_conveyance_salary cv
            left join tbl_employee e on e.Employee_SlNo = cv.employee_id 
            left join tbl_month m on m.month_id = cv.month_id
            left join tbl_bank_accounts ac on ac.account_id = cv.account_id 
            where cv.branch_id = ?
            $statusClause
        ", $this->session->userdata('BRANCHid'))->result();
        echo json_encode($conveyance);
    }

    public function saveConveyanceSalary(){
        $res = ["success" => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $paymentObj = $data;
            
            $salaryConveyance = (array)$paymentObj;
          
            unset($salaryConveyance['conveyanceSalaryId']);
            $salaryConveyance['AddBy'] = $this->session->userdata("userId");
            $salaryConveyance['AddTime'] = date("Y-m-d H:i:s");
            $salaryConveyance['transaction_date'] = date("Y-m-d");
            $salaryConveyance['branch_id'] = $this->session->userdata("BRANCHid");
            $salaryConveyance['status'] = 'a';
           
            $this->db->insert('tbl_conveyance_salary', $salaryConveyance);

            $res = ["success" => true, 'message' => 'Salary Conveyance Payment Success'];

            echo json_encode($res);
        } catch (\Exception $e) {
            $res = ["success" => false, 'message' => $e->getMessage()];
        }
    }

    public function updateConveyanceSalary(){
        $res = ["success" => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $paymentObj = $data;
            
            $salaryConveyance = (array)$paymentObj;
            
            $conveyance_id = $salaryConveyance['conveyanceSalaryId'];
            unset($salaryConveyance['conveyanceSalaryId']);
            $salaryConveyance['UpdateBy'] = $this->session->userdata("userId");
            $salaryConveyance['UpdateTime'] = date("Y-m-d H:i:s");
            $salaryConveyance['transaction_date'] = date("Y-m-d");
            $salaryConveyance['last_update_ip'] = get_client_ip();
            $this->db->where('id', $conveyance_id);
            $this->db->update('tbl_conveyance_salary', $salaryConveyance);
            $res = ["success" => true, 'message' => 'Salary Conveyance Updated Success'];

            echo json_encode($res);
        } catch (\Exception $e) {
            $res = ["success" => false, 'message' => $e->getMessage()];
        }
    }

    public function deleteConveyanceSalary(){
        $res = ["success" => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $salaryConveyance = [
                'status'      => 'd',
                'DeletedBy'   => $this->session->userdata("userId"),
                'DeletedTime' => date("Y-m-d H:i:s"),
                'last_update_ip' => get_client_ip()
            ];           
            $this->db->where('id', $data->conveyanceId)->update('tbl_conveyance_salary', $salaryConveyance);
            $res = ["success" => true, 'message' => 'Salary Conveyance Deleted Success'];
            echo json_encode($res);
        } catch (\Exception $e) {
            $res = ["success" => false, 'message' => $e->getMessage()];
        }
    }

    public function employeeConveyanceLedger(){
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Employee Salary Conveyance Ledger";
        $data['content'] = $this->load->view('Administrator/employee/salary/salary_conveyance_ledger', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getConveyanceDetails()
    {
        $data = json_decode($this->input->raw_input_stream);
        $clauses = "";

        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and ep.payment_date between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->month_id) && $data->month_id != '') {
            $clauses .= " and cs.month_id = '$data->month_id'";
        }

        if (isset($data->employee_id) && $data->employee_id != '') {
            $clauses .= " and cs.employee_id = '$data->employee_id'";
        }

        $payments = $this->db->query(
            " SELECT cs.*,
            e.Employee_ID,
            e.Employee_Name,
            d.Department_Name,
            de.Designation_Name,
            m.month_name

            from tbl_conveyance_salary cs
            left join tbl_month m on m.month_id = cs.month_id
            left join tbl_employee e on e.Employee_SlNo = cs.employee_id
            left join tbl_designation de on de.Designation_SlNo = e.Designation_ID
            left join tbl_department d on d.Department_SlNo = e.Department_ID
            where cs.status = 'a'
            and cs.branch_id = ?
            $clauses
        ",
            $this->session->userdata("BRANCHid")
        )->result();

        echo json_encode($payments);
    }



}
