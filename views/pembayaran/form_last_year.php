<script src="<?php echo base_url('assets/js/bootstrap3-typeahead.js')?>"></script>
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

$("select").change(function(){
	$(this).parent().parent().removeClass('has-error');
	$(this).next().next().empty();
});

$('.datetimePicker #tgl_srt_penyerahan, #tanggal_bas, #pjbtpenerima_tglsk, #tgl_bast').datetimepicker(
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


//untuk typeahead
$('.typeahead').typeahead({
	/* source:  function (query, process) {
		return $.post('hasil_pekerjaan/ajax_cari_data', { query: query }, function (data) {
			data = $.parseJSON(data);
			return process(data);
		});
	},
    updater: function(item) {
		$('#id_pegawai_pphp').val(item.id_pegawai_pphp);
		$('[name="pjbtpenerima_tglsk"]').val(item.pjbtpenerima_tglsk);
		$('.selectpicker').attr('data-live-search', 'true');
		$('.selectpicker').selectpicker('refresh');
        return item;
    } */
	
});

$('#id_pegawai_pphp').change(function(){

	var ListBoxObject = document.getElementById("id_pegawai_pphp");
	if (ListBoxObject.options[0].selected)
	{
		ListBoxObject.options[0].selected = false;
		$('.selectpicker').attr('data-live-search', 'true');
		$('.selectpicker').selectpicker('refresh');
	}
});

$(document).on('mousedown', 'ul.typeahead', function(e) {
    e.preventDefault();
});
//untuk typeahead

function save()
{
    $('#btnSave').text('saving...'); //change button text
    $('#btnSave').attr('disabled',true); //set button disable 
    var url;
	url = "<?php echo base_url('ba/'.$class_name.'/ajax_save')?>";

    // ajax adding data to database
	$("#loading-overlay").show();
    var formData = new FormData($('#form')[0]);
	formData.append('id', id_hasil_pekerjaan);
	formData.append('id_pencairan', id_pencairan);
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
				id_hasil_pekerjaan = data.id_hasil_pekerjaan; 
				// $('#page-wrapper-form').html(''); //reset / menghilangkan form menu dalam div 
				$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');			
				// $(".page-header").html('Data Pencairan <div class="btn-group pull-right tombl"></div>');
				$('a[href="#data-uraian-pekerjaan"]').trigger('click');
				$(".alert").delay(2000).slideUp(500, function() {					
					// reload_table();
					// $('#page-wrapper-datatable').show();
					// $(".tombl").html('<button class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" type="button"><i class="fa fa-cogs fa-fw"></i></button><ul class="dropdown-menu slidedown"><li><a href="javascript:void(0)" onclick="add_penyedia()"><i class="fa fa-folder-o fa-fw"></i> Tambah </a></li></ul>');
				});
            }
            else
            {
                if(data.inputerror){
					for (var i = 0; i < data.inputerror.length; i++) 
					{					
						$('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
						if(data.inputerror[i] == 'termin' || data.inputerror[i] == 'id_pegawai_ppk' || data.inputerror[i] == 'id_pegawai_pptk' || data.inputerror[i] == 'id_pegawai_bendahara' || data.inputerror[i] == 'id_pegawai_pengguna_anggaran'){
							$('[name="'+data.inputerror[i]+'"]').next().next().text(data.error_string[i]); //select span help-block class set text error string						
						}else if(data.inputerror[i] == 'id_pegawai_pphp'){
							alert('PPHP Harus ditentukan');
						}else{
							$('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
						}
					}					
				}else{
					$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');	
					$(".alert").delay(2000).slideUp(500, function() {					
					});
				}
            }
            $('#btnSave').text('Lanjutkan'); //change button text
            $('#btnSave').attr('disabled',false); //set button enable 


        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error adding / update data');
            $("#loading-overlay").hide();
			$('#btnSave').text('Lanjutkan'); //change button text
            $('#btnSave').attr('disabled',false); //set button enable 

        }
    });
}

$('a[href="#data-adendum"]').click(function(){	
	$('#data-adendum').load('<?=base_url('ba/adendum/ajax_form');?>', {id_pembayaran:'<?php echo $id_pembayaran; ?>'}, function(data, status) 
	{
		$("#loading-overlay").hide();
	});	
});
</script>
<div class="row">
	<div class="col-lg-12">
	
		<div class="panel panel-default">					
			<div class="panel-body">
			
				<ul class="nav nav-tabs">
					<li class="active"><a href="#utama" data-toggle="tab"><span class="glyphicon glyphicon-file"></span> Data Utama</a>
					</li>
					<li><a href="#data-adendum" data-toggle="tab"><span class="glyphicon glyphicon-list"></span> Data Adendum</a>
					</li>
				</ul>

				<!-- Tab panes -->
				<div class="tab-content">
					<div class="tab-pane fade in active" id="utama">
						<br>
						<form action="#" id="form" class="form-horizontal">					
							<!-- 
							<input type="hidden"  value="<?=@$data->id_hasil_pekerjaan; ?>" name="id"/>
							<input type="hidden"  value="<?=@$data->id_pencairan; ?>" name="id_pencairan"/>
							-->
							
							<div class="panel panel-primary">
								<div class="panel-heading" data-toggle="collapse" data-target="#collapse1">
								  <h4 class="panel-title accordion-toggle">
									  Form Utama
								  </h4>
								</div>
								<div id="collapse1" class="panel-collapse in">
								  <div class="panel-body">						
									<div class="form-group">
										<label class="control-label col-md-3">Waktu Pelaksanaan</label>
										<div class="col-md-9">
											<input name="waktu_pelaksanaan" placeholder="Waktu Pelaksanaan" class="form-control" type="text" value="<?php echo @$data->waktu_pelaksanaan; ?>">
											<span class="help-block"></span>
										</div>
									</div>					
									<div class="form-group">
										<label class="control-label col-md-3">Pembayaran (%) </label>
										<div class="col-md-9">
											<input name="bayar_persen" placeholder="Pembayaran Sebesar .....% " class="form-control money" type="text" value="<?php echo @$data->bayar_persen; ?>">
											<span class="help-block"></span>
										</div>
									</div>				
									<div class="form-group">
										<label class="control-label col-md-3">Pembayaran uang muka</label>
										<div class="col-md-9">
											<input name="uang_muka" placeholder="Pembayaran uang muka" class="form-control money" type="text" value="<?php echo @$data->uang_muka; ?>">
											<span class="help-block"></span>
										</div>
									</div>		
									<div class="form-group">
										<label class="control-label col-md-3">Retensi 5%</label>
										<div class="col-md-9">
											<input name="retensi" placeholder="Retensi 5%" class="form-control money" type="text" value="<?php echo @$data->retensi; ?>">
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-3">Lain-lain</label>
										<div class="col-md-9">
											<input name="lain_lain" placeholder="Lain-lain" class="form-control money" type="text" value="<?php echo @$data->lain_lain; ?>">
											<span class="help-block"></span>
										</div>
									</div>	
									<div class="form-group">
										<label class="control-label col-md-3">Cara Pembayaran</label>
										<div class="col-md-9">
											<input type="radio" name="cara_pembayaran" value="1"> Bulanan&nbsp;&nbsp;&nbsp;
											<input type="radio" name="cara_pembayaran" value="2"> Termin&nbsp;&nbsp;&nbsp;
											<input type="radio" name="cara_pembayaran" value="3"> Sekaligus&nbsp;&nbsp;&nbsp;
											<span class="help-block"></span>
										</div>
									</div>										
								  </div>
								</div>
							</div>
						</form>
						<div class="modal-footer">
							<button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Lanjutkan</button>
							
							<!--<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>-->
						</div>
					</div>
					<div class="tab-pane fade" id="data-adendum">
						
					</div>
					</div>					
				</div>
			</div>
		</div>
	</div>
</div>