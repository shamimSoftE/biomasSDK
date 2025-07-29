<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Assets extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->brunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        $this->accountType = $this->session->userdata('accountType');
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
        $data['title'] = "Assets Entry";
        $data['assets'] = $this->Other_model->get_all_asset_info();
        $data['asset_names'] = $this->db->query("
            SELECT a.*
            from tbl_asset_name a
            where a.status = 'a'
            and a.branch_id = '$this->brunch'
            order by a.id desc")->result();
        $data['content'] = $this->load->view('Administrator/assets/assets_entry', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }


    public function insert_Assets()
    {
        $data = array(
            "as_date"        => date('Y-m-d'),
            "asset_name_id"  => $this->input->post('asset_name_id'),
            "as_sp_name"     => $this->input->post('supplier_name'),
            "as_qty"         => $this->input->post('qty'),
            "as_rate"        => $this->input->post('rate'),
            "as_amount"      => $this->input->post('amount'),
            "unit_valuation" => $this->input->post('unit_valuation') ?? 0.00,
            "valuation"      => $this->input->post('valuation') ?? 0.00,
            "buy_or_sale"    => $this->input->post('buy_or_sale'),
            "as_note"        => $this->input->post('note'),
            "status"         => 'a',
            "AddBy"          => $this->session->userdata("userId"),
            "AddTime"        => date("Y-m-d H:i:s"),
            "last_update_ip" => get_client_ip(),
            "branch_id"      => $this->session->userdata('BRANCHid'),
        );
        // echo json_encode($data);
        // exit;

        $this->mt->save_data('tbl_assets', $data);
        echo json_encode(TRUE);
    }


    public function Assets_edit($id = null)
    {
        $data['edit'] = $this->db->where('as_id', $id)->get('tbl_assets')->row();
        $data['asset_names'] = $this->db->query("
            SELECT a.*
            from tbl_asset_name a
            where a.status = 'a'
            and a.branch_id = '$this->brunch'
            order by a.id desc")->result();
        $this->load->view('Administrator/assets/edit_assets', $data);
    }

    public function Update_Assets($id = null)
    {
        $data = array(
            "asset_name_id"  => $this->input->post('asset_name_id'),
            "as_sp_name"     => $this->input->post('supplier_name'),
            "as_qty"         => $this->input->post('qty'),
            "as_rate"        => $this->input->post('rate'),
            "unit_valuation" => $this->input->post('unit_valuation') ?? 0.00,
            "valuation"      => $this->input->post('valuation') ?? 0.00,
            "as_amount"      => $this->input->post('amount'),
            "as_note"        => $this->input->post('note'),
            "UpdateBy"       => $this->session->userdata("userId"),
            "UpdateTime"     => date("Y-m-d H:i:s"),
            "last_update_ip" => get_client_ip(),
        );
        $up = $this->db->where('as_id', $id)->update('tbl_assets', $data);
        if ($up) :
            echo json_encode(TRUE);
        else : return false;
        endif;
    }


    public function Assets_delete($id = null)
    {
        $data = array('status' => 'd', 'DeletedBy' => $this->session->userdata('userId'), 'DeletedTime' => date("Y-m-d H:i:s"));
        $up = $this->db->where('as_id', $id)->update('tbl_assets', $data);
        if ($up) :
            echo json_encode(TRUE);
        else : return false;
        endif;
    }


    public function getAssetsCost()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (
            isset($data->dateFrom) && $data->dateFrom != ''
            && isset($data->dateTo) && $data->dateTo != ''
        ) {
            $clauses .= " and ass.as_date between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->buy_or_sale) && $data->buy_or_sale != '') {
            $clauses .= " and ass.buy_or_sale = '$data->buy_or_sale'";
        }

        $assets = $this->db->query("
            select ass.* from tbl_assets ass
                where ass.status = 'a'
                and ass.branch_id= " . $this->session->userdata('BRANCHid') . "
                $clauses
        ")->result();

        $cost = array_reduce($assets, function ($prev, $curr) {
            return $prev + $curr->as_amount;
        });

        $res = [
            'cost' => $cost,
            'assets' => $assets
        ];

        echo json_encode($res);
    }

    public function assetsReport()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Assets Report";
        $data['content'] = $this->load->view('Administrator/assets/assets_report', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getGroupAssets()
    {
        $assets = $this->db->query("
            SELECT as_name as group_name
            from tbl_assets
            where status = 'a'
            and branch_id = '$this->brunch'
            group by as_name
        ")->result();

        echo json_encode($assets);
    }

    public function getAssetsReport()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = '';

        if (isset($data->asset) && $data->asset != '') {
            $clauses .= " and a.as_name = '$data->asset'";
        }

        $assets = $this->mt->assetsReport($clauses);

        echo json_encode($assets);
    }

    // asset name entry------------------------------
    public function assetNameEntry(){
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Asset Name Entry";
        $data['content'] = $this->load->view('Administrator/assets/add_asset_name', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getAssetName()
    {
        $assets = $this->db->query("
            SELECT a.*
            from tbl_asset_name a
            where a.status = 'a'
            and a.branch_id = '$this->brunch'
            order by a.id desc
        ")->result();
        echo json_encode($assets);
    }

    public function addAssetName() {
        $res = ['status' => false];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $query = $this->db->query("select * from tbl_asset_name where name = '$data->name'")->row();
            if (!empty($query)) {
                $area = array(
                    'status'     => 'a',
                    "UpdateBy"   => $this->session->userdata("userId"),
                    "UpdateTime" => date("Y-m-d H:i:s"),
                    "last_update_ip" => get_client_ip()
                );
                $this->db->where('id', $query->id);
                $this->db->update('tbl_asset_name', $area);
            } else {
                $area = array(
                    'status'         => 'a',
                    "name"           => $data->name,
                    "AddBy"          => $this->session->userdata("userId"),
                    "AddTime"        => date("Y-m-d H:i:s"),
                    "last_update_ip" => get_client_ip(),
                    "branch_id"      => $this->brunch
                );
                $this->db->insert('tbl_asset_name', $area);
            }

            $res = ['status' => true, 'message' => 'Asset name added successfully'];
        } catch (\Throwable $th) {
            $res = ['status' => false, 'message' => $th->getMessage()];
        }
        echo json_encode($res);
    }

    public function updateAssetName()
    {
        $res = ['status' => false];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $area = array(
                "name"           => $data->name,
                "UpdateBy"       => $this->session->userdata("userId"),
                "UpdateTime"     => date("Y-m-d H:i:s"),
                "last_update_ip" => get_client_ip()
            );
            $this->db->where('id', $data->id);
            $this->db->update('tbl_asset_name', $area);

            $res = ['status' => true, 'message' => 'Asset name update successfully'];
        } catch (\Throwable $th) {
            $res = ['status' => false, 'message' => $th->getMessage()];
        }
        echo json_encode($res);
    }

    public function deleteAssetName()
    {
        $data = json_decode($this->input->raw_input_stream);
        $asset = array(
            'status'         => 'd',
            "DeletedBy"      => $this->session->userdata("userId"),
            "DeletedTime"    => date("Y-m-d H:i:s"),
            "last_update_ip" => get_client_ip()
        );
        $this->db->where('id', $data->assetId);
        $this->db->update('tbl_asset_name', $asset);
        echo json_encode(['status' => true, 'message' => 'Asset name successfully']);
    }
}
