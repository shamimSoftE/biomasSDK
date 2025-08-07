<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['default_controller'] = 'Page';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['logout'] = 'Login/logout';

$route['Administrator'] = 'Administrator/Page';
$route['module/(:any)'] = 'Administrator/Page/module/$1';
$route['brachAccess/(:any)'] = 'Administrator/Login/brach_access/$1';
$route['getBrachAccess'] = 'Administrator/Login/branch_access_main_admin';

$route['category'] = 'Administrator/Page/add_category';
$route['get_categories'] = 'Administrator/Page/getCategories';
$route['add_category'] = 'Administrator/Page/insert_category';
$route['update_category'] = 'Administrator/Page/update_category';
$route['delete_category'] = 'Administrator/page/catdelete';

$route['brand'] = 'Administrator/Page/add_brand';
$route['get_brands'] = 'Administrator/Page/getBrands';
$route['add_brand'] = 'Administrator/Page/insert_brand';
$route['update_brand'] = 'Administrator/Page/Update_brand';
$route['delete_brand'] = 'Administrator/Page/branddelete';

//delete data list
$route['deleted_data']     = 'Administrator/TrashData/index';
$route['deleted_sale']     = 'Administrator/TrashData/deletedSale';
$route['deleted_purchase'] = 'Administrator/TrashData/deletedPurchase';
$route['deleted_product'] = 'Administrator/TrashData/deletedProduct';
$route['deleted_customer'] = 'Administrator/TrashData/deletedCustomer';
$route['deleted_supplier'] = 'Administrator/TrashData/deletedSupplier';
$route['deleted_customerpayment'] = 'Administrator/TrashData/deletedCustomerPayment';
$route['deleted_supplierpayment'] = 'Administrator/TrashData/deletedSupplierPayment';
$route['deleted_cashtransaction'] = 'Administrator/TrashData/deletedCashtransaction';

$route['restore_product'] = 'Administrator/TrashData/reStoreProduct';
$route['restore_customer'] = 'Administrator/TrashData/reStoreCustomer';
$route['restore_supplier'] = 'Administrator/TrashData/reStoreSupplier';
$route['deleted_sale_invoice/(:any)'] = 'Administrator/TrashData/deletedSaleInvoice/$1';
$route['deleted_purchase_invoice/(:any)'] = 'Administrator/TrashData/deletedPurchaseInvoice/$1';

// Bill=============
$route['BillEntry']                 = 'Administrator/BillController/index';
$route['BillEntry/store']             = 'Administrator/BillController/store';
$route['BillEntry/edit/(:any)']     = 'Administrator/BillController/edit/$1';
$route['BillEntry/update/(:any)']     = 'Administrator/BillController/update/$1';
$route['BillEntry/delete/(:any)']     = 'Administrator/BillController/delete/$1';
$route['BillEntry/search']             = 'Administrator/BillController/search';


// Assets Info===========
$route['AssetsEntry']               = 'Administrator/Assets';
$route['insertassets']              = 'Administrator/Assets/insert_Assets';
$route['assetsEdit/(:any)']         = 'Administrator/Assets/Assets_edit/$1';
$route['assetsUpdate/(:any)']       = 'Administrator/Assets/Update_Assets/$1';
$route['assetsDelete/(:any)']       = 'Administrator/Assets/Assets_delete/$1';
$route['get_assets_cost']           = 'Administrator/Assets/getAssetsCost';
$route['assets_report']             = 'Administrator/Assets/assetsReport';
$route['get_group_assets']          = 'Administrator/Assets/getGroupAssets';
$route['get_assets_report']         = 'Administrator/Assets/getAssetsReport';

// asset name info----------------------------------------------------------
$route['asset_name_entry']        = 'Administrator/Assets/assetNameEntry';
$route['get_asset_name']          = 'Administrator/Assets/getAssetName';
$route['add_asset_name']          = 'Administrator/Assets/addAssetName';
$route['update_asset_name']          = 'Administrator/Assets/updateAssetName';
$route['delete_asset_name']          = 'Administrator/Assets/deleteAssetName';

$route['unit'] = 'Administrator/Page/unit';
$route['add_unit'] = 'Administrator/Page/insert_unit';
$route['update_unit'] = 'Administrator/Page/unitupdate';
$route['delete_unit'] = 'Administrator/Page/unitdelete';
$route['get_units'] = 'Administrator/Page/getUnits';

$route['color'] = 'Administrator/Page/add_color';
$route['insertcolor'] = 'Administrator/Page/insert_color';
$route['colordelete'] = 'Administrator/Page/colordelete';
$route['coloredit/(:any)'] = 'Administrator/Page/coloredit/$1';
$route['colorupdate'] = 'Administrator/Page/colorupdate';

$route['area']        = 'Administrator/Page/area';
$route['add_area']    = 'Administrator/Page/insert_area';
$route['delete_area'] = 'Administrator/Page/areadelete';
$route['update_area'] = 'Administrator/Page/areaupdate';
$route['get_areas']   = 'Administrator/Page/getDistricts';

$route['product']                    = 'Administrator/Products';
$route['product/(:any)']             = 'Administrator/Products/productEdit/$1';
$route['add_product']                = 'Administrator/Products/addProduct';
$route['productEdit']                = 'Administrator/Products/product_edit';
$route['update_product']             = 'Administrator/Products/updateProduct';
$route['delete_product']             = 'Administrator/Products/deleteProduct';
$route['active_product']             = 'Administrator/Products/activeProduct';
$route['productlist']                = 'Administrator/Reports/productlist';
$route['currentStock']               = 'Administrator/Products/current_stock';
$route['productName']                = 'Administrator/Products/product_name';
$route['get_products']               = 'Administrator/Products/getProducts';
$route['get_product_stock']          = 'Administrator/Products/getProductStock';
$route['get_current_stock']          = 'Administrator/Products/getCurrentStock';
$route['get_transter_product_stock'] = 'Administrator/Products/getTransferProductStock';
$route['get_total_stock']            = 'Administrator/Products/getTotalStock';
$route['product_ledger']             = 'Administrator/Products/productLedger';
$route['get_product_ledger']         = 'Administrator/Products/getProductLedger';
$route['reorder_list']               = 'Administrator/Reports/reOrderList';

$route['totalStock'] = 'Administrator/Products/total_stock';
$route['GenerateBarcode/(:any)'] = 'BarcodeController/barcode_create/$1';
$route['multibarcodeStore'] = 'Administrator/Products/multibarcodeStore';
$route['multibarcodePrint'] = 'Administrator/Products/multibarcodePrint';


$route['supplier'] = 'Administrator/Supplier';
$route['supplier/(:any)'] = 'Administrator/Supplier/editSupplier/$1';
$route['add_supplier'] = 'Administrator/Supplier/addSupplier';
$route['supplieredit'] = 'Administrator/Supplier/supplier_edit/';
$route['update_supplier'] = 'Administrator/Supplier/updateSupplier';
$route['supplierList'] = 'Administrator/Reports/supplierList';
$route['delete_supplier'] = 'Administrator/Supplier/deleteSupplier';

$route['customer'] = 'Administrator/Customer';
$route['customer/(:any)'] = 'Administrator/Customer/customeredit/$1';
$route['add_customer'] = 'Administrator/Customer/addCustomer';
$route['customeredit/(:any)'] = 'Administrator/Customer/customeredit/$1';
$route['update_customer'] = 'Administrator/Customer/updateCustomer';
$route['customerlist'] = 'Administrator/Customer/customerlist';
$route['delete_customer'] = 'Administrator/Customer/deleteCustomer';
$route['get_customers'] = 'Administrator/Customer/getCustomers';
$route['get_customer_due'] = 'Administrator/Customer/getCustomerDue';
$route['due_reminder'] = 'Administrator/Customer/due_reminder';
$route['get_customer_due_remainder'] = 'Administrator/Customer/get_customer_due_remainder';  
$route['get_customer_ledger'] = 'Administrator/Customer/getCustomerLedger';
$route['get_customer_payments'] = 'Administrator/Customer/getCustomerPayments';
$route['add_customer_payment'] = 'Administrator/Customer/addCustomerPayment';
$route['update_customer_payment'] = 'Administrator/Customer/updateCustomerPayment';
$route['delete_customer_payment'] = 'Administrator/Customer/deleteCustomerPayment';

$route['customerPaymentPage'] = 'Administrator/Customer/customerPaymentPage';
$route['customerPaymentPage/(:any)'] = 'Administrator/Customer/customerPaymentEdit/$1';
$route['customer_payment_report'] = 'Administrator/Customer/customerPaymentReport';
$route['customer_payment_history'] = 'Administrator/Customer/customerPaymentHistory';

$route['get_purchases'] = 'Administrator/Purchase/getPurchases';
$route['get_purchasedetails'] = 'Administrator/Purchase/getPurchaseDetails';
$route['get_purchasedetails_for_return'] = 'Administrator/Purchase/getPurchaseDetailsForReturn';
$route['add_purchase_return'] = 'Administrator/Purchase/addPurchaseReturn';
$route['update_purchase_return'] = 'Administrator/Purchase/updatePurchaseReturn';
$route['get_purchase_return_details'] = 'Administrator/Purchase/getPurchaseReturnDetails';
$route['purchase'] = 'Administrator/Purchase/order';
$route['purchase/(:any)'] = 'Administrator/Purchase/purchaseEdit/$1';
$route['purchase_invoice_print/(:any)'] = 'Administrator/Purchase/purchaseInvoicePrint/$1';
$route['add_purchase'] = 'Administrator/Purchase/addPurchase';
$route['update_purchase'] = 'Administrator/Purchase/updatePurchase';
$route['purchaseInvoice'] = 'Administrator/Purchase/purchase_bill';
$route['purchaseInvoiceSearch'] = 'Administrator/Purchase/purchase_invoice_search';
$route['purchaseRecord'] = 'Administrator/Purchase/purchase_record';
$route['get_purchase_record'] = 'Administrator/Purchase/getPurchaseRecord';
$route['delete_purchase'] = 'Administrator/Purchase/deletePurchase';
$route['supplierDue'] = 'Administrator/Supplier/supplier_due';
$route['supplierPayment'] = 'Administrator/Supplier/supplierPaymentPage';
$route['searchSupplierDue'] = 'Administrator/Supplier/search_supplier_due';
$route['supplierPaymentReport'] = 'Administrator/Supplier/supplier_payment_report';
$route['searchSupplierPayments'] = 'Administrator/Supplier/search_supplier_payments';
$route['get_suppliers'] = 'Administrator/Supplier/getSuppliers';
$route['get_supplier_due'] = 'Administrator/Supplier/getSupplierDue';
$route['get_supplier_ledger'] = 'Administrator/Supplier/getSupplierLedger';
$route['get_supplier_payments'] = 'Administrator/Supplier/getSupplierPayments';
$route['add_supplier_payment'] = 'Administrator/Supplier/addSupplierPayment';
$route['update_supplier_payment'] = 'Administrator/Supplier/updateSupplierPayment';
$route['delete_supplier_payment'] = 'Administrator/Supplier/deleteSupplierPayment';

$route['change_purchase_status']          = 'Administrator/Purchase/changePurchaseStatus';

$route['purchaseReturns'] = 'Administrator/Purchase/returns';
$route['purchaseReturns/(:any)'] = 'Administrator/Purchase/purchaseReturnEdit/$1';
$route['PurchasereturnSearch'] = 'Administrator/Purchase/PurchasereturnSearch';
$route['PurchaseReturnInsert'] = 'Administrator/Purchase/PurchaseReturnInsert';
$route['returnsList'] = 'Administrator/Purchase/returns_list';
$route['purchaseReturnRecord'] = 'Administrator/Purchase/purchase_return_record';
$route['get_purchase_returns'] = 'Administrator/Purchase/getPurchaseReturns';
$route['purchase_return_invoice/(:any)'] = 'Administrator/Purchase/purchaseReturnInvoice/$1';
$route['delete_purchase_return'] = 'Administrator/Purchase/deletePurchaseReturn';
$route['purchase_return_details'] = 'Administrator/Purchase/purchaseReturnDetails';
$route['check_purchase_return/(:any)'] = 'Administrator/Purchase/checkPurchaseReturn/$1';

$route['damageEntry'] = 'Administrator/Purchase/damage_entry';
$route['add_damage'] = 'Administrator/Purchase/addDamage';
$route['update_damage'] = 'Administrator/Purchase/updateDamage';
$route['delete_damage'] = 'Administrator/Purchase/deleteDamage';
$route['get_damages'] = 'Administrator/Purchase/getDamages';
$route['get_damage'] = 'Administrator/Purchase/getDamage';
$route['damageList'] = 'Administrator/Purchase/damage_product_list';
$route['SelectDamageProduct'] = 'Administrator/Purchase/damage_select_product';

$route['damage_invoice/(:any)'] = 'Administrator/Purchase/damageInvoice/$1';

$route['sales'] = 'Administrator/Sales/index';

$route['sales/(:any)'] = 'Administrator/Sales/salesEdit/$1';
$route['salesinvoice'] = 'Administrator/Sales/sales_invoice';

$route['salesOrderUpdate'] = 'Administrator/Sales/sales_order_update';
$route['productDelete'] = 'Administrator/Sales/product_delete';
$route['productSalesSearch'] = 'Administrator/Sales/product_sales_search';
$route['salesInvoiceSearch'] = 'Administrator/Sales/sales_invoice_search';
$route['add_sales'] = 'Administrator/Sales/addSales';
$route['get_sales'] = 'Administrator/Sales/getSales';
$route['get_sales_record'] = 'Administrator/Sales/getSalesRecord';
$route['get_saledetails'] = 'Administrator/Sales/getSaleDetails';
$route['update_sales'] = 'Administrator/Sales/updateSales';
$route['delete_sales'] = 'Administrator/Sales/deleteSales';
$route['get_saledetails_for_return'] = 'Administrator/Sales/getSaleDetailsForReturn';
$route['add_sales_return'] = 'Administrator/Sales/addSalesReturn';
$route['update_sales_return'] = 'Administrator/Sales/updateSalesReturn';
$route['delete_sale_return'] = 'Administrator/Sales/deleteSaleReturn';
$route['get_sale_returns'] = 'Administrator/Sales/getSaleReturns';
$route['get_sale_return_details'] = 'Administrator/Sales/getSaleReturnDetails';
$route['sale_return_invoice/(:any)'] = 'Administrator/Sales/saleReturnInvoice/$1';
$route['sale_return_details'] = 'Administrator/Sales/saleReturnDetails';
$route['check_sale_return/(:any)'] = 'Administrator/Sales/checkSaleReturn/$1';

$route['sale_invoice_print/(:any)'] = 'Administrator/Sales/saleInvoicePrint/$1';
$route['salesrecord'] = 'Administrator/Sales/sales_record';
$route['customerPaymentReport'] = 'Administrator/Customer/customer_payment_report';
$route['invoiceProductDetails'] = 'Administrator/Sales/invoice_product_list';
$route['invoiceProductList'] = 'Administrator/Sales/invoice_product_list_search';
$route['chalan/(:any)'] = 'Administrator/Sales/chalan/$1';


$route['orders'] = 'Administrator/Sales/orders';
$route['orders/(:any)'] = 'Administrator/Sales/ordersEdit/$1';
$route['ordersrecord'] = 'Administrator/Sales/orders_record';
$route['add_orders'] = 'Administrator/Sales/addOrder';
$route['update_orders'] = 'Administrator/Sales/updateOrder';
$route['order_invoice_print/(:any)'] = 'Administrator/Sales/orderInvoicePrint/$1';
$route['delete_orders'] = 'Administrator/Sales/deleteOrders';
$route['orders_to_sale'] = 'Administrator/Sales/ordersToSale';



//Quotation================
$route['quotation']                = 'Administrator/Quotation';
$route['quotation/(:any)']         = 'Administrator/Quotation/editQuotation/$1';
$route['add_quotation']            = 'Administrator/Quotation/addQuotation';
$route['update_quotation']         = 'Administrator/Quotation/updateQuotation';
$route['delete_quotation']         = 'Administrator/Quotation/deleteQuotation';
$route['quotation_record']         = 'Administrator/Quotation/quotationRecord';
$route['get_quotations']           = 'Administrator/Quotation/getQuotations';
$route['quotationReport']          = 'Administrator/Quotation/quotation_report';
$route['quotation_invoice/(:any)'] = 'Administrator/Quotation/quotationInvoice/$1';
$route['quotation_invoice_report'] = 'Administrator/Quotation/quotationInvoiceReport';
$route['DeleteQuotationInvoice']   = 'Administrator/Quotation/delete_quotation_invoice';


// chalan (false chalan) ==============================================================
$route['chalan_entry']        = 'Administrator/ChalanController';
$route['chalan_entry/(:any)'] = 'Administrator/ChalanController/editChalan/$1';
$route['get_chalans']         = 'Administrator/ChalanController/getChalans';
$route['add_chalan']          = 'Administrator/ChalanController/addChalan';
$route['update_chalan']       = 'Administrator/ChalanController/updateChalan';
$route['delete_chalan']       = 'Administrator/ChalanController/deleteChalan';
$route['chalan_invoice/(:any)'] = 'Administrator/ChalanController/chalanInvoice/$1';
$route['chalan_record']       = 'Administrator/ChalanController/chalanRecord';
// end chalan =========================================================================


$route['salesReturn'] = 'Administrator/Sales/salesreturn';
$route['salesReturn/(:any)'] = 'Administrator/Sales/salesReturnEdit/$1';
$route['salesreturnSearch'] = 'Administrator/Sales/salesreturnSearch';
$route['SalesReturnInsert'] = 'Administrator/Sales/SalesReturnInsert';
$route['returnList'] = 'Administrator/Sales/return_list';
$route['salesReturnRecord'] = 'Administrator/Sales/sales_return_record';

$route['profitLoss'] = 'Administrator/Sales/profitLoss';
$route['profitLossSearch'] = 'Administrator/Sales/profitLossSearch';
$route['get_profit_loss'] = 'Administrator/Sales/getProfitLoss';

$route['customerDue'] = 'Administrator/Customer/customer_due';
$route['paymentAndReport/(:any)'] = 'Administrator/Customer/paymentAndReport/$1';

$route['user'] = 'Administrator/User_management';
$route['get_users'] = 'Administrator/User_management/getUsers';
$route['get_all_users'] = 'Administrator/User_management/getAllUsers';
$route['add_user'] = 'Administrator/User_management/user_Insert';
$route['update_user'] = 'Administrator/User_management/userupdate';
$route['delete_user'] = 'Administrator/User_management/userDelete';
$route['change_user_status'] = 'Administrator/User_management/userstatusChange';
$route['check_username'] = 'Administrator/User_management/check_user_name';
$route['access/(:any)'] = 'Administrator/User_management/user_access/$1';
$route['get_user_access'] = 'Administrator/User_management/getUserAccess';
$route['profile'] = 'Administrator/User_management/profile';
$route['profile_update'] = 'Administrator/User_management/profileUpdate';
$route['define_access/(:any)'] = 'Administrator/User_management/define_access/$1';
$route['add_user_access'] = 'Administrator/User_management/addUserAccess';
$route['user_activity'] = 'Administrator/User_management/userActivity';
$route['get_user_activity'] = 'Administrator/User_management/getUserActivity';
$route['delete_user_activity'] = 'Administrator/User_management/deleteUserActivity';

$route['add_branch'] = 'Administrator/Page/addBranch';
$route['update_branch'] = 'Administrator/Page/updateBranch';
$route['branchEdit'] = 'Administrator/Page/branch_edit';
$route['branchUpdate'] = 'Administrator/Page/branch_update';
$route['branchDelete'] = 'Administrator/Page/branch_delete';
$route['get_branches'] = 'Administrator/Page/getBranches';
$route['get_current_branch'] = 'Administrator/Page/getCurrentBranch';
$route['change_branch_status'] = 'Administrator/Page/changeBranchstatus';

$route['companyProfile'] = 'Administrator/Page/company_profile';
$route['company_profile_Update'] = 'Administrator/Page/company_profile_Update';
$route['company_profile_insert'] = 'Administrator/Page/company_profile_insert';
$route['get_company_profile'] = 'Administrator/Page/getCompanyProfile';

$route['employee'] = 'Administrator/employee';
$route['get_employees'] = 'Administrator/Employee/getEmployees';
$route['employeeInsert'] = 'Administrator/Employee/employee_insert/';
$route['emplists/(:any)'] = 'Administrator/Employee/emplists/$1';
$route['employeeEdit/(:any)'] = 'Administrator/Employee/employee_edit/$1';
$route['employeeUpdate'] = 'Administrator/Employee/employee_Update';
$route['employeeDelete'] = 'Administrator/Employee/employee_Delete';
$route['employeeActive'] = 'Administrator/Employee/active';

//salary Payment
$route['salary_payment']        = 'Administrator/Employee/employeePayment';
$route['check_payment_month']   = 'Administrator/Employee/checkPaymentMonth';
$route['get_payments']          = 'Administrator/Employee/getPayments';
$route['get_salary_details']    = 'Administrator/Employee/getSalaryDetails';
$route['add_salary_payment']    = 'Administrator/Employee/saveSalaryPayment';
$route['update_salary_payment'] = 'Administrator/Employee/updateSalaryPayment';
$route['salary_payment_report'] = 'Administrator/Employee/employeePaymentReport';
$route['delete_payment']        = 'Administrator/Employee/deletePayment';

// salary conveyance------------------------------------------------------
$route['salary_conveyance']     = 'Administrator/Employee/conveyanceSalary';
$route['get_salary_conveyance'] = 'Administrator/Employee/getConveyanceSalary';
$route['add_conveyance_salary'] = 'Administrator/Employee/saveConveyanceSalary';
$route['update_conveyance_salary'] = 'Administrator/Employee/updateConveyanceSalary';
$route['delete_salary_conveyance'] = 'Administrator/Employee/deleteConveyanceSalary';
$route['salary_conveyance_ledger'] = 'Administrator/Employee/employeeConveyanceLedger';
$route['get_conveyance_details'] = 'Administrator/Employee/getConveyanceDetails';
// salary advance------------------------------------------------------
$route['salary_advance']        = 'Administrator/Employee/advanceSalary';
$route['get_salary_advance']    = 'Administrator/Employee/getSalaryAdvance';
$route['add_advance_salary']    = 'Administrator/Employee/addAdvanceSalary';
$route['update_advance_salary'] = 'Administrator/Employee/updateAdvanceSalary';
$route['delete_salary_advance'] = 'Administrator/Employee/deleteAdvanceSalary';

$route['designation'] = 'Administrator/Employee/designation/';
$route['get_designations'] = 'Administrator/Employee/getDesignations';
$route['add_designation'] = 'Administrator/Employee/insert_designation';
$route['update_designation'] = 'Administrator/Employee/designationupdate';
$route['delete_designation'] = 'Administrator/Employee/designationdelete';

$route['depertment'] = 'Administrator/Employee/depertment';
$route['get_departments'] = 'Administrator/Employee/getDepartments';
$route['add_department'] = 'Administrator/Employee/insert_depertment';
$route['update_department'] = 'Administrator/Employee/depertmentupdate';
$route['delete_department'] = 'Administrator/Employee/depertmentdelete';

$route['month'] = 'Administrator/Employee/month';
$route['add_month'] = 'Administrator/Employee/insert_month';
$route['update_month'] = 'Administrator/Employee/updateMonth';
$route['get_months'] = 'Administrator/Employee/getMonths';

$route['get_cash_transactions'] = 'Administrator/Account/getCashTransactions';
$route['cashTransaction'] = 'Administrator/Account/cash_transaction';
$route['cashTransaction/(:any)'] = 'Administrator/Account/cashTransactionEdit/$1';
$route['get_cash_transaction_code'] = 'Administrator/Account/getCashTransactionCode';
$route['add_cash_transaction'] = 'Administrator/Account/addCashTransaction';
$route['update_cash_transaction'] = 'Administrator/Account/updateCashTransaction';
$route['delete_cash_transaction'] = 'Administrator/Account/deleteCashTransaction';
$route['transactionEdit'] = 'Administrator/Account/cash_transaction_edit';
$route['viewTransaction/(:any)'] = 'Administrator/Account/viewTransaction/$1';

$route['account'] = 'Administrator/Account';
$route['add_account'] = 'Administrator/Account/addAccount';
$route['accountEdit'] = 'Administrator/Account/account_edit';
$route['update_account'] = 'Administrator/Account/updateAccount';
$route['delete_account'] = 'Administrator/Account/deleteAccount';
$route['get_accounts'] = 'Administrator/Account/getAccounts';
$route['get_cash_and_bank_balance'] = 'Administrator/Account/getCashAndBankBalance';

$route['sub_account'] = 'Administrator/Account/subAccount';
$route['add_sub_account'] = 'Administrator/Account/addSubAccount';
// $route['accountEdit'] = 'Administrator/Account/account_edit';
$route['sub_account_edit'] = 'Administrator/Account/subAccountEdit';
$route['update_sub_account'] = 'Administrator/Account/updateSubAccount';
$route['delete_sub_account'] = 'Administrator/Account/deleteSubAccount';
$route['get_sub_accounts'] = 'Administrator/Account/getSubAccounts';
// $route['get_cash_and_bank_balance'] = 'Administrator/Account/getCashAndBankBalance';

$route['TransactionReport'] = 'Administrator/Account/all_transaction_report';
$route['bank_transaction_report'] = 'Administrator/Account/bankTransactionReprot';
$route['get_other_income_expense'] = 'Administrator/Account/getOtherIncomeExpense';

$route['cashView'] = 'Administrator/Account/cash_view';
$route['cashView'] = 'Administrator/Account/cash_view';
$route['cashSearch'] = 'Administrator/Account/cash_view';
$route['cash_ledger'] = 'Administrator/Account/cashLedger';
$route['get_cash_ledger'] = 'Administrator/Account/getCashLedger';
$route['cashStatment'] = 'Administrator/Reports/cashStatment';
$route['cashStatmentList'] = 'Administrator/Reports/cashStatmentList';
$route['day_book'] = 'Administrator/Reports/dayBook';

$route['BalanceSheet']          = 'Administrator/Reports/balanceSheet';
$route['balance_sheet']         = 'Administrator/Reports/balance_sheet';
$route['get_balance_sheet']     = 'Administrator/Reports/getBalanceSheet';
$route['balanceSheetList']      = 'Administrator/Reports/balanceSheetList';
$route['balanceSheetListPrint'] = 'Administrator/Reports/balanceSheetListPrint';


$route['price_list']                   = 'Administrator/Reports/price_list';
$route['check/pending/list']           = 'Administrator/Check/check_pendaing_date_list';
$route['check/reminder/list']          = 'Administrator/Check/check_reminder_date_list';
$route['check/dis/list']               = 'Administrator/Check/check_dishonor_date_list';
$route['check/paid/list']              = 'Administrator/Check/check_paid_date_list';
$route['check/list']                   = 'Administrator/Check/check_list';
$route['check/paid/submit/(:any)']     = 'Administrator/Check/check_paid_submission/$1';
$route['check/dishonor/submit/(:any)'] = 'Administrator/Check/check_dishonor_submission/$1';
$route['check/entry']                  = 'Administrator/Check/check_entry_page';
$route['check/store']                  = 'Administrator/Check/check_date_store';
$route['check/view/(:any)']            = 'Administrator/Check/check_view_page/$1';
$route['check/edit/(:any)']            = 'Administrator/Check/check_edit_page/$1';
$route['check/update/(:any)']          = 'Administrator/Check/check_update_info/$1';
$route['check/delete/(:any)']          = 'Administrator/Check/check_delete_info/$1';
// Transfer
$route['product_transfer']        = 'Administrator/Transfer/productTransfer';
$route['product_transfer/(:any)'] = 'Administrator/Transfer/transferEdit/$1';
$route['add_product_transfer']    = 'Administrator/Transfer/addProductTransfer';
$route['update_product_transfer'] = 'Administrator/Transfer/updateProductTransfer';
$route['delete_transfer']         = 'Administrator/Transfer/deleteTransfer';
$route['transfer_list']           = 'Administrator/Transfer/transferList';
$route['get_transfers']           = 'Administrator/Transfer/getTransfers';
$route['get_transfer_details']    = 'Administrator/Transfer/getTransferDetails';
$route['received_list']           = 'Administrator/Transfer/receivedList';
$route['get_receives']            = 'Administrator/Transfer/getReceives';
$route['transfer_invoice/(:any)'] = 'Administrator/Transfer/transferInvoice/$1';
$route['receivedTransfer']        = 'Administrator/Transfer/receivedTransfer';

// Banks
$route['bank_accounts']         = 'Administrator/Account/bankAccounts';
$route['add_bank_account']      = 'Administrator/Account/addBankAccount';
$route['update_bank_account']   = 'Administrator/Account/updateBankAccount';
$route['get_bank_accounts']     = 'Administrator/Account/getBankAccounts';
$route['change_account_status'] = 'Administrator/Account/changeAccountstatus';

// Bank Transactions
$route['bank_transactions']         = 'Administrator/Account/bankTransactions';
$route['bank_transactions/(:any)']  = 'Administrator/Account/bankTransactionEdit/$1';
$route['add_bank_transaction']      = 'Administrator/Account/addBankTransaction';
$route['update_bank_transaction']   = 'Administrator/Account/updateBankTransaction';
$route['get_bank_transactions']     = 'Administrator/Account/getBankTransactions';
$route['get_all_bank_transactions'] = 'Administrator/Account/getAllBankTransactions';
$route['remove_bank_transaction']   = 'Administrator/Account/removeBankTransaction';
$route['get_bank_balance']          = 'Administrator/Account/getBankBalance';

$route['cash_view'] = 'Administrator/Account/cashView';
$route['bank_ledger'] = 'Administrator/Account/bankLedger';

// Graph
$route['graph']            = 'Administrator/Graph/graph';
$route['get_overall_data'] = 'Administrator/Graph/getOverallData';
$route['get_graph_data']   = 'Administrator/Graph/getGraphData';
$route['get_top_data']     = 'Administrator/Graph/getTopData';

// SMS
$route['sms']               = 'Administrator/SMS';
$route['send_sms']          = 'Administrator/SMS/sendSms';
$route['send_bulk_sms']     = 'Administrator/SMS/sendBulkSms';
$route['sms_settings']      = 'Administrator/SMS/smsSettings';
$route['get_sms_settings']  = 'Administrator/SMS/getSmsSettings';
$route['save_sms_settings'] = 'Administrator/SMS/saveSmsSettings';

$route['user_login'] = 'Login/userLogin';
$route['database_backup'] = 'Administrator/Page/databaseBackup';


// Loan
$route['loan_transactions']         = 'Administrator/Loan/loanTransactions';
$route['get_loan_transactions']     = 'Administrator/Loan/getLoanTransactions';
$route['get_loan_initial_balance']  = 'Administrator/Loan/getLoanInitialBalance';
$route['add_loan_transaction']      = 'Administrator/Loan/addLoanTransaction';
$route['update_loan_transaction']   = 'Administrator/Loan/updateLoanTransaction';
$route['remove_loan_transaction']   = 'Administrator/Loan/removeLoanTransaction';
$route['get_loan_balance']          = 'Administrator/Loan/getLoanBalance';
$route['loan_view']                 = 'Administrator/Loan/loanView';
$route['loan_transaction_report']   = 'Administrator/Loan/loanTransactionReprot';
$route['get_all_loan_transactions'] = 'Administrator/Loan/getAllLoanTransactions';
$route['loan_ledger']               = 'Administrator/Loan/loanLedger';

// FDR----------------------------------------------------------------------------
$route['fdr_transactions']         = 'Administrator/FDRController/FDRTransactions';
$route['get_fdr_transactions']         = 'Administrator/FDRController/getFDRTransactions';
$route['add_fdr_transaction']         = 'Administrator/FDRController/addFDRTransactions';
$route['update_fdr_transaction']         = 'Administrator/FDRController/updateFDRTransaction';
$route['remove_fdr_transaction']         = 'Administrator/FDRController/deleteFDRTransaction';
// ------------------------------- fdr account ---------------------------------
$route['fdr_accounts']              = 'Administrator/FDRController/FDRAccounts';
$route['fdr_accounts_view']         = 'Administrator/FDRController/FDRAccountsView';
$route['add_fdr_account']           = 'Administrator/FDRController/addFDRAccount';
$route['update_fdr_account']        = 'Administrator/FDRController/updateFDRAccount';
$route['get_fdr_accounts']          = 'Administrator/FDRController/getFDRAccounts';
$route['change_fdr_account_status'] = 'Administrator/FDRController/changeLoanAccountstatus';

//loan account
$route['loan_accounts'] = 'Administrator/Loan/loanAccounts';
$route['add_loan_account'] = 'Administrator/Loan/addLoanAccount';
$route['update_loan_account'] = 'Administrator/Loan/updateLoanAccount';
$route['get_loan_accounts'] = 'Administrator/Loan/getLoanAccounts';
$route['change_loan_account_status'] = 'Administrator/Loan/changeLoanAccountstatus';

//investment
$route['investment_transactions']         = 'Administrator/Invest/investmentTransactions';
$route['get_investment_transactions']     = 'Administrator/Invest/getInvestmentTransactions';
$route['add_investment_transaction']      = 'Administrator/Invest/addInvestmentTransaction';
$route['update_investment_transaction']   = 'Administrator/Invest/updateInvestmentTransaction';
$route['remove_investment_transaction']   = 'Administrator/Invest/removeInvestmentTransaction';
$route['get_investment_balance']          = 'Administrator/Invest/getInvestmentBalance';
$route['investment_view']                 = 'Administrator/Invest/investmentView';
$route['investment_transaction_report']   = 'Administrator/Invest/investmentTransactionReprot';
$route['get_all_investment_transactions'] = 'Administrator/Invest/getAllInvestmentTransactions';
$route['investment_ledger']               = 'Administrator/Invest/investmentLedger';


//investment account
$route['investment_account']        = 'Administrator/Invest/investmentAccount';
$route['add_investment_account']    = 'Administrator/Invest/addInvestmentAccount';
$route['update_investment_account'] = 'Administrator/Invest/updateInvestmentAccount';
$route['delete_investment_account'] = 'Administrator/Invest/deleteInvestmentAccount';
$route['get_investment_accounts']   = 'Administrator/Invest/getInvestmentAccounts';

////LC Purchase
$route['get_lc_purchases'] = 'Administrator/LcPurchase/getLcPurchases';
$route['lc_purchases'] = 'Administrator/LcPurchase/order';
$route['lc_purchases/(:any)'] = 'Administrator/LcPurchase/purchaseEdit/$1';
$route['lc_purchase_invoice_print/(:any)'] = 'Administrator/LcPurchase/lcPurchaseInvoicePrint/$1';
$route['add_lc_purchase'] = 'Administrator/LcPurchase/addLCPurchase';
$route['update_lc_purchase'] = 'Administrator/LcPurchase/updatePurchase';
$route['get_lc_purchasedetails'] = 'Administrator/LcPurchase/getPurchaseDetails';
$route['lc_purchaseRecord'] = 'Administrator/LcPurchase/lcPurchaseRecord';
$route['get_lc_purchase_record'] = 'Administrator/LcPurchase/getPurchaseRecord';
$route['get_pending_lc_purchase_record'] = 'Administrator/LcPurchase/getPendingPurchaseRecord';
$route['delete_lc_purchase'] = 'Administrator/LcPurchase/deletePurchase';
$route['approve_lc_purchase'] = 'Administrator/LcPurchase/approveLcPurchase';

// CC -----------------------------------------------------------------------
$route['cbm_costing']              = 'Administrator/LcPurchase/cbmCostingEntry';
$route['add_cbm_costing']          = 'Administrator/LcPurchase/addCBMCosting';
$route['update_cbm_costing']       = 'Administrator/LcPurchase/updateCBMCosting';
$route['delete_cbm_costing']       = 'Administrator/LcPurchase/deleteCBMCosting';
$route['get_cbm_costings']         = 'Administrator/LcPurchase/getCBMCostings';
$route['get_lc_costing']          = 'Administrator/LcPurchase/getLcCosting';
$route['get_purchase_products']    = 'Administrator/LcPurchase/getPurchaseProducts';

// DC ---------------------------------------------------------------
$route['duty_costing']        = 'Administrator/LcPurchase/dutyCostingEntry';
$route['add_duty_costing']    = 'Administrator/LcPurchase/addDutyCosting';
$route['update_duty_costing'] = 'Administrator/LcPurchase/updateDutyCosting';
$route['delete_duty_costing'] = 'Administrator/LcPurchase/deleteDutyCosting';
$route['get_duty_costings']   = 'Administrator/LcPurchase/getDutyCostings';
$route['get_duty_costing_product'] = 'Administrator/LcPurchase/getDutyCostingProduct';

$route['costing_invoice']        = 'Administrator/LcPurchase/costingInvoice';
$route['get_costing_details']    = 'Administrator/LcPurchase/getCostingDetails';

// Exp entry................................................
$route['lc_expanse'] = 'Administrator/LcPurchase/lc_expanse';
$route['get_lc_expanses'] = 'Administrator/LcPurchase/get_lc_expanses';
$route['add_lc_expanse'] = 'Administrator/LcPurchase/insert_lc_expanse';
$route['update_lc_expanse'] = 'Administrator/LcPurchase/update_lc_expanse';
$route['delete_lc_expanse'] = 'Administrator/LcPurchase/delete_lc_expanse';

// LC Expense entry...................................................
$route['lc_expanse_entry'] = 'Administrator/LcPurchase/lcExpanseEntry';
$route['get_lc_purchase_expanses'] = 'Administrator/LcPurchase/getLCPurchaseExpanses';
$route['add_expense_lc_purchase'] = 'Administrator/LcPurchase/addLCPurchaseExpanse';
$route['update_expense_lc_purchase'] = 'Administrator/LcPurchase/updateLCPurchaseExpanse';
$route['delete_lc_purchase_expanse'] = 'Administrator/LcPurchase/deleteLCPurchaseExpnese';


//mother api content
$route['get_mother_api_content'] = 'Administrator/Page/getMotherApiContent';
