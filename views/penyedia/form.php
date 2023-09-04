<script>
$("input").change(function(){
	$(this).parent().parent().removeClass('has-error');
	$(this).next().empty();
});
$("textarea").change(function(){
	$(this).parent().parent().removeClass('has-error');
	$(this).next().empty();
});

$('input[name="npwp"]').mask('00.000.000.0-000.000', {reverse: true});

$('.selectpicker').attr('data-live-search', 'true');
$('.selectpicker').selectpicker('refresh');

$('#kategori').on('change', function() {
	if(this.value == 1){
		$('.nama_perusahaan').html('<label class="control-label col-md-3">Nama Perusahaan</label><div class="col-md-9"><input name="nama_perusahaan" placeholder="Nama Perusahaan" class="form-control" type="text" value="<?=@$data_penyedia->nama_perusahaan; ?>"><span class="help-block"></span></div>');
		$('.jabatan').html('<label class="control-label col-md-3">Jabatan Penyedia/ Penanggung Jawab</label><div class="col-md-9"><input name="jabatan" placeholder="Jabatan Penyedia/ Penanggung Jawab" class="form-control" type="text" value="<?=@$data_penyedia->jabatan; ?>"><span class="help-block"></span></div>');

		
		<?php 
		if(!empty(@$bukanba)){ 
		?>		
		$(".dataTA").css("visibility", "visible");
		<?php
			} 
		?>  
	}else{
		$('.nama_perusahaan').html('');
		$('.jabatan').html('');
		<?php 
		if(!empty(@$bukanba)){ 
		?>		
		$(".dataTA").css("visibility", "hidden");
		<?php
			} 
		?>  
	}
});

var kategori = "<?=@$data_penyedia->kategori; ?>";
$('#kategori').val(kategori).change();

$('.selectpicker').attr('data-live-search', 'true');
$('.selectpicker').selectpicker('refresh');

function save(){	
	$('.form-group').removeClass('has-error'); // clear error class
	$('#modal_form').modal('show'); // show bootstrap modal
	$('.modal-title').text('Konfirmasi'); // Set Title to Bootstrap modal title
	$("#btnModal_proses").attr("onclick","save_proses()");
	$('#btnModal_proses').text('Proses'); // Set Title to Bootstrap modal title
	$('#kalimat_tampil').html('Apakah Anda yakin <b>Simpan Data</b> ini ?'); // Add Teks
}


<?php if(empty(@$bukanba)){ ?>  						
function save_proses()
{
    // $('#btnModal_proses').attr('disabled',true); //set button disable 
    $('#btnModal_cancel').attr('disabled',true); //set button disable 
	
    $('#btnSave').text('saving...'); //change button text
    // $('#btnSave').attr('disabled',true); //set button disable 
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
			$('#modal_form').modal('hide');			
			$('#btnModal_proses').attr('disabled',false);
			$('#btnModal_cancel').attr('disabled',false);
			
			
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
<?php } ?>
</script>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">					
			<div class="panel-body">
				<form action="#" id="form<?php if(!empty(@$bukanba)){ echo 'perusahaan'; } ?>" class="form-horizontal">
					<input type="hidden"  value="<?=@$data_penyedia->id_penyedia; ?>" name="id"/> 
					<div class="form-body">
						<div class="form-group">
							<label class="control-label col-md-3">Kategori Penyedia</label>
							<div class="col-md-9">
								<select class="form-control selectpicker show-tick" name="kategori" id="kategori" data-live-search="true" required="required">
									<option value="0">
										Perorangan
									</option>
									<option value="1">
										Perusahaan
									</option>
								</select>
							</div>
						</div>
						<div class="form-group nama_perusahaan">							
						</div>
						
						<?php 
						if(!empty(@$bukanba)){ 
						?>							
							<div class="form-group niktambahan">
								<label class="control-label col-md-3">NIK </label>
								<div class="col-md-9">
									<input name="nik" placeholder="NIK Penyedia/ Penanggung Jawab" class="form-control" type="text" value="<?=@$data_penyedia->nik; ?>">
									<span class="help-block"></span>
								</div>
							</div>
						<?php
						 } 
						 ?>  
						<div class="form-group">
							<label class="control-label col-md-3">Nama Penyedia/ Penanggung Jawab</label>
							<div class="col-md-9">
								<input name="nama_penyedia" placeholder="Nama Penyedia/ Penanggung Jawab" class="form-control" type="text" value="<?=@$data_penyedia->nama_penyedia; ?>">
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group jabatan">
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Alamat</label>
							<div class="col-md-9">
								<textarea name="alamat" placeholder="Alamat Lengkap" class="form-control"><?=@$data_penyedia->alamat; ?></textarea>
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Email</label>
							<div class="col-md-9">
								<input name="email" placeholder="Email" class="form-control" type="email" value="<?=@$data_penyedia->email; ?>">
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">NPWP</label>
							<div class="col-md-9">
								<input name="npwp" placeholder="NPWP" class="form-control" type="text" value="<?=@$data_penyedia->npwp; ?>">
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group dihide">
							<label class="control-label col-md-3">BANK</label>
							<div class="col-md-9">
								<input name="bank" placeholder="Bank" class="form-control" type="text" value="<?=@$data_penyedia->bank; ?>">
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group dihide">
							<label class="control-label col-md-3">No Rekening</label>
							<div class="col-md-9">
								<input name="no_rekening_penyedia" placeholder="No Rekening Penyedia" class="form-control" type="text" value="<?=@$data_penyedia->no_rekening_penyedia; ?>">
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group dihide">
							<label class="control-label col-md-3">Atas Nama Rekening</label>
							<div class="col-md-9">
								<input name="atas_nama_rekening" placeholder="Atas Nama Rekening" class="form-control" type="text" value="<?=@$data_penyedia->atas_nama_rekening; ?>">
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group dihide">
							<label class="control-label col-md-3">Cabang BANK</label>
							<div class="col-md-9">
								<input name="cabang_bank" placeholder="Cabank BANK" class="form-control" type="text" value="<?=@$data_penyedia->cabang_bank; ?>">
								<span class="help-block"></span>
							</div>
						</div>
						
						<?php if(!empty(@$bukanba)){ 
							echo $formtambahan;
						 } ?>  
					</div>
				</form>
				<div class="modal-footer">
					
					<?php if(empty(@$bukanba)){ ?>  						
						<button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
					<?php } ?>
					<!--<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>-->
				</div>
			</div>
		</div>
	</div>
</div>