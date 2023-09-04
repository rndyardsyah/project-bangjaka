<link href="<?php echo base_url('assets/form_wizard/form-wizard.css'); ?>" rel="stylesheet" type="text/css" >
<script>
var id_pembayarannya = '<?=@$data->id_pembayaran; ?>';


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

/* $('.datetimePicker #tgl_nota_dinas_pencairan').datetimepicker(
{
	pickTime: false 
}
); */

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

$('#id_pencairan').val('<?php echo @$id_pencairan; ?>').change();


function getPasti(data){	
	var nilai_pasti = data.value;
	if(nilai_pasti == 2){
		$('#form-belum-pasti').load('<?=base_url('ba/'.$class_name.'/getDataListBelumPasti');?>', function(data, status) 
			{
				$("#loading-overlay").hide();			
				// $('.selectpicker').attr('data-live-search', 'true');
				// $('.selectpicker').selectpicker('refresh');
			}
		);
	}else{
		$('#form-belum-pasti').html('');
	}
}

function reset(){
	
}

function tarikDataDPA(id_pencairan){
	
	$("#loading-overlay").show();
	
	$.ajax({
        url : "<?=base_url('ba/'.$class_name.'/getDataRincianDPA');?>",
        type: "POST",
        data: {id_pencairan: id_pencairan},
        dataType: "JSON",
        success: function(data)
        {
            if(data.status) //if success close modal and reload ajax table
            {				
				if(!id_pembayarannya){
					$('[name="satuan"]').val(data.satuan);
					$('[name="volume"]').val(data.volume);
					$('[name="harga_satuan"]').val(data.harga_satuan);
					$('[name="uraian_dpa"]').val(data.uraian);
				}
            }			
            $("#loading-overlay").hide();
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            $("#loading-overlay").hide();
        }
    });
}

function getData(id_pencairan){
	//kirim data pencairan ke data hasil pekerjaan untuk mendapatkan termin
	// alert(id_pencairan);
	$("#loading-overlay").show();
	var id_pembayaran = $('[name="id"]').val();
	if(id_pencairan){
		$('.data-termin').load('<?=base_url('ba/'.$class_name.'/getDataListTermin');?>', {id_pencairan:id_pencairan, id_pembayaran:id_pembayaran}, function(data, status) 
			{
				$("#loading-overlay").hide();			
				$('.selectpicker').attr('data-live-search', 'true');
				$('.selectpicker').selectpicker('refresh');
				
				$("select").change(function(){
					$(this).parent().parent().removeClass('has-error');
					$(this).next().next().empty();
				});
			}
		);
		// tarikDataDPA(id_pencairan);
		$('.data-pasti').load('<?=base_url('ba/'.$class_name.'/getDataListPasti');?>', {id_pencairan:id_pencairan, id_pembayaran:id_pembayaran}, function(data, status) 
			{
				$("#loading-overlay").hide();			
				$('.selectpicker').attr('data-live-search', 'true');
				$('.selectpicker').selectpicker('refresh');
				$('#pasti').val('<?php echo @$data->pasti; ?>').change();
				$("select").change(function(){
					$(this).parent().parent().removeClass('has-error');
					$(this).next().next().empty();
				});

			}
		);		
	}else{
		$("#loading-overlay").hide();
		$('.data-termin').html('');
		$('.data-pasti').html('');
		
		
		$('[name="satuan"]').val('');
		$('[name="volume"]').val('');
		$('[name="harga_satuan"]').val('');
		$('[name="uraian_dpa"]').val('');
	}
	
}


function save(){	
	$('.form-group').removeClass('has-error'); // clear error class
	$('#modal_form').modal('show'); // show bootstrap modal
	$('.modal-title').text('Konfirmasi'); // Set Title to Bootstrap modal title
	$("#btnModal_proses").attr("onclick","save_proses()");
	$('#btnModal_proses').show(); // Set Title to Bootstrap modal title
	$('#btnModal_proses').text('Proses'); // Set Title to Bootstrap modal title
	$('#form-tambahan').html(''); // Add Teks
	$('#kalimat_tampil').html('Apakah Anda yakin <b>Simpan Data</b> ini ?'); // Add Teks
}

function save_proses_data(simpan = false){
	save_method = 'save_proses';
	save_proses(simpan);
}

function save_proses(simpan = false)
{	
    if(simpan == false){
		$('#btnModal_proses').attr('disabled',true); //set button disable 
		$('#btnModal_cancel').attr('disabled',true); //set button disable 
	}
	
	$('#btnSave').text('saving...'); //change button text
    $('#btnSave').attr('disabled',true); //set button disable 
    var url;
	url = "<?php echo base_url('ba/'.$class_name.'/ajax_save')?>";

    // ajax adding data to database
	$("#loading-overlay").show();
	$('#id_pencairan').removeAttr("disabled");
    var formData = new FormData($('#form')[0]);	
		formData.append('id', id_pembayarannya);
	if(save_method == 'proses_adendum'){
		formData.append('save_method', save_method);
	}
	
    $.ajax({
        url : url,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "JSON",
        success: function(data)
        {
			if(simpan == false){
				$('#modal_form').modal('hide');			
				$('#btnModal_proses').attr('disabled',false);
				$('#btnModal_cancel').attr('disabled',false);
			}
			
			$("#loading-overlay").hide();
			$('div.alert').remove();
			$("#message").html(data.notif);
            if(data.status) //if success close modal and reload ajax table
            {				
				id_pembayarannya = data.id_pembayaran;
				if(save_method == 'proses_adendum'){
					$('#page-wrapper-form').html(''); //reset / menghilangkan form menu dalam div 
				}
				$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');		
				$('a[href="#data-lampiran-ba"]').trigger('click');
				
				$(".page-header").html('Data Pengajuan Pembayaran <div class="btn-group pull-right tombl"></div>');
				$(".tombl").html('<a href="javascript:void(0)" onclick="kembali()" class="btn btn-primary"><i class="glyphicon glyphicon-backward"></i> kembali</a>');
				
				$(".alert").delay(2000).slideUp(500, function() {	
					if(save_method == 'proses_adendum'){
						reload_table();
						$('#page-wrapper-datatable').show();
						$(".tombl").html('<button class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" type="button"><i class="fa fa-cogs fa-fw"></i></button><ul class="dropdown-menu slidedown"><li><a href="javascript:void(0)" onclick="add_penyedia()"><i class="fa fa-folder-o fa-fw"></i> Tambah </a></li></ul>');
					}
				});
            }
            else
            {
				$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');	
                for (var i = 0; i < data.inputerror.length; i++) 
                {
					$('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                    if(data.inputerror[i] == 'id_pencairan' || data.inputerror[i] == 'id_hasil_pekerjaan[]' || data.inputerror[i] == 'pasti'){
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
<!------ Include the above in your HEAD tag ---------->

<script>
$(document).ready(function () {

    var navListItems = $('div.setup-panel div a'),
            allWells = $('.setup-content'),
            allNextBtn = $('.nextBtn');

    allWells.hide();

    navListItems.click(function (e) {
        e.preventDefault();
        var $target = $($(this).attr('href')),
                $item = $(this);

        if (!$item.hasClass('disabled')) {
            navListItems.removeClass('btn-primary').addClass('btn-default');
            $item.addClass('btn-primary');
            allWells.hide();
            $target.show();
            $target.find('input:eq(0)').focus();
        }
    });

    allNextBtn.click(function(){
        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
            curInputs = curStep.find("input[type='text'], select, input[type='url']"),
			isValid = true;
		
        $(".form-group").removeClass("has-error");
		for(var i=0; i<curInputs.length; i++){
			if (!curInputs[i].validity.valid){
				isValid = false;
                $(curInputs[i]).closest(".form-group").addClass("has-error");
            }
        }

        if (isValid)
            nextStepWizard.removeAttr('disabled').trigger('click');
    });

    $('div.setup-panel div a.btn-primary').trigger('click');
});

$('a[href="#data-adendum"]').click(function(){		
	 //Ajax Load data from ajax
    var idpencairan = $('#id_pencairan').val();
	if(idpencairan && id_pembayarannya){
		$.ajax({		
			url : "<?php echo base_url('ba/pembayaran_rincian/ajax_check')?>/" + id_pembayarannya + "/" + idpencairan,
			type: "GET",
			dataType: "JSON",        
			success: function(data)
			{
				if(data.status){
					$('#data-adendum').load('<?=base_url('ba/adendum/ajax_form');?>', {id_pembayaran:id_pembayarannya}, function(data, status) 
					{
						$("#loading-overlay").hide();
					});
				}else{
					$('#formModal')[0].reset(); // reset form on modals
					$('.form-group').removeClass('has-error'); // clear error class
					$('#modal_form').modal('show'); // show bootstrap modal
					$('.modal-title').text('Informasi'); // Set Title to Bootstrap modal title
					$('#btnModal_cancel').text('Close'); // Set Title to Bootstrap modal title
					$('#btnModal_proses').hide(); // Set Title to Bootstrap modal title
					$('#kalimat_tampil').html('Lengkapi Data Lampiran BA (Seluruh Uraian Kegiatan)'); // Add Teks		
					$('a[href="#data-lampiran-ba"]').trigger('click');
					return false;
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert('Error get data from ajax');
			}
		});
	}else{
		$('#formModal')[0].reset(); // reset form on modals
		$('.form-group').removeClass('has-error'); // clear error class
		$('#modal_form').modal('show'); // show bootstrap modal
		$('.modal-title').text('Informasi'); // Set Title to Bootstrap modal title
		$('#btnModal_cancel').text('Close'); // Set Title to Bootstrap modal title
		$('#btnModal_proses').hide(); // Set Title to Bootstrap modal title
		$('#kalimat_tampil').html('Silahkan Lengkapi dan Klik tombol Lanjutkan Data Utama'); // Add Teks		
		
		return false;
	}
});

$('a[href="#data-lampiran-ba"]').click(function(){		
	// alert(id_pembayarannya);	
	if(id_pembayarannya){
		$('#data-lampiran-ba').load('<?=base_url('ba/'.$class_name.'/getDataRincianDPA');?>', {id_pencairan:$('#id_pencairan').val()}, function(data, status) 
		{
			$("#loading-overlay").hide();
		});		
	}else{
		$('#formModal')[0].reset(); // reset form on modals
		$('.form-group').removeClass('has-error'); // clear error class
		$('#modal_form').modal('show'); // show bootstrap modal
		$('.modal-title').text('Informasi'); // Set Title to Bootstrap modal title
		$('#btnModal_cancel').text('Close'); // Set Title to Bootstrap modal title
		$('#btnModal_proses').hide(); // Set Title to Bootstrap modal title
		$('#kalimat_tampil').html('Data Utama Wajib Diisi'); // Add Teks		
		
		return false;		
	}
});

function parseCurrency( num ) {
    return parseFloat( num.replace( /,/g, '') );
}

$('.accordion-body').each(function(){
    if ($(this).hasClass('in')) {
        $(this).collapse('toggle');
    }
});

// $('#collapse5').collapse({
  // toggle: false
// })
</script>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">					
			<div class="panel-body">
				
				<ul class="nav nav-tabs">
					<li class="active"><a href="#utama" data-toggle="tab"><span class="glyphicon glyphicon-file"></span> Data Utama</a></li>
					<li><a href="#data-lampiran-ba" data-toggle="tab"><span class="glyphicon glyphicon-list"></span> Data Lampiran BA (Rincian Kegiatan)</a>
					<li><a href="#data-adendum" data-toggle="tab"><span class="glyphicon glyphicon-list"></span> Data Adendum</a>
					</li>
				</ul>
				
				
				<div class="tab-content">
					<div class="tab-pane fade in active" id="utama">
						
						<form action="#" id="form" class="form-horizontal" style="margin-top: 10px;">					
							<input type="hidden"  value="<?=@$data->id_pembayaran; ?>" name="id"/>
							
							<div class="panel panel-primary">
								<div class="panel-heading" data-toggle="collapse" data-target="#collapse1">
								  <h4 class="panel-title accordion-toggle">
									  Form SPK
								  </h4>
								</div>
								<div id="collapse1" class="panel-collapse in">
								  <div class="panel-body">														
									<div class="form-group">
										<label class="control-label col-md-3">Data Kontrak</label>
										<div class="col-md-9">
											<?php echo $data_kontrak; ?>
											<span class="help-block"></span>
										</div>
									</div>	
									<div class="form-group data-termin">							
									</div>									
									<div class="form-group data-pasti">										
									</div>																
									<!-- 
									<div class="form-group">
										<label class="control-label col-md-3">Pembayaran (%) </label>
										<div class="col-md-9">
											<input name="bayar_persen" placeholder="Pembayaran Sebesar .....% " class="form-control money" type="text" value="<?php //echo number_format(@$data->bayar_persen, 2); ?>">
											<span class="help-block"></span>
										</div>
									</div>
									-->
								  </div>
								</div>
							</div>
							
							
							<div class="panel panel-primary">
								<div class="panel-heading" data-toggle="collapse" data-target="#collapse2">
								  <h4 class="panel-title accordion-toggle">
									  Form Permohonan Pembayaran
								  </h4>
								</div>
								<div id="collapse2" class="panel-collapse in">
								  <div class="panel-body">															
									<div class="form-group">
										<label class="control-label col-md-3">No Permohonan Pembayaran</label>
										<div class="col-md-9">
											<input maxlength="200" type="text" required="required" id="no_permohonan_pembayaran" name="no_permohonan_pembayaran" class="form-control" placeholder="Nomor Permohonan Pembayaran" value="<?=@$data->no_permohonan_pembayaran; ?>"/>
											<span class="help-block"></span>
										</div>
									</div>
								  </div>
								</div>
							</div>
							
							
							<div class="panel panel-primary">
								<div class="panel-heading" data-toggle="collapse" data-target="#collapse3">
								  <h4 class="panel-title accordion-toggle">
									  Form Berita Pembayaran
								  </h4>
								</div>
								<div id="collapse3" class="panel-collapse in">
								  <div class="panel-body">															
									<div class="form-group">
										<label class="control-label col-md-3">No Berita Acara Pembayaran</label>
										<div class="col-md-9">
											<input maxlength="200" type="text" required="required" id="no_ba_pembayaran"  name="no_ba_pembayaran" class="form-control" placeholder="Nomor Permohonan Pembayaran" value="<?=@$data->no_ba_pembayaran; ?>" />
											<span class="help-block"></span>
										</div>
									</div>
								  </div>
								</div>
							</div>
							
							<div class="panel panel-primary">
								<div class="panel-heading" data-toggle="collapse" data-target="#collapse4">
								  <h4 class="panel-title accordion-toggle">
									  Form Nota Dinas Pencairan
								  </h4>
								</div>
								<div id="collapse4" class="panel-collapse in">
								  <div class="panel-body">															
									<div class="form-group">
										<label class="control-label col-md-3">No Nota Dinas Pencairan</label>
										<div class="col-md-9">
											<input maxlength="200" type="text" required="required" id="nota_dinas_pencairan"  name="nota_dinas_pencairan" class="form-control" placeholder="Nomor Nota Dinas Pencairan" value="<?=@$data->nota_dinas_pencairan; ?>"/>
											<span class="help-block"></span>
										</div>
									</div>
								  </div>
								</div>
							</div>
							
							<div id="form-belum-pasti">
							</div>
						</form>
						<div class="modal-footer">
							<button type="button" id="btnSave" onclick="save_proses_data(true)" class="btn btn-primary">Lanjutkan</button>
							<!--<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>-->
						</div>						
					</div>					
					<div class="tab-pane fade" id="data-lampiran-ba">
						
					</div>				
					<div class="tab-pane fade" id="data-adendum">
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>