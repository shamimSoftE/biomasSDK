<div class="row">
	<div class="col-xs-12">
		<table id="dynamic-table" class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>Cheque Date</th>
					<th>Cheque No</th>
					<th>Bank Name - Branch Name</th>
					<th>Customer Name</th>
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
							<?php if($check->type == 'supplier') :  ?>
							<td><?= $check->Supplier_Code . ' - ' . $check->Supplier_Name; ?></td>
							<?php endif; ?>
							<?php if($check->type == 'customer') :  ?>
							<td><?= $check->Customer_Code . ' - ' . $check->Customer_Name; ?></td>
							<?php endif; ?>
							<td>
								<?php if ($check->check_status == 'Pa') : ?>
									<span class="badge" style="background: green;">Paid</span>
								<?php elseif ($check->check_status == 'Di') : ?>
									<span class="badge" style="background: red;">Dishonour</span>
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