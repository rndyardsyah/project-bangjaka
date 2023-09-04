<html> 
<head>

<style>
	#template_bapembayaran {
		font-family: sans-serif;
		font-size: 11pt;
		/* margin-left: 2cm; */
	}
	#table_template_bapembayaran {					
		/* border-collapse: collapse; */
		border-spacing: 0;
		margin-top: 10px;
		margin-bottom: 10px;
	}
	#td_template_bapembayaran {					
		vertical-align : top;
	}
	p{
		text-align:justify;
		line-height:20px;
	}
	.pull-right{
		float: right !important;
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
<body id="template_bapembayaran">		
	
	<div id="batas-margin">	

			
			<?php
				$total = count($data_pembayaran);
				
				$no = 1;
				$termin_view = '';
				$total_pembayaran = 0;
				$pekerjaan_view = '';
				$rincian_view = '';
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
					
					if($data_pembayaran[0]->pekerjaan_termin == 1){
						$termins = '';
					}else{
						$termins = 'Termin '. getRomawi($row->termin);
					}
					
					$pekerjaan_view .= 'Laporan '. $termins . ' ' . $row->nama_pekerjaan . ' sebanyak 5 CD <br>';
					$nama_penyedia_text = ($data_pembayaran[0]->kategori == 1) ? $data_pembayaran[0]->nama_perusahaan : $data_pembayaran[0]->nama_penyedia;
					
					$rincian_view .= '
						<li>Surat Penyerahan Hasil Pekerjaan '.$row->nama_pekerjaan.' '.$termins.' dari '.$nama_penyedia_text.' Nomor: '.@$row->no_srt_penyerahan.', tanggal '.GetFullDateFull(@$row->tgl_srt_penyerahan).'</li>
						<li>Berita Acara Penerimaan Hasil Pekerjaan '.$row->nama_pekerjaan.' '.$termins.', Nomor: '.@$row->no_bas_penerimaan.', tanggal '.GetFullDateFull(@$row->tanggal_bas).'</li>
						<li>Berita Acara Serah Terima Pekerjaan '.$row->nama_pekerjaan.' '.$termins.' dari '.$nama_penyedia_text.', Nomor: '.@$row->no_bast.', tanggal '.GetFullDateFull(@$row->tgl_bast).'</li>
					';
				}
				
			?>

		<table id="table_template_bapembayaran" width="100%" style="border-collapse: collapse;" align="center">
			<tr style="text-align: center; font-weight: bold; text-align: center;">
				<td id="td_template_bapembayaran" colspan="2" class="full kop">
				PEMERINTAH KOTA TANGERANG <br><?php echo strtoupper(getNamaUnor(substr(@$data_pembayaran[0]->kode_unor,0,5))); ?><br>
				Kegiatan : <br>
				<?php echo @$data_pembayaran[0]->nama_kegiatan; ?>
				</td>
				<td id="td_template_bapembayaran" colspan="2" class="full kop2" style="vertical-align: middle!important; ">BERITA ACARA <br>PEMBAYARAN</td>
			</tr>
			<tr>
				<td id="td_template_bapembayaran" width="15%"class="kiri">Pekerjaan <span class="pull-right">:</span></td>
				<td id="td_template_bapembayaran" width="35%"><?php echo @$data_pembayaran[0]->nama_pekerjaan; ?></td>
				<td id="td_template_bapembayaran" width="15%" class="kiri">Nomor <span class="pull-right">:</span></td>
				<td id="td_template_bapembayaran" width="35%" class="kanan"><?php echo @$data_pembayaran[0]->no_ba_pembayaran; ?></td>
			</tr>
			<tr>
				<td id="td_template_bapembayaran" rowspan="2" class="kiri-bawah">Output <span class="pull-right">:</span></td>
				<td id="td_template_bapembayaran" rowspan="2" class="kanan-bawah">
					<?php 		
						foreach($data_pembayaran as $row){
							
							if(!empty($row->data_uraian_pekerjaan)){
								foreach($row->data_uraian_pekerjaan as $rsss){
									echo $rsss['uraian'] . ' '. $rsss['volume'].' '. $rsss['satuan'] . '<br>';
								}
							}
							
						}	
					?>
				</td>
				<td id="td_template_bapembayaran" class="kiri">Tanggal <span class="pull-right">:</span></td>
				<td id="td_template_bapembayaran" class="kanan"><?php if(@$data_pembayaran[0]->tgl_ba_pembayaran){ echo GetFullDateFull(@$data_pembayaran[0]->tgl_ba_pembayaran); }else{ echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-'. date('Y');} ?>
				</td>
			</tr>
			<tr>
				<td id="td_template_bapembayaran" class="kiri-bawah">Lampiran <span class="pull-right">:</span></td>
				<td id="td_template_bapembayaran" class="kanan-bawah">-</td>
			</tr>
		</table>

		<p style="text-align: justify; text-indent: 2cm;">
		Pada Hari ini, 
		<?php 
		if(@$data_pembayaran[0]->tgl_ba_pembayaran){ 
			echo GetDayDate(@$data_pembayaran[0]->tgl_ba_pembayaran); 
		}else{ 
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		} ?> 
		Tanggal <i><?php 
		if(@$data_pembayaran[0]->tgl_ba_pembayaran){
			echo ucwords(terbilang(date('d', strtotime(@$data_pembayaran[0]->tgl_ba_pembayaran))));
		}else{
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		} 
		?></i> bulan <i>
		<?php 
		if(@$data_pembayaran[0]->tgl_ba_pembayaran){ 
			echo GetMonth(date('m', strtotime(@$data_pembayaran[0]->tgl_ba_pembayaran)));
		}else{
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		} 
		?></i> tahun <i><?php 
			if(@$data_pembayaran[0]->tgl_ba_pembayaran){
				echo ucwords(terbilang(date('Y', strtotime(@$data_pembayaran[0]->tgl_ba_pembayaran))));
			}else{ 
				echo ucwords(terbilang(date('Y')));
			} ?></i> 
		(
		<?php 
		if(@$data_pembayaran[0]->tgl_ba_pembayaran){
			echo date('d-m-Y', strtotime(@$data_pembayaran[0]->tgl_ba_pembayaran)); 
		}else{ 
			echo '&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;-'. date('Y', strtotime(@$data_pembayaran[0]->cdd)); 
			} ?>), Kami yang bertandatangan dibawah ini:
		</p>

		<table id="table_template_bapembayaran" border="0" width="100%" style="border-collapse: collapse; padding-top: 5px; padding-bottom: 5px;">
			<tr>
				<td id="td_template_bapembayaran" width="3%">I.</td>
				<td id="td_template_bapembayaran" width="20%">Nama</td>
				<td id="td_template_bapembayaran" width="3%">:</td>
				<td id="td_template_bapembayaran" width="74%"><?php echo @$data_pembayaran[0]->nama_pegawai_ppk; ?></td>
			</tr>
			<tr>
				<td id="td_template_bapembayaran"></td>
				<td id="td_template_bapembayaran">Jabatan</td>
				<td id="td_template_bapembayaran">:</td>
				<td id="td_template_bapembayaran"><?php echo @$data_pembayaran[0]->nomenklatur_jabatan_ppk; ?> <br> Selaku Pejabat Pembuat Komitmen (PPK)</td>
			</tr>
			<tr>
				<td id="td_template_bapembayaran"></td>
				<td id="td_template_bapembayaran">Alamat Kantor</td>
				<td id="td_template_bapembayaran">:</td>
				<td id="td_template_bapembayaran">
				<?php echo getNamaUnor(substr(@$data_pembayaran[0]->kode_unor,0,5)); ?> Kota Tangerang <br>
				Gedung Pusat Pemerintahan Lt. IV <br>
				Jalan Satria Sudirman No.1 –   Kota Tangerang
				</td>
			</tr>
			<tr >
				<td></td>
				<td colspan="3" height="3%" style="vertical-align : middle !important;">Yang selanjutnya disebut <b><u>PIHAK PERTAMA</u></b></td>
			</tr>
			<tr>
				<td id="td_template_bapembayaran" width="3%">II.</td>
				<td id="td_template_bapembayaran" width="20%">Nama</td>
				<td id="td_template_bapembayaran" width="3%">:</td>
				<td id="td_template_bapembayaran" width="74%"><?php echo @$data_pembayaran[0]->nama_penyedia; ?></td>
			</tr>
			<tr>
				<td id="td_template_bapembayaran" width="3%"></td>
				<td id="td_template_bapembayaran" width="20%">Jabatan</td>
				<td id="td_template_bapembayaran" width="3%">:</td>
				<td id="td_template_bapembayaran" width="74%"><?php 
				if(!empty($data_pembayaran[0]->bentuk)){
					if($data_pembayaran[0]->kategori == '1'){ echo @$data_pembayaran[0]->jabatan . ' ' . $data_pembayaran[0]->nama_perusahaan; }else{ echo @$data_pembayaran[0]->jabatan; }
				}
				else{  echo 'Tenaga Ahli';  } ?></td>
			</tr>
			<tr>
				<td id="td_template_bapembayaran" width="3%"></td>
				<td id="td_template_bapembayaran" width="20%">Alamat</td>
				<td id="td_template_bapembayaran" width="3%">:</td>
				<td id="td_template_bapembayaran" width="74%"><?php echo @$data_pembayaran[0]->alamat; ?></td>
			</tr>
			<tr >
				<td id="td_template_bapembayaran"></td>
				<td id="td_template_bapembayaran" colspan="3" height="3%" style="vertical-align : middle !important;">Yang selanjutnya disebut <b><u>PIHAK KEDUA</u></b></td>
			</tr>
		</table> 

		<b>Kedua belah pihak berdasarkan:</b>
		<ol>
		  <li>Surat Perintah Kerja (SPK) Nomor: <?php echo @$data_pembayaran[0]->no_spk; ?>, tertanggal <?php echo GetFullDateFull(@$data_pembayaran[0]->tgl_pekerjaan); ?></li>
		  <li>Surat Nota Dinas Pencairan Nomor Hasil <?php echo @$data_pembayaran[0]->nama_pekerjaan; ?> Nomor: <?php echo @$data_pembayaran[0]->nota_dinas_pencairan; ?>, tanggal <?php if(@$data_pembayaran[0]->tgl_nota_dinas_pencairan){ echo GetFullDateFull(@$data_pembayaran[0]->tgl_nota_dinas_pencairan); }else{ echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-'.date('Y', strtotime(@$data_pembayaran[0]->cdd)); }?></li>
		  
		  <?php echo $rincian_view; ?>
		  
		  <li>Surat Permohonan Pembayaran Pekerjaan dari <?php if($data_pembayaran[0]->kategori == 1){ echo $data_pembayaran[0]->nama_perusahaan; }else{  echo $data_pembayaran[0]->nama_penyedia;  } ?>, Nomor: <?php echo @$data_pembayaran[0]->no_permohonan_pembayaran; ?>, tanggal <?php if(@$data_pembayaran[0]->tgl_permohonan_pembayaran){ echo GetFullDateFull(@$data_pembayaran[0]->tgl_permohonan_pembayaran); }else{ echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-'. date('Y');} ?></li>
		</ol>

		<p>Bahwa :</p>


		<ol>
			<li>PIHAK KEDUA telah menerima pembayaran dari PIHAK PERTAMA sebesar <b>Rp. <?php echo @number_format($total_pembayaran , 0 ,'' , '.').',-'; ?> (<?php echo ucfirst(terbilang($total_pembayaran)); ?> rupiah)</b> sudah termasuk pajak-pajak yang berlaku;</li>
			<li>Pembayaran PIHAK PERTAMA kepada PIHAK KEDUA <b>Rp. <?php echo @number_format($total_pembayaran , 0 ,'' , '.').',-'; ?> (<?php echo ucfirst(terbilang($total_pembayaran)); ?> rupiah)</b> termasuk pajak-pajak yang berlaku. Pembayaran tersebut dilakukan melalui transfer ke Rekening <?php echo @$data_pembayaran[0]->bank; ?>  dengan nomor Rek: <?php echo @$data_pembayaran[0]->no_rekening_penyedia; ?>  An. <?php echo @$data_pembayaran[0]->atas_nama_rekening; ?> NPWP : <?php echo @$data_pembayaran[0]->npwp; ?>.</li>
		</ol>

		<div>
		</div>

		<table id="table_template_bapembayaran" border="0">
			<tr>
				<td>
					<table id="table_template_bapembayaran" border="0" style="text-align: center;" width="100%">
						<tr>
							<td id="" colspan="2" width="50%">
								<div class="tab">
									<p>Demikian Berita Acara Pembayaran ini dibuat dan ditandatangani oleh Kedua Belah PIHAK di Kota Tangerang pada tanggal tersebut diatas untuk dipergunakan seperlunya.</p>
								</div>
							</td>
						</tr>
						<tr style="font-weight: bold;">
							<td id="td_template_bapembayaran" width="50%">Yang Menerima Pembayaran,</td>
							<td id="td_template_bapembayaran" width="50%">Yang Membayar,</td>
						</tr>
						<tr style="font-weight: bold; text-decoration: underline;">
							<td id="td_template_bapembayaran" width="50%">PIHAK KEDUA</td>
							<td id="td_template_bapembayaran" width="50%">PIHAK PERTAMA</td>
						</tr>
						<tr>
							<td id="td_template_bapembayaran" width="50%">PENYEDIA BARANG/ JASA
							<br><?php if($data_pembayaran[0]->kategori == 1){ echo @$data_pembayaran[0]->nama_perusahaan; } ?>
							</td>
							<td id="td_template_bapembayaran" width="50%">PEJABAT PEMBUAT KOMITMEN <br>(PPK)</td>
						</tr>
						<tr>
							<td id="td_template_bapembayaran" height="55px"></td>
							<td id="td_template_bapembayaran">
								<?=@$ttd_ppk; ?>
							</td>
						</tr>
						<tr>
							<td id="td_template_bapembayaran"><b><u><?php echo @$data_pembayaran[0]->nama_penyedia; ?></u></b></td>
							<td id="td_template_bapembayaran"><b><u><?php echo @$data_pembayaran[0]->nama_pegawai_ppk; ?></u></b></td>
						</tr>
						<tr>
							<td id="td_template_bapembayaran"><?php if(!empty($data_pembayaran[0]->jabatan)){ echo @$data_pembayaran[0]->jabatan; }else{  echo 'Tenaga Ahli';  } ?></td>
							<td id="td_template_bapembayaran">NIP. <?php echo @$data_pembayaran[0]->nip_pegawai_ppk; ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</body>
</html>