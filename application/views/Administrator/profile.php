<div class="main-content" id="profile">
	<div class="main-content-inner">
		<div class="page-content">
			<form @submit.prevent="saveProfile">
				<div class="row">
					<div class="col-xs-12">
						<div id="home" class="tab-pane in active">
							<div class="row">
								<div class="col-xs-12 col-sm-3 center">
									<span class="profile-picture">
										<img class="editable img-responsive" style="width:120px;" alt="<?php echo $this->session->userdata('FullName'); ?>" id="profileImage" :src="imageUrl" />
									</span>

									<div class="space space-4"></div>

									<label class="btn btn-sm btn-block btn-warning">
										<input type="file" style="display:none;" @change="previewImage($event)" id="profileImageInput" accept="image/x-png,image/gif,image/jpeg">
										<span class="bigger-110">Change Image</span>
									</label>

									<a href="#" class="btn btn-sm btn-block btn-success">
										<i class="ace-icon fa fa-plus-circle bigger-120"></i>
										<span class="bigger-110"><?= ucwords($user->FullName) ?></span>
									</a>

									<a href="#" class="btn btn-sm btn-block btn-primary">
										<i class="ace-icon fa fa-envelope-o bigger-110"></i>
										<span class="bigger-110">Email: <?= $user->UserEmail ?></span>
									</a>
								</div><!-- /.col -->

								<div class="col-xs-12 col-sm-9">

									<div class="profile-user-info">
										<div class="profile-info-row">
											<div class="profile-info-name"> Username </div>

											<div class="profile-info-value">
												<span><?= $user->User_Name ?></span>
											</div>
										</div>
										<div class="profile-info-row">
											<div class="profile-info-name"> Branch Name </div>

											<div class="profile-info-value">
												<span><?= ucwords($branch_info->Branch_name) ?></span>
											</div>
										</div>

										<div class="profile-info-row">
											<div class="profile-info-name">Branch Location </div>

											<div class="profile-info-value">
												<i class="fa fa-map-marker light-orange bigger-110"></i>
												<span><?= ucwords($branch_info->Branch_title) ?></span>
												<span><?= ucwords($branch_info->Branch_address) ?></span>
											</div>
										</div>


										<div class="profile-info-row">
											<div class="profile-info-name"> Age </div>

											<div class="profile-info-value">
												<?php if ($user->UserType == 'a') : ?>
													<span>Admin</span>
												<?php elseif ($user->UserType == 'u') : ?>
													<span>User</span>
												<?php else : ?>
													<span>Member</span>
												<?php endif; ?>
											</div>
										</div>


									</div>

									<div class="hr hr-8 dotted"></div>
									<div class="profile-user-info">
										<div class="profile-info-row">
											<div class="profile-info-name"> Current Password </div>

											<div class="profile-info-value">
												<input type="password" name="current_password" v-model="profile.current_password" class="form-control" placeholder="Current Password" style="width: 30%;">
												<div class="current_password" style="color: red;"></div>
											</div>
										</div>

										<div class="profile-info-row">
											<div class="profile-info-name"> New Password </div>

											<div class="profile-info-value">
												<input type="password" @input="checkPassword" name="password" v-model="profile.password" class="form-control" placeholder="New Password" style="width: 30%;">
											</div>
										</div>
										<div class="profile-info-row">
											<div class="profile-info-name"> Confirm Password </div>
											<div class="profile-info-value">
												<input type="password" @input="checkPassword" name="confirm_password" v-model="profile.confirm_password" class="form-control" placeholder="Confirm Password" style="width: 30%;">
												<div class="error-password"></div>
											</div>
										</div>
										<div class="profile-info-row">
											<div class="profile-info-name"> </div>

											<div class="profile-info-value">
												<button type="submit" class="btn btn-sm btn-info" style="margin-left: 16%;">Update</button>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="space-20"></div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<?php if ($this->session->flashdata('msg')) : ?>
	<script>
		alert('<?= $this->session->flashdata('msg') ?>');
	</script>
<?php endif; ?>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
	new Vue({
		el: '#profile',
		data() {
			return {
				profile: {
					current_password: "",
					password: "",
					confirm_password: ""
				},
				selectedFile: "",
				imageUrl: "<?php echo $this->session->userdata('user_image'); ?>",
				message: "",
			}
		},

		created() {
			this.imageUrl = this.imageUrl == "" ? "/uploads/no_user.png" : this.imageUrl;
		},

		methods: {
			async saveProfile() {
				$(".current_password").text("")
				let formdata = new FormData();
				formdata.append("user_image", this.selectedFile);
				formdata.append("password", this.profile.password)
				formdata.append("current_password", this.profile.current_password);

				await axios.post("/profile_update", formdata)
					.then(res => {
						if (res.data.success) {
							alert(res.data.message);
							location.reload();
						} else {
							$(".current_password").text(res.data.message)
						}
					})
			},

			checkPassword() {
				$(".error-password").text("");
				if (this.profile.password != '' && this.profile.confirm_password != '') {
					if (this.profile.password == this.profile.confirm_password) {
						$(".error-password").text("Both password match").css({
							color: 'green'
						});
					} else {
						$(".error-password").text("Both password not match").css({
							color: 'red'
						});
					}
				} else {
					$(".error-password").text("");
				}
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
		},
	});
</script>