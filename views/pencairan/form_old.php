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
					$(".tombl").html('<button class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" type="button"><i class="fa fa-cogs fa-fw"></i></button><ul class="dropdown-menu slidedown"><li><a href="javascript:void(0)" onclick="add_penyedia()"><i class="fa fa-folder-o fa-fw"></i> Tambah </a></li></ul>');
				$(".alert").delay(2000).slideUp(500, function() {					
					reload_table();
					$('#page-wrapper-datatable').show();					
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
					
					<div class="panel panel-primary accordion4">
						<div class="panel-heading">
							Form Data Penyedia
							<button data-toggle="collapse" href="#collapseOne4" class="btn btn-default btn-xs pull-right form-accordion" type="button" id="accordion4"><i class="fa fa-caret-up fa-fw"></i></button>                
						</div>
						
					   <div id="collapseOne4" class="panel-collapse in" style="height:auto">					   
							<div class="panel-body">							
								<div class="form-group">
									<label class="control-label col-md-3">Nama Penyedia</label>
									<div class="col-md-9">
										<?=getformselect('m_penyedia','id_penyedia','nama_penyedia','status = 1'); ?>
										<span class="help-block"></span>
									</div>
								</div>					
								<div class="form-group">
									<label class="control-label col-md-3">Nama Pekerjaan</label>
									<div class="col-md-9">
										<?=getformselect('m_spk','id_spk','nama_pekerjaan','status = 1'); ?>
										<span class="help-block"></span>
									</div>
								</div>
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
								<div class="form-group">
									<label class="control-label col-md-3">Termin Pekerjaan</label>
									<div class="col-md-9">
										<input name="pekerjaan_termin" placeholder="Pekerjaan Termin" class="form-control" type="text" value="">
										<span class="help-block"></span>
									</div>
								</div>					
								<div class="form-group">
									<label class="control-label col-md-3">Nominal Biaya</label>
									<div class="col-md-9">
										<input name="nominal_bayar" placeholder="Nominal Biaya" class="form-control" type="text" value="">
										<span class="help-block"></span>
									</div>
								</div>		
								<div class="form-group">
									<label class="control-label col-md-3">Nominal Biaya terbilang</label>
									<div class="col-md-9">
										<input name="nominal_bayar_terbilang" placeholder="Nominal Biaya terbilang" class="form-control" type="text" value="">
										<span class="help-block"></span>
									</div>
								</div>
							</div>					   
					   </div>					   
					</div>
					
					<div class="panel panel-primary accordion6">
						<div class="panel-heading">
							Form Surat Permohonan Pembayaran
							<button data-toggle="collapse" href="#collapseOne6" class="btn btn-default btn-xs pull-right form-accordion" type="button" id="accordion6"><i class="fa fa-caret-up fa-fw"></i></button>                
						</div>
						
					   <div id="collapseOne6" class="panel-collapse in" style="height:auto">					   
							<div class="panel-body">							
								<div class="form-group">
									<label class="control-label col-md-3">Perihal</label>
									<div class="col-md-9">
										<input name="perihal" placeholder="Perihal" class="form-control" type="text" value="">
										<span class="help-block"></span>
									</div>
								</div>					
								<div class="form-group">
									<label class="control-label col-md-3">Termin Pembayaran</label>
									<div class="col-md-9">
										<input name="termin" placeholder="Termin Pembayaran" class="form-control" type="text" value="">
										<span class="help-block"></span>
									</div>
								</div>				
								<div class="form-group">
									<label class="control-label col-md-3">No Surat Pembayaran</label>
									<div class="col-md-9">
										<input name="no_srt_pembayaran" placeholder="No Surat Pembayaran" class="form-control" type="text" value="">
										<span class="help-block"></span>
									</div>
								</div>		
								<div class="form-group">
									<label class="control-label col-md-3">Tanggal Surat Pembayaran</label>
									<div class="col-md-9">
										<div class="dateContainer">	
											<div class="input-group date datetimePicker" id="tgl_srt_pembayaran">
											<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
												<input class="form-control" name="tgl_srt_pembayaran" id="tgl_srt_pembayaran" value="23-07-2018" placeholder="DD-MM-YYYY" data-date-format="DD-MM-YYYY" type="text">
											 </div>
										  </div>
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Nama PPK</label>
									<div class="col-md-9">
										<input name="nama_ppk" placeholder="Nama PPK" class="form-control" type="text" value="">
										<span class="help-block"></span>
									</div>
								</div>
							</div>					   
					   </div>					   
					</div>
					
					<div class="panel panel-primary accordion7">
						<div class="panel-heading">
							Form (BAST) Berita Acara Pembayaran
							<button data-toggle="collapse" href="#collapseOne7" class="btn btn-default btn-xs pull-right form-accordion" type="button" id="accordion7"><i class="fa fa-caret-up fa-fw"></i></button>                
						</div>
						
					   <div id="collapseOne7" class="panel-collapse in" style="height:auto">					   
							<div class="panel-body">							
								<div class="form-group">
									<label class="control-label col-md-3">Nota Dinas Pembayaran</label>
									<div class="col-md-9">
										<input name="nota_dinas_pembayaran" placeholder="Nota Dinas Pembayaran" class="form-control" type="text" value="">
										<span class="help-block"></span>
									</div>
								</div>		
								<div class="form-group">
									<label class="control-label col-md-3">No. BA Pembayaran</label>
									<div class="col-md-9">
										<input name="no_ba_bayar" placeholder="No. BA Pembayaran" class="form-control" type="text" value="">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Tanggal BA Pembayaran</label>
									<div class="col-md-9">
										<div class="dateContainer">	
											<div class="input-group date datetimePicker" id="tgl_ba_bayar">
											<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
												<input class="form-control" name="tgl_ba_bayar" id="tgl_ba_bayar" value="23-07-2018" placeholder="DD-MM-YYYY" data-date-format="DD-MM-YYYY" type="text">
											 </div>
										  </div>
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Nilai Pekerjaan</label>
									<div class="col-md-9">
										<input name="nilai_pekerjaan" placeholder="Nilai Pekerjaan" class="form-control" type="text" value="">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Nilai Pekerjaan Terbilang</label>
									<div class="col-md-9">
										<input name="nilai_pekerjaan_terbilang" placeholder="Nilai Pekerjaan Terbilang" class="form-control" type="text" value="">
										<span class="help-block"></span>
									</div>
								</div>
							</div>					   
					   </div>					   
					</div>
					
					<div class="panel panel-primary accordion5">
						<div class="panel-heading">
							Form (BAST) Berita Acara Serah Terima Pekerjaan
							<button data-toggle="collapse" href="#collapseOne5" class="btn btn-default btn-xs pull-right form-accordion" type="button" id="accordion5"><i class="fa fa-caret-up fa-fw"></i></button>                
						</div>
						
					   <div id="collapseOne5" class="panel-collapse in" style="height:auto">					   
							<div class="panel-body">							
								<div class="form-group">
									<label class="control-label col-md-3">Output Bast</label>
									<div class="col-md-9">
										<input name="output_bast" placeholder="Output Bast" class="form-control" type="text" value="">
										<span class="help-block"></span>
									</div>
								</div>		
								<div class="form-group">
									<label class="control-label col-md-3">No. BAST</label>
									<div class="col-md-9">
										<input name="no_bast" placeholder="No. BAST" class="form-control" type="text" value="">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Tanggal No. BAST</label>
									<div class="col-md-9">
										<div class="dateContainer">	
											<div class="input-group date datetimePicker" id="tgl_bast">
											<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
												<input class="form-control" name="tgl_bast" id="tgl_bast" value="23-07-2018" placeholder="DD-MM-YYYY" data-date-format="DD-MM-YYYY" type="text">
											 </div>
										  </div>
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Nama PPK</label>
									<div class="col-md-9">
										<input name="nama_ppk" placeholder="Nama PPK" class="form-control" type="text" value="">
										<span class="help-block"></span>
									</div>
								</div>
							</div>					   
					   </div>					   
					</div>
					
					<div class="panel panel-primary accordion3">
						<div class="panel-heading">
							Form (BA) Data Pejabat
							<button data-toggle="collapse" href="#collapseOne3" class="btn btn-default btn-xs pull-right form-accordion" type="button" id="accordion3"><i class="fa fa-caret-up fa-fw"></i></button>                
						</div>
						
					   <div id="collapseOne3" class="panel-collapse in" style="height:auto">					   
							<div class="panel-body">							
								<div class="form-group">
									<label class="control-label col-md-3">Pejabat Penerima</label>
									<div class="col-md-9">
										<input name="pjbtpenerima_nama" placeholder="Pejabat Penerima" class="form-control" type="text" value="">
										<span class="help-block"></span>
									</div>
								</div>		
								<div class="form-group">
									<label class="control-label col-md-3">No. SK PPHP</label>
									<div class="col-md-9">
										<input name="pjbtpenerima_nosk" placeholder="No. SK PPHP" class="form-control" type="text" value="">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Tanggal No. SK PPHP</label>
									<div class="col-md-9">
										<div class="dateContainer">	
											<div class="input-group date datetimePicker" id="pjbtpenerima_tglsk">
											<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
												<input class="form-control" name="pjbtpenerima_tglsk" id="pjbtpenerima_tglsk" value="23-07-2018" placeholder="DD-MM-YYYY" data-date-format="DD-MM-YYYY" type="text">
											 </div>
										  </div>
										<span class="help-block"></span>
									</div>
								</div>
							</div>					   
					   </div>					   
					</div>
					
					<div class="panel panel-primary accordion2">
						<div class="panel-heading">
							Form (BA) Berita Acara Penerimaan
							<button data-toggle="collapse" href="#collapseOne2" class="btn btn-default btn-xs pull-right form-accordion" type="button" id="accordion2"><i class="fa fa-caret-up fa-fw"></i></button>                
						</div>
						
					   <div id="collapseOne2" class="panel-collapse in" style="height:auto">					   
							<div class="panel-body">							
								<div class="form-group">
									<label class="control-label col-md-3">Nomor</label>
									<div class="col-md-9">
										<input name="no_ba_penerimaan" placeholder="Nomor Berita Acara Penerimaan" class="form-control" type="text" value="">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Tanggal Surat</label>
									<div class="col-md-9">
										<div class="dateContainer">	
											<div class="input-group date datetimePicker" id="tanggal_ba">
											<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
												<input class="form-control" name="tanggal_ba" id="tanggal_ba" value="23-07-2018" placeholder="DD-MM-YYYY" data-date-format="DD-MM-YYYY" type="text">
											 </div>
										  </div>
										<span class="help-block"></span>
									</div>
								</div>
							</div>					   
					   </div>					   
					</div>
										
					<div class="panel panel-primary accordion1">
						<div class="panel-heading">
							Form Surat Penyerahan
							<button data-toggle="collapse" href="#collapseOne1" class="btn btn-default btn-xs pull-right form-accordion" type="button" id="accordion1"><i class="fa fa-caret-up fa-fw"></i></button>                
						</div>
						
					   <div id="collapseOne1" class="panel-collapse in" style="height:auto">					   
							<div class="panel-body">							
								<div class="form-group">
									<label class="control-label col-md-3">Uraian Hasil Pekerjaan</label>
									<div class="col-md-9">
										<input name="uraian_hsl_pekerjaan" placeholder="Uraian Hasil Pekerjaan" class="form-control" type="text" value="">
										<span class="help-block"></span>
									</div>
								</div>					
								<div class="form-group">
									<label class="control-label col-md-3">Nomor</label>
									<div class="col-md-9">
										<input name="no_srt_penyerahan" placeholder="Nomor Surat Penyerahan" class="form-control" type="text" value="">
										<span class="help-block"></span>
									</div>
								</div>
								<div class="form-group">
									<label class="control-label col-md-3">Tanggal Surat</label>
									<div class="col-md-9">
										<div class="dateContainer">	
											<div class="input-group date datetimePicker" id="tgl_srt_penyerahan">
											<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
												<input class="form-control" name="tgl_srt_penyerahan" id="tgl_srt_penyerahan" value="23-07-2018" placeholder="DD-MM-YYYY" data-date-format="DD-MM-YYYY" type="text">
											 </div>
										  </div>
										<span class="help-block"></span>
									</div>
								</div>
							</div>					   
					   </div>					   
					</div>
				</form>
				<div class="modal-footer">
					<button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div>
</div>