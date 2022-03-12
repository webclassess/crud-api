<div class="container">
	<form name="user" action="<?php echo base_url('update/'.$id); ?>" method="post"  enctype="multipart/form-data">
		<div class="row">	
			<div class="col-sm-6">			
				<div class="form-group row">
					<label for="" class="col-sm-2 col-form-label">Name</label>
					<div class="col-sm-10">
					  <input type="text" name="name" value="<?php echo !empty($userdetails['name'])? $userdetails['name'] : ''; ?>" id="name" class="form-control" required>
					</div>
				</div>
				<div class="form-group row">
					<label for="" class="col-sm-2 col-form-label">Email</label>
					<div class="col-sm-10">
					  <input type="email" name="email" id="email" value="<?php echo !empty($userdetails['email'])? $userdetails['email'] : ''; ?>" class="form-control" required>
					</div>
				</div>

				<div class="form-group row">
					<label for="" class="col-sm-2 col-form-label">Mobile</label>
					<div class="col-sm-10">
					  <input type="text" name="mobile" id="mobile" value="<?php echo !empty($userdetails['mobile'])? $userdetails['mobile'] : ''; ?>" class="form-control" required>
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
					  <input type="password" name="password" id="password" value="<?php echo !empty($userdetails['password'])? $userdetails['password'] : ''; ?>" class="form-control" required>
					</div>
				</div>
				<div class="form-group row">
					<label for="" class="col-sm-2 col-form-label">Image</label>
					<div class="col-sm-10">
					  <input type="file" name="image" id="image" class="form-control">
					  <img src="<?php echo base_url($userdetails['image']); ?>" width="30" height="30">
					</div>
				</div>

				<div class="form-group row">
					<label for="" class="col-sm-2 col-form-label">Date</label>
					<div class="col-sm-10">
					  <input type="text" name="created_date" id="created_date" value="<?php echo !empty($userdetails['created_date'])? $userdetails['created_date'] : ''; ?>" class="form-control" data-date-format="yyyy/mm/dd" placeholder="0000-00-00" required>
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
	
	
</div>