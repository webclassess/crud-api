$(document).ready(function () {
	
	/* $('input').keyup(function(e)                                {
	  if (/\D/g.test(this.value))
	  {
		this.value = this.value.replace(/\D/g, '');
	  }
	  
	}); */
	
	
	
	$(document).on('blur', '.numslot', function(){
		
		$('.numslot').val($(this).val());
	});
	
	$(document).on('click', '.addSlot', function(){
		
		var j = $('.numslot').val();
		var i = 0;
		$('.columns').each(function(e){
			i++;
		});
		
		var slot = '';
		for(var k = 0; k< j; k++)
		{
			i = i+1;
			slot += '<div class="col-4 border pb-4 columns"> <div><h4>Slot '+i+'</h4></div> <div><input type="text" name="r'+i+'[]" value="0" class="form-control"></div> <div><input type="text" name="r'+i+'[]" value="0" class="form-control"></div> <div><input type="text" name="r'+i+'[]" value="0" class="form-control"></div> </div>';
		}
		$('.columns').last().after(slot);
		
	});
	
	
	
});

