<?php
/* Smarty version 3.1.29, created on 2018-08-02 15:13:25
  from "C:\xampp\htdocs\2017\ctsvn2\managehiring\hiring\templates\cv_rejection_analysis.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5b62d23dce2c06_69270747',
  'file_dependency' => 
  array (
    '8920a856c820cedbcdd7800c10883e7d8c60d3d0' => 
    array (
      0 => 'C:\\xampp\\htdocs\\2017\\ctsvn2\\managehiring\\hiring\\templates\\cv_rejection_analysis.tpl',
      1 => 1533203004,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:include/header.tpl' => 1,
    'file:include/sidebar.tpl' => 1,
    'file:include/footer.tpl' => 1,
  ),
),false)) {
function content_5b62d23dce2c06_69270747 ($_smarty_tpl) {
if (!is_callable('smarty_function_html_options')) require_once 'C:\\xampp\\htdocs\\2017\\ctsvn2\\managehiring\\hiring\\vendor\\smarty-3.1.29\\libs\\plugins\\function.html_options.php';
?>

   

<style type="text/css">
.printAreaTable a{color:#000000 !important}
.printAreaTable tr td{font-size:13px;padding:6px;}
.printAreaTable tr th{font-size:13px;padding:6px;}
.dl-horizontal dt{text-align:left}
.btnExport li a:hover{background:#efefef !important; color:#000 !important}
ul.statusLegend li{width:200px;}
</style>

	
<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:include/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
 
<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:include/sidebar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
       
    <div id="contentwrapper">
                <div class="main_content">
					<div class="row-fluid">
					  <div class="span12" style="margin:0;">
					  <form action="cv_rejection_analysis.php" id="formID" class="formID" method="post" accept-charset="utf-8">								
						<div class="dn dataTables_filter srchBox homeSrchBox" id="dt_gal_filter" style="display: block;">	
							
							<span id="sandbox-container">
						<span class="input-daterange" id="datepicker">	
						<label>From Date: <input type="text" id="" placeholder="dd/mm/yyyy" class="input-small datepick" name="f_date" value="<?php echo $_smarty_tpl->tpl_vars['f_date']->value;?>
" aria-controls="dt_gal"></label>
						<label>To Date: <input type="text"  id=""  placeholder="dd/mm/yyyy" name="t_date" value="<?php echo $_smarty_tpl->tpl_vars['t_date']->value;?>
" class="input-small datepick" aria-controls="dt_gal"></label>				
						</span>	
						</span>	
						<label>	Client: 
						<select name="client_id" class="input-medium" placeholder="" style="clear:left" id="IncentiveEmpId">
						<option value="">Select</option>
							<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['client_name']->value,'selected'=>$_smarty_tpl->tpl_vars['client_id']->value),$_smarty_tpl);?>

						</option>
						</select>
						</label>
						<label>	Role: 
						<select name="role_id" class="input-medium" placeholder="" style="clear:left" id="IncentiveEmpId">
						<option value="">Select</option>
							<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['role_name']->value,'selected'=>$_smarty_tpl->tpl_vars['role_id']->value),$_smarty_tpl);?>

						</option>	
						</select>
						</label>					
						<label>	Branch: 
						<select name="branch_id" class="input-medium" placeholder="" style="clear:left" id="IncentiveEmpId">
						<option value="">Select</option>
							<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['branch_name']->value,'selected'=>$_smarty_tpl->tpl_vars['branch_id']->value),$_smarty_tpl);?>

						</option>
						</select>						
						</label>
						<label>Employee: 
						<select name="emp_id" class="input-medium" placeholder="" style="clear:left" id="IncentiveEmpId">
						<option value="">Select</option>
							<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['emp_name']->value,'selected'=>$_smarty_tpl->tpl_vars['emp_id']->value),$_smarty_tpl);?>

						</option>
						</select>						
						</label>
											
					<label style="margin-top:18px;"><input type="submit" value="Submit" class="btn btn-gebo validateReport"></label>
					<label style="margin-top:18px;"><a class="jsRedirect" href=""><input value="Reset" type="button" class="btn"></a></label>

					</div>
					
						<input type="hidden" name="data[srchSubmit]" id="srchSubmit"/>
						<input type="hidden" value="1" id="SearchKeywords">
						<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['this']->value->webroot;?>
report/get_employee/" id="webroot">	
						<input type="hidden" name="data[type]" id="type"/>	
					
					</form>
						
					<div class="row-fluid <?php echo $_smarty_tpl->tpl_vars['format_type_chart']->value;?>
 printAreaTable">		
					<div class="span12">	
						<h3 class="heading" style="margin-bottom:0;border:;clear:left;">CV Rejection Analysis (Table View)</h3>
						
						<div class="">
							<a href="javascript:void(0)" rel="printAreaTable" style="margin:10px 0px 10px 0px;" class="printBtn no-print pull-left"><input value="Print" type="button" class="btn btn-success"/></a>
						<div class="btn-group no-print pull-left"  style="margin:10px 0px 10px 10px;" >
						<button class="btn btn-warning">Export</button>
						<button data-toggle="dropdown"  class="btn btn-warning dropdown-toggle"><span class="caret"></span></button>
						<ul class="dropdown-menu btnExport">
							<li><a href="">Excel</a></li>
							<li><a href="">PDF</a></li>
						</ul>
						</div>
						</div>		
						
						<?php if ($_smarty_tpl->tpl_vars['fromDate']->value != '' || $_smarty_tpl->tpl_vars['toDate']->value != '' || $_smarty_tpl->tpl_vars['clientName']->value != '' || $_smarty_tpl->tpl_vars['roleName']->value != '' || $_smarty_tpl->tpl_vars['locName']->value != '' || $_smarty_tpl->tpl_vars['empName']->value != '') {?>
							<h6 style="clear:left;margin-top:10px">Search Filters:</h6> 
		
		<dl class="dl-horizontal">
										
										<?php if (!empty($_smarty_tpl->tpl_vars['fromDate']->value)) {?>
										<dt>From Date: </dt>
										<dd><?php echo $_smarty_tpl->tpl_vars['fromDate']->value;?>
</dd>
										<?php }?>	
										
										<?php if (!empty($_smarty_tpl->tpl_vars['toDate']->value)) {?>
										<dt>To Date: </dt>
										<dd><?php echo $_smarty_tpl->tpl_vars['toDate']->value;?>
</dd>
										<?php }?>	
										
										
										<?php if (!empty($_smarty_tpl->tpl_vars['clientName']->value)) {?>
										<dt>Client: </dt>
										<dd><?php echo $_smarty_tpl->tpl_vars['clientName']->value;?>
</dd>
										<?php }?>	
										
										<?php if (!empty($_smarty_tpl->tpl_vars['roleName']->value)) {?>
										<dt>Role: </dt>
										<dd><?php echo $_smarty_tpl->tpl_vars['roleName']->value;?>
</dd>
										<?php }?>	
										
										<?php if (!empty($_smarty_tpl->tpl_vars['locName']->value)) {?>
										<dt>Branch: </dt>
										<dd><?php echo $_smarty_tpl->tpl_vars['locName']->value;?>
</dd>
										<?php }?>	
										
										
										<?php if (!empty($_smarty_tpl->tpl_vars['empName']->value)) {?>
										<dt>Employee: </dt>
										<dd><?php echo $_smarty_tpl->tpl_vars['empName']->value;?>
</dd>
										<?php }?>	
										
									</dl>
									<?php }?>					
									
							<table class="table table-hover table-bordered table-striped printAreaTable" style="margin: 15px 0px;">
								<thead>								
									<tr>
										<th width="250" style="min-width: 0px; max-width: none;"><a href="#">Reasons / Client</a></th>	
										<?php
$_from = $_smarty_tpl->tpl_vars['data']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_item_0_saved_item = isset($_smarty_tpl->tpl_vars['item']) ? $_smarty_tpl->tpl_vars['item'] : false;
$__foreach_item_0_saved_key = isset($_smarty_tpl->tpl_vars['key']) ? $_smarty_tpl->tpl_vars['key'] : false;
$_smarty_tpl->tpl_vars['item'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['key'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['item']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
$__foreach_item_0_saved_local_item = $_smarty_tpl->tpl_vars['item'];
?>
										<th width="180" style="min-width: 0px; max-width: none;text-align:center"><a href="#"><?php echo $_smarty_tpl->tpl_vars['item']->value['client_name'];?>
</a></th>
										<?php
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_0_saved_local_item;
}
if ($__foreach_item_0_saved_item) {
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_0_saved_item;
}
if ($__foreach_item_0_saved_key) {
$_smarty_tpl->tpl_vars['key'] = $__foreach_item_0_saved_key;
}
?>
									
										<th width="80" style="min-width: 0px; max-width: none;text-align:center""><a href="#">Contribution</a></th>	
									</tr>
								</thead>
								<tbody>							
																						
								<tr>
										<th width="">Total CVs Sent </th>
										<?php
$_from = $_smarty_tpl->tpl_vars['data_cv_sent']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_item_1_saved_item = isset($_smarty_tpl->tpl_vars['item']) ? $_smarty_tpl->tpl_vars['item'] : false;
$__foreach_item_1_saved_key = isset($_smarty_tpl->tpl_vars['key']) ? $_smarty_tpl->tpl_vars['key'] : false;
$_smarty_tpl->tpl_vars['item'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['key'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['item']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
$__foreach_item_1_saved_local_item = $_smarty_tpl->tpl_vars['item'];
?>
										<td style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['item']->value;?>
</td>
										<?php
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_1_saved_local_item;
}
if ($__foreach_item_1_saved_item) {
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_1_saved_item;
}
if ($__foreach_item_1_saved_key) {
$_smarty_tpl->tpl_vars['key'] = $__foreach_item_1_saved_key;
}
?>	
										<td style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['total_cv_sent_count']->value;?>
</td>
								</tr>	
								<tr>
										<th width="">CVs Billed </th>
										<?php
$_from = $_smarty_tpl->tpl_vars['total_cv_billed']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_item_2_saved_item = isset($_smarty_tpl->tpl_vars['item']) ? $_smarty_tpl->tpl_vars['item'] : false;
$__foreach_item_2_saved_key = isset($_smarty_tpl->tpl_vars['key']) ? $_smarty_tpl->tpl_vars['key'] : false;
$_smarty_tpl->tpl_vars['item'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['key'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['item']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
$__foreach_item_2_saved_local_item = $_smarty_tpl->tpl_vars['item'];
?>
										<td style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['item']->value['total_billed'];?>
</td>
									<?php
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_2_saved_local_item;
}
if ($__foreach_item_2_saved_item) {
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_2_saved_item;
}
if ($__foreach_item_2_saved_key) {
$_smarty_tpl->tpl_vars['key'] = $__foreach_item_2_saved_key;
}
?>	
								<td style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['total_cv_billed_count']->value;?>
</td>
								</tr>
								<tr>
										<th width="">CVs Still Active </th>
										<?php
$_from = $_smarty_tpl->tpl_vars['active_cv']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_item_3_saved_item = isset($_smarty_tpl->tpl_vars['item']) ? $_smarty_tpl->tpl_vars['item'] : false;
$__foreach_item_3_saved_key = isset($_smarty_tpl->tpl_vars['key']) ? $_smarty_tpl->tpl_vars['key'] : false;
$_smarty_tpl->tpl_vars['item'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['key'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['item']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
$__foreach_item_3_saved_local_item = $_smarty_tpl->tpl_vars['item'];
?>
										<td style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['item']->value['cv_active'];?>
</td>
									<?php
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_3_saved_local_item;
}
if ($__foreach_item_3_saved_item) {
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_3_saved_item;
}
if ($__foreach_item_3_saved_key) {
$_smarty_tpl->tpl_vars['key'] = $__foreach_item_3_saved_key;
}
?>	
									<td style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['active_cv_count']->value;?>
</td>
								</tr>
								<tr>
										<th width="">CVs Rejected / Gone Inactive </th>
										<?php
$_from = $_smarty_tpl->tpl_vars['rejected_cv']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_item_4_saved_item = isset($_smarty_tpl->tpl_vars['item']) ? $_smarty_tpl->tpl_vars['item'] : false;
$__foreach_item_4_saved_key = isset($_smarty_tpl->tpl_vars['key']) ? $_smarty_tpl->tpl_vars['key'] : false;
$_smarty_tpl->tpl_vars['item'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['key'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['item']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
$__foreach_item_4_saved_local_item = $_smarty_tpl->tpl_vars['item'];
?>
										<td style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['item']->value['cv_rejected'];?>
</td>
									<?php
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_4_saved_local_item;
}
if ($__foreach_item_4_saved_item) {
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_4_saved_item;
}
if ($__foreach_item_4_saved_key) {
$_smarty_tpl->tpl_vars['key'] = $__foreach_item_4_saved_key;
}
?>	
									<td style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['rejected_cv_count']->value;?>
</td>
								</tr>
								
								<tr>
									<?php
$_from = $_smarty_tpl->tpl_vars['rejected_code']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_item_5_saved_item = isset($_smarty_tpl->tpl_vars['item']) ? $_smarty_tpl->tpl_vars['item'] : false;
$__foreach_item_5_saved_key = isset($_smarty_tpl->tpl_vars['key']) ? $_smarty_tpl->tpl_vars['key'] : false;
$_smarty_tpl->tpl_vars['item'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['key'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['item']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
$__foreach_item_5_saved_local_item = $_smarty_tpl->tpl_vars['item'];
?>
										<th width=""><?php echo $_smarty_tpl->tpl_vars['item']->value['code'];?>
</th>
									<?php
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_5_saved_local_item;
}
if ($__foreach_item_5_saved_item) {
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_5_saved_item;
}
if ($__foreach_item_5_saved_key) {
$_smarty_tpl->tpl_vars['key'] = $__foreach_item_5_saved_key;
}
?>	
									<td style="text-align:center">%</td>
									<?php
$_from = $_smarty_tpl->tpl_vars['data']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_item_6_saved_item = isset($_smarty_tpl->tpl_vars['item']) ? $_smarty_tpl->tpl_vars['item'] : false;
$__foreach_item_6_saved_key = isset($_smarty_tpl->tpl_vars['key']) ? $_smarty_tpl->tpl_vars['key'] : false;
$_smarty_tpl->tpl_vars['item'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['key'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['item']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
$__foreach_item_6_saved_local_item = $_smarty_tpl->tpl_vars['item'];
?>
										<td style="text-align:center">%</td>
									<?php
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_6_saved_local_item;
}
if ($__foreach_item_6_saved_item) {
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_6_saved_item;
}
if ($__foreach_item_6_saved_key) {
$_smarty_tpl->tpl_vars['key'] = $__foreach_item_6_saved_key;
}
?>
								</tr>
																
								<tr>
										<th width="">Total </th>
										<?php
$_from = $_smarty_tpl->tpl_vars['data']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_item_7_saved_item = isset($_smarty_tpl->tpl_vars['item']) ? $_smarty_tpl->tpl_vars['item'] : false;
$__foreach_item_7_saved_key = isset($_smarty_tpl->tpl_vars['key']) ? $_smarty_tpl->tpl_vars['key'] : false;
$_smarty_tpl->tpl_vars['item'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['key'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['item']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
$__foreach_item_7_saved_local_item = $_smarty_tpl->tpl_vars['item'];
?>
										<td style="text-align:center"></td>
									<?php
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_7_saved_local_item;
}
if ($__foreach_item_7_saved_item) {
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_7_saved_item;
}
if ($__foreach_item_7_saved_key) {
$_smarty_tpl->tpl_vars['key'] = $__foreach_item_7_saved_key;
}
?>	
									<td style="text-align:center"></td>									
								</tr>
			
							</tbody>
							</table>																	

		<div class="alert alert-login no_record dn">
								<a class="close" data-dismiss="alert">×</a>
								<strong>Oops!</strong> No records found!.
							</div>							
				</div></div>		
				<div class="row-fluid <?php echo $_smarty_tpl->tpl_vars['format_type_table']->value;?>
 printAreaGraph">						
		<div class="span12">
						<h3 class="heading" style="margin-bottom:0;border:;clear:left;">CV Rejection Analysis (Graph View)							
								</h3>						
		<a href="javascript:void(0)" rel="printAreaGraph" style="float:left;clear:left;margin:10px 10px 0px 0px" class="printBtn no-print"><input value="Print" type="button" class="btn btn-success"/></a>	
			<div class="graph printAreaGraph"  id="ctc_wise2" style="clear:both;height:<?php echo $_smarty_tpl->tpl_vars['chart_height']->value;?>
px">
							</div>						
				</div>						
					</div>	
              </div>	          				
					</div>			    
                </div>
            </div>		

					
		 <?php echo '<script'; ?>
 type="text/javascript" src="https://www.gstatic.com/charts/loader.js"><?php echo '</script'; ?>
>
  <?php echo '<script'; ?>
 type="text/javascript">
    google.charts.load("current", {packages:['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ['Client', 'CV Sent to Shortlisted %', 'CV Sent to Interviewed %', 'CV Sent to Offered %', 'CV Sent to Joined %','CV Sent to Billed %', 'Openings Worked to Billed %'],
		<?php echo '<?php'; ?>
 $i = 0; 
		foreach($clientDetail as $id => $client): <?php echo '?>'; ?>

		<?php echo '<?php'; ?>
 //if($cv_sent_shortlist[$i] != '' || $cv_sent_interview[$i] != '' || $cv_sent_offer[$i] != '' || $cv_sent_join[$i] != '' || $cv_sent_billed[$i] != '' || 	$opening_work_billed[$i] != ''):<?php echo '?>'; ?>

        ['{ucwords($client)}', {$cv_sent_shortlist[$i]}, {$cv_sent_interview[$i]}, {$cv_sent_offer[$i]}, {$cv_sent_join[$i]}, {$cv_sent_billed[$i]},{$opening_work_billed[$i]}],   
		
		<?php echo '<?php'; ?>
 //endif; 
			$i++;
			endforeach; <?php echo '?>'; ?>

      ]);
	  
	  var formatPercent = new google.visualization.NumberFormat({
   // pattern: '#,##0.0%'
	pattern:'#.#%'
  });

      var view = new google.visualization.DataView(data);
      view.setColumns([0,
	  1,{
      calc: function (dt, row) {
        return   formatPercent.formatValue(dt.getValue(row, 1)/100 );
      },
      type: "string",
      role: "annotation"
    },
	  2,{
      calc: function (dt, row) {
        return   formatPercent.formatValue(dt.getValue(row, 2)/100 );
      },
      type: "string",
      role: "annotation"
    },
	  3,{
      calc: function (dt, row) {
        return   formatPercent.formatValue(dt.getValue(row, 3)/100 );
      },
      type: "string",
      role: "annotation"
    },
	  4,{
      calc: function (dt, row) {
        return   formatPercent.formatValue(dt.getValue(row, 4)/100 );
      },
      type: "string",
      role: "annotation"
    },
	  5,{
      calc: function (dt, row) {
        return   formatPercent.formatValue(dt.getValue(row, 5)/100 );
      },
      type: "string",
      role: "annotation"
    },
	  6,{
      calc: function (dt, row) {
        return   formatPercent.formatValue(dt.getValue(row, 6)/100 );
      },
      type: "string",
      role: "annotation"
    },
	  ]);

      var options = {
        title: "",
		// CTC Wise Monthly Openings Handled (Recruiter: Lavanya)
        annotations: {
         //  alwaysOnTop: true,
          textStyle: {
             fontSize: 15,
             color: '#000',
             auraColor: 'none'
          }
		  
        },
		  vAxis: {
          title: 'Clients',
		  gridlines:{color:'#ccc', count: 4},
		   textStyle: {
             fontSize: 12
			 }
		  

        },
		hAxis: {
           title: 'No. of Openings',
		   gridlines:{color:'#fff'},
		    textPosition : 'none',
		   // format:'#.##%'
		   format:'#%'
        },
		
      // legend: { position: "bottom" },
		
		
		  // '#453d7e', '#2f97d3', '#bdcd40', '#ffcc2b', '#f58634'
		
		
		// '#ea3639','#f58634', '#ffcc2b', '#bdcd40', '#2f97d3', '#453d7e'
		
		
		  colors: ['#453d7e','#2f97d3', '#bdcd40', '#ffcc2b', '#f58634', '#ea3639'],

		//  colors: ['#6688e9', '#f58634', '#12de6d', '#811905', '#ab1f57', '#23E5FF', '#ab1f57',  '#811905','#09418d', '#fabec2', '#0dac01','#d7f477'],
		  legend: {position: 'bottom',alignment: 'start', maxLines:5, textStyle: {color: '#000000', fontSize: 14}},
         dataOpacity: 0.8,
		  isStacked:'percent',
		  bar: { groupWidth: '65%'},
		  chartArea:{width:"85%",left:120, top:30},
		  tooltip:{textStyle: {color: '#000000', fontSize: 14}},
		  titleTextStyle:{ fontSize: 15},
      };
	  
	  function getValueAt(column, dataTable, row) {
			return dataTable.getFormattedValue(row, column);
	}
	
      var chart = new google.visualization.BarChart(document.getElementById("ctc_wise2"));
      chart.draw(view, options);
  }
  
  <?php echo '</script'; ?>
>	
  	
		</div>	
	</div>
<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:include/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
