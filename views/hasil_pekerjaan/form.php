<script src="<?php echo base_url('assets/js/bootstrap3-typeahead.js')?>"></script>
<link href="<?php echo base_url('assets/select2/select2.css'); ?>" rel="stylesheet" type="text/css" >
<script src="<?php echo base_url('assets/select2/select2.js'); ?>"></script>
<style>
input[type="file"] {
  display: block;
}
.pip {
  display: inline-block;
  margin: 1px 1px 0 0;
}
.remove {
  display: block;
  background: #444;
  border: 1px solid black;
  color: white;
  text-align: center;
  cursor: pointer;
}
.remove:hover {
  background: white;
  color: black;
}
</style>
<script>
var id_hasil_pekerjaan = '<?php if(@$data->id_hasil_pekerjaan){ echo @$data->id_hasil_pekerjaan; }else{ echo 0; } ?>';
var id_pencairan = 0;


$("#id_pegawai_pphp").select2({
	placeholder: "Silahkan Pilih PPHP"
});

$("#id_pegawai_pphp").on("select2:select", function (evt) {
  var element = evt.params.data.element;
  var $element = $(element);
  
  $element.detach();
  $(this).append($element);
  $(this).trigger("change");
});

//utk auto pilih pejabat pphp ketika edit surat
// var id_pejabat_pphp = '<?=@$id_pejabat_pphp; ?>';
// $('#id_pegawai_pphp').val([43493,43612, 40954]).trigger("change");
// var newOption = new Option("RIZKY FEBRIYANTO SUNARYO, S.Kom", 41027, false, true);
// $('#id_pegawai_pphp').val([43612, 40954, 43493]).trigger("change");

if(id_pencairan_post > 0){
	id_pencairan = id_pencairan_post;
}else{
	id_pencairan = '<?=@$data->id_pencairan; ?>';
}


// $(document).ready(function() {
	// if(id_hasil_pekerjaan == 0)
	// {
		// $.ajax({
			// url : '<?php echo base_url('ba/'.$class_name.'/ajax_cek_pejabat/')?>'+ id_pencairan,
			// type: "GET",
			// dataType: "JSON",
			// success: function(data)
			// {
				
				// if(data.status) //if success close modal and reload ajax table
				// {	
					// if(data.id_pegawai_ppk)
					// {
						// $("#id_pegawai_ppk").val(data.id_pegawai_ppk);
					// }
					
					// if(data.id_pegawai_pptk)
					// {
						// $("#id_pegawai_pptk").val(data.id_pegawai_pptk);
					// }
					
					// $('.selectpicker').attr('data-live-search', 'true');
					// $('.selectpicker').selectpicker('refresh');
				// }
			// },
			// error: function (jqXHR, textStatus, errorThrown)
			// {

			// }
		// });
	// }
// });

function fileValidation(e){
	var fileInput = document.getElementById('file_pekerjaan');
    var filePath = fileInput.value;
    // var allowedExtensions = /(\.pdf|\.jpeg|\.png|\.gif)$/i;
    var allowedExtensions = /(\.pdf)$/i;
    if(!allowedExtensions.exec(filePath)){
        // alert('Please upload file having extensions .jpeg/.jpg/.png/.gif only.');
        alert('File Upload Harus format .pdf.');
        fileInput.value = '';
        return false;
    }else{
        
		//Image preview
		var urlfile = URL.createObjectURL(e.target.files[0]);
		var files = e.target.files,
			filesLength = files.length;
		for (var i = 0; i < filesLength; i++) {
		var f = files[i]
		var fileReader = new FileReader();
		fileReader.onload = (function(e) {
			var file = e.target;
			
			$("<span class=\"pip\">" +
			"<a href='" + urlfile + "' target='_blank'>"	+
			"<img class=\"imageThumb\" src=\"" + base_url + "assets/file/kontrak/icon_pdf.png \" title=\"" + file.name + "\"/></a>" +
			"<br/>" +
			"</span>").insertAfter("#file_pekerjaan");
			
			
			/* //dengan tombol remove 
			
			$("<br><span class=\"pip\">" +
			"<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
			"<br/><span class=\"remove\">Remove image</span>" +
			"</span>").insertAfter("#file");
			
			$(".remove").click(function(){
				$(this).parent(".pip").remove();
			}); */
		});
			fileReader.readAsDataURL(f);
		}
    }
}

//utk upload foto
if (window.File && window.FileList && window.FileReader) {
$("#file_pekerjaan").on("change", function(e) {
  fileValidation(e);
});
} else {
	alert("Your browser doesn't support to File API")
}

var photo_open = '<?php echo @$data->file_pekerjaan; ?>';

if(photo_open){
	// alert("<img src='"+base_url+"assets/images/laporan/"+photo_open+"' class='imageThumb'>");
	$("<span class=\"pip\"><br><a href='"+base_url+"assets/file/dokumen_pekerjaan/"+photo_open+"' target='_blank'><img src='"+base_url+"assets/file/kontrak/icon_pdf.png' class='imageThumb'></a></span>").insertAfter("#file_pekerjaan");
}


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

$(document).on('mousedown', 'ul.typeahead', function(e) {
    e.preventDefault();
});
//untuk typeahead

function save(kondisi)
{
    $('#btnSave').text('saving...'); //change button text
    $('#btnSave').attr('disabled',true); //set button disable 
    var url;
	url = "<?php echo base_url('ba/'.$class_name.'/ajax_save')?>";

    // ajax adding data to database
	$("#loading-overlay").show();
	$("#termin").attr('disabled',false);
    var formData = new FormData($('#form')[0]);
	formData.append('id', id_hasil_pekerjaan);
	formData.append('id_pencairan', id_pencairan);
	// formData.append('data_pegawai', <? //echo @$data_pegawai; ?>);
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
			if(kondisi != 1){
				$('div.alert').remove();
				$("#message").html(data.notif);
			}
			$("#termin").attr('disabled',true);
            if(data.status) //if success close modal and reload ajax table
            {				
				id_hasil_pekerjaan = data.id_hasil_pekerjaan; 
				// $('#page-wrapper-form').html(''); //reset / menghilangkan form menu dalam div 
				$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');			
				// $(".page-header").html('Data Pencairan <div class="btn-group pull-right tombl"></div>');
				if(kondisi != 1){
					$('a[href="#data-uraian-pekerjaan"]').trigger('click');
				}
				$(".alert").delay(2000).slideUp(500, function() {					
					// reload_table();
					// $('#page-wrapper-datatable').show();
					// $(".tombl").html('<button class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" type="button"><i class="fa fa-cogs fa-fw"></i></button><ul class="dropdown-menu slidedown"><li><a href="javascript:void(0)" onclick="add_penyedia()"><i class="fa fa-folder-o fa-fw"></i> Tambah </a></li></ul>');
				});
            }
            else
            {
				if(kondisi == 1){
					$('a[href="#utama"]').trigger('click');
					alert('Lengkapi Kembali Data Utama');
				}
				
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

$('a[href="#data-uraian-pekerjaan"]').click(function(){	
	if(id_hasil_pekerjaan == 0){
		$('#formModal')[0].reset(); // reset form on modals
		$('.form-group').removeClass('has-error'); // clear error class
		$('#modal_form').modal('show'); // show bootstrap modal
		$('.modal-title').text('Informasi'); // Set Title to Bootstrap modal title
		$('#btnModal_cancel').text('Close'); // Set Title to Bootstrap modal title
		$('#btnModal_proses').hide(); // Set Title to Bootstrap modal title
		$('#kalimat_tampil').html('Data Utama Wajib Diisi'); // Add Teks		
		
		return false;
	}else{
		$('#data-uraian-pekerjaan').load('<?=base_url('ba/uraian/ajax_form');?>', function(data, status){
			save(1); //benar artinya kondisi sedang diedit namun sudah punya id hasil pekerjaan, bukan kodnisi buat baru
		});	
	}
});
</script>
<div class="row">
	<div class="col-lg-12">
	
		<div class="panel panel-default">					
			<div class="panel-body">
			
				<ul class="nav nav-tabs">
					<li class="active"><a href="#utama" data-toggle="tab"><span class="glyphicon glyphicon-file"></span> Data Utama</a>
					</li>
					<li><a href="#data-uraian-pekerjaan" data-toggle="tab"><span class="glyphicon glyphicon-list"></span> Uraian Pekerjaan</a>
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
							<input type="hidden"  value="<?=htmlentities(@$data_pegawai); ?>" name="data_pegawai"/>	
							
							<div class="panel panel-primary">
								<div class="panel-heading" data-toggle="collapse" data-target="#collapse1">
								  <h4 class="panel-title accordion-toggle">
									  Form Surat
								  </h4>
								</div>
								<div id="collapse1" class="panel-collapse in">
								  <div class="panel-body">						
									<div class="form-group">
										<label class="control-label col-md-3">Nomor Surat</label>
										<div class="col-md-9">
											<input name="no_srt_penyerahan" placeholder="Nomor Surat Penyerahan Hasil Pekerjaan" class="form-control" type="text" value="<?php echo @$data->no_srt_penyerahan; ?>">
											<span class="help-block"></span>
										</div>
									</div>	
									<div class="form-group">
										<label class="control-label col-md-3">Tanggal Surat</label>
										<div class="col-md-9">
											<div class="dateContainer">	
												<div class="input-group date datetimePicker" id="tgl_srt_penyerahan">
												<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
													<input class="form-control" name="tgl_srt_penyerahan" id="tgl_srt_penyerahan" value="<?php if(!empty($data->tgl_srt_penyerahan)){ echo date('d-m-Y', strtotime($data->tgl_srt_penyerahan)); }else{ echo date('d-m-Y'); } ?>" placeholder="DD-MM-YYYY" data-date-format="DD-MM-YYYY" type="text">
												 </div>
											  </div>
											<span class="help-block"></span>
										</div>
									</div>													
									<div class="form-group">
										<label class="control-label col-md-3">Termin</label>
										<div class="col-md-9">
											<?php echo $data_termin; ?>
											<span class="help-block"></span>
										</div>
									</div>															
									<div class="form-group">
										<label class="control-label col-md-3">Biaya Pencairan</label>
										<div class="col-md-9">
											<input name="nilai_pekerjaan" placeholder="Biaya Pencairan" class="form-control money" type="text" value="<?php if(@$data->nilai_pekerjaan){echo number_format(@$data->nilai_pekerjaan, 2); } ?>">
											<span class="help-block"></span>
										</div>
									</div>
								  </div>
								</div>
							</div>
							
							<div class="panel panel-warning">
								<div class="panel-heading" data-toggle="collapse" data-target="#collapse2">
								  <h4 class="panel-title accordion-toggle">
									  Form PPHP
								  </h4>
								</div>
								<div id="collapse2" class="panel-collapse in">
								  <div class="panel-body">															
									<div class="form-group">
										<label class="control-label col-md-3">Nomor SK</label>
										<div class="col-md-9">
											<input name="pjbtpenerima_nosk" placeholder="Nomor SK PPHP" class="typeahead form-control" type="text" value="<?php echo @$data->pjbtpenerima_nosk; ?>">
											<span class="help-block"></span>
										</div>
									</div>	
									<div class="form-group">
										<label class="control-label col-md-3">Tanggal SK</label>
										<div class="col-md-9">
											<div class="dateContainer">	
												<div class="input-group date datetimePicker" id="pjbtpenerima_tglsk">
												<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
													<input class="form-control" name="pjbtpenerima_tglsk" id="pjbtpenerima_tglsk" value="<?php if(!empty($data->pjbtpenerima_tglsk)){ echo date('d-m-Y', strtotime($data->pjbtpenerima_tglsk)); }else{ echo date('d-m-Y'); } ?>" placeholder="Tanggal SK PPHP" data-date-format="DD-MM-YYYY" type="text">
												 </div>
											  </div>
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-3">PPHP</label>
										<div class="col-md-9">
											<?php echo $pphp; ?>
											<span class="help-block"></span>
											<span><i>*Bisa Dipilih lebih dari satu</i></span>
										</div>
										<!--
										<div class="col-md-1" style="padding-left: 0px;">									
											<button type="button" class="btn btn-warning" id="tr_tambah_pphp"><i class="glyphicon glyphicon-plus-sign"></i> PPHP</button>
										</div>
										-->
									</div>		
								  </div>
								</div>
							</div>
							
							<div class="panel panel-primary">
								<div class="panel-heading" data-toggle="collapse" data-target="#collapse4">
								  <h4 class="panel-title accordion-toggle">
									  Form Pejabat Penandatangan
								  </h4>
								</div>
								<div id="collapse4" class="panel-collapse in">
								  <div class="panel-body">		
									<div class="form-group">
										<label class="control-label col-md-3">PPK</label>
										<div class="col-md-9">
											<?php echo $ppk; ?>
											<span class="help-block"></span>
										</div>
									</div>	
									<div class="form-group">
										<label class="control-label col-md-3">PPTK</label>
										<div class="col-md-9">
											<?php echo $pptk; ?>
											<span class="help-block"></span>
										</div>
									</div>	
									<div class="form-group">
										<label class="control-label col-md-3">Bendahara</label>
										<div class="col-md-9">
											<?php echo $bendahara; ?>
											<span class="help-block"></span>
										</div>
									</div>		
									<div class="form-group">
										<label class="control-label col-md-3">Pengguna Anggaran</label>
										<div class="col-md-9">
											<?php echo $pengguna_anggaran; ?>
											<span class="help-block"></span>
											<input type="checkbox" name="kuasa_anggaran" value="1" <?php if(@$data->kuasa_anggaran == 1){ echo "checked"; } ?>> Selaku Kuasa Anggaran
										</div>
									</div>		
								  </div>
								</div>
							</div>
							
							<div class="panel panel-primary">
								<div class="panel-heading" data-toggle="collapse" data-target="#collapse3">
								  <h4 class="panel-title accordion-toggle">
									  Form Berita Acara Penerimaan
								  </h4>
								</div>
								<div id="collapse3" class="panel-collapse in">
								  <div class="panel-body">															
									<div class="form-group">
										<label class="control-label col-md-3">Nomor</label>
										<div class="col-md-9">
											<input name="no_bas_penerimaan" placeholder="Nomor Berita Acara Penerimaan" class="form-control" type="text" value="<?php echo @$data->no_bas_penerimaan; ?>">
											<span class="help-block"></span>
										</div>
									</div>	
									<div class="form-group">
										<label class="control-label col-md-3">Tanggal</label>
										<div class="col-md-9">
											<div class="dateContainer">	
												<div class="input-group date datetimePicker" id="tanggal_bas">
												<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
													<input class="form-control" name="tanggal_bas" id="tanggal_bas" value="<?php if(!empty($data->tanggal_bas)){ echo date('d-m-Y', strtotime($data->tanggal_bas)); }else{ echo date('d-m-Y'); } ?>" placeholder="DD-MM-YYYY" data-date-format="DD-MM-YYYY" type="text">
												 </div>
											  </div>
											<span class="help-block"></span>
										</div>
									</div>
								  </div>
								</div>
							</div>
							
							<div class="panel panel-primary">
								<div class="panel-heading" data-toggle="collapse" data-target="#collapse5">
								  <h4 class="panel-title accordion-toggle">
									  Form Berita Acara Serah Terima Pekerjaan (BAST)
								  </h4>
								</div>
								<div id="collapse5" class="panel-collapse in">
								  <div class="panel-body">															
									<div class="form-group">
										<label class="control-label col-md-3">Nomor</label>
										<div class="col-md-9">
											<input name="no_bast" placeholder="Nomor Berita Acara Serah Terima Pekerjaan (BAST)" class="form-control" type="text" value="<?php echo @$data->no_bast; ?>">
											<span class="help-block"></span>
										</div>
									</div>	
									<div class="form-group">
										<label class="control-label col-md-3">Tanggal</label>
										<div class="col-md-9">
											<div class="dateContainer">	
												<div class="input-group date datetimePicker" id="tgl_bast">
												<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
													<input class="form-control" name="tgl_bast" id="tgl_bast" value="<?php if(!empty($data->tgl_bast)){ echo date('d-m-Y', strtotime($data->tgl_bast)); }else{ echo date('d-m-Y'); } ?>" placeholder="DD-MM-YYYY" data-date-format="DD-MM-YYYY" type="text">
												 </div>
											  </div>
											<span class="help-block"></span>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-3" id="label-photo">Upload Hasil Pekerjaan <br><i><font color="red">*Format .pdf, maks 20Mb</font></i> </label>
										<div class="col-md-9">
											<input name="file_pekerjaan" id="file_pekerjaan" type="file" class="form-control" accept="application/pdf">
											<span class="help-block"></span>
										</div>
									</div>
								  </div>
								</div>
							</div>

						</form>
						<div class="modal-footer">
							<button type="button" id="btnSave" onclick="save(2)" class="btn btn-primary">Lanjutkan</button>
							
							<!--<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>-->
						</div>
					</div>
					<div class="tab-pane fade" id="data-uraian-pekerjaan">
						
					</div>
					</div>					
				</div>
			</div>
		</div>
	</div>
</div>
<script>

// var newOption = new Option("ADHI ZULKIFLI, ST. MT.", 1692, true, true);
// $('#id_pegawai_pphp').append(newOption).trigger('change');
// $('#id_pegawai_pphp').val(["1692", "41027", "40846"]).trigger("change");

</script>