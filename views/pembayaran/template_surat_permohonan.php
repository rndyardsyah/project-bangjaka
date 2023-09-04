
<html> 
<head>

<style>
	#template_surat_permohonan_pembayaran {
		font-family: sans-serif;
		font-size: 11pt;
		margin-left: 2cm;
	}
	#table_template_surat_permohonan_pembayaran {					
		/* border-collapse: collapse; */
		border-spacing: 0;
		margin-top: 10px;
		margin-bottom: 10px;
	}
	#td_template_surat_permohonan_pembayaran {					
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
	.penyedia_surat_permohonan_pembayaran {
		float: right;
		margin-right: 50px;
	}
	#batas-margin {
		// padding : 50px;
		// margin-left: 2cm;
	}
</style>
</head>

<?php 

$margins ='';
if($data->kategori == 1){
	$margins = 'style="margin-top: 35px"';
}
?>

<body id="template_surat_permohonan_pembayaran">	
	<div id="batas-margin" <?php echo $margins; ?>>			
		
		<?php
			$total = count($data_pembayaran);
			$no = 1;
			$termin_view = '';
			$total_pembayaran = 0;
			$type_kontrak = '';
			foreach($data_pembayaran as $row)
			{
				
				if($total == 1){
					$termin_view .= getRomawi($row->termin);
				}else{
					if($no++ == $total){
						$termin_view .= ' dan ' . getRomawi($row->termin);
					}else{
						$termin_view .= getRomawi($row->termin) . ', ';
					}
				}
				
				$total_pembayaran += $row->nilai_pekerjaan;
				$type_kontrak = getTypeKontrak(@$row);
				
			}
			
			$termin = ($data_pembayaran[0]->pekerjaan_termin == 1) ? '' : 'Termin '.$termin_view;
		?>
		
		<table id="table_template_surat_permohonan_pembayaran" border="0" width="100%" style="margin-top: 100px;">
			<tr>
				<td id="td_template_surat_permohonan_pembayaran" width="10%">Nomor</td>
				<td id="td_template_surat_permohonan_pembayaran" width="2%">:</td>
				<td id="td_template_surat_permohonan_pembayaran" width="50%"><?php echo @$data_pembayaran[0]->no_permohonan_pembayaran; ?></td>
				<td id="td_template_surat_permohonan_pembayaran" width="38%">Tangerang,  <?php if(@$data_pembayaran[0]->tgl_permohonan_pembayaran){ echo GetFullDateFull(@$data_pembayaran[0]->tgl_permohonan_pembayaran);  }else{ echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. date('Y'); } ?></td>
			</tr>
			<tr>
				<td id="td_template_surat_permohonan_pembayaran">Lampiran</td>
				<td id="td_template_surat_permohonan_pembayaran">:</td>
				<td id="td_template_surat_permohonan_pembayaran">-</td>
				<td id="td_template_surat_permohonan_pembayaran"></td>
			</tr>
		</table>	                                         

		<p>
		Kepada  Yth, <br>
		<b>
		Kepala <?php echo getNamaUnor(substr(@$data_pembayaran[0]->kode_unor,0,5)); ?> Kota Tangerang <br>
		Melalui Pejabat Pembuat Komitmen (PPK) </b><br>
		Gd. Puspem Lt.4 Jl. Satria Sudirman No.1- Kota Tangerang <br>
		di - <br>
		<b><span style="padding-left:25px;">Tangerang</span></b> 
		</p>
		
		<table id="table_template_surat_permohonan_pembayaran"  border="0" width="100%">
			<tr>
				<td id="td_template_surat_permohonan_pembayaran" width="10%">Perihal</td>
				<td id="td_template_surat_permohonan_pembayaran" width="2%">:</td>
				<td id="td_template_surat_permohonan_pembayaran" width="88%"><u>Permohonan Pembayaran</u></td>
			</tr>
		</table>	 
		
		<p>Dengan Hormat,</p>
		<div class="tab">
		<p>Sehubungan dengan telah selesainya Pekerjaan <?php echo @$data_pembayaran[0]->nama_pekerjaan; ?> <?php echo @$termin; ?> dengan baik, maka saya, 
		<?php if($data_pembayaran[0]->kategori == 1){ echo @$data_pembayaran[0]->nama_perusahaan; }else{  echo @$data_pembayaran[0]->nama_penyedia;  } ?> selaku Penyedia Barang/ Jasa, bersama ini bermaksud mengajukan permohonan pembayaran atas pelaksanaan pekerjaan <?php echo @$data_pembayaran[0]->nama_pekerjaan; ?> <?php echo @$termin; ?> sesuai  <?php echo $type_kontrak; ?> Nomor: <?php echo @$data_pembayaran[0]->no_spk; ?>, tanggal <?php echo GetFullDateFull(@$data_pembayaran[0]->tgl_pekerjaan); ?> 
		  <?php
		  
			if(@$data_adendum){
				$noms = 1;
				echo 'dan ';
				foreach($data_adendum as $rsa){					
					echo 'Adendum '.$noms++. ' : '.$rsa['no_adendum'].', tanggal '. GetFullDateFull($rsa['tgl_adendum']).', ';
				}
			}
		  
		  ?>
		dengan nilai pekerjaan sebesar <b>Rp. <?php echo @number_format($total_pembayaran, 2); ?> (<?php echo ucfirst(terbilang($total_pembayaran)); ?> rupiah)</b> dengan cara pembayaran yang telah ditentukan, melalui <?php echo @$data_pembayaran[0]->bank; ?> dengan nomor Rek: <?php echo @$data_pembayaran[0]->no_rekening_penyedia; ?> An. <?php echo @$data_pembayaran[0]->atas_nama_rekening; ?> NPWP : <?php echo @$data_pembayaran[0]->npwp; ?>.</p>
		
		<p>Demikian Kami sampaikan, atas perhatian dan kerjasamanya kami ucapkan terimakasih.</p>
		</div>
		
		<div>
			<div class="penyedia_surat_permohonan_pembayaran">
				<table id="table_template_surat_permohonan_pembayaran" border="0" style="text-align: center;">
					<tr>
						<td id="td_template_surat_permohonan_pembayaran" >Hormat Kami,</td>
					</tr>
					<tr>
						<td id="td_template_surat_permohonan_pembayaran" ><b><?php if($data_pembayaran[0]->kategori == 1){ echo @$data_pembayaran[0]->nama_perusahaan; }else{  echo 'Penyedia Jasa Perorangan';  } ?></b></td>
					</tr>
					<tr>
						<td id="td_template_surat_permohonan_pembayaran"  height="55px"></td>
					</tr>
					<tr>
						<td id="td_template_surat_permohonan_pembayaran" ><b><u><?php echo $data_pembayaran[0]->nama_penyedia; ?></u></b></td>
					</tr>
					<tr>
						<td id="td_template_surat_permohonan_pembayaran" ><b><?php 
						if(!empty($data_pembayaran[0]->jabatan)){ echo @$data_pembayaran[0]->jabatan; }else{  echo 'Tenaga Ahli';  } ?></b></td>
					</tr>
				</table>
			</div>
			 <div style="clear: both;"></div>
		</div>
	</div>
	
</body>
</html>