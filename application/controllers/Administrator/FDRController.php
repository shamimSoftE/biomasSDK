<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class FDRController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->brunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model('Model_table', "mt", TRUE);
    }

    public function FDRTransactions()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "FDR Transactions";
        $data['content'] = $this->load->view('Administrator/account/fdr/fdr_transactions', $data, true);
        $this->load->view('Administrator/index', $data);
    }

    public function getLoanInitialBalance()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (
            isset($data->dateFrom) && $data->dateFrom != ''
            && isset($data->dateTo) && $data->dateTo != ''
        ) {
            $clauses .= " and la.save_date between '$data->dateFrom' and '$data->dateTo'";
        }

        $accounts = $this->db->query("
            select la.* from tbl_loan_accounts la
                where la.status = 1
                and la.branch_id= " . $this->session->userdata('BRANCHid') . "
                $clauses
        ")->result();

        $balance = array_reduce($accounts, function ($prev, $curr) {
            return $prev + $curr->initial_balance;
        });
        $res = [
            'balance' => $balance,
            'accounts' => $accounts,
        ];

        echo json_encode($res);
    }

    public function getFDRTransactions()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->accountId) && $data->accountId != null) {
            $clauses .= " and fdr.fdr_account_id  = '$data->accountId'";
        }

        if (
            isset($data->dateFrom) && $data->dateFrom != ''
            && isset($data->dateTo) && $data->dateTo != ''
        ) {
            $clauses .= " and fdr.transaction_date between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->transactionType) && $data->transactionType != '') {
            $clauses .= " and fdr.transaction_type = '$data->transactionType'";
        }

        $transactions = $this->db->query("
            select 
                fdr.*,
                fac.account_name,
                fac.account_number,
                fac.bank_name,
                fac.branch_name,
                bac.bank_name as bankName,
                bac.account_name as bank_accountName,
                bac.branch_name as bank_branchName,
                bac.account_number as bank_accountNumber,
                u.FullName as AddBy
            from tbl_fdr_transactions fdr
            left join tbl_fdr_accounts fac on fac.fdr_account_id = fdr.fdr_account_id
            left join tbl_bank_accounts bac on bac.account_id = fdr.bank_account_id
            left join tbl_user u on u.User_SlNo = fdr.AddBy
            where fdr.status = 1
            and fdr.branch_id = ?
            $clauses
            order by fdr.fdr_transaction_id desc
        ", $this->session->userdata('BRANCHid'))->result();
        echo json_encode($transactions);
    }

    public function addFDRTransactions()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $transaction = (array)$data;
            unset($transaction['fdr_transaction_id']);
            $transaction['AddBy'] = $this->session->userdata('userId');
            $transaction['AddTime'] = date('Y-m-d H:i:s');
            $transaction['last_update_ip'] = get_client_ip();
            $transaction['branch_id'] = $this->session->userdata('BRANCHid');

            $this->db->insert('tbl_fdr_transactions', $transaction);

            $res = ['success' => true, 'message' => 'Transaction added successfully'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }
        echo json_encode($res);
    }

    public function updateFDRTransaction()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
           
            $transactionId = $data->fdr_transaction_id;
            $transaction = (array)$data;
            unset($transaction['fdr_transaction_id']);
            $transaction['UpdateBy'] = $this->session->userdata('userId');
            $transaction['UpdateTime'] = date('Y-m-d H:i:s');
            $transaction['last_update_ip'] = get_client_ip();

            $this->db->where('fdr_transaction_id', $transactionId)->update('tbl_fdr_transactions', $transaction);

            $res = ['success' => true, 'message' => 'Transaction update successfully'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function deleteFDRTransaction()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $account = array(
                'status'         => '0',
                "DeletedBy"      => $this->session->userdata("userId"),
                "DeletedTime"    => date("Y-m-d H:i:s"),
                "last_update_ip" => get_client_ip()
            );
            $this->db->where('fdr_transaction_id', $data->fdr_transaction_id)->update('tbl_fdr_transactions', $account);
            $res = ['success' => true, 'message' => 'Transaction removed'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }
        echo json_encode($res);
    }

    public function getLoanBalance()
    {
        $data = json_decode($this->input->raw_input_stream);

        $accountId = null;
        if (isset($data->accountId) && $data->accountId != '') {
            $accountId = $data->accountId;
        }

        $loanBalance = $this->mt->getLoanTransactionSummary($accountId);

        echo json_encode($loanBalance);
    }

    public function FDRAccounts()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "FDR Accounts";
        $data['content'] = $this->load->view('Administrator/account/fdr/fdr_accounts', $data, true);
        $this->load->view('Administrator/index', $data);
    }

    public function addFDRAccount()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $accountCheck = $this->db->query("
                select
                *
                from tbl_fdr_accounts
                where account_number = ?
            ", $data->account_number)->num_rows();

            if ($accountCheck != 0) {
                $res = ['success' => false, 'message' => 'Account number already exists'];
                echo json_encode($res);
                exit;
            }

            $account                   = (array)$data;
            $account['AddBy']          = $this->session->userdata('userId');
            unset($account['account_id']);
            $account['save_date']      = date('Y-m-d');
            $account['AddTime']        = date('Y-m-d H:i:s');
            $account['last_update_ip'] = get_client_ip();
            $account['branch_id']      = $this->session->userdata('BRANCHid');

            $this->db->insert('tbl_fdr_accounts', $account);
            $res = ['success' => true, 'message' => 'Account created successfully'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }
        echo json_encode($res);
    }

    public function updateFDRAccount()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $accountCheck = $this->db->query("
                select
                *
                from tbl_fdr_accounts
                where account_number = ?
                and fdr_account_id != ?
            ", [$data->account_number, $data->account_id])->num_rows();

            if ($accountCheck != 0) {
                $res = ['success' => false, 'message' => 'Account number already exists'];
                echo json_encode($res);
                exit;
            }

            $account                   = (array)$data;
            $account['UpdateBy']       = $this->session->userdata('userId');
            $account['UpdateTime']     = date('Y-m-d H:i:s');
            $account['last_update_ip'] = get_client_ip();
            
            $this->db->where('fdr_account_id', $data->account_id);
            unset($account['account_id']);
            $this->db->update('tbl_fdr_accounts', $account);
            $res = ['success' => true, 'message' => 'Account updated successfully'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }
        echo json_encode($res);
    }

    public function getFDRAccounts() 
    {
        $accounts = $this->db->query("
            select 
            *,
            case status 
            when 1 then 'Active'
            else 'Inactive'
            end as status_text
            from tbl_fdr_accounts 
            where branch_id = ?
        ", $this->session->userdata('BRANCHid'))->result();
        echo json_encode($accounts);
    }

    public function changeLoanAccountstatus()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $rules = array(
                'status'         => $data->account->status == 1 ? 0 : 1,
                "DeletedBy"      => $this->session->userdata("userId"),
                "DeletedTime"    => date("Y-m-d H:i:s"),
                "last_update_ip" => get_client_ip()
            );
            $this->db->where('account_id', $data->account->account_id)->update('tbl_fdr_accounts', $rules);
            $res = ['success' => true, 'message' => 'status Changed'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }
        echo json_encode($res);
    }

    public function FDRAccountsView()
    {
        $data['title'] = "FDR Account View";

        $data['loan_account_summary'] = $this->mt->getFDRTransactionSummary();

        $data['content'] = $this->load->view('Administrator/account/fdr/fdr_view', $data, true);
        $this->load->view('Administrator/index', $data);
    }

    public function loanTransactionReprot()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Loan Transaction Report";
        $data['content'] = $this->load->view("Administrator/account/loan/loan_transaction_report", $data, true);
        $this->load->view("Administrator/index", $data);
    }


    public function getAllLoanTransactions()
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

        $transactions = $this->db->query("
            select * from(
                select 
                    'a' as sequence,
                    lt.transaction_id as id,
                    lt.transaction_type as description,
                    lt.account_id,
                    lt.transaction_date,
                    lt.transaction_type,
                    lt.amount as receive,
                    0.00 as payment,
                    0.00 as interest,
                    lt.note,
                    ac.account_name,
                    ac.account_number,
                    ac.bank_name,
                    ac.branch_name,
                    0.00 as balance
                from tbl_loan_transactions lt
                join tbl_loan_accounts ac on ac.account_id = lt.account_id
                where lt.status = 1
                and lt.transaction_type = 'Receive'
                and lt.branch_id = " . $this->session->userdata('BRANCHid') . "

                UNION
                select 
                    'b' as sequence,
                    lt.transaction_id as id,
                    lt.transaction_type as description,
                    lt.account_id,
                    lt.transaction_date,
                    lt.transaction_type,
                    0.00 as receive,
                    0.00 as payment,
                    lt.amount as interest,
                    lt.note,
                    ac.account_name,
                    ac.account_number,
                    ac.bank_name,
                    ac.branch_name,
                    0.00 as balance
                from tbl_loan_transactions lt
                join tbl_loan_accounts ac on ac.account_id = lt.account_id
                where lt.status = 1
                and lt.transaction_type = 'Interest'
                and lt.branch_id = " . $this->session->userdata('BRANCHid') . "

                UNION
                select 
                    'c' as sequence,
                    lt.transaction_id as id,
                    lt.transaction_type as description,
                    lt.account_id,
                    lt.transaction_date,
                    lt.transaction_type,
                    0.00 as receive,
                    lt.amount as payment,
                    0.00 as interest,
                    lt.note,
                    ac.account_name,
                    ac.account_number,
                    ac.bank_name,
                    ac.branch_name,
                    0.00 as balance
                from tbl_loan_transactions lt
                join tbl_loan_accounts ac on ac.account_id = lt.account_id
                where lt.status = 1
                and lt.transaction_type = 'Payment'
                and lt.branch_id = " . $this->session->userdata('BRANCHid') . "

            ) as tbl
            where 1 = 1 $clauses
            order by $order
        ")->result();

        if (!isset($data->ledger)) {
            echo json_encode($transactions);
            exit;
        }

        $previousBalance = $this->mt->getLoanTransactionSummary($data->accountId, $data->dateFrom)[0]->balance;

        $transactions = array_map(function ($key, $trn) use ($previousBalance, $transactions) {
            $trn->balance = (($key == 0 ? $previousBalance : $transactions[$key - 1]->balance) + $trn->receive + $trn->interest) - $trn->payment;
            return $trn;
        }, array_keys($transactions), $transactions);

        $res['previousBalance'] = $previousBalance;
        $res['transactions'] = $transactions;

        echo json_encode($res);
    }

    public function loanLedger()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Loan Ledger";
        $data['content'] = $this->load->view("Administrator/account/loan/loan_ledger", $data, true);
        $this->load->view("Administrator/index", $data);
    }
}
