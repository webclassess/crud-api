<div class="container pb-4">
	<form name="user" action="" method="post"  enctype="multipart/form-data">
		<div class="row">	
			<div class="col-sm-6">			
				<div class="form-group row">
					<label for="" class="col-sm-2 col-form-label">Name</label>
					<div class="col-sm-10">
					  <input type="text" name="name" id="name" class="form-control" required>
					</div>
				</div>
				<div class="form-group row">
					<label for="" class="col-sm-2 col-form-label">Email</label>
					<div class="col-sm-10">
					  <input type="email" name="email" id="email" class="form-control" required>
					</div>
				</div>

				<div class="form-group row">
					<label for="" class="col-sm-2 col-form-label">Mobile</label>
					<div class="col-sm-10">
					  <input type="text" name="mobile" id="mobile" class="form-control" required>
					</div>
				</div>
				
				<div class="form-group row">
					<label for="" class="col-sm-2 col-form-label">Role</label>
					<div class="col-sm-10">
					 <select name="role" id="rolw" class="form-control" required>
						<option value="1"> Admin </option>
						<option value="2"> User </option>
					 </select>
					</div>
				</div>
				  
			</div>
			
			<div class="col-sm-6">			
				  <div class="form-group row">
					<label for="" class="col-sm-2 col-form-label">Password</label>
					<div class="col-sm-10">
					  <input type="password" name="password" id="password" class="form-control" required>
					</div>
				</div>
				<div class="form-group row">
					<label for="" class="col-sm-2 col-form-label">Image</label>
					<div class="col-sm-10">
					  <input type="file" name="image" id="image" class="form-control" required>
					</div>
				</div>

				<div class="form-group row">
					<label for="" class="col-sm-2 col-form-label">Date</label>
					<div class="col-sm-10">
					  <input type="text" name="created_date" id="created_date" class="form-control datepicker" data-date-format="yyyy/mm/dd" placeholder="0000-00-00" required>
					</div>
				</div>			
			</div>
			
		</div>
		
		<div class="row">	
			<div class="col-sm-12 text-center">
				<button type="submit" class="btn btn-primary col-sm-4">Submit</button>
			</div>
		</div>
		
	</form>
	
	<?php if(isset($userdetails) && !empty($userdetails)){?>	
		<hr class="pt-4 pb-4">
		
		<div class="row u-details pb-4">
			<div class="col-lg-12">
				<div class="table-responsive">
				   <table class="table generator_group_tbl_wrapper table-striped custome-sort">
						<thead>
							<tr>
								<th width="auto">ID</th>
								<th width="auto">Name</th>
								<th width="auto">Email</th>
								<th width="auto">Mobile</th>
								<th width="auto">Role</th>
								<th width="auto">Password</th>
								<th width="auto">Image</th>
								<th width="auto">Date</th>
								<th width="auto">Actions</th>				 
						   </tr>
						 </thead>
						 <?php if(count($userdetails) > 0){ foreach($userdetails as $i=>$user) { 
							$role = ($user['role'] == 1)?' Admin ': ' User ';
						 ?>
						 <tr class="">
							 <td scope="row" data-label="ID"><?php echo ($i +1) ; ?></td>
							 <td data-label="Name"><?php echo $user['name']; ?></td>
							 <td data-label="Email"><?php echo $user['email']; ?></td>
							 <td data-label="Mobile"><?php echo $user['mobile']; ?></td>
							 <td data-label="Role"><?php echo $role; ?></td>
							 <td data-label="Password"><?php echo $user['password']; ?></td>
							 <td data-label="Image"><img src="<?php echo $user['image']; ?>" width="30" height="30"></td>
							 <td data-label="Date"><?php echo $user['created_date']; ?></td>
							 <td data-label="Actions"><a href="<?php echo base_url('update/'.$i); ?>" class="btn btn-warning">Edit</a> | <a href="<?php echo base_url('delete/'.$i); ?>" class="btn btn-warning">Delete</td>
						</tr>
						 <?php } } ?>
					</table>
				</div>
			</div>
		</div>
		
		<form name="final" action="" method="post"  enctype="multipart/form-data">
		<div class="row">			
			<div class="col-sm-12 text-center">
				<button type="submit" class="btn btn-success col-sm-4">Final Submit</button>
				<input type="hidden" name="finalDatabase" value="1">
			</div>			
		</div>
		</form>
	<?php }elseif(isset($final_stage)) {?>
			
			<hr class="pt-4 pb-4">
		
			<div class="row u-details pb-4">
				<div class="col-lg-12">
					<?php echo $final_stage; ?>
				</div>
			</div>
	<?php } ?>
	
	
</div>