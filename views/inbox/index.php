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


<link rel="stylesheet" href="<?php echo base_url('assets/lobibox-master/Lobibox.min.css"')?>"/>
<script src="<?php echo base_url('assets/lobibox-master/Lobibox.js"')?>"></script>
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
  max-height: 45px;
  border: 2px solid;
  padding: 1px;
  cursor: pointer;
}
</style>
<div class="row">
	<div class="col-lg-12">
		<div id="message"  style="clear:both; margin-top: 10px;"></div>
		<h3 class="page-header">Data Kotak Masuk 
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
					<div class="panel-body form-horizontal">
							 <div class="form-group">
								<div class="col-md-3">
									<div class="form-group">
										<div class="col-md-2">
											<label for="status">Filter</label>
										</div>
										<div class="col-md-10">
											<select class="form-control selectpicker show-tick" name="status" id="status" data-live-search="true" required="required">
												<option value="">Silahkan Pilih</option>
												<option value="1">Belum Terbaca</option>
												<option value="2">Belum Diacc</option>
												<option value="3">Diacc</option>
												<option value="4">Ditolak</option>
											</select>
										</div>
									</div>
								</div>
							</div>
					</div>
				</div>
				
				<div class="panel panel-default">					
					<div class="panel-body">
						<table id="table" class="table table-hover table-striped " cellspacing="0" width="100%" >
							<thead>
								<tr>
									<th>Dari</th>
									<th>Perihal</th>
									<th>Tanggal</th>
									<th><center>Status</center></th>
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
var kodeset;
var base_url = '<?php echo base_url();?>';


var id_tolak; //for save method string
var paraf_tolak; //for save method string
var name_id_tolak; //for save method string
var idinbox; //for save method string

$(document).ready(function() {	
		
	$("#loading-overlay").show();
	// $(".tombl").html('<button class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" type="button"><i class="fa fa-cogs fa-fw"></i></button><ul class="dropdown-menu slidedown"><li><a href="javascript:void(0)" onclick="add_penyedia()"><i class="fa fa-folder-o fa-fw"></i> Tambah </a></li></ul>');
    //datatables
    table = $('#table').DataTable({ 

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
		// "bStateSave": true,
		// "responsive": true,
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo base_url('ba/'.$class_name.'/ajax_list')?>",
            "type": "POST",
			"data": function ( data ) {
                data.status = $('#status').val();
            },
			'beforeSend': function (request) {
				$("#loading-overlay").show();
			},
			"dataSrc" : function ( json ) {
				$("#loading-overlay").hide();
				return json.data;
			}
        },
		"columns": [
            // { "data": null,
				// "render": function (data, type, row, meta) {
					// return meta.row + meta.settings._iDisplayStart + 1;
				// }
			// },
            { "data": 0},
            { "data": 1},
            { "data": 2},
            { "data": 3}
            
        ],

        //Set column definition initialisation properties.
        "columnDefs": [
            { 
                "targets": [ 3 ], //2 last column (photo)
                "orderable": false, //set not orderable
            },
        ],
		
		"initComplete":function( settings, json){
            var data_id_inbox = '<?php echo $id_inbox; ?>';
			var data_id_read = '<?php echo $id_read; ?>';
			var data_keterangan = '<?php echo $keterangan; ?>';
			var data_kk = '<?php echo $kk; ?>';
			
			$("#loading-overlay").hide();
			if(data_id_inbox){
				read_view_all(data_id_inbox, data_id_read, data_keterangan, data_kk);
			}

			var api = this.api();
			$('#table_filter input')
			.off('.DT')
			.on('keyup.DT', function (e) {
				if (e.keyCode == 13) {
					api.search(this.value).draw();
				}
			});
        },
		  "drawCallback": function() {
			table.state.clear();
		  },
		
		"language": {
			"url": "<?php echo base_url('assets/datatables/i18n/Indonesian.json');?>"
		} 

    });
	
});

$('#status').on('change', function() {
  reload_table();
});

function createspk_prakontrak(id_surat)
{
	$("#loading-overlay").show();
	$.redirect('<?=base_url('ba/spk');?>',{id_surat:id_surat});
}

function reload_table()
{
    table.ajax.reload(null,false); //reload datatable ajax 
}

function kembali()
{
	$("#loading-overlay").show();
	$(".page-header").html('Data Kotak Masuk <div class="btn-group pull-right tombl"></div>');
	$('#page-wrapper-form').html(''); //reset / menghilangkan form menu dalam div 
	
	$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');	
	reload_table();
	get_total_inbox();
	$("#page-wrapper-datatable").show();
	$("#loading-overlay").hide();
}

function read_view_all(id_inbox, id_read, keterangan, kk)
{
	$("#loading-overlay").show();
	$('#page-wrapper-form').load('<?=base_url('ba/'.$class_name.'/ajax_read');?>', {id_inbox:id_inbox, id_read:id_read, keterangan:keterangan}, function(data, status) 
		{
			get_total_inbox();
			$("#loading-overlay").hide();
			$("#page-wrapper-datatable").hide();
			// $('[name="id_pencairan"]').val(id);
			$(".page-header").html('Data Kotak Masuk <div class="btn-group pull-right tombl"></div>');
			$(".tombl").html('<a href="javascript:void(0)" onclick="kembali()" class="btn btn-primary"><i class="glyphicon glyphicon-backward"></i> kembali</a>');
			$('html, body').animate({scrollTop:$('#wrapper').offset().top - 50}, 'slow');	
			// var returnedData = JSON.parse(data);
			// alert(kk);
			$('#li_'+kk).css('background-color', 'white');
		}
	);
}

function doAcc(name_id, id, hasilpekerjaan, id_inbox)
{
	// alert(hasilpekerjaan);
	// $("#"+name_id).removeAttr('checked');
	// $("#"+name_id).prop('checked',true);	
	var text_cancel;
	
	if(name_id != "ttd_pptk" || name_id != "ttd_ppk" ||  name_id != "ttd_bendahara" ||   name_id != "ttd_pengguna_anggaran"){
		
		if(name_id == 'bukanBA')
		{
			text_cancel = 'Cancel';
			$("."+name_id).removeAttr('checked');
		}else{
			text_cancel = 'TOLAK';
		}
	}else{
		text_cancel = 'Cancel';
	}
	
	$("#"+name_id).removeAttr('checked');
	Lobibox.confirm({
	msg: "Anda yakin akan melakukan Tanda Tangan pada surat ini",
	buttons: {				
			yes: {
				'class': 'btn btn-success',
				text: 'Yes',
				closeOnClick: true
			},
			cancel: {
				'class': 'btn btn-danger',
				text: text_cancel,
				closeOnClick: true
			}	
		},
		callback: function ($this, type) {
			btnType = 'success';
			if (type === 'yes') {	
				// $("#"+name_id).removeAttr("disabled");				
				$("#"+name_id).prop('checked',true);	
				gotoAcc(name_id, id, hasilpekerjaan, id_inbox);
				
			} else if (type === 'cancel') {
				if(name_id != "ttd_pptk" || name_id != "ttd_ppk" ||  name_id != "ttd_bendahara" ||   name_id != "ttd_pengguna_anggaran"){	
					if(name_id == 'bukanBA'){
						$("."+name_id).removeAttr('checked');
					}else{
						$("#"+name_id).show;
						$("#"+name_id).removeAttr('checked');
						$('#modal_form .modal-footer').html('<button type="button" id="btnModal_proses" onclick="proses_tolak()" class="btn btn-primary">Save</button><button type="button" id="btnModal_cancel" class="btn btn-danger" data-dismiss="modal">Cancel</button>');
						post_komentar(id, name_id,id_inbox);
					}
				}else{
					$("#"+name_id).removeAttr('checked');
				}
				// ajax adding data to database
				
			}
		}
	});     	
}

function gotoAcc(name_id, id, hasilpekerjaan, id_inbox, kodeset, nilaparf){
	// ajax adding data to database
	var nilaiparaf = 1;
	if(kodeset){
		nilaiparaf = nilaparf;	
	}
	
	$("#loading-overlay").show();
	$.ajax({
		url : "<?php echo base_url('ba/'.$class_name.'/ajax_insert')?>",
		type: "POST",
		data: {id:id, statusParaf:nilaiparaf, name_id:name_id, hasilpekerjaan:hasilpekerjaan, id_inbox:id_inbox},
		// async:false,
		dataType: "JSON",
		success: function(data)
		{
			
			$("#loading-overlay").hide();
			if(data.status) //if success close modal and reload ajax table
			{	
				if(kodeset){
					// alert(name_id);
					if(nilaiparaf == 1){
						$(".cek"+name_id).html('<button type="button" class="btn btn-success btn-circle btn-lg"><i class="glyphicon glyphicon-ok"></i></button><br>');
					}else{
						$(".cek"+name_id).html('<button type="button" class="btn btn-danger btn-circle btn-lg"><i class="glyphicon glyphicon-remove-sign"></i></button><br>');
					}
				}else{
					$("#"+name_id).attr("disabled", true);		
					$(".tombol_"+name_id).hide();		
					$(".logo_"+name_id).html('<button type="button" class="btn btn-success btn-circle btn-lg"><i class="glyphicon glyphicon-ok"></i></button><br>');
				}

				if(name_id == 'bukanBA')
				{
					$(".logo_"+name_id).html('<button type="button" class="btn btn-success btn-circle btn-lg"><i class="glyphicon glyphicon-ok"></i></button>');
				}
				
				if(nilaiparaf == 1){					
					Lobibox.notify('success', {
						title: 'ACC',
						msg: 'Surat telah anda Tanda Tangan.'
					});
				}else{
					Lobibox.notify('error', {
						title: 'Tolak',
						msg: 'Surat Telah di Tolak.'
					});
				}
			}else{
				kembali();
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error adding / update data');
			// $("#loading-overlay").hide();

		}
	});
	// ajax adding data to database
}

function post_komentar(id, name_id,id_inbox){
	
	id_tolak = id;
	name_id_tolak = name_id;
	paraf_tolak = 0;
	idinbox = id_inbox;
	
	// $('[name="id"]').val(id);
	$('#formModal')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
	$('#modal_form').modal('show'); // show bootstrap modal
	
	$('.modal-title').text('Alasan Penolakan Dokumen'); // Set Title to Bootstrap modal title
	$('#btnModal_proses').text('Proses'); // Set Title to Bootstrap modal title
	$('#kalimat_tampil').html(''); // Add Teks
	$('#kalimat_tampil').html(''); // Add Teks
}


function proses_tolak(){
	// alert(id_tolak);	
	// alert(name_id_tolak);	
	// alert(paraf_tolak);
	// var catatan = $('[name="catatan"]').val();
	
	$("#loading-overlay").show();
	$('#modal_form').modal('toggle'); // show bootstrap modal
	$("#loading-overlay").show();
    var formData = new FormData($('#formModal')[0]);
	// {id:id_tolak, statusParaf:paraf_tolak, name_id:name_id_tolak, catatan:catatan}	
	formData.append('id', id_tolak);
	formData.append('statusParaf', paraf_tolak);
	formData.append('name_id', name_id_tolak);
	formData.append('id_inbox', idinbox);
	
	$.ajax({
		url : "<?php echo base_url('ba/'.$class_name.'/ajax_insert')?>",
		type: "POST",
		data: formData,
		// async:false,
		dataType: "JSON",
		cache: false,
		contentType: false,
		processData: false,
		success: function(data)
		{			
			$("#loading-overlay").hide();
			if(data.status) //if success close modal and reload ajax table
			{			
				Lobibox.notify('error', {
					// size: 'mini',
					// delay: 100, 	
					title: 'Tolak',
					msg: 'Surat Telah di Tolak.'
				});
				
				$("#"+name_id_tolak).attr("disabled", true);		
				$(".tombol_"+name_id_tolak).hide();		
				$(".logo_"+name_id_tolak).html('<button type="button" class="btn btn-danger btn-circle btn-lg"><i class="glyphicon glyphicon-remove-sign"></i></button><br>');
			}else{
				kembali();
			}
			
			$("#loading-overlay").hide();
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error adding / update data');
			$("#loading-overlay").hide();

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
						<!-- <input type="hidden" name="id"> -->
						<label for="comment">Catatan:</label>
						<textarea class="form-control" rows="5" name="catatan" id="catatan" placeholder="Masukan Catatan atau alasan penolakan dokumen"></textarea>
						<label id="kalimat_tampil"></label>
					</div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnModal_proses" onclick="proses_tolak()" class="btn btn-primary">Save</button>
                <button type="button" id="btnModal_cancel" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->