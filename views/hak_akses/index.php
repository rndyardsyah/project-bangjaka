<link href="<?php echo base_url('assets/js/bootstrap-select/css/bootstrap-select.min.css'); ?>" rel="stylesheet" type="text/css" >
<script src="<?php echo base_url('assets/js/bootstrap-select/js/bootstrap-select.min.js'); ?>"></script>


<div class="row">
	<div class="col-lg-12">
		<div id="message"  style="clear:both; margin-top: 10px;"></div>
		<h3 class="page-header">Pengaturan Hak Akses Pengguna
			<div class="btn-group pull-right tombl">
			</div>
		</h3>
	</div>
	<!-- /.col-lg-12 -->
</div>

<div id="page-wrapper-datatable">
	<div class="row">
		<div class="col-lg-12">
			<!-- <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%"> -->
				
				<div class="panel panel-default" id="panel">					
					<div class="panel-body" id="panel-body">
						<div class="bs-callin bs-callin-info">
						   <div class="col-sm-2"><label>Group Pengguna</label></div>
						   <div class="col-sm-5"><?=@$selectListUserGroup;?></div>
						</div>
					</div>
				</div>
		</div>
		<!-- /.col-lg-12 -->
	</div>
</div>

<div class="row">
	<div class="col-sm-12" id="user_auth">
    
    </div>
</div>

<script type="text/javascript">

$(function(){

	$('#id_akses').change(function(){
		
		var group_id = $(this).val();
		
		// loading("#user_auth");
		$("#user_auth").load('<?php echo base_url('ba/'.$class_name.'/auth_group')?>/'+group_id);				
	
	});


});
</script>


<div id="page-wrapper-form">
</div>
