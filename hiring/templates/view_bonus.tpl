{* Purpose : To list and search bonus.
   Created : Nikitasa
   Date : 21-02-2017 *}
   

			{include file='include/header.tpl'}

			<!-- main content -->
            <div id="contentwrapper">
                <div class="main_content">
                <div class="row-fluid">
						<div class="span12">
								<nav>
                        <div id="jCrumbs" class="breadCrumb module">
                            <ul>
                                <li>
                                    <a href="{$smarty.const.webroot}home"><i class="icon-home"></i></a>
                                </li>
                                <li>
                                    <a href="bonus.php">Bonus</a>
                                </li>
                            
                                <li>
                                   Reshu
                                </li>
                            </ul>
                        </div>
                    </nav>	
							
						<div class="row-fluid">
							<div class="span6">
							<table class="table table-striped table-bordered dataTable" style="margin-bottom:0;">
								<tbody>
									<tr>
										<td width="120" class="tbl_column">Employee Name</td>
										<td>Reshu</td>
									</tr>
									
									<tr>
										<td width="" class="tbl_column">Bonus(%) </td>
										<td>2%</td>
									</tr>
									<tr>
										<td width="" class="tbl_column">Created Date </td>
										<td>20-06-2016</td>
									</tr>	
									
								</tbody>
							</table>
							</div>
							
							<div class="span6">
							<table class="table table-striped table-bordered dataTable" style="margin-bottom:0">
								<tbody>
								<tr>
									<td class="tbl_column">Quarter </td>
									<td>2</td>
								</tr>	
									
								<tr>
									<td class="tbl_column">Bonus Amount  </td>
									<td>10,000</td>
								</tr>	
								</tbody>
							</table>
							</div>
                 </div>
						<div class="form-actions">
								<a href="bonus.php" class="jsRedirect"><button class="btn">Back</button></a>
						</div>
               </div>
					
          </div>
       </div>
      </div>
	</div>
</div>
</div>
{include file='include/footer.tpl'}