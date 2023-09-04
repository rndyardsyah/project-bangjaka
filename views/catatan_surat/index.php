<style>
.img-circle {
    width: 50px;
    height: 50px;
}

</style>
<div class="panel-body">
	<ul class="chat">
		<?php 
		
		if($bukanBA === false)
		{			
			if(!empty($data)){ ?>
				<?php 
					$no = 1;
					$x = '';
					foreach($data as $rss){ 
					if(!empty($rss['catatan'])){
						
					if($no++%2 == 0){
						$x = 'right';
					}else{
						$x = 'left';
					}
				?>
				<li class="<?php echo $x; ?> clearfix">
					<span class="chat-img pull-<?php echo $x; ?>">
						<img src="<?php 
						$foto = ini_pegawai_foto($rss['mailfrom']);
						echo 'https://simasn.tangerangkota.go.id/apps/assets/media/file/'.$rss['nip_mailfrom'].'/pasfoto/'.@$foto; ?>" alt="User Avatar" width="50px" class="img-circle">
					</span>
					<div class="chat-body clearfix">
						<div class="">
							<strong class="primary-font"><?php echo $rss['nama_mailfrom']; ?></strong> 
							<small class="pull-right text-muted">
								<i class="fa fa-clock-o fa-fw"></i> <?php echo GetFullDate($rss['cdd']); ?>
							</small>
						</div>
						<p>
							<?php echo nl2br($rss['catatan']); ?>
						</p>
					</div>
				</li>
				<?php 				
						
					}
				} ?>
			<?php }else{
				echo "Tidak ada catatan";
			} 
		}else{
			if(!empty($data)){ ?>
				<?php 
					$no = 1;
					$x = '';
					foreach($data as $rss){ 

					if(!empty($rss['komentar'])){
						
					if($no++%2 == 0){
						$x = 'right';
					}else{
						$x = 'left';
					}

					$data_pegawai_json = json_decode($rss['json_pegawai'], true);
					$data_pegawai = $data_pegawai_json[0];
				?>
				<li class="<?php echo $x; ?> clearfix">
					<span class="chat-img pull-<?php echo $x; ?>">
						<img src="<?php 
						$foto = ini_pegawai_foto($rss['id_pegawai']);
						echo 'https://simasn.tangerangkota.go.id/apps/assets/media/file/'.$data_pegawai['nip_baru'].'/pasfoto/'.@$foto; ?>" alt="User Avatar" width="50px" class="img-circle">
					</span>
					<div class="chat-body clearfix">
						<div class="">
							<strong class="primary-font"><?php echo ini_pegawai_nama_arr($data_pegawai); ?></strong> 
							<small class="pull-right text-muted">
								<i class="fa fa-clock-o fa-fw"></i> <?php echo GetFullDate($rss['cdd']); ?>
							</small>
						</div>
						<p>
							<?php echo nl2br($rss['komentar']); ?>
						</p>
					</div>
				</li>
				<?php 				
						
					}
				} ?>
			<?php }else{
				echo "Tidak ada catatan";
			} 
		}?>
	</ul>
</div>