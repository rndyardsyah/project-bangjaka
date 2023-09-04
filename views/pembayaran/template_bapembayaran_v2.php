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
				
				$type_kontrak = getTypeKontrak(@$data_pembayaran[0]);
				
			?>

		<table id="table_template_bapembayaran" width="100%" style="border-collapse: collapse;" align="center">
			<tr style="text-align: center; font-weight: bold; text-align: center;">
				<td id="td_template_bapembayaran" colspan="2" class="full kop">
				PEMERINTAH KOTA TANGERANG. <br><?php echo strtoupper(getNamaUnor(substr(@$data_pembayaran[0]->kode_unor,0,5))); ?><br>
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
				<td id="td_template_bapembayaran" class="kanan-bawah">KOTA TANGERANG</td>
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
			} 
		?>), Kami yang bertandatangan dibawah ini:
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
				<td colspan="3" height="3%" style="vertical-align : middle !important;">Selaku <?php if(@$data_pembayaran[0]->kuasa_anggaran == 0){ echo 'Pengguna Anggaran'; }else if(@$data_pembayaran[0]->kuasa_anggaran == 1){ echo 'Kuasa Anggaran'; }?>, yang bertindak untuk dan atas nama Pemerintah Kota Tangerang, selanjutnya disebut <b>PIHAK KESATU</b>.</td>
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
				<td id="td_template_bapembayaran"><?php 
				if(!empty($data_pembayaran[0]->jabatan)){
					if($data_pembayaran[0]->kategori == '1'){ echo @$data_pembayaran[0]->jabatan . ' ' . $data_pembayaran[0]->nama_perusahaan; }else{ echo @$data_pembayaran[0]->jabatan; }
				}else{  echo 'Tenaga Ahli';  } ?></td>
			</tr>
			<tr>
				<td id="td_template_bapembayaran"></td>
				<td id="td_template_bapembayaran"><i>Perusahaan/ Pokmas</i></td>
				<td id="td_template_bapembayaran">:</td>
				<td id="td_template_bapembayaran">-</td>
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
				yang bertindak untuk dan atas nama 
				<?php if($data_pembayaran[0]->kategori == 1){ echo @$data_pembayaran[0]->nama_perusahaan; }else{ echo 'Penyedia Jasa Perorangan'; } ?>, selanjutnya disebut <b>PIHAK KEDUA</b></td>
			</tr>
		</table> 
		
		Dengan ini menyatakan :
		<ol>
		  <li>Kedua belah pihak telah setuju dan sepakat bahwa :
				  <ol type="a">
					  <?php $nomerst = 1; ?>
					  <table width="100%" border="0">
						<tr>
							<td id="td_template_bapembayaran" width="2%"><?php echo getCharacter($nomerst++); ?>.</td>
							<td id="td_template_bapembayaran" width="25%"><?php echo $type_kontrak; ?></td>
							<td id="td_template_bapembayaran" width="2%">:</td>
							<td id="td_template_bapembayaran" width="25%">Nomor</td>
							<td id="td_template_bapembayaran" width="2%">:</td>
							<td id="td_template_bapembayaran"><?php echo @$data_pembayaran[0]->no_spk; ?></td>
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
							<td><?php echo @$data_pembayaran[0]->waktu_pelaksanaan; ?></td>
						</tr>
						<?php
							$nomers = 1;
							$biaya_terakhir_adendum = 0;
							if($data_adendum){
							foreach($data_adendum as $dta){
								$biaya_terakhir_adendum = $dta['biaya_adendum'];
						?>
						<tr>
							<td><?php echo getCharacter($nomerst++); ?>.</td>
							<td>Addendum <?php echo $nomers++; ?></td>
							<td>:</td>
							<td>Nomor</td>
							<td>:</td>
							<td><?php echo $dta['no_adendum']; ?></td>
						</tr>
						<tr>
							<td></td>
							<td rowspan="3"><i>(Apabila terdapat lebih dari satu adendum agar dicantumkan)</i></td>
							<td></td>
							<td>Tanggal</td>
							<td>:</td>
							<td><?php echo GetFullDateFull($dta['tgl_adendum']); ?></td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td>Biaya</td>
							<td>:</td>
							<td>Rp <?php echo number_format($dta['biaya_adendum'], 2); ?></td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td>Waktu Pelaksanaan</td>
							<td>:</td>
							<td><?php echo $dta['waktu_pelaksanaan_adendum']; ?></td>
						</tr>
						
						<?php
								}
							}
						?>			
						<tr>
							<td><?php echo getCharacter($nomerst++); ?>.</td>
							<td colspan="5">Pembayaran dilakukan dengan cara
							<?php 
			
							if($data_pembayaran[0]->cara_pembayaran == 1){
								echo 'bulanan';
							}
							
							if($data_pembayaran[0]->cara_pembayaran == 2){
								echo 'termin';
							}
							
							if($data_pembayaran[0]->cara_pembayaran == 3){
								echo 'sekaligus';
							}
							
							?> 
							</td>
						</tr>
					  </table>
				  </ol>
		  </li>
		  <?php
		  
			if($data_pembayaran[0]->cara_pembayaran == 1){
				$kalimat_bulan = 'bulan '. $termin_view;
			}
			
			if($data_pembayaran[0]->cara_pembayaran == 2){
				$kalimat_bulan = 'termin '. $termin_view;
			}
			
			if($data_pembayaran[0]->cara_pembayaran == 3){
				$kalimat_bulan = 'sekaligus';
			}
			
		  if($data_pembayaran[0]->pasti > 1){ //pembayaran belum pasti == 2
		  ?>
		  
		  <li>
		  Berdasarkan <?php echo $type_kontrak; ?> (kontrak) maka <b>PIHAK KEDUA</b> berhak menerima dari <b>PIHAK KESATU</b> pembayaran sebesar Rp <?php echo number_format($total_pembayaran, 2); ?>, dengan perincian sebagai berikut :</li>
			<table width="100%" border="0">
				<tr>
					<td>a.</td>
					<td colspan="4">Pembayaran</td>
				</tr>
				<tr>				
					<td></td>
					<td>-</td>
					<td colspan="2">Total kontrak Rp. <?php echo number_format(@$data_pembayaran[0]->nominal_bayar, 2); ?> x .......%</td>
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
					<td width="30%">Rp <?php echo number_format($data_pembayaran[0]->uang_muka, 2); ?></td>
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
					<td>Rp <span class="pull-right" style="margin-right: 70px;"><?php echo number_format($data_pembayaran[0]->retensi, 2); ?></span></td>
					<td></td>
				</tr>
				<tr>
					<td></td>
					<td>-</td>
					<td>Lain-lain</td>
					<td>Rp <span class="pull-right" style="margin-right: 70px;"><?php echo number_format($data_pembayaran[0]->lain_lain, 2); ?></span></td>
					<td></td>
				</tr>
				<tr>
					<td></td>
					<td colspan="2">Jumlah potongan</td>
					<td></td>
					<td style="text-decoration: underline;">Rp................................</td>
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
			<li>PIHAK KESATU</b> setuju melakukan pembayaran <?php echo $kalimat_bulan;	?> sebesar Rp <?php 
			$x = '';
			$nomr = 1;
			$total_sudah_dibayar = 0;
			$histori_ba = getHistoriPembayaran($data_pembayaran[0]->id_pencairan, $data_pembayaran[0]->id_pembayaran);
			
			if($histori_ba){
				foreach($histori_ba as $rst){
					$total_sudah_dibayar += $rst['nilai_pekerjaan'];
				}		
			}		
			
			$sisa_pembayaran = $total_pembayaran - $total_sudah_dibayar;
			
			
			echo number_format($total_pembayaran, 2); ?> kepada <b>PIHAK KEDUA</b> melalui bank <?php echo @$data_pembayaran[0]->bank; ?> dengan nomor rekening <?php echo @$data_pembayaran[0]->no_rekening_penyedia; ?>.</li>
		  <?php
		  }else{
			?>
			<?php
				$x = '';
				$nomr = 1;
				$nomre = 1;
				$total_sudah_dibayar = 0;
				$histori_ba = getHistoriPembayaran($data_pembayaran[0]->id_pencairan, $data_pembayaran[0]->id_pembayaran);
				
				if($histori_ba){
					foreach($histori_ba as $rst){
						$total_sudah_dibayar += $rst['nilai_pekerjaan'];
						$x .= '
						<tr>
							<td width="2%">'.$nomr++.'.</td>
							<td width="27%">Bulan/termin '.getRomawi($rst['termin']).'</td>
							<td width="25%">Rp <span class="pull-right" style="margin-right: 70px;">
								'.number_format($rst['nilai_pekerjaan'], 2).'
							</span></td>
							<td width="25%"></td>
						</tr>
						';
					}		
				}
				
				//ini yg akan dibayar
				foreach($data_pembayaran as $rst){
					$x .= '
					<tr>
						<td width="2%">'.$nomr++.'.</td>
						<td width="27%">Bulan/termin '.getRomawi($rst->termin).'</td>
						<td width="25%">Rp <span class="pull-right" style="margin-right: 70px;">
							'.number_format(0, 2).'
						</span></td>
						<td width="25%"></td>
					</tr>
					';
				}	
			?>
			<li>
			Berdasarkan <?php echo $type_kontrak; ?> (kontrak) maka <b>PIHAK KEDUA</b> berhak menerima dari <b>PIHAK KESATU</b> pembayaran 
			<?php echo $kalimat_bulan;	?> 
			sebesar Rp <?php 
			
			// var_dump($total_sudah_dibayar, $total_pembayaran);exit;
			
			if($biaya_terakhir_adendum <= 0){
				$sisa_pembayaran = $data_pembayaran[0]->nominal_bayar - $total_sudah_dibayar;
			}else{
				$sisa_pembayaran = $biaya_terakhir_adendum - $total_sudah_dibayar;
				
			}
			echo number_format($total_pembayaran, 2); ?> terbilang (<i><?php echo ucwords(terbilang($total_pembayaran)); ?> Rupiah</i>).
			</li>
			<?php 
			
			if($total_sudah_dibayar > 0){ ?>
			Adapun rincian pembayaran sampai dengan berita acara pembayaran ini dibuat adalah sebagai berikut :
			<table width="100%">
				<?php echo $x; ?>
				<tr>
					<td width="2%"><?php echo $nomr++; ?>.</td>
					<td width="33%">Sisa yang belum dibayar</td>
					<td width="40%">Rp <span class="pull-right" style="margin-right: 70px;">
					<?php 
					echo number_format($sisa_pembayaran, 2); ?></span></td>
					<td width="25%"></td>
				</tr>
			</table>
			<?php }else{
				//jika sekaligus maka kalimat ini hilang
				if($data_pembayaran[0]->cara_pembayaran != 3){
			?>
			Adapun rincian pembayaran sampai dengan berita acara pembayaran ini dibuat adalah sebagai berikut :
			<table width="100%">
				<?php 
				foreach($data_pembayaran as $rstx){
					echo '
					<tr>
						<td width="2%">'.$nomre++.'.</td>
						<td width="33%">Bulan/ Termin '.getRomawi($rstx->termin).'</td>
						<td width="40%">Rp <span class="pull-right" style="margin-right: 70px;">0.00</span></td>
						<td width="25%"></td>
					</tr>
					';
				}
				?>
				<tr>
					<td width="2%"><?php echo $nomre++; ?>.</td>
					<td width="33%">Sisa yang belum dibayar</td>
					<td width="40%">Rp <span class="pull-right" style="margin-right: 70px;">
					<?php 
					echo number_format($data_pembayaran[0]->nominal_bayar, 2); ?></span></td>
					<td width="25%"></td>
				</tr>
			</table>
			<?php					
				}
			}?>
			
			<li>			
			<?php
			if($data_pembayaran[0]->kategori == 3){
			?>
			<b>PIHAK KESATU</b> setuju melakukan pembayaran <?php echo $data_pembayaran[0]->bayar_persen; ?>% dari nilai kontrak kepada <b>PIHAK KEDUA</b> melalui bank <?php echo @$data_pembayaran[0]->bank; ?> dengan nomor rekening <?php echo @$data_pembayaran[0]->no_rekening_penyedia; ?>.
			<?php
			}else{
			?>	
			<b>PIHAK KESATU</b> setuju melakukan pembayaran sebesar Rp <?php echo number_format($total_pembayaran, 2); ?> (<i><?php echo ucwords(terbilang($total_pembayaran)); ?> Rupiah</i>) kepada <b>PIHAK KEDUA</b> melalui bank <?php echo @$data_pembayaran[0]->bank; ?> dengan nomor rekening <?php echo @$data_pembayaran[0]->no_rekening_penyedia; ?>.
			<?php	
			}
			?>
			<?php
		  }
		  ?>			
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
							<td id="td_template_bapembayaran" height="100px"></td>
							<td id="td_template_bapembayaran">
								<?=@$ttd_pengguna_anggaran; ?>
							</td>
						</tr>
						<tr>
							<td id="td_template_bapembayaran"><b><u><?php echo @$data_pembayaran[0]->nama_penyedia; ?></u></b></td>
							<td id="td_template_bapembayaran"><b><u><?php echo @$data_pembayaran[0]->nama_pegawai_pengguna_anggaran; ?></u></b></td>
						</tr>
						<tr>
							<td id="td_template_bapembayaran"><?php 							
							if(!empty($data_pembayaran[0]->jabatan)){ echo @$data_pembayaran[0]->jabatan; }else{  echo 'Tenaga Ahli';  } ?></td>
							<td id="td_template_bapembayaran">NIP. <?php echo @$data_pembayaran[0]->nip_pegawai_pengguna_anggaran; ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</body>
</html>