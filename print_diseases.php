<?php

include("./pdflib/logics-builder-pdf.php");
include './config/connection.php';

$reportTitle = "Disease Based Visits";
$from = $_GET['from'];
$to = $_GET['to'];
$disease = $_GET['disease'];

$fromArr = explode("/", $from);
$toArr = explode("/", $to);

$fromMysql = $fromArr[2].'-'.$fromArr[0].'-'.$fromArr[1];
$toMysql = $toArr[2].'-'.$toArr[0].'-'.$toArr[1];

$pdf = new LB_PDF('L', false, $reportTitle, $from, $to);
$pdf->SetMargins(15, 10);
$pdf->AliasNbPages();
$pdf->AddPage();

$titlesArr = array('S.No', 'Visit Date', 'Patient Name', 
'Address', 'Contact#', 'Disease');

$pdf->SetWidths(array(15, 25, 50, 70, 30, 70));
$pdf->SetAligns(array('L', 'L', 'L', 'L', 'L', 'L'));
// $pdf->Ln();
// $pdf->Ln();
 $pdf->Ln(15);
$diseaseCaption = strtoupper($disease);
$pdf->AddTableCaption($diseaseCaption);

$pdf->AddTableHeader($titlesArr);

$query = "SELECT `p`.`patient_name`, `p`.`address`, 
`p`.`phone_number`, `pv`.`visit_date`, `pv`.`disease` 
from `patients` as `p`, `patient_visits` as `pv` 
where `pv`.`visit_date` between '$fromMysql' and '$toMysql' and 
    `pv`.`patient_id` = `p`.`id` and 
    `pv`.`disease` like('%$disease%') 
    order by `pv`.`visit_date` asc;";
$stmt = $con->prepare($query);
$stmt->execute();

$i = 0;
while($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$i++;

	$data = array($i, 
		$r['visit_date'],
		$r['patient_name'],
		$r['address'],
		$r['phone_number'],
		$r['disease']
	);
	
	$pdf->AddRow($data);

}
$pdf->Output('print_patient_diseases.pdf', 'I');
?>