<?php
session_start();
include('../includes/MysqliDb.php');
$gQuery = "SELECT * FROM global_config";
$gResult=$db->query($gQuery);
$global = array();
// now we create an associative array so that we can easily create view variables
for ($i = 0; $i < sizeof($gResult); $i++) {
  $global[$gResult[$i]['name']] = $gResult[$i]['value'];
}
if(isset($global['default_time_zone']) && count($global['default_time_zone'])> 0){
  date_default_timezone_set($global['default_time_zone']);
}else{
  date_default_timezone_set("Europe/London");
}
$hideResult = '';$hideRequest='';
if(isset($global['instance_type']) && $global['instance_type']!=''){
    if($global['instance_type']=='Clinic/Lab'){
        $hideResult = "display:none;";
    }
    //else if($global['instance_type']=='Viral Load Lab'){
       // $hideRequest = "display:none;";
    //}
}
if(!isset($_SESSION['userId'])){
    header("location:../login.php");
}

$link = $_SERVER['PHP_SELF'];
$link_array = explode('/',$link);
if(end($link_array)!='error.php' && end($link_array)!='vlResultUnApproval.php' && end($link_array)!='importedStatistics.php' && end($link_array)!='vlExportField.php'){
  if(isset($_SESSION['privileges']) && !in_array(end($link_array), $_SESSION['privileges'])){
    header("location:../error/error.php");
  }
}
if(isset($_SERVER['HTTP_REFERER'])){
  $previousUrl = $_SERVER['HTTP_REFERER'];
  $urlLast = explode('/',$previousUrl);
  if(end($urlLast)=='importedStatistics.php'){
      $db->delete('temp_sample_import');
      unset($_SESSION['controllertrack']);
  }
}
if(isset($_SESSION['privileges']) && array_intersect($_SESSION['privileges'], array('roles.php', 'users.php','facilities.php','globalConfig.php','importConfig.php','otherConfig.php'))) {
  $allAdminMenuAccess = true;
}else{
  $allAdminMenuAccess = false;  
}
if(isset($_SESSION['privileges']) && array_intersect($_SESSION['privileges'], array('vlRequest.php', 'addVlRequest.php','batchcode.php','vlRequestMail.php'))) {
  $requestMenuAccess = true;
}else{
  $requestMenuAccess = false;  
}
if(isset($_SESSION['privileges']) && array_intersect($_SESSION['privileges'], array('addImportResult.php', 'vlPrintResult.php','vlTestResult.php'))) {
  $testResultMenuAccess = true;
}else{
  $testResultMenuAccess = false;  
}
if(isset($_SESSION['privileges']) && array_intersect($_SESSION['privileges'], array('missingResult.php', 'vlResult.php','highViralLoad.php'))) {
  $managementMenuAccess = true;
}else{
  $managementMenuAccess = false;  
}
if(isset($_SESSION['privileges']) && in_array(('index.php'),$_SESSION['privileges']))
{
  $dashBoardMenuAccess = true;
}else{
  $dashBoardMenuAccess = false;  
}

$formConfigQuery ="SELECT * from global_config where name='vl_form'";
$formConfigResult=$db->query($formConfigQuery);




?>
<!DOCTYPE html>
<html lang="en-US">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <title><?php echo (isset($title) && $title != null && $title != "") ? $title : "VLSM | Viral Load LIS" ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" media="all" type="text/css" href="../assets/css/fonts.css" />

  <link rel="stylesheet" media="all" type="text/css" href="../assets/css/jquery-ui.1.11.0.css" />
  <link rel="stylesheet" media="all" type="text/css" href="../assets/css/jquery-ui-timepicker-addon.css" />

  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../assets/css/font-awesome.min.4.5.0.css">
  
  <!-- Ionicons -->
  <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">-->
  <!-- DataTables -->
  <link rel="stylesheet" href=".././assets/plugins/datatables/dataTables.bootstrap.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">
  <!-- iCheck -->
  
  <link href="../assets/plugins/daterangepicker/daterangepicker.css" rel="stylesheet" />
  
  <link href="../assets/css/select2.min.css" rel="stylesheet" />
  <link href="../assets/css/style.css" rel="stylesheet" />
  <link href="../assets/css/deforayModal.css" rel="stylesheet" />
  <link href="../assets/css/jquery.fastconfirm.css" rel="stylesheet" />
 
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <!-- jQuery 2.2.3 -->

<script type="text/javascript" src="../assets/js/jquery.min.2.0.2.js"></script>

 <!-- Latest compiled and minified JavaScript -->
    
<script type="text/javascript" src="../assets/js/jquery-ui.1.11.0.js"></script>
<script src="../assets/js/deforayModal.js"></script>
<script src="../assets/js/jquery.fastconfirm.js"></script>
  <!--<script type="text/javascript" src="assets/js/jquery-ui-sliderAccess.js"></script>-->
<style>
  .dataTables_wrapper{
  position: relative;
    clear: both;
    overflow-x: scroll !important;
    overflow-y: visible !important;
    padding: 15px 0 !important;
  }
  
  
  .select2-selection__choice__remove{
    color: red !important;
  }
  .select2-container--default .select2-selection--multiple .select2-selection__choice{
    background-color: #00c0ef;
    border-color: #00acd6;
    color: #fff !important;
    font-family:helvetica, arial, sans-serif;
  }
  
</style>
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="<?php echo($dashBoardMenuAccess == true)?'../dashboard/index.php':'#'; ?>" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>VLSM</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg">VLSM</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="../assets/img/default-user.png" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php if(isset($_SESSION['userName'])){ echo $_SESSION['userName']; } ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- Menu Footer-->
              <li class="user-footer">
                  <a href="../logout.php" class="">Sign out</a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <!-- Sidebar user panel -->
      <?php
        if(isset($global['logo']) && trim($global['logo'])!="" && file_exists('uploads'. DIRECTORY_SEPARATOR . "logo" . DIRECTORY_SEPARATOR . $global['logo'])){
        ?>
      <div class="user-panel">
        <div align="center">
          <img src="../uploads/logo/<?php echo $global['logo']; ?>"  alt="Logo Image" style="max-width:120px;" >
        </div>
        
      </div>
      <?php } ?>
      <ul class="sidebar-menu">
	<?php
	if($dashBoardMenuAccess == true){ ?>
	    <li class="allMenu dashboardMenu active">
	      <a href="../dashboard/index.php">
		<i class="fa fa-dashboard"></i> <span>Dashboard</span>
	      </a>
	    </li>
	<?php } ?>
	
	<?php
	if($allAdminMenuAccess == true){ ?>
	    <li class="treeview manage">
	      <a href="#">
          <i class="fa fa-gears"></i>
          <span>Admin</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
	      </a>
	      <ul class="treeview-menu">
          <?php if(isset($_SESSION['privileges']) && in_array("roles.php", $_SESSION['privileges'])){ ?>
            <li class="allMenu roleMenu">
              <a href="../roles/roles.php"><i class="fa fa-circle-o"></i> Roles</a>
            </li>
          <?php } if(isset($_SESSION['privileges']) && in_array("users.php", $_SESSION['privileges'])){ ?>
            <li class="allMenu userMenu">
              <a href="../users/users.php"><i class="fa fa-circle-o"></i> Users</a>
            </li>
          <?php } if(isset($_SESSION['privileges']) && in_array("facilities.php", $_SESSION['privileges'])){ ?>
            <li class="allMenu facilityMenu">
              <a href="../facilities/facilities.php"><i class="fa fa-circle-o"></i> Facilities</a>
            </li>
          <?php } if(isset($_SESSION['privileges']) && in_array("globalConfig.php", $_SESSION['privileges'])){ ?>
            <li class="allMenu globalConfigMenu">
              <a href="../global-config/globalConfig.php"><i class="fa fa-circle-o"></i> General Configuration</a>
            </li>
          <?php } if(isset($_SESSION['privileges']) && in_array("importConfig.php", $_SESSION['privileges'])){ ?>
            <li class="allMenu importConfigMenu">
              <a href="../import-configs/importConfig.php"><i class="fa fa-circle-o"></i> Import Configuration</a>
            </li>
          <?php } if(isset($_SESSION['privileges']) && in_array("testRequestEmailConfig.php", $_SESSION['privileges'])){ ?>
            <li class="allMenu requestEmailConfigMenu">
              <a href="../request-mail/testRequestEmailConfig.php"><i class="fa fa-circle-o"></i>Test Request Email/SMS <br>Configuration</a>
            </li>
          <?php } if(isset($_SESSION['privileges']) && in_array("testResultEmailConfig.php", $_SESSION['privileges'])){ ?>
            <li class="allMenu resultEmailConfigMenu">
              <a href="../result-mail/testResultEmailConfig.php"><i class="fa fa-circle-o"></i>Test Result Email/SMS <br>Configuration</a>
            </li>
          <?php } ?>
	      </ul>
	    </li>
	<?php }
        if($requestMenuAccess == true){
        ?>
        <li class="treeview request" style="<?php echo $hideRequest;?>">
            <a href="#">
                <i class="fa fa-edit"></i>
                <span>Request Management</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
              <?php
               if(isset($_SESSION['privileges']) && in_array("vlRequest.php", $_SESSION['privileges'])){ ?>
                  <li class="allMenu vlRequestMenu">
                    <a href="../vl-request/vlRequest.php"><i class="fa fa-circle-o"></i> View Test Requests</a>
                  </li>
              <?php }  if(isset($_SESSION['privileges']) && in_array("addVlRequest.php", $_SESSION['privileges'])){ ?>
                  <li class="allMenu addVlRequestMenu">
                    <a href="../vl-request/addVlRequest.php"><i class="fa fa-circle-o"></i> Add New Request</a>
                  </li>
              <?php }  if(isset($_SESSION['privileges']) && in_array("batchcode.php", $_SESSION['privileges'])){ ?>
                  <li class="allMenu batchCodeMenu">
                    <a href="../batch/batchcode.php"><i class="fa fa-circle-o"></i> Manage Batch</a>
                  </li>
              <?php } if(isset($_SESSION['privileges']) && in_array("vlRequestMail.php", $_SESSION['privileges'])){ ?>
                  <li class="allMenu vlRequestMailMenu">
                    <a href="../mail/vlRequestMail.php"><i class="fa fa-circle-o"></i> E-mail Test Request</a>
                  </li>
              <?php } if(isset($_SESSION['privileges']) && in_array("addImportTestResult.php", $_SESSION['privileges'])){ ?>
                  <!--<li class="allMenu importTestResultMenu">
                    <a href="../vl-request/addImportTestResult.php"><i class="fa fa-circle-o"></i> Import Test Result</a>
                  </li>-->
              <?php } ?>
            </ul>
        </li>
        <?php }
        if($testResultMenuAccess == true){
        ?>
        <li class="treeview test" style="<?php echo $hideResult;?>">
            <a href="#">
                <i class="fa fa-edit"></i>
                <span>Test Result Management</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
              <?php if(isset($_SESSION['privileges']) && in_array("addImportResult.php", $_SESSION['privileges'])){ ?>
                <li class="allMenu importResultMenu"><a href="../import-result/addImportResult.php"><i class="fa fa-circle-o"></i> Import Result</a></li>
              <?php }  if(isset($_SESSION['privileges']) && in_array("vlTestResult.php", $_SESSION['privileges'])){ ?>
                <li class="allMenu vlTestResultMenu"><a href="../vl-print/vlTestResult.php"><i class="fa fa-circle-o"></i> Enter Result</a></li>
              <?php } if(isset($_SESSION['privileges']) && in_array("vlResultApproval.php", $_SESSION['privileges'])){ ?>
                <li class="allMenu vlResultApprovalMenu"><a href="../vl-print/vlResultApproval.php"><i class="fa fa-circle-o"></i> Approve Results</a></li>
              <?php }  if(isset($_SESSION['privileges']) && in_array("vlResultMail.php", $_SESSION['privileges'])){ ?>
                <li class="allMenu vlResultMailMenu"><a href="../mail/vlResultMail.php"><i class="fa fa-circle-o"></i> E-mail Test Result</a></li>
              <?php } if(isset($_SESSION['privileges']) && in_array("addImportTestRequest.php", $_SESSION['privileges'])){ ?>
                <!--<li class="allMenu importTestRequestMenu"><a href="../import-result/addImportTestRequest.php"><i class="fa fa-circle-o"></i> Import Test Request</a></li>-->
              <?php }?>
            </ul>
        </li>
        <?php }
        if($managementMenuAccess == true){
        ?>
            <li class="treeview program">
                <a href="#">
                    <i class="fa fa-book"></i>
                    <span>Management</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if(isset($_SESSION['privileges']) && in_array("missingResult.php", $_SESSION['privileges'])){ ?>
                    <li class="allMenu missingResultMenu"><a href="../program-management/missingResult.php"><i class="fa fa-circle-o"></i> Sample Status Report</a></li>
                    <?php } ?>
                    <!--<li><a href="#"><i class="fa fa-circle-o"></i> TOT Report</a></li>
                    <li><a href="#"><i class="fa fa-circle-o"></i> VL Suppression Report</a></li>-->
                    <?php if(isset($_SESSION['privileges']) && in_array("vlResult.php", $_SESSION['privileges'])){ ?>
                    <li class="allMenu vlResultMenu"><a href="../program-management/vlResult.php"><i class="fa fa-circle-o"></i> Export Results</a></li>
                    <?php } if(isset($_SESSION['privileges']) && in_array("vlPrintResult.php", $_SESSION['privileges'])){ ?>
                    <li class="allMenu vlPrintResultMenu"><a href="../vl-print/vlPrintResult.php"><i class="fa fa-circle-o"></i> Print Result</a></li>
                    <?php } if(isset($_SESSION['privileges']) && in_array("highViralLoad.php", $_SESSION['privileges'])){ ?>
                    <li class="allMenu vlHighMenu"><a href="../program-management/highViralLoad.php"><i class="fa fa-circle-o"></i> High Viral Load</a></li>
                    <?php }  if(isset($_SESSION['privileges']) && in_array("patientList.php", $_SESSION['privileges'])){ ?>
                    <!--<li class="allMenu patientList"><a href="patientList.php"><i class="fa fa-circle-o"></i> Export Patient List</a></li>-->
                    <?php } if(isset($_SESSION['privileges']) && in_array("vlWeeklyReport.php", $_SESSION['privileges'])){ ?>
                    <li class="allMenu vlWeeklyReport"><a href="../program-management/vlWeeklyReport.php"><i class="fa fa-circle-o"></i> VL Lab Weekly Report</a></li>
                    <?php } if(isset($_SESSION['privileges']) && in_array("sampleRejectionReport.php", $_SESSION['privileges'])){ ?>
                    <li class="allMenu sampleRejectionReport"><a href="../program-management/sampleRejectionReport.php"><i class="fa fa-circle-o"></i> Sample Rejection Report</a></li>
                    <?php } if(isset($_SESSION['privileges']) && in_array("vlMonitoringReport.php", $_SESSION['privileges'])){ ?>
                    <li class="allMenu vlMonitoringReport"><a href="../program-management/vlMonitoringReport.php"><i class="fa fa-circle-o"></i> Sample Monitoring Report</a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php
        }?>
        <?php
        if(isset($global['enable_qr_mechanism']) && trim($global['enable_qr_mechanism']) == 'yes'){ ?>
          <li class="treeview qr">
            <a href="#">
                <i class="fa fa-qrcode"></i>
                <span>QR Code</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
              <?php if(isset($_SESSION['privileges']) && in_array("generate.php", $_SESSION['privileges'])){ ?>
                <li class="allMenu generateQRCode"><a href="../qr-code/generate.php"><i class="fa fa-circle-o"></i> Generate QR Code</a></li>
              <?php } if(isset($_SESSION['privileges']) && in_array("readQRCode.php", $_SESSION['privileges'])){ ?>
                <li class="allMenu readQRCode"><a href="../qr-code/readQRCode.php"><i class="fa fa-circle-o"></i> Read QR Code</a></li>
              <?php } ?>
            </ul>
          </li>
        <?php } ?>
        <!---->
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
  <!-- content-wrapper -->
    <div id="dDiv" class="dialog">
        <div style="text-align:center"><span onclick="closeModal();" style="float:right;clear:both;" class="closeModal"></span></div> 
        <iframe id="dFrame" src="" style="border:none;" scrolling="yes" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0">some problem</iframe> 
    </div>