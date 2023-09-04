<style>
#frame {
	width:100%;
	height:100vh;
	overflow:auto;
	border-left:solid 2px #9A9A9A;
	border-right:solid 2px #F1F1F1;
	border-top:solid 2px #9A9A9A;
	border-bottom:solid 2px #eee;
	padding:20px 0px;
	background:#525659;	
	color:#000;	
	font-family:"Times New Roman", Times, serif;
}

.frame-nav {
	width:100%;
	float:left;
	border:solid 1px #BFBFBF;
	/* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#ffffff+0,f3f3f3+50,ededed+51,ffffff+100;White+Gloss+%232 */
	background: rgb(255,255,255); /* Old browsers */
	background: -moz-linear-gradient(top,  rgba(255,255,255,1) 0%, rgba(243,243,243,1) 50%, rgba(237,237,237,1) 51%, rgba(255,255,255,1) 100%); /* FF3.6-15 */
	background: -webkit-linear-gradient(top,  rgba(255,255,255,1) 0%,rgba(243,243,243,1) 50%,rgba(237,237,237,1) 51%,rgba(255,255,255,1) 100%); /* Chrome10-25,Safari5.1-6 */
	background: linear-gradient(to bottom,  rgba(255,255,255,1) 0%,rgba(243,243,243,1) 50%,rgba(237,237,237,1) 51%,rgba(255,255,255,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ffffff',GradientType=0 ); /* IE6-9 */
	
}

.btn-frame-nav {
	border:none;
	background: rgb(255,255,255); /* Old browsers*/
	background: -moz-linear-gradient(top,  rgba(255,255,255,1) 0%, rgba(243,243,243,1) 50%, rgba(237,237,237,1) 51%, rgba(255,255,255,1) 100%); /* FF3.6-15 */
	background: -webkit-linear-gradient(top,  rgba(255,255,255,1) 0%,rgba(243,243,243,1) 50%,rgba(237,237,237,1) 51%,rgba(255,255,255,1) 100%); /* Chrome10-25,Safari5.1-6 */
	background: linear-gradient(to bottom,  rgba(255,255,255,1) 0%,rgba(243,243,243,1) 50%,rgba(237,237,237,1) 51%,rgba(255,255,255,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ffffff',GradientType=0 ); /* IE6-9 */

}

body {
  background: rgb(204,204,204); 
}
page {  
  display: block;
  margin-bottom: 0.5cm;
  /* box-shadow: 0 0 0.5cm rgba(0,0,0,0.5); */
}
page[size="A4"] {  
	background: white;
	margin: 0 auto;
	/* margin-top: 0.5cm; */
	width: 21cm;
	/* height: 29.7cm;  */	
	height: auto; 
	margin-top: 1cm;	
	margin-bottom: 1cm;	
}

page[size="kwitansi"] {  
	background: white;
	margin: 0 auto;
	margin-top: 0.5cm;
	width: 21cm;
	height: auto; 
	margin-bottom: 1cm;
}

page[size="F4"] {  
	background: white;
	margin: 0 auto;
	/* margin-top: 0.5cm; */
	width: 21cm;
	/* height: 36cm;  */
	height: auto; 
	margin-top: 1cm;	
	margin-bottom: 1cm;	
}

page[size="batas"][layout="portrait"] {
  /* background: none !important; */
  width: 21.5cm;
  height: 1cm;  
}

page[size="A4"][layout="portrait"] {
  width: 29.7cm;
  /* height: 21cm;   */
	height: auto; 
}
page[size="A3"] {
  width: 29.7cm;
  /* height: 42cm; */
	height: auto; 
}
page[size="A3"][layout="portrait"] {
  width: 42cm;
  /* height: 29.7cm;   */
	height: auto; 
}
page[size="A5"] {
  width: 14.8cm;
  /* height: 21cm; */
	height: auto; 
}
page[size="A5"][layout="portrait"] {
  width: 21cm;
  /* height: 14.8cm;   */
	height: auto; 
}
@media print {
  body, page {
    margin: 0;
    box-shadow: 0;
  }
}

@media only screen and (max-width: 720px),
(min-device-width: 768px) and (max-device-width: 1024px) {
    #frame {
        /* height: 13.4cm; */
		height:100vh;
        padding: 8px 0px 0px 0px;
    }
	page {  
	  margin-bottom: 1cm;
	}
    page[size="A4"] {
		margin: 0 auto;	
		height: auto; 
		margin-top: 1cm;	
    }
    page[size="kwitansi"] {
		margin: 0 auto;	
		height: auto; 
		margin-top: 1cm;	
		margin-bottom: 1cm;	
    }
    page[size="F4"] {
		margin: 0 auto;	
		height: auto; 
		margin-top: 1cm;
    }
}

</style>

<script>
// $('#zoom-in').click(function() {
   // updateZoom(0.1);
// });

// $('#zoom-out').click(function() {
   // updateZoom(-0.1);
// });

var id_ceklis;

zoomLevel = 1;

var updateZoom = function(zoom) {
   zoomLevel += zoom;
   $('page').css({ zoom: zoomLevel, '-moz-transform': 'scale(' + zoomLevel + ')' });
}

var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
if (isMobile) {
  /* your code here */
	// $('#zoom-out').trigger('click');
	// $('#zoom-out').trigger('click');
	// $('#zoom-out').trigger('click');
	// $('#zoom-out').trigger('click');
	// $('#zoom-out').trigger('click');
	// $('#zoom-out').trigger('click');
	
	updateZoom(-0.1);
	updateZoom(-0.1);
	updateZoom(-0.1);
	updateZoom(-0.1);
	updateZoom(-0.1);
	updateZoom(-0.1);
}

function pdf_render(){
	
	$("#loading-overlay").show();
	var url = '';
	var keterangan = <?php echo $keterangan; ?>;
	if(keterangan == 0){ //keterangan 0 itu hasil berarti id_hasil_pekerjaan
		url = "<?php echo base_url('ba/hasil_pekerjaan/download_pdf')?>";
	}else if(keterangan == 2){
		url = "<?php echo base_url('surat/download/filepdf')?>";
	}else{
		url = "<?php echo base_url('ba/pembayaran/download_pdf')?>";
	}
	
	$.ajax({
		url : url,
		type: "POST",
		data: {id:"<?php echo $id_read; ?>"},
		// async:false,
		dataType: "JSON",
		success: function(data)
		{
			if(data.status) //if success close modal and reload ajax table
			{			
				window.open("<?php echo base_url()?>"+data.url_file, '_blank');
			}
			$("#loading-overlay").hide();
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error adding / update data');
			$("#loading-overlay").hide();
		}
	}); 
	// ajax adding data to database
}

$('a[href="#riwayat_surat"]').click(function(){
	
	$.ajax({
		url : "<?php echo base_url('ba/riwayat_surat')?>",
		type: "POST",
		data: {id_read:"<?php echo $id_read; ?>", keterangan: <?php echo $keterangan; ?>}, //keterangan 0 itu hasil berarti id_hasil_pekerjaan
		// async:false,
		success: function(data)
		{
			// console.log();
			$('#riwayat_surat').html(data);
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error adding / update data');
			// $("#loading-overlay").hide();
		}
	}); 
});

$('a[href="#catatan"]').click(function(){
	
	$.ajax({
		url : "<?php echo base_url('ba/catatan_surat')?>",
		type: "POST",
		data: {id_read:"<?php echo $id_read; ?>", keterangan: <?php echo $keterangan; ?>}, //keterangan 0 itu hasil berarti id_hasil_pekerjaan
		// async:false,
		success: function(data)
		{
			$('#catatan').html(data);
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert('Error adding / update data');
			// $("#loading-overlay").hide();
		}
	}); 
});


//ceklis berkas
$("input:checkbox.id_cek_berkas").change(function() {
	
	// alert($(this).prop('checked'));
	// alert(($(this)).val());
	// alert('RENDY');
	id_ceklis = ($(this)).val();

	if ($(this).prop('checked')) {		
		$(".ceklisberkas"+id_ceklis).removeAttr('checked');
		Lobibox.confirm({
		msg: "Anda yakin akan melakukan ceklist pada berkas ini",
		buttons: {				
				yes: {
					'class': 'btn btn-success',
					text: 'Lengkap',
					closeOnClick: true
				},
				cancel: {
					'class': 'btn btn-danger',
					text: 'Tidak',
					closeOnClick: true
				}	
			},
			callback: function ($this, type) {
				btnType = 'success';
				if (type === 'yes') {				
					// alert('YES');
						
					// ajax adding data to database
					$.ajax({
						url : "<?php echo base_url('ba/hasil_pekerjaan/ajax_getberkas')?>",
						type: "POST",
						data: {id:'<?php echo $id_read; ?>', statusParaf:1, name_id:id_ceklis, id_inbox:'<?php echo $id_inbox; ?>'},
						// async:false,
						dataType: "JSON",
						success: function(data)
						{
							if(data.status) //if success close modal and reload ajax table
							{	
								Lobibox.notify('success', {
									title: 'ACC',
									msg: 'Berkas Telah dinyatakan Lengkap.'
								});				
								$(".td_ceklisberkas"+id_ceklis).html('Lengkap');
								cek_kelengkapan();
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
					
				} else if (type === 'cancel') {
					
					// alert('NO');
					
					$('#modal_form .modal-footer').html('<button type="button" id="btnModal_proses" onclick="proses_tolak_berkas()" class="btn btn-primary">Save</button><button type="button" id="btnModal_cancel" class="btn btn-danger" data-dismiss="modal">Cancel</button>');
					post_komentar(<?php echo $id_read; ?>, <?php echo $id_read; ?>, <?php echo $id_inbox; ?>);
					
				}
			}
			// ,beforeClose: function ($this, type) {	
				// alert('OK OCE');
			// }
		}); 
	}
	else {
	
	  /* var url = "<?=base_url('ba/'.$class_name.'/save_auth/false');?>/"+id_menu;		
	  $.ajax({url: url, success: function(result){
			//$("#div1").html(result);
		}}); */
	   
	}
});

function cek_kelengkapan(){
	//apabila totalnya yg di paraf ceklis sama yg diopen ceklistnya sama dan tidak ada yg ditolak paraf ceklisnya maka show ttd dan kasih teks pada hasil kesimpulan lengkap
	//apabila totalnya yg di paraf ceklis sama yg diopen ceklistnya sama dan ada 1 yg ditolak paraf ceklisnya maka show ttd dan kasih teks pada hasil kesimpulan tidak lengkap
	//jika yg diparaf ceklis kurang dari total yg diopen ceklist maka tidak ada aksi apapun
	
	$.ajax({
		url : "<?php echo base_url('ba/hasil_pekerjaan/ajax_hitungberkas')?>",
		type: "POST",
		data: {id_hasil_pekerjaan:'<?php echo $id_read; ?>'},
		dataType: "JSON",
		cache: false,
		success: function(data)
		{
			if(data.status) //if success close modal and reload ajax table
			{			
				$(".kesimpulan-bas").html(data.teks);	
				// alert(<?php echo @$rss['buttonTtd']; ?>);
				// $(".ttdpphpcek").html("<?php echo @$rss['buttonTtd']; ?>");
				if(data.kondisi == 1){
					gotoAcc("<?php if(@$name_id){ echo @$name_id; }else{ echo 'ttd_pphp'; } ?>", <?php if(!empty(@$id_read)){ echo @$id_read; }else{ echo false; } ?>, 0, <?php  if(!empty(@$id_inbox)){ echo @$id_inbox; }else{ echo 0; } ?>, true, 1);
				}
				
				if(data.kondisi == 2){
					gotoAcc("<?php if(@$name_id){ echo @$name_id; }else{ echo 'ttd_pphp'; } ?>", <?php if(!empty(@$id_read)){ echo @$id_read; }else{ echo false; } ?>, 0, <?php  if(!empty(@$id_inbox)){ echo @$id_inbox; }else{ echo 0; } ?>, true, 0);
				}
				
			}else{				
				$(".kesimpulan-bas").html(data.teks);
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

function proses_tolak_berkas(){
	$('#modal_form').modal('toggle'); // show bootstrap modal
	$("#loading-overlay").show();
    var formData = new FormData($('#formModal')[0]);
	formData.append('id', id_tolak);
	formData.append('statusParaf', paraf_tolak);
	formData.append('name_id', id_ceklis);
	formData.append('id_inbox', idinbox);
	
	$.ajax({
		url : "<?php echo base_url('ba/hasil_pekerjaan/ajax_getberkas')?>",
		type: "POST",
		data: formData,
		// async:false,
		dataType: "JSON",
		cache: false,
		contentType: false,
		processData: false,
		success: function(data)
		{
			if(data.status) //if success close modal and reload ajax table
			{			
				Lobibox.notify('error', {
					// size: 'mini',
					// delay: 100, 	
					title: 'Tolak',
					msg: 'Berkas Telah dinyatakan Tidak Lengkap.'
				});				
				$(".td_ceklisberkas"+id_ceklis).html('Tidak Lengkap');
				cek_kelengkapan();
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
<!--
<button type="button" id="zoom-in">zoom in</button>
<button type="button" id="zoom-out">zoom out</button> -->
<ul class="nav nav-tabs panel-primary info">
  <li class="active"><a data-toggle="tab" href="#lembar_surat">LEMBAR SURAT</a></li>
  <li><a data-toggle="tab" href="#riwayat_surat">RIWAYAT SURAT</a></li>
  <li><a data-toggle="tab" href="#catatan">CATATAN</a></li>
</ul>

<div class="tab-content">
  <div id="lembar_surat" class="tab-pane fade in active">
    <div class="row">
		<div class="col-lg-12">     	   	            
			<div class="frame-nav">
				<a target="" class="btn btn-default btn-xs" href="javascript:void(0)" onclick="pdf_render()"><i class="fa fa-file-pdf-o"></i> PDF</a>
				
				<div class="btn-group pull-right isilampiran">
				</div>
			</div>
			<div id="frame" style="margin-bottom: 20px;">	
				<?=$hasil;?> 
			</div>
		</div>
	</div>
  </div>
  <div id="riwayat_surat" class="tab-pane fade">
  
  </div>
  <div id="catatan" class="tab-pane fade">
  
  </div>
</div>