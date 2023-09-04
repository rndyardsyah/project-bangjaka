
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
		<p style="text-align: center; font-weight: bold; font-size: 11pt;">
		PEKERJAAN <?php echo strtoupper(@$data->nama_pekerjaan); ?> KEGIATAN <?php echo strtoupper(@$data->nama_kegiatan); ?> <br>TAHUN ANGGARAN  <?php echo date('Y', strtotime($data->tgl_pekerjaan)); ?>
		</p>
		<hr>
		<p style="text-align: center; font-weight: bold;"><u>BERITA ACARA PEMERIKSAAN ADMINISTRASI HASIL PEKERJAAN</u><br>
		Nomor : <?php echo @$data->no_bas_penerimaan; ?>
		</p>

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
					<td id="td_template_bas">
					<?php 
					
					echo @$data->no_spk.'<br>'; 
					
					if(@$data_adendum){
						$noms = 1;
						foreach($data_adendum as $rsa){
							
							echo $rsa['no_adendum'];
							if(count($data_adendum) != $noms++){
								echo '<br>';
							}
						}
					}
					?>
					</td>
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

			<p>Berdasarkan surat dari <?php if($data->kategori == 1){ echo @$data->nama_perusahaan; }else{  echo @$data->nama_penyedia;  } ?>  Nomor: <?php echo @$data->no_srt_penyerahan; ?>, tanggal <?php echo GetFullDateFull(@$data->tgl_srt_penyerahan); ?>, perihal Penyerahan Hasil Pekerjaan <?php echo @$data->nama_pekerjaan; ?> <?php echo $termin; ?>, setelah dilakukan pemeriksaan terhadap hasil kelengkapan administrasi pekerjaan tersebut dapat dijelaskan Bahwa Pekerjaan <?php echo @$data->nama_pekerjaan; ?> <?php echo $termin; ?>, pada <?php echo getNamaUnor(substr(@$data->kode_unor,0,5)); ?> Tahun Anggaran  <?php echo date('Y', strtotime($data->tgl_pekerjaan)); ?> telah lengkap, adapun rinciannya sebagai berikut :</p>
									
			<table id="table_template_bas" align="center" border="1" width="95%" style="border-collapse: collapse; padding-top: 5px; padding-bottom: 5px;">
				<tr style="font-weight: bold; text-align: center;">
					<td id="td_template_bas" width="5%">No.</td>
					<td id="td_template_bas" width="65%">Nama Berkas</td>
					<td id="td_template_bas" width="30%">Hasil Pemeriksaan<br>(lengkap/tidak lengkap)</td>
				</tr>
				<?php 
					$nor = 1;
					if(@$data_berkas){
						$tombols = '';
						$getReturn = '';
						$catatans = '';
						$file = '';
						foreach($data_berkas as $rts){
							// var_dump($data_pejabat_pphp);
							// exit;
							if($data_pejabat_pphp){ //jika data pphp tidak kosong
								foreach($data_pejabat_pphp as $xft){
									if($xft['id_pegawai_pphp'] == $this->session->userdata('id_pegawai'))
									{ //jika yg login adalah pejabat pphp maka tombol yg tampil
										$getReturn = getDataHasilBerkas($rts['id_berkas'], $data->id_hasil_pekerjaan, $this->session->userdata('id_pegawai'));
										
										if($getReturn){
											if($getReturn['status'] == '1'){
												$tombols = 'Lengkap';
											}else if($getReturn['status'] == '0'){
												$catatans = (!empty($getReturn['catatan'])) ? nl2br($getReturn['catatan']) : 'Tidak Ada Catatan';
												$tombols = '<label title="'.$catatans.'">Tidak Lengkap</label>';
											}
										}else{										
											$tombols = '								
												<input type="checkbox" name="id_cek_berkas" class="id_cek_berkas ceklisberkas'.$rts['id_berkas'].'" value="'.$rts['id_berkas'].'">
											';
										}
										break;
									}else{
										$getReturn = getDataHasilBerkas($rts['id_berkas'], $data->id_hasil_pekerjaan);
										if($getReturn){
											if($getReturn['status'] == '1'){
												$tombols = 'Lengkap';
											}else if($getReturn['status'] == '0'){
												$catatans = (!empty($getReturn['catatan'])) ? nl2br($getReturn['catatan']) : 'Tidak Ada Catatan';
												$tombols = '<label title="'.$catatans.'">Tidak Lengkap</label>';
											}
										}else{
											$tombols = 'Belum diperiksa';
											// $tombols = 'Lengkap';
										}
									}
								}
							}
							
							if(empty(@$pdf)){
								$file = '<br>'.getFileBerkas($data->id_spk, $rts['id_berkas']);
							}
							
							echo '
								<tr>
									<td id="td_template_bas" style="text-align: center;">'.$nor++.'</td>
									<td id="td_template_bas">'.nl2br($rts['nama_berkas']).' '.$file.'</td>
									<td id="td_template_bas" style="text-align: center;" class="td_ceklisberkas'.$rts['id_berkas'].'">
										'.$tombols.'
									</td>
								</tr>
							';
						}
					}
				
				?>				
				<tr>
					<td id="td_template_bas"></td>
					<td id="td_template_bas"><center>Kesimpulan</center></td>
					<td id="td_template_bas" style="text-align: center;" class="kesimpulan-bas">
						<?php
							$getDataTidakLengkap = getDataBerkasTidakLengkap($data->id_hasil_pekerjaan, $this->session->userdata('id_pegawai'));
							$getDataLengkap = getDataBerkasLengkap($data->id_hasil_pekerjaan, $this->session->userdata('id_pegawai'));
							
							if($getDataTidakLengkap == $getDataLengkap){	
								echo 'Belum diperiksa';
								// echo 'Lengkap';
							}else{
								$tots = is_array($getDataLengkap) ? count($getDataLengkap) : 0;
								$tots2 = is_array($getDataTidakLengkap) ? count($getDataTidakLengkap) : 0;
								if($tots == 8){
									echo 'Lengkap';
								}elseif($getDataTidakLengkap){
									if($tots2 == 1){
										echo 'Tidak Lengkap';
									}
								}
							}
						?>
					</td>
				</tr>
			</table>
				  
			Berdasarkan hasil pemeriksaan administrasi hasil pekerjaan, maka dapat <!-- /tidak dapat --> dilakukan serah terma hasil pekerjaan dari PPK kepada PA/KPA.
		<div>
		</div>
		<table border="0" width="100%" style="border-collapse: collapse;">
			<tr>
				<td>
				Demikian Berita Serah Acara ini dibuat dengan sebenarnya dalam rangkap 3 (tiga) untuk dipergunakan sebagaimana mestinya.
				<table id="table_template_bas" border="0" style="text-align: center;" width="100%">			
					<?php 
					$nom = 1;
					$penyedia = ($data->kategori == 1) ? @$data->nama_perusahaan : '';
					$jabatan = (!empty($data->jabatan)) ? @$data->jabatan : 'Tenaga Ahli';
					foreach($data_pejabat_pphp as $rss){
						
						$panitia_pphp = (count($data_pejabat_pphp) > 1) ? '<td id="td_template_bas" width="50%">PANTIA PENERIMA HASIL PEKERJAAN</td>' : '<td id="td_template_bas" width="50%">PEJABAT PENERIMA HASIL PEKERJAAN</td>';
						
						$nomor = $nom++;
						if($nomor == 1){
							echo '						
								<tr>
									<td id="td_template_bas" width="50%">PEJABAT PEMBUAT KOMITMEN (PPK)<br>
									</td>
									'.$panitia_pphp.'
								</tr>
							';
						}
					?>
					<tr>
						<td id="td_template_bas" height="55px">
							<?
							if($nomor == 1){
								echo @$ttd_ppk_bapenerimaan; 
							}							
							?>
						</td>
						<td height="55px" style="vertical-align: center!importan;" class="cekttd_pphp<?php echo $nomor; ?>">					
							<?
							
							//cek dulu, apa sudah diceklis semuanya
							$getReturn = getDataHasilBerkas(false, $data->id_hasil_pekerjaan, $this->session->userdata('id_pegawai')); //total yg sudah di ceklis
							
							$getReturn = is_array($getReturn) ? count($getReturn): 0;
							$getTotalBerkas = getDataOpenBerkas(); //total berkas yg diopen
							$getTotalBerkas = is_array($getTotalBerkas) ? count($getTotalBerkas): 0;
							
							// var_dump($getTotalBerkas, $getReturn);exit;
							if($this->session->userdata('id_pegawai') == @$rss['id_pegawai_pphp']){								
								if($getReturn == @$getTotalBerkas){								
									/* if($getDataTidakLengkap){								
										if(count($getDataTidakLengkap) == 1)
										{
											echo @$rss['buttonTtd']; 
										}else{
											echo 'Lengkap';
										}
									} */
									echo @$rss['buttonTtd']; 
								}
							}else{
								echo @$rss['buttonTtd']; 
							}
							
							?>
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
									<td id="td_template_bas"><b><u>'.@$data->nama_pegawai_ppk.'</u></b></td>
									<td id="td_template_bas"><b><u>'.@$rss['nama_pegawai_pphp'].'</u></b></td>
								</tr>				
								<tr>
									<td id="td_template_bas">
									NIP. '.@$data->nip_pegawai_ppk.'
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