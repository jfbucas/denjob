<?php
include 'func.php';

$msg = "";
$error = "";

function get_page($c, $msg = "", $error = "") {
	get_js_toggle();	
	echo "<script src='https://code.jquery.com/jquery-1.11.3.js'></script>";

	echo '<script language="javascript" src="jquery-ui.js"></script>';
	echo '<link href="jquery-ui.css" rel="stylesheet" type="text/css">';

	echo '<script language="javascript"> $(function() { $( ".date" ).datepicker( { "dateFormat": "yy-mm-dd" } ); }); </script>';
	echo "<style>";
	echo ".job     { border-style: groove;  }";
	echo ".visible { display: block; }";
	echo "</style>";
	echo "";
	echo "<h2>Welcome Admin, you will be able to manage the assessors for each positions below </h2>\n";

	if ($msg != "") {
		echo "<h4> <font color=green>$msg</font> </h4>";
	}
	if ($error != "") {
		echo "<h4> <font color=red>$error</font> </h4>";
	}

	echo "<h3> Positions </h3>\n";
	echo "<hr>";
	echo "<h5> Add a position </h5>\n";
	echo "<form action='admin.php?c=$c' method='post' name='formaddpos'>\n";
	echo "Job Title <input type='text' name='jobtitle' maxlength='80' value=''><br>\n";
	echo "Due date <input type='text' name='jobdue' maxlength='12' value='".date("Y-m-d"). "' class='date'><br>\n";
	echo "Description <textarea rows='20' cols='150' name='jobdesc'></textarea><br>\n";
	echo "<input type='hidden' name='c' value='$c'>";
	echo '<input type="hidden" name="action" value="addposition">';
	echo "Minimum number of referees required: <select name='jobrefnumber'>\n";
	echo "<option value='0'>0</option>\n";
	echo "<option value='1'>1</option>\n";
	echo "<option value='2'>2</option>\n";
	echo "<option value='3'>3</option>\n";
	echo "<option value='4'>4</option>\n";
	echo "<option value='5'>5</option>\n";
	echo "</select><br>\n";
	echo "<input type='submit' value='Add'>";
	echo "</form>";


	$positions = array_reverse(scandir(file_positionsdir()));
	foreach ($positions as $p) {
		if ( !is_numeric($p) ) continue;
		echo "<div id='job$p' class='job'>";
		echo "<a name='job$p'/>";

		$jobtitle = get_jobtitle($p);
		$jobdesc  = get_jobdesc($p);
		$jobdue   = get_jobdue($p);
		$jobrefnumber = get_jobrefnumber($p);
		$jobrefearly = get_jobrefearly($p);
		echo "<h4 onclick='toggle(\"jobinfo$p\");'><a href='#job$p'>" . $jobtitle . " - close date is " . $jobdue . "</a></h4>\n";
		echo "<div id='jobinfo$p' class='hide'>";

		$status = get_jobcondition($p);
		if ( $status == "open" ) {
			echo "Status: <font color=green> Open </font> \n";
			echo "<form style='display: inline;' action='admin.php?c=$c#job$p' method='post' name='formcloseposition'>";
			echo "<input type='hidden' name='action' value='closeposition'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='c' value='$c'>";
			echo "<input type='submit' style='background-color:black; color:white;' onclick=\"return confirm('This will close the position and delete all incomplete records. Are you sure?')\" value='Close'>";
			echo "</form>";
		} else {
			echo "Status: <font color=red> Closed </font> \n";
			echo "<form style='display: inline;' action='admin.php?c=$c#job$p' method='post' name='formopenposition'>";
			echo "<input type='hidden' name='action' value='openposition'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='c' value='$c'>";
			echo "<input type='submit' value='Open'>";
			echo "</form>";


			echo "<font color=red> Delete all non applicants </font> \n";
			echo "<form style='display: inline;' action='admin.php?c=$c' method='post' name='formdeletenonapplicantsposition'>";
			echo "<input type='hidden' name='action' value='deletenonapplicantsposition'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='c' value='$c'>";
			echo "<input type='submit' style='background-color:black; color:white;' onclick=\"return confirm('This will delete all incomplete records. Are you sure?')\" value='Delete'>";
			echo "</form>";

		}
		echo "<br>";
		echo "<br>";
		echo "<form style='display: inline;' action='admin.php?c=$c#job$p' method='post' name='formupdatedesc'>";
		echo "Description <textarea rows='20' cols='100' name='jobdesc'>$jobdesc</textarea>\n";
		echo "<input type='hidden' name='action' value='updatedescposition'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='c' value='$c'>";
		echo "<input type='submit' value='Update'>";
		echo "</form>";
	
		echo "<br>";
		echo "<br>";
		echo "<form style='display: inline;' action='admin.php?c=$c#job$p' method='post' name='formupdatedue'>";
		echo "Due date <input type='text' name='jobdue' maxlength='12' value='$jobdue' class='date'>\n";
		echo "<input type='hidden' name='action' value='updatedueposition'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='c' value='$c'>";
		echo "<input type='submit' value='Update'>";
		echo "</form>";

		echo "<br>";
		echo "<br>";
		echo "<form style='display: inline;' action='admin.php?c=$c#job$p' method='post' name='formupdaterefnumber'>";
		echo "Minimum number of referees required: <select name='jobrefnumber'>\n";
		for ($i = 0; $i<=5; $i++)
			echo "<option value='$i' ".(($i==$jobrefnumber)?"selected":"").">$i</option>\n";
		echo "</select>\n";
		echo "<input type='hidden' name='action' value='updaterefnumber'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='c' value='$c'>";
		echo "<input type='submit' value='Update'>";
		echo "</form>";

		echo "<br>";
		echo "<br>";
		echo "<form style='display: inline;' action='admin.php?c=$c#job$p' method='post' name='formupdaterefearly'>";
		echo "Get referees letters early in the process: <select name='jobrefearly'>\n";
		for ($i = 0; $i<=1; $i++)
			echo "<option value='$i' ".(($i==$jobrefearly)?"selected":"").">".($i=="0"?"Off":"On"). "</option>\n";
		echo "</select>\n";
		echo "<input type='hidden' name='action' value='updaterefearly'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='c' value='$c'>";
		echo "<input type='submit' value='Update'>";
		echo "</form>";
	
		echo "<br>";
		echo "<br>";
		echo "<div style='border: 1px solid lightgray; padding: 10px;'>";
		echo "<h5> Add an assessor </h5>\n";
		echo "<form action='admin.php?c=$c#job$p' method='post' name='formaddass'>\n";
		echo "Name <input type='text' name='assname' maxlength='50' value=''><br>\n";
		echo "Email <input type='email' name='assemail' maxlength='50' value=''><br>\n";
		echo "Role: <select name='assstatus'>\n";
		echo "<option value='normal'>Normal</option>\n";
		echo "<option value='chairman'>Chairman</option>\n";
		echo "<option value='coordinator'>Coordinator</option>\n";
		echo "</select>\n";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='c' value='$c'>";
		echo '<input type="hidden" name="action" value="add_assessor">';
		echo "<input type='submit' value='Add'>";
		echo "</form>";

		echo "<h5> List of current assessors </h5>\n";

		$assessors = get_assessors($p);
		$assessors = explode( "\n", $assessors );
		echo "<ul>\n";
		foreach ($assessors as $assemail) {
			if ( $assemail == "" ) continue;

			$a=do_hash($assemail);
			if (!valid_p_a($p, $a)) continue;

			$assname   = get_assname($p, $a);
			$assstatus = get_assstatus($p, $a);
			/*echo "<li>  $assname ($assemail)  <font color='blue'>$assstatus</font> ";
			echo "<form action='admin.php?c=$c#job$p' method='post' name='formdelass' style='display: inline;'>";
			echo "<input type='hidden' name='action' value='del_assessor'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='c' value='$c'>";
			echo "<input type='hidden' name='a' value='$a'>";
			echo "<input type='submit' value='Delete'>";
			echo "</form>";
			*/
			echo "<li>";
			echo "<form action='admin.php?c=$c#job$p' method='post' name='formupdateass'>\n";
			echo "$assname ($assemail) <select name='assstatus'>\n";
			echo "<option value='normal' ".($assstatus=="normal"?"selected":"").">Normal</option>\n";
			echo "<option value='chairman' ".($assstatus=="chairman"?"selected":"").">Chairman</option>\n";
			echo "<option value='coordinator' ".($assstatus=="coordinator"?"selected":"").">Coordinator</option>\n";
			echo "<option value='expired' ".($assstatus=="expired"?"selected":"").">Expired</option>\n";
			echo "<option value='deleted' ".($assstatus=="deleted"?"selected":"").">Deleted</option>\n";
			echo "</select>\n";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='c' value='$c'>";
			echo "<input type='hidden' name='a' value='$a'>";
			echo '<input type="hidden" name="action" value="update_assessor">';
			echo "<input type='submit' value='Update'>";
			echo "</form>";
			echo "</li>";
		}
		echo "</ul>";
		echo "</div>";
		echo "<br>";
		echo "<form action='admin.php?c=$c#job$p' method='post' name='formresults'>";
		echo "<input type='hidden' name='action' value='results'>";
		echo "<input type='hidden' name='c' value='$c'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='submit' value='View results'>";
		echo "</form>";
		echo "<br>";
	
		if (file_exists(file_jobmonitoringnationality($p))) {
			echo "<a href='".file_jobmonitoringnationality($p)."' target=blank>Nationality statistics</a><br>\n";
		}
		if (file_exists(file_jobmonitoringgender($p))) {
			echo "<a href='".file_jobmonitoringgender($p)."' target=blank>Gender statistics</a><br>\n";
		}

		echo "<br>";
		echo "<div style='border: 1px solid lightgray; padding: 10px;'>";
		echo "<h3>Template for emails</h3>";

		$templateapplicant = get_template_applicant($p);
		$templateuploaded  = get_template_uploaded($p);
		$templateresponse  = get_template_response($p);
		$templatereminder  = get_template_reminder($p);
		$templateref       = get_template_ref($p);
		$templateass       = get_template_ass($p);

		echo "<form style='display: inline;' action='admin.php?c=$c#job$p' method='post' name='formupdatetemplateapplicant'>";
		echo "Template Email for Applicants <textarea rows='6' cols='50' name='templateapplicant'>$templateapplicant</textarea>\n";
		echo "<input type='hidden' name='action' value='updatetemplateapplicant'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='c' value='$c'>";
		echo "<input type='submit' value='Update'>";
		echo "</form>";
		echo "<br>";

		echo "<form style='display: inline;' action='admin.php?c=$c#job$p' method='post' name='formupdatetemplateuploaded'>";
		echo "Template Email for Uploaded <textarea rows='6' cols='50' name='templateuploaded'>$templateuploaded</textarea>\n";
		echo "<input type='hidden' name='action' value='updatetemplateuploaded'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='c' value='$c'>";
		echo "<input type='submit' value='Update'>";
		echo "</form>";
		echo "<br>";

		echo "<form style='display: inline;' action='admin.php?c=$c#job$p' method='post' name='formupdatetemplateresponse'>";
		echo "Template Email for Response <textarea rows='6' cols='50' name='templateresponse'>$templateresponse</textarea>\n";
		echo "<input type='hidden' name='action' value='updatetemplateresponse'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='c' value='$c'>";
		echo "<input type='submit' value='Update'>";
		echo "</form>";
		echo "<br>";

		echo "<form style='display: inline;' action='admin.php?c=$c#job$p' method='post' name='formupdatetemplatereminder'>";
		echo "Template Email for Reminder <textarea rows='6' cols='50' name='templatereminder'>$templatereminder</textarea>\n";
		echo "<input type='hidden' name='action' value='updatetemplatereminder'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='c' value='$c'>";
		echo "<input type='submit' value='Update'>";
		echo "</form>";
		echo "<br>";

		echo "<form style='display: inline;' action='admin.php?c=$c#job$p' method='post' name='formupdatetemplateref'>";
		echo "Template Email for Referees <textarea rows='6' cols='50' name='templateref'>$templateref</textarea>\n";
		echo "<input type='hidden' name='action' value='updatetemplateref'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='c' value='$c'>";
		echo "<input type='submit' value='Update'>";
		echo "</form>";
		echo "<br>";

		echo "<form style='display: inline;' action='admin.php?c=$c#job$p' method='post' name='formupdatetemplateass'>";
		echo "Template Email for Assessors <textarea rows='6' cols='50' name='templateass'>$templateass</textarea>\n";
		echo "<input type='hidden' name='action' value='updatetemplateass'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='c' value='$c'>";
		echo "<input type='submit' value='Update'>";
		echo "</form>";
		echo "<br>";
		echo "</div>";
		echo "</div>";
		echo "</div>";
	}



	echo "<script>";
	echo "var v = window.location.hash.slice(1);";
	echo "var w = v.replace('job', 'jobinfo');";
	echo "console.log(v,w);";
	echo "toggle(w);";
	echo "</script>";
}




function sendadminmail( $adminemail ) {

	# email unique link to fill details
	mail_admin($adminemail);
}

if ( isset($argv) ){ 
	if (!is_dir( file_positionsdir() ) ) {
		mkdir( file_positionsdir() ) or die( "Cannot create positions folder ".file_positionsdir() );
		chown( file_positionsdir(), "www-data" );
	}
	if (!file_exists( file_admins() ) ) { 
		echo "Creating admin file ". file_admins() . "\n";
		echo "Please add email address per line and restart the command\n";
		$fh = fopen(file_admins(), 'w+') or die("can't open file ".file_admins());
		fclose($fh);
	} else {
		echo "Sending login addresses to admin emails\n";
		$admins = get_admins();
		$admins = explode( "\n", $admins );
		foreach ($admins as $adminemail) {
			if ( valid_email($adminemail) ) {
				echo " -> $adminemail\n";
				sendadminmail( $adminemail );
			}
		}
	}
	
} else if ($_SERVER['REQUEST_METHOD'] == "GET") {
        $c = $_GET['c'];
	valid_c($c) or die("Invalid Admin URL");

	get_page($c);

} else if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$do_page = true;

        $c = $_POST['c'];
	valid_c($c) or die("Invalid Admin URL");

        $action = $_POST['action'];

	# Validate p (except for addposition)
	switch ($action) {
		case "addposition" : break;
		default :
		        $p = $_POST['p'];
			valid_p($p) or die("Invalid Admin Position URL");
	}
	
	switch ($action) {
		case "addposition" :
		        $jobtitle     = $_POST['jobtitle'];
		        $jobdesc      = $_POST['jobdesc'];
		        $jobdue       = $_POST['jobdue'];
		        $jobrefnumber = $_POST['jobrefnumber'];
		        $jobrefearly  = "0";
			# TODO: Validate admin input

			$p = time();

			if (!is_dir(file_positionsdir()."/$p/")) mkdir(file_positionsdir()."/$p/") or die("can't make position folder");

			$fh = fopen(file_jobtitle($p), 'w+') or die("can't open file for job title");
			fwrite($fh, $jobtitle);
			fclose($fh);

			$fh = fopen(file_jobdesc($p), 'w+') or die("can't open file for job description");
			fwrite($fh, $jobdesc);
			fclose($fh);

			$fh = fopen(file_jobdue($p), 'w+') or die("can't open file for job due date");
			fwrite($fh, $jobdue);
			fclose($fh);

			$fh = fopen(file_jobrefnumber($p), 'w+') or die("can't open file for job referee number");
			fwrite($fh, $jobrefnumber);
			fclose($fh);

			$fh = fopen(file_jobrefearly($p), 'w+') or die("can't open file for job referee early");
			fwrite($fh, $jobrefearly);
			fclose($fh);

			$fh = fopen(file_jobstatus($p), 'w+') or die("can't open file for job status");
			fwrite($fh, "open");
			fclose($fh);

			$msg="Position ". get_jobtitle($p). " added";
			$error="";
			break;

		case "closeposition":
			$fh = fopen(file_jobstatus($p), 'w+');
			fwrite($fh, "close");
			fclose($fh);
			$msg="Position ". get_jobtitle($p). " closed";
			$error="";
			break;

		case "openposition":
			$fh = fopen(file_jobstatus($p), 'w+') or die("can't open file");
			fwrite($fh, "open");
			fclose($fh);
			$msg="Position ". get_jobtitle($p). " open";
			$error="";
			break;

		case "updatedescposition" :
		        $jobdesc      = $_POST['jobdesc'];
			$fh = fopen(file_jobdesc($p), 'w+') or die("can't open file for job description");
			fwrite($fh, $jobdesc);
			fclose($fh);

			$msg="Position ". get_jobtitle($p). " description updated";
			$error="";
			break;

		case "updatedueposition" :
		        $jobdue      = $_POST['jobdue'];
			$fh = fopen(file_jobdue($p), 'w+') or die("can't open file for job due date");
			fwrite($fh, $jobdue);
			fclose($fh);
			$msg="Position ". get_jobtitle($p). " due date updated";
			$error="";
			break;

		case "updaterefnumber" :
		        $jobrefnumber      = $_POST['jobrefnumber'];
			$fh = fopen(file_jobrefnumber($p), 'w+') or die("can't open file for job ref number");
			fwrite($fh, $jobrefnumber);
			fclose($fh);
			$msg="Position ". get_jobtitle($p). " ref number updated";
			$error="";
			break;

		case "updaterefearly" :
		        $jobrefearly      = $_POST['jobrefearly'];
			$fh = fopen(file_jobrefearly($p), 'w+') or die("can't open file for job ref early");
			fwrite($fh, $jobrefearly);
			fclose($fh);
			$msg="Position ". get_jobtitle($p). " ref early referee updated";
			$error="";
			break;

		case "updatetemplateapplicant" :
		        $templateapplicant     = $_POST['templateapplicant'];
			set_template_applicant($p, $templateapplicant);
			$msg="Position ". get_jobtitle($p). " template applicant updated";
			$error="";
			break;

		case "updatetemplateuploaded" :
		        $templateuploaded     = $_POST['templateuploaded'];
			set_template_uploaded($p, $templateuploaded);
			$msg="Position ". get_jobtitle($p). " template uploaded updated";
			$error="";
			break;

		case "updatetemplateresponse" :
		        $templateresponse     = $_POST['templateresponse'];
			set_template_response($p, $templateresponse);
			$msg="Position ". get_jobtitle($p). " template response updated";
			$error="";
			break;

		case "updatetemplatereminder" :
		        $templatereminder     = $_POST['templatereminder'];
			set_template_reminder($p, $templatereminder);
			$msg="Position ". get_jobtitle($p). " template reminder updated";
			$error="";
			break;

		case "updatetemplateref" :
		        $templateref     = $_POST['templateref'];
			set_template_ref($p, $templateref);
			$msg="Position ". get_jobtitle($p). " template ref updated";
			$error="";
			break;

		case "updatetemplateass" :
		        $templateass     = $_POST['templateass'];
		        $p = $_POST['p'];
			valid_p($p) or die("Invalid Admin Position URL");
			set_template_ass($p, $templateass);
			$msg="Position ". get_jobtitle($p). " template ass updated";
			$error="";
			break;

		case "deletenonapplicantsposition":
			$msg="";
			$error="";
			$applicants = get_applicants($p);
			$applicants = explode( "\n", $applicants );
			foreach ($applicants as $appemail) {
				$h=do_hash($appemail);
				if (!valid_p_h( $p, $h )) continue;
				if (!file_exists(file_apppdf($p, $h))) {
					$appname = get_appname($p, $h);
					$msg .= "<br>Applicant $appname deleted";
					rrmdir(file_appdir($p, $h));
				}
			}
			break;

		case "add_assessor" :
		        $assname   = filter_var($_POST['assname'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_LOW|FILTER_FLAG_ENCODE_AMP );
		        $assemail  = $_POST['assemail'];
		        $assstatus = $_POST['assstatus'];
			valid_name_email_status($assname, $assemail, $status);

			# add address to referees
			$assessors = get_assessors($p);
			if(strpos($assessors, $assemail) === false) {
				$fh = fopen(file_assessors($p), 'a');
				fwrite($fh, $assemail . "\n");
				fclose($fh);

				# add name to position/$p/$app/$ref/name
				$a=do_hash($assemail);
				if (!is_dir(file_jobpanel($p))) mkdir(file_jobpanel($p) ) or die("can't make panel folder");
				if (!is_dir(file_assdir($p, $a))) mkdir(file_assdir($p, $a) ) or die("can't make assessor folder");
				$fh = fopen(file_assname($p, $a), 'w+') or die("can't open file");
				set_assname($p, $a, $assname);
				set_assemail($p, $a, $assemail);
				set_assstatus($p, $a, $assstatus);

				sendassmail($p, $a);
				$msg="Assessor added and email sent";
				$error="";
			} else {
				$msg="";
				$error="Assessor already in the list";
			}

			break;

		case "sendassmail" :
		        $a  = $_POST['a'];
			valid_p_a($p, $a) or die("Invalid URL");

			$msg="Reminder sent to assessor";
			$error="";
			sendassmail($p, $a);
			break;

		case "del_assessor" :
		        $a  = $_POST['a'];
			valid_p_a($p, $a) or die("Invalid URL");

			$assemail = get_assemail($p, $a);

			# del address from assessors
			$assessors = explode( "\n", get_assessors($p) );
			$key = array_search( $assemail, $assessors );
			if ($key !== false) {
				unset($assessors[$key]);
				$assessors = implode( "\n", $assessors );
				$fh = fopen(file_assessors($p), 'w+') or die("can't open file");
				fwrite($fh, $assessors);
				fclose($fh);

				rrmdir(file_assdir($p, $a));

				$msg="Assessor has been deleted";
				$error="";
			}else{
				$msg="";
				$error="Assessor $assemail not found";
			}
			break;

		case "update_assessor" :
		        $a  = $_POST['a'];
			valid_p_a($p, $a) or die("Invalid URL");

			$assemail = get_assemail($p, $a);
				
		        $assstatus  = $_POST['assstatus'];


			switch ($assstatus) {
				case "normal":
				case "chairman":
				case "coordinator":
				case "expired":
					set_assstatus($p, $a, $assstatus);
					$msg="Assessor set as ".$assstatus;
					$error="";
					break;

				case "deleted":
					# del address from assessors
					$assessors = explode( "\n", get_assessors($p) );
					$key = array_search( $assemail, $assessors );
					if ($key !== false) {
						unset($assessors[$key]);
						$assessors = implode( "\n", $assessors );
						$fh = fopen(file_assessors($p), 'w+') or die("can't open file");
						fwrite($fh, $assessors);
						fclose($fh);

						rrmdir(file_assdir($p, $a));


						$msg="Assessor has been deleted";
						$error="";
					}else{
						$msg="";
						$error="Assessor $assemail not found";
					}
					break;
			}
			break;

		case "results" :
			get_results_page($p, $c, "admin" );
			$do_page=false;
			break;

		case "app_response" :
		        $h = $_POST['h'];
			valid_p_h($p, $h) or die("Invalid Applicant");
			
			
			$appshortlist = get_appshortlist($p, $h);
			if ($appshortlist == "") {

				$jobtitle = get_jobtitle($p);
				$appname  = get_appname($p, $h );
				$appemail = get_appemail($p, $h );
			
				mail_applicant_response($appname, $appemail, $jobtitle, $p, $h);

				$fh = fopen(file_appresponse($p, $h), 'w+') or die("Can't open response file");
				fwrite($fh, $c);
				fclose($fh);

				$msg="Response sent to applicant";
				$error="";

			} else {
				$msg="";
				$error="Applicant is in shortlist";
			}

			get_results_page($p, $c, "admin", $msg, $error);

			$do_page=false;
			break;

		case "app_massresponse" :
			$msg="";
			$error="";
			$applicants = get_applicants($p);
			$applicants = explode( "\n", $applicants );
			foreach ($applicants as $appemail) {
				$h=do_hash($appemail);
				if (!valid_p_h( $p, $h )) continue;

				$appresponse  = get_appresponse($p, $h);
				$appshortlist = get_appshortlist($p, $h);
				if (($appresponse == "") &&  ($appshortlist == "")) {

					$jobtitle = get_jobtitle($p);
					$appname  = get_appname($p, $h );
					$appemail = get_appemail($p, $h );
			
					mail_applicant_response($appname, $appemail, $jobtitle, $p, $h);

					$fh = fopen(file_appresponse($p, $h), 'w+') or die("Can't open response file");
					fwrite($fh, $c);
					fclose($fh);

					$msg .= "<br>Response sent to Applicant $appname";
					$error="";
				}
			}

			get_results_page($p, $c, "admin", $msg, $error);

			$do_page=false;
			break;


		case "app_shortlist" :
		        $h = $_POST['h'];
			valid_p_h($p, $h) or die("Invalid Applicant");

			# No email sent when placed on shortlist

			$appresponse  = get_appresponse($p, $h);
			if ($appresponse == "") {

				$fh = fopen(file_appshortlist($p, $h), 'w+') or die("Can't open shortlist file");
				fwrite($fh, $c);
				fclose($fh);

				$msg="Applicant added to shortlist";
				$error="";

			} else {
				$msg="";
				$error="Applicant has already received a response";
			}

			get_results_page($p, $c, "admin", $msg, $error);

			$do_page=false;
			break;

		case "app_removeshortlist" :
		        $h = $_POST['h'];
			valid_p_h($p, $h) or die("Invalid Applicant");

			# No email sent when placed on shortlist

			$appresponse  = get_appresponse($p, $h);
			if ($appresponse == "") {

				$fh = fopen(file_appshortlist($p, $h), 'w+') or die("Can't open shortlist file");
				fclose($fh);

				$msg="Applicant removed from shortlist";
				$error="";

			} else {
				$msg="";
				$error="Applicant has already received a response";
			}

			get_results_page($p, $c, "admin", $msg, $error);

			$do_page=false;
			break;

		case "app_reminder" :
		        $h = $_POST['h'];
			valid_p_h($p, $h) or die("Invalid Applicant");

			$jobtitle = get_jobtitle($p);
			$appname  = get_appname($p, $h );
			$appemail = get_appemail($p, $h );
		
			mail_applicant_reminder($appname, $appemail, $jobtitle, $p, $h);

			get_results_page($p, $c, "admin", $msg, $error);

			$do_page=false;
			break;

	}

	if ( $do_page )
		get_page($c, $msg, $error);
}

