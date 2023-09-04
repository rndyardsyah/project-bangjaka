<style>
#input_rincian_kegiatan .form-control[disabled]{
	cursor: pointer;
	background-color: transparent;
}
</style>

<script>
$("#cek_nip").click(function(){
	
	var nip = $('[name="nip"]').val();
	
	$("#loading-overlay").show();
	$.post("<?php echo base_url('ba/'.$class_name.'/cek_nip')?>",
			{nip:nip},
			function(data,status){
				
				$("#loading-overlay").hide();
				var obj = jQuery.parseJSON(data);
				$('[name="nama_user"]').val(obj.nama_pegawai);
				$('[name="kode_unor"]').val(obj.kode_unor);
				$('[name="unor"]').val(obj.nama_unor);
				$('[name="nip_baru"]').val(obj.nip);
				$('[name="id_pegawai"]').val(obj.id_pegawai);
			}	
	);

});

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

function ValidateEmail(email) {
	
	var expr = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	return expr.test(email);
	
};

function save(){	
	$('.form-group').removeClass('has-error'); // clear error class
	$('#modal_form').modal('show'); // show bootstrap modal
	$('.modal-title').text('Konfirmasi'); // Set Title to Bootstrap modal title
	$("#btnModal_proses").attr("onclick","save_proses()");
	$('#btnModal_proses').text('Proses'); // Set Title to Bootstrap modal title
	$('#kalimat_tampil').html('Apakah Anda yakin <b>Simpan Data</b> ini ?'); // Add Teks
}

function save_proses()
{
    $('#btnSave').text('saving...'); //change button text
    $('#btnSave').attr('disabled',true); //set button disable 
    var url;
	url = "<?php echo base_url('ba/'.$class_name.'/ajax_save')?>";
	
	if($("#email").val() != ''){
		if (!ValidateEmail($("#email").val())) {
			$('#modal_form').modal('hide');			
			$('#btnModal_proses').attr('disabled',false);
			$('#btnModal_cancel').attr('disabled',false);
			
			$('div.alert').remove();
			$("#message").html('<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Invalid email address</div>');
			$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');
			$(".alert").delay(2000).slideUp(1000, function() {	
				
			});	
			$('#btnSave').text('save'); //change button text
			$('#btnSave').attr('disabled',false); //set button enable 
			$( "#email" ).focus(function() {
				
			});
			return false;
		}
	}
	
	
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
				<form action="#" id="form" class="form-horizontal" autocomplete="off">
					<input type="hidden"  value="<?php echo @$data_user->id; ?>" name="id"/> 
					<input type="hidden"  value="<?php echo @$data_user->nip_baru; ?>" name="nip_baru"/> 
					<input type="hidden"  value="<?php echo @$data_user->kode_unor; ?>" name="kode_unor"/> 
					<input type="hidden"  value="<?php echo @$data_user->id_pegawai; ?>" name="id_pegawai"/> 
					<div class="form-body">
						<?php $display = (!empty(@$data_user)) ? 'style="display: none;"':''; ?>
						<div class="form-group" <?php echo $display; ?>>
							<label class="control-label col-md-3">NIP</label>
							<div class="col-md-7">
								<input name="nip" placeholder="NIP" class="form-control" type="text" value="">
								<span class="help-block"></span>
							</div>
							<div class="col-sm2">
							  <button name="cek_nip" id="cek_nip" class="btn btn-primary" onclick="return false;"><i class="fa fa-refresh fa-fw fa-refresh"></i> CEK NIP</button>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Nama Pegawai</label>
							<div class="col-md-9">
								<input name="nama_user" placeholder="Nama Pegawai" class="form-control" type="text" value="<?php echo @$data_user->nama_user; ?>" ReadOnly>
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">UNOR</label>
							<div class="col-md-9">
								<input name="unor" placeholder="Unor" class="form-control" type="text" value="<?php if(@$data_user->kode_unor){ 
								$unor = getByKodeUnor(@$data_user->kode_unor); 

								echo @$unor['nama_unor'];
								} ?>" ReadOnly>
								<span class="help-block"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3">Email</label>
							<div class="col-md-9">
								<input name="email" id="email" placeholder="Email" class="form-control" type="email" value="<?php echo @$data_user->email; ?>">
								<span class="help-block"></span>
							</div>
						</div>
						
						<div class="form-group">
							<label class="control-label col-md-3">Status</label>
							<div class="col-md-9">
								<select class="form-control selectpicker show-tick" name="status" id="status" data-live-search="true">
									<option value="" >Silahkan Pilih</option>
									<option value="0" <?php if(@$data_user->status == '0'){ echo 'selected'; } ?>>TIDAK AKTIF</option>
									<option value="1" <?php if(@$data_user->status == '1'){ echo 'selected'; } ?>>AKTIF</option>
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
