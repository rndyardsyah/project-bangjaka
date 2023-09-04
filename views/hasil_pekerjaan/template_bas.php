
<html> 
<head>

<style>
	#template_bas {
		font-family: sans-serif;
		font-size: 10pt;
	}
	#table_template_bas {					
		/* border-collapse: collapse; */
		border-spacing: 0;
		margin-top: 10px;
		margin-bottom: 10px;
	}
	#td_template_bas {					
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
	ol{
		text-align:justify;
		margin: 0;
		padding-left: 20px;
	}
</style>
</head>

<?php
	$termin = ($data->pekerjaan_termin == 1) ? '' : 'Termin ' . getRomawi($data->termin);
	$type_kontrak = getTypeKontrak(@$data);
?>
<body id="template_bas">	
	<?php if(empty(@$pdf)){?><div id="batas-margin"><?php } ?>	
		<p style="text-align: center; font-weight: bold; font-size: 13pt;">
		PEKERJAAN <?php echo strtoupper(@$data->nama_pekerjaan); ?><br>
		KEGIATAN <?php echo strtoupper(@$data->nama_kegiatan); ?><br>
		TAHUN ANGGARAN  <?php echo date('Y', strtotime($data->tgl_pekerjaan)); ?>
		</p>
		<hr>
		<p style="text-align: center; font-weight: bold;"><u>BERITA ACARA PENERIMAAN HASIL PEKERJAAN</u><br>
		Nomor : <?php echo @$data->no_bas_penerimaan; ?>
		</p>

		<div style="margin-top: 5px">
			<p style="text-align: justify;">
			Pada Hari ini, <?php echo GetDayDate(@$data->tanggal_bas); ?> Tanggal <i><?php echo ucwords(terbilang(date('d', strtotime(@$data->tanggal_bas)))); ?></i> bulan <i><?php echo GetMonth(date('m', strtotime(@$data->tanggal_bas))); ?></i> tahun <i><?php echo ucwords(terbilang(date('Y', strtotime(@$data->tanggal_bas)))); ?></i> (<?php echo date('d-m-Y', strtotime(@$data->tanggal_bas)); ?>), Kami yang bertanda tangan dibawah ini:
			</p>

			<table id="table_template_bas" border="1" width="95%" style="border-collapse: collapse; text-align: center;" align="center">
				<tr>
					<td id="td_template_bas" width="55%"><b>NAMA/NIP</b></td>
					<td id="td_template_bas" width="40%"><b>JABATAN</b></td>
				</tr>
				<?php foreach($data_pejabat_pphp as $rss){ ?>
				<tr>
					<td id="td_template_bas"><?php echo @$rss['nama_pegawai_pphp']; ?> / <?php echo @$rss['nip_pegawai_pphp']; ?></td>
					<td id="td_template_bas">Pejabat Penerima Hasil Pekerjaan</td>
				</tr>
				<?php } ?>
			</table>

			<p>	  
			Berdasarkan Surat Keputusan Kepala <?php echo getNamaUnor(substr(@$data->kode_unor,0,5)); ?> Kota Tangerang Nomor : <?php echo @$data->pjbtpenerima_nosk; ?>, tanggal  <?php echo GetFullDateFull(@$data->pjbtpenerima_tglsk); ?>, tentang Penunjukan Pejabat Penerima Hasil Pekerjaan pada <?php echo getNamaUnor(substr(@$data->kode_unor,0,5)); ?> Kota Tangerang Tahun Anggaran  <?php echo date('Y', strtotime($data->tgl_pekerjaan)); ?>, telah melakukan pemeriksaan dan menerima atas hasil pekerjaan :
			</p>	


			<table id="table_template_bas" border="0" width="100%" style="border-collapse: collapse;">
				<tr>
					<td id="td_template_bas" width="30%">Kegiatan</td>
					<td id="td_template_bas" width="2%">:</td>
					<td id="td_template_bas" width="68%"><?php echo @$data->nama_kegiatan; ?></td>
				</tr>
				<tr>
					<td id="td_template_bas">Pekerjaan</td>
					<td id="td_template_bas">:</td>
					<td id="td_template_bas"><?php echo @$data->nama_pekerjaan; ?></td>
				</tr>
				<tr>
					<td id="td_template_bas"><?php echo $type_kontrak; ?></td>
					<td id="td_template_bas">:</td>
					<td id="td_template_bas"><?php echo @$data->no_spk; ?></td>
				</tr>
				<tr>
					<td id="td_template_bas">Pelaksana Pekerjaan</td>
					<td id="td_template_bas">:</td>
					<td id="td_template_bas"><?php if($data->kategori == 1){ echo @$data->nama_perusahaan; }else{  echo @$data->nama_penyedia;  } ?></td>
				</tr>
				<tr>
					<td id="td_template_bas">Alamat</td>
					<td id="td_template_bas">:</td>
					<td id="td_template_bas"><?php echo @$data->alamat; ?></td>
				</tr>
			</table>  

			<p>Berdasarkan surat dari <?php if($data->kategori == 1){ echo @$data->nama_perusahaan; }else{  echo @$data->nama_penyedia;  } ?>  Nomor: <?php echo @$data->no_srt_penyerahan; ?>, tanggal <?php echo GetFullDateFull(@$data->tgl_srt_penyerahan); ?>, perihal Penyerahan Hasil Pekerjaan <?php echo @$data->nama_pekerjaan; ?> <?php echo $termin; ?>, setelah dilakukan pemeriksaan terhadap hasil kelengkapan administrasi pekerjaan tersebut dapat dijelaskan sebagai berikut :</p>
			
			<ol>
			  <li>Bahwa Pekerjaan <?php echo @$data->nama_pekerjaan; ?> <?php echo $termin; ?>, pada <?php echo getNamaUnor(substr(@$data->kode_unor,0,5)); ?> Tahun Anggaran  <?php echo date('Y', strtotime($data->tgl_pekerjaan)); ?> telah lengkap, adapun rinciannya sebagai berikut :</li>
						
				<table id="table_template_bas" border="1" width="100%" style="border-collapse: collapse; padding-top: 5px; padding-bottom: 5px;">
					<tr style="font-weight: bold; text-align: center;">
						<td id="td_template_bas" width="5%">No.</td>
						<td id="td_template_bas" width="65%">Uraian Barang / Spesifikasi Teknis</td>
						<td id="td_template_bas" width="10%">Vol</td>
						<td id="td_template_bas" width="10%">Sat</td>
						<td id="td_template_bas" width="10%">Ket</td>
					</tr>
				<?php
				
				$no = 1;
				foreach($data->data_uraian_pekerjaan as $row){
					echo '
						<tr>
							<td id="td_template_bas" style="text-align: center;">'.$no++.'</td>
							<td id="td_template_bas">'.$row['uraian'].'</td>
							<td id="td_template_bas" style="text-align: center;">'.$row['volume'].'</td>
							<td id="td_template_bas" style="text-align: center;">'.$row['satuan'].'</td>
							<td id="td_template_bas" style="text-align: center;">'.ucfirst($row['keterangan']).'</td>
						</tr>';
				}			
				?>
				</table>
				  
			  <li>Berdasarkan hasil pemeriksaan, bahwa Pihak Pelaksana Kegiatan/penyedia jasa telah menyerahkan kelengkapan administrasi pelengkap pekerjaan sesuai dengan yang telah disepakati dalam <?php echo $type_kontrak; ?>, maka hasil pekerjaan tersebut dapat diserahterimakan kepada Kepala <?php echo getNamaUnor(substr(@$data->kode_unor,0,5)); ?> Kota Tangerang selaku Pengguna Barang/Jasa, dan selanjutnya kepada Pihak Pelaksana Pekerjaan (Penyedia Barang/Jasa) <?php if($data->kategori == 1){ echo @$data->nama_perusahaan; }else{  echo @$data->nama_penyedia;  } ?> dapat dibayarkan pembayaran pekerjaan sesuai dengan <?php echo $type_kontrak; ?>, Sebesar <b>Rp. <?php echo number_format(@$data->nilai_pekerjaan , 0 ,'' , '.').',-'; ?> (<?php echo @$data->nilai_pekerjaan_terbilang; ?> rupiah) </b> sudah termasuk pajak dan lain-lain.</li>
			</ol> 
		</div>
		<div>
		</div>
		<table border="0" width="100%" style="border-collapse: collapse;">
			<tr>
				<td>
				Demikian Berita Acara ini dibuat dalam rangkap 5 (lima) untuk dipergunakan sebagaimana mestinya.
				<table id="table_template_bas" border="0" style="text-align: center;" width="100%">			
					<?php 
					$nom = 1;
					$penyedia = ($data->kategori == 1) ? @$data->nama_perusahaan : '';
					$jabatan = ($data->kategori == 1) ? @$data->jabatan : 'Tenaga Ahli';
					foreach($data_pejabat_pphp as $rss){
						
						$panitia_pphp = (count($data_pejabat_pphp) > 1) ? '<td id="td_template_bas" width="50%">PANTIA PENERIMA HASIL PEKERJAAN</td>' : '<td id="td_template_bas" width="50%">PEJABAT PENERIMA HASIL PEKERJAAN</td>';
						
						$nomor = $nom++;
						if($nomor == 1){
							echo '						
								<tr>
									<td id="td_template_bas" width="50%">PENYEDIA BARANG/JASA<br>
									'.$penyedia.'
									</td>
									'.$panitia_pphp.'
								</tr>
							';
						}
					?>
					<tr>
						<td id="td_template_bas" height="55px"></td>
						<td height="55px" style="vertical-align: center!importan;">					
							<?=@$rss['buttonTtd']; ?>
						</td>
					</tr>
					<?php 
						if($nomor > 1){
							echo '				
								<tr>
									<td id="td_template_bas"></td>
									<td id="td_template_bas"><b><u>'.@$rss['nama_pegawai_pphp'].'</u><b/></td>
								</tr>			
								<tr>
									<td id="td_template_bas"></td>
									<td id="td_template_bas">NIP. '.@$rss['nip_pegawai_pphp'].'</td>
								</tr>
							';
						}else{
							echo '	
								<tr>
									<td id="td_template_bas"><b><u>'.@$data->nama_penyedia.'</u></b></td>
									<td id="td_template_bas"><b><u>'.@$rss['nama_pegawai_pphp'].'</u></b></td>
								</tr>				
								<tr>
									<td id="td_template_bas">
									'.$jabatan.'
									</td>
									<td id="td_template_bas">NIP. '.@$rss['nip_pegawai_pphp'].'</td>
								</tr>
							';
						}
					?>
					<?php }?>
				</table>
				</td>
			</tr>
		</table>
	<?php if(empty(@$pdf)){?></div><?php } ?>	
</body>
</html>