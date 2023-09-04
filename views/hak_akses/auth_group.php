<div class="row">
    <div class="col-lg-12">    
        <div class="panel panel-default">           
            <div class="panel-body">
                <div class="table-responsive">
                    <table width="100%" class="table table-hover table-striped">
                    <thead>
                      <tr>
                        <th width="2%" style="text-align:left;"><input type="checkbox" name="check_all" id="check_all" ></th>
                        <th width="98%" style="text-align:left;">Menu</th>
                      </tr>
                        </thead>
                        <tbody>
                           <?=@$list_menu;?>                                                    
                        </tbody>
                    </table>
              </div>               
            </div>
        </div>
    </div>
    
</div>


<script>
$(function(){

	$("input:checkbox.id_menu").change(function() {
	
		var group_id = $("#id_akses option:selected").val();
		var id_menu = ($(this)).val();
	
		if ($(this).prop('checked')) {
			
		  var url = "<?=base_url('ba/'.$class_name.'/save_auth/true');?>/"+group_id+"/"+id_menu;		
		  $.ajax({url: url, success: function(result){
				//$("#div1").html(result);
			}});
		}
		else {
		
		  var url = "<?=base_url('ba/'.$class_name.'/save_auth/false');?>/"+group_id+"/"+id_menu;		
		  $.ajax({url: url, success: function(result){
				//$("#div1").html(result);
			}});
		   
		}
	});
	
	
	$("input:checkbox#check_all").change(function() {
	
		$('.id_menu').prop('checked', this.checked);
		
		var group_id = $("#user_group option:selected").val();
	
        if ($(this).prop('checked')) {
			  var url = "<?=site_url('eo/user/save_auth_all/true');?>/"+group_id;		
			  $.ajax({url: url, success: function(result){
					//$("#div1").html(result);
				}});
		}else{
			  var url = "<?=site_url('eo/user/save_auth_all/false');?>/"+group_id;		
			  $.ajax({url: url, success: function(result){
					//$("#div1").html(result);
				}});
		}
		
    });	
	
	
	
	
});
</script>