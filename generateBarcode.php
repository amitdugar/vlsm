<?php
ob_start();
session_start();
include('./includes/MysqliDb.php');
include ('./includes/tcpdf/tcpdf.php');
$id=base64_decode($_POST['id']);

if($id >0){
    
    //global config
    $cSampleQuery="SELECT * FROM global_config";
    $cSampleResult=$db->query($cSampleQuery);
    $arr = array();
    // now we create an associative array so that we can easily create view variables
    for ($i = 0; $i < sizeof($cSampleResult); $i++) {
      $arr[$cSampleResult[$i]['name']] = $cSampleResult[$i]['value'];
    }
    
    if (!file_exists('uploads') && !is_dir('uploads')) {
        mkdir('uploads');
    }
        
    if (!file_exists('uploads'. DIRECTORY_SEPARATOR . "barcode") && !is_dir('uploads'. DIRECTORY_SEPARATOR."barcode")) {
        mkdir('uploads'. DIRECTORY_SEPARATOR."barcode");
    }
    $lQuery="SELECT * from global_config where name='logo'";
    $lResult=$db->query($lQuery);
    
    $hQuery="SELECT * from global_config where name='header'";
    $hResult=$db->query($hQuery);

    $query="SELECT * from batch_details where batch_id=$id";
    $bResult=$db->query($query);
    
    $fQuery="SELECT vl_sample_id,sample_code from vl_request_form where batch_id=$id";
    $result=$db->query($fQuery);
    
    
    if(count($result)>0){
        // Extend the TCPDF class to create custom Header and Footer
        class MYPDF extends TCPDF {
            public function setHeading($logo,$header) {
                $this->header = $header;
                $this->logo = $logo;
            }
            //Page header
            public function Header() {
                // Logo
                //$image_file = K_PATH_IMAGES.'logo_example.jpg';
                //$this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
                // Set font
                if(trim($this->logo)!=""){
                    if (file_exists('uploads'. DIRECTORY_SEPARATOR . 'logo'. DIRECTORY_SEPARATOR.$this->logo)) {
                        $image_file = 'uploads'. DIRECTORY_SEPARATOR . 'logo'. DIRECTORY_SEPARATOR.$this->logo;
                        $this->Image($image_file,10, 10, 25, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
                    }
                }
    
                $this->SetFont('helvetica', '', 15);
                $this->header=str_replace("<div","<span",trim($this->header));
                $this->header=str_replace("</div>","</span><br/>",$this->header);
    
                $this->writeHTMLCell(0,0,35,10,$this->header, 0, 0, 0, true, 'C', true);
                $html='<hr/>';
                $this->writeHTMLCell(0, 0,10,35, $html, 0, 0, 0, true, 'J', true);
                //$this->Cell(0, 15,$this->header, 0, false, 'C', 0, '', 0, false, 'M', 'M');
            }
        
            // Page footer
            public function Footer() {
                // Position at 15 mm from bottom
                $this->SetY(-15);
                // Set font
                $this->SetFont('helvetica', 'I', 8);
                // Page number
                $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
            }
        }

        // create new PDF document
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $pdf->setHeading($lResult[0]['value'],$hResult[0]['value']);
        
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Admin');
        $pdf->SetTitle('Generate Barcode');
        $pdf->SetSubject('Barcode');
        $pdf->SetKeywords('Generate Barcode');
    
        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
    
        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    
        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }
    
        // set font
        $pdf->SetFont('helvetica', '', 10);
    
        // add a page
        $pdf->AddPage();
    
$tbl = '
<table cellspacing="0" cellpadding="3" border="1">
<thead>
    <tr nobr="true" style="background-color:#71b9e2;color:#FFFFFF;">
        <td align="center" width="8%">S.No.</td>
        <td align="center" width="27%">Sample ID</td>
        <td align="center" width="65%">Barcode</td>
    </tr>
</thead>';
    if($arr['number_of_in_house_controls'] !='' && $arr['number_of_in_house_controls']!=NULL){
        for($i=1;$i<=$arr['number_of_in_house_controls'];$i++){
            $tbl.='<tr nobr="true">
                <td align="center" width="8%" >'.$i.'.</td>
                <td align="center" width="27%" >In-House Controls '. $i.'</td>
                <td align="center" width="65%" ></td>
            </tr>';
        }
    }
    if($arr['number_of_manufacturer_controls'] !='' && $arr['number_of_manufacturer_controls']!=NULL){
        for($i=1;$i<=$arr['number_of_manufacturer_controls'];$i++){
            $sNo = $arr['number_of_in_house_controls']+$i;
            $tbl.='<tr nobr="true">
                <td align="center" width="8%" >'.$sNo.'.</td>
                <td align="center" width="27%" >Manufacturer Controls '. $i.'</td>
                <td align="center" width="65%" ></td>
            </tr>';
        }
    }
    $tbl.='</table>';
    $pdf->writeHTMLCell('', '', 12,$pdf->getY(),$tbl, 0, 1, 0, true, 'C', true);
    $sampleCounter = ($arr['number_of_manufacturer_controls']+$arr['number_of_in_house_controls']+1);

    foreach($result as $val){
        if($pdf->getY()>=250){
          $pdf->AddPage();
        }
        $params = $pdf->serializeTCPDFtagParameters(array($val['sample_code'], 'C39', '', '','' ,15, 0.25,array('border'=>false,'align' => 'C','padding'=>1, 'fgcolor'=>array(0,0,0), 'bgcolor'=>array(255,255,255), 'text'=>false, 'font'=>'helvetica', 'fontsize'=>10, 'stretchtext'=>2),'N'));
        
        $sampleCodeTable='<table cellspacing="0" cellpadding="3" border="1" style="width:100%">';
        $sampleCodeTable.='<tr>';
        $sampleCodeTable.='<td align="center" width="8%">'.$sampleCounter.'.</td>';
        $sampleCodeTable.='<td align="center" width="27%">'.$val['sample_code'].'</td>';
        $sampleCodeTable.='<td align="center" width="65%">';
        $sampleCodeTable.='<tcpdf method="write1DBarcode" params="'.$params.'" />';
        $sampleCodeTable.='</td>';
        $sampleCodeTable.='</tr>';
        $sampleCodeTable .='</table>';
                 
        $sampleCounter++;
        $pdf->writeHTMLCell('', '', 12,$pdf->getY(),$sampleCodeTable, 0, 1, 0, true, 'C', true);
    } 
    
        $filename = trim($bResult[0]['batch_code']).'.pdf';
        $pdf->Output('uploads'. DIRECTORY_SEPARATOR.'barcode'. DIRECTORY_SEPARATOR.$filename, "F");
        echo $filename;
  }
}
?>
