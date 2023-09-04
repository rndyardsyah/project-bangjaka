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
				$total_pembayaran = '';
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
				<td id="td_template_bapembayaran" width="15%"class="kiri">Kode Rek <span class="pull-right">:</span></td>
				<td id="td_template_bapembayaran" width="35%"><?php echo @$data_pembayaran[0]->kode_rek; ?></td>
				<td id="td_template_bapembayaran" width="15%" class="kiri">Nomor <span class="pull-right">:</span></td>
				<td id="td_template_bapembayaran" width="35%" class="kanan"><?php echo @$data_pembayaran[0]->no_ba_pembayaran; ?></td>
			</tr>
			<tr>
				<td id="td_template_bapembayaran" class="kiri" style="font-size: 10.5pt;">Nama Kegiatan<span class="pull-right">:</span></td>
				<td id="td_template_bapembayaran" class="kanan"><?php echo @$data_pembayaran[0]->nama_kegiatan; ?></td>
				<td id="td_template_bapembayaran" class="kiri">Tanggal <span class="pull-right">:</span></td>
				<td id="td_template_bapembayaran" class="kanan"><?php if(@$data_pembayaran[0]->tgl_ba_pembayaran){ echo GetFullDateFull(@$data_pembayaran[0]->tgl_ba_pembayaran); }else{ echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-'. date('Y');} ?>
				</td>
			</tr>
			<tr>
				<td id="td_template_bapembayaran" class="kiri">Nama Paket <span class="pull-right">:</span></td>
				<td id="td_template_bapembayaran" class="kanan"><?php echo @$data_pembayaran[0]->nama_pekerjaan; ?></td>
				<td id="td_template_bapembayaran" class="kiri"></td>
				<td id="td_template_bapembayaran" class="kanan"></td>
			</tr>
			<tr>
				<td id="td_template_bapembayaran" class="kiri-bawah">Lokasi <span class="pull-right">:</span></td>
				<td id="td_template_bapembayaran" class="kanan-bawah">-</td>
				<td id="td_template_bapembayaran" class="kiri-bawah"></td>
				<td id="td_template_bapembayaran" class="kanan-bawah"></td>
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
				<td id="td_template_bapembayaran" width="74%"><?php echo @$data_pembayaran[0]->nama_pegawai_pengguna_anggaran; ?></td>
			</tr>
			<tr>
				<td id="td_template_bapembayaran"></td>
				<td id="td_template_bapembayaran">Jabatan</td>
				<td id="td_template_bapembayaran">:</td>
				<td id="td_template_bapembayaran"><?php echo @$data_pembayaran[0]->nomenklatur_jabatan_pengguna_anggaran; ?></td>
			</tr>
			<tr >
				<td></td>
				<td colspan="3" height="3%" style="vertical-align : middle !important;">Selaku Pengguna Anggaran/Kuasa Pengguna Anggaran*, yang bertindak untuk dan atas nama Pemerintah Kota Tangerang, selanjutnya disebut <b>PIHAK KESATU</b>.</td>
			</tr>
			<tr>
				<td id="td_template_bapembayaran">II.</td>
				<td id="td_template_bapembayaran">Nama</td>
				<td id="td_template_bapembayaran">:</td>
				<td id="td_template_bapembayaran"><?php echo @$data_pembayaran[0]->nama_penyedia; ?></td>
			</tr>
			<tr>
				<td id="td_template_bapembayaran"></td>
				<td id="td_template_bapembayaran">Jabatan</td>
				<td id="td_template_bapembayaran">:</td>
				<td id="td_template_bapembayaran"><?php if($data_pembayaran[0]->kategori == 1){ echo @$data_pembayaran[0]->jabatan . ' ' . $data_pembayaran[0]->nama_perusahaan; }else{  echo 'Tenaga Ahli';  } ?></td>
			</tr>
			<tr>
				<td id="td_template_bapembayaran"></td>
				<td id="td_template_bapembayaran"><i>Perusahaan/ Pokmas</i></td>
				<td id="td_template_bapembayaran">:</td>
				<td id="td_template_bapembayaran"></td>
			</tr>
			<tr>
				<td id="td_template_bapembayaran"></td>
				<td id="td_template_bapembayaran">Alamat</td>
				<td id="td_template_bapembayaran">:</td>
				<td id="td_template_bapembayaran"><?php echo @$data_pembayaran[0]->alamat; ?></td>
			</tr>
			<tr >
				<td id="td_template_bapembayaran"></td>
				<td id="td_template_bapembayaran" colspan="3" height="3%" style="vertical-align : middle !important;">
				yang bertindak untuk dan atas nama PT/CV/Kelompok Masyarakat, selanjutnya disebut <b>PIHAK KEDUA</b></td>
			</tr>
		</table> 

		Dengan ini menyatakan :
		<ol>
		  <li>Kedua belah pihak telah setuju dan sepakat bahwa :</li>
		  <table width="100%" border="0">
			<tr>
				<td width="2%">a.</td>
				<td width="25%">Surat Perjanjian/SPK</td>
				<td width="2%">:</td>
				<td width="25%">Nomor</td>
				<td width="2%">:</td>
				<td><?php echo @$data_pembayaran[0]->no_spk; ?></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td>Tanggal</td>
				<td>:</td>
				<td><?php echo GetFullDateFull(@$data_pembayaran[0]->tgl_pekerjaan); ?></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td>Biaya</td>
				<td>:</td>
				<td>Rp <?php echo number_format(@$data_pembayaran[0]->nominal_bayar, 2); ?></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td>Waktu Pelaksanaan</td>
				<td>:</td>
				<td>.......</td>
			</tr>
			<tr>
				<td>b.</td>
				<td>Addendum 1</td>
				<td>:</td>
				<td>Nomor</td>
				<td>:</td>
				<td>.......</td>
			</tr>
			<tr>
				<td></td>
				<td rowspan="3"><i>(Apabila terdapat lebih dari satu adendum agar dicantumkan)</i></td>
				<td></td>
				<td>Tanggal</td>
				<td>:</td>
				<td>.......</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td>Biaya</td>
				<td>:</td>
				<td>.......</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td>Waktu Pelaksanaan</td>
				<td>:</td>
				<td>.......</td>
			</tr>
			<tr>
				<td>c.</td>
				<td colspan="5">Pembayaran dilakukan dengan cara bulanan/termin/sekaligus*</td>
			</tr>
		  </table>
		  <li><i>[Untuk pembayaran yang dilakukan bulanan/termin dengan nilai yang belum ditentukan/pasti]</i></li>
		  Berdasarkan Surat Perjanjian/Surat Perintah Kerja/Surat Pesanan* (kontrak) maka <b>PIHAK KEDUA</b> berhak menerima dari <b>PIHAK KESATU</b> pembayaran sebesar .....% dari nilai kontrak, dengan perincian sebagai berikut :
			<table width="100%" border="0">
				<tr>
					<td>a.</td>
					<td colspan="4">Pembayaran</td>
				</tr>
				<tr>				
					<td></td>
					<td>-</td>
					<td colspan="2">Total kontrak Rp. ..................... x .......%</td>
					<td>Rp................................</td>
				</tr>
				<tr>
					<td>b.</td>
					<td colspan="4">Potongan</td>
				</tr>
				<tr>
					<td width="2%"></td>
					<td width="2%">-</td>
					<td width="25%">Pembayaran uang muka</td>
					<td width="30%">Rp ........................</td>
					<td width="25%"></td>
				</tr>
				<tr>
					<td></td>
					<td>-</td>
					<td>Pembayaran sebelumnya</td>
					<td>:</td>
					<td></td>
				</tr>
				<?php
					foreach($data_pembayaran as $rzt){
				?>
				<tr>
					<td></td>
					<td></td>
					<td>Termin <?php echo getRomawi($rzt->termin); ?></td>
					<td>Rp <span class="pull-right" style="margin-right: 70px;"><?php echo number_format($rzt->nilai_pekerjaan, 2); ?></span></td>
					<td></td>
				</tr>
				<?php
					}				
				?>
				<tr>
					<td></td>
					<td>-</td>
					<td>Retensi 5%</td>
					<td>Rp ........................</td>
					<td></td>
				</tr>
				<tr>
					<td></td>
					<td>-</td>
					<td>Lain-lain</td>
					<td>Rp ........................</td>
					<td></td>
				</tr>
				<tr>
					<td></td>
					<td colspan="2">Jumlah potongan</td>
					<td></td>
					<td>Rp................................</td>
				</tr>
				<tr>
					<td>c.</td>
					<td colspan="2">Yang dibayarkan</td>
					<td></td>
					<td>Rp................................</td>
				</tr>
				<tr>
					<td>d.</td>
					<td colspan="2">Dibulatkan</td>
					<td></td>
					<td>Rp................................</td>
				</tr>
			</table>
			Terbilang : (..........................................................................................................................)
			<br>
			<b><i>Atau</i></b>
			<li><i>[Untuk pembayaran yang dilakukan bulanan/termin/sekaligus dengan nilai yang sudah ditentukan/pasti]</i></li>
			Berdasarkan Surat Perjanjian/Surat Perintah Kerja/Surat Pesanan* (kontrak) maka <b>PIHAK KEDUA</b> berhak menerima dari <b>PIHAK KESATU</b> pembayaran bulan/termin*...... sebesar Rp............................ terbilang (...........................................................................................).
			<br>
			Adapun rincian pembayaran sampai dengan berita acara pembayaran ini dibuat adalah sebagai berikut :
			<table width="100%">
				<?php
					$nomr = 1;
					$total_sudah_dibayar = 0;
					foreach($data_pembayaran as $rst){
						$total_sudah_dibayar += $rst->nilai_pekerjaan;
				?>
				<tr>
					<td width="2%"><?php echo $nomr++; ?>.</td>
					<td width="27%">Bulan/termin <?php echo getRomawi($rst->termin); ?></td>
					<td width="25%">Rp <span class="pull-right" style="margin-right: 70px;"><?php echo number_format($rst->nilai_pekerjaan, 2); ?></span></td>
					<td width="25%"></td>
				</tr>
				<?php
					}
				
				?>
				<tr>
					<td><?php echo $nomr++; ?>.</td>
					<td>Sisa yang belum dibayar</td>
					<td>Rp <span class="pull-right" style="margin-right: 70px;"><?php echo number_format($data_pembayaran[0]->nominal_bayar - $total_sudah_dibayar, 2); ?></span></td>
					<td width="25%"></td>
				</tr>
			</table>
			<li><i>[Untuk pembayaran yang dilakukan bulanan/termin dengan nilai yang belum ditentukan/pasti]</i>
			</li>
			<b>PIHAK KESATU</b> setuju melakukan pembayaran ....% dari nilai kontrak kepada <b>PIHAK KEDUA</b> melalui bank <?php echo @$data_pembayaran[0]->bank; ?> dengan nomor rekening <?php echo @$data_pembayaran[0]->no_rekening_penyedia; ?>, setelah <b>PIHAK KEDUA</b> menyerahkan jaminan uang muka/pelaksanaan/pemeliharaan* sebesar 5% dari nilai kontrak<br>
			<b><i>Atau</i></b>
			<li><i>[Untuk pembayaran yang dilakukan bulanan/termin/sekaligus dengan nilai yang sudah ditentukan/pasti]</i></li>
			<b>PIHAK KESATU</b> setuju melakukan pembayaran bulan/termin* .... sebesar Rp <?php echo number_format($data_pembayaran[0]->nominal_bayar - $total_sudah_dibayar, 2); ?> kepada <b>PIHAK KEDUA</b> melalui bank <?php echo @$data_pembayaran[0]->bank; ?> dengan nomor rekening <?php echo @$data_pembayaran[0]->no_rekening_penyedia; ?>, setelah <b>PIHAK KEDUA</b> menyerahkan jaminan uang muka/pelaksanaan/pemeliharaan* sebesar 5% dari nilai kontrak (khusus untuk jaminan pelaksanaan/pemeliharaan).
		</ol>

		<table id="table_template_bapembayaran" border="0">
			<tr>
				<td>
					<table id="table_template_bapembayaran" border="0" style="text-align: center;" width="100%">
						<tr>
							<td id="" colspan="2" width="50%">
								<div class="tab">
									<p>Demikian Berita Acara Pembayaran ini dibuat dan ditandatangani di Kota Tangerang pada tanggal tersebut di atas untuk dipergunakan sebagaimana mestinya.</p>
								</div>
							</td>
						</tr>
						<tr style="font-weight: bold;">
							<td id="td_template_bapembayaran" width="50%">PIHAK KEDUA</td>
							<td id="td_template_bapembayaran" width="50%">PIHAK PERTAMA</td>
						</tr>
						<tr style="font-weight: bold;">
							<td id="td_template_bapembayaran"><?php if($data_pembayaran[0]->kategori == 1){ echo @$data_pembayaran[0]->nama_perusahaan; }else{ echo 'PENYEDIA JASA PERORANGAN'; } ?>
							</td>
							<td id="td_template_bapembayaran">PENGGUNA ANGGARAN</td>
						</tr>
						<tr>
							<td id="td_template_bapembayaran" height="55px"></td>
							<td id="td_template_bapembayaran">
								<?=@$ttd_ppk; ?>
							</td>
						</tr>
						<tr>
							<td id="td_template_bapembayaran"><b><u><?php echo @$data_pembayaran[0]->nama_penyedia; ?></u></b></td>
							<td id="td_template_bapembayaran"><b><u><?php echo @$data_pembayaran[0]->nama_pegawai_pengguna_anggaran; ?></u></b></td>
						</tr>
						<tr>
							<td id="td_template_bapembayaran"><?php if($data_pembayaran[0]->kategori == 1){ echo @$data_pembayaran[0]->jabatan; }else{  echo 'Tenaga Ahli';  } ?></td>
							<td id="td_template_bapembayaran">NIP. <?php echo @$data_pembayaran[0]->nip_pegawai_pengguna_anggaran; ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		• Coret yang tidak perlu
	</div>
</body>
</html>