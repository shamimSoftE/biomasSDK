<?php
class Graph extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $access = $this->session->userdata('userId');
        $this->branchId = $this->session->userdata('BRANCHid');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model('Model_table', "mt", TRUE);
    }

    public function graph()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Graph";
        $data['content'] = $this->load->view('Administrator/graph/graph', $data, true);
        $this->load->view('Administrator/index', $data);
    }

    public function getOverallData()
    {
        // Monthly Record
        $year = date('Y');
        $month = date('m');

        // Sales text for marquee
        $sales_text = $this->db->query("
                select 
                    concat(
                        'Invoice: ', sm.SaleMaster_InvoiceNo,
                        ', Customer: ', ifnull(c.Customer_Code, ''), ' - ', ifnull(c.Customer_Name, sm.customerName),
                        ', Amount: ', sm.SaleMaster_TotalSaleAmount,
                        ', Paid: ', sm.SaleMaster_PaidAmount,
                        ', Due: ', sm.SaleMaster_DueAmount
                    ) as sale_text
                from tbl_salesmaster sm 
                left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
                where sm.status = 'a'
                and sm.branch_id = ?
                order by sm.SaleMaster_SlNo desc limit 20
            ", $this->branchId)->result();

        // Today's Sale
        $todaysSale = $this->db->query("
                select 
                    ifnull(sum(ifnull(sm.SaleMaster_TotalSaleAmount, 0)), 0) as total_amount
                from tbl_salesmaster sm
                where sm.status = 'a'
                and sm.SaleMaster_SaleDate = ?
                and sm.branch_id = ?
            ", [date('Y-m-d'), $this->branchId])->row()->total_amount;

        // This Month's Sale
        $thisMonthSale = $this->db->query("
                select 
                    ifnull(sum(ifnull(sm.SaleMaster_TotalSaleAmount, 0)), 0) as total_amount
                from tbl_salesmaster sm
                where sm.status = 'a'
                and month(sm.SaleMaster_SaleDate) = ?
                and year(sm.SaleMaster_SaleDate) = ?
                and sm.branch_id = ?
            ", [$month, $year, $this->branchId])->row()->total_amount;

        // Today's Cash Collection
        $todaysCollection = $this->db->query("
                select 
                ifnull((
                    select sum(ifnull(sm.SaleMaster_PaidAmount, 0)) 
                    from tbl_salesmaster sm
                    where sm.status = 'a'
                    and sm.branch_id = " . $this->branchId . "
                    and sm.SaleMaster_SaleDate = '" . date('Y-m-d') . "'
                ), 0) +
                ifnull((
                    select sum(ifnull(cp.CPayment_amount, 0)) 
                    from tbl_customer_payment cp
                    where cp.status = 'a'
                    and cp.CPayment_TransactionType = 'CR'
                    and cp.branch_id = " . $this->branchId . "
                    and cp.CPayment_date = '" . date('Y-m-d') . "'
                ), 0) +
                ifnull((
                    select sum(ifnull(ct.In_Amount, 0)) 
                    from tbl_cashtransaction ct
                    where ct.status = 'a'
                    and ct.branch_id = " . $this->branchId . "
                    and ct.Tr_date = '" . date('Y-m-d') . "'
                ), 0) as total_amount
            ")->row()->total_amount;

        // Cash Balance
        $cashBalance = $this->mt->getTransactionSummary()->cash_balance;

        // Customer Due
        $customerDueResult = $this->mt->customerDue();
        $customerDue = array_sum(array_map(function ($due) {
            return $due->dueAmount;
        }, $customerDueResult));

        // Supplier Due
        $supplierDueResult = $this->mt->supplierDue();
        $supplierDue = array_sum(array_map(function ($due) {
            return $due->due;
        }, $supplierDueResult));

        // Bank balance
        $bankTransactions = $this->mt->getBankTransactionSummary();
        $bankBalance = array_sum(array_map(function ($bank) {
            return $bank->balance;
        }, $bankTransactions));

        // Invest balance
        $investTransactions = $this->mt->getInvestmentTransactionSummary();
        $investBalance = array_sum(array_map(function ($bank) {
            return $bank->balance;
        }, $investTransactions));

        // Loan balance
        $loanTransactions = $this->mt->getLoanTransactionSummary();
        $loanBalance = array_sum(array_map(function ($bank) {
            return $bank->balance;
        }, $loanTransactions));

        //Assets Value
        $assets = $this->mt->assetsReport();
        $assets_value = array_reduce($assets, function ($prev, $curr) {
            return $prev + $curr->approx_amount;
        });

        //stock value
        $stocks = $this->mt->currentStock();
        $stockValue = array_sum(
            array_map(function ($product) {
                return $product->stock_value;
            }, $stocks)
        );

        //this month profit loss
        $sales = $this->db->query("
                select 
                    sm.*
                from tbl_salesmaster sm
                where sm.branch_id = ? 
                and sm.status = 'a'
                and DATE_FORMAT(sm.SaleMaster_SaleDate, '%m') = ?
                and DATE_FORMAT(sm.SaleMaster_SaleDate, '%Y') = ?
            ", [$this->branchId, $month, $year])->result();

        foreach ($sales as $sale) {
            $sale->saleDetails = $this->db->query("
                    select
                        sd.*,
                        (sd.Purchase_Rate * sd.SaleDetails_TotalQuantity) as purchased_amount,
                        (select sd.SaleDetails_TotalAmount - purchased_amount) as profit_loss
                    from tbl_saledetails sd
                    where sd.SaleMaster_IDNo = ?
                ", $sale->SaleMaster_SlNo)->result();
        }

        $profits = array_reduce($sales, function ($prev, $curr) {
            return $prev + array_reduce($curr->saleDetails, function ($p, $c) {
                return $p + $c->profit_loss;
            });
        });

        $total_transport_cost = array_reduce($sales, function ($prev, $curr) {
            return $prev + $curr->SaleMaster_Freight;
        });

        $total_discount = array_reduce($sales, function ($prev, $curr) {
            return $prev + $curr->SaleMaster_TotalDiscountAmount;
        });

        $total_vat = array_reduce($sales, function ($prev, $curr) {
            return $prev + $curr->SaleMaster_TaxAmount;
        });


        $other_income_expense = $this->db->query("
                select
                (
                    select ifnull(sum(ct.In_Amount), 0)
                    from tbl_cashtransaction ct
                    where ct.branch_id = '$this->branchId'
                    and ct.status = 'a'
                    and DATE_FORMAT(ct.Tr_date, '%m') = '$month'
                    and DATE_FORMAT(ct.Tr_date, '%Y') = '$year'
                ) as income,
            
                (
                    select ifnull(sum(ct.Out_Amount), 0)
                    from tbl_cashtransaction ct
                    where ct.branch_id = '$this->branchId'
                    and ct.status = 'a'
                    and DATE_FORMAT(ct.Tr_date, '%m') = '$month'
                    and DATE_FORMAT(ct.Tr_date, '%Y') = '$year'
                ) as expense,

                (
                    select ifnull(sum(it.amount), 0)
                    from tbl_investment_transactions it
                    where it.branch_id = '$this->branchId'
                    and it.transaction_type = 'Profit'
                    and it.status = 1
                    and DATE_FORMAT(it.transaction_date, '%m') = '$month'
                    and DATE_FORMAT(it.transaction_date, '%Y') = '$year'
                ) as profit_distribute,

                (
                    select ifnull(sum(lt.amount), 0)
                    from tbl_loan_transactions lt
                    where lt.branch_id = '$this->branchId'
                    and lt.transaction_type = 'Interest'
                    and lt.status = 1
                    and DATE_FORMAT(lt.transaction_date, '%m') = '$month'
                    and DATE_FORMAT(lt.transaction_date, '%Y') = '$year'
                ) as loan_interest,

                (
                    select ifnull(sum(a.valuation - a.as_amount), 0)
                    from tbl_assets a
                    where a.branch_id = '$this->branchId'
                    and a.buy_or_sale = 'sale'
                    and a.status = 'a'
                    and DATE_FORMAT(a.as_date, '%m') = '$month'
                    and DATE_FORMAT(a.as_date, '%Y') = '$year'
                ) as assets_sales_profit_loss,
            
                (
                    select ifnull(sum(ep.total_payment_amount), 0)
                    from tbl_employee_payment ep
                    where ep.branch_id = '$this->branchId'
                    and ep.status = 'a'
                    and DATE_FORMAT(ep.payment_date, '%m') = '$month'
                    and DATE_FORMAT(ep.payment_date, '%Y') = '$year'
                ) as employee_payment,

                (
                    select ifnull(sum(dd.damage_amount), 0) 
                    from tbl_damagedetails dd
                    join tbl_damage d on d.Damage_SlNo = dd.Damage_SlNo
                    where d.branch_id = '$this->branchId'
                    and dd.status = 'a'
                    and DATE_FORMAT(d.Damage_Date, '%m') = '$month'
                    and DATE_FORMAT(d.Damage_Date, '%Y') = '$year'
                ) as damaged_amount,

                (
                    select ifnull(sum(rd.SaleReturnDetails_ReturnAmount) - sum(sd.Purchase_Rate * rd.SaleReturnDetails_ReturnQuantity), 0)
                    from tbl_salereturndetails rd
                    join tbl_salereturn r on r.SaleReturn_SlNo = rd.SaleReturn_IdNo
                    join tbl_salesmaster sm on sm.SaleMaster_InvoiceNo = r.SaleMaster_InvoiceNo
                    join tbl_saledetails sd on sd.Product_IDNo = rd.SaleReturnDetailsProduct_SlNo and sd.SaleMaster_IDNo = sm.SaleMaster_SlNo
                    where r.status = 'a'
                    and r.branch_id= '$this->branchId'
                    and DATE_FORMAT(r.SaleReturn_ReturnDate, '%m') = '$month'
                    and DATE_FORMAT(r.SaleReturn_ReturnDate, '%Y') = '$year'
                ) as returned_amount
            ")->row();

        $net_profit = (
            $profits + $total_transport_cost +
            $other_income_expense->income + $total_vat
        ) - (
            $total_discount +
            $other_income_expense->returned_amount +
            $other_income_expense->damaged_amount +
            $other_income_expense->expense +
            $other_income_expense->employee_payment +
            $other_income_expense->profit_distribute +
            $other_income_expense->loan_interest +
            $other_income_expense->assets_sales_profit_loss
        );


        $responseData = [
            'sales_text'        => $sales_text,
            'todays_sale'       => $todaysSale,
            'this_month_sale'   => $thisMonthSale,
            'todays_collection' => $todaysCollection,
            'cash_balance'      => $cashBalance,
            'customer_due'      => $customerDue,
            'supplier_due'      => $supplierDue,
            'bank_balance'      => $bankBalance,
            'invest_balance'    => $investBalance,
            'loan_balance'      => $loanBalance,
            'asset_value'       => $assets_value,
            'stock_value'       => $stockValue,
            'this_month_profit' => $net_profit,
        ];

        echo json_encode($responseData, JSON_NUMERIC_CHECK);
    }

    public function getGraphData()
    {
        $data = json_decode($this->input->raw_input_stream);
        // Monthly Record
        $monthlyRecord = [];
        $year = explode('-', $data->monthYear)[0];
        $month = explode('-', $data->monthYear)[1];
        $dayNumber = date('t', mktime(0, 0, 0, $month, 1, $year));
        for ($i = 1; $i <= $dayNumber; $i++) {
            $date = $year . '-' . $month . '-' . sprintf("%02d", $i);
            $query = $this->db->query("
                    select ifnull(sum(sm.SaleMaster_TotalSaleAmount), 0) as sales_amount 
                    from tbl_salesmaster sm 
                    where sm.SaleMaster_SaleDate = ?
                    and sm.status = 'a'
                    and sm.branch_id = ?
                    group by sm.SaleMaster_SaleDate
                ", [$date, $this->branchId]);

            $amount = 0.00;

            if ($query->num_rows() == 0) {
                $amount = 0.00;
            } else {
                $amount = $query->row()->sales_amount;
            }
            $sale = [sprintf("%02d", $i), $amount];
            array_push($monthlyRecord, $sale);
        }

        $yearlyRecord = [];
        for ($i = 1; $i <= 12; $i++) {
            $yearMonth = $year . sprintf("%02d", $i);
            $query = $this->db->query("
                    select ifnull(sum(sm.SaleMaster_TotalSaleAmount), 0) as sales_amount 
                    from tbl_salesmaster sm 
                    where extract(year_month from sm.SaleMaster_SaleDate) = ?
                    and sm.status = 'a'
                    and sm.branch_id = ?
                    group by extract(year_month from sm.SaleMaster_SaleDate)
                ", [$yearMonth, $this->branchId]);

            $amount = 0.00;
            $monthName = date("M", mktime(0, 0, 0, $i, 10));

            if ($query->num_rows() == 0) {
                $amount = 0.00;
            } else {
                $amount = $query->row()->sales_amount;
            }
            $sale = [$monthName, $amount];
            array_push($yearlyRecord, $sale);
        }

        $responseData = [
            'monthly_record'    => $monthlyRecord,
            'yearly_record'     => $yearlyRecord,
        ];

        echo json_encode($responseData, JSON_NUMERIC_CHECK);
    }
    public function getTopData()
    {
        $data = json_decode($this->input->raw_input_stream);
        $dateY = explode("-", $data->dateFrom)[0];
        $fmonthOfYear = $dateY . "-01-01";
        $lmonthOfYear = $dateY . "-12-01";
        $dateFrom = !empty($data->fromSubmit) ? $data->dateFrom : $fmonthOfYear;
        $dateTo = !empty($data->fromSubmit) ? $data->dateTo : $lmonthOfYear;

        $customerClauses = "and sm.SaleMaster_SaleDate between '$dateFrom' and '$dateTo'";
        $productClauses = "and sm.SaleMaster_SaleDate between '$dateFrom' and '$dateTo'";

        // Top Customers
        $topCustomers = $this->db->query("
                select 
                ifnull(c.Customer_Name, sm.customerName) as customer_name,
                ifnull(sum(sm.SaleMaster_TotalSaleAmount), 0) as amount
                from tbl_salesmaster sm 
                left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
                where sm.branch_id = ?
                $customerClauses
                group by sm.SalseCustomer_IDNo
                order by amount desc 
                limit 10
            ", $this->branchId)->result();

        // Top Products
        $topProducts = $this->db->query("
                select 
                    p.Product_Name as product_name,
                    ifnull(sum(sd.SaleDetails_TotalQuantity), 0) as sold_quantity
                from tbl_saledetails sd
                join tbl_salesmaster sm on sm.SaleMaster_SlNo = sd.SaleMaster_IDNo
                join tbl_product p on p.Product_SlNo = sd.Product_IDNo
                where sd.status = 'a'
                $productClauses
                group by sd.Product_IDNo
                order by sold_quantity desc
                limit 5
            ")->result();


        $responseData = [
            'top_customers'     => $topCustomers,
            'top_products'      => $topProducts,
        ];

        echo json_encode($responseData, JSON_NUMERIC_CHECK);
    }
}
