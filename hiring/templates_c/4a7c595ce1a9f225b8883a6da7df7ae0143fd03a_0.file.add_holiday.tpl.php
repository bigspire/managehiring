<?php
/* Smarty version 3.1.29, created on 2018-06-27 13:52:11
  from "C:\xampp\htdocs\2017\ctsvn2\managehiring\hiring\templates\add_holiday.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_5b334933cf44a3_72469138',
  'file_dependency' => 
  array (
    '4a7c595ce1a9f225b8883a6da7df7ae0143fd03a' => 
    array (
      0 => 'C:\\xampp\\htdocs\\2017\\ctsvn2\\managehiring\\hiring\\templates\\add_holiday.tpl',
      1 => 1530087729,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b334933cf44a3_72469138 ($_smarty_tpl) {
?>


<!DOCTYPE html>
<html>
<head>
	
         <!-- main styles -->
         <link rel="stylesheet" href="css/style.css" />
		 <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=PT+Sans" />
		 
	   <!-- Bootstrap framework -->
         <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
         <link rel="stylesheet" href="bootstrap/css/bootstrap-responsive.min.css" />
       <!-- gebo blue theme-->
         <link rel="stylesheet" href="css/blue.css" id="link_theme" />            
         <!-- main styles -->
         <link rel="stylesheet" href="css/style.css" />
        
	
</head>
<body  class="menu_hover " >
	<div id="container">
		<div id="content">
			<div id="maincontainer" class="clearfix">
			
			<!-- main content -->
            <div id="contentwrapper">
               <div class="main_content" style="min-height:auto;">
            <div class="row-fluid">
				 <div class="span12">
				 
				 
				 
		<?php if ($_smarty_tpl->tpl_vars['form_sent']->value == '1') {?>						
		<div  class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert-success">×</button>Excel Imported Successfully</div>
		Redirecting now...
		<?php }?>	
		
		
<?php if ($_smarty_tpl->tpl_vars['form_sent']->value != '1') {?>		 		
<form action="add_holiday.php" class="formID" id="formID" method="post" enctype="multipart/form-data" accept-charset="utf-8">
<div class="box">
	<div class="row-fluid">
		<div class="span6">
			<table class="table table-bordered dataTable" align="centre">
				<tbody> 					
				<tr class="tbl_row" >
						<td width="120" class="tbl_column">Upload Excel <span class="f_req">*</span></td>
						<td>
						<input type="file" tabindex="3" name="event_excel" class="upload" id="event_excel"/>
						<label class="error"><?php echo $_smarty_tpl->tpl_vars['event_excelErr']->value;
echo $_smarty_tpl->tpl_vars['attachmentuploadErr']->value;?>
 </label>
						<a href = "add_holiday.php?action=download">Download Excel Template</a>
						</td>	
					</tr>
				</tbody>
		   </table>
							
			<div class="form-actions">
				<input name="submit" class="btn btn-gebo theForm" value="Save" type="submit"/>
				<input type="button" value="Cancel" class="cancelBtn btn cancel">
			</div>
		</div>
	</div>
</div>

</form>
<?php }?>
  </div>
  </div>
 </div> 
</div>
</div>
</div>
</div>


	 
<?php echo '<script'; ?>
 src="js/jquery.min.js"><?php echo '</script'; ?>
>		
<!-- main bootstrap js -->
<?php echo '<script'; ?>
 src="bootstrap/js/bootstrap.min.js"><?php echo '</script'; ?>
>			
<?php echo '<script'; ?>
 src="lib_cthiring/jquery-ui/jquery-ui-1.8.20.custom.min.js"><?php echo '</script'; ?>
>
<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['page_redirect']->value;?>
" class="redirect_url"/>		
<!-- main bootstrap js -->
		 



<?php echo '<script'; ?>
 type="text/javascript">
$(".cancel").click(function(){
	parent.$.colorbox.close();
});
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
<?php echo '</script'; ?>
>	


<?php if ($_smarty_tpl->tpl_vars['form_sent']->value == '1') {?>

<?php echo '<script'; ?>
 type="text/javascript">

/* redirect to list page successfully */
self.parent.location.href = jQuery('.redirect_url').val();
parent.jQuery(".modalCloseImg").click();
parent.$.colorbox.close();
<?php echo '</script'; ?>
>

<?php }?>
</body>
</html><?php }
}
