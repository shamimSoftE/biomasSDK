<style>
	.inline-radio {
		display: inline;
	}

	#branch .Inactive {
		color: red;
	}

	#hideid {
		width: 100px;
		background: white;
		border: 1px solid gray;
		padding: 2px;
	}
	#branch label {
		font-size: 13px;
	}

	#branch select {
		border-radius: 3px;
	}

	#branch .add-button {
		padding: 2.5px;
		width: 100%;
		background-color: #298db4;
		display: block;
		text-align: center;
		color: white;
		cursor: pointer;
		border-radius: 3px;
	}

	#branch .add-button:hover {
		background-color: #41add6;
		color: white;
	}

	#branch input[type="file"] {
		display: none;
	}

	#branch .custom-file-upload {
		border: 1px solid #ccc;
		display: inline-block;
		padding: 2px 35px;
		cursor: pointer;
		margin-top: 5px;
		background-color: #298db4;
		border: none;
		color: white;
	}

	#branch .custom-file-upload:hover {
		background-color: #41add6;
	}

	#branchImage {
		height: 100%;
		width: 100%;
	}
</style>
<div class="row">
	<div class="col-xs-12">
		<div class="col-md-6">
			<fieldset class="scheduler-border scheduler-search">
				<legend class="scheduler-border">Company Profile</legend>
				<div class="control-group">
					<?php if ($selected) { ?>
						<form class="form-vertical" method="post" enctype="multipart/form-data" action="<?php echo base_url(); ?>company_profile_Update">
							<div class="form-group">
								<label class="control-label" for="">Company Logo</label>
								<div class="col-md-3">
									<div class="left">
										<?php if ($selected->Company_Logo_thum != "") { ?>
											<img id="hideid" src="<?php echo base_url() . $selected->Company_Logo_thum; ?>" alt="" style="width:100px">
										<?php } else { ?>
											<img id="hideid" src="<?php echo base_url(); ?>images/No-Image-.jpg" alt="" style="width:200px">
										<?php } ?>
										<img id="preview" src="#" style="width:100px;height:100px" hidden>
									</div>
								</div>
								<div class="col-md-9">
									<input name="companyLogo" style="height: 26px;padding: 2px 0;border-radius:4px;" id="companyLogo" type="file" onchange="readURL(this)" class="form-control" style="height:26px;" />
								</div>
							</div>
							<input name="iidd" type="hidden" id="iidd" value="<?php echo $selected->Company_SlNo; ?>" class="txt" />

							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label" for="form-field-1"> Company Name </label>
										<div>
											<input name="Company_name" type="text" id="Company_name" value="<?php echo $selected->Company_Name; ?>" class="form-control" />
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="form-field-1"> Invoice Header </label>
										<div>
											<input name="InvoiceHeder" type="text" id="InvoiceHeder" value="<?php echo $selected->InvoiceHeder; ?>" class="form-control" />
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="form-field-1"> Invoice Due Area </label>
										<div>
											<select name="dueStatus" class="form-control">
												<option value="true" <?= $selected->dueStatus == 'true' ? 'selected' : '' ?>>With Due</option>
												<option value="false" <?= $selected->dueStatus == 'false' ? 'selected' : '' ?>>Without Due</option>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="form-field-1"> Currency </label>
										<div>
											<input name="Currency_Name" type="text" id="Currency_Name" value="<?php echo $selected->Currency_Name; ?>" class="form-control" />
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="form-field-1"> Sub Currency </label>
										<div>
											<input name="SubCurrency_Name" type="text" id="SubCurrency_Name" value="<?php echo $selected->SubCurrency_Name; ?>" class="form-control" />
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label" for="form-field-1"> Description </label>
										<div>
											<textarea id="Description" name="Description" class="form-control"><?php echo $selected->Repot_Heading; ?></textarea>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<div class="control-group">
										<label class="col-md-12 control-label bolder blue">Invoice Print Type</label>
										<div class="radio inline-radio">
											<label>
												<input name="inpt" id="a4" type="radio" value="1" <?php if ($selected->print_type == 1) {
																										echo "checked";
																									} ?> class="ace" />
												<span class="lbl"> A4 Size</span>
											</label>
										</div>

										<div class="radio inline-radio">
											<label>
												<input name="inpt" id="a42" type="radio" value="2" <?php if ($selected->print_type == 2) {
																										echo "checked";
																									} ?> class="ace" />
												<span class="lbl"> 1/2 of A4 Size</span>
											</label>
										</div>

										<div class="radio inline-radio">
											<label>
												<input name="inpt" id="pos" type="radio" value="3" <?php if ($selected->print_type == 3) {
																										echo "checked";
																									} ?> class="ace" />
												<span class="lbl"> POS </span>
											</label>
										</div>
									</div>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-12 text-right">
									<button type="submit" name="btnSubmit" title="Update" class="btnSave">
										Update
										<i class="ace-icon fa fa-arrow-right"></i>
									</button>

								</div>
							</div>
						</form>
					<?php
					} else {
					?>

						<form method="post" enctype="multipart/form-data" action="<?php echo base_url(); ?>company_profile_insert">
							<div class="form-group">
								<label class="col-md-12 control-label" for="pro_Name">Company Logo</label>
								<div class="col-md-4">
									<img id="hideid" src="<?php echo base_url(); ?>images/No-Image-.jpg" alt="" style="width:100px">
									<img id="preview" src="#" style="width:100px;height:100px" hidden>
								</div>
								<div class="col-md-8">
									<input name="companyLogo" id="companyLogo" type="file" class="form-control" style="height: 26px;padding: 2px 0;" />
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-12 control-label" for="form-field-1" style="margin-top:15px;"> Company Name </label>
								<div class="col-md-12">
									<input name="Company_name" type="text" id="Company_name" value="" class="form-control" />
									<input name="iidd" type="hidden" id="iidd" value="" class="txt" />
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label" for="form-field-1" style="margin-top:15px;"> Description </label>
										<div>
											<textarea id="Description" name="Description" class="form-control"></textarea>
										</div>
									</div>
								</div>
							</div>


							<div class="control-group" style="margin-top:15px;">
								<label class="col-md-12 control-label bolder blue">Invoice Print Type</label>
								<div class="radio inline-radio">
									<label>
										<input name="inpt" id="a4" type="radio" value="1" class="ace" />
										<span class="lbl"> A4 Size</span>
									</label>
								</div>

								<div class="radio inline-radio">
									<label>
										<input name="inpt" id="a42" type="radio" value="2" class="ace" />
										<span class="lbl"> 1/2 of A4 Size</span>
									</label>
								</div>

								<div class="radio inline-radio">
									<label>
										<input name="inpt" id="pos" type="radio" value="3" class="ace" />
										<span class="lbl"> POS </span>
									</label>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-12 text-right">
									<button type="submit" name="btnSubmit" title="Update" class="btnSave">
										Save
										<i class="ace-icon fa fa-arrow-right icon-on-right bigger-110"></i>
									</button>

								</div>
							</div>
						</form>
					<?php
					}
					?>
				</div>
			</fieldset>
		</div>
		<div class="col-md-6">
			<div id="branch">
				<div class="row">
					<fieldset class="scheduler-border scheduler-search">
						<legend class="scheduler-border">Branch Entry Form</legend>
						<div class="control-group">
							<div class="">
								<form class="form-horizontal" @submit.prevent="saveBranch">
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right"> Branch Name : </label>
												<!-- <label class="col-md-1 control-label no-padding-right">:</label> -->
												<div class="col-md-8">
													<input type="text" placeholder="Branch Name" class="form-control" v-model="branch.name" required />
												</div>
											</div>

											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right"> Branch Title : </label>
												<!-- <label class="col-md-1 control-label no-padding-right">:</label> -->
												<div class="col-md-8">
													<input type="text" placeholder="Branch Title" class="form-control" v-model="branch.title" required />
												</div>
											</div>

											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right"> Branch Phone : </label>
												<!-- <label class="col-md-1 control-label no-padding-right">:</label> -->
												<div class="col-md-8">
													<input type="text" placeholder="Branch Phone" class="form-control" v-model="branch.phone" required />
												</div>
											</div>

											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right"> Branch Address : </label>
												<!-- <label class="col-md-1 control-label no-padding-right">:</label> -->
												<div class="col-md-8">
													<textarea class="form-control" placeholder="Branch Address" v-model="branch.address" required></textarea>
												</div>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group clearfix" style="display: flex;align-items:center;flex-direction:column;">
												<div style="width: 140px; height:60px;border: 1px solid #ccc;overflow:hidden;">
													<img id="branchImage" v-if="imageUrl == '' || imageUrl == null" src="/assets/no_image.gif" >
													<img id="branchImage" v-if="imageUrl != '' && imageUrl != null" v-bind:src="imageUrl">
												</div>
												<div style="text-align:center;">
													<label class="custom-file-upload">
														<input type="file" @change="previewImage" />
														Header Image
													</label>
												</div>
											</div>
											<div class="form-group clearfix" style="display: flex;align-items:center;flex-direction:column;">
												<div style="width: 140px; height:60px;border: 1px solid #ccc;overflow:hidden;">
													<img id="branchImage" v-if="imageUrl2 == '' || imageUrl2 == null" src="/assets/no_image.gif" >
													<img id="branchImage" v-if="imageUrl2 != '' && imageUrl2 != null" v-bind:src="imageUrl2">
												</div>
												<div style="text-align:center;">
													<label class="custom-file-upload">
														<input type="file" @change="previewImage2" />
														Footer Image
													</label>
												</div>
											</div>

										</div>
									</div>

									<div class="form-group">
										<label class="col-md-2 control-label no-padding-right"></label>
										<div class="col-md-10" style="margin: 5px 0;">
											<input type="button" class="btnReset" value="Reset" />
											<button type="submit" class="btnSave">
												Submit <i class="ace-icon fa fa-arrow-right"></i>
											</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</fieldset>
				</div>

				<div class="row" style="margin-top: 20px;display:none;" v-bind:style="{display: branches.length > 0 ? '' : 'none'}">
					<div class="col-md-12 no-padding">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Sl</th>
									<th>Branch Name</th>
									<th>Branch Title</th>
									<th>Branch Phone</th>
									<th>Branch Address</th>
									<th>status</th>
									<th style="width: 10%;">Action</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(branch, sl) in branches">
									<td>{{ sl + 1 }}</td>
									<td>{{ branch.Branch_name }}</td>
									<td>{{ branch.Branch_title }}</td>
									<td>{{ branch.Branch_phone }}</td>
									<td>{{ branch.Branch_address }}</td>
									<td><span v-bind:class="branch.active_status">{{ branch.active_status }}</span></td>
									<td>
										<?php if ($this->session->userdata('accountType') != 'u') { ?>
											<i title="Edit Branch" @click.prevent="editBranch(branch)" class="btnEdit fa fa-pencil"></i>&nbsp;
											<i title="Deactive Branch" v-if="branch.status == 'a'" @click.prevent="changestatus(branch.branch_id)" class="btnDelete fa fa-trash"></i></a>
											<i v-else title="Active Branch" @click.prevent="changestatus(branch.branch_id)" class="btnEdit fa fa-check"></i>
										<?php } ?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>

<script>
	new Vue({
		el: '#branch',
		data() {
			return {
				branch: {
					branchId: 0,
					name: '',
					title: '',
					phone: '',
					address: ''
				},
				branches: [],
				imageUrl: '',
				imageUrl2: '',
				selectedFile: null,
				selectedFile2: null,
			}
		},
		created() {
			this.getBranches();
		},
		methods: {
			getBranches() {
				axios.get('/get_branches').then(res => {
					this.branches = res.data;
				})
			},

			saveBranch() {
				let url = "/add_branch";
				if (this.branch.branchId != 0) {
					url = "/update_branch";
				}

				let fd = new FormData();
				fd.append('image', this.selectedFile);
				fd.append('image2', this.selectedFile2);
				fd.append('data', JSON.stringify(this.branch));

				axios.post(url, fd).then(res => {
					let r = res.data;
					alert(r.message);
					if (r.success) {
						this.getBranches();
						this.clearForm();
					}
				})
			},

			editBranch(branch) {
				this.branch.branchId = branch.branch_id;
				this.branch.name = branch.Branch_name;
				this.branch.title = branch.Branch_title;
				this.branch.phone = branch.Branch_phone;
				this.branch.address = branch.Branch_address;

				if (branch.header_image == null || branch.header_image == '') {
					this.imageUrl = null;
				} else {
					this.imageUrl = branch.header_image;
				}

				if (branch.footer_image == null || branch.footer_image == '') {
					this.imageUrl2 = null;
				} else {
					this.imageUrl2 = branch.footer_image;
				}
			},

			changestatus(branchId) {
				let changeConfirm = confirm('Are you sure?');
				if (changeConfirm == false) {
					return;
				}
				axios.post('/change_branch_status', {
					branchId: branchId
				}).then(res => {
					let r = res.data;
					alert(r.message);
					if (r.success) {
						this.getBranches();
					}
				})
			},

			clearForm() {
				this.branch = {
					branchId: 0,
					name: '',
					title: '',
					address: ''
				}
				this.imageUrl = '';
				this.imageUrl2 = '';
				this.selectedFile = null;
				this.selectedFile2 = null;
			},

			previewImage(event) {
				const WIDTH = 722;
				const HEIGHT = 100;
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
			previewImage2(event) {
				const WIDTH = 722;
				const HEIGHT = 100;
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
							this.imageUrl2 = new_img_url;
							const resizedImage = await new Promise(rs => canvas.toBlob(rs, 'image/jpeg', 1))
							this.selectedFile2 = new File([resizedImage], event.target.files[0].name, {
								type: resizedImage.type
							});
						}
					}
				} else {
					event.target.value = '';
				}
			},
		}
	})
</script>

<script type="text/javascript">
	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				document.getElementById('preview').src = e.target.result;
			}
			reader.readAsDataURL(input.files[0]);
			$("#hideid").hide();
			$("#preview").show();
		}
	}
	
</script>