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
		/* padding : 50px; */
		margin-right: 1cm;
	}
</style>
</head>
<body id="template_bapembayaran">		
	<div id="batas-margin">			
		LAMPIRAN BERITA ACARA PEMBAYARAN
		<table width="100%" style="border-collapse: collapse;">
			<tr>
				<td>Nama Sub Kegiatan</td>
				<td>:</td>
				<td><?php echo @$data_pembayaran[0]->nama_kegiatan; ?></td>
			</tr>
			<tr>
				<td>Nama Paket</td>
				<td>:</td>
				<td><?php echo @$data_pembayaran[0]->nama_pekerjaan; ?></td>
			</tr>
			<tr>
				<td>Lokasi</td>
				<td>:</td>
				<td>KOTA TANGERANG</td>
			</tr>
			<tr>
				<td>Nama Penyedia</td>
				<td>:</td>
				<td><?php if($data_pembayaran[0]->kategori == 1){ echo @$data_pembayaran[0]->nama_perusahaan; }else{  echo @$data_pembayaran[0]->nama_penyedia;  } ?></td>
			</tr>
			<tr>
				<td>Nomor Kontrak</td>
				<td>:</td>
				<td><?php echo @$data_pembayaran[0]->no_spk; ?></td>
			</tr>
			<tr>
				<td>Tanggal Kontrak</td>
				<td>:</td>
				<td><?php echo GetFullDateFull(@$data_pembayaran[0]->tgl_pekerjaan); ?></td>
			</tr>
			<?php
				$nomers = 1;
				if($data_adendum){
				foreach($data_adendum as $dta){
			?>
			<tr>
				<td>Nomor Adendum <?php echo $nomers++; ?></td>
				<td>:</td>
				<td><?php echo $dta['no_adendum']; ?></td>
			</tr>
			<tr>
				<td>Tanggal</td>
				<td>:</td>
				<td><?php echo GetFullDateFull($dta['tgl_adendum']); ?></td>
			</tr>
			
			<?php
					}
				}
			?>		
		</table>
		<table width="100%" border="1" style="border-collapse: collapse;">
			<tr style="text-align: center;">
				<td width="5%">No</td>
				<td width="30%">Jenis Pekerjaan</td>
				<td width="10%">Volume</td>
				<td width="5%">Satuan</td>
				<td width="20%">Harga Satuan</td>
				<td width="20%">Jumlah</td>
				<td width="10%">Keterangan</td>
			</tr>
			<?php 
			if($data_pembayaran_rincian){
				$nomer = 1;
				$textview = '';
				foreach($data_pembayaran_rincian as $row){
					
					$textview = ($row['satuan'] == 'OB') ? ' Bulan' : '';
					echo '
						<tr>
							<td id="td_template_bapembayaran" style="text-align: center;">'.$nomer++.'</td>
							<td id="td_template_bapembayaran">'.$row['uraian_dpa'].'</td>
							<td id="td_template_bapembayaran" style="text-align: center;">'.$row['volume'].$textview.'</td>
							<td id="td_template_bapembayaran" style="text-align: center;">'.$row['satuan'].'</td>
							<td id="td_template_bapembayaran" style="text-align: center;">'.number_format($row['harga_satuan'], 2).'</td>
							<td id="td_template_bapembayaran" style="text-align: center;">'.number_format($row['jumlah_harga_satuan'], 2).'</td>
							<td id="td_template_bapembayaran" style="text-align: center;"></td>
						</tr>
					';
				}
			}else{
				
			?>
			<tr>
				<td id="td_template_bapembayaran" style="text-align: center;">1.</td>
				<td id="td_template_bapembayaran"><?php echo $data_pembayaran[0]->uraian_dpa; ?></td>
				<td id="td_template_bapembayaran" style="text-align: center;"><?php echo $data_pembayaran[0]->volume; ?></td>
				<td id="td_template_bapembayaran" style="text-align: center;"><?php echo $data_pembayaran[0]->satuan; ?></td>
				<td id="td_template_bapembayaran" style="text-align: center;"><?php echo number_format($data_pembayaran[0]->harga_satuan, 2); ?></td>
				<td id="td_template_bapembayaran" style="text-align: center;"><?php echo number_format($data_pembayaran[0]->jumlah_harga_satuan, 2); ?></td>
				<td id="td_template_bapembayaran" style="text-align: center;"></td>
			</tr>
			<?php
			}
			
			?>
		</table>
	</div>
</body>
</html>