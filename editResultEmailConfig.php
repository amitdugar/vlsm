<?php
ob_start();
include('header.php');
//include('./includes/MysqliDb.php');
$otherConfigQuery ="SELECT * from other_config";
$otherConfigResult=$db->query($otherConfigQuery);
$resultEmailConfigQuery ="SELECT * from other_config WHERE name ='result_email_field'";
$resultEmailConfigResult=$db->query($resultEmailConfigQuery);
$arr = array();
// now we create an associative array so that we can easily create view variables
for ($i = 0; $i < sizeof($otherConfigResult); $i++) {
    $arr[$otherConfigResult[$i]['name']] = $otherConfigResult[$i]['value'];
}
$resultArr = array();
//Set selected field
if(isset($resultEmailConfigResult) && trim($resultEmailConfigResult[0]['value'])!= ''){
  $explodField = explode(",",$resultEmailConfigResult[0]['value']);
  for($f=0;$f<count($explodField); $f++){
    $resultArr[] = $explodField[$f];
  }
}
?>
<link href="assets/css/multi-select.css" rel="stylesheet" />
<style>
    .ms-container{
        width:100%;
    }
</style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1 class="fa fa-gears"> Edit Result Email Configuration</h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="otherConfig.php"><i class="fa fa-dashboard"></i> Manage Email/SMS Config</a></li>
        <li class="active">Edit Result Email Configuration</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- SELECT2 EXAMPLE -->
      <div class="box box-default">
        <!--<div class="box-header with-border">
          <div class="pull-right" style="font-size:15px;"> </div>
        </div>-->
        <!-- /.box-header -->
        <div class="box-body">
          <!-- form start -->
            <form class="form-horizontal" method="post" name="editResultEmailConfigForm" id="editResultEmailConfigForm" autocomplete="off" action="editResultEmailConfigHelper.php">
              <div class="box-body">
                <div class="row">
                    <div class="col-md-9">
                    <div class="form-group">
                        <label for="result_email_field" class="col-lg-3 control-label">Choose VL Fields</label>
                        <div class="col-lg-9">
                           <div style="width:100%;margin:0 auto;clear:both;">
                            <a href="#" id="select-all-field" style="float:left" class="btn btn-info btn-xs">Select All&nbsp;&nbsp;<i class="icon-chevron-right"></i></a>  <a href="#" id="deselect-all-field" style="float:right" class="btn btn-danger btn-xs"><i class="icon-chevron-left"></i>&nbsp;Deselect All</a>
                            </div><br/><br/>
                            <select id="result_email_field" name="result_email_field[]" multiple="multiple" class="search">
                                <option value="Form Serial No" <?php echo(in_array("Form Serial No",$resultArr)?"selected='selected'":""); ?>>Form Serial No</option>
                                <option value="Urgency" <?php echo(in_array("Urgency",$resultArr)?"selected='selected'":""); ?>>Urgency</option>
                                <option value="Province" <?php echo(in_array("Province",$resultArr)?"selected='selected'":""); ?>>Province</option>
                                <option value="District Name" <?php echo(in_array("District Name",$resultArr)?"selected='selected'":""); ?>>District Name</option>
                                <option value="Clinic Name" <?php echo(in_array("Clinic Name",$resultArr)?"selected='selected'":""); ?>>Clinic Name</option>
                                <option value="Clinician Name" <?php echo(in_array("Clinician Name",$resultArr)?"selected='selected'":""); ?>>Clinician Name</option>
                                <option value="Sample Collection Date" <?php echo(in_array("Sample Collection Date",$resultArr)?"selected='selected'":""); ?>>Sample Collection Date</option>
                                <option value="Sample Received Date" <?php echo(in_array("Sample Received Date",$resultArr)?"selected='selected'":""); ?>>Sample Received Date</option>
                                <option value="Collected by (Initials)" <?php echo(in_array("Collected by (Initials)",$resultArr)?"selected='selected'":""); ?>>Collected by (Initials)</option>
                                <option value="Patient First Name" <?php echo(in_array("Patient First Name",$resultArr)?"selected='selected'":""); ?>>Patient First Name</option>
                                <option value="Surname" <?php echo(in_array("Surname",$resultArr)?"selected='selected'":""); ?>>Surname</option>
                                <option value="Gender" <?php echo(in_array("Gender",$resultArr)?"selected='selected'":""); ?>>Gender</option>
                                <option value="Date Of Birth" <?php echo(in_array("Date Of Birth",$resultArr)?"selected='selected'":""); ?>>Date Of Birth</option>
                                <option value="Age in years" <?php echo(in_array("Age in years",$resultArr)?"selected='selected'":""); ?>>Age in years</option>
                                <option value="Age in months" <?php echo(in_array("Age in months",$resultArr)?"selected='selected'":""); ?>>Age in months</option>
                                <option value="Is Patient Pregnant?" <?php echo(in_array("Is Patient Pregnant?",$resultArr)?"selected='selected'":""); ?>>Is Patient Pregnant?</option>
                                <option value="Is Patient Breastfeeding?" <?php echo(in_array("Is Patient Breastfeeding?",$resultArr)?"selected='selected'":""); ?>>Is Patient Breastfeeding?</option>
                                <option value="Patient OI/ART Number" <?php echo(in_array("Patient OI/ART Number",$resultArr)?"selected='selected'":""); ?>>Patient OI/ART Number</option>
                                <option value="Date Of ART Initiation" <?php echo(in_array("Date Of ART Initiation",$resultArr)?"selected='selected'":""); ?>>Date Of ART Initiation</option>
                                <option value="ART Regimen" <?php echo(in_array("ART Regimen",$resultArr)?"selected='selected'":""); ?>>ART Regimen</option>
                                <option value="Patient consent to SMS Notification?" <?php echo(in_array("Patient consent to SMS Notification?",$resultArr)?"selected='selected'":""); ?>>Patient consent to SMS Notification?</option>
                                <option value="Patient Mobile Number" <?php echo(in_array("Patient Mobile Number",$resultArr)?"selected='selected'":""); ?>>Patient Mobile Number</option>
                                <option value="Date Of Last Viral Load Test" <?php echo(in_array("Date Of Last Viral Load Test",$resultArr)?"selected='selected'":""); ?>>Date Of Last Viral Load Test</option>
                                <option value="Result Of Last Viral Load" <?php echo(in_array("Result Of Last Viral Load",$resultArr)?"selected='selected'":""); ?>>Result Of Last Viral Load</option>
                                <option value="Viral Load Log" <?php echo(in_array("Viral Load Log",$resultArr)?"selected='selected'":""); ?>>Viral Load Log</option>
                                <option value="Reason For VL Test" <?php echo(in_array("Reason For VL Test",$resultArr)?"selected='selected'":""); ?>>Reason For VL Test</option>
                                <option value="Lab Name" <?php echo(in_array("Lab Name",$resultArr)?"selected='selected'":""); ?>>Lab Name</option>
                                <option value="LAB No" <?php echo(in_array("lab_no",$resultArr)?"selected='selected'":""); ?>>LAB No</option>
                                <option value="VL Testing Platform" <?php echo(in_array("VL Testing Platform",$resultArr)?"selected='selected'":""); ?>>VL Testing Platform</option>
                                <option value="Specimen type" <?php echo(in_array("Specimen type",$resultArr)?"selected='selected'":""); ?>>Specimen type</option>
                                <option value="Sample Testing Date" <?php echo(in_array("Sample Testing Date",$resultArr)?"selected='selected'":""); ?>>Sample Testing Date</option>
                                <option value="Viral Load Result(copiesl/ml)" <?php echo(in_array("Viral Load Result(copiesl/ml)",$resultArr)?"selected='selected'":""); ?>>Viral Load Result(copiesl/ml)</option>
                                <option value="Log Value" <?php echo(in_array("Log Value",$resultArr)?"selected='selected'":""); ?>>Log Value</option>
                                <option value="If no result" <?php echo(in_array("If no result",$resultArr)?"selected='selected'":""); ?>>If no result</option>
                                <option value="Rejection Reason" <?php echo(in_array("Rejection Reason",$resultArr)?"selected='selected'":""); ?>>Rejection Reason</option>
                                <option value="Reviewed By" <?php echo(in_array("Reviewed By",$resultArr)?"selected='selected'":""); ?>>Reviewed By</option>
                                <option value="Approved By" <?php echo(in_array("Approved By",$resultArr)?"selected='selected'":""); ?>>Approved By</option>
                                <option value="Laboratory Scientist Comments" <?php echo(in_array("Laboratory Scientist Comments",$resultArr)?"selected='selected'":""); ?>>Laboratory Scientist Comments</option>
                            </select>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <a class="btn btn-primary" href="javascript:void(0);" onclick="validateNow();return false;">Submit</a>
                <a href="otherConfig.php" class="btn btn-default"> Cancel</a>
              </div>
              <!-- /.box-footer -->
            </form>
          <!-- /.row -->
        </div>
       
      </div>
      <!-- /.box -->

    </section>
    <!-- /.content -->
  </div>
  <script src="assets/js/jquery.multi-select.js"></script>
  <script src="assets/js/jquery.quicksearch.js"></script>
  <script type="text/javascript">
  $(document).ready(function() {
      $('.search').multiSelect({
       selectableHeader: "<input type='text' class='search-input form-control' autocomplete='off' placeholder='Enter Field Name'>",
       selectionHeader: "<input type='text' class='search-input form-control' autocomplete='off' placeholder='Enter Field Name'>",
       afterInit: function(ms){
	 var that = this,
	     $selectableSearch = that.$selectableUl.prev(),
	     $selectionSearch = that.$selectionUl.prev(),
	     selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
	     selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';
     
	 that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
	 .on('keydown', function(e){
	   if (e.which === 40){
	     that.$selectableUl.focus();
	     return false;
	   }
	 });
     
	 that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
	 .on('keydown', function(e){
	   if (e.which == 40){
	     that.$selectionUl.focus();
	     return false;
	   }
	 });
       },
       afterSelect: function(){
         this.qs1.cache();
         this.qs2.cache();
       },
       afterDeselect: function(){
        this.qs1.cache();
        this.qs2.cache();
       }
     });
      
      $('#select-all-field').click(function(){
       $('#result_email_field').multiSelect('select_all');
       return false;
     });
     $('#deselect-all-field').click(function(){
       $('#result_email_field').multiSelect('deselect_all');
       return false;
     });
   });
  
  function validateNow(){
    flag = deforayValidator.init({
        formId: 'editResultEmailConfigForm'
    });
    
    if(flag){
        $.blockUI();
      document.getElementById('editResultEmailConfigForm').submit();
    }
  }
</script>
  
 <?php
 include('footer.php');
 ?>
