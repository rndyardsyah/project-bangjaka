
<html> 
<head>

<style>
	#template_surat_permohonan {
		font-family: sans-serif;
		font-size: 11pt;
	}
	#table_template_surat_permohonan {					
		/* border-collapse: collapse; */
		border-spacing: 0;
		margin-top: 10px;
		margin-bottom: 10px;
	}
	#td_template_surat_permohonan {					
		vertical-align : top;
	}
	p{
		text-align:justify;
		line-height:20px;
	}
	.tab p{
		 text-indent: 50px;
		 margin-top: -5px;
	}
	.penyedia_surat_permohonan {		
		float: right;
		margin-right: 50px;
	}
	#batas-margin {
		padding : 30px;
		margin-left: 2cm;
	}
</style>
</head>
<?php
	$termin = ($data->pekerjaan_termin == 1) ? '' : 'Termin ' . getRomawi($data->termin);
	$type_kontrak = getTypeKontrak(@$data);
	
	$margins ='';
	if($data->kategori == 1){
		$margins = 'style="margin-top: 35px"';
	}
?>
<body id="template_surat_permohonan">	
	<div id="batas-margin" <?php echo $margins; ?>>
		<table id="table_template_surat_permohonan" border="0" width="100%" style="margin-top: 50px;">
			<tr>
				<td id="td_template_surat_permohonan" width="10%">Nomor</td>
				<td id="td_template_surat_permohonan" width="2%">:</td>
				<td id="td_template_surat_permohonan"  width="38%" style="word-wrap: break-word;"><?php echo $data->no_srt_penyerahan; ?></td>
				<td id="td_template_surat_permohonan"  width="50%" align="right"><span>Tangerang,  <?php echo GetFullDateFull($data->tgl_srt_penyerahan); ?></span></td>
			</tr>
			<tr>
				<td id="td_template_surat_permohonan" >Lampiran</td>
				<td id="td_template_surat_permohonan" >:</td>
				<td id="td_template_surat_permohonan" >1 (satu)  set</td>
				<td id="td_template_surat_permohonan" ></td>
			</tr>
		</table>	                                         

		<p>
		Kepada  Yth, <br>
		<b>
		Kepala <?php echo getNamaUnor(substr(@$data->kode_unor,0,5)); ?> Kota Tangerang <br>
		Melalui Pejabat Pembuat Komitmen (PPK) </b><br>
		Gd. Puspem Lt.4 Jl. Satria Sudirman No.1- Kota Tangerang <br>
		di - <br>
		<b><span style="padding-left:25px;">Tangerang</span></b> 
		</p>
		<table id="table_template_surat_permohonan" border="0" width="100%">
			<tr>
				<td id="td_template_surat_permohonan"  width="10%">Perihal</td>
				<td id="td_template_surat_permohonan"  width="2%">:</td>
				<td id="td_template_surat_permohonan"  width="88%">Penyerahan Hasil Pekerjaan <?php echo $data->nama_pekerjaan; ?> <?php echo $termin; ?></td>
			</tr>
		</table>	 
		
		<p>Dengan Hormat,</p>
		<div class="tab">
		<p>Sehubungan telah diselesaikannya Pekerjaan <?php echo $data->nama_pekerjaan; ?> <?php echo $termin; ?> pada <?php echo getNamaUnor(substr(@$data->kode_unor,0,5)); ?> Kota Tangerang Tahun Anggaran  <?php echo date('Y', strtotime($data->tgl_pekerjaan)); ?>. Bersama ini, Kami sampaikan laporan hasil pekerjaan <?php echo $data->nama_pekerjaan; ?> <?php echo $termin; ?>.</p>
		 
		<p>Sesuai dengan <?php echo $type_kontrak; ?> Nomor : <?php echo $data->no_spk; ?>, tertanggal <?php echo GetFullDateFull($data->tgl_pekerjaan); ?>, 
		<?php
		  
			if(@$data_adendum){
				$noms = 1;
				echo 'dan ';
				foreach($data_adendum as $rsa){					
					echo 'Adendum '.$noms++. ' : '.$rsa['no_adendum'].', tertanggal tanggal '. GetFullDateFull($rsa['tgl_adendum']).', ';
				}
			}
		  
		  ?>
		antara <?php echo getNamaUnor(substr(@$data->kode_unor,0,5)); ?> Kota Tangerang dengan <?php if($data->kategori == 1){ echo @$data->nama_perusahaan; }else{  echo @$data->nama_penyedia;  } ?> selaku Pelaksana Pekerjaan (Penyedia Barang/Jasa) bermaksud menyerahkan hasil pekerjaan tersebut diatas pada <?php echo getNamaUnor(substr(@$data->kode_unor,0,5)); ?> Kota Tangerang.</p>
		
		<p>Demikian surat Kami, atas perhatian dan kerjasama kami sampaikan terima kasih.</p>
		</div>
		
		<div>
			<div class="penyedia_surat_permohonan">
				<table id="table_template_surat_permohonan" border="0" style="text-align: center;">
					<tr>
						<td id="td_template_surat_permohonan" >Hormat Kami,</td>
					</tr>
					<tr>
						<td id="td_template_surat_permohonan" ><b><?php if($data->kategori == 1){ echo @$data->nama_perusahaan; }else{  echo 'Penyedia Jasa Perorangan';  } ?></b></td>
					</tr>
					<tr>
						<td id="td_template_surat_permohonan"  height="55px"></td>
					</tr>
					<tr>
						<td id="td_template_surat_permohonan" ><b><u><?php echo $data->nama_penyedia; ?></u></b></td>
					</tr>
					<tr>
						<td id="td_template_surat_permohonan" ><b><?php if(!empty($data->jabatan)){ echo @$data->jabatan; }else{  echo 'Tenaga Ahli';  } ?></b></td>
					</tr>
				</table>
			</div>
			<div style="clear: both;"></div>
		</div>
		
	</div>
</body>
</html>