{* Purpose : To show location performance report.
   Created : Nikitasa
   Date : 23-06-2017 *}
   

			{include file='include/header.tpl'}	
		<!-- main content -->
            <div id="contentwrapper">
                <div class="main_content">
                
				
					
										
					<div class="row-fluid">
						
					  
					  <div class="span12" style="margin:0;">
					  <nav>
                        <div id="jCrumbs" class="breadCrumb module">
                            <ul>
                                <li>
                                    <a href="{$smarty.const.webroot}home"><i class="icon-home"></i></a>
                                </li>
                                  <li>
                                     <a href="client_retention.php">Client Retention</a> 
                                      </li>
                                <li>
                                  Reporting 
                                </li>
                            </ul>
                        </div>
                    </nav>
				<div class="srch_buttons">

							<a class="jsRedirect toggleSearch" href="javascript:void(0)">
							<input type="button" value="Search" class="homeSrch btn btn-success"></a>
														 							
							</div>
							<form>
															
							<div class="dataTables_filter homeSrchBox srchBox"  id="dt_gal_filter">

							
				<span id="sandbox-container">
						<span class="input-daterange" id="datepicker">	
							<label style="margin-left:0" >From Date: <input placeholder="dd/mm/yyyy" type="text" class="input-small" name="data[Position][from]" value="" aria-controls="dt_gal"></label>

							<label>To Date: <input placeholder="dd/mm/yyyy" type="text" name="data[Position][to]" value="" class="input-small" aria-controls="dt_gal"></label>

						</span>	
						</span>	
						
						
							<label>Employee: 
						<select name="data[emp_id]" class="input-medium" placeholder="" style="clear:left" id="emp_id">
<option value="">Select</option>
<option value="0">Bhargavi</option>
<option value="1" selected="selected">Suganya</option>
</select> 															
													
							</label>
			<label>Client: <input type="text" placeholder="Client Name" name="data[Home][client]" id = "SearchText" value="Amrutanjan" class="input-large" aria-controls="dt_gal"></label>
			<label>
							Branch: 
							<select name="data[Position][loc]" class="input-medium" placeholder="" style="clear:left" id="PositionLoc">
<option value="">Select</option>
<option value="104">Ahmadabad</option>
<option value="102">Bangalore</option>
<option value="103">Chennai</option>
<option value="105">Hyderabad</option>
</select> 
							</label>
												
		<label style="margin-top:18px;"><input type="button" value="Submit" class="btn btn-gebo" /></label>
	<label style="margin-top:18px;"><a href="client_retention.php"><input value="Reset" type="button" class="btn"/></a></label>

	<label style="margin-top:18px;"><a href="#"><input value="Export" type="button" class="btn btn-warning"/></a></label>
							
					
					
					
						
														</div>
<input type="hidden" name="data[srchSubmit]" id="srchSubmit"/><input type="hidden" value="23/12/2016" id="end_date">
<input type="hidden" value="23/09/2016" id="start_date">
		<input type="hidden" value="1" id="SearchKeywords">
						<input type="hidden" value="home/" id="webroot">	
<input type="hidden" name="data[type]" id="type"/>						
						</form>
						
						
							<table class="table table-striped table-hover table-bordered  stickyTable" style="padding: 0px;">
								<thead class="tableFloatingHeaderOriginal" style="position: static; margin-top: 0px; left: 31px; z-index: 3; width: 1287px; top: 0px;">
									<tr>
										<th width="150">Client Name </th>
										<th width="60">Status </th>
										<th width="130">Last Position Received </th>
										<th width="70">Last Resume Served  </th>
										<th width="70">Last Invoice Raised </th>
										<th width="70">Average Billing Days </th>
										<th width="70">Most Active Periods </th>
										<th width="100" style="text-align:center;min-width: 0px; max-width: none;" colspan="4">No.of Invoices Raised </th>
										<th width="100">Reason for Inactive</th>
									</tr>
									<tr>
										<th width=""> </th>
										<th width=""> </th>
										<th width=""> </th>
										<th width="">  </th>
										<th width=""></th>
										<th width=""></th>
										<th width=""></th>
										<th width="80" style="text-align:center">Oct, 2017 </th>
										<th width="80" style="text-align:center">Jul, 2017 </th>
										<th width="80" style="text-align:center">Oct, 2016 </th>
										<th width="80" style="text-align:center">Jul, 2016 </th>
										<th width=""></th>
									</tr>	
								</thead>
								
								
								<tbody>
							
								<tr>
										<td>Maruti</td>
										<td>Inactive</td>
										<td>2 days</td>
										<td>3 days</td>
										<td>3 days</td>
										<td>3 days</td>
										<td>Jan, 2017</td>
										<td style="text-align:center">3</td>
										<td style="text-align:center">6</td>
										<td style="text-align:center">6</td>
										<td style="text-align:center">12</td>
										<td>No New Recruitments</td>
								</tr>
	                    	<tr>
										<td>Olympia</td>
										<td>Active</td>
										<td>3 days</td>
										<td>2 days</td>
										<td>4 days</td>
										<td>5 days</td>
										<td>Jun, 2017</td>
										<td style="text-align:center">32</td>
										<td style="text-align:center">45</td>
										<td style="text-align:center">15</td>
										<td style="text-align:center">76</td>
										<td> </td>
								</tr>
								<tr>
										<td>Ramgopal.N</td>
										<td>INctive</td>
										<td>3 days</td>
										<td>2 days</td>
										<td>2 days</td>
										<td>2 days</td>
										<td>Jun, 2017</td>
										<td style="text-align:center">7</td>
										<td style="text-align:center">12</td>
										<td style="text-align:center">9</td>
										<td style="text-align:center">87</td>
										<td>Client is not responding to CT calls </td>
								</tr>
								<tr>
										<td>Lucas - TVS</td>
										<td>Inactive</td>
										<td>2 days</td>
										<td>3 days</td>
										<td>3 days</td>
										<td>3 days</td>
										<td>Jan, 2017</td>
										<td style="text-align:center">7</td>
										<td style="text-align:center">34</td>
										<td style="text-align:center">12</td>
										<td style="text-align:center">87</td>
										<td>No New Recruitments</td>
								</tr>
								<tr>
										<td>Salcomp</td>
										<td>Active</td>
										<td>3 days</td>
										<td>2 days</td>
										<td>4 days</td>
										<td>5 days</td>
										<td>Jun, 2017</td>
										<td style="text-align:center">7</td>
										<td style="text-align:center">67</td>
										<td style="text-align:center">12</td>
										<td style="text-align:center">55</td>
										<td> </td>
								</tr>
								<tr>
										<td>QAssure Technologies</td>
										<td>Inactive</td>
										<td>2 days</td>
										<td>3 days</td>
										<td>2 days</td>
										<td>5 days</td>
										<td>Jan, 2017</td>
										<td style="text-align:center">12</td>
										<td style="text-align:center">22</td>
										<td style="text-align:center">55</td>
										<td style="text-align:center">45</td>
										<td>Client is not happy with CT Service</td>
								</tr>
	                    	<tr>
										<td>FoodBox</td>
										<td>Active</td>
										<td>3 days</td>
										<td>2 days</td>
										<td>4 days</td>
										<td>3 days</td>
										<td>Feb, 2017</td>
										<td style="text-align:center">6</td>
										<td style="text-align:center">7</td>
										<td style="text-align:center">1</td>
										<td style="text-align:center">4</td>
										<td> </td>
								</tr>
								<tr>
										<td>QAssure Technologies</td>
										<td>INctive</td>
										<td>3 days</td>
										<td>2 days</td>
										<td>2 days</td>
										<td>2 days</td>
										<td>Jun, 2017</td>
										<td style="text-align:center">5</td>
										<td style="text-align:center">6</td>
										<td style="text-align:center">77</td>
										<td style="text-align:center">22</td>
										<td>Client is not responding to CT calls </td>
								</tr>
								
								</tbody>
							</table>
							<div class="row" style="margin-left:0px;">


<div class="span4">					   
<div class="" id="dt_gal_info">

Page <span>1</span> of <span>14</span> <b>Total:</b> <span>100</span>

</div> 
</div>

<div class="span8">

<div class="dataTables_paginate paging_bootstrap pagination">
					
 <ul>
<li class="disabled"><a>1</a></li> <li><a href="#">2</a></li> <li><a href="#">3</a></li> <li><a href="#">4</a></li> <li><a href="#">5</a></li> <li><a href="#">6</a></li> <li><a href="#">7</a></li> <li><a href="#">8</a></li> <li><a href="#">9</a></li>
<li class="next"><a href="#" rel="next"> Next &gt;</a></li><li><a href="#" rel="last"> Last &gt;&gt;</a></li>

</ul>
</div>
</div>
</div>
</div>
</div>
</div>
 </div>
            
	
		
		</div>
		
	</div>
		
		
{include file='include/footer.tpl'}