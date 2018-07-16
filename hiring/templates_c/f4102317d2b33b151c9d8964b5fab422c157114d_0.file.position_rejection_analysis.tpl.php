<?php
/* Smarty version 3.1.29, created on 2018-07-16 14:28:45
  from "C:\xampp\htdocs\2017\ctsvn2\managehiring\hiring\templates\position_rejection_analysis.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5b4c5e45dc21a9_94191582',
  'file_dependency' => 
  array (
    'f4102317d2b33b151c9d8964b5fab422c157114d' => 
    array (
      0 => 'C:\\xampp\\htdocs\\2017\\ctsvn2\\managehiring\\hiring\\templates\\position_rejection_analysis.tpl',
      1 => 1531731273,
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
function content_5b4c5e45dc21a9_94191582 ($_smarty_tpl) {
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
						<label>From Date: <input type="text" placeholder="dd/mm/yyyy" class="input-small datepick" name="f_date" value="<?php echo $_smarty_tpl->tpl_vars['f_date']->value;?>
" aria-controls="dt_gal"></label>
						<label>To Date: <input type="text" placeholder="dd/mm/yyyy" name="t_date" value="<?php echo $_smarty_tpl->tpl_vars['t_date']->value;?>
" class="input-small datepick" aria-controls="dt_gal"></label>				
						</span>	
						</span>	
						<label>	Client: 
						<select name="client_id" class="input-medium" placeholder="" style="clear:left" id="IncentiveEmpId">
						<option value="">Select</option>
							<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['program_name']->value,'selected'=>$_smarty_tpl->tpl_vars['client_id']->value),$_smarty_tpl);?>

						</option>
						</select>
						</label>
						<label>	Role: 
						<select name="role_id" class="input-medium" placeholder="" style="clear:left" id="IncentiveEmpId">
						<option value="">Select</option>
							<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['program_name']->value,'selected'=>$_smarty_tpl->tpl_vars['role_id']->value),$_smarty_tpl);?>

						</option>	
						</select>
						</label>					
						<label>	Branch: 
						<select name="branch_id" class="input-medium" placeholder="" style="clear:left" id="IncentiveEmpId">
						<option value="">Select</option>
							<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['program_name']->value,'selected'=>$_smarty_tpl->tpl_vars['branch_id']->value),$_smarty_tpl);?>

						</option>
						</select>						
						</label>
						<label>Employee: 
						<select name="emp_id" class="input-medium" placeholder="" style="clear:left" id="IncentiveEmpId">
						<option value="">Select</option>
							<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['program_name']->value,'selected'=>$_smarty_tpl->tpl_vars['emp_id']->value),$_smarty_tpl);?>

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
						<h3 class="heading" style="margin-bottom:0;border:;clear:left;">Client Wise CV Status (Table View)</h3>
						
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
										<th width="150" style="min-width: 0px; max-width: none;"><a href="#">Client</a></th>																		
										<th width="50" style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Openings Worked" href="#">OW</a></th>
										<th width="50" style="min-width: 0px; max-width: none;text-align:center" ><a rel="tooltip" title="CV Sent"href="#">Sent</a></th>
										<th width="50" style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="CV Shortlisted" href="#">CVS</a> </th>
										<th width="50" style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="CV Rejected" href="#">CVR</a> </th>
										<th width="50" style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="CV Feedback Awaited" href="#">FA</a></th>
										<th width="50" style="min-width: 0px; max-width: none;text-align:center" ><a rel="tooltip" title="Interview Schedule Awaited" href="#">ISA</a></th>
										<th width="50" style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Preliminary Interviews Attended" href="#">PIA</a> </th>
										<th width="50" style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Final Interview Attended" href="#">FIA</a> </th>
										<th width="50" style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Offer Pending" href="#">OP</a></th>
										<th width="50" style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Offer Accepted" href="#">OA</a> </th>					
										<th width="50" style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Offer Rejected" href="#">OR</a> </th>
										<th width="50" style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Joining Awaited" href="#">JA</a> </th>
										<th width="50" style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Candidate Joined" href="#">J</a> </th>
										<th width="50" style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Not Joined" href="#">NJ</a></th>
										<th width="50" style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Joining Deferred" href="#">JD</a> </th>
										<th width="50" style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Billing Pending" href="#">BP</a> </th>
										<th width="50" style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Candidate Billed" href="#">B</a> </th>								
									</tr>
								</thead>
								<tbody>							
								<?php $_smarty_tpl->tpl_vars['j'] = new Smarty_Variable(0, null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'j', 0);?>
								<?php
$_from = $_smarty_tpl->tpl_vars['clientDetail']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_client_0_saved_item = isset($_smarty_tpl->tpl_vars['client']) ? $_smarty_tpl->tpl_vars['client'] : false;
$__foreach_client_0_saved_key = isset($_smarty_tpl->tpl_vars['id']) ? $_smarty_tpl->tpl_vars['id'] : false;
$_smarty_tpl->tpl_vars['client'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['id'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['client']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['id']->value => $_smarty_tpl->tpl_vars['client']->value) {
$_smarty_tpl->tpl_vars['client']->_loop = true;
$__foreach_client_0_saved_local_item = $_smarty_tpl->tpl_vars['client'];
?>
									
								<tr>
										<td width=""><a target="_blank" href="<?php echo $_smarty_tpl->tpl_vars['this']->value->webroot;?>
client/view/<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
/"><?php echo ucwords($_smarty_tpl->tpl_vars['client']->value);?>
</td>
										
										<td style="text-align:center"></td>
										
										<td style="text-align:center"></td>
										
										
										<td style="text-align:center"></td>
										
										<td style="text-align:center"></td>
										
										<td style="text-align:center"></td>
										
										
										<td style="text-align:center"></td>
						
										<td style="text-align:center"></td>
										
										<td style="text-align:center"></td>
										
										<td style="text-align:center"></td>
										
																
										<td style="text-align:center"></td>
										
										<td style="text-align:center"></td>
																												

										<td style="text-align:center"></td>

																																								
										<td style="text-align:center"></td>
										
										<td style="text-align:center"></td>
										
										<td style="text-align:center"></td>
														
								</tr>
							<?php
$_smarty_tpl->tpl_vars['client'] = $__foreach_client_0_saved_local_item;
}
if ($__foreach_client_0_saved_item) {
$_smarty_tpl->tpl_vars['client'] = $__foreach_client_0_saved_item;
}
if ($__foreach_client_0_saved_key) {
$_smarty_tpl->tpl_vars['id'] = $__foreach_client_0_saved_key;
}
?>								
								<tr>
										<th width="">Total </th>
										<th style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['ow_total']->value;?>
</th>
										<th style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['sent_total']->value;?>
</a></th>								
										<th style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['shortlist_total']->value;?>
</a></th>									
										<th style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['reject_total']->value;?>
</th>										
										<th style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['feedback_total']->value;?>
</th>																			
										<th style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['interview_await_total']->value;?>
</th>										
										<th style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['prili_interview_attend_total']->value;?>
</th>										
										<th style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['final_interview_total']->value;?>
</th>										
										<th style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['op_total']->value;?>
</th>										
										<th style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['oa_total']->value;?>
</th>										
										<th style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['or_total']->value;?>
</th>										
										<th style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['jp_total']->value;?>
</th>										
										<th style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['ja_total']->value;?>
</th>										
										<th style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['nj_total']->value;?>
</th>										
										<th style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['jd_total']->value;?>
</th>										
										<th style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['not_bill_total']->value;?>
</th>										
										<th style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['bill_total']->value;?>
</th>
								</tr>					
							</tbody>
							</table>																	
<ul class="status_row statusLegend" style="margin-left:0px;">	
<li><span class="btn-mini legendBg" > OW </span> &nbsp; Openings Worked</li>
<li><span class="btn-mini legendBg" style="padding:0px 10px !important"> Sent </span> &nbsp; CV Sent	</li>
<li><span class="btn-mini legendBg"> CVS  </span> &nbsp; CV Shortlisted	</li>
<li><span class="btn-mini legendBg"> CVR </span> &nbsp; CV Rejected</li>
<li><span class="btn-mini legendBg"> FA </span> &nbsp; CV Feedback Awaited</li>
<li><span class="btn-mini legendBg" style="padding:0px 7px !important"> ISA  </span> &nbsp; Interview Schedule Awaited</li>
<li><span class="btn-mini legendBg"> PIA </span> &nbsp; Preli. Interviews Attended</li>
<li><span class="btn-mini legendBg" > FIA </span> &nbsp; Final Interview Attended</li>
<li><span class="btn-mini legendBg"> OP  </span> &nbsp; Offer Pending</li>
<li><span class="btn-mini legendBg"> OA </span> &nbsp; Offer Accepted</li>
<li><span class="btn-mini legendBg"> OR </span> &nbsp; Offer Rejected</li>

<li><span class="btn-mini legendBg" style="padding:0px 7px !important"> JA </span> &nbsp; Joining Awaited</li>
<li><span class="btn-mini legendBg" style="padding:0px 7px !important"> J </span> &nbsp; Candidate Joined</li>
<li><span class="btn-mini legendBg" style="padding:0px 10px !important"> NJ </span> &nbsp; Not Joined </li>

<li><span class="btn-mini legendBg" style="padding:0px 7px !important"> JD </span> &nbsp; Joining Deferred</li>
<li><span class="btn-mini legendBg" style="padding:0px 7px !important"> BP </span> &nbsp; Billing Pending</li>
<li><span class="btn-mini legendBg" style="padding:0px 10px !important"> B </span> &nbsp; Candidate Billed </li>


</ul>
		<div class="alert alert-login no_record dn">
								<a class="close" data-dismiss="alert">×</a>
								<strong>Oops!</strong> No records found!.
							</div>							
				</div></div>		
				<div class="row-fluid <?php echo $_smarty_tpl->tpl_vars['format_type_table']->value;?>
 printAreaGraph">						
		<div class="span12">
						<h3 class="heading" style="margin-bottom:0;border:;clear:left;">Client Wise CV Status (Graph View)							
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
