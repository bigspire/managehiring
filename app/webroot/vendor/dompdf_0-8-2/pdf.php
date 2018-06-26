<?php
require_once 'dompdf/lib/html5lib/Parser.php';
require_once 'dompdf/lib/php-font-lib/src/FontLib/Autoloader.php';
require_once 'dompdf/lib/php-svg-lib/src/autoload.php';
require_once 'dompdf/src/Autoloader.php';

/* function to print the value */
function print_value_zero($val){
	return $val ? $val : '0';
}
	
Dompdf\Autoloader::register();
// reference the Dompdf namespace
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();


								$j = 0;
							foreach($client_detail as $id => $client){ 
									
								$tbody .= "<tr><td width='150'>".$client."</td>";
								
								$tbody .=	"<td  style='width:40px;text-align:center'>".print_value_zero($opening_worked[$j][0][0]['no_job'])."</td>";
								$tbody .=	"<td   style='width:40px;text-align:center''>".print_value_zero($cv_sent_count[$j])."</td>";
								$tbody .=	"<td   style='width:40px;text-align:center''>".print_value_zero($cv_shortlist_count[$j])."</td>";
								$tbody .=	"<td   style='width:40px;text-align:center''>".print_value_zero($cv_reject_count[$j])."</td>";
								$tbody .=	"<td   style='width:40px;text-align:center''>".print_value_zero($feedback_await_count[$j])."<</td>";
								$tbody .=	"<td   style='width:40px;text-align:center''>".print_value_zero($interview_await_count[$j])."</td>";
								$tbody .=	"<td   style='width:40px;text-align:center''>".print_value_zero($prili_interview_count[$j])."</td>";
								$tbody .=	"<td   style='width:40px;text-align:center''>".print_value_zero($final_interview_count[$j])."</td>";
								$tbody .=	"<td   style='width:40px;text-align:center''>".print_value_zero($offer_pending_count[$j])."</td>";
								$tbody .=	"<td   style='width:40px;text-align:center''>".print_value_zero($offer_accept_count[$j])."</td>";
								$tbody .=	"<td   style='width:40px;text-align:center''>".print_value_zero($offer_reject_count[$j])."</td>";
								$tbody .=	"<td   style='width:40px;text-align:center''>".print_value_zero($join_pending_count[$j])."</td>";
								$tbody .=	"<td   style='width:40px;text-align:center''>".print_value_zero($join_accept_count[$j])."</td>";
								$tbody .=	"<td   style='width:40px;text-align:center''>".print_value_zero($join_reject_count[$j])."</td>";
								$tbody .=	"<td   style='width:40px;text-align:center''>".print_value_zero($join_defer_count[$j])."</td>";
								$tbody .=	"<td   style='width:40px;text-align:center''>".print_value_zero($not_billed_count[$j])."</td>";
								$tbody .=	"<td   style='width:40px;text-align:center''>".print_value_zero($billed_count[$j])."</td>";
								$tbody .= "</tr>";
								$j++;
						}

$str = <<<EOD
					
<table class="table table-hover table-bordered table-striped printAreaTable" border="1" cellpadding="4" cellspacing="3"  style="border-collapse:collapse;margin: 15px 0px;">
								<thead>
									
									<tr>

										<th width="" style="min-width: 0px; max-width: none;"></th>										
										<th width="" style="text-align:left;min-width: 0px; max-width: none;padding:10px; font-size:15px;" colspan="18">
										Client Wise CV Status										

										</th>
										
									</tr>
								
									<tr>

										<th width="150" style="min-width: 0px; max-width: none;"><a href="#">Client</a></th>										
										
										
										<th  style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Openings Worked" href="#">OW</a></th>
										<th  style="min-width: 0px; max-width: none;text-align:center" ><a rel="tooltip" title="CV Sent"href="#">Sent</a></th>
										<th  style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="CV Shortlisted" href="#">CVS</a> </th>
										<th  style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="CV Rejected" href="#">CVR</a> </th>
										
										<th  style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="CV Feedback Awaited" href="#">FA</a></th>
										<th  style="min-width: 0px; max-width: none;text-align:center" ><a rel="tooltip" title="Interview Schedule Awaited" href="#">ISA</a></th>
										<th  style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Preliminary Interviews Attended" href="#">PIA</a> </th>
										<th  style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Final Interview Attended" href="#">FIA</a> </th>
										<th  style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Offer Pending" href="#">OP</a></th>
										<th  style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Offer Accepted" href="#">OA</a> </th>
										
										<th  style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Offer Rejected" href="#">OR</a> </th>
										<th  style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Joining Awaited" href="#">JA</a> </th>
										<th  style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Candidate Joined" href="#">J</a> </th>
										<th  style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Not Joined" href="#">NJ</a></th>
										<th  style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Joining Deferred" href="#">JD</a> </th>
										<th  style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Billing Pending" href="#">BP</a> </th>
										<th  style="min-width: 0px; max-width: none;text-align:center"><a rel="tooltip" title="Candidate Billed" href="#">B</a> </th>
									
									</tr>
								
								
								</thead>
								
{$tbody}

								<tbody>	
								
							</tbody>
							
							</table>
							

							<table>
							<tr>
							<td>
							OW - Openings Worked
							</td>
							<td>
							 Sent - CV Sent
							</td>
							<td>
							CVS - CV Shortlisted	
							</td>
							<td>
							CVR -  CV Rejected
							</td>
							
							<td>
							FA - CV Feedback Awaited
							</td>
							
							
							
							</tr>
							
							<tr>
							
							<td>
							ISA  - Interview Schedule Awaited
							</td>
							<td>
							PIA -  Preli. Interviews Attended	
							</td>
							<td>
						FIA - Final Interview Attended
							</td
							
							
							<td>
							 OP -  Offer Pending
							</td>
							<td>
							OA -  Offer Accepted
							</td>
							
							
							
							</tr>
							<tr>
							
							<td>
							OR - Offer Rejected	
							</td>
							<td>
						JA -  Joining Awaited
							</td>
							
							<td>
							J -  Candidate Joined
							</td>
							<td>
							 NJ -  Not Joined
							</td>
							<td>
							JD -  Joining Deferred	
							</td>
							</tr>
							
							<tr>
							<td>
						BP - Billing Pending
							</td
							
							
							<td>
							B - Candidate Billed 
							</td>
							<td>
							
							</td>
							<td>
						
							</td>
							<td>
						
							</td>
							
							
							
						
							
							</tr>
							</table>
							

	
EOD;
	
$dompdf->loadHtml($str);


// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// $dompdf->stream("dompdf_out.pdf", array("Attachment" => true));

// Output the generated PDF to Browser
$dompdf->stream($pdf_file.'.pdf');

?>