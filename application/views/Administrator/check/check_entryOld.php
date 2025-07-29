<style type="text/css">
  span.select2.select2-container.select2-container--default {
    width: 100% !important;
  }

  .select2-container .select2-selection--single {
    height: 28px;
    border-radius: 4px;
    margin-bottom: 5px;
  }

  .select2-container--default .select2-selection .select2-selection__clear {
    display: none;
  }
  
</style>

<div id="cheque">
  <?php 
  $suppliers = $this->db->select('Supplier_SlNo,Supplier_Code,Supplier_Name')->where('branch_id', $this->branch_id)->where('status', 'a')->get('tbl_supplier')->result();
  ?>
  <fieldset class="scheduler-border">
    <legend class="scheduler-border">Cheque Entry Form</legend>
    <div class="control-group">
      <div class="row">
        <div class="col-md-12">
          <form id="check_form" class="login-form" action="<?= base_url(); ?>check/store" method="POST">
            <div class="row">
              <div class="col-sm-2"></div>

              <div class="col-sm-4">

                <div class="form-group">
                <label class="col-sm-5 control-label no-padding-left" for="type">Type:<span class="text-bold text-danger">*</span> </label>
                  <div class="col-sm-7">
                    <input type="radio" value="customer" onclick="changeItem('customer')" name="type" checked> Customer 
                      <input type="radio" value="supplier" onclick="changeItem('supplier')" name="type"> Supplier 
                  </div>
                </div><br><br>
                <div class="form-group selecedcustomer">
                  <label class="col-sm-5 control-label no-padding-left" for="cus_id">Select Customer:<span class="text-bold text-danger">*</span></label>
                  <div class="col-sm-7">
                    <select class="select2 form-control" id="cus_id" name="cus_id" style="height: 30px; border-radius: 5px;">
                      <option value=" ">Select a Customer</option>
                      <?php if ($customers && isset($customers)) :  foreach ($customers as $customer) : ?>
                          <option value="<?= $customer->Customer_SlNo; ?>"><?= $customer->Customer_Code . ' - ' . ucfirst($customer->Customer_Name); ?></option>
                      <?php endforeach;
                      endif; ?>
                    </select>
                  </div>
                </div>
                <div class="form-group selecedsupplier" style="display: none;">
                  <label class="col-sm-5 control-label no-padding-left" for="sup_id">Select Supplier:<span class="text-bold text-danger">*</span></label>
                  <div class="col-sm-7">
                    <select class="select2 form-control" id="sup_id" name="sup_id" style="height: 30px; border-radius: 5px;">
                      <option value=" ">Select a Supplier</option>
                      <?php if (isset($suppliers) && $suppliers) :  foreach ($suppliers as $supplier) : ?>
                          <option value="<?= $supplier->Supplier_SlNo; ?>"><?= $supplier->Supplier_Code . ' - ' . ucfirst($supplier->Supplier_Name); ?></option>
                      <?php endforeach;
                      endif; ?>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-5 control-label no-padding-left" for="bank_name">Bank Name:<span class="text-bold text-danger">*</span> </label>
                  <div class="col-sm-7">
                    <!-- <input type="text" id="bank_name" name="bank_name" required placeholder="Bank Name" class="form-control" /> -->
                     <v-select v-bind:options="accounts" v-model="employee.selectBank" label="display_name" placeholder="Select account" ></v-select>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-5 control-label no-padding-left" for="branch_name">Branch Name:<span class="text-bold text-danger">*</span> </label>
                  <div class="col-sm-7">
                    <input type="text" id="branch_name" name="branch_name" required placeholder="Branch Name" class="form-control" />
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-5 control-label no-padding-left" for="check_no">Cheque No:<span class="text-bold text-danger">*</span> </label>
                  <div class="col-sm-7">
                    <input type="text" id="check_no" name="check_no" required placeholder="Cheque No" class="form-control" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-5 control-label no-padding-left" for="check_amount">Cheque Amount:<span class="text-bold text-danger">*</span> </label>
                  <div class="col-sm-7">
                    <input type="text" id="check_amount" name="check_amount" required placeholder="Cheque Amount" class="form-control" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-5 control-label no-padding-left" for="check_status">Cheque status:<span class="text-bold text-danger">*</span></label>
                  <div class="col-sm-7">
                    <select class="chosen-select form-control" id="check_status" required name="check_status" style="height: 30px; border-radius: 5px;">
                      <option value="Pe">Pending</option>
                      <option value="Pa">Paid</option>
                    </select>
                  </div>
                </div>

              </div>

              <div class="col-sm-4">
                <div class="form-group">
                  <label class="col-sm-5 control-label no-padding-left" for="date"> Date:<span class="text-bold text-danger">*</span> </label>
                  <div class="col-sm-7">
                    <input class="form-control" style="margin-bottom: 4px;" required id="date" name="date" type="date" value="<?php echo date('Y-m-d'); ?>" data-date-format="yyyy-mm-dd" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-5 control-label no-padding-left" for="check_date">Cheque Date:<span class="text-bold text-danger">*</span> </label>
                  <div class="col-sm-7">
                    <input class="form-control" style="margin-bottom: 4px;" required id="check_date" name="check_date" type="date" value="<?php echo date('Y-m-d'); ?>" data-date-format="yyyy-mm-dd" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-5 control-label no-padding-left" for="remid_date">Reminder Date:<span class="text-bold text-danger">*</span> </label>
                  <div class="col-sm-7">
                    <input class="form-control" style="margin-bottom: 4px;" required id="remid_date" name="remid_date" type="date" value="<?php echo date('Y-m-d'); ?>" data-date-format="yyyy-mm-dd" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-5 control-label no-padding-left" for="sub_date">Submit Date:<span class="text-bold text-danger">*</span> </label>
                  <div class="col-sm-7">
                    <input class="form-control" style="margin-bottom: 4px;" required id="sub_date" name="sub_date" type="date" value="<?php echo date('Y-m-d'); ?>" data-date-format="yyyy-mm-dd" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-5 control-label no-padding-left" for="note">Despription: </label>
                  <div class="col-sm-7">
                    <input type="text" id="note" name="note" class="form-control" placeholder="Despription" />
                  </div>
                </div>

                <div class="form-group" style="margin-top: 10px;">
                  <label class="col-sm-4 control-label no-padding-left" for="ord_budget_range"> </label>
                  <div class="col-sm-8 text-right">
                    <button type="submit" id="check_submit" class="btnSave">Submit</button>
                  </div>
                </div>


              </div>

            </div>
          </form>
        </div>
      </div>
    </div>
  </fieldset>

  <div class="row">
    <div class="col-xs-12">
      <table id="dynamic-table" class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>Cheque Date</th>
            <th>Cheque No</th>
            <th>Bank Name - Branch Name</th>
            <th>Customer/Supplier Name</th>
            <th>Cheque status</th>
            <th>Cheque Amount</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody id="tBody">
          <?php $i = 1;
          if (isset($checks) && $checks) : foreach ($checks as $check) : ?>
              <tr style="<?= ($check->check_status == 'Di') ? 'background-color:#ff000036;' : '' ?>">
                <td><?php
                    $date = new DateTime($check->check_date);
                    echo date_format($date, 'd M Y');
                    ?></td>
                <td><?= $check->check_no; ?></td>

                <td><?= $check->bank_name . '-' . $check->branch_name; ?></td>
                <?php if($check->type == 'supplier'){  ?>
                  <td><?= $check->Supplier_Code . ' - ' . $check->Supplier_Name; ?></td>
                <?php } ?>
                <?php if($check->type == 'customer'){  ?>
                  <td><?= $check->Customer_Code . ' - ' . $check->Customer_Name; ?></td>
                <?php } ?>
                <td>
                  <?php if ($check->check_status == 'Pa') : ?>
                    <span class="badge" style="background: green;">Paid</span>
                  <?php elseif ($check->check_status == 'Di') : ?>
                    <span class="badge" style="background: red;">Dishonor</span>
                  <?php else : ?>
                    <span class="badge" style="background: #ec880a;">Pending</span>
                  <?php endif; ?>
                </td>
                <td><?= number_format($check->check_amount, 2); ?></td>
                <td>
                  <div class="hidden-sm hidden-xs action-buttons">
                    <a class="linka fancybox fancybox.ajax" style="color: #F89406;" href="<?= base_url(); ?>check/view/<?= $check->id; ?>">
                      <i class="ace-icon fa fa-eye bigger-130"></i>
                    </a>
                    <?php if ($this->session->userdata('accountType') != 'u') { ?>
                      <a class="green" href="<?= base_url(); ?>check/edit/<?= $check->id; ?>">
                        <i class="ace-icon fa fa-pencil bigger-130"></i>
                      </a>
                      <a class="red" href="<?= base_url(); ?>check/delete/<?= $check->id ?>" onclick="return confirm('Are You Sure Went to Delete This! ')">
                        <i class="ace-icon fa fa-trash-o bigger-130"></i>
                      </a>
                    <?php } ?>
                  </div>
                </td>


              </tr>
          <?php endforeach;
          endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script type="text/javascript">

    function changeItem(data){
        // var district= $(this).val();
        // alert(data);
        if(data=='supplier'){
          $('.selecedcustomer').hide();
          $('.selecedsupplier').show();
        }
        if(data=='customer'){
          $('.selecedcustomer').show();
          $('.selecedsupplier').hide();
        }
    }
</script>
<?php //$this->load->view('admin/ajax/check_ajax');
?>