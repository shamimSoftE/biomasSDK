<div class="row">
  <div class="col-xs-12">
    <table id="dynamic-table" class="table table-striped table-bordered table-hover">
      <thead>
        <tr>
          <th>Cheque Date</th>
          <th>Cheque No</th>
          <th>Bank Name - Branch Name</th>
          <th>Customer Name</th>
          <th>Reminder Date</th>
          <th>Cheque Amount</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody id="tBody">
        <?php $i = 1;
        if (isset($checks) && $checks) : foreach ($checks as $check) : ?>
            <tr style="<?= ($check->check_status == 'Di') ? 'background-color:#ff000036;' : '' ?>">
              <td>
                <?php
                $date = new DateTime($check->check_date);
                echo date_format($date, 'd M Y');
                ?>
              </td>
              <td><?= $check->check_no; ?></td>
              <td><?= $check->account_bank_name . '-' . $check->branch_name; ?></td>
              <?php if($check->type == 'supplier') :  ?>
							<td><?= $check->Supplier_Code . ' - ' . $check->Supplier_Name; ?></td>
							<?php endif; ?>
							<?php if($check->type == 'customer') :  ?>
							<td><?= $check->Customer_Code . ' - ' . $check->Customer_Name; ?></td>
							<?php endif; ?>

              <td>
                <?php
                $date = new DateTime($check->remid_date);
                echo date_format($date, 'd M Y');
                ?>
              </td>
              <td><?= number_format($check->check_amount, 2); ?></td>
              <td>
                <div class="hidden-sm hidden-xs action-buttons">
                  <a class="green cheque_submit" id="<?= $check->id; ?>" href="#">
                    <i class="btnEdit fa fa-money" aria-hidden="true"></i>
                  </a>
                  <a class="red" title="Dishonor" href="<?= base_url() ?>check/dishonor/submit/<?= $check->id ?>">
                    <i class="btnDelete fa fa-ban" aria-hidden="true"></i>
                  </a>
                </div>
              </td>


            </tr>
        <?php endforeach;
        endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  $('.cheque_submit').click(function() {
    var cheque_id = $(this).attr('id');

    $.ajax({
      url: '<?= base_url(); ?>check/paid/submit/' + cheque_id,
      type: 'POST',
      dataType: 'json',
      success: function(data) {
        if (data == 1) {
          alert('Cheque Successfully Paid');
          location.reload();
        } else {
          alert('Cheque Not Successfully Paid');
          location.reload();
        }
      }
    });
  });
</script>