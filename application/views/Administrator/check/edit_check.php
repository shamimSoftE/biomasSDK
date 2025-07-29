<fieldset class="scheduler-border">
  <legend class="scheduler-border">Cheque Edit Form</legend>
  <div class="control-group">
    <div class="row">
      <div class="col-xs-12">
        <form id="check_form" action="<?= base_url(); ?>check/update/<?= $check->id; ?>" method="POST">
          <div class="row">
            <div class="col-sm-2"></div>

            <div class="col-sm-4">
              
              <div class="form-group">
                <label class="col-sm-5 control-label no-padding-left" for="type">Type:<span class="text-bold text-danger">*</span> </label>
                  <div class="col-sm-7">
                    
                    <input type="radio" value="customer" id="checkboxinfo" <?= ($check->type =='customer') ? 'checked' : '' ?>    onclick="changeItem('customer')" name="type"> Customer 
                      <input type="radio" value="supplier" id="checkboxinfo2" <?= ($check->type =='supplier') ? 'checked' : '' ?>   onclick="changeItem('supplier')" name="type"> Supplier 
                  </div>
                </div><br><br>

              <div class="form-group selecedcustomer">
                <label class="col-sm-5 control-label no-padding-left" for="cus_id">Select Customer:<span class="text-bold text-danger">*</span></label>
                <div class="col-sm-7">
                  <select class="select2 form-control" id="cus_id" name="cus_id" style="height: 30px; border-radius: 5px;">
                    <option value=" ">Select a Customer</option>
                    <?php if ($customers && isset($customers)) :  foreach ($customers as $customer) : ?>
                        <option value="<?= $customer->Customer_SlNo; ?>" <?= ($check->cus_id == $customer->Customer_SlNo) ? 'selected' : '' ?>><?= $customer->Customer_Code . '-' . ucfirst($customer->Customer_Name); ?></option>
                    <?php endforeach;
                    endif; ?>
                  </select>
                </div>
              </div>
              <div class="form-group selecedsupplier">
                <label class="col-sm-5 control-label no-padding-left" for="sup_id">Select Supplier:<span class="text-bold text-danger">*</span></label>
                <div class="col-sm-7">
                <?php 
                  $suppliers = $this->db->select('Supplier_SlNo,Supplier_Code,Supplier_Name')->where('branch_id', $this->branch_id)->where('status', 'a')->get('tbl_supplier')->result();
                  ?>
                  <select class="select2 form-control" id="sup_id" name="sup_id" style="height: 30px; border-radius: 5px;">
                    <option value=" ">Select a Supplier</option>
                    <?php if ($suppliers && isset($suppliers)) :  foreach ($suppliers as $supplier) : ?>
                        <option value="<?= $supplier->Supplier_SlNo; ?>" <?= ($check->sup_id == $supplier->Supplier_SlNo) ? 'selected' : '' ?>><?= $supplier->Supplier_Code . '-' . ucfirst($supplier->Supplier_Name); ?></option>
                    <?php endforeach;
                    endif; ?>
                  </select>
                </div>
              </div>
              <div class="form-group" >
                  <label class="col-sm-5 control-label no-padding-left" for="account_id">Select Bank:<span class="text-bold text-danger">*</span></label>
                  <div class="col-sm-7">
                    <select class="select2 form-control" onchange="bankOnChange()" id="account_id" name="account_id" style="height: 30px; border-radius: 5px;">
                      <option value=" ">Select a Bank</option>
                      <?php if (isset($banks) && $banks) :  foreach ($banks as $bank) : ?>
                          <option value="<?= $bank->account_id; ?>" dataBranch="<?= $bank->branch_name; ?>" <?= ($bank->account_id == $check->account_id) ? 'selected' : '' ?>>
                            <?= $bank->account_number . ' - ' . ucfirst($bank->bank_name) . ' - ' . ucfirst($bank->branch_name); ?>
                          </option>
                      <?php endforeach;
                      endif; ?>
                    </select>
                  </div>
                </div>
              <div class="form-group">
                <label class="col-sm-5 control-label no-padding-left" for="branch_name">Branch Name:<span class="text-bold text-danger">*</span> </label>
                <div class="col-sm-7">
                  <input type="text" id="branch_name" name="branch_name" value="<?= $check->branch_name; ?>" required placeholder="Branch Name" class="form-control" />
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-5 control-label no-padding-left" for="check_no">Cheque No:<span class="text-bold text-danger">*</span> </label>
                <div class="col-sm-7">
                  <input type="text" id="check_no" name="check_no" value="<?= $check->check_no; ?>" required placeholder="Cheque No" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-5 control-label no-padding-left" for="check_amount">Cheque Amount:<span class="text-bold text-danger">*</span> </label>
                <div class="col-sm-7">
                  <input type="text" id="check_amount" name="check_amount" value="<?= $check->check_amount; ?>" required placeholder="Cheque Amount" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-5 control-label no-padding-left" for="check_status">Cheque status:<span class="text-bold text-danger">*</span></label>
                <div class="col-sm-7">
                  <select class="chosen-select form-control" id="check_status" required name="check_status" style="height: 30px; border-radius: 5px;">
                    <option value="Pe" <?= ($check->check_status == 'Pe') ? 'selected' : '' ?>>Pending</option>
                    <option value="Pa" <?= ($check->check_status == 'Pa') ? 'selected' : '' ?>>Paid</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group">
                <label class="col-sm-5 control-label no-padding-left" for="date"> Date:<span class="text-bold text-danger">*</span> </label>
                <div class="col-sm-7">
                  <?php
                  $date = new DateTime($check->date);
                  $e_date =  date_format($date, 'Y-m-d');
                  ?>
                  <input class="form-control date-picker" required id="date" name="date" type="text" value="<?php echo $e_date; ?>" data-date-format="yyyy-mm-dd" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-5 control-label no-padding-left" for="check_date">Cheque Date:<span class="text-bold text-danger">*</span> </label>
                <div class="col-sm-7">
                  <?php
                  $date = new DateTime($check->check_date);
                  $c_date =  date_format($date, 'Y-m-d');
                  ?>
                  <input class="form-control date-picker" required id="check_date" name="check_date" type="text" value="<?php echo $c_date; ?>" data-date-format="yyyy-mm-dd" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-5 control-label no-padding-left" for="remid_date">Reminder Date:<span class="text-bold text-danger">*</span> </label>
                <div class="col-sm-7">
                  <?php
                  $date = new DateTime($check->remid_date);
                  $r_date =  date_format($date, 'Y-m-d');
                  ?>
                  <input class="form-control date-picker" required id="remid_date" name="remid_date" type="text" value="<?php echo $r_date; ?>" data-date-format="yyyy-mm-dd" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-5 control-label no-padding-left" for="sub_date">Submit Date:<span class="text-bold text-danger">*</span> </label>
                <div class="col-sm-7">
                  <?php
                  $date = new DateTime($check->sub_date);
                  $s_date =  date_format($date, 'Y-m-d');
                  ?>
                  <input class="form-control date-picker" required id="sub_date" name="sub_date" type="text" value="<?php echo $s_date; ?>" data-date-format="yyyy-mm-dd" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-5 control-label no-padding-left" for="note">Description: </label>
                <div class="col-sm-7">
                  <input type="text" id="note" name="note" value="<?= $check->note; ?>" class="form-control" placeholder="Description" />
                </div>
              </div>

              <div class="form-group" style="margin-top: 10px;">
                <label class="col-sm-4 control-label no-padding-left" for="ord_budget_range"> </label>
                <div class="col-sm-8 text-right">
                  <button type="submit" id="check_submit" class="btnSave">Update</button>
                </div>
              </div>


            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</fieldset>


<script type="text/javascript">
  $(document).ready(function() {
      // Check if the supplier radio button is checked
      if ($('#checkboxinfo2').is(':checked')) {
          changeItem('supplier');
      } else if ($('#checkboxinfo').is(':checked')) {
          changeItem('customer');
      }
  });

  function bankOnChange(){
    let branchName = $("#account_id option:selected").attr('dataBranch');
    $("#branch_name").val(branchName);
  }
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