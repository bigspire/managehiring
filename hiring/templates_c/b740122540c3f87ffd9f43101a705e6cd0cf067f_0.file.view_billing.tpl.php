<?php
/* Smarty version 3.1.29, created on 2017-12-22 16:49:59
  from "C:\xampp\htdocs\2017\ctsvn\cthiring\hiring\templates\view_billing.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5a3cea5fd27875_06451524',
  'file_dependency' => 
  array (
    'b740122540c3f87ffd9f43101a705e6cd0cf067f' => 
    array (
      0 => 'C:\\xampp\\htdocs\\2017\\ctsvn\\cthiring\\hiring\\templates\\view_billing.tpl',
      1 => 1513941589,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:include/header.tpl' => 1,
    'file:include/footer.tpl' => 1,
  ),
),false)) {
function content_5a3cea5fd27875_06451524 ($_smarty_tpl) {
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
                                    <a href="billing.php"> Billing</a>
                                </li>
                            
                                <li>
								<?php echo ucwords($_smarty_tpl->tpl_vars['candidate_name']->value);?>

                                </li>
                            </ul>
                        </div>
                    </nav>
							
		<div class="box">
		<div class="row-fluid">
		<div class="span12">
		<div class="mbox">
			<div class="tabbable">
								
			<div class="tab-content" style="overflow:visible">			
			
			<div class="span6">
							<table class="table table-striped table-bordered dataTable" style="">
								<tbody>
									
									<tr>
										<td width="" class="tbl_column">Client Name </td>
										<td><?php echo $_smarty_tpl->tpl_vars['client_name']->value;?>
</td>
									</tr>	
									
									<tr>
										<td width="" class="tbl_column">Position  </td>
										<td><?php echo $_smarty_tpl->tpl_vars['position']->value;?>
</td>
									</tr>
									
									<tr>
										<td width="120" class="tbl_column">Recruiter </td>
										<td><?php echo $_smarty_tpl->tpl_vars['recruiter']->value;?>
</td>
									</tr>
									<tr>
										<td width="120" class="tbl_column">Account Holder </td>
										<td><?php echo $_smarty_tpl->tpl_vars['ac_holder']->value;?>
</td>
									</tr>
									
									<tr>
										<td width="120" class="tbl_column">Billing % </td>
										<td><?php if ($_smarty_tpl->tpl_vars['bill_percent']->value > '0') {
echo $_smarty_tpl->tpl_vars['bill_percent']->value;
}?></td>
									</tr>
									
									<tr>
									<td class="tbl_column">Billing Amount</td>
									<td><?php echo $_smarty_tpl->tpl_vars['billing_amount']->value;?>
</td>
								<tr>
									<td class="tbl_column">Proof of Offer</td>
									<td>
									<a href = "view_billing.php?id=<?php echo $_GET['id'];?>
&action=download&file=<?php echo $_smarty_tpl->tpl_vars['proof_attach']->value;?>
">
									<?php echo $_smarty_tpl->tpl_vars['proof_attach']->value;?>

									</a>
									</td>
								</tr>
								</tbody>
							</table>
				</div>
							
				<div class="span6">
							<table class="table table-striped table-bordered dataTable" style="">
								<tbody>
								
								<tr>
										<td width="120" class="tbl_column">Candidate Name </td>
										<td><?php echo ucwords($_smarty_tpl->tpl_vars['candidate_name']->value);?>
 </td>
									</tr>
									
								<tr>
										<td width="" class="tbl_column">CTC Offered </td>
										<td><?php echo $_smarty_tpl->tpl_vars['ctc_offer']->value;?>
</td>
									</tr>
									
									
								<tr>
									<td class="tbl_column">Billing Date </td>
									<td><?php echo $_smarty_tpl->tpl_vars['billing_date']->value;?>
</td>
								</tr>	
									
											<tr>
										<td width="120" class="tbl_column">Joined Date </td>
										<td><?php echo $_smarty_tpl->tpl_vars['joined_date']->value;?>
</td>
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
								<a href="billing.php" class="jsRedirect"><button class="btn">Back</button></a>
						</div>
               </div>
					
          </div>
       </div>
      </div>
	</div>
</div>
</div>
			
<?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:include/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
