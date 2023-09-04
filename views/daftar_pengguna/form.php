<style>
#input_rincian_kegiatan .form-control[disabled]{
	cursor: pointer;
	background-color: transparent;
}
</style>

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

$('.selectpicker').attr('data-live-search', 'true');
$('.selectpicker').selectpicker('refresh');

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
				$('#btnModal_proses').show(); // Set Title to Bootstrap modal title
				$('#btnModal_cancel').text('Close'); // Set Title to Bootstrap modal title
				$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');			
				$(".page-header").html('Daftar Pengguna <div class="btn-group pull-right tombl"></div>');
				
				$(".alert").delay(2000).slideUp(500, function() {					
					reload_table();
					$('#page-wrapper-datatable').show();	
					$(".tombl").html('<button class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" type="button"><i class="fa fa-cogs fa-fw"></i></button><ul class="dropdown-menu slidedown"><li><a href="javascript:void(0)" onclick="add()"><i class="glyphicon glyphicon-plus-sign"></i> Tambah User </a></li><li><a href="javascript:void(0)" onclick="add_sso()"><i class="glyphicon glyphicon-plus-sign"></i> Tambah User Pegawai <sup>sso</sup> </a></li></ul>');
				});
            }
            else
            {
                if(data.inputerror){
					for (var i = 0; i < data.inputerror.length; i++) 
					{
						$('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
						if(data.inputerror[i] == 'status' || data.inputerror[i] == 'id_akses'){
							$('[name="'+data.inputerror[i]+'"]').next().next().text(data.error_string[i]); //select span help-block class set text error string						
						}else{
							$('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
						}
					}
				}else{
					$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');
					$(".alert").delay(2000).slideUp(1000, function() {	
					
					});	
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
			<div class="panel-body" id="panel-body">
				<form action="#" id="form" class="form-horizontal">
					<input type="hidden"  value="" name="id"/> 
					<div class="form-body">
						<div class="form-group">
							<label class="control-label col-md-3">Username</label>
							<div class="col-md-9">
								<input name="username" placeholder="Username" class="form-control" type="text" value="">
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Password</label>
							<div class="col-md-9">
								<input name="password" placeholder="Password" class="form-control" type="password" value="">
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">DINAS</label>
							<div class="col-md-9">
								<?php echo $dinas; ?>
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Status</label>
							<div class="col-md-9">
								<select class="form-control selectpicker show-tick" name="status" id="status" data-live-search="true">
									<option value="">Silahkan Pilih</option>
									<option value="0">TIDAK AKTIF</option>
									<option value="1">AKTIF</option>
								</select>
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Hak Akses</label>
							<div class="col-md-9">
								<?php echo $hak_akses; ?>
								<span class="help-block"></span>
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
