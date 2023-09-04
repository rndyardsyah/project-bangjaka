<script>

var table_berkas;

$("input").change(function(){
	$(this).parent().parent().removeClass('has-error');
	$(this).next().empty();
});
$("textarea").change(function(){
	$(this).parent().parent().removeClass('has-error');
	$(this).next().empty();
});


$('.selectpicker').attr('data-live-search', 'true');
$('.selectpicker').selectpicker('refresh');

function save_berkas(){
	$('#btnSave').attr('disabled',true);
	$("#loading-overlay").show();
    var formData = new FormData($('#form-berkas')[0]);
	formData.append('id_spk', <?php echo @$id_spk; ?>);
	$.ajax({
		url : "<?php echo base_url('ba/pemberkasan/ajax_save')?>/",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "JSON",
        success: function(data)
		{
			//if success reload ajax table			
			$("#loading-overlay").hide();
			// $('div.alert-modal').remove();			
			
			$("#message").html(data.notif);
			$('#btnSave').attr('disabled',false);
			if(data.status)
			{
				$('#form-berkas')[0].reset(); // reset form on modals				
				$('.selectpicker').attr('data-live-search', 'true');
				$('.selectpicker').selectpicker('refresh');
				
				$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');			
				$(".alert").delay(2000).slideUp(500, function() {	
				
				});
			}else{
				for (var i = 0; i < data.inputerror.length; i++) 
				{
					$('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
					if(data.inputerror[i] == 'id_berkas'){
						$('[name="'+data.inputerror[i]+'"]').next().next().text(data.error_string[i]); //select span help-block class set text error string						
					}
					else{
						$('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
					}
				}
			}
			reload_table_berkas();
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error deleting data');		
			$("#loading-overlay").hide();
            $('#btnSave').attr('disabled',false); //set button enable 
		}
	});
}

$(document).ready(function() {	
	
	
	$("#loading-overlay").hide();
	
    //datatables
    table_berkas = $('#table_berkas').DataTable({ 

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
		// "responsive": true,
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo base_url('ba/'.$class_name.'/ajax_list')?>",
            "type": "POST",
			"data": function ( data ) {
                data.id_spk = <?php echo @$id_spk; ?>;
            }
        },
		/* "columns": [
            // { "data": null,
				// "render": function (data, type, row, meta) {
					// return meta.row + meta.settings._iDisplayStart + 1;
				// }
			// },
            { "data": 0},
            { "data": 1},
            { "data": 2}
            
        ], */

        //Set column definition initialisation properties.
        "columnDefs": [
            { 
                "targets": [ 0 ], //2 last column (photo)
                "orderable": false, //set not orderable
            },
        ],
		
		"language": {
			"url": "<?php echo base_url('assets/datatables/i18n/Indonesian.json');?>"
		} 

    });
	
});

function reload_table_berkas()
{
    table_berkas.ajax.reload(null,false); //reload datatable ajax 
}

function delete_berkas(id)
{
	$('[name="id"]').val(id);
	$('#formModal')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
	$('#modal_form').modal('show'); // show bootstrap modal
	$('.modal-title').text('Hapus Data'); // Set Title to Bootstrap modal title
	$("#btnModal_proses").attr("onclick","delete_proses_berkas()");
	$('#btnModal_proses').text('Proses'); // Set Title to Bootstrap modal title
	$('#kalimat_tampil').html('Apakah Anda yakin <b>Hapus Data</b> ini ?'); // Add Teks
}


function delete_proses_berkas()
{
	
    $('#btnModal_proses').attr('disabled',true); //set button disable 
	
	$('div.alert-modal').remove();	
	$("#loading-overlay").show();
    var formData = new FormData($('#formModal')[0]);
	$.ajax({
		url : "<?php echo base_url('ba/'.$class_name.'/ajax_delete')?>/",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "JSON",
        success: function(data)
		{
			//if success reload ajax table			
			$("#loading-overlay").hide();			
			$('#kalimat_tampil').html(''); // Add Teks	
			$("#message-modal").html(data.notif);
			$('#btnModal_proses').attr('disabled',true);
			$('#btnModal_cancel').attr('disabled',true);
			if(data.status)
			{
				$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');			
				$(".alert").delay(2000).slideUp(500, function() {	
					$('#modal_form').modal('hide');			
					$('#btnModal_proses').attr('disabled',false);
					$('#btnModal_cancel').attr('disabled',false);
				});
			}else{
				$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');			
				$(".alert").delay(2000).slideUp(500, function() {
					$('#modal_form').modal('hide');			
					$('#btnModal_proses').attr('disabled',false);
					$('#btnModal_cancel').attr('disabled',false);
				});
			}
			reload_table_berkas();
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error deleting data');
            $('#btnModal_proses').attr('disabled',false); //set button enable 
		}
	});
}

</script>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">					
			<div class="panel-body">
				<form action="#" id="form-berkas" class="form-horizontal">
					<input type="hidden"  value="<?=@$data_penyedia->id_berkas; ?>" name="id"/> 
					<div class="form-body">
						
						<div class="form-group">
							<label class="control-label col-md-3">Jenis Berkas</label>
							<div class="col-md-9">
								<?php echo getformselect('m_berkas', 'id_berkas', 'nama_berkas','active="1"', false, false,'sort asc'); ?>
								
							</div>
						</div>						
						<div class="form-group">
							<label class="control-label col-md-3">Keterangan</label>
							<div class="col-md-9">
								<input name="keterangan" placeholder="Keterangan" class="form-control" type="text" value="<?=@$data_penyedia->keterangan; ?>">
								<span class="help-block"></span>
							</div>
						</div>
                        <div class="form-group">
                            <label class="control-label col-md-3" id="label-photo">Upload File <br><i><font color="red">*Format .pdf, maks 10Mb</font></i> </label>
                            <div class="col-md-9">
                                <input name="file" id="file" type="file" class="form-control" accept="application/pdf">
                                <span class="help-block"></span>
                            </div>
                        </div>
					</div>
				</form>
				<div class="modal-footer">
					<button type="button" id="btnSave" onclick="save_berkas()" class="btn btn-primary">Save</button>
					<!--<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>-->
				</div>
			</div>
		</div>
	</div>
</div>


<div class="row">
	<div class="col-lg-12">
		<!-- <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%"> -->
			<div class="panel panel-default" id="panel">					
				<div class="panel-body" id="panel-body">
					<table id="table_berkas" class="table table-hover table-striped " cellspacing="0" width="100%" >
						<thead>
							<tr>
								<th width="10%">Aksi</th>
								<th>Nama Berkas</th>
								<th>Keterangan</th>
								<th width="5%">File</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
	</div>
	<!-- /.col-lg-12 -->
</div>