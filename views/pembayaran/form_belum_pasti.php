<div class="panel panel-primary">
	<div class="panel-heading" data-toggle="collapse" data-target="#collapse5">
	  <h4 class="panel-title accordion-toggle">
		  Form Adendum
	  </h4>
	</div>
	<div id="collapse5" class="panel-collapse in">
	  <div class="panel-body">					
		<div class="form-group">
			<label class="control-label col-md-3">Pembayaran uang muka</label>
			<div class="col-md-9">
				<input name="uang_muka" placeholder="Pembayaran uang muka" class="form-control money" type="text" value="<?php echo number_format(@$data->uang_muka, 2); ?>">
				<span class="help-block"></span>
			</div>
		</div>		
		<div class="form-group">
			<label class="control-label col-md-3">Retensi 5%</label>
			<div class="col-md-9">
				<input name="retensi" placeholder="Retensi 5%" class="form-control money" type="text" value="<?php echo number_format(@$data->retensi, 2); ?>">
				<span class="help-block"></span>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-3">Lain-lain</label>
			<div class="col-md-9">
				<input name="lain_lain" placeholder="Lain-lain" class="form-control money" type="text" value="<?php echo number_format(@$data->lain_lain, 2); ?>">
				<span class="help-block"></span>
			</div>
		</div>											
	  </div>
	</div>
</div>