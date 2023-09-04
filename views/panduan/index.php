<link href="<?php echo base_url('assets/js/bootstrap-select/css/bootstrap-select.min.css'); ?>" rel="stylesheet" type="text/css" >

<link href="<?php echo base_url('assets/datatables/css/dataTables.bootstrap.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/datatables/css/dataTables.responsive_v2.css'); ?>" rel="stylesheet">
<!-- <link href="<?php echo base_url('assets/datatables/css/dataTables.bootstrap.css'); ?>" type="text/css" rel="stylesheet" /> -->
<link href="<?php echo base_url('assets/datatables/css/fixedHeader.bootstrap.min.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/datatables/css/responsive.bootstrap.min.css'); ?>" rel="stylesheet">

<script src="<?php echo base_url('assets/datatables/js/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/datatables/js/dataTables.bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/datatables/js/dataTables.fixedHeader.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/datatables/js/dataTables.responsive.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/datatables/js/responsive.bootstrap.min.js'); ?>"></script>	
<script src="<?php echo base_url('assets/js/bootstrap-select/js/bootstrap-select.min.js'); ?>"></script>

<style>
.close {display: none;} /* hide close di modal */
div.dataTables_filter label {
    float: right;
    font-weight: normal;
}
div.dataTables_length label {
    float: left;
    text-align: left;
    font-weight: normal;
}
</style>

<div class="row">
	<div class="col-lg-12">
		<div id="message"  style="clear:both; margin-top: 10px;"></div>
		<h3 class="page-header">Data Panduan
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
				
				<div class="panel panel-default">					
					<div class="panel-body">
						<table id="table" class="table table-hover table-striped " cellspacing="0" width="100%" >
							<thead>
								<tr>
									<th width="5%">NO</th>
									<th>Data Dokumen</th>
									<th>Link</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>1.</td>
									<td>Usermanual</td>
									<td><a href="https://drive.google.com/file/d/101KWgROALir6G7ljT1kxGRs7WnRKnfHb/view" target="_blank">Klik</a></td>
								</tr>
								<tr>
									<td>2.</td>
									<td>Video Tutorial</td>
									<td><a href="<?php base_url('assets/file/user_manual_ekontrak.docx'); ?>" target="_blank">Klik</a></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
		</div>
		<!-- /.col-lg-12 -->
	</div>
</div>


<script type="text/javascript">

</script>


<div id="page-wrapper-form">
</div>


<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">           
		            
		    <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body form">               
				<form action="#" id="formModal" class="form-horizontal" enctype="multipart/form-data">
					<div class="form-body">						
						<div id="message-modal"  style="clear:both; margin-top: 10px;"></div>
						<input type="hidden" name="id">
						<label id="kalimat_tampil"></label>
					</div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnModal_proses" onclick="delete_proses()" class="btn btn-primary">Save</button>
                <button type="button" id="btnModal_cancel" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->