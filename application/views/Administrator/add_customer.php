<style>
	.v-select {
		margin-bottom: 5px;
		background: #fff;
		border-radius: 3px;
	}

	.v-select.open .dropdown-toggle {
		border-bottom: 1px solid #ccc;
	}

	.v-select .dropdown-toggle {
		padding: 0px;
		height: 25px;
		border: none;
	}

	.v-select input[type=search],
	.v-select input[type=search]:focus {
		margin: 0px;
	}

	.v-select .vs__selected-options {
		overflow: hidden;
		flex-wrap: nowrap;
	}

	.v-select .selected-tag {
		margin: 2px 0px;
		white-space: nowrap;
		position: absolute;
		left: 0px;
	}

	.v-select .vs__actions {
		margin-top: -5px;
	}

	.v-select .dropdown-menu {
		width: auto;
		overflow-y: auto;
	}

	#customers label {
		font-size: 13px;
	}

	#customers select {
		border-radius: 3px;
	}

	#customers .add-button {
		padding: 2.5px;
		width: 100%;
		background-color: #298db4;
		display: block;
		text-align: center;
		color: white;
		cursor: pointer;
		border-radius: 3px;
	}

	#customers .add-button:hover {
		background-color: #41add6;
		color: white;
	}

	#customers input[type="file"] {
		display: none;
	}

	#customers .custom-file-upload {
		border: 1px solid #ccc;
		display: inline-block;
		padding: 5px 12px;
		cursor: pointer;
		margin-top: 5px;
		background-color: #298db4;
		border: none;
		color: white;
	}

	#customers .custom-file-upload:hover {
		background-color: #41add6;
	}

	#customerImage {
		height: 100%;
	}
</style>

<div id="customers">
	<form @submit.prevent="saveCustomer">
		<div class="row" style="margin:0;">
			<fieldset class="scheduler-border">
				<legend class="scheduler-border">Customer Entry Form</legend>
				<div class="control-group">
					<div class="col-md-4 no-padding-right">
						<div class="form-group clearfix">
							<label class="control-label col-md-5">Customer Id:</label>
							<div class="col-md-7">
								<input type="text" class="form-control" v-model="customer.Customer_Code" required readonly>
							</div>
						</div>

						<div class="form-group clearfix">
							<label class="control-label col-md-5">Customer Name:</label>
							<div class="col-md-7">
								<input type="text" class="form-control" v-model="customer.Customer_Name" required>
							</div>
						</div>

						<div class="form-group clearfix">
							<label class="control-label col-md-5">Owner Name:</label>
							<div class="col-md-7">
								<input type="text" class="form-control" v-model="customer.owner_name">
							</div>
						</div>

						<div class="form-group clearfix">
							<label class="control-label col-md-5">Address:</label>
							<div class="col-md-7">
								<input type="text" class="form-control" v-model="customer.Customer_Address">
							</div>
						</div>

						<div class="form-group clearfix">
							<label class="control-label col-md-5">Area:</label>
							<div class="col-md-7" style="display: flex;align-items:center;margin-bottom:5px;">
								<div style="width: 86%;">
									<v-select v-bind:options="districts" style="margin:0;" v-model="selectedDistrict" label="District_Name"></v-select>
								</div>
								<div style="width:13%;margin-left:2px;">
									<span class="add-button" @click.prevent="modalOpen('/add_area', 'Add Area', 'District_Name')"><i class="fa fa-plus"></i></span>
								</div>
							</div>
						</div>
						<div class="form-group clearfix">
							<label class="control-label col-md-5">Employee:</label>
							<div class="col-md-7" style="display: flex;align-items:center;margin-bottom:5px;">
								<div style="width: 86%;">
									<v-select v-bind:options="employees" v-model="selectedEmployee" label="display_name" placeholder="Select Employee"></v-select>
								</div>
								<div style="width:13%;margin-left:2px;">
								<a href="<?= base_url('employee') ?>" class="add-button" target="_blank" title="Add New Product"><i class="fa fa-plus" aria-hidden="true"></i></a>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-5 no-padding-right">
						<div class="form-group clearfix">
							<label class="control-label col-md-5">Mobile:</label>
							<div class="col-md-7">
								<input type="text" class="form-control" v-model="customer.Customer_Mobile" required>
							</div>
						</div>

						<div class="form-group clearfix">
							<label class="control-label col-md-5">Office Phone:</label>
							<div class="col-md-7">
								<input type="text" class="form-control" v-model="customer.Customer_OfficePhone">
							</div>
						</div>

						<div class="form-group clearfix">
							<label class="control-label col-md-5">Previous Due:</label>
							<div class="col-md-7">
								<input type="number" class="form-control" v-model="customer.previous_due" required>
							</div>
						</div>
						<div class="form-group clearfix">
							<label class="control-label col-md-5">Due Remiander Date:</label>
							<div class="col-md-7">
								<select  v-model="customer.Customer_remainder_day" class="form-control">
									<option value="">Select Day</option>
									<option value="01">01 (One)</option>
									<option value="02">02 (Two)</option>
									<option value="03">03 (Three)</option>
									<option value="04">04 (Four)</option>
									<option value="05">05 (Five)</option>
									<option value="06">06 (Six)</option>
									<option value="07">07 (Seven)</option>
									<option value="08">08 (Eight)</option>
									<option value="09">09 (Nine)</option>
									<option value="10">10 (Ten)</option>
									<option value="11">11 (Eleven)</option>
									<option value="12">12 (Twelve)</option>
									<option value="13">13 (Thirteen)</option>
									<option value="14">14 (Fourteen)</option>
									<option value="15">15 (Fifteen)</option>
									<option value="16">16 (Sixteen)</option>
									<option value="17">17 (Seventeen)</option>
									<option value="18">18 (Eighteen)</option>
									<option value="19">19 (Nineteen)</option>
									<option value="20">20 (Twenty)</option>
									<option value="21">21 (Twenty-one)</option>
									<option value="22">22 (Twenty-two)</option>
									<option value="23">23 (Twenty-three)</option>
									<option value="24">24 (Twenty-four)</option>
									<option value="25">25 (Twenty-five)</option>
									<option value="26">26 (Twenty-six)</option>
									<option value="27">27 (Twenty-seven)</option>
									<option value="28">28 (Twenty-eight)</option>
									<option value="29">29 (Twenty-nine)</option>
									<option value="30">30 (Thirty)</option>
									<option value="31">31 (Thirty-One)</option>
								</select>
							</div>
						</div>

						<div class="form-group clearfix">
							<label class="control-label col-md-5">Credit Limit:</label>
							<div class="col-md-7">
								<input type="number" class="form-control" v-model="customer.Customer_Credit_Limit" required>
							</div>
						</div>

						<div class="form-group clearfix">
							<label class="control-label col-md-5">Customer Type:</label>
							<div class="col-md-7">
								<input type="radio" name="customerType" value="retail" v-model="customer.Customer_Type"> Retail
								<input type="radio" name="customerType" value="wholesale" v-model="customer.Customer_Type"> Wholesale
							</div>
						</div>

						<div class="form-group clearfix">
							<div class="col-md-7 col-md-offset-4 text-right">
								<input type="button" @click="resetForm" class="btnReset" value="Reset">
								<input type="submit" class="btnSave" value="Save">
							</div>
						</div>
					</div>
					<div class="col-md-3 text-center;">
						<div class="form-group clearfix" style="display: flex;align-items:center;flex-direction:column;">
							<div style="width: 100px;height:100px;border: 1px solid #ccc;overflow:hidden;">
								<img id="customerImage" v-if="imageUrl == '' || imageUrl == null" src="/assets/no_image.gif">
								<img id="customerImage" v-if="imageUrl != '' && imageUrl != null" v-bind:src="imageUrl">
							</div>
							<div style="text-align:center;">
								<label class="custom-file-upload">
									<input type="file" @change="previewImage" />
									Select Image
								</label>
							</div>
						</div>
					</div>
				</div>
			</fieldset>
		</div>
	</form>

	<div class="row">
		<div class="col-sm-12 form-inline">
			<div class="form-group">
				<label for="filter" class="sr-only">Filter</label>
				<input type="text" class="form-control" v-model="filter" placeholder="Filter">
			</div>
		</div>
		<div class="col-md-12">
			<div class="table-responsive">
				<datatable :columns="columns" :data="customers" :filter-by="filter" style="margin-bottom: 5px;">
					<template scope="{ row }">
						<tr>
							<td>{{ row.AddTime | dateOnly('DD-MM-YYYY') }}</td>
							<td>{{ row.Customer_Code }}</td>
							<td>{{ row.Customer_Name }}</td>
							<td>{{ row.owner_name }}</td>
							<td>{{ row.District_Name }}</td>
							<td>{{ row.Customer_Mobile }}</td>
							<td>{{ row.Customer_Type }}</td>
							<td>{{ row.Customer_Credit_Limit }}</td>
							<td>
								<?php if ($this->session->userdata('accountType') != 'u') { ?>
									<i class="btnEdit fa fa-pencil" @click="editCus(row)"></i>
									<i class="btnDelete fa fa-trash" @click="deleteCustomer(row.Customer_SlNo)"></i>
								<?php } ?>
							</td>
						</tr>
					</template>
				</datatable>
				<datatable-pager v-model="page" type="abbreviated" :per-page="per_page" style="margin-bottom: 50px;"></datatable-pager>
			</div>
		</div>
	</div>

	<!-- modal form -->
	<div class="modal formModal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-sm" role="document">
			<form @submit.prevent="saveModalData($event)">
				<div class="modal-content">
					<div class="modal-header" style="display: flex;align-items: center;justify-content: space-between;">
						<h5 class="modal-title" v-html="modalTitle"></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" style="padding-top: 0;">
						<div class="form-group">
							<label for="">Name</label>
							<input type="text" :name="formInput" v-model="fieldValue" class="form-control" autocomplete="off" />
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btnReset" data-dismiss="modal">Close</button>
						<button type="submit" class="btnSave">Save</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#customers',
		data() {
			return {
				customer: {
					Customer_SlNo         : "<?php echo $customerId; ?>",
					Customer_Code         : '<?php echo $customerCode; ?>',
					Customer_Name         : '',
					Customer_Type         : 'retail',
					Customer_Phone        : '',
					Customer_Mobile       : '',
					Customer_Email        : '',
					Customer_remainder_day: '',
					Customer_OfficePhone  : '',
					Customer_Address      : '',
					owner_name            : '',
					employee_id           : '',
					area_ID               : '',
					Customer_Credit_Limit : 0,
					previous_due          : 0
				},
				customers: [],
				districts: [],
				selectedDistrict: null,
				employees: [],
				selectedEmployee: null,
				imageUrl: '',
				selectedFile: null,

				columns: [{
						label: 'Added Date',
						field: 'AddTime',
						align: 'center',
						filterable: false
					},
					{
						label: 'Customer Id',
						field: 'Customer_Code',
						align: 'center',
						filterable: false
					},
					{
						label: 'Customer Name',
						field: 'Customer_Name',
						align: 'center'
					},
					{
						label: 'Owner Name',
						field: 'owner_name',
						align: 'center'
					},
					{
						label: 'Area',
						field: 'District_Name',
						align: 'center'
					},
					{
						label: 'Contact Number',
						field: 'Customer_Mobile',
						align: 'center'
					},
					{
						label: 'Customer Type',
						field: 'Customer_Type',
						align: 'center'
					},
					{
						label: 'Credit Limit',
						field: 'Customer_Credit_Limit',
						align: 'center'
					},
					{
						label: 'Action',
						align: 'center',
						filterable: false
					}
				],

				page: 1,
				per_page: 100,
				filter: '',

				formInput: '',
				url: '',
				modalTitle: '',
				fieldValue: ''
			}
		},
		filters: {
			dateOnly(datetime, format) {
				return moment(datetime).format(format);
			}
		},
		created() {
			this.getDistricts();
			this.getCustomers();
			this.getEmployees();
			if (this.customer.Customer_SlNo != 0) {
				this.editCustomer(this.customer.Customer_SlNo);
			}
		},
		methods: {
			getDistricts() {
				axios.get('/get_areas').then(res => {
					this.districts = res.data;
				})
			},
			getEmployees() {
				axios.get('/get_employees').then(res => {
					this.employees = res.data;
				})
			},
			getCustomers() {
				axios.get('/get_customers').then(res => {
					this.customers = res.data;
				})
			},
			saveCustomer() {
				if (this.selectedDistrict == null) {
					Swal.fire({
						icon: "error",
						text: "Area name is empty!",
					});
					return;
				}
				if (this.selectedEmployee == null) {
					Swal.fire({
						icon: "error",
						text: "Employee name is empty!",
					});
					return;
				}
				if (this.customer.Customer_Name == '') {
					Swal.fire({
						icon: "error",
						text: "Customer name is empty!",
					});
					return;
				}

				this.customer.area_ID     = this.selectedDistrict.District_SlNo;
				this.customer.employee_id = this.selectedEmployee.Employee_SlNo;

				let url = '/add_customer';
				if (this.customer.Customer_SlNo != 0) {
					url = '/update_customer';
				}

				let fd = new FormData();
				fd.append('image', this.selectedFile);
				fd.append('data', JSON.stringify(this.customer));

				axios.post(url, fd, {
					onUploadProgress: upe => {
						let progress = Math.round(upe.loaded / upe.total * 100);
					}
				}).then(res => {
					let r = res.data;
					alert(r.message);
					if (r.success) {
						this.resetForm();
						this.customer.Customer_Code = r.customerCode;
						this.getCustomers();
					}
				})
			},

			editCus(row){
				window.open('/customer/' + row.Customer_SlNo, '_blank');
			},

			editCustomer(id) {
				axios.post('/get_customers', {customerId:id}).then(res => {
					let customer = res.data[0];
				
					let keys = Object.keys(this.customer);
					keys.forEach(key => {
						this.customer[key] = customer[key];
					})
	
					this.selectedDistrict = {
						District_SlNo: customer.area_ID,
						District_Name: customer.District_Name
					}
					this.selectedEmployee = {
						Employee_SlNo: customer.employee_id,
						display_name: customer.display_text
					}
	
					if (customer.image_name == null || customer.image_name == '') {
						this.imageUrl = null;
					} else {
						this.imageUrl = customer.image_name;
					}
				})

			},
			deleteCustomer(customerId) {
				let deleteConfirm = confirm('Are you sure?');
				if (deleteConfirm == false) {
					return;
				}
				axios.post('/delete_customer', {
					customerId: customerId
				}).then(res => {
					let r = res.data;
					alert(r.message);
					if (r.success) {
						this.getCustomers();
					}
				})
			},
			resetForm() {
				let keys = Object.keys(this.customer);
				keys = keys.filter(key => key != "Customer_Type");
				keys.forEach(key => {
					if (typeof(this.customer[key]) == 'string') {
						this.customer[key] = '';
					} else if (typeof(this.customer[key]) == 'number') {
						this.customer[key] = 0;
					}
				})
				this.imageUrl = '';
				this.selectedFile = null;
				this.selectedEmployee = null;
			},
			previewImage(event) {
				const WIDTH = 150;
				const HEIGHT = 150;
				if (event.target.files[0]) {
					let reader = new FileReader();
					reader.readAsDataURL(event.target.files[0]);
					reader.onload = (ev) => {
						let img = new Image();
						img.src = ev.target.result;
						img.onload = async e => {
							let canvas = document.createElement('canvas');
							canvas.width = WIDTH;
							canvas.height = HEIGHT;
							const context = canvas.getContext("2d");
							context.drawImage(img, 0, 0, canvas.width, canvas.height);
							let new_img_url = context.canvas.toDataURL(event.target.files[0].type);
							this.imageUrl = new_img_url;
							const resizedImage = await new Promise(rs => canvas.toBlob(rs, 'image/jpeg', 1))
							this.selectedFile = new File([resizedImage], event.target.files[0].name, {
								type: resizedImage.type
							});
						}
					}
				} else {
					event.target.value = '';
				}
			},

			// modal data store
			modalOpen(url, title, txt) {
				$(".formModal").modal("show");
				this.formInput = txt;
				this.url = url;
				this.modalTitle = title;
			},

			saveModalData(event) {
				let filter = {}
				if (this.formInput == "District_Name") {
					filter.District_Name = this.fieldValue;
				}

				axios.post(this.url, filter)
					.then(res => {
						if (this.formInput == "District_Name") {
							this.getDistricts();
						}

						$(".formModal").modal('hide');
						this.formInput = '';
						this.url = "";
						this.modalTitle = '';
						this.fieldValue = '';
					})
			},
		}
	})
</script>