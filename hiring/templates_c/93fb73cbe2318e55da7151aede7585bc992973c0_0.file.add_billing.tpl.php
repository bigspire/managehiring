<?php
/* Smarty version 3.1.29, created on 2018-03-15 11:35:52
  from "C:\xampp\htdocs\2017\ctsvn2\cthiring\hiring\templates\add_billing.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5aaa0d409af0e9_56567395',
  'file_dependency' => 
  array (
    '93fb73cbe2318e55da7151aede7585bc992973c0' => 
    array (
      0 => 'C:\\xampp\\htdocs\\2017\\ctsvn2\\cthiring\\hiring\\templates\\add_billing.tpl',
      1 => 1518689730,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:include/header.tpl' => 1,
    'file:include/footer.tpl' => 1,
  ),
),false)) {
function content_5aaa0d409af0e9_56567395 ($_smarty_tpl) {
?>

   

			<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:include/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

			
			<!-- main content -->
            <div id="contentwrapper">
               <div class="main_content">
            <div class="row-fluid">
				 <div class="span12">
				  <nav>
                        <div id="jCrumbs" class="breadCrumb module">
                            <ul>
                                <li>
                                    <a href="<?php echo @constant('webroot');?>
home"><i class="icon-home"></i></a>
                                </li>
                                <li>
                                    <a href="billing.php">Billing</a>
                                </li>
                            
                                <li>
                                   Add Billing
                                </li>
                            </ul>
                        </div>
                    </nav>
				<?php if ($_smarty_tpl->tpl_vars['EXIST_MSG']->value) {?>
				 <div id="flashMessage" class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button><?php echo $_smarty_tpl->tpl_vars['EXIST_MSG']->value;?>
</div>					
				<?php }?>
<form action="add_billing.php?res_id=<?php echo $_GET['res_id'];?>
&req_res_id=<?php echo $_GET['req_res_id'];?>
" id="formID"  enctype="multipart/form-data" name="searchFrm" class="formID" method="post" accept-charset="utf-8">
	<div class="box">
		<div class="row-fluid">
		<div class="span12">
		<div class="mbox">
			<div class="tabbable">
							
			<div class="tab-content" style="overflow:visible">			
			<div class="span6">
			<table class="table table-bordered dataTable" style="margin-bottom:0;">
						<tbody>
						<input type="hidden" value="<?php if ($_smarty_tpl->tpl_vars['resume_id']->value) {
echo $_smarty_tpl->tpl_vars['resume_id']->value;
} else {
echo $_POST['resume_id'];
}?>" name="resume_id">
	<input type="hidden" value="<?php if ($_smarty_tpl->tpl_vars['requirement_id']->value) {
echo $_smarty_tpl->tpl_vars['requirement_id']->value;
} else {
echo $_POST['requirement_id'];
}?>" name="requirement_id">
	<input type="hidden" value="<?php if ($_smarty_tpl->tpl_vars['client_id']->value) {
echo $_smarty_tpl->tpl_vars['client_id']->value;
} else {
echo $_POST['client_id'];
}?>" name="client_id">
	
									<tr class="tbl_row">
										<td width="120" class="tbl_column">Candidate Name <span class="f_req">*</span></td>
													
										<td>
											<input type="text" name="candidate_name" disabled value="<?php if ($_smarty_tpl->tpl_vars['candidate_name']->value) {
echo $_smarty_tpl->tpl_vars['candidate_name']->value;
} else {
echo $_POST['candidate_name'];
}?>" class="span8" aria-controls="dt_gal">
										<input type="hidden" name="candidate_name" value="<?php if ($_smarty_tpl->tpl_vars['candidate_name']->value) {
echo $_smarty_tpl->tpl_vars['candidate_name']->value;
} else {
echo $_POST['candidate_name'];
}?>" class="span10" aria-controls="dt_gal">
										
										</td>	
									</tr>
									
									
									<tr>
										<td width="120" class="tbl_column">Position <span class="f_req">*</span></td>
										<td>
										<input type="text" class="span8"  name="position" disabled value="<?php if ($_smarty_tpl->tpl_vars['position']->value) {
echo $_smarty_tpl->tpl_vars['position']->value;
} else {
echo $_POST['position'];
}?>">															
									<input type="hidden" name="position"  value="<?php if ($_smarty_tpl->tpl_vars['position']->value) {
echo $_smarty_tpl->tpl_vars['position']->value;
} else {
echo $_POST['position'];
}?>">															
									
									</td>
									</tr>	
									<tr class="tbl_row">
										<td width="120" class="tbl_column">Client Name <span class="f_req">*</span></td>
										<td>
										<input type="text" class="span8"  name="client_name" disabled value="<?php if ($_smarty_tpl->tpl_vars['client_name']->value) {
echo $_smarty_tpl->tpl_vars['client_name']->value;
} else {
echo $_POST['client_name'];
}?>">						
									<input type="hidden" name="client_name"  value="<?php if ($_smarty_tpl->tpl_vars['client_name']->value) {
echo $_smarty_tpl->tpl_vars['client_name']->value;
} else {
echo $_POST['client_name'];
}?>">						
									
									</td>
									</tr>		
									<tr>
									<tr>
										<td width="120" class="tbl_column">Joined Date <span class="f_req">*</span></td>
										<td> 
										<input type="text" class="span8"  name="joined_date" disabled value="<?php if ($_smarty_tpl->tpl_vars['joined_date']->value) {
echo $_smarty_tpl->tpl_vars['joined_date']->value;
} else {
echo $_POST['joined_date'];
}?>">										
										<input type="hidden" name="joined_date"  value="<?php if ($_smarty_tpl->tpl_vars['joined_date']->value) {
echo $_smarty_tpl->tpl_vars['joined_date']->value;
} else {
echo $_POST['joined_date'];
}?>">										
										
										</td>
									</tr>
										<tr>
										<td width="120" class="tbl_column">Proof of Offer <span class="f_req">*</span></td>
										<td> 
										<input type="file" tabindex="3" name="offer" class="upload" id="offer"/>
										<label class="error"><?php echo $_smarty_tpl->tpl_vars['offerErr']->value;
echo $_smarty_tpl->tpl_vars['attachmentuploadErr']->value;?>
 </label>
										</td>
									</tr>
									
									
																			
								</tbody>
							</table>
						</div>
							
						<div class="span6">		
							<table class="table table-bordered dataTable" style="margin-bottom:0;">
								<tbody>
									<tr class="tbl_row">
										<td width="120" class="tbl_column">CTC Offered <span class="f_req">*</span></td>
										<td> 
										<input type="text" class="span8"  id="ctc_offer" name="ctc_offer"  value="<?php echo $_smarty_tpl->tpl_vars['ctc_offer']->value;?>
">
										<label for="reg_city" generated="true" class="error"><?php echo $_smarty_tpl->tpl_vars['ctc_offerErr']->value;?>
</label>									
						
										</td>
									</tr>
									
									<tr>
										<td width="120" class="tbl_column">Billing % <span class="f_req"></span></td>
										<td> 
										<input type="text" class="span8"  id="bill_percent" name="bill_percent"  value="<?php if ($_smarty_tpl->tpl_vars['bill_percent']->value) {
echo $_smarty_tpl->tpl_vars['bill_percent']->value;
} else {
echo $_POST['bill_percent'];
}?>">
										<label for="reg_city" generated="true" class="error"><?php echo $_smarty_tpl->tpl_vars['bill_percentErr']->value;?>
</label>
										</td>
									</tr>
									
									<tr class="tbl_row">
										<td width="120" class="tbl_column">Billing Amount <span class="f_req">*</span></td>
										<td> 
										<input type="text" class="span8"  id="result" name="billing_amount"  value="<?php echo $_smarty_tpl->tpl_vars['billing_amount']->value;?>
">
										<label for="reg_city" generated="true" class="error"><?php echo $_smarty_tpl->tpl_vars['billing_amountErr']->value;
echo $_smarty_tpl->tpl_vars['billing_amountEr']->value;?>
</label>									
						
										</td>
									</tr>
									<tr>
										<td width="120" class="tbl_column">Billing Date <span class="f_req">*</span></td>
										<td> 
										<input type="text" name="billing_date"  value="<?php echo $_smarty_tpl->tpl_vars['billing_date']->value;?>
" class="datepick span8" id="HrEmployeeDob">									
										<label for="reg_city" generated="true" class="error"><?php echo $_smarty_tpl->tpl_vars['billing_dateErr']->value;?>
</label>									
										</td>
									</tr>
				
					<input type="hidden" id="start_date" name="start_date" value="<?php echo $_smarty_tpl->tpl_vars['noformat_joined_date']->value;?>
">

															
								</tbody>
							</table>
						</div>
 
		</div>
		</div>
		</div>
		</div>
</div>
</div>
<input type="hidden" id="billing_count" name="billing_count" value="<?php echo $_smarty_tpl->tpl_vars['billingCount']->value;?>
">
					 <div class="form-actions">
					 			<input name="submit" class="btn btn-gebo submit" value="Submit" type="submit"/>
													
								<input type="hidden" name="bill_can" value="billing.php" id="webroot">
								<input type="hidden" name="balc" value="add_billing.php?res_id=<?php echo $_GET['res_id'];?>
&req_res_id=<?php echo $_GET['req_res_id'];?>
" id="balc">
	<a href="javascript:void(0)" class="jsRedirect cancel_event">
	<input type="button" value="Cancel" class="btn cancelBtn">
	</a>
				 </div>
		</form>
               

			   </div>
            </div> 
		</div>
		</div>
		</div>
		</div>
	</div>	

<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:include/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>



<?php echo '<script'; ?>
 type="text/javascript">
$(document).ready(function(){
		/* when the form submitted */
	$('.formID').submit(function(){ 		
		// Disable the 'Next' button to prevent multiple clicks		
		$('input[type=submit]', this).attr('value', 'Processing...');		
		$('input[type=submit]', this).attr('disabled', 'disabled');
		// hide cancel button
		$('button[type=button]', this).hide();
		$('.cancelBtn').hide();
		
	});
});
	
	


$(document).on("change keyup blur", "#bill_percent", function() {
var main = $('#ctc_offer').val();
var disc = $('#bill_percent').val();
var discont = main*(disc/100).toFixed(2); //its convert 10 into 0.10
if(main > discont){
	$('#result').val(discont);
}else{
	smoke.signal("Please prove valid percentage (%)", function(e){
	}, {
		duration: 1000,
		classname: "custom-class"
	});
}
});
<?php echo '</script'; ?>
>	
<?php }
}
