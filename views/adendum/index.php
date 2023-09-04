<div class="panel panel-default">					
	<div class="panel-body">
		<table id="table-adendum" class="table table-hover table-striped " cellspacing="0" width="100%" >
			<thead>
					<th width="3%">No</th>
					<th>No Adendum</th>
					<th>Tanggal</th>
					<th>Biaya</th>
					<th>Waktu Pelaksanaan</th>
					<th width="10%">Aksi</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>


<script type="text/javascript">
var table_adendum;

$(document).ready(function() {	
	
	$("#loading-overlay").hide();
    table_adendum = $('#table-adendum').DataTable({ 

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
		// "responsive": true,
        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo base_url('ba/'.$class_name.'/ajax_list')?>",
            "type": "POST",
			"data": function ( data ) {
                data.id_pembayaran = '<?php echo $id_pembayaran; ?>';
            }
        },
		"columns": [
            { "data": null,
				"render": function (data, type, row, meta) {
					return meta.row + meta.settings._iDisplayStart + 1;
				}
			},     
            { "data": 0},
            { "data": 1},
            { "data": 2},       
            { "data": 3},       
            { "data": 4},       
        ],

        //Set column definition initialisation properties.
        "columnDefs": [
            { 
                "targets": [ 0,-1 ], //2 last column (photo)
                "orderable": false, //set not orderable
            },
        ],
		
		"language": {
			"url": "<?php echo base_url('assets/datatables/i18n/Indonesian.json');?>"
		} 

    });
	
});

function reload_table_adendum()
{
    table_adendum.ajax.reload(null,false); //reload datatable ajax 
}


function delete_adendum(id)
{
	save_method = 'delete_adendum';
	$('[name="id"]').val(id);
	$('#formModal')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
	$('#modal_form').modal('show'); // show bootstrap modal
	$('.modal-title').text('Hapus Data'); // Set Title to Bootstrap modal title
	$('#btnModal_proses').text('Proses'); // Set Title to Bootstrap modal title
	$('#kalimat_tampil').html('Apakah Anda yakin <b>Hapus Data</b> ini ?'); // Add Teks
	$('#form-tambahan').html('');
}

function edit_adendum(id)
{
	$("#loading-overlay").show();
	$('#form-adendum')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string


    //Ajax Load data from ajax
    $.ajax({
        url : "<?php echo base_url('ba/'.$class_name.'/ajax_edit')?>/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
			$("#loading-overlay").hide();
            $('#form-adendum [name="id"]').val(data.id_adendum);
            $('[name="no_adendum"]').val(data.no_adendum);
            $('[name="tgl_adendum"]').val(data.tgl_adendum);
            $('[name="biaya_adendum"]').val(data.biaya_adendum);
            $('[name="waktu_pelaksanaan_adendum"]').val(data.waktu_pelaksanaan_adendum);
			$('.selectpicker').attr('data-live-search', 'true');
			$('.selectpicker').selectpicker('refresh');
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
			$("#loading-overlay").hide();
            alert('Error get data from ajax');
        }
    });
}
</script>