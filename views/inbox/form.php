<script>
$("input").change(function(){
	$(this).parent().parent().removeClass('has-error');
	$(this).next().empty();
});
$("textarea").change(function(){
	$(this).parent().parent().removeClass('has-error');
	$(this).next().empty();
});

$("select").change(function(){
	$(this).parent().parent().removeClass('has-error');
	$(this).next().next().empty();
});

$('.datetimePicker #tgl_srt_penyerahan').datetimepicker(
{
	pickTime: false 
}
);

$('.selectpicker').attr('data-live-search', 'true');
$('.selectpicker').selectpicker('refresh');

$('button.form-accordion').click(function(evt){		

	var id = $(this).attr("id");
	var className = $('.'+id+' button i').attr('class');	
	
	if(className=='fa fa-caret-up fa-fw'){				
		jQuery('.'+id+' button i').attr("class","fa fa-caret-down fa-fw");				
	}		
	if(className=='fa fa-caret-down fa-fw'){				
		jQuery('.'+id+' button i').attr("class","fa fa-caret-up fa-fw");		
	}			
});



function reset(){
	
}

function save()
{
    $('#btnSave').text('saving...'); //change button text
    $('#btnSave').attr('disabled',true); //set button disable 
    var url;
	url = "<?php echo base_url('ba/'.$class_name.'/ajax_save')?>";

    // ajax adding data to database
	$("#loading-overlay").show();
    var formData = new FormData($('#form')[0]);
    $.ajax({
        url : url,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "JSON",
        success: function(data)
        {
			$("#loading-overlay").hide();
			$('div.alert').remove();
			$("#message").html(data.notif);
            if(data.status) //if success close modal and reload ajax table
            {				
				$('#page-wrapper-form').html(''); //reset / menghilangkan form menu dalam div 
				$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');			
				$(".page-header").html('Data Penyedia <div class="btn-group pull-right tombl"></div>');
				
				$(".alert").delay(2000).slideUp(500, function() {					
					reload_table();
					$('#page-wrapper-datatable').show();
					$(".tombl").html('<button class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" type="button"><i class="fa fa-cogs fa-fw"></i></button><ul class="dropdown-menu slidedown"><li><a href="javascript:void(0)" onclick="add_penyedia()"><i class="fa fa-folder-o fa-fw"></i> Tambah </a></li></ul>');
				});
            }
            else
            {
				$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');			
				
				$(".alert").delay(2000).slideUp(500, function() {});
				
				for (var i = 0; i < data.inputerror.length; i++) 
                {
                    $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                    if(data.inputerror[i] == 'id_penyedia' || data.inputerror[i] == 'id_spk'){
						$('[name="'+data.inputerror[i]+'"]').next().next().text(data.error_string[i]); //select span help-block class set text error string						
					}else{
						$('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
					}
                }
            }
            $('#btnSave').text('save'); //change button text
            $('#btnSave').attr('disabled',false); //set button enable 


        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error adding / update data');
            $("#loading-overlay").hide();
			$('#btnSave').text('save'); //change button text
            $('#btnSave').attr('disabled',false); //set button enable 

        }
    });
}
</script>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">					
			<div class="panel-body">
				<form action="#" id="form" class="form-horizontal">					
					<input type="hidden"  value="<?=@$data_penyedia->id_pencairan; ?>" name="id"/>
					
					<div class="panel panel-primary">
						<div class="panel-heading" data-toggle="collapse" data-target="#collapseThree">
						  <h4 class="panel-title accordion-toggle">
							  Form Data Penyedia
						  </h4>
						</div>
						<div id="collapseThree" class="panel-collapse in">
						  <div class="panel-body">
							<div class="form-group">
									<label class="control-label col-md-3">Nama Penyedia</label>
									<div class="col-md-9">
										<?php echo $nama_penyedia; ?>
										<span class="help-block"></span>
									</div>
								</div>					
								<div class="form-group">
									<label class="control-label col-md-3">Nama Pekerjaan</label>
									<div class="col-md-9">
										<?php echo $nama_pekerjaan; ?>
										<span class="help-block"></span>
									</div>
								</div>
								<!--
								<div class="form-group">
									<label class="control-label col-md-3">Nama Pekerjaan</label>
									<div class="col-md-9">
										<div class="">	
											<div class="input-group date " id="">
											<span class="input-group-addon"><span class="fa fa-caret-down fa-fw"></span></span>
												<input class="form-control" name="" id="" type="text" placeholder="Pilih Nama Pekerjaan">
											 </div>
										  </div>
										<span class="help-block"></span>
									</div>
								</div>	
								-->
								<div class="form-group">
									<label class="control-label col-md-3">Termin Pekerjaan</label>
									<div class="col-md-9">
										<input name="pekerjaan_termin" placeholder="Termin Pekerjaan" class="form-control" type="text" value="<?php echo @$data_penyedia->pekerjaan_termin; ?>">
										<span class="help-block"></span>
									</div>
								</div>					
								<div class="form-group">
									<label class="control-label col-md-3">Nominal Biaya</label>
									<div class="col-md-9">
										<input name="nominal_bayar" placeholder="Nominal Biaya" class="form-control" type="text" value="<?php echo @$data_penyedia->nominal_bayar; ?>">
										<span class="help-block"></span>
									</div>
								</div>		
						  </div>
						</div>
					</div>
					
				</form>
				<div class="modal-footer">
					<button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
					<!--<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>-->
				</div>
			</div>
		</div>
	</div>
</div>