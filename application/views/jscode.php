<div class="container">
	<form name="user" action="" method="post"  enctype="multipart/form-data">
	
		<div class="row pb-4">
			<div class="col-sm-6">	
				
				<?php 
					if($this->session->flashdata('success'))
					{
						echo '<div class="alert alert-success">';
						echo $this->session->flashdata('success');
						echo '</div>';
						$this->session->unset_userdata('success');
					}
					if($this->session->flashdata('error'))
					{
						echo '<div class="alert alert-danger">';
						echo $this->session->flashdata('error');
						echo '</div>';
						$this->session->unset_userdata('error');
					}
				?>
				 <div class="main d-flex">
				 
				 <?php foreach($default as $k=>$kValues){
						$i = $k+1;
					 ?>
					<div class="col-4 border pb-4 columns">
						<div><h4>Slot <?php echo $i; ?></h4></div>
						
						<?php foreach($kValues as $m=>$mValues){ ?>						
							<div><input type="text" name="r<?php echo $i; ?>[]" value="<?php echo $mValues; ?>" class="form-control"></div>
						<?php } ?>
						
					</div>
				 <?php } ?>
					
					<div class="col-4 border pb-4 bg-warning">
						<div><h4>Slot (nth)</h4></div>
						<div><input type="text" value="0" class="form-control numslot"></div>
						<div><input type="text" value="0" class="form-control numslot"></div>
						<div><input type="text" value="0" class="form-control numslot"></div>
					</div>
					
				 
				 </div>				 
			</div>			
		</div>
		
		<div class="row">	
			<div class="col-sm-6 text-center">
				<button type="submit" class="btn btn-success col-sm-4 update">Update</button>
			</div>
			<div class="col-sm-6 text-center">
				<button type="button" class="btn btn-primary col-sm-4 addSlot">Add Column</button>
			</div>
		</div>
		
	</form>	
	
	
</div>