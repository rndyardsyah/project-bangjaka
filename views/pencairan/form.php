<script>
var pagu = 0;

$("input").change(function(){
	$(this).parent().parent().removeClass('has-error');
	$(this).next().empty();
});

$("textarea").change(function(){
	$(this).parent().parent().removeClass('has-error');
	$(this).next().empty();
});		

function nomData(){
	
	$('input[name="pembayaran_termin[]"]').each(function(event) {	
		if($(this).val()){
			$('input[name="pembayaran_termin[]"]:eq('+event+')').parent().parent().removeClass('has-error');
			$('input[name="pembayaran_termin[]"]:eq('+event+')').next().empty();
		}
	});
}

/* $('[name="pembayaran_termin[]"]').change(function () {
    alert('RENDY')
}); */

$('.money').mask('000,000,000,000,000.00', {reverse: true});
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

$('#id_spk').on('change', function() {
	
	$("#loading-overlay").show();
	$.post("<?php echo base_url('ba/'.$class_name.'/ajax_cek_pagu')?>",
			{id:this.value},
			function(data,status){
				
				$("#loading-overlay").hide();
				var obj = jQuery.parseJSON(data);
				pagu = obj.hasil;
				$(".pagu").html(accounting.formatMoney(pagu));
			}	
	);
});

$('#pekerjaan_termin').change(function(){
	
	var current_pembayaran_termin = $("input[name='pembayaran_termin[]']").length;
	var add_pembayaran_termin = $(this).val();
	
	if(add_pembayaran_termin){
		
		var i;
		
		if(current_pembayaran_termin > 0){
			if(current_pembayaran_termin < add_pembayaran_termin){
				//apabila menambah data di saat sudah ada/ ditegah jalan
				for (i = current_pembayaran_termin+1; i <= add_pembayaran_termin; i++) {
					// text += cars[i] + "<br>";		
					$("#data-nominal_bayar").append('<div class="form-group group-pembayaran_termin'+i+'"><label class="control-label col-md-3">Biaya Pembayaran Termin '+i+'</label><div class="col-md-9"><input onkeyup="myJumlah()" name="pembayaran_termin[]" placeholder="Biaya Pembayaran Termin '+i+'"  onChange="nomData()" class="form-control money" type="text"><span class="help-block"></span></div></div>');
				}
			}else{
				//apabila data di kurangi, maka dihapus
				for (i = current_pembayaran_termin; i > add_pembayaran_termin; i--) {
					// $('input[name="pembayaran_termin[]"]:eq('+i+')').remove(); //remove input array
					$(".group-pembayaran_termin"+i).remove();
				}
				myJumlah();
			}
		}else{
			//apabila menambah data baru
			for (i = 1; i <= add_pembayaran_termin; i++) {
				// text += cars[i] + "<br>";		
				$("#data-nominal_bayar").append('<div class="form-group group-pembayaran_termin'+i+'"><label class="control-label col-md-3">Biaya Pembayaran Termin '+i+'</label><div class="col-md-9"><input onkeyup="myJumlah()" name="pembayaran_termin[]" onChange="nomData()" placeholder="Biaya Pembayaran Termin '+i+'" class="form-control money" type="text"><span class="help-block"></span></div></div>');
			}
		}
		
		
		
	}else{
		$("#data-nominal_bayar").html('');
	}
	
	
});

function myJumlah(){
	var val = 0;
	$('input[name="pembayaran_termin[]"]').each(function() {		
		if($(this).val()){
			val += Number(parseCurrency($(this).val()));
		}
	});
    $('input[name="nominal_bayar"]').val(accounting.formatMoney(val));
    // $('input[name="nominal_bayar"]').val(val);
}

function parseCurrency( num ) {
	if(num){
		return parseFloat( num.replace( /,/g, '') );
	}
}

function save()
{
    $('#btnSave').text('saving...'); //change button text
    $('#btnSave').attr('disabled',true); //set button disable 
    var url;
	url = "<?php echo base_url('ba/'.$class_name.'/ajax_save')?>";
	
	/* var nominal_bayar = $("[name='nominal_bayar']").val();
	if(parseCurrency(nominal_bayar) > pagu){
		$('div.alert').remove();
		$("#message").html('<div class="alert alert-danger" role="alert"><i class="fa fa-thumbs-o-down fa-fw"></i> Data Nominal Biaya Lebih dari Pagu!</div>');
		$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');			
		$(".alert").delay(2000).slideUp(700, function() 
		{	
			$("[name='nominal_bayar']").focus();
		});
				
		$('#btnSave').text('save'); //change button text
		$('#btnSave').attr('disabled',false); //set button enable 
		return false;
	} */
	
    // ajax adding data to database
	$("#loading-overlay").show();
    var formData = new FormData($('#form')[0]);
	formData.append('pagu', pagu);
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
			
            $('#btnSave').text('save'); //change button text
            $('#btnSave').attr('disabled',false); //set button enable 
            if(data.status) //if success close modal and reload ajax table
            {				
				$('#page-wrapper-form').html(''); //reset / menghilangkan form menu dalam div 
				$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');			
				$(".page-header").html('Data Pengajuan Pencairan <div class="btn-group pull-right tombl"></div>');
				
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
                    if(data.inputerror[i].includes("pembayaran_termin")){						
						
						// $('input[name="pembayaran_termin[]"]:eq('+i+')').remove(); //remove input array
						var angka = data.inputerror[i].substring(18);
						$('input[name="pembayaran_termin[]"]:eq('+angka+')').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
						$('input[name="pembayaran_termin[]"]:eq('+angka+')').next().text(data.error_string[i]); //select span help-block class set text error string
					}else{
						$('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
						if(data.inputerror[i] == 'id_penyedia' || data.inputerror[i] == 'id_spk' ||  data.inputerror[i] == 'pekerjaan_termin'){
							$('[name="'+data.inputerror[i]+'"]').next().next().text(data.error_string[i]); //select span help-block class set text error string						
						}else{
							$('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
						}						
					}
                }
            }
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
										<div class="pagu"></div>
									</div>
								</div>								
								<div class="form-group">
									<label class="control-label col-md-3">Termin Pekerjaan</label>
									<div class="col-md-9">
										<select class="form-control selectpicker show-tick" name="pekerjaan_termin" id="pekerjaan_termin" data-live-search="true" required="required">
										<option value="">Silahkan Pilih</option>
										<?php for($i=1; $i<=12; $i++){?>
										<option value="<?php echo $i; ?>"><?php echo $i; ?> Termin</option>
										<?php }?>
									</select>
									<span class="help-block"></span>
									</div>
								</div>					
								<div class="form-group">
									<label class="control-label col-md-3">Nominal Biaya Kontrak</label>
									<div class="col-md-9">
										<input name="nominal_bayar" placeholder="Nominal Biaya" class="form-control money" type="text" value="<?php if(@$data_penyedia->nominal_bayar){echo @$data_penyedia->nominal_bayar;}else{ echo 0;} ?>" ReadOnly>
										<span class="help-block"></span>
									</div>
								</div>
								<div id="data-nominal_bayar">
								
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