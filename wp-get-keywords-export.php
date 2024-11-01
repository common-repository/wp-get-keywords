<?php
require_once((dirname(__FILE__)) . '/wp-get-keywords-config.php');

$exportexcel = '';
if($_POST["buttonFlag"]=='WP_GET_OUT')
{
	$url = $_POST['WP_GET_URL'];
	if($url==""){
	$sql = "select * from `".WP_GET_KEYWORD_TABLENAME."` order by date desc";}else
	{
$sql = "select * from `".WP_GET_KEYWORD_TABLENAME."` where `url` like '%".$url."' order by date desc";
	}
	$results = $wpdb->get_results($sql, ARRAY_A);
	wp_get_keywords_Export2Excel($results);
}


/* 导出操作 */
function wp_get_keywords_Export2Excel($results)
{
	/* 数据库表列显示字段，以及保存时候要保存的字段 */
	$export_cols = array("keyword"=>"关键词","url"=>"链接","se"=>"搜索引擎","date"=>"时间","ip"=>"IP","beforeurl"=>"来源URL");
	
	$colcount = count($export_cols);
	$path = (dirname(__FILE__));
	$filename = "/export/export-baoming.xls";
	$xml = "<?xml version=\"1.0\"?>
<?mso-application progid=\"Excel.Sheet\"?>
<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\"
 xmlns:o=\"urn:schemas-microsoft-com:office:office\"
 xmlns:x=\"urn:schemas-microsoft-com:office:excel\"
 xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\"
 xmlns:html=\"http://www.w3.org/TR/REC-html40\">
 <DocumentProperties xmlns=\"urn:schemas-microsoft-com:office:office\">
  <Author>lezou</Author>
  <LastAuthor>lezou</LastAuthor>
  <Created>2012-08-04T16:41:21Z</Created>
  <Version>12.00</Version>
 </DocumentProperties>
 <ExcelWorkbook xmlns=\"urn:schemas-microsoft-com:office:excel\">
  <WindowHeight>10215</WindowHeight>
  <WindowWidth>21195</WindowWidth>
  <WindowTopX>240</WindowTopX>
  <WindowTopY>285</WindowTopY>
  <ProtectStructure>False</ProtectStructure>
  <ProtectWindows>False</ProtectWindows>
 </ExcelWorkbook>
 <Styles>
  <Style ss:ID=\"Default\" ss:Name=\"Normal\">
   <Alignment ss:Vertical=\"Center\"/>
   <Borders/>
   <Font ss:FontName=\"宋体\" x:CharSet=\"134\" ss:Size=\"12\"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
 </Styles>
 <Worksheet ss:Name=\"export\">
  <Table ss:ExpandedColumnCount=\"8\" ss:ExpandedRowCount=\"". (count($results)+1) ."\" x:FullColumns=\"1\"
   x:FullRows=\"1\" ss:DefaultColumnWidth=\"99.75\" ss:DefaultRowHeight=\"14.25\">";
   $xml .= "<Column ss:AutoFitWidth=\"0\" ss:Width=\"45.75\"/>";
   $xml .= "<Column ss:AutoFitWidth=\"0\" ss:Width=\"45.75\"/>";
   $xml .= "<Column ss:AutoFitWidth=\"0\" ss:Width=\"45.75\"/>";
   $xml .= "<Column ss:AutoFitWidth=\"0\" ss:Width=\"45.75\"/>";
   $xml .= "<Column ss:AutoFitWidth=\"0\" ss:Width=\"45.75\"/>";
   $xml .= "<Column ss:AutoFitWidth=\"0\" ss:Width=\"45.75\"/>";
   $xml .= "<Column ss:AutoFitWidth=\"0\" ss:Width=\"45.75\"/>";
   $xml .= "<Column ss:AutoFitWidth=\"0\" ss:Width=\"45.75\"/>";
   
   $xml .= "<Row ss:AutoFitHeight=\"0\">";
   	   $xml .= "<Cell><Data ss:Type=\"String\">序号</Data></Cell>";
	foreach ($export_cols as $col_name => $col_title) { 
   	   $xml .= "<Cell><Data ss:Type=\"String\">".$col_title."</Data></Cell>";
	}
	$xml .= "</Row>";
	
	$i=1;
	foreach ($results as $row)
	{
		$xml .= "<Row ss:AutoFitHeight=\"0\"><Cell><Data ss:Type=\"String\">".$i++."</Data></Cell>";
		
		foreach ($export_cols as $col_name => $col_title) { 
			$xml .= "<Cell><Data ss:Type=\"String\">".$row[$col_name]."</Data></Cell>";
		}
		$xml .= "</Row>";
	}
	$xml .= "  </Table>
			  <WorksheetOptions xmlns=\"urn:schemas-microsoft-com:office:excel\">
			   <Unsynced/>
			   <Selected/>
			   <Panes>
				<Pane>
				 <Number>3</Number>
				 <ActiveRow>1</ActiveRow>
				 <ActiveCol>1</ActiveCol>
				</Pane>
			   </Panes>
			   <ProtectObjects>False</ProtectObjects>
			   <ProtectScenarios>False</ProtectScenarios>
			  </WorksheetOptions>
			 </Worksheet>
			</Workbook>";
	header("Content-Type: application/vnd.ms-excel; charset=UTF-8");   
	header("Content-Disposition:attachment; filename=关键词.xls"); 
	echo $xml;
	exit();
}
?>