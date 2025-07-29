<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Account extends CI_Controller
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
        $data['title'] = "Add Account";
        $data['accountCode'] = $this->mt->generateAccountCode();
        $data['content'] = $this->load->view('Administrator/account/add_account', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function subAccount()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Add Sub Account";
        $data['subaccountCode'] = $this->mt->generateSubAccountCode();
        $data['content'] = $this->load->view('Administrator/account/add_sub_account', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function addAccount()
    {
        $res = ['success' => false, 'message' => 'Nothing'];
        try {
            $accountObj = json_decode($this->input->raw_input_stream);

            $accountObj->Acc_Code = isset($accountObj->Acc_Code) ? $accountObj->Acc_Code : $this->mt->generateAccountCode();

            $duplicateCodeCount = $this->db->query("select * from tbl_account where Acc_Code = ?", $accountObj->Acc_Code)->num_rows();
            if ($duplicateCodeCount != 0) {
                $accountObj = $this->mt->generateAccountCode();
            }

            $duplicateNameCount = $this->db->query("select * from tbl_account where Acc_Name = ? and branch_id = ?", [$accountObj->Acc_Name, $this->brunch])->num_rows();
            if ($duplicateNameCount != 0) {
                $this->db->query("update tbl_account set status = 'a' where Acc_Name = ? and branch_id = ?", [$accountObj->Acc_Name, $this->brunch]);
                $res = ['success' => true, 'message' => 'Account activated', 'newAccountCode' => $this->mt->generateAccountCode()];
                echo json_encode($res);
                exit;
            }

            $account = (array)$accountObj;
            if (isset($account['Acc_SlNo'])) {
                unset($account['Acc_SlNo']);
            }
            $account['status']         = 'a';
            $account['AddBy']          = $this->session->userdata("userId");
            $account['AddTime']        = date('Y-m-d H:i:s');
            $account['last_update_ip'] = get_client_ip();
            $account['branch_id']      = $this->brunch;

            $this->db->insert('tbl_account', $account);

            $res = ['success' => true, 'message' => 'Account added', 'newAccountCode' => $this->mt->generateAccountCode()];
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }

        echo json_encode($res);
    }

    public function addSubAccount()
    {
        $res = ['success' => false, 'message' => 'Nothing'];
        try {
            $accountObj = json_decode($this->input->raw_input_stream);

            $accountObj->Sub_Acc_Code = isset($accountObj->Sub_Acc_Code) ? $accountObj->Sub_Acc_Code : $this->mt->generateSubAccountCode();

            $duplicateCodeCount = $this->db->query("select * from tbl_sub_account where Sub_Acc_Code = ?", $accountObj->Sub_Acc_Code)->num_rows();
            if ($duplicateCodeCount != 0) {
                $accountObj = $this->mt->generateSubAccountCode();
            }

            $duplicateNameCount = $this->db->query("select * from tbl_sub_account where Sub_Acc_Name = ? and branch_id = ?", [$accountObj->Sub_Acc_Name, $this->brunch])->num_rows();
            if ($duplicateNameCount != 0) {
                $this->db->query("update tbl_sub_account set status = 'a' where Sub_Acc_Name = ? and branch_id = ?", [$accountObj->Sub_Acc_Name, $this->brunch]);
                $res = ['success' => true, 'message' => 'Account activated', 'newSubAccountCode' => $this->mt->generateSubAccountCode()];
                echo json_encode($res);
                exit;
            }

            $account = (array)$accountObj;
            if (isset($account['id'])) {
                unset($account['id']);
            }
            $account['status']         = 'a';
            $account['AddBy']          = $this->session->userdata("userId");
            $account['AddTime']        = date('Y-m-d H:i:s');
            $account['last_update_ip'] = get_client_ip();
            $account['branch_id']      = $this->brunch;

            $this->db->insert('tbl_sub_account', $account);

            $res = ['success' => true, 'message' => 'Sub Account added', 'newSubAccountCode' => $this->mt->generateSubAccountCode()];
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
        echo json_encode($res);
    }

    public function updateAccount()
    {
        $res = ['success' => false, 'message' => 'Nothing'];
        try {
            $accountObj = json_decode($this->input->raw_input_stream);

            $duplicateNameCount = $this->db->query("select * from tbl_account where Acc_Name = ? and branch_id = ? and Acc_SlNo != ?", [$accountObj->Acc_Name, $this->brunch, $accountObj->Acc_SlNo])->num_rows();
            if ($duplicateNameCount != 0) {
                $this->db->query("update tbl_account set status = 'a' where Acc_Name = ? and branch_id = ?", [$accountObj->Acc_Name, $this->brunch]);
                $res = ['success' => true, 'message' => 'Account activated', 'newAccountCode' => $this->mt->generateAccountCode()];
                echo json_encode($res);
                exit;
            }

            $account = (array)$accountObj;
            unset($account['Acc_SlNo']);
            $account['UpdateBy']       = $this->session->userdata("userId");
            $account['UpdateTime']     = date('Y-m-d H:i:s');
            $account['last_update_ip'] = get_client_ip();

            $this->db->where('Acc_SlNo', $accountObj->Acc_SlNo)->update('tbl_account', $account);

            $res = ['success' => true, 'message' => 'Account updated', 'newAccountCode' => $this->mt->generateAccountCode()];
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
        echo json_encode($res);
    }

    public function updateSubAccount()
    {
        $res = ['success' => false, 'message' => 'Nothing'];
        try {
            $accountObj = json_decode($this->input->raw_input_stream);

            $duplicateNameCount = $this->db->query("select * from tbl_sub_account where Sub_Acc_Name = ? and branch_id = ? and id != ?", [$accountObj->Sub_Acc_Name, $this->brunch, $accountObj->id])->num_rows();
            if ($duplicateNameCount != 0) {
                $this->db->query("update tbl_sub_account set status = 'a' where Sub_Acc_Name = ? and branch_id = ?", [$accountObj->Sub_Acc_Name, $this->brunch]);
                $res = ['success' => true, 'message' => 'Account activated', 'newSubAccountCode' => $this->mt->generateSubAccountCode()];
                echo json_encode($res);
                exit;
            }

            $account = (array)$accountObj;
            unset($account['id']);
            $account['UpdateBy']       = $this->session->userdata("userId");
            $account['UpdateTime']     = date('Y-m-d H:i:s');
            $account['last_update_ip'] = get_client_ip();

            $this->db->where('id', $accountObj->id)->update('tbl_sub_account', $account);

            $res = ['success' => true, 'message' => 'Sub Account updated', 'newSubAccountCode' => $this->mt->generateSubAccountCode()];
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
        echo json_encode($res);
    }

    public function deleteAccount()
    {
        $res = ['success' => false, 'message' => 'Nothing'];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $account = array(
                'status' => 'd',
                'DeletedBy' => $this->session->userdata("userId"),
                'DeletedTime' => date('Y-m-d H:i:s'),
                'laste_update_ip' => get_client_ip()
            );

            $this->db->query("update tbl_account set status = 'd' where Acc_SlNo = ?", $data->accountId);

            $res = ['success' => true, 'message' => 'Account deleted'];
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }

        echo json_encode($res);
    }
    
    public function deleteSubAccount()
    {
        $res = ['success' => false, 'message' => 'Nothing'];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $account = array(
                'status' => 'd',
                'DeletedBy' => $this->session->userdata("userId"),
                'DeletedTime' => date('Y-m-d H:i:s'),
                'laste_update_ip' => get_client_ip()
            );

            $this->db->query("update tbl_sub_account set status = 'd' where id = ?", $data->subaccountId);

            $res = ['success' => true, 'message' => 'Account deleted'];
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }

        echo json_encode($res);
    }

    public function getAccounts()
    {
        $accounts = $this->db->query("select *, concat_ws(' - ', Acc_Code,Acc_Name) as account_name 
        
        from tbl_account where status = 'a' and branch_id = ? order by Acc_SlNo desc", $this->session->userdata('BRANCHid'))->result();
        echo json_encode($accounts);
    }

    //// Sub Account
    public function getSubAccounts()
    {
        $accounts = $this->db->query("
         select sa.* ,
         concat_ws(' - ', a.Acc_Code,a.Acc_Name) as account_name
         from  tbl_sub_account sa
         join tbl_account a on a.Acc_SlNo = sa.account_id
         where sa.status = 'a' 
         and sa.branch_id = ? 
         order by sa.id desc"
         , $this->session->userdata('BRANCHid'))->result();
        echo json_encode($accounts);
    }

    // Cash Transaction
    public function cash_transaction()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Cash Transaction";
        $data['transactionId'] = 0;
        $data['transaction'] = $this->Billing_model->select_all_transaction();
        $data['accounts'] = $this->Other_model->get_all_account_info();
        $data['content'] = $this->load->view('Administrator/account/cash_transaction', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function cashTransactionEdit($id)
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Cash Transaction";
        $data['transactionId'] = $id;
        $data['transaction'] = $this->Billing_model->select_all_transaction();
        $data['accounts'] = $this->Other_model->get_all_account_info();
        $data['content'] = $this->load->view('Administrator/account/cash_transaction', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }


    public function getCashTransactions()
    {
        $data = json_decode($this->input->raw_input_stream);

        $dateClause = "";
        $status = "a";
        if (isset($data->status) && $data->status != '') {
            $status = $data->status;
        }
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $dateClause = " and ct.Tr_date between '$data->dateFrom' and '$data->dateTo'";
        }

        $transactionTypeClause = "";
        if (isset($data->transactionType) && $data->transactionType != '' && $data->transactionType == 'received') {
            $transactionTypeClause = " and ct.Tr_Type = 'In Cash'";
        }
        if (isset($data->transactionType) && $data->transactionType != '' && $data->transactionType == 'paid') {
            $transactionTypeClause = " and ct.Tr_Type = 'Out Cash'";
        }

        $accountClause = "";

        if (isset($data->accountId) && $data->accountId != '') {
            $accountClause .= " and ct.Acc_SlID = '$data->accountId'";
        }
        if (isset($data->subaccountId) && $data->subaccountId != '') {
            $accountClause .= " and ct.Sub_Acc_id = '$data->subaccountId'";
        }

        $transactions = $this->db->query("
            select 
                ct.*,
                a.Acc_Name,
                b.Sub_Acc_Name,
                ua.User_Name as added_by,
                ba.account_name,
                ba.branch_name,
                ba.account_number,
                ud.User_Name as deleted_by
            from tbl_cashtransaction ct
            join tbl_account a on a.Acc_SlNo = ct.Acc_SlID
            left join tbl_sub_account b on b.id = ct.Sub_Acc_id
            left join tbl_bank_accounts ba on ba.account_id = ct.bank_account_id
            left join tbl_user ua on ua.User_SlNo = ct.AddBy
            left join tbl_user ud on ud.User_SlNo = ct.DeletedBy
            where ct.status = '$status'
            and ct.branch_id = ?
            $dateClause $transactionTypeClause $accountClause
            order by ct.Tr_SlNo desc
        ", $this->session->userdata('BRANCHid'))->result();

        echo json_encode($transactions);
    }

    public function getCashTransactionCode()
    {
        echo json_encode($this->mt->generateCashTransactionCode());
    }

    public function addCashTransaction()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $transactionObj = json_decode($this->input->raw_input_stream);

            $transaction = (array)$transactionObj;
            $transaction['status'] = 'a';
            $transaction['AddBy'] = $this->session->userdata("userId");
            $transaction['AddTime'] = date('Y-m-d H:i:s');
            $transaction['last_update_ip'] = get_client_ip();
            $transaction['branch_id'] = $this->session->userdata('BRANCHid');

            $this->db->insert('tbl_cashtransaction', $transaction);

            $res = ['success' => true, 'message' => 'Transaction added'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateCashTransaction() 
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $transactionObj = json_decode($this->input->raw_input_stream);

            $transaction = (array)$transactionObj;
            unset($transaction['Tr_SlNo']);
            $transaction['UpdateBy'] = $this->session->userdata("userId");
            $transaction['UpdateTime'] = date('Y-m-d H:i:s');
            $transaction['last_update_ip'] = get_client_ip();

            $this->db->where('Tr_SlNo', $transactionObj->Tr_SlNo)->update('tbl_cashtransaction', $transaction);

            $res = ['success' => true, 'message' => 'Transaction updated'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }
    public function deleteCashTransaction()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $transaction = array(
                'status' => 'd',
                'DeletedBy' => $this->session->userdata('userId'),
                'DeletedTime' => date('H-m-d H:i:s'),
                'last_update_ip' => get_client_ip()
            );
            $this->db->set($transaction)->where('Tr_SlNo', $data->transactionId)->update('tbl_cashtransaction');

            $res = ['success' => true, 'message' => 'Transaction deleted'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    function getOtherIncomeExpense()
    {
        $data = json_decode($this->input->raw_input_stream);

        $transactionDateClause = "";
        $employePaymentDateClause = "";
        $profitDistributeDateClause = "";
        $loanInterestDateClause = "";
        $assetsSalesDateClause = "";
        $damageClause = "";
        $returnClause = "";
        $purchaseClause = "";
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $transactionDateClause = " and ct.Tr_date between '$data->dateFrom' and '$data->dateTo'";
            $employePaymentDateClause = " and ep.payment_date between '$data->dateFrom' and '$data->dateTo'";
            $profitDistributeDateClause = " and it.transaction_date between '$data->dateFrom' and '$data->dateTo'";
            $loanInterestDateClause = " and lt.transaction_date between '$data->dateFrom' and '$data->dateTo'";
            $assetsSalesDateClause = " and a.as_date between '$data->dateFrom' and '$data->dateTo'";
            $damageClause = " and d.Damage_Date between '$data->dateFrom' and '$data->dateTo'";
            $returnClause = " and r.SaleReturn_ReturnDate between '$data->dateFrom' and '$data->dateTo'";
            $purchaseClause = " and pm.PurchaseMaster_OrderDate between '$data->dateFrom' and '$data->dateTo'";
        }

        $result = $this->db->query("
            select
            (
                select ifnull(sum(ct.In_Amount), 0)
                from tbl_cashtransaction ct
                where ct.branch_id = '" . $this->session->userdata('BRANCHid') . "'
                and ct.status = 'a'
                $transactionDateClause
            ) as income,
        
            (
                select ifnull(sum(ct.Out_Amount), 0)
                from tbl_cashtransaction ct
                where ct.branch_id = '" . $this->session->userdata('BRANCHid') . "'
                and ct.status = 'a'
                $transactionDateClause
            ) as expense,
        
            (
                select ifnull(sum(ep.total_payment_amount), 0)
                from tbl_employee_payment ep
                where ep.branch_id = '" . $this->session->userdata('BRANCHid') . "'
                and ep.status = 'a'
                $employePaymentDateClause
            ) as employee_payment,

            (
                select ifnull(sum(it.amount), 0)
                from tbl_investment_transactions it
                where it.branch_id = '" . $this->session->userdata('BRANCHid') . "'
                and it.transaction_type = 'Profit'
                and it.status = 1
                $profitDistributeDateClause
            ) as profit_distribute,

            (
                select ifnull(sum(lt.amount), 0)
                from tbl_loan_transactions lt
                where lt.branch_id = '" . $this->session->userdata('BRANCHid') . "'
                and lt.transaction_type = 'Interest'
                and lt.status = 1
                $loanInterestDateClause
            ) as loan_interest,

            (
                select ifnull(sum(a.valuation - a.as_amount), 0)
                from tbl_assets a
                where a.branch_id = '" . $this->session->userdata('BRANCHid') . "'
                and a.buy_or_sale = 'sale'
                and a.status = 'a'
                $assetsSalesDateClause
            ) as assets_sales_profit_loss,

            (
                select ifnull(sum(pm.PurchaseMaster_DiscountAmount), 0) 
                from tbl_purchasemaster pm
                where pm.branch_id = '" . $this->session->userdata('BRANCHid') . "'
                and pm.status = 'a'
                $purchaseClause
            ) as purchase_discount,
            
            (
                select ifnull(sum(pm.PurchaseMaster_Tax), 0) 
                from tbl_purchasemaster pm
                where pm.branch_id = '" . $this->session->userdata('BRANCHid') . "'
                and pm.status = 'a'
                $purchaseClause
            ) as purchase_vat,
            
            (
                select ifnull(sum(pm.PurchaseMaster_Freight), 0) 
                from tbl_purchasemaster pm
                where pm.branch_id = '" . $this->session->userdata('BRANCHid') . "'
                and pm.status = 'a'
                $purchaseClause
            ) as purchase_transport_cost,
            
            (
                select ifnull(sum(dd.damage_amount), 0) 
                from tbl_damagedetails dd
                join tbl_damage d on d.Damage_SlNo = dd.Damage_SlNo
                where d.branch_id = '" . $this->session->userdata('BRANCHid') . "'
                and dd.status = 'a'
                $damageClause
            ) as damaged_amount,

            (
                select ifnull(sum(rd.SaleReturnDetails_ReturnAmount) - sum(sd.Purchase_Rate * rd.SaleReturnDetails_ReturnQuantity), 0)
                from tbl_salereturndetails rd
                join tbl_salereturn r on r.SaleReturn_SlNo = rd.SaleReturn_IdNo
                join tbl_salesmaster sm on sm.SaleMaster_InvoiceNo = r.SaleMaster_InvoiceNo
                join tbl_saledetails sd on sd.Product_IDNo = rd.SaleReturnDetailsProduct_SlNo and sd.SaleMaster_IDNo = sm.SaleMaster_SlNo
                where r.status = 'a'
                and r.branch_id= '" . $this->session->userdata('BRANCHid') . "'
                $returnClause
            ) as returned_amount
        ")->row();

        echo json_encode($result);
    }


    function cash_view()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Cash View";
        $sql = $this->db->query("SELECT 
                                ct.*,
                                tbl_account.* 
                                FROM tbl_cashtransaction ct
                                left join tbl_account on tbl_account.Acc_SlNo = ct.Acc_SlID");
        $data["record"] = $sql->result();
        $data['content'] = $this->load->view('Administrator/account/cashview_search_list', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function bankAccounts()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Bank Accounts";
        $data['content'] = $this->load->view('Administrator/account/bank_accounts', $data, true);
        $this->load->view('Administrator/index', $data);
    }

    public function addBankAccount()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $accountCheck = $this->db->query("
                select
                *
                from tbl_bank_accounts
                where account_number = ?
            ", $data->account_number)->num_rows();

            if ($accountCheck != 0) {
                $res = ['success' => false, 'message' => 'Account number already exists'];
                echo json_encode($res);
                exit;
            }

            $account                   = (array)$data;
            $account['AddBy']          = $this->session->userdata('userId');
            $account['AddTime']        = date('Y-m-d H:i:s');
            $account['status']         = 1;
            $account['last_update_ip'] = get_client_ip();
            $account['branch_id']      = $this->session->userdata('BRANCHid');

            $this->db->insert('tbl_bank_accounts', $account);
            $res = ['success' => true, 'message' => 'Account created successfully'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateBankAccount()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $accountCheck = $this->db->query("
                select
                *
                from tbl_bank_accounts
                where account_number = ?
                and account_id != ?
            ", [$data->account_number, $data->account_id])->num_rows();

            if ($accountCheck != 0) {
                $res = ['success' => false, 'message' => 'Account number already exists'];
                echo json_encode($res);
                exit;
            }

            $account = (array)$data;
            $account['UpdateBy'] = $this->session->userdata('userId');
            $account['UpdateTime'] = date('Y-m-d H:i:s');
            $account['last_update_ip'] = get_client_ip();

            $this->db->where('account_id', $data->account_id);
            $this->db->update('tbl_bank_accounts', $account);
            $res = ['success' => true, 'message' => 'Account updated successfully'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function getBankAccounts()
    {
        $accounts = $this->db->query("
            select 
            *,
            concat_ws(' - ', account_name, account_number, bank_name) as display_name,
            case status 
            when 1 then 'Active'
            else 'Inactive'
            end as status_text
            from tbl_bank_accounts 
            where branch_id = ?
        ", $this->session->userdata('BRANCHid'))->result();
        echo json_encode($accounts);
    }

    public function changeAccountstatus()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $rules = array(
                'status'         => $data->account->status == 1 ? 0 : 1,
                'UpdateBy'       => $this->session->userdata('userId'),
                'UpdateTime'     => date('H-m-d H:i:s'),
                'last_update_ip' => get_client_ip()
            );
            $this->db->set($rules)->where('account_id', $data->account->account_id)->update('tbl_bank_accounts');

            $res = ['success' => true, 'message' => 'status Changed'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function bankTransactions()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Bank Transactions";
        $data['transactionId'] = 0;
        $data['content'] = $this->load->view('Administrator/account/bank_transactions', $data, true);
        $this->load->view('Administrator/index', $data);
    }

    public function bankTransactionEdit($id)
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Bank Transactions";
        $data['transactionId'] = $id;
        $data['content'] = $this->load->view('Administrator/account/bank_transactions', $data, true);
        $this->load->view('Administrator/index', $data);
    }

    public function addBankTransaction()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $transaction = (array)$data;
            $transaction['AddBy'] = $this->session->userdata('userId');
            $transaction['AddTime'] = date('Y-m-d H:i:s');
            $transaction['last_update_ip'] = get_client_ip();
            $transaction['branch_id'] = $this->session->userdata('BRANCHid');

            $this->db->insert('tbl_bank_transactions', $transaction);

            $res = ['success' => true, 'message' => 'Transaction added successfully'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateBankTransaction()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $transactionId = $data->transaction_id;
            $transaction = (array)$data;
            unset($transaction['transaction_id']);

            $this->db->where('transaction_id', $transactionId)->update('tbl_bank_transactions', $transaction);

            $res = ['success' => true, 'message' => 'Transaction update successfully'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function getBankTransactions()
    {
        $data = json_decode($this->input->raw_input_stream);

        $accountClause = "";
        if (isset($data->accountId) && $data->accountId != null) {
            $accountClause = " and bt.account_id = '$data->accountId'";
        }

        $dateClause = "";
        if (
            isset($data->dateFrom) && $data->dateFrom != ''
            && isset($data->dateTo) && $data->dateTo != ''
        ) {
            $dateClause = " and bt.transaction_date between '$data->dateFrom' and '$data->dateTo'";
        }

        $clause = "";
        if (isset($data->transactionType) && $data->transactionType != '') {
            $clause .= " and bt.transaction_type = '$data->transactionType'";
        }

        if (isset($data->transactionId) && $data->transactionId != '') {
            $clause .= " and bt.transaction_id = '$data->transactionId'";
        }

        $transactions = $this->db->query("
            select 
                bt.*,
                ac.account_name,
                ac.account_number,
                ac.bank_name,
                ac.branch_name,
                u.FullName as AddBy
            from tbl_bank_transactions bt
            join tbl_bank_accounts ac on ac.account_id = bt.account_id
            join tbl_user u on u.User_SlNo = bt.AddBy
            where bt.status = 1
            and bt.branch_id = ?
            and bt.form_type = 'bank'
            $accountClause $dateClause $clause
            order by bt.transaction_id desc
        ", $this->session->userdata('BRANCHid'))->result();

        echo json_encode($transactions);
    }

    public function getAllBankTransactions()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        $order = "transaction_date desc, sequence, id desc";

        if (isset($data->accountId) && $data->accountId != null) {
            $clauses .= " and account_id = '$data->accountId'";
        }

        if (
            isset($data->dateFrom) && $data->dateFrom != ''
            && isset($data->dateTo) && $data->dateTo != ''
        ) {
            $clauses .= " and transaction_date between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->transactionType) && $data->transactionType != '') {
            $clauses .= " and transaction_type = '$data->transactionType'";
        }

        if (isset($data->ledger)) {
            $order = "transaction_date, sequence, id";
        }
        // tbl_loan_transactions
        $transactions = $this->db->query("
            select * from(
                select 
                    'a' as sequence,
                    bt.transaction_id as id,
                    bt.transaction_type as description,
                    bt.account_id,
                    bt.transaction_date,
                    bt.transaction_type,
                    bt.amount as deposit,
                    0.00 as withdraw,
                    bt.note,
                    ac.account_name,
                    ac.account_number,
                    ac.bank_name,
                    ac.branch_name,
                    0.00 as balance
                from tbl_bank_transactions bt
                join tbl_bank_accounts ac on ac.account_id = bt.account_id
                where bt.status = 1
                and bt.transaction_type = 'deposit'
                and bt.branch_id = " . $this->session->userdata('BRANCHid') . "

                UNION
                select 
                    'b' as sequence,
                    bt.transaction_id as id,
                    bt.transaction_type as description,
                    bt.account_id,
                    bt.transaction_date,
                    bt.transaction_type,
                    0.00 as deposit,
                    bt.amount as withdraw,
                    bt.note,
                    ac.account_name,
                    ac.account_number,
                    ac.bank_name,
                    ac.branch_name,
                    0.00 as balance
                from tbl_bank_transactions bt
                join tbl_bank_accounts ac on ac.account_id = bt.account_id
                where bt.status = 1
                and bt.transaction_type = 'withdraw'
                and bt.branch_id = " . $this->session->userdata('BRANCHid') . "
                
                UNION
                select 
                    'c' as sequence,
                    bt.transaction_id as id,
                    concat('Loan Bank Received - ', ac.bank_name, ' - ', ac.branch_name, ' - ', ac.account_name, ' - ', ac.account_number) as description,
                    bt.bank_account_id as account_id,
                    bt.transaction_date,
                    bt.transaction_type,
                    bt.amount as deposit,
                    0.00 as withdraw,
                    bt.note,
                    ac.account_name,
                    ac.account_number,
                    ac.bank_name,
                    ac.branch_name,
                    0.00 as balance
                from tbl_loan_transactions bt 
                left join tbl_bank_accounts ac on ac.account_id = bt.bank_account_id
                where bt.status = 1
                and bt.branch_id = " . $this->session->userdata('BRANCHid') . "
                and bt.transaction_type = 'Receive'
                and bt.payment_type = 'Bank'
                
                UNION
                select
                    'd' as sequence,
                    bt.transaction_id as id,
                    concat('Loan Bank Paid - ', ac.bank_name, ' - ', ac.branch_name, ' - ', ac.account_name, ' - ', ac.account_number) as description,
                    bt.bank_account_id as account_id,
                    bt.transaction_date,
                    bt.transaction_type,
                    0.00 as deposit,
                    bt.amount as withdraw,
                    bt.note,
                    ac.account_name,
                    ac.account_number,
                    ac.bank_name,
                    ac.branch_name,
                    0.00 as balance
                from tbl_loan_transactions bt
                left join tbl_bank_accounts ac on ac.account_id = bt.bank_account_id
                where bt.status = 1
                and bt.branch_id = " . $this->session->userdata('BRANCHid') . "
                and bt.transaction_type = 'Payment'
                and bt.payment_type = 'Bank'
                
                UNION
                select
                    'e' as sequence,
                    cp.CPayment_id as id,
                    concat('Payment Received - ', c.Customer_Name, ' (', c.Customer_Code, ')') as description, 
                    cp.account_id,
                    cp.CPayment_date as transaction_date,
                    'deposit' as transaction_type,
                    cp.CPayment_amount as deposit,
                    0.00 as withdraw,
                    cp.CPayment_notes as note,
                    ac.account_name,
                    ac.account_number,
                    ac.bank_name,
                    ac.branch_name,
                    0.00 as balance
                from tbl_customer_payment cp
                join tbl_bank_accounts ac on ac.account_id = cp.account_id
                join tbl_customer c on c.Customer_SlNo = cp.CPayment_customerID
                where cp.account_id is not null
                and cp.status = 'a'
                and cp.CPayment_TransactionType = 'CR'
                and cp.branch_id = " . $this->session->userdata('BRANCHid') . "
                
                UNION
                select
                    'f' as sequence,
                    cp.CPayment_id as id,
                    concat('paid to customer - ', c.Customer_Name, ' (', c.Customer_Code, ')') as description, 
                    cp.account_id,
                    cp.CPayment_date as transaction_date,
                    'withdraw' as transaction_type,
                    0.00 as deposit,
                    cp.CPayment_amount as withdraw,
                    cp.CPayment_notes as note,
                    ac.account_name,
                    ac.account_number,
                    ac.bank_name,
                    ac.branch_name,
                    0.00 as balance
                from tbl_customer_payment cp
                join tbl_bank_accounts ac on ac.account_id = cp.account_id
                join tbl_customer c on c.Customer_SlNo = cp.CPayment_customerID
                where cp.account_id is not null
                and cp.status = 'a'
                and cp.CPayment_TransactionType = 'CP'
                and cp.branch_id = " . $this->session->userdata('BRANCHid') . "
                
                UNION
                select 
                    'g' as sequence,
                    sp.SPayment_id as id,
                    concat('paid - ', s.Supplier_Name, ' (', s.Supplier_Code, ')') as description, 
                    sp.account_id,
                    sp.SPayment_date as transaction_date,
                    'withdraw' as transaction_type,
                    0.00 as deposit,
                    sp.SPayment_amount as withdraw,
                    sp.SPayment_notes as note,
                    ac.account_name,
                    ac.account_number,
                    ac.bank_name,
                    ac.branch_name,
                    0.00 as balance
                from tbl_supplier_payment sp
                join tbl_bank_accounts ac on ac.account_id = sp.account_id
                join tbl_supplier s on s.Supplier_SlNo = sp.SPayment_customerID
                where sp.account_id is not null
                and sp.status = 'a'
                and sp.SPayment_TransactionType = 'CP'
                and sp.branch_id = " . $this->session->userdata('BRANCHid') . "
                
                UNION
                select 
                    'h' as sequence,
                    sp.SPayment_id as id,
                    concat('received from supplier - ', s.Supplier_Name, ' (', s.Supplier_Code, ')') as description, 
                    sp.account_id,
                    sp.SPayment_date as transaction_date,
                    'deposit' as transaction_type,
                    sp.SPayment_amount as deposit,
                    0.00 as withdraw,
                    sp.SPayment_notes as note,
                    ac.account_name,
                    ac.account_number,
                    ac.bank_name,
                    ac.branch_name,
                    0.00 as balance
                from tbl_supplier_payment sp
                join tbl_bank_accounts ac on ac.account_id = sp.account_id
                join tbl_supplier s on s.Supplier_SlNo = sp.SPayment_customerID
                where sp.account_id is not null
                and sp.status = 'a'
                and sp.SPayment_TransactionType = 'CR'
                and sp.branch_id = " . $this->session->userdata('BRANCHid') . "

                UNION
                select 
                    'i' as sequence,
                    ct.Tr_SlNo as id,
                    concat('Cash Transaction Bank Received - ', ac.bank_name, ' - ', ac.branch_name, ' - ', ac.account_name, ' - ', ac.account_number) as description,
                    ct.bank_account_id as account_id,
                    ct.Tr_date as transaction_date,
                    ct.Tr_Type as transaction_type,
                    ct.In_Amount as deposit,
                    0.00 as withdraw,
                    ct.Tr_Description as note,
                    ac.account_name,
                    ac.account_number,
                    ac.bank_name,
                    ac.branch_name,
                    0.00 as balance
                from tbl_cashtransaction ct
                left join tbl_bank_accounts ac on ac.account_id = ct.bank_account_id
                where ct.status = 'a'
                and ct.Tr_Type = 'In Cash'
                and ct.payment_type = 'Bank'
                and ct.branch_id = " . $this->session->userdata('BRANCHid') . "

                UNION
                select 
                    'j' as sequence,
                    ct.Tr_SlNo as id,
                    concat('Cash Transaction Bank Received - ', ac.bank_name, ' - ', ac.branch_name, ' - ', ac.account_name, ' - ', ac.account_number) as description,
                    ct.bank_account_id as account_id,
                    ct.Tr_date as transaction_date,
                    ct.Tr_Type as transaction_type,
                    0.00 as deposit,
                    ct.Out_Amount as withdraw,
                    ct.Tr_Description as note,
                    ac.account_name,
                    ac.account_number,
                    ac.bank_name,
                    ac.branch_name,
                    0.00 as balance
                from tbl_cashtransaction ct
                left join tbl_bank_accounts ac on ac.account_id = ct.bank_account_id
                where ct.status = 'a'
                and ct.Tr_Type = 'Out Cash'
                and ct.payment_type = 'Bank'
                and ct.branch_id = " . $this->session->userdata('BRANCHid') . "

                UNION
                select
                    'k' as sequence,
                    bt.transaction_id as id,
                    concat('Loan Bank Paid - ', ac.bank_name, ' - ', ac.branch_name, ' - ', ac.account_name, ' - ', ac.account_number) as description,
                    bt.bank_account_id as account_id,
                    bt.transaction_date,
                    bt.transaction_type,
                    bt.amount as deposit,
                    0.00 as withdraw,
                    bt.note,
                    ac.account_name,
                    ac.account_number,
                    ac.bank_name,
                    ac.branch_name,
                    0.00 as balance
                from tbl_loan_transactions bt
                left join tbl_bank_accounts ac on ac.account_id = bt.bank_account_id
                where bt.status = 1
                and bt.branch_id = " . $this->session->userdata('BRANCHid') . "
                and bt.transaction_type = 'Interest'
                and bt.payment_type = 'Bank'

                UNION
                select
                    'l' as sequence,
                    ch.id,
                    concat('Bank Cheque Paid - ', ac.bank_name, ' - ', ac.branch_name, ' - ', ac.account_name, ' - ', ac.account_number) as description,
                    ch.account_id,
                    ch.date as transaction_date,
                    ch.type as transaction_type,
                    ch.check_amount as withdraw ,
                    0.00 as deposit,
                    ch.note,
                    ac.account_name,
                    ac.account_number,
                    ac.bank_name,
                    ac.branch_name,
                    0.00 as balance
                from tbl_cheque ch
                left join tbl_bank_accounts ac on ac.account_id = ch.account_id
                where ch.status = 'a'
                and ch.check_status = 'Pa'
                and ch.branch_id = " . $this->session->userdata('BRANCHid') . "

                UNION
                select
                    'm' as sequence,
                    bt.transaction_id as id,
                    concat('Investment Transaction - ', ac.bank_name, ' - ', ac.branch_name, ' - ', ac.account_name, ' - ', ac.account_number) as description,
                    bt.bank_account_id,
                    bt.transaction_date,
                    bt.transaction_type,
                    bt.amount as withdraw ,
                    0.00 as deposit,
                    bt.note,
                    ac.account_name,
                    ac.account_number,
                    ac.bank_name,
                    ac.branch_name,
                    0.00 as balance
                from tbl_investment_transactions bt
                left join tbl_bank_accounts ac on ac.account_id = bt.bank_account_id
                where bt.status = 1
                and bt.transaction_type = 'Payment'
                and bt.payment_type = 'Bank'
                and bt.branch_id = " . $this->session->userdata('BRANCHid') . "

                UNION
                select
                    'n' as sequence,
                    bt.transaction_id as id,
                    concat('Investment Transaction - ', ac.bank_name, ' - ', ac.branch_name, ' - ', ac.account_name, ' - ', ac.account_number) as description,
                    bt.bank_account_id,
                    bt.transaction_date,
                    bt.transaction_type,
                    0.00 as withdraw ,
                    bt.amount as deposit,
                    bt.note,
                    ac.account_name,
                    ac.account_number,
                    ac.bank_name,
                    ac.branch_name,
                    0.00 as balance
                from tbl_investment_transactions bt
                left join tbl_bank_accounts ac on ac.account_id = bt.bank_account_id
                where bt.status = 1
                and bt.transaction_type = 'Receive'
                and bt.payment_type = 'Bank'
                and bt.branch_id = " . $this->session->userdata('BRANCHid') . "

                UNION
                select
                    'o' as sequence,
                    fbt.fdr_transaction_id as id,
                    concat('FDR Transaction - ', ac.bank_name, ' - ', ac.branch_name, ' - ', ac.account_name, ' - ', ac.account_number) as description,
                    fbt.bank_account_id,
                    fbt.transaction_date,
                    fbt.transaction_type,
                    0.00 as withdraw ,
                    fbt.amount as deposit,
                    fbt.note,
                    ac.account_name,
                    ac.account_number,
                    ac.bank_name,
                    ac.branch_name,
                    0.00 as balance
                from tbl_fdr_transactions fbt
                left join tbl_bank_accounts ac on ac.account_id = fbt.bank_account_id
                where fbt.status = 1
                and fbt.transaction_type = 'deposit'
                and fbt.branch_id = " . $this->session->userdata('BRANCHid') . "

                UNION
                select
                    'p' as sequence,
                    fbt.fdr_transaction_id as id,
                    concat('FDR Transaction - ', ac.bank_name, ' - ', ac.branch_name, ' - ', ac.account_name, ' - ', ac.account_number) as description,
                    fbt.bank_account_id,
                    fbt.transaction_date,
                    fbt.transaction_type,
                    fbt.amount as withdraw ,
                    0.00 as deposit,
                    fbt.note,
                    ac.account_name,
                    ac.account_number,
                    ac.bank_name,
                    ac.branch_name,
                    0.00 as balance
                from tbl_fdr_transactions fbt
                left join tbl_bank_accounts ac on ac.account_id = fbt.bank_account_id
                where fbt.status = 1
                and fbt.transaction_type = 'withdraw'
                and fbt.branch_id = " . $this->session->userdata('BRANCHid') . "

                UNION
                select
                    'q' as sequence,
                    cvs.id,
                    concat('Salary Conveyance Payment - ', ac.bank_name, ' - ', ac.branch_name, ' - ', ac.account_name, ' - ', ac.account_number) as description,
                    cvs.account_id,
                    cvs.transaction_date,
                    cvs.transaction_type,
                    cvs.conveyance as withdraw,
                    0.00 as deposit,
                    cvs.note,
                    ac.account_name,
                    ac.account_number,
                    ac.bank_name,
                    ac.branch_name,
                    0.00 as balance
                from tbl_conveyance_salary cvs
                left join tbl_bank_accounts ac on ac.account_id = cvs.account_id
                where cvs.status = 'a'
                and cvs.transaction_type = 'payment'
                and cvs.payment_type = 'Bank'
                and cvs.branch_id = " . $this->session->userdata('BRANCHid') . "

                UNION
                select
                    'r' as sequence,
                    cvs.id,
                    concat('Salary Conveyance Receive - ', ac.bank_name, ' - ', ac.branch_name, ' - ', ac.account_name, ' - ', ac.account_number) as description,
                    cvs.account_id,
                    cvs.transaction_date,
                    cvs.transaction_type,
                    0.00 as withdraw,
                    cvs.conveyance as deposit,
                    cvs.note,
                    ac.account_name,
                    ac.account_number,
                    ac.bank_name,
                    ac.branch_name,
                    0.00 as balance
                from tbl_conveyance_salary cvs
                left join tbl_bank_accounts ac on ac.account_id = cvs.account_id
                where cvs.status = 'a'
                and cvs.transaction_type = 'receive'
                and cvs.payment_type = 'Bank'
                and cvs.branch_id = " . $this->session->userdata('BRANCHid') . "

            ) as tbl
            where 1 = 1 $clauses
            order by $order
        ")->result();

        if (!isset($data->ledger)) {
            echo json_encode($transactions);
            exit;
        }

        $previousBalance = $this->mt->getBankTransactionSummary($data->accountId, $data->dateFrom)[0]->balance;

        $transactions = array_map(function ($key, $trn) use ($previousBalance, $transactions) {
            $trn->balance = (($key == 0 ? $previousBalance : $transactions[$key - 1]->balance) + $trn->deposit) - $trn->withdraw;
            return $trn;
        }, array_keys($transactions), $transactions);

        $res['previousBalance'] = $previousBalance;
        $res['transactions'] = $transactions;

        echo json_encode($res);
    }

    public function removeBankTransaction()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $transaction = array(
                'status' => 0,
                'DeletedBy' => $this->session->userdata('userId'),
                'DeletedTime' => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
            );
            $this->db->set($transaction)->where('transaction_id', $data->transaction_id)->update('tbl_bank_transactions');

            $res = ['success' => true, 'message' => 'Transaction removed'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function bankTransactionReprot()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Bank Transaction Report";
        $data['content'] = $this->load->view("Administrator/account/bank_transaction_report", $data, true);
        $this->load->view("Administrator/index", $data);
    }

    public function cashView()
    {
        $data['title'] = "Cash View";

        $data['transaction_summary'] = $this->mt->getTransactionSummary();
        $data['bank_account_summary'] = $this->mt->getBankTransactionSummary();

        $data['content'] = $this->load->view('Administrator/account/cash_view', $data, true);
        $this->load->view('Administrator/index', $data);
    }

    public function getBankBalance()
    {
        $data = json_decode($this->input->raw_input_stream);

        $accountId = null;
        if (isset($data->accountId) && $data->accountId != '') {
            $accountId = $data->accountId;
        }
        $bankBalance = $this->mt->getBankTransactionSummary($accountId);
        echo json_encode($bankBalance);
    }

    public function getCashAndBankBalance()
    {
        $data = json_decode($this->input->raw_input_stream);

        $date = null;
        if (isset($data->date) && $data->date != '') {
            $date = $data->date;
        }

        $res['cashBalance'] = $this->mt->getTransactionSummary($date);

        $res['bankBalance'] = $this->mt->getBankTransactionSummary(null, $date);

        echo json_encode($res);
    }

    public function bankLedger()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Bank Ledger";
        $data['content'] = $this->load->view("Administrator/account/bank_ledger", $data, true);
        $this->load->view("Administrator/index", $data);
    }

    public function cashLedger()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Cash Ledger";
        $data['content'] = $this->load->view("Administrator/account/cash_ledger", $data, true);
        $this->load->view("Administrator/index", $data);
    }

    public function getCashLedger()
    {
        $data = json_decode($this->input->raw_input_stream);

        $previousBalance = $this->mt->getTransactionSummary($data->fromDate)->cash_balance;

        $ledger = $this->db->query("
            /* Cash In */
            select 
                sm.SaleMaster_SlNo as id,
                sm.SaleMaster_SaleDate as date,
                concat('Sale - ', sm.SaleMaster_InvoiceNo, ' - ', c.Customer_Name, ' (', c.Customer_Code, ')', ' - Bill: ', sm.SaleMaster_TotalSaleAmount) as description,
                sm.cash_paid as in_amount,
                0.00 as out_amount
            from tbl_salesmaster sm 
            left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            where sm.status = 'a'
            and sm.branch_id = '$this->brunch'
            and sm.SaleMaster_SaleDate between '$data->fromDate' and '$data->toDate'
            
            UNION
            
            select 
                cp.CPayment_id as id,
                cp.CPayment_date as date,
                concat('Due collection - ', cp.CPayment_invoice, ' - ', c.Customer_Name, ' (', c.Customer_Code, ')') as description,
                cp.CPayment_amount  as in_amount,
                0.00 as out_amount
            from tbl_customer_payment cp
            left join tbl_customer c on c.Customer_SlNo = cp.CPayment_customerID
            where cp.status = 'a'
            and cp.branch_id = '$this->brunch'
            and cp.CPayment_TransactionType = 'CR'
            and cp.CPayment_Paymentby != 'bank'
            and cp.CPayment_date between '$data->fromDate' and '$data->toDate'
            
            UNION
            
            select 
                sp.SPayment_id as id,
                sp.SPayment_date as date,
                concat('Received from supplier - ', sp.SPayment_invoice, ' - ', s.Supplier_Name, ' (', s.Supplier_Code, ')') as description,
                sp.SPayment_amount as in_amount,
                0.00 as out_amount
            from tbl_supplier_payment sp
            left join tbl_supplier s on s.Supplier_SlNo = sp.SPayment_customerID
            where sp.SPayment_TransactionType = 'CR'
            and sp.status = 'a'
            and sp.SPayment_Paymentby != 'bank'
            and sp.branch_id = '$this->brunch'
            and sp.SPayment_date between '$data->fromDate' and '$data->toDate'
            
            UNION
            
            select 
                ct.Tr_SlNo as id,
                ct.Tr_date as date,
                concat('Cash in - ', acc.Acc_Name) as description,
                ct.In_Amount as in_amount,
                0.00 as out_amount
            from tbl_cashtransaction ct
            join tbl_account acc on acc.Acc_SlNo = ct.Acc_SlID
            where ct.status = 'a'
            and ct.branch_id = '$this->brunch'
            and ct.Tr_Type = 'In Cash'
            and ct.payment_type = 'Cash'
            and ct.Tr_date between '$data->fromDate' and '$data->toDate'
            
            UNION
            
            select 
                bt.transaction_id as id,
                bt.transaction_date as date,
                concat('Bank withdraw - ', ba.bank_name, ' - ', ba.branch_name, ' - ', ba.account_name, ' - ', ba.account_number) as description,
                bt.amount as in_amount,
                0.00 as out_amount
            from tbl_bank_transactions bt 
            join tbl_bank_accounts ba on ba.account_id = bt.account_id
            where bt.status = 1
            and bt.branch_id = '$this->brunch'
            and bt.transaction_type = 'withdraw'
            and bt.transaction_date between '$data->fromDate' and '$data->toDate'
            
            UNION
            
            select 
                bt.transaction_id as id,
                bt.transaction_date as date,
                concat('Loan Received - ', ba.bank_name, ' - ', ba.branch_name, ' - ', ba.account_name, ' - ', ba.account_number) as description,
                bt.amount as in_amount,
                0.00 as out_amount
            from tbl_loan_transactions bt 
            join tbl_loan_accounts ba on ba.account_id = bt.account_id
            where bt.status = 1
            and bt.branch_id = '$this->brunch'
            and bt.transaction_type = 'Receive'
            and bt.payment_type = 'Cash'
            and bt.transaction_date between '$data->fromDate' and '$data->toDate'
            
            UNION
            select 
                ba.	account_id as id,
                ba.save_date as date,
                concat('Loan Initial Balance - ', ba.bank_name, ' - ', ba.branch_name, ' - ', ba.account_name, ' - ', ba.account_number) as description,
                ba.initial_balance as in_amount,
                0.00 as out_amount
            from tbl_loan_accounts ba
            where ba.branch_id = '$this->brunch'
            and ba.save_date between '$data->fromDate' and '$data->toDate'
            
            UNION
            
            select 
                bt.transaction_id as id,
                bt.transaction_date as date,
                concat('Invest Received - ', ba.Acc_Name, ' (', ba.Acc_Code, ')') as description,
                bt.amount as in_amount,
                0.00 as out_amount
            from tbl_investment_transactions bt 
            join tbl_investment_account ba on ba.Acc_SlNo = bt.account_id
            where bt.status = 1
            and bt.payment_type = 'Cash'
            and bt.branch_id = '$this->brunch'
            and bt.transaction_type = 'Receive'
            and bt.transaction_date between '$data->fromDate' and '$data->toDate'

            UNION
            
            select 
                ass.as_id as id,
                ass.as_date as date,
                concat('Sale Assets - ', ass.as_name) as description,
                ass.as_amount as in_amount,
                0.00 as out_amount
            from tbl_assets ass
            where ass.branch_id = '$this->brunch'
            and ass.status = 'a'
            and ass.buy_or_sale = 'sale'
            and ass.as_date between '$data->fromDate' and '$data->toDate'
            
            /* Cash out */
            
            UNION
            
            select 
                pm.PurchaseMaster_SlNo as id,
                pm.PurchaseMaster_OrderDate as date,
                concat('Purchase - ', pm.PurchaseMaster_InvoiceNo, ' - ', s.Supplier_Name, ' (', s.Supplier_Code, ')', ' - Bill: ', pm.PurchaseMaster_TotalAmount) as description,
                0.00 as in_amount,
                pm.PurchaseMaster_PaidAmount as out_amount
            from tbl_purchasemaster pm 
            left join tbl_supplier s on s.Supplier_SlNo = pm.Supplier_SlNo
            where pm.status = 'a'
            and pm.branch_id = '$this->brunch'
            and pm.PurchaseMaster_OrderDate between '$data->fromDate' and '$data->toDate'
            
            UNION
            
            select 
                sp.SPayment_id as id,
                sp.SPayment_date as date,
                concat('Supplier payment - ', sp.SPayment_invoice, ' - ', s.Supplier_Name, ' (', s.Supplier_Code, ')') as description,
                0.00 as in_amount,
                sp.SPayment_amount as out_amount
            from tbl_supplier_payment sp 
            left join tbl_supplier s on s.Supplier_SlNo = sp.SPayment_customerID
            where sp.SPayment_TransactionType = 'CP'
            and sp.status = 'a'
            and sp.SPayment_Paymentby != 'bank'
            and sp.branch_id = '$this->brunch'
            and sp.SPayment_date between '$data->fromDate' and '$data->toDate'
            
            UNION
            
            select 
                cp.CPayment_id as id,
                cp.CPayment_date as date,
                concat('Paid to customer - ', cp.CPayment_invoice, ' - ', c.Customer_Name, '(', c.Customer_Code, ')') as description,
                0.00 as in_amount,
                cp.CPayment_amount as out_amount
            from tbl_customer_payment cp
            join tbl_customer c on c.Customer_SlNo = cp.CPayment_customerID
            where cp.CPayment_TransactionType = 'CP'
            and cp.status = 'a'
            and cp.CPayment_Paymentby != 'bank'
            and cp.branch_id = '$this->brunch'
            and cp.CPayment_date between '$data->fromDate' and '$data->toDate'
            
            UNION
            
            select 
                ct.Tr_SlNo as id,
                ct.Tr_date as date,
                concat('Cash out - ', acc.Acc_Name) as description,
                0.00 as in_cash,
                ct.Out_Amount as out_amount
            from tbl_cashtransaction ct
            join tbl_account acc on acc.Acc_SlNo = ct.Acc_SlID
            where ct.Tr_Type = 'Out Cash'
            and ct.payment_type = 'Cash'
            and ct.status = 'a'
            and ct.branch_id = '$this->brunch'
            and ct.Tr_date between '$data->fromDate' and '$data->toDate'
            
            UNION
            
            select 
                bt.transaction_id as id,
                bt.transaction_date as date,
                concat('Bank deposit - ', ba.bank_name, ' - ', ba.branch_name, ' - ', ba.account_name, ' - ', ba.account_number) as description,
                0.00 as in_amount,
                bt.amount as out_amount
            from tbl_bank_transactions bt
            join tbl_bank_accounts ba on ba.account_id = bt.account_id
            where bt.transaction_type = 'deposit'
            and bt.form_type = 'bank'
            and bt.status = 1
            and bt.branch_id = '$this->brunch'
            and bt.transaction_date between '$data->fromDate' and '$data->toDate'

            UNION
            
            select 
                ep.id as id,
                ep.payment_date as date,
                concat('Employee salary - ', m.month_name) as description,
                0.00 as in_amount,
                ep.total_payment_amount as out_amount
            from tbl_employee_payment ep
            join tbl_month m on m.month_id = ep.month_id
            where ep.branch_id = '$this->brunch'
            and ep.status = 'a'
            and ep.payment_date between '$data->fromDate' and '$data->toDate'

            UNION
            
            select 
                bt.transaction_id as id,
                bt.transaction_date as date,
                concat('Loan Payment - ', ba.bank_name, ' - ', ba.branch_name, ' - ', ba.account_name, ' - ', ba.account_number) as description,
                0.00 as in_amount,
                bt.amount as out_amount
            from tbl_loan_transactions bt
            join tbl_loan_accounts ba on ba.account_id = bt.account_id
            where bt.transaction_type = 'Payment'
            and bt.payment_type = 'Cash'
            and bt.status = 1
            and bt.branch_id = '$this->brunch'
            and bt.transaction_date between '$data->fromDate' and '$data->toDate'

            UNION
            
            select 
                bt.transaction_id as id,
                bt.transaction_date as date,
                concat('Invest Payment - ', ba.Acc_Name, ' (', ba.Acc_Code, ')') as description,
                0.00 as in_amount,
                bt.amount as out_amount
            from tbl_investment_transactions bt 
            join tbl_investment_account ba on ba.Acc_SlNo = bt.account_id
            where bt.status = 1
            and bt.payment_type = 'Cash'
            and bt.branch_id = '$this->brunch'
            and bt.transaction_type = 'Payment'
            and bt.transaction_date between '$data->fromDate' and '$data->toDate'

            UNION
            
            select 
                ass.as_id as id,
                ass.as_date as date,
                concat('Buy Assets - ', ass.as_name, ' from ', ass.as_sp_name) as description,
                0.00 as in_amount,
                ass.as_amount as out_amount
            from tbl_assets ass
            where ass.branch_id = '$this->brunch'
            and ass.status = 'a'
            and ass.buy_or_sale = 'buy'
            and ass.as_date between '$data->fromDate' and '$data->toDate'

            order by date, id
        ")->result();

        $ledger = array_map(function ($ind, $row) use ($previousBalance, $ledger) {
            $row->balance = (($ind == 0 ? $previousBalance : $ledger[$ind - 1]->balance) + $row->in_amount) - $row->out_amount;
            return $row;
        }, array_keys($ledger), $ledger);

        $res['previousBalance'] = $previousBalance;
        $res['ledger'] = $ledger;
        echo json_encode($res);
    }

    function all_transaction_report()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Cash Transaction Report";
        $data['content'] = $this->load->view('Administrator/account/all_transaction_report', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
}
