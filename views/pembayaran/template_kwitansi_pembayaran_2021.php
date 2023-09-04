<html> 
<head>

<style>
	#template_kwitansi_pembayaran {
		font-family: sans-serif;
		font-size: 11pt;
		border: 2px solid;
		/* margin-left: 2cm; */
	}
	#template_kwitansi_pembayaran2 {
		font-family: sans-serif;
		font-size: 11pt;
		border: 2px solid;
		margin-left: -1cm;
		margin-right: -0.5cm;
	}
	#table_kwitansi_pembayaran {					
		/* border-collapse: collapse; */
		border-spacing: 0;
		margin-top: 10px;
		margin-bottom: 10px;
	}
	#td_kwitansi_pembayaran {					
		vertical-align : top;
	}
	#RENDY, .pertama td {					
		height: 35px;
	}
	p{
		text-align:justify;
		line-height:20px;
	}
	.full{
		border-top: 1px solid;
		border-bottom: 1px solid;
		border-left: 1px solid;
		border-right: 1px solid;
		width: 70%;
	}
	.header{
		font-size:22pt; 
		padding-top: -35px; 
		font-weight: bold;
		/* position: fixed;	 */
	}
</style>
</head>

<?php 

$margins1 ='';
$margins2 ='margin-top: 20px';
$margins3 ='height="90px"';
if($data->kategori == 1){
	$margins1 = 'style="margin-top: 20px;"';
	$margins2 = 'margin-top: 20px';
	$margins3 = 'height="70px"';
}
?>
<body id="template_kwitansi_pembayaran2" <?php echo $margins1; ?>>		

			
			<?php
				$total = count($data_pembayaran);
				
				$no = 1;
				$termin_view = 'Termin ';
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
					
				}
				
				$termin = ($data_pembayaran[0]->pekerjaan_termin == 1) ? '' : $termin_view;
			?>

		<div class="header">
			<center>KWITANSI</center>
		</div>		
		
		<?php if(isset($viewer)){?>
		<div style="border-style: solid; margin: 10px;">
		<?php }?>
		
			<div style="padding: 10px;">

				<b>NPWP : <?php 
				
				echo @$data_pembayaran[0]->npwp; ?></b>

				<table id="table_kwitansi_pembayaran" class="pertama" id="RENDY" width="100%" style="border-collapse: collapse; padding-top: 20px; padding-bottom: 5px; font-size: 9pt">
					<tr>
						<td id="td_kwitansi_pembayaran" width="20%">Sudah Terima Dari</td>
						<td id="td_kwitansi_pembayaran" width="3%">:</td>
						<td id="td_kwitansi_pembayaran" width="74%">Bendahara Pengeluaran <?php echo getNamaUnor(substr($data_pembayaran[0]->kode_unor, 0,5)); ?> Kota Tangerang</td>
					</tr>
					<tr>
						<td id="td_kwitansi_pembayaran">Banyaknya Uang</td>
						<td id="td_kwitansi_pembayaran">:</td>
						<td id="td_kwitansi_pembayaran"><b><i><?php echo ucfirst(terbilang($total_pembayaran)) . ' rupiah'; ?></i></b></td>
					</tr>
					<tr>
						<td id="td_kwitansi_pembayaran">Untuk Pembayaran</td>
						<td id="td_kwitansi_pembayaran">:</td>
						<td id="td_kwitansi_pembayaran">Pekerjaan <?php echo @$data_pembayaran[0]->nama_pekerjaan; ?> <?php echo @$termin; ?></td>
					</tr>
					<tr>
						<td id="td_kwitansi_pembayaran"></td>
						<td id="td_kwitansi_pembayaran"></td>
						<td id="td_kwitansi_pembayaran">Sub Kegiatan <?php echo @$data_pembayaran[0]->nama_kegiatan; ?></td>
					</tr>
					<tr>
						<td id="td_kwitansi_pembayaran"></td>
						<td id="td_kwitansi_pembayaran"></td>
						<td id="td_kwitansi_pembayaran">Kegiatan <?php echo $data_prokeg[1]['uraian_prokeg']; ?></td>
					</tr>
					<tr>
						<td id="td_kwitansi_pembayaran"></td>
						<td id="td_kwitansi_pembayaran"></td>
						<td id="td_kwitansi_pembayaran">Dengan Surat Perintah Kerja (SPK) Nomor:<br>
						<?php echo @$data_pembayaran[0]->no_spk; ?>, Tanggal <?php echo GetFullDateFull(@$data_pembayaran[0]->tgl_pekerjaan); ?><br>
						<span style="font-size: 10pt;">
						<?php
						
						if(@$data_adendum){
							$noms = 1;
							foreach($data_adendum as $rsa){
								
								echo $rsa['no_adendum']. ', Tanggal '.GetFullDateFull($rsa['tgl_adendum']);
								if(count($data_adendum) != $noms++){
									echo '<br>';
								}
							}
						}
						
						?></span>
						<br><br>
						</td>
					</tr>
					<tr>
						<td id="td_kwitansi_pembayaran"></td>
						<td id="td_kwitansi_pembayaran"></td>
						<td id="td_kwitansi_pembayaran">Sebesar:</td>
					</tr>
					<tr>
						<td id="td_kwitansi_pembayaran"></td>
						<td id="td_kwitansi_pembayaran"></td>		
						<td id="td_kwitansi_pembayaran"style="vertical-align : middle;"><div class="full" style="font-size: 14pt; font-weight: bold;">&nbsp;Rp.<?php echo @number_format($total_pembayaran , 2 ,',' , '.').',-'; ?></div><br></td>
					</tr>
					<tr>
						<td id="td_kwitansi_pembayaran"></td>
						<td id="td_kwitansi_pembayaran"></td>
						<td id="td_kwitansi_pembayaran">Rekening:</td>
					</tr>
					<tr>
						<td id="td_kwitansi_pembayaran"></td>
						<td id="td_kwitansi_pembayaran"></td>
						<td id="td_kwitansi_pembayaran">
						<b><?php echo @$data_pembayaran[0]->bank; ?><br>
						No. Rek: <?php echo @$data_pembayaran[0]->no_rekening_penyedia; ?><br>
						an. <?php echo @$data_pembayaran[0]->atas_nama_rekening; ?></b>
						<br>
						<br>
						</td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td><b>Tangerang, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo date('Y', strtotime(@$data_pembayaran[0]->tgl_pekerjaan)); ?></b></td>
					</tr>
				</table> 
				
				<table id="table_kwitansi_pembayaran" border="0" style="text-align: center; font-size: 8pt; <?php echo $margins2; ?>" width="100%">
					<tr style="font-weight: bold;">
						<td id="td_kwitansi_pembayaran" width="32%">PPTK,</td>
						<td id="td_kwitansi_pembayaran" width="33%"></td>
						<td id="td_kwitansi_pembayaran" width="35%">Penyedia Barang/ Jasa,
						<br><?php if($data_pembayaran[0]->kategori == 1){ echo @$data_pembayaran[0]->nama_perusahaan; }else{  echo 'Penyedia Jasa Perorangan';  } ?>
						</td>
					</tr>
					<tr>
						<td id="td_kwitansi_pembayaran" height="105px">
							<?=@$ttd_pptk; ?>
							</td>
						<td id="td_kwitansi_pembayaran"></td>
						<td id="td_kwitansi_pembayaran"></td>
					</tr>
					<tr>
						<td id="td_kwitansi_pembayaran"><b><u><?php echo @$data_pembayaran[0]->nama_pegawai_pptk; ?></u></b></td>
						<td id="td_kwitansi_pembayaran"></td>
						<td id="td_kwitansi_pembayaran"><b><u><?php echo @$data_pembayaran[0]->nama_penyedia; ?></u></b></td>
					</tr>
					<tr>
						<td id="td_kwitansi_pembayaran">NIP. <?php echo @$data_pembayaran[0]->nip_pegawai_pptk; ?></td>
						<td id="td_kwitansi_pembayaran"></td>
						<td id="td_kwitansi_pembayaran"><?php if(!empty($data_pembayaran[0]->jabatan)){ echo @$data_pembayaran[0]->jabatan; }else{  echo 'Tenaga Ahli';  } ?></td>
					</tr>
					<tr>
						<td id="td_kwitansi_pembayaran" height="20px"></td>
						<td id="td_kwitansi_pembayaran"></td>
						<td id="td_kwitansi_pembayaran"></td>
					</tr>
					<tr>
						<td id="td_kwitansi_pembayaran"></td>
						<td id="td_kwitansi_pembayaran">Mengetahui,</td>
						<td id="td_kwitansi_pembayaran"></td>
					</tr>
					<tr>
						<td id="td_kwitansi_pembayaran">PENGGUNA ANGGARAN,</td>
						<td id="td_kwitansi_pembayaran">PPK</td>
						<td id="td_kwitansi_pembayaran">Bendahara Pengeluaran <br> <?php echo strtoupper(getNamaUnor(substr(@$data_pembayaran[0]->kode_unor,0,5))); ?></td>
					</tr>
					<tr>
						<td id="td_kwitansi_pembayaran" <?php echo $margins3; ?>>
							<?=@$ttd_pengguna_anggaran; ?>
						</td>
						<td id="td_kwitansi_pembayaran">
							<?=@$ttd_ppk; ?>
						</td>
						<td id="td_kwitansi_pembayaran">
							<?=@$ttd_bendahara; ?>
						</td>
					</tr>
					<tr>
						<td id="td_kwitansi_pembayaran"><b><u><?php echo @$data_pembayaran[0]->nama_pegawai_pengguna_anggaran; ?></u></b></td>
						<td id="td_kwitansi_pembayaran"><b><u><?php echo @$data_pembayaran[0]->nama_pegawai_ppk; ?></u></b></td>
						<td id="td_kwitansi_pembayaran"><b><u><?php echo @$data_pembayaran[0]->nama_pegawai_bendahara; ?></u></b></td>
					</tr>
					<tr>
						<td id="td_kwitansi_pembayaran">NIP. <?php echo @$data_pembayaran[0]->nip_pegawai_pengguna_anggaran; ?></td>
						<td id="td_kwitansi_pembayaran">NIP. <?php echo @$data_pembayaran[0]->nip_pegawai_ppk; ?></td>
						<td id="td_kwitansi_pembayaran">NIP. <?php echo @$data_pembayaran[0]->nip_pegawai_bendahara; ?></td>
					</tr>
					
					
				</table>
				
			</div>
	<?php if(isset($viewer)){?>
	</div>
	&nbsp;&nbsp;
	<?php }?>
</body>
</html>