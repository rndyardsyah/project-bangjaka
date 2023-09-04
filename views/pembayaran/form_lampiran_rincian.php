<div class="form-group">
	<label class="control-label col-md-3">Jenis Pekerjaan</label>
	<div class="col-md-9">
		<input type="text" required="required" id="uraian_dpa"  name="uraian_dpa" class="form-control" placeholder="Jenis Uraian Pekerjaan" value="<?php if(!empty($data_rincian[0]['uraian'])){ echo $data_rincian[0]['uraian']; }else{ echo $data_rincian[0]['uraian_dpa']; } ?>"/>
		<span class="help-block"></span>
	</div>
</div>																						
<div class="form-group">
	<label class="control-label col-md-3">Satuan</label>
	<div class="col-md-9">
		<input type="text" required="required" id="satuan"  name="satuan" class="form-control" placeholder="Satuan" value="<?=@$data_rincian[0]['satuan']; ?>"/>
		<span class="help-block"></span>
	</div>
</div>															
<div class="form-group">
	<label class="control-label col-md-3">Volume</label>
	<div class="col-md-9">
		<input type="text" required="required" id="volume"  name="volume" class="form-control money" placeholder="Volume" value="<?=@$data_rincian[0]['volume']; ?>"/>
		<span class="help-block"></span>
	</div>
</div>															
<div class="form-group">
	<label class="control-label col-md-3">Harga Satuan</label>
	<div class="col-md-9">
		<input type="text" required="required" id="harga_satuan"  name="harga_satuan" class="form-control money" placeholder="Harga Satuan" value="<?=@$data_rincian[0]['harga_satuan']; ?>"/>
		<span class="help-block"></span>
	</div>
</div>														
<div class="form-group">
	<label class="control-label col-md-3">Jumlah Harga</label>
	<div class="col-md-9">
		<input type="text" required="required" id="jumlah_harga_satuan"  name="jumlah_harga_satuan" class="form-control money" placeholder="Jumlah" value="<?=@$data_rincian[0]['jumlah_harga_satuan']; ?>"/>
		<span class="help-block"></span>
	</div>
</div>

<script>
$('#idpembayaran').val(id_pembayarannya);
$("input").change(function(){
	$(this).parent().parent().removeClass('has-error');
	$(this).next().empty();
});
function save_rincian()
{	
	
	$('#btnModal_proses').text('saving...'); //change button text
    $('#btnModal_proses').attr('disabled',true); //set button disable 
	$('#btnModal_cancel').attr('disabled',true); //set button disable 
    var url;
	url = "<?php echo base_url('ba/'.$class_name.'/ajax_save')?>";

    // ajax adding data to database
	$("#loading-overlay").show();
    var formData = new FormData($('#modal_form #formModal')[0]);	
	formData.append('id_pembayaran', id_pembayarannya);
	formData.append('id_rincian_detail_spk', '<?php if(!empty($data_rincian[0]['id'])){ echo $data_rincian[0]['id']; }else{ echo $data_rincian[0]['id_rincian_detail_spk']; } ?>');
	formData.append('id_pembayaran_rinci', '<?=@$data_rincian[0]['id_pembayaran_rinci']; ?>');
	
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
			$('#modal_form').modal('hide');	
			$("#message").html(data.notif);
            if(data.status) //if success close modal and reload ajax table
            {				
				$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');		
				$(".alert").delay(2000).slideUp(500, function() {	
					// $('#form-tambahan').html('');
				});
            }
            else
            {
				$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');	
                for (var i = 0; i < data.inputerror.length; i++) 
                {
					$('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                    $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
                }
            }
            $('#btnModal_proses').text('save'); //change button text
			$('#btnModal_proses').attr('disabled',false);
			$('#btnModal_cancel').attr('disabled',false);


        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error adding / update data');
            $("#loading-overlay").hide();
			$('#btnModal_proses').text('save'); //change button text
            $('#btnModal_proses').attr('disabled',false); //set button enable 
			$('#btnModal_cancel').attr('disabled',false);

        }
    });
}
</script>