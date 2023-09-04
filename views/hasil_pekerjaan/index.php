<link href="<?php echo base_url('assets/js/bootstrap-select/css/bootstrap-select.min.css'); ?>" rel="stylesheet" type="text/css" >
<link href="<?php echo base_url('assets/css/custom-heading.css'); ?>" rel="stylesheet" type="text/css" >
<link href="<?php echo base_url('assets/css/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css'); ?>" rel="stylesheet" type="text/css" >
<link href="<?php echo base_url('assets/css/dropdown-submenu.css'); ?>" rel="stylesheet" type="text/css" >

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
<script src="<?php echo base_url('assets/js/plugins/bootstrap-datetimepicker/moment.min.js')?>"></script>
<script src="<?php echo base_url('assets/js/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js')?>"></script>

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
.imageThumb {
  max-height: 55px;
  border: 2px solid;
  padding: 1px;
  cursor: pointer;
}
</style>



<div class="row">
	<div class="col-lg-12">
		<div id="message"  style="clear:both; margin-top: 10px;"></div>
		<h3 class="page-header">Data Pengajuan Hasil Pekerjaan
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
									<th>Data Penyedia</th>
									<th>Data Kontrak</th>
									<th>Status</th>
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
var id_pencairan_post = 0;


$(document).ready(function() {	
	
	$("#loading-overlay").hide();
	// $(".tombl").html('<button class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" type="button"><i class="fa fa-cogs fa-fw"></i></button><ul class="dropdown-menu slidedown"><li><a href="javascript:void(0)" onclick="add_hasil_pekerjaan()"><i class="fa fa-folder-o fa-fw"></i> Tambah </a></li></ul>');
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
                "targets": [ -1 ], //2 last column (photo)
                "orderable": false, //set not orderable
            },
        ],
		
		"language": {
			"url": "<?php echo base_url('assets/datatables/i18n/Indonesian.json');?>"
		} 

    });
	
    //check all
    $("#check-all").click(function () {
        $(".data-check").prop('checked', $(this).prop('checked'));
    });
	
	
});


function bulk_delete()
{
    var list_id = [];
    $(".data-check:checked").each(function() {
            list_id.push(this.value);
    });
    if(list_id.length > 0)
    {
        if(confirm('Are you sure delete this '+list_id.length+' data?'))
        {
            $.ajax({
                type: "POST",
                data: {id:list_id},
                url: "<?php echo site_url('ba/'.$class_name.'/ajax_bulk_delete')?>",
                dataType: "JSON",
                success: function(data)
                {
                    if(data.status)
                    {
                        reload_table();
                    }
                    else
                    {
                        alert('Failed.');
                    }
                    
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    alert('Error deleting data');
                }
            });
        }
    }
    else
    {
        alert('no data selected');
    }
}

function reload_table()
{
    table.ajax.reload(null,false); //reload datatable ajax 
}

function kembali()
{
	$("#loading-overlay").show();
	$(".page-header").html('Data Pengajuan Hasil Pekerjaan <div class="btn-group pull-right tombl"></div>');
	// $(".tombl").html('<button class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" type="button"><i class="fa fa-cogs fa-fw"></i></button><ul class="dropdown-menu slidedown"><li><a href="javascript:void(0)" onclick="add_penyedia()"><i class="fa fa-folder-o fa-fw"></i> Tambah </a></li></ul>');
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
			$(".page-header").html('Data Pengajuan Hasil Pekerjaan <small>Tambah Data</small><div class="btn-group pull-right tombl"></div>');
			$(".tombl").html('<a href="javascript:void(0)" onclick="kembali()" class="btn btn-primary"><i class="glyphicon glyphicon-backward"></i> kembali</a>');
			$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');	
			// var returnedData = JSON.parse(data);
		}
	);
}

function add_hasil_pekerjaan(id)
{
	$("#loading-overlay").show();
	$('#page-wrapper-form').load('<?=base_url('ba/hasil_pekerjaan/ajax_form');?>', {id_pencairan:id}, function(data, status) 
		{
			$("#loading-overlay").hide();
			$("#page-wrapper-datatable").hide();
			$('[name="id_pencairan"]').val(id);
			$(".page-header").html('Data Pengajuan Hasil Pekerjaan <small>Hasil Pekerjaan -> Tambah Data</small><div class="btn-group pull-right tombl"></div>');
			$(".tombl").html('<a href="javascript:void(0)" onclick="kembali()" class="btn btn-primary"><i class="glyphicon glyphicon-backward"></i> kembali</a>');
			$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');	
			// var returnedData = JSON.parse(data);
		}
	);
}

function read(id_inbox, id_read, keterangan, kk)
{
	$("#loading-overlay").show();
	$('#page-wrapper-form').load('<?=base_url('ba/inbox/ajax_read');?>', {id_inbox:id_inbox,id_read:id_read,keterangan:keterangan,kk:kk}, function(data, status) 
		{
			$("#loading-overlay").hide();
			$("#page-wrapper-datatable").hide();
			// $('[name="id_pencairan"]').val(id);
			$(".page-header").html('Data Pengajuan Hasil Pekerjaan <div class="btn-group pull-right tombl"></div>');
			$(".tombl").html('<a href="javascript:void(0)" onclick="kembali()" class="btn btn-primary"><i class="glyphicon glyphicon-backward"></i> kembali</a>');
			$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');	
			// var returnedData = JSON.parse(data);
		}
	);
}

function cek_status_draft(id, id_inbox, id_read, keterangan, kk){
	
	 $.ajax({
		type: "GET",
		data: {id:id},
		url: "<?php echo site_url('ba/'.$class_name.'/cek_draft')?>",
		dataType: "JSON",
		success: function(data)
		{
			if(data.status)
			{
				if(data.draft == 0 || data.draft == 2){ //0 draf, 2 perbaikan
					edit_penyedia(id);
				}
				
				if(data.draft == 1){
					read(id_inbox, id_read, keterangan, kk);
				}
			}
			else
			{
				alert('Failed.');
			}
			
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error deleting data');
		}
	});
}

function edit_penyedia(id)
{
	$("#loading-overlay").show();
	$('#page-wrapper-form').load('<?=base_url('ba/'.$class_name.'/ajax_form');?>', {id_hasil_pekerjaan:id}, function(data, status) 
		{
			$("#loading-overlay").hide();
			$("#page-wrapper-datatable").hide();
			$(".page-header").html('Data Pengajuan Hasil Pekerjaan <small>Ubah Data</small><div class="btn-group pull-right tombl"></div>');
			$(".tombl").html('<a href="javascript:void(0)" onclick="kembali()" class="btn btn-primary"><i class="glyphicon glyphicon-backward"></i> kembali</a>');
			$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');				
			$("#termin").attr('disabled',true); 
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
	$('#btnModal_proses').show(); // Set Title to Bootstrap modal title
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