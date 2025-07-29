<?php $this->load->view('Administrator/dashboard_style'); ?>
<style scoped>
    .fa-trash {
        color: black !important;
        border: none !important;
    }

    .section20 {
        position: relative;
    }

    .countingData {
        position: absolute;
        top: 0;
        right: 0;
        font-weight: 900;
        font-size: 15px;
        background: red;
        padding: 0 5px;
        color: white;
        border-radius: 2px;
    }
</style>
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="col-md-2 col-xs-6 ">
            <div class="col-md-12 section20">
                <span class="countingData"><?= count($sales) ?></span>
                <a href="<?php echo base_url(); ?>deleted_sale">
                    <div class="logo">
                        <i class="menu-icon fa fa-trash"></i>
                    </div>
                    <div class="textModule">
                        Sale Invoice
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-2 col-xs-6 ">
            <div class="col-md-12 section20">
                <span class="countingData"><?= count($purchases) ?></span>
                <a href="<?php echo base_url(); ?>deleted_purchase">
                    <div class="logo">
                        <i class="menu-icon fa fa-trash"></i>
                    </div>
                    <div class="textModule">
                        Purchase Invoice
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-2 col-xs-6 ">
            <div class="col-md-12 section20">
                <span class="countingData"><?= count($products) ?></span>
                <a href="<?php echo base_url(); ?>deleted_product">
                    <div class="logo">
                        <i class="menu-icon fa fa-trash"></i>
                    </div>
                    <div class="textModule">
                        Deleted Product
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-2 col-xs-6 ">
            <div class="col-md-12 section20">
                <span class="countingData"><?= count($customers) ?></span>
                <a href="<?php echo base_url(); ?>deleted_customer">
                    <div class="logo">
                        <i class="menu-icon fa fa-trash"></i>
                    </div>
                    <div class="textModule">
                        Deleted Customer
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-2 col-xs-6 ">
            <div class="col-md-12 section20">
                <span class="countingData"><?= count($suppliers) ?></span>
                <a href="<?php echo base_url(); ?>deleted_supplier">
                    <div class="logo">
                        <i class="menu-icon fa fa-trash"></i>
                    </div>
                    <div class="textModule">
                        Deleted Supplier
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-2 col-xs-6 ">
            <div class="col-md-12 section20">
                <span class="countingData"><?= count($customer_payment) ?></span>
                <a href="<?php echo base_url(); ?>deleted_customerpayment">
                    <div class="logo">
                        <i class="menu-icon fa fa-trash"></i>
                    </div>
                    <div class="textModule">
                        Customer Payment
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-2 col-xs-6 ">
            <div class="col-md-12 section20">
                <span class="countingData"><?= count($supplier_payment) ?></span>
                <a href="<?php echo base_url(); ?>deleted_supplierpayment">
                    <div class="logo">
                        <i class="menu-icon fa fa-trash"></i>
                    </div>
                    <div class="textModule">
                        Supplier Payment
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-2 col-xs-6 ">
            <div class="col-md-12 section20">
                <span class="countingData"><?= count($cashtransactions) ?></span>
                <a href="<?php echo base_url(); ?>deleted_cashtransaction">
                    <div class="logo">
                        <i class="menu-icon fa fa-trash"></i>
                    </div>
                    <div class="textModule">
                        Cash Transaction
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>