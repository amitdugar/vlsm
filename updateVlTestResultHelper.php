<?php
ob_start();
session_start();
include('./includes/MysqliDb.php');
//include('header.php');
include('General.php');

$general=new Deforay_Commons_General();

$tableName="vl_request_form";
$treamentId=(int) $_POST['treamentId'];

try {
   if(isset($_POST['sampleReceivedOn']) && trim($_POST['sampleReceivedOn'])!=""){
               $_POST['sampleReceivedOn']=$general->dateFormat($_POST['sampleReceivedOn']);  
          }
          
          if(isset($_POST['sampleTestedOn']) && trim($_POST['sampleTestedOn'])!=""){
               $_POST['sampleTestedOn']=$general->dateFormat($_POST['sampleTestedOn']);  
          }
          
          if(isset($_POST['resultDispatchedOn']) && trim($_POST['resultDispatchedOn'])!=""){
               $_POST['resultDispatchedOn']=$general->dateFormat($_POST['resultDispatchedOn']);  
          }
          
          if(isset($_POST['reviewedOn']) && trim($_POST['reviewedOn'])!=""){
               $_POST['reviewedOn']=$general->dateFormat($_POST['reviewedOn']);  
          }
   
   $vldata =  array('lab_name'=>$_POST['labName'],
          'lab_contact_person'=>$_POST['labContactPerson'],
          'lab_phone_no'=>$_POST['labPhoneNo'],
          'date_sample_received_at_testing_lab'=>$_POST['sampleReceivedOn'],
          'lab_tested_date'=>$_POST['sampleTestedOn'],
          'date_results_dispatched'=>$_POST['resultDispatchedOn'],
          'result_reviewed_by'=>$_SESSION['userId'],
          'result_reviewed_date'=>$_POST['reviewedOn'],
          'log_value'=>$_POST['logValue'],
          'absolute_value'=>$_POST['absoluteValue'],
          'text_value'=>$_POST['textValue'],
          'result'=>$_POST['result'],
          'comments'=>$_POST['comments'],
          'status'=>$_POST['status'],
        );
          //print_r($vldata);die;
          $db=$db->where('treament_id',$treamentId);
          $db->update($tableName,$vldata);
          
          $_SESSION['alertMsg']="VL Result updated successfully";
 header("location:vlTestResult.php"); 
  
} catch (Exception $exc) {
    error_log($exc->getMessage());
    error_log($exc->getTraceAsString());
}