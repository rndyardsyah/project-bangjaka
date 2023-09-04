<link href="<?php echo base_url('assets/form_wizard/form-wizard.css'); ?>" rel="stylesheet" type="text/css" >
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

$('.datetimePicker #tgl_permohonan_pembayaran, #tgl_ba_pembayaran').datetimepicker(
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

function getData(id_pencairan){
	//kirim data pencairan ke data hasil pekerjaan untuk mendapatkan termin
	// alert(id_pencairan);
	$("#loading-overlay").show();
	
	$('.data-termin').load('<?=base_url('ba/'.$class_name.'/getDataListTermin');?>', {id_pencairan:id_pencairan}, function(data, status) 
		{
			$("#loading-overlay").hide();			
			$('.selectpicker').attr('data-live-search', 'true');
			$('.selectpicker').selectpicker('refresh');
		}
	);
	
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
                    if(data.inputerror[i] == 'id_penyedia' || data.inputerror[i] == 'id_spk'){
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
</script>


<div class="row">
	<div class="col-lg-12">
		<div class="stepwizard">
			<div class="stepwizard-row setup-panel">
				<div class="stepwizard-step">
					<a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
					<p>Step 1</p>
				</div>
				<div class="stepwizard-step">
					<a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
					<p>Step 2</p>
				</div>
				<div class="stepwizard-step">
					<a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
					<p>Step 3</p>
				</div>
			</div>
		</div>
		<form action="#" id="form" class="form-horizontal">
			<div class="row setup-content" id="step-1">
				<div class="col-xs-12">
					<div class="col-md-12">
						<h3> Step 1</h3>		
						<input type="hidden" id="id" name="id" />
						<div class="form-group">
							<label class="control-label">Data Kontrak</label>
							<?=getformselect_onclick('t_pencairan','id_pencairan','no_spk','status = 1'); ?>
						</div>
						<div class="form-group data-termin">							
						</div>
						<button class="btn btn-primary nextBtn btn-lg pull-right" type="button" >Next</button>
					</div>
				</div>
			</div>
			<div class="row setup-content" id="step-2">
				<div class="col-xs-12">
					<div class="col-md-12">
						<h3> Step 2</h3>
						<div class="form-group">
							<label class="control-label">No Permohonan Pembayaran</label>
							<input maxlength="200" type="text" required="required" id="no_permohonan_pembayaran" name="no_permohonan_pembayaran" class="form-control" placeholder="Nomor Permohonan Pembayaran" />
						</div>
						<div class="form-group">
							<label class="control-label">Tanggal Permohonan Pembayaran</label>
							<div class="dateContainer">	
								<div class="input-group date datetimePicker" id="tgl_permohonan_pembayaran">
								<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
									<input class="form-control" name="tgl_permohonan_pembayaran" id="tgl_permohonan_pembayaran" value="23-07-2018" placeholder="DD-MM-YYYY" data-date-format="DD-MM-YYYY" type="text">
								 </div>
							</div>
						</div>
						<button class="btn btn-primary nextBtn btn-lg pull-right" type="button" >Next</button>
					</div>
				</div>
			</div>
			<div class="row setup-content" id="step-3">
				<div class="col-xs-12">
					<div class="col-md-12">
						<h3> Step 3</h3>
						<div class="form-group">
							<label class="control-label">No Berita Acara Pembayaran</label>
							<input maxlength="200" type="text" required="required" id="no_ba_pembayaran"  name="no_ba_pembayaran" class="form-control" placeholder="Nomor Permohonan Pembayaran" />
						</div>
						<div class="form-group">
							<label class="control-label">Tanggal Berita Acara Pembayaran</label>
							<div class="dateContainer">	
								<div class="input-group date datetimePicker" id="tgl_ba_pembayaran">
								<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
									<input class="form-control" name="tgl_ba_pembayaran" id="tgl_ba_pembayaran" value="23-07-2018" placeholder="DD-MM-YYYY" data-date-format="DD-MM-YYYY" type="text">
								 </div>
							</div>
						</div>
						<button class="btn btn-success btn-lg pull-right" type="button" onclick="save()">Finish!</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>