{* Purpose : To show cv rejection analysis
 Created : Nikitasa
   Date : 16-07-2018 *}
   
{literal}
<style type="text/css">
.printAreaTable a{color:#000000 !important}
.printAreaTable tr td{font-size:13px;padding:6px;}
.printAreaTable tr th{font-size:13px;padding:6px;}
.dl-horizontal dt{text-align:left}
.btnExport li a:hover{background:#efefef !important; color:#000 !important}
ul.statusLegend li{width:200px;}
</style>
{/literal}
	
{include file='include/header.tpl'} 
{include file='include/sidebar.tpl'}       
    <div id="contentwrapper">
                <div class="main_content">
					<div class="row-fluid">
					  <div class="span12" style="margin:0;">
					  <form action="cv_rejection_analysis.php" id="formID" class="formID" method="post" accept-charset="utf-8">								
						<div class="dn dataTables_filter srchBox homeSrchBox" id="dt_gal_filter" style="display: block;">	
							
							<span id="sandbox-container">
						<span class="input-daterange" id="datepicker">	
						<label>From Date: <input type="text" id="" placeholder="dd/mm/yyyy" class="input-small datepick" name="f_date" value="{$f_date}" aria-controls="dt_gal"></label>
						<label>To Date: <input type="text"  id=""  placeholder="dd/mm/yyyy" name="t_date" value="{$t_date}" class="input-small datepick" aria-controls="dt_gal"></label>				
						</span>	
						</span>	
						<label>	Client: 
						<select name="client_id" class="input-medium" placeholder="" style="clear:left" id="IncentiveEmpId">
						<option value="">Select</option>
							{html_options options=$client_name selected=$client_id}
						</option>
						</select>
						</label>
						<label>	Role: 
						<select name="role_id" class="input-medium" placeholder="" style="clear:left" id="IncentiveEmpId">
						<option value="">Select</option>
							{html_options options=$role_name selected=$role_id}
						</option>	
						</select>
						</label>					
						<label>	Branch: 
						<select name="branch_id" class="input-medium" placeholder="" style="clear:left" id="IncentiveEmpId">
						<option value="">Select</option>
							{html_options options=$branch_name selected=$branch_id}
						</option>
						</select>						
						</label>
						<label>Employee: 
						<select name="emp_id" class="input-medium" placeholder="" style="clear:left" id="IncentiveEmpId">
						<option value="">Select</option>
							{html_options options=$emp_name selected=$emp_id}
						</option>
						</select>						
						</label>
											
					<label style="margin-top:18px;"><input type="submit" value="Submit" class="btn btn-gebo validateReport"></label>
					<label style="margin-top:18px;"><a class="jsRedirect" href=""><input value="Reset" type="button" class="btn"></a></label>

					</div>
					
						<input type="hidden" name="data[srchSubmit]" id="srchSubmit"/>
						<input type="hidden" value="1" id="SearchKeywords">
						<input type="hidden" value="{$this->webroot}report/get_employee/" id="webroot">	
						<input type="hidden" name="data[type]" id="type"/>	
					
					</form>
						
					<div class="row-fluid {$format_type_chart} printAreaTable">		
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
						
						{if $fromDate != '' || $toDate != '' || $clientName != '' || $roleName != '' || $locName != '' || $empName != ''}
							<h6 style="clear:left;margin-top:10px">Search Filters:</h6> 
		
		<dl class="dl-horizontal">
										
										{if !empty($fromDate)}
										<dt>From Date: </dt>
										<dd>{$fromDate}</dd>
										{/if}	
										
										{if !empty($toDate)}
										<dt>To Date: </dt>
										<dd>{$toDate}</dd>
										{/if}	
										
										
										{if !empty($clientName)}
										<dt>Client: </dt>
										<dd>{$clientName}</dd>
										{/if}	
										
										{if !empty($roleName)}
										<dt>Role: </dt>
										<dd>{$roleName}</dd>
										{/if}	
										
										{if !empty($locName)}
										<dt>Branch: </dt>
										<dd>{$locName}</dd>
										{/if}	
										
										
										{if !empty($empName)}
										<dt>Employee: </dt>
										<dd>{$empName}</dd>
										{/if}	
										
									</dl>
									{/if}					
									
							<table class="table table-hover table-bordered table-striped printAreaTable" style="margin: 15px 0px;">
								<thead>								
									<tr>
										<th width="150" style="min-width: 0px; max-width: none;"><a href="#">Reasons / Client</a></th>	
										{foreach from=$data item=item key=key}
										<th width="180" style="min-width: 0px; max-width: none;text-align:center"><a href="#">{$item.client_name}</a></th>
										{/foreach}
									</tr>
								</thead>
								<tbody>							
																						
								<tr>
										<th width="">Total CVs Sent </th>
										{foreach from=$data item=item key=key}
										<td style="text-align:center"></td>
										{/foreach}	
									
								</tr>	
								<tr>
										<th width="">CVs Billed </th>
										{foreach from=$data item=item key=key}
										<td style="text-align:center"></td>
									{/foreach}	
								
								</tr>
								<tr>
										<th width="">CVs Still Active </th>
										{foreach from=$data item=item key=key}
										<td style="text-align:center"></td>
									{/foreach}	
								</tr>
								<tr>
										<th width="">CVs Rejected / Gone Inactive </th>
										{foreach from=$data item=item key=key}
										<td style="text-align:center"></td>
									{/foreach}	
								</tr>
								
								<tr>
										<th width="">Code 1 </th>
										{foreach from=$data item=item key=key}
										<td style="text-align:center"></td>
									{/foreach}	
								</tr>
								
								<tr>
										<th width="">Code 2 </th>
										{foreach from=$data item=item key=key}
										<td style="text-align:center"></td>
									{/foreach}	
								</tr>
								
								<tr>
										<th width="">Total </th>
										{foreach from=$data item=item key=key}
										<td style="text-align:center"></td>
									{/foreach}									
								</tr>
			
							</tbody>
							</table>																	

		<div class="alert alert-login no_record dn">
								<a class="close" data-dismiss="alert">×</a>
								<strong>Oops!</strong> No records found!.
							</div>							
				</div></div>		
				<div class="row-fluid {$format_type_table} printAreaGraph">						
		<div class="span12">
						<h3 class="heading" style="margin-bottom:0;border:;clear:left;">CV Rejection Analysis (Graph View)							
								</h3>						
		<a href="javascript:void(0)" rel="printAreaGraph" style="float:left;clear:left;margin:10px 10px 0px 0px" class="printBtn no-print"><input value="Print" type="button" class="btn btn-success"/></a>	
			<div class="graph printAreaGraph"  id="ctc_wise2" style="clear:both;height:{$chart_height}px">
							</div>						
				</div>						
					</div>	
              </div>	          				
					</div>			    
                </div>
            </div>		

			{literal}		
		 <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load("current", {packages:['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ['Client', 'CV Sent to Shortlisted %', 'CV Sent to Interviewed %', 'CV Sent to Offered %', 'CV Sent to Joined %','CV Sent to Billed %', 'Openings Worked to Billed %'],
		<?php $i = 0; 
		foreach($clientDetail as $id => $client): ?>
		<?php //if($cv_sent_shortlist[$i] != '' || $cv_sent_interview[$i] != '' || $cv_sent_offer[$i] != '' || $cv_sent_join[$i] != '' || $cv_sent_billed[$i] != '' || 	$opening_work_billed[$i] != ''):?>
        ['{ucwords($client)}', {$cv_sent_shortlist[$i]}, {$cv_sent_interview[$i]}, {$cv_sent_offer[$i]}, {$cv_sent_join[$i]}, {$cv_sent_billed[$i]},{$opening_work_billed[$i]}],   
		
		<?php //endif; 
			$i++;
			endforeach; ?>
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
  
  </script>	
  {/literal}	
		</div>	
	</div>
{include file='include/footer.tpl'}