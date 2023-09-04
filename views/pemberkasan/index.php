<link href="<?php echo base_url('assets/js/bootstrap-select/css/bootstrap-select.min.css'); ?>" rel="stylesheet" type="text/css" >

<link href="<?php echo base_url('assets/datatables/css/dataTables.bootstrap.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/datatables/css/dataTables.responsive_v2.css'); ?>" rel="stylesheet">
<!-- <link href="<?php echo base_url('assets/datatables/css/dataTables.bootstrap.css'); ?>" type="text/css" rel="stylesheet" /> -->
<link href="<?php echo base_url('assets/datatables/css/fixedHeader.bootstrap.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/datatables/css/responsive.bootstrap.min.css'); ?>" rel="stylesheet">

<script src="<?php echo base_url('assets/datatables/js/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/datatables/js/dataTables.bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/datatables/js/dataTables.fixedHeader.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/datatables/js/dataTables.responsive.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/datatables/js/responsive.bootstrap.min.js'); ?>"></script>	
<script src="<?php echo base_url('assets/js/bootstrap-select/js/bootstrap-select.min.js'); ?>"></script>

<style>
.close {display: none;} /* hide close di modal */
div.dataTables_filter label {
    float: right;
    font-weight: normal;
}
div.dataTables_length label {
    float: left;
    text-align: left;
    font-weight: normal;
}
</style>

<div class="row">
	<div class="col-lg-12">
		<div id="message"  style="clear:both; margin-top: 10px;"></div>
		<h3 class="page-header">Data Pemberkasan
			<div class="btn-group pull-right tombl">
			</div>
		</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div id="page-wrapper-datatable">
	<div class="row">
		<div class="col-lg-12">
			<!-- <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%"> -->
				
				<div class="panel panel-default">					
					<div class="panel-body">
						<table id="table" class="table table-hover table-striped " cellspacing="0" width="100%" >
							<thead>
								<tr>
									<th width="5%">Aksi</th>
									<th>Data Pemberkasan</th>
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
</div>


<script type="text/javascript">

var save_method; //for save method string
var table;
var base_url = '<?php echo base_url();?>';

$(document).ready(function() {	
	
	$("#loading-overlay").hide();
	$(".tombl").html('<button class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" type="button"><i class="fa fa-cogs fa-fw"></i></button><ul class="dropdown-menu slidedown"><li><a href="javascript:void(0)" onclick="add_penyedia()"><i class="fa fa-folder-o fa-fw"></i> Tambah </a></li></ul>');
    //datatables
    table = $('#table').DataTable({ 

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
		// "responsive": true,
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo base_url('ba/'.$class_name.'/ajax_list')?>",
            "type": "POST"
        },
		"columns": [
            // { "data": null,
				// "render": function (data, type, row, meta) {
					// return meta.row + meta.settings._iDisplayStart + 1;
				// }
			// },
            { "data": 0},
            { "data": 1},
            { "data": 2}            
        ],

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

function reload_table()
{
    table.ajax.reload(null,false); //reload datatable ajax 
}

function kembali()
{
	$("#loading-overlay").show();
	$(".page-header").html('Data Pemberkasan <div class="btn-group pull-right tombl"></div>');
	$(".tombl").html('<button class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" type="button"><i class="fa fa-cogs fa-fw"></i></button><ul class="dropdown-menu slidedown"><li><a href="javascript:void(0)" onclick="add_penyedia()"><i class="fa fa-folder-o fa-fw"></i> Tambah </a></li></ul>');
	$('#page-wrapper-form').html(''); //reset / menghilangkan form menu dalam div 
	
	$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');	
	$("#page-wrapper-datatable").show();
	$("#loading-overlay").hide();
}

function add_penyedia()
{
	$("#loading-overlay").show();
	$('#page-wrapper-form').load('<?=base_url('ba/'.$class_name.'/ajax_form');?>', function(data, status) 
		{
			$("#loading-overlay").hide();
			$("#page-wrapper-datatable").hide();
			$(".page-header").html('Data Pemberkasan <small>Tambah Data</small><div class="btn-group pull-right tombl"></div>');
			$(".tombl").html('<a href="javascript:void(0)" onclick="kembali()" class="btn btn-primary"><i class="glyphicon glyphicon-backward"></i> kembali</a>');
			$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');	
			// var returnedData = JSON.parse(data);
		}
	);
}


function edit_penyedia(id)
{
	$("#loading-overlay").show();
	$('#page-wrapper-form').load('<?=base_url('ba/'.$class_name.'/ajax_form');?>', {id_penyedia:id}, function(data, status) 
		{
			$("#loading-overlay").hide();
			$("#page-wrapper-datatable").hide();
			$(".page-header").html('Data Pemberkasan <small>Ubah Data</small><div class="btn-group pull-right tombl"></div>');
			$(".tombl").html('<a href="javascript:void(0)" onclick="kembali()" class="btn btn-primary"><i class="glyphicon glyphicon-backward"></i> kembali</a>');
			$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');				
			// var returnedData = JSON.parse(data);
		}
	);
}

function delete_penyedia(id)
{
	$('[name="id"]').val(id);
	$('#formModal')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
	$('#modal_form').modal('show'); // show bootstrap modal
	$('.modal-title').text('Hapus Data'); // Set Title to Bootstrap modal title
	$("#btnModal_proses").attr("onclick","delete_proses()");
	$('#btnModal_proses').text('Proses'); // Set Title to Bootstrap modal title
	$('#kalimat_tampil').html('Apakah Anda yakin <b>Hapus Data</b> ini ?'); // Add Teks
}

function delete_proses()
{
	
    $('#btnModal_proses').attr('disabled',true); //set button disable 
	
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
			$('div.alert-modal').remove();				
			$('#kalimat_tampil').html(''); // Add Teks	
			$("#message-modal").html(data.notif);
			if(data.status)
			{
				$('#btnModal_proses').attr('disabled',true);
				$('#btnModal_cancel').attr('disabled',true);
				$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');			
				$(".alert").delay(2000).slideUp(500, function() {	
					$('#modal_form').modal('hide');			
					$('#btnModal_proses').attr('disabled',false);
					$('#btnModal_cancel').attr('disabled',false);
				});
			}else{
				$('#btnModal_proses').attr('disabled',false);
			}
			reload_table();
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error deleting data');
            $('#btnModal_proses').attr('disabled',false); //set button enable 
		}
	});
}

</script>


<div id="page-wrapper-form">
</div>


<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">           
		            
		    <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body form">               
				<form action="#" id="formModal" class="form-horizontal" enctype="multipart/form-data">
					<div class="form-body">						
						<div id="message-modal"  style="clear:both; margin-top: 10px;"></div>
						<input type="hidden" name="id">
						<label id="kalimat_tampil"></label>
					</div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnModal_proses" onclick="delete_proses()" class="btn btn-primary">Save</button>
                <button type="button" id="btnModal_cancel" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->