{* Purpose : To view mailbox.
 Created : Nikitasa
   Date : 13-07-2017 *}
   

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
                                    <a href="mailbox.php">Mail Box</a>
                                </li>                          
                                <li>
                                    View Sent Items
                                </li>
                            </ul>
                        </div>
                    </nav>
					{if $EXIST_MSG}
				 <div id="flashMessage" class="alert alert-success"><button type="button" class="close" data-dismiss="alert">×</button>{$EXIST_MSG}</div>					
				{/if}
		<form action="view_mailbox.php?id={$smarty.get.id}&multi_resume_id={$smarty.get.multi_resume_id}&req_id={$smarty.get.req_id}" name="formID" class="formID" id="formID" method="post" enctype="multipart/form-data" accept-charset="utf-8">
					
		<div class="box">
		<div class="row-fluid">
		<div class="span12">
		<div class="mbox">
			<div class="tabbable">
								
			<div class="tab-content" style="overflow:visible">			
			
			<div class="span12">
							<table class="table table-striped table-bordered dataTable" style="">
								<tbody>
									<tr>
										<td width="120" class="tbl_column">To </td>
										<td>{if $data.mail_type == 'R'}{ucwords($data.candidate_name)} ({$data.email_id}){else}{$data.client_con_name} ({$data.email}){/if} </td>
									</tr>
									{if $data.mail_type == 'C'}
									<tr>
										<td width="120" class="tbl_column">Cc </td>
										<td>{$data.cc}</td>
									</tr>
									{/if}
									<tr>
										<td width="120" class="tbl_column">Client Name </td>
										<td>{ucwords($data.client_name)}</td>
									</tr>
									
									<tr>
										<td width="" class="tbl_column">Subject  </td>
										<td>{$data.subject}</td>
									</tr>
									<tr>
										<td width="" class="tbl_column">Message </td>
										<td>{$data.message}</td>
									</tr>
									<tr>
										<td width="" class="tbl_column">Attachment </td>
										<td><a href = "view_mailbox.php?id={$smarty.get.id}&multi_resume_id={$smarty.get.multi_resume_id}&req_id={$smarty.get.req_id}&action=download&file={$data.attachment}">{$data.attachment}</a></td>
									</tr>	
									<!-- tr>
										<td width="" class="tbl_column">Candidate Resumes </td>
										<td><a href = "view_mailbox.php?id={$smarty.get.id}&multi_resume_id={$smarty.get.multi_resume_id}&req_id={$smarty.get.req_id}&action=download_resume&file={$cond}">{$cond}</a></td>
									</tr-->									
									<tr>
										<td width="" class="tbl_column">Sent Date </td>
										<td>{$created_date}</td>
									</tr>
									<tr>
										<td width="" class="tbl_column">Sent By </td>
										<td>{$data.employee}</td>
									</tr>
									
								</tbody>
							</table>
				</div>
                        	
              
					</div>
					</div>  
					</div>
					</div>
					</div>
					</div>
						<div class="form-actions">
								<input name="resend" id="resend" class="btn btn-gebo confirm_btn" value="Resend" type="submit"/>
								<a href="mailbox.php" class="jsRedirect cancelBtn"><input type="button" value="Back" class="btn">
						</div>

               </div>
			</form>		
          </div>
       </div>
      </div>
	</div>
</div>
</div>
			


{include file='include/footer.tpl'}
