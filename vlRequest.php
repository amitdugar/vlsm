<?php
include('header.php');
//include('./includes/MysqliDb.php');
$tsQuery="SELECT * FROM testing_status";
$tsResult = $db->rawQuery($tsQuery);
$sQuery="SELECT * FROM r_sample_type";
$sResult = $db->rawQuery($sQuery);
$fQuery="SELECT * FROM facility_details where status='active'";
$fResult = $db->rawQuery($fQuery);
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Test Request</h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Test Request</li>
      </ol>
    </section>

     <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
	    <table class="table" cellpadding="1" cellspacing="3" style="margin-left:1%;margin-top:20px;width: 98%;margin-bottom: 0px;">
		<tr>
		    <td style=""><b>Sample Collection Date&nbsp;:</b></td>
		    <td>
		      <input type="text" id="sampleCollectionDate" name="sampleCollectionDate" class="form-control" placeholder="Select Collection Date" readonly style="width:220px;background:#fff;"/>
		    </td>
		    <td>&nbsp;<b>Batch Code&nbsp;:</b></td>
		    <td>
			<input type="text" id="batchCode" name="batchCode" class="form-control" placeholder="Enter Batch Code"/>
		    </td>
		    </tr>
		<tr>
		    <td>&nbsp;<b>Sample Type&nbsp;:</b></td>
		    <td>
		      <select style="width:220px;" class="form-control" id="sampleType" name="sampleType" title="Please select sample type">
		      <option value=""> -- Select -- </option>
			<?php
			foreach($sResult as $type){
			 ?>
			 <option value="<?php echo $type['sample_id'];?>"><?php echo ucwords($type['sample_name']);?></option>
			 <?php
			}
			?>
		      </select>
		    </td>
		
		    <td>&nbsp;<b>Facility Name & Code&nbsp;:</b></td>
		    <td>
		      <select class="form-control" id="facilityName" name="facilityName" title="Please select facility name">
		      <option value=""> -- Select -- </option>
			<?php
			foreach($fResult as $name){
			 ?>
			 <option value="<?php echo $name['facility_id'];?>"><?php echo ucwords($name['facility_name']."-".$name['facility_code']);?></option>
			 <?php
			}
			?>
		      </select>
		    </td>
		</tr>
		<tr>
		  <td colspan="3">&nbsp;<input type="button" onclick="searchVlRequestData();" value="Search" class="btn btn-success btn-sm">
		    &nbsp;<button class="btn btn-danger btn-sm" onclick="document.location.href = document.location"><span>Reset</span></button>
		    &nbsp;<button class="btn btn-primary btn-sm" onclick="$('#showhide').fadeToggle();return false;"><span>Manage Columns</span></button>
		    </td>
		</tr>
		
	    </table>
	    <span style="display: none;position:absolute;z-index: 9999 !important;color:#000;padding:5px;" id="showhide" class="">
			<div class="row" style="background:#e0e0e0;float: right !important;padding: 15px;">
			    <div class="col-md-12" >
			      
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="0" id="iCol0" data-showhide="form_id" class="showhideCheckBox" /> <label for="iCol0">Serial No</label>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="1" id="iCol1" data-showhide="sample_collection_date" class="showhideCheckBox"  /> <label for="iCol1">Sample Collection Date</label>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="2" id="iCol2" data-showhide="batch_code" class="showhideCheckBox"  /> <label for="iCol2">Batch Code</label> <br>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="3" id="iCol3" data-showhide="art_no" class="showhideCheckBox"  /> <label for="iCol3">Art No</label>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="4" id="iCol4" data-showhide="patient_name" class="showhideCheckBox" /> <label for="iCol4">Patient's Name</label> <br>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="5" id="iCol5" data-showhide="facility_name"  class="showhideCheckBox" /> <label for="iCol5">Faility Name</label>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="6" id="iCol6" data-showhide="state"  class="showhideCheckBox" /> <label for="iCol6">Province</label>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="7" id="iCol7" data-showhide="district"  class="showhideCheckBox" /> <label for="iCol7">District</label>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="8" id="iCol8" data-showhide="sample_name"  class="showhideCheckBox" /> <label for="iCol8">Sample Type</label>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="9" id="iCol9" data-showhide="result"  class="showhideCheckBox" /> <label for="iCol9">Result</label>
				    </div>
				    <div class="col-md-3">
					    <input type="checkbox" onclick="javascript:fnShowHide(this.value);" value="10" id="iCol10" data-showhide="status_name" class="showhideCheckBox" /> <label for="iCol10">Status</label>
				    </div>
				</div>
			    </div>
			</span>
            <div class="box-header with-border">
			  <?php if(isset($formConfigResult[0]['value']) && $formConfigResult[0]['value']==2){ 
			  if(isset($_SESSION['privileges']) && in_array("addVlRequestZm.php", $_SESSION['privileges'])){
			  ?>
              <a href="addVlRequestZm.php" class="btn btn-primary pull-right"> <i class="fa fa-plus"></i> Add VL Request Form</a>
			  <?php } ?>
			  <?php }else{ 
			  if(isset($_SESSION['privileges']) && in_array("addVlRequest.php", $_SESSION['privileges'])){ ?>
              <a href="addVlRequest.php" class="btn btn-primary pull-right"> <i class="fa fa-plus"></i> Add VL Request Form</a>
			  <?php } 
			  } ?>
	      
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="vlRequestDataTable" class="table table-bordered table-striped">
                <thead>
                <tr>
		  <!--<th><input type="checkbox" id="checkTestsData" onclick="toggleAllVisible()"/></th>-->
				  <th>Form Serial No.</th>
                  <th>Sample Collection Date</th>
                  <th>Batch Code</th>
                  <th>Unique ART No</th>
                  <th>Patient's Name</th>
				  <th>Facility Name</th>
				  <th>Province</th>
				  <th>District</th>
                  <th>Sample Type</th>
                  <th>Result</th>
                  <th>Status</th>
		  <?php if(isset($_SESSION['privileges']) && (in_array("editVlRequest.php", $_SESSION['privileges'])) || (in_array("viewVlRequest.php", $_SESSION['privileges']))){ ?>
                  <th>Action</th>
		  <?php } ?>
                </tr>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="15" class="dataTables_empty">Loading data from server</td>
                </tr>
                </tbody>
              </table>
            </div>
            <!-- /.box-body -->
	<!--    <table class="table" cellpadding="1" cellspacing="3" style="margin-left:1%;margin-top:30px;width: 45%;">-->
	<!--    <tr style="margin-top:30px;">-->
	<!--	  <td><b>Choose Status&nbsp;:</b></td>-->
	<!--	  <td>-->
	<!--	    <input type="hidden" name="checkedTests" id="checkedTests"/>-->
	<!--	    <select style="" class="form-control" id="status" name="status" title="Please select test status" disabled=disabled"">-->
	<!--	      <option value="">-- Select --</option>-->
	<!--		< ?php-->
	<!--		foreach($tsResult as $status){-->
	<!--		 ?>-->
	<!--		 <option value="< ?php echo $status['status_id'];?>">< ?php echo ucwords($status['status_name']);?></option>-->
	<!--		 < ?php-->
	<!--		}-->
	<!--		?>-->
	<!--	    </select>-->
	<!--	  </td>-->
	<!--	  <td>&nbsp;<input type="button" onclick="submitTestStatus();" value="Update" class="btn btn-success btn-sm"></td>-->
	<!--	</tr>-->
	<!--  </table>-->
          </div>
          <!-- /.box -->
	  
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <script type="text/javascript" src="assets/plugins/daterangepicker/moment.min.js"></script>
  <script type="text/javascript" src="assets/plugins/daterangepicker/daterangepicker.js"></script>
  <script type="text/javascript">
   var startDate = "";
   var endDate = "";
   var selectedTests=[];
   var selectedTestsId=[];
   var oTable = null;
  $(document).ready(function() {
     $('#sampleCollectionDate').daterangepicker({
            format: 'DD-MMM-YYYY',
	    separator: ' to ',
            startDate: moment().subtract('days', 29),
            endDate: moment(),
            maxDate: moment(),
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                'Last 7 Days': [moment().subtract('days', 6), moment()],
                'Last 30 Days': [moment().subtract('days', 29), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
            }
        },
        function(start, end) {
            startDate = start.format('YYYY-MM-DD');
            endDate = end.format('YYYY-MM-DD');
      });
     $('#sampleCollectionDate').val("");
     loadVlRequestData();
     
     $(".showhideCheckBox").change(function(){
            
            if($(this).attr('checked')){
                idpart = $(this).attr('data-showhide');
                $("#"+idpart+"-sort").show();
            }else{
                idpart = $(this).attr('data-showhide');
                $("#"+idpart+"-sort").hide();
            }
        });
        
        $("#showhide").hover(function(){}, function(){$(this).fadeOut('slow')});
        
        for(colNo=0;colNo <=10;colNo++){
            $("#iCol"+colNo).attr("checked",oTable.fnSettings().aoColumns[parseInt(colNo)].bVisible);
            if(oTable.fnSettings().aoColumns[colNo].bVisible){
                $("#iCol"+colNo+"-sort").show();    
            }else{
                $("#iCol"+colNo+"-sort").hide();    
            }
        }
  } );
  
  function fnShowHide(iCol)
    {
        var bVis = oTable.fnSettings().aoColumns[iCol].bVisible;
        oTable.fnSetColumnVis( iCol, bVis ? false : true );
    }
  function loadVlRequestData(){
    $.blockUI();
     oTable = $('#vlRequestDataTable').dataTable({
            "oLanguage": {
                "sLengthMenu": "_MENU_ records per page"
            },
            "bJQueryUI": false,
            "bAutoWidth": false,
            "bInfo": true,
            "bScrollCollapse": true,
            "bStateSave" : true,
            "bRetrieve": true,                        
            "aoColumns": [
		//{"sClass":"center","bSortable":false},
                {"sClass":"center"},
                {"sClass":"center"},
                {"sClass":"center"},
                {"sClass":"center"},
                {"sClass":"center"},
                {"sClass":"center"},
                {"sClass":"center"},
                {"sClass":"center"},
                {"sClass":"center"},
                {"sClass":"center","bSortable":false},
                {"sClass":"center"},
		<?php if(isset($_SESSION['privileges']) && (in_array("editVlRequest.php", $_SESSION['privileges'])) || (in_array("viewVlRequest.php", $_SESSION['privileges']))){ ?>
                {"sClass":"center","bSortable":false},
		<?php } ?>
            ],
            //"aaSorting": [[ 1, "asc" ]],
	    "fnDrawCallback": function() {
		var checkBoxes=document.getElementsByName("chk[]");
                len = checkBoxes.length;
                for(c=0;c<len;c++){
                    if (jQuery.inArray(checkBoxes[c].id, selectedTestsId) != -1 ){
			checkBoxes[c].setAttribute("checked",true);
                    }
                }
	    },
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "getVlRequestDetails.php",
            "fnServerData": function ( sSource, aoData, fnCallback ) {
	      aoData.push({"name": "batchCode", "value": $("#batchCode").val()});
	      aoData.push({"name": "sampleCollectionDate", "value": $("#sampleCollectionDate").val()});
	      aoData.push({"name": "facilityName", "value": $("#facilityName").val()});
	      aoData.push({"name": "sampleType", "value": $("#sampleType").val()});
              $.ajax({
                  "dataType": 'json',
                  "type": "POST",
                  "url": sSource,
                  "data": aoData,
                  "success": fnCallback
              });
            }
        });
     $.unblockUI();
  }
  function searchVlRequestData(){
    $.blockUI();
    oTable.fnDraw();
    $.unblockUI();
  }
    
  function convertPdf(id){
      $.post("vlRequestPdf.php", { id : id, format: "html"},
      function(data){
	  if(data == "" || data == null || data == undefined){
	      alert('Unable to generate download');
	  }else{
	      window.open('uploads/'+data,'_blank');
	  }
	  
      });
  }
  function convertZmbPdf(id){
      $.post("vlRequestZmbPdf.php", { id : id, format: "html"},
      function(data){
	  if(data == "" || data == null || data == undefined){
	      alert('Unable to generate download');
	  }else{
	      window.open('uploads/'+data,'_blank');
	  }
	  
      });
  }
  
  function toggleTest(obj){
	 if ($(obj).is(':checked')) {
	     if($.inArray(obj.value, selectedTests) == -1){
		 selectedTests.push(obj.value);
		 selectedTestsId.push(obj.id);
	     }
	 } else {
	     selectedTests.splice( $.inArray(obj.value, selectedTests), 1 );
	     selectedTestsId.splice( $.inArray(obj.id, selectedTestsId), 1 );
	     $("#checkTestsData").attr("checked",false);
	 }
	 $("#checkedTests").val(selectedTests.join());
	 if(selectedTests.length!=0){
	  $("#status").prop('disabled', false);
	 }else{
	  $("#status").prop('disabled', true);
	 }
	 
    }
      
    function toggleAllVisible(){
        //alert(tabStatus);
	$(".checkTests").each(function(){
	     $(this).prop('checked', false);
	     selectedTests.splice( $.inArray(this.value, selectedTests), 1 );
	     selectedTestsId.splice( $.inArray(this.id, selectedTestsId), 1 );
	     $("#status").prop('disabled', true);
	 });
	 if ($("#checkTestsData").is(':checked')) {
	 $(".checkTests").each(function(){
	     $(this).prop('checked', true);
		 selectedTests.push(this.value);
		 selectedTestsId.push(this.id);
	 });
	 $("#status").prop('disabled', false);
     } else{
	$(".checkTests").each(function(){
	     $(this).prop('checked', false);
	     selectedTests.splice( $.inArray(this.value, selectedTests), 1 );
	     selectedTestsId.splice( $.inArray(this.id, selectedTestsId), 1 );
	     $("#status").prop('disabled', true);
	 });
     }
     $("#checkedTests").val(selectedTests.join());
   }
   
   function submitTestStatus(){
    var stValue = $("#status").val();
    var testIds = $("#checkedTests").val();
    if(stValue!='' && testIds!=''){
      conf=confirm("Do you wish to change the test status ?");
      if (conf) {
    $.post("updateTestStatus.php", { status : stValue,id:testIds, format: "html"},
      function(data){
	  if(data != ""){
	    $("#checkedTests").val('');
	    selectedTests = [];
	    selectedTestsId = [];
	    $("#checkTestsData").attr("checked",false);
	    $("#status").val('');
	    $("#status").prop('disabled', true);
	    oTable.fnDraw();
	    alert('Updated successfully.');
	  }
      });
      }
    }else{
      alert("Please checked atleast one checkbox.");
    }
   }
  
  function printBarcode(tId) {
    $.post("printBarcode.php",{id:tId},
      function(data){
	  if(data == "" || data == null || data == undefined){
	    alert('Unable to generate download');
	  }else{
	    window.open('uploads/barcode/'+data,'_blank');
	  }
    });
  }
</script>
 <?php
 include('footer.php');
 ?>
