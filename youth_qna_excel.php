<?php




error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
ini_set('memory_limit','-1');
ini_set("max_execution_time","0");
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
date_default_timezone_set('Asia/Seoul');

// 메모리제한 제거
// ini_set('memory_limit', -1);

header("Content-Type: text/html; charset=utf-8");
header("Content-Encoding: utf-8");

require_once 'PHPExcel.php';
require_once 'PHPExcel/IOFactory.php';
require_once dirname(__FILE__).'/inc/db_conn.php';
$objPHPExcel = new PHPExcel();

// Set properties
$objPHPExcel->getProperties()
->setCreator("")
->setLastModifiedBy("")
->setTitle("")
->setSubject("")
->setDescription("")
->setKeywords("")
->setCategory("License");

$objPHPExcel->setActiveSheetIndex(0)
->setCellValue("A1", "아이디")
->setCellValue("B1", "이름")
->setCellValue("C1", "휴대폰")
->setCellValue("D1", "카테고리")
->setCellValue("E1", "맞춘갯수")
->setCellValue("F1", "생성날짜");

// 쿼리 생성, 실행
$sql = 'SELECT id, name, phone, c_id, score, created_at FROM youth_event';
$db = new Sql($sql);
$db->getQuery();
$events =$db->getArray();

// $events = array(
// 	array(
// 		"name"=>"hhh",
// 		"gender"=>"man"
// 	),
// 	array(
// 		"name"=>"hhh",
// 		"gender"=>"man"
// 	),
// 	array(
// 		"name"=>"hhh",
// 		"gender"=>"man"
// ));

// $i = 2;
// while ($events){
// 	if($events['type'] == 'FB'){
// 		$type = '페이스북';
// 	}else{
// 		$type = '일반';
// 	}
	
// 	// Add some data
// 	$objPHPExcel->setActiveSheetIndex(0)
// 	->setCellValue("A$i", $events['id'])
// 	->setCellValue("B$i", $events['name'])
// 	->setCellValueExplicit("C$i", $events['phone'], PHPExcel_Cell_DataType::TYPE_STRING)
// 	->setCellValueExplicit("D$i", $events['c_id'])
// 	->setCellValueExplicit("E$i", $events['score'], PHPExcel_Cell_DataType::TYPE_STRING)
// 	->setCellValue("F$i", $events['created_at']);
// 	$i++;
// };

$objPHPExcel->getActiveSheet()->fromArray($events, NULL, 'A2');
$objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);

// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
$filename = iconv("UTF-8", "EUC-KR", "youthqna");

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
?>
<div>aaa</div>