<div class="row">
  <div class="col-xs-12">
    <table id="dynamic-table" class="table table-striped table-bordered table-hover">
      <thead>
        <tr>
          <th>#</th>
          <th>Check Date</th>
          <th>Chack No</th>
          <th>Bank Name - Branch Name</th>
          <th>Customer/Supplier Name</th>
          <th>Submited Date</th>
          <th>Check Amount</th>
        </tr>
      </thead>

      <tbody id="tBody">
        <?php $i = 1;
        if (isset($checks) && $checks) : foreach ($checks as $check) : ?>
            <tr>
              <td><?= $i++ ?></td>
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
                $date = new DateTime($check->sub_date);
                echo date_format($date, 'd M Y');
                ?>
              </td>
              <td><?= number_format($check->check_amount, 2); ?></td>

            </tr>
        <?php endforeach;
        endif; ?>
      </tbody>
    </table>
  </div>
</div>