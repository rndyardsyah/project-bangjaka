<script>
$('.rinciandetail').click(function() {
	
	$('#modal_form').modal('show'); // show bootstrap modal
	$('.modal-title').text('Data Rincian Kegiatan'); // Set Title to Bootstrap modal title
	$("#btnModal_proses").attr("onclick","save_rincian()");
	$('#btnModal_proses').show(); // Set Title to Bootstrap modal title
	$('#btnModal_proses').text('Simpan'); // Set Title to Bootstrap modal title
	$('#modal_form #form-tambahan').load('<?=base_url('ba/pembayaran_rincian/getDataRincian');?>', {id_rincian_detail:this.id, id_pembayaran:id_pembayarannya}, function(data, status) 
	{
		$("#loading-overlay").hide();
	});	
	// alert('RENDY');
	$('#kalimat_tampil').html(''); // Add Teks
  // console.log(this.id);
});
</script>

<?php 
$no = 1;
foreach($data_rincian as $row){ ?> 
<div class="panel panel-danger rinciandetail" id="rinci<?php echo $row['id']; ?>">
	<div class="panel-heading">
	  <h4 class="panel-title">
		  <span class="glyphicon glyphicon-plus-sign" style="float: right; color: black;"></span>
		  Rincian <?php echo $row['uraian']; ?> 
	  </h4>
	</div>
</div>
<?php } ?> 