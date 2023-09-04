<script>
$('.money').mask('000,000,000,000,000.00', {reverse: true});
$("input").change(function(){
	$(this).parent().parent().removeClass('has-error');
	$(this).next().empty();
});
$("textarea").change(function(){
	$(this).parent().parent().removeClass('has-error');
	$(this).next().empty();
});


$('.datetimePicker #tgl_adendum').datetimepicker(
{
	pickTime: false 
}
);

$('.selectpicker').attr('data-live-search', 'true');
$('.selectpicker').selectpicker('refresh');


function proses_adendum(){
	save_method = 'proses_adendum';
	save();
}

function save_adendum()
{
    $('#btnSave_adendum').text('saving...'); //change button text
    $('#btnSave_adendum').attr('disabled',true); //set button disable 
    var url;
	url = "<?php echo base_url('ba/'.$class_name.'/ajax_save')?>";

    // ajax adding data to database
	$("#loading-overlay").show();
    var formDataAdendum = new FormData($('#form-adendum')[0]);
	formDataAdendum.append('id_pembayaran', '<?php echo $id_pembayaran; ?>');
    $.ajax({
        url : url,
        type: "POST",
        data: formDataAdendum,
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
				$('#form-adendum')[0].reset(); 
				$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');				
				reload_table_adendum();			
				
				$(".alert").delay(2000).slideUp(500, function() {	
					$(".page-header").html('Data Pengajuan Pembayaran <small>Ubah Data</small><div class="btn-group pull-right tombl"></div>');
				});
            }
            else
            {
                for (var i = 0; i < data.inputerror.length; i++) 
                {
                    $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                    $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
                }
            }
            $('#btnSave_adendum').text('Save Adendum'); //change button text
            $('#btnSave_adendum').attr('disabled',false); //set button enable 


        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error adding / update data');
            $("#loading-overlay").hide();
			$('#btnSave_adendum').text('Save Adendum'); //change button text
            $('#btnSave_adendum').attr('disabled',false); //set button enable 

        }
    });
}

$('#datatable-adendum').load('<?=base_url('ba/adendum');?>', {id_pembayaran:'<?php echo $id_pembayaran; ?>'}, function(data, status) 
{
	$("#loading-overlay").hide();
});	
</script>
<div class="row">		
	<div class="panel-body">
		<form action="#" id="form-adendum" class="form-horizontal">
			<input type="hidden"  value="<?=@$data->id_adendum; ?>" name="id"/> 
			
			<div class="panel panel-success">
				<div class="panel-heading" data-toggle="collapse" data-target="#collapse2">
				  <h4 class="panel-title accordion-toggle">
					  Form Adendum
				  </h4>
				</div>
				<div id="collapse2" class="panel-collapse in">
				  <div class="panel-body">						
					<div class="form-group">
						<label class="control-label col-md-3">Nomor Adendum</label>
						<div class="col-md-9">
							<input name="no_adendum" placeholder="Nomor Adendum" class="form-control" type="text" value="<?php echo @$data->no_adendum; ?>">
							<span class="help-block"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-3">Tanggal</label>
						<div class="col-md-9">
							<div class="dateContainer">	
								<div class="input-group date datetimePicker" id="tgl_adendum">
								<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
									<input class="form-control" name="tgl_adendum" id="tgl_adendum" value="<?php if(!empty($data->tgl_adendum)){ echo date('d-m-Y', strtotime($data->tgl_adendum)); }else{ echo date('d-m-Y'); } ?>" placeholder="DD-MM-YYYY" data-date-format="DD-MM-YYYY" type="text">
								 </div>
							  </div>
							<span class="help-block"></span>
						</div>
					</div>	
					<div class="form-group">
						<label class="control-label col-md-3">Biaya Adendum</label>
						<div class="col-md-9">
							<input name="biaya_adendum" placeholder="Biaya Adendum" class="form-control money" type="text" value="<?php echo @$data->biaya_adendum; ?>">
							<span class="help-block"></span>
						</div>
					</div>	
					<div class="form-group">
						<label class="control-label col-md-3">Waktu Pelaksanaan</label>
						<div class="col-md-9">
							<input name="waktu_pelaksanaan_adendum" placeholder="Waktu Pelaksanaan (Adendum)" class="form-control" type="text" value="<?php echo @$data->waktu_pelaksanaan_adendum; ?>">
							<span class="help-block"></span>
						</div>
					</div>											
				  </div>
				</div>
			</div>					
		</form>
		<div class="modal-footer">
			<button type="button" id="btnSave_proses" onclick="proses_adendum()" class="btn btn-primary">Proses</button>
			<button type="button" id="btnSave_adendum" onclick="save_adendum()" class="btn btn-success">Save Adendum</button>
			<!--<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>-->
		</div>
	</div>
</div>
<div id="datatable-adendum">
</div>