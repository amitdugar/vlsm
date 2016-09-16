<?php
ob_start();
session_start();
include('./includes/MysqliDb.php');
include('General.php');
$general=new Deforay_Commons_General();
$tableName="temp_sample_report";
$tableName1="vl_request_form";
$tableName2="hold_sample_report";
try {
    $result ='';
    $id= explode(",",$_POST['value']);
    $status= explode(",",$_POST['status']);
    if($_POST['value']!=''){
    for($i=0;$i<count($id);$i++){
            $sQuery="SELECT * FROM temp_sample_report where temp_sample_id='".$id[$i]."'";
            $rResult = $db->rawQuery($sQuery);
            $data=array(
                        'lab_name'=>$rResult[0]['lab_name'],
                        'lab_contact_person'=>$rResult[0]['lab_contact_person'],
                        'lab_phone_no'=>$rResult[0]['lab_phone_no'],
                        'date_sample_received_at_testing_lab'=>$rResult[0]['date_sample_received_at_testing_lab'],
                        'lab_tested_date'=>$rResult[0]['lab_tested_date'],
                        'date_results_dispatched'=>$rResult[0]['date_results_dispatched'],
                        'result_reviewed_date'=>$rResult[0]['result_reviewed_date'],
                        'result_reviewed_by'=>$rResult[0]['result_reviewed_by'],
                        'comments'=>$_POST['comments'],
                        'log_value'=>$rResult[0]['log_value'],
                        'absolute_value'=>$rResult[0]['absolute_value'],
                        'text_value'=>$rResult[0]['text_value'],
                        'absolute_decimal_value'=>$rResult[0]['absolute_decimal_value'],
                        'result'=>$rResult[0]['result'],
                        'lab_tested_date'=>$rResult[0]['lab_tested_date'],
                        'lab_id'=>$rResult[0]['lab_id'],
                        'file_name'=>$rResult[0]['file_name'],
                    );
            if($status[$i]=='1'){
                $data['result_reviewed_by']=$rResult[0]['result_reviewed_by'];
               $data['facility_id']=$rResult[0]['facility_id'];
               $data['sample_code']=$rResult[0]['sample_code'];
               $data['batch_code']=$rResult[0]['batch_code'];
               $data['status']=$status[$i];
               $data['import_batch_tracking']=$_SESSION['controllertrack'];
               $result = $db->insert($tableName2,$data);
            }else{
            $data['created_by']=$rResult[0]['result_reviewed_by'];
            $data['created_on']=$general->getDateTime();
            $data['modified_on']=$general->getDateTime();
            $data['result_approved_by']=$_SESSION['userId'];
            $data['result_approved_on']=$general->getDateTime();
            $sampleVal = $rResult[0]['sample_code'];
            //get bacth code
            $bquery="select * from batch_details where batch_code='".$rResult[0]['batch_code']."'";
            $bvlResult=$db->rawQuery($bquery);
            if($bvlResult){
                $data['batch_id'] = $bvlResult[0]['batch_id'];
            }else{
                $batchResult = $db->insert('batch_details',array('batch_code'=>$rResult[0]['batch_code'],'batch_code_key'=>$rResult[0]['batch_code_key'],'sent_mail'=>'no','created_on'=>$general->getDateTime()));
                $data['batch_id'] = $db->getInsertId();
            }
            $query="select treament_id,result from vl_request_form where sample_code='".$sampleVal."'";
            $vlResult=$db->rawQuery($query);
            $data['status']=$_POST['status'];
            if(count($vlResult)>0){
                $db=$db->where('sample_code',$rResult[0]['sample_code']);
                $result=$db->update($tableName1,$data);
            }else{
                $data['sample_code']=$rResult[0]['sample_code'];
                $db->insert($tableName1,$data);
            }
            }
            $db=$db->where('temp_sample_id',$id[$i]);
            $result=$db->delete($tableName);
    }
        if (!file_exists('uploads'. DIRECTORY_SEPARATOR . "import-result". DIRECTORY_SEPARATOR . $rResult[0]['file_name'])) {
            copy('temporary'. DIRECTORY_SEPARATOR ."import-result". DIRECTORY_SEPARATOR.$rResult[0]['file_name'], 'uploads'. DIRECTORY_SEPARATOR ."import-result" . DIRECTORY_SEPARATOR . $rResult[0]['file_name']);
        }
    }
    //get all accepted data result
    $accQuery="SELECT * FROM temp_sample_report where status='7'";
    $accResult = $db->rawQuery($accQuery);
    if($accResult){
    for($i = 0;$i<count($accResult);$i++){
        $data=array(
                        'lab_name'=>$accResult[$i]['lab_name'],
                        'lab_contact_person'=>$accResult[$i]['lab_contact_person'],
                        'lab_phone_no'=>$accResult[$i]['lab_phone_no'],
                        'date_sample_received_at_testing_lab'=>$accResult[$i]['date_sample_received_at_testing_lab'],
                        'lab_tested_date'=>$accResult[$i]['lab_tested_date'],
                        'date_results_dispatched'=>$accResult[$i]['date_results_dispatched'],
                        'result_reviewed_date'=>$accResult[$i]['result_reviewed_date'],
                        'result_reviewed_by'=>$accResult[$i]['result_reviewed_by'],
                        'comments'=>$_POST['comments'],
                        'log_value'=>$accResult[$i]['log_value'],
                        'absolute_value'=>$accResult[$i]['absolute_value'],
                        'text_value'=>$accResult[$i]['text_value'],
                        'absolute_decimal_value'=>$accResult[$i]['absolute_decimal_value'],
                        'result'=>$accResult[$i]['result'],
                        'lab_tested_date'=>$accResult[$i]['lab_tested_date'],
                        'lab_id'=>$accResult[$i]['lab_id'],
                        'created_by'=>$accResult[$i]['result_reviewed_by'],
                        'created_on'=>$general->getDateTime(),
                        'modified_on'=>$general->getDateTime(),
                        'result_approved_by'=>$_SESSION['userId'],
                        'result_approved_on'=>$general->getDateTime(),
                        'file_name'=>$accResult[$i]['file_name'],
                        'status'=>'7'
                    );
            //get bacth code
                $bquery="select * from batch_details where batch_code='".$accResult[$i]['batch_code']."'";
                $bvlResult=$db->rawQuery($bquery);
                if($bvlResult){
                    $data['batch_id'] = $bvlResult[0]['batch_id'];
                }else{
                    $batchResult = $db->insert('batch_details',array('batch_code'=>$accResult[$i]['batch_code'],'batch_code_key'=>$accResult[$i]['batch_code_key'],'sent_mail'=>'no','created_on'=>$general->getDateTime()));
                    $data['batch_id'] = $db->getInsertId();
                }
                $db=$db->where('sample_code',$accResult[$i]['sample_code']);
                $result=$db->update($tableName1,$data);
                if (!file_exists('uploads'. DIRECTORY_SEPARATOR . "import-result". DIRECTORY_SEPARATOR . $accResult[$i]['file_name'])) {
                    copy('temporary'. DIRECTORY_SEPARATOR ."import-result" . DIRECTORY_SEPARATOR . $accResult[$i]['file_name'], 'uploads'. DIRECTORY_SEPARATOR ."import-result" . DIRECTORY_SEPARATOR . $accResult[$i]['file_name']);
                }
                $db=$db->where('temp_sample_id',$accResult[$i]['temp_sample_id']);
                $result=$db->delete($tableName);
        
    }
        
    }
    
}
catch (Exception $exc) {
    error_log($exc->getMessage());
    error_log($exc->getTraceAsString());
}
echo $result;