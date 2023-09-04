<html> 
<head>

<style>
	#template_bast {
		font-family: sans-serif;
		font-size: 11pt;
		margin-left: 2cm;
	}
	#table_template_bast {					
		/* border-collapse: collapse; */
		border-spacing: 0;
		margin-top: 10px;
		margin-bottom: 10px;
	}
	#td_template_bast {					
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
	.penyedia {
		float: right;
	}
	ol{
		text-align:justify;
		margin-top: 15px;
		margin-left: 0;
		margin-right: 0;
		margin-bottom: 15px;
		padding-left: 20px;
	}
	.pull-right{
		float: right !important;
	}
	.full{
		border-top: 1px solid;
		border-bottom: 1px solid;
		border-left: 1px solid;
		border-right: 1px solid;
	}
	.kiri{
		border-left: 1px solid;
	}
	.kiri-bawah{
		border-left: 1px solid;
		border-bottom: 1px solid;
	}
	.kanan{
		border-right: 1px solid;
	}
	.kanan-bawah{
		border-right: 1px solid;
		border-bottom: 1px solid;
	}
	.kop{
		font-size: 12pt;
	}
	.kop2{
		font-size: 18pt;
	}
	#batas-margin {
		/* padding : 50px;
		margin-left: 2cm; */
	}
</style>
</head>
<?php
	$termin = ($data->pekerjaan_termin == 1) ? '' : 'Termin ' . getRomawi($data->termin);
	$type_kontrak = getTypeKontrak(@$data);
?>
<body id="template_bast">		

	<div id="batas-margin" style="margin-bottom: 25px;">	
		<table id="table_template_bast" width="100%" style="border-collapse: collapse;" align="center">
			<tr style="text-align: center; font-weight: bold; text-align: center;">
				<td id="td_template_bast" colspan="2" class="full kop">
				PEMERINTAH KOTA TANGERANG <br><?php echo strtoupper(getNamaUnor(substr(@$data->kode_unor,0,5))); ?><br>
				Kegiatan : <br>
				<?php echo @$data->nama_kegiatan; ?>
				</td>
				<td id="td_template_bast" colspan="2" class="full kop2">BERITA ACARA <br>SERAH TERIMA <br>PEKERJAAN</td>
			</tr>
			<tr>
				<td id="td_template_bast" width="14%" class="kiri">Pekerjaan <span class="pull-right">:</span></td>
				<td id="td_template_bast" width="35%"><?php echo @$data->nama_pekerjaan; ?></td>
				<td id="td_template_bast" width="14%"  class="kiri">Nomor <span class="pull-right">:</span></td>
				<td id="td_template_bast" width="35%" class="kanan"><?php echo @$data->no_bast; ?></td>
			</tr>
			<tr>
				<td id="td_template_bast" rowspan="2" class="kiri-bawah">Output <span class="pull-right">:</span></td>
				<td id="td_template_bast" rowspan="2" class="kanan-bawah">
					<?php 		
						foreach($data->data_uraian_pekerjaan as $row){
							echo $row['uraian'] . ' '. $row['volume'].' '. $row['satuan'] . '<br>';
						}	
					?>
				</td>
				<td id="td_template_bast" class="kiri">Tanggal <span class="pull-right">:</span></td>
				<td id="td_template_bast" class="kanan"><?php echo GetFullDateFull(@$data->tgl_bast); ?></td>
			</tr>
			<tr>
				<td id="td_template_bast" class="kiri-bawah">Lampiran <span class="pull-right">:</span></td>
				<td id="td_template_bast" class="kanan-bawah">1 (satu) Set</td>
			</tr>
		</table>

		<p style="text-align: justify; text-indent: 2cm;">
		Pada Hari ini, <?php echo GetDayDate(@$data->tgl_bast); ?> Tanggal <i><?php echo ucwords(terbilang(date('d', strtotime(@$data->tgl_bast)))); ?></i> bulan <i><?php echo GetMonth(date('m', strtotime(@$data->tgl_bast))); ?></i> tahun <i><?php echo ucwords(terbilang(date('Y', strtotime(@$data->tgl_bast)))); ?></i> (<?php echo date('d-m-Y', strtotime(@$data->tgl_bast)); ?>), Kami yang bertanda tangan dibawah ini:
		</p>

		<table id="table_template_bast" border="0" width="100%" style="border-collapse: collapse; padding-top: 5px; padding-bottom: 5px;">
			<tr>
				<td id="td_template_bast" width="3%">I.</td>
				<td id="td_template_bast" width="20%">Nama</td>
				<td id="td_template_bast" width="3%">:</td>
				<td id="td_template_bast" width="74%"><?php echo @$data->nama_pegawai_ppk; ?></td>
			</tr>
			<tr>
				<td id="td_template_bast"></td>
				<td id="td_template_bast">Jabatan</td>
				<td id="td_template_bast">:</td>
				<td id="td_template_bast"><?php echo @$data->nomenklatur_jabatan_ppk; ?> <br> Selaku Pejabat Pembuat Komitmen (PPK)</td>
			</tr>
			<tr>
				<td id="td_template_bast"></td>
				<td id="td_template_bast">Alamat Kantor</td>
				<td id="td_template_bast">:</td>
				<td id="td_template_bast">
				<?php echo getNamaUnor(substr(@$data->kode_unor,0,5)); ?> Kota Tangerang <br>
				Gedung Pusat Pemerintahan Lt. IV <br>
				Jalan Satria Sudirman No.1 –   Kota Tangerang
				</td>
			</tr>
			<tr >
				<td id="td_template_bast"></td>
				<td id="td_template_bast" colspan="3" height="3%" style="vertical-align : middle !important;">Yang selanjutnya disebut <b><u>PIHAK PERTAMA</u></b></td>
			</tr>
			<tr>
				<td id="td_template_bast" width="3%">II.</td>
				<td id="td_template_bast" width="20%">Nama</td>
				<td id="td_template_bast" width="3%">:</td>
				<td id="td_template_bast" width="74%"><?php echo @$data->nama_penyedia; ?></td>
			</tr>
			<tr>
				<td id="td_template_bast" width="3%"></td>
				<td id="td_template_bast" width="20%">Jabatan</td>
				<td id="td_template_bast" width="3%">:</td>
				<td id="td_template_bast" width="74%"><?php 
				if(!empty($data->jabatan))
				{ 
					if($data->kategori == '1'){
						echo @$data->jabatan . ' ' . $data->nama_perusahaan; 
					}else{
						echo @$data->jabatan; 
					}				
				}
				else{ echo 'Tenaga Ahli';  } ?>
				<br>Selaku Penyedia Barang/Jasa
				</td>
			</tr>
			<tr>
				<td id="td_template_bast" width="3%"></td>
				<td id="td_template_bast" width="20%">Alamat</td>
				<td id="td_template_bast" width="3%">:</td>
				<td id="td_template_bast" width="74%"><?php echo @$data->alamat; ?></td>
			</tr>
			<tr >
				<td id="td_template_bast"></td>
				<td id="td_template_bast" colspan="3" height="3%" style="vertical-align : middle !important;">Yang selanjutnya disebut <b><u>PIHAK KEDUA</u></b></td>
			</tr>
		</table> 

		<b>Kedua belah pihak berdasarkan:</b>
		<ol>
		  <li><?php echo $type_kontrak; ?> Nomor: <?php echo @$data->no_spk; ?>, tertanggal <?php echo GetFullDateFull(@$data->tgl_pekerjaan); ?></li>
		  
		  
		  <?php
		  
			if(@$data_adendum){
				$noms = 1;
				foreach($data_adendum as $rsa){					
					echo '<li>Adendum '.$noms++. ' : '.$rsa['no_adendum']. ', tertanggal '. GetFullDateFull($rsa['tgl_adendum']), '</li>';
				}
			}
		  
		  ?>
		  <li>Surat Penyerahan Hasil Pekerjaan <?php echo @$data->nama_pekerjaan; ?> <?php echo $termin; ?> dari <?php if($data->kategori == 1){ echo $data->nama_perusahaan; }else{  echo $data->nama_penyedia;  } ?> Nomor: <?php echo @$data->no_srt_penyerahan; ?>, tanggal <?php echo GetFullDateFull(@$data->tgl_srt_penyerahan); ?></li>		
		</ol>

		<div class="tab">
			<p>Dengan demikian maka kedua belah Pihak telah setuju dan sepakat mengenai hasil Pekerjaan <?php echo @$data->nama_pekerjaan; ?> <?php echo $termin; ?> pada Kegiatan <?php echo @$data->nama_kegiatan; ?> pada Dinas Komunikasi dan Informatika Kota Tangerang Tahun Anggaran  <?php echo date('Y', strtotime($data->tgl_pekerjaan)); ?> dalam Berita Acara Serah Terima Pekerjaan dengan ketentuan-ketentuan sebagai berikut :</p>
		</div>

		<?php $nom = 1; ?>
		<table id="table_template_bast" border="0" width="100%" style="text-align: justify;">
			<tr>
				<td width="2%" id="td_template_bast">a.</td>
				<td id="td_template_bast">PIHAK KEDUA menyerahkan kepada PIHAK PERTAMA dan PIHAK PERTAMA menerima dari PIHAK KEDUA Hasil Pekerjaan untuk:</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<table border="0" width="100%">				
						<tr>
							<td id="td_template_bast" width="3%"><?php echo $nom++; ?>.</td>
							<td id="td_template_bast" width="20%">Pekerjaan</td>
							<td id="td_template_bast" width="3%">:</td>
							<td id="td_template_bast" width="74%"><?php echo @$data->nama_pekerjaan; ?> <?php echo $termin; ?></td>
						</tr>
						<tr>
							<td id="td_template_bast"><?php echo $nom++; ?>.</td>
							<td id="td_template_bast">Kegiatan</td>
							<td id="td_template_bast">:</td>
							<td id="td_template_bast"><?php echo @$data->nama_kegiatan; ?></td>
						</tr>
						<tr>
							<td id="td_template_bast"><?php echo $nom++; ?>.</td>
							<td id="td_template_bast">Lokasi</td>
							<td id="td_template_bast">:</td>
							<td id="td_template_bast">Kota Tangerang</td>
						</tr>				
						<?php
						$nor = 1;
						foreach($data->data_uraian_pekerjaan as $row){
							if($nor++ > 1){
								echo '
									<tr>
										<td id="td_template_bast"></td>
										<td id="td_template_bast"></td>
										<td id="td_template_bast"></td>
										<td id="td_template_bast">'.$row['uraian'] . ' '. $row['volume'].' '. $row['satuan'] .'</td>
									</tr>
									';

							}else{
								echo '
									<tr>
										<td id="td_template_bast">'.$nom++.'.</td>
										<td id="td_template_bast">Output</td>
										<td id="td_template_bast">:</td>
										<td id="td_template_bast">'.$row['uraian'] . ' '. $row['volume'].' '. $row['satuan'] .'</td>
									</tr>
									';
							}
						}			
						?>				
						<tr>
							<td id="td_template_bast"><?php echo $nom++; ?>.</td>
							<td id="td_template_bast">Unit Kerja</td>
							<td id="td_template_bast">:</td>
							<td id="td_template_bast"><?php echo getNamaUnor(substr(@$data->kode_unor,0,5)); ?> Kota Tangerang</td>
						</tr>
						<tr>
							<td id="td_template_bast"><?php echo $nom++; ?>.</td>
							<td id="td_template_bast">Tahun Anggaran</td>
							<td id="td_template_bast">:</td>
							<td id="td_template_bast"><?php echo date('Y', strtotime($data->tgl_pekerjaan)); ?></td>
						</tr>
						<?php
							if(($data->dpa_skpd == '1.02.10.1.02.10.01.15.108.' || $data->dpa_skpd == '1.02.10.1.02.10.01.15.89.' ) && date('m', strtotime($data->tgl_bast)) >= 9){
						?>
						<tr>
							<td id="td_template_bast"><?php echo $nom++; ?>.</td>
							<td id="td_template_bast">No. DPPA SKPD</td>
							<td id="td_template_bast">:</td>
							<td id="td_template_bast"><?php echo @$data->dpa_skpd; ?></td>
						</tr>
						<?php
							}else{
						?>						
						<tr>
							<td id="td_template_bast"><?php echo $nom++; ?>.</td>
							<td id="td_template_bast">No. DPA SKPD</td>
							<td id="td_template_bast">:</td>
							<td id="td_template_bast"><?php echo @$data->dpa_skpd; ?></td>
						</tr>
						<?php
							}
						?>
						<tr>
							<td id="td_template_bast"><?php echo $nom++; ?>.</td>
							<td id="td_template_bast">Kode Rek.</td>
							<td id="td_template_bast">:</td>
							<td id="td_template_bast"><?php echo @$data->kode_rek; ?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td id="td_template_bast">b.</td>
				<td id="td_template_bast">
					Berdasarkan <?php echo $type_kontrak; ?> Nomor: <?php echo @$data->no_spk; ?>, tertanggal <?php echo GetFullDateFull(@$data->tgl_pekerjaan); ?>, 
					<?php
					  
						if(@$data_adendum){
							$noms = 1;
							echo 'dan ';
							foreach($data_adendum as $rsa){					
								echo 'Adendum '.$noms++. ' : '.$rsa['no_adendum'].', tertanggal '.GetFullDateFull($rsa['tgl_adendum']).', ';
							}
						}
					  
					?>
					maka <b>PIHAK KEDUA</b> berhak menerima pembayaran dari <b>PIHAK PERTAMA</b> sebesar  Nilai Pekerjaan yaitu sebesar  <b>Rp. <?php echo number_format(@$data->nilai_pekerjaan , 0 ,'' , '.').',-'; ?> (<?php echo @$data->nilai_pekerjaan_terbilang; ?> rupiah)</b> sudah termasuk Pajak <?php if($data->kategori == 1){ echo 'PPN dan '; } ?>PPh.
				</td>
			</tr>
		</table>


		<div class="tab">
			<p>Demikian Berita Acara Serah Terima Pekerjaan  ini dibuat dan ditandatangani di Kota Tangerang pada tanggal tersebut diatas untuk dipergunakan seperlunya</p>
		</div>


		<table id="table_template_bast" border="0" style="text-align: center;" width="100%">
			<tr style="font-weight: bold; text-decoration: underline;">
				<td id="td_template_bast" width="50%">PIHAK KEDUA</td>
				<td id="td_template_bast" width="50%">PIHAK PERTAMA</td>
			</tr>
			<tr>
				<td id="td_template_bast" width="50%">PENYEDIA BARANG/ JASA
				<br><?php if($data->kategori == 1){ echo @$data->nama_perusahaan; } ?>
				</td>
				<td id="td_template_bast" width="50%">PEJABAT PEMBUAT KOMITMEN <br>(PPK)</td>
			</tr>
			<tr>
				<td id="td_template_bast" height="55px"></td>
				<td id="td_template_bast" height="55px">
					<?=@$ttd_ppk_bast; ?>
				</td>
			</tr>
			<tr>
				<td id="td_template_bast"><b><u><?php echo @$data->nama_penyedia; ?></u></b></td>
				<td id="td_template_bast"><b><u><?php echo @$data->nama_pegawai_ppk; ?></u></b></td>
			</tr>
			<tr>
				<td id="td_template_bast"><?php 
				if(!empty($data->jabatan))
				{ 
					if($data->kategori == '1'){
						echo @$data->jabatan . ' ' . $data->nama_perusahaan; 
					}else{
						echo @$data->jabatan; 
					}				
				}
				else{ echo 'Tenaga Ahli';  } ?></td>
				<td id="td_template_bast">NIP. <?php echo @$data->nip_pegawai_ppk; ?></td>
			</tr>
		</table>
	</div>
</body>
</html>