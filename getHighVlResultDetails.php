<?php
session_start();
include('./includes/MysqliDb.php');
include('General.php');
$general=new Deforay_Commons_General();
$tableName="vl_request_form";
$primaryKey="treament_id";

        /* Array of database columns which should be read and sent back to DataTables. Use a space where
         * you want to insert a non-database field (for example a counter or static image)
        */
        
        $aColumns = array('vl.sample_code',"DATE_FORMAT(vl.sample_collection_date,'%d-%b-%Y')",'f.facility_name','f.phone_number','vl.art_no','vl.patient_name','vl.patient_phone_number','vl.absolute_value','cn.contact_notes','vl.contact_complete_status');
        $orderColumns = array('vl.sample_code','vl.sample_collection_date','f.facility_name','f.phone_number','vl.art_no','vl.patient_name','vl.patient_phone_number','vl.absolute_value','cn.contact_notes','vl.contact_complete_status');
        
        /* Indexed column (used for fast and accurate table cardinality) */
        $sIndexColumn = $primaryKey;
        
        $sTable = $tableName;
        /*
         * Paging
         */
        $sLimit = "";
        if (isset($_POST['iDisplayStart']) && $_POST['iDisplayLength'] != '-1') {
            $sOffset = $_POST['iDisplayStart'];
            $sLimit = $_POST['iDisplayLength'];
        }
        
        /*
         * Ordering
        */
        
        $sOrder = "";
        if (isset($_POST['iSortCol_0'])) {
            $sOrder = "";
            for ($i = 0; $i < intval($_POST['iSortingCols']); $i++) {
                if ($_POST['bSortable_' . intval($_POST['iSortCol_' . $i])] == "true") {
                    $sOrder .= $orderColumns[intval($_POST['iSortCol_' . $i])] . "
				 	" . ( $_POST['sSortDir_' . $i] ) . ", ";
                }
            }
            $sOrder = substr_replace($sOrder, "", -2);
        }
        
        /*
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
        */
        
        $sWhere = "";
        if (isset($_POST['sSearch']) && $_POST['sSearch'] != "") {
			$sWhere = " AND ";
            $searchArray = explode(" ", $_POST['sSearch']);
            $sWhereSub = "";
            foreach ($searchArray as $search) {
                if ($sWhereSub == "") {
                    $sWhereSub .= "(";
                } else {
                    $sWhereSub .= " AND (";
                }
                $colSize = count($aColumns);
                
                for ($i = 0; $i < $colSize; $i++) {
                    if ($i < $colSize - 1) {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' OR ";
                    } else {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' ";
                    }
                }
                $sWhereSub .= ")";
            }
            $sWhere .= $sWhereSub;
        }
        
        /* Individual column filtering */
        for ($i = 0; $i < count($aColumns); $i++) {
            if (isset($_POST['bSearchable_' . $i]) && $_POST['bSearchable_' . $i] == "true" && $_POST['sSearch_' . $i] != '') {
                if ($sWhere == "") {
                    $sWhere .= $aColumns[$i] . " LIKE '%" . ($_POST['sSearch_' . $i]) . "%' ";
                } else {
                    $sWhere .= " AND " . $aColumns[$i] . " LIKE '%" . ($_POST['sSearch_' . $i]) . "%' ";
                }
            }
        }
        
        /*
         * SQL queries
         * Get data to display
        */
	$aWhere = '';
	$sQuery="SELECT * FROM vl_request_form as vl INNER JOIN facility_details as f ON vl.facility_id=f.facility_id INNER JOIN r_sample_type as s ON s.sample_id=vl.sample_id LEFT JOIN r_art_code_details as art ON vl.current_regimen=art.art_id LEFT JOIN batch_details as b ON b.batch_id=vl.batch_id LEFT JOIN contact_notes_details as cn ON cn.treament_contact_id=vl.treament_id where vl.status=7 AND vl.absolute_decimal_value > 1000";
	//$sWhere = ' where vl.status=7 AND vl.absolute_decimal_value > 1000';
	$start_date = '';
	$end_date = '';
	if(isset($_POST['sampleCollectionDate']) && trim($_POST['sampleCollectionDate'])!= ''){
	   $s_c_date = explode("to", $_POST['sampleCollectionDate']);
	   //print_r($s_c_date);die;
	   if (isset($s_c_date[0]) && trim($s_c_date[0]) != "") {
	     $start_date = $general->dateFormat(trim($s_c_date[0]));
	   }
	   if (isset($s_c_date[1]) && trim($s_c_date[1]) != "") {
	     $end_date = $general->dateFormat(trim($s_c_date[1]));
	   }
	}
	
	if(isset($_POST['batchCode']) && trim($_POST['batchCode'])!= ''){
	    $sWhere = $sWhere.' AND b.batch_code LIKE "%'.$_POST['batchCode'].'%"';
	}
	if(isset($_POST['sampleCollectionDate']) && trim($_POST['sampleCollectionDate'])!= ''){
	    if (trim($start_date) == trim($end_date)) {
		$sWhere = $sWhere.' AND DATE(vl.sample_collection_date) = "'.$start_date.'"';
	    }else{
	       $sWhere = $sWhere.' AND DATE(vl.sample_collection_date) >= "'.$start_date.'" AND DATE(vl.sample_collection_date) <= "'.$end_date.'"';
	    }
        }
        if(isset($_POST['sampleType']) && $_POST['sampleType']!=''){
	  $sWhere = $sWhere.' AND s.sample_id = "'.$_POST['sampleType'].'"';
        }
        if(isset($_POST['facilityName']) && $_POST['facilityName']!=''){
	  $sWhere = $sWhere.' AND f.facility_id = "'.$_POST['facilityName'].'"';
        }
       
	$sQuery = $sQuery.' '.$sWhere;
        $sQuery = $sQuery.' group by vl.treament_id';
        if (isset($sOrder) && $sOrder != "") {
            $sOrder = preg_replace('/(\v|\s)+/', ' ', $sOrder);
            $sQuery = $sQuery.' order by '.$sOrder;
        }
        
        if (isset($sLimit) && isset($sOffset)) {
            $sQuery = $sQuery.' LIMIT '.$sOffset.','. $sLimit;
        }
        
        //echo $sQuery;die;
        $rResult = $db->rawQuery($sQuery);
       // print_r($rResult);
        /* Data set length after filtering */
        
        $aResultFilterTotal =$db->rawQuery("SELECT * FROM vl_request_form as vl INNER JOIN facility_details as f ON vl.facility_id=f.facility_id INNER JOIN r_sample_type as s ON s.sample_id=vl.sample_id LEFT JOIN r_art_code_details as art ON vl.current_regimen=art.art_id LEFT JOIN batch_details as b ON b.batch_id=vl.batch_id LEFT JOIN contact_notes_details as cn ON cn.treament_contact_id=vl.treament_id where vl.status=7 AND vl.absolute_decimal_value > 1000 $sWhere group by vl.treament_id order by $sOrder");
        $iFilteredTotal = count($aResultFilterTotal);

        /* Total data set length */
        $aResultTotal =  $db->rawQuery("select COUNT(treament_id) as total FROM vl_request_form where status=7 AND absolute_decimal_value > 1000");
       // $aResultTotal = $countResult->fetch_row();
       //print_r($aResultTotal);
        $iTotal = $aResultTotal[0]['total'];

        /*
         * Output
        */
        $output = array(
            "sEcho" => intval($_POST['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );
	$vlNotes = false;
	if(isset($_SESSION['privileges']) && (in_array("addContactNotes.php", $_SESSION['privileges']))){
	    $vlNotes = true;
	}
        
        foreach ($rResult as $aRow) {
	    $cNoteQuery = "select contact_notes from contact_notes_details where treament_contact_id='".$aRow['treament_id']."' order by added_on DESC LIMIT 1";
	    $cnResult = $db->rawQuery($cNoteQuery);
	    if($cnResult){
		$aRow['contact_notes'] = $cnResult[0]['contact_notes'];
	    }else{
		$aRow['contact_notes'] = '';
	    }
	    if(isset($aRow['sample_collection_date']) && trim($aRow['sample_collection_date'])!= '' && $aRow['sample_collection_date']!= '0000-00-00 00:00:00'){
		$xplodDate = explode(" ",$aRow['sample_collection_date']);
		$aRow['sample_collection_date'] = $general->humanDateFormat($xplodDate[0]);
	    }else{
		$aRow['sample_collection_date'] = '';
	    }
            $row = array();
	    $row[] = $aRow['sample_code'];
	    $row[] = $aRow['sample_collection_date'];
	    $row[] = ucwords($aRow['facility_name']);
	    $row[] = $aRow['phone_number'];
            $row[] = $aRow['art_no'];
            $row[] = ucwords($aRow['patient_name']).' '.ucwords($aRow['surname']);
            $row[] = ucwords($aRow['patient_phone_number']);
            $row[] = ucwords($aRow['absolute_value']);
            $row[] = ucwords($aRow['contact_notes']);
            $row[] = '<select class="form-control" name="status" id=' . $aRow['treament_id'] . ' title="Please select status" onchange="updateStatus(this.id,this.value)">
			    <option value=""> -- Select -- </option>
			    <option value="yes" ' . ($aRow['contact_complete_status'] == "yes" ? "selected=selected" : "") . '>Yes</option>
			    <option value="no" ' . ($aRow['contact_complete_status'] == "no" ? "selected=selected" : "") . '>No</option>
		    </select>';
	   if($vlNotes){
            //$row[] = '<a href="addContactNotes.php?id=' . base64_encode($aRow['treament_id']) . '" class="btn btn-primary btn-xs" style="margin-right: 2px;" title="View"><i class="fa fa-file"> Add Contact Notes</i></a>';
            $row[] = '<a href="javascript:void(0)" class="btn btn-primary btn-xs" style="margin-right: 2px;" title="Add" onclick="showModal(\'addContactNotes.php?id=' . base64_encode($aRow['treament_id']) . '\',900,520)"><i class="fa fa-file"> Add Contact Notes</i></a>';
           }
            $output['aaData'][] = $row;
        }
        
        echo json_encode($output);
?>