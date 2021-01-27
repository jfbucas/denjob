<?php
include 'func.php';

$msg = "";
$error = "";

function get_page($c, $msg = "", $error = "") {
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
	echo "Description <textarea rows='6' cols='50' name='jobdesc'></textarea><br>\n";
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
	echo "<hr>";

	$positions = array_reverse(scandir(file_positionsdir()));
	foreach ($positions as $p) {
		if ( !is_numeric($p) ) continue;

		$jobtitle = get_jobtitle($p);
		$jobdesc  = get_jobdesc($p);
		echo "<h4>" . $jobtitle . "</h3>\n";

		$status = get_jobstatus($p);
		if ( $status == "open" ) {
			echo "Status: <font color=green> Open </font> \n";
			echo "<form style='display: inline;' action='admin.php?c=$c' method='post' name='formcloseposition'>";
			echo "<input type='hidden' name='action' value='closeposition'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='c' value='$c'>";
			echo "<input type='submit' style='background-color:black; color:white;' onclick=\"return confirm('This will close the position and delete all incomplete records. Are you sure?')\" value='Close'>";
			echo "</form>";
		} else {
			echo "Status: <font color=red> Closed </font> \n";
			echo "<form style='display: inline;' action='admin.php?c=$c' method='post' name='formopenposition'>";
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
	
		echo "<div style='border: 1px solid lightgray; padding: 10px;'>";
		echo "<h5> Add an assessor </h5>\n";
		echo "<form action='admin.php?c=$c' method='post' name='formaddass'>\n";
		echo "Name <input type='text' name='assname' maxlength='50' value=''><br>\n";
		echo "Email <input type='email' name='assemail' maxlength='50' value=''><br>\n";
		echo "Role: <select name='assstatus'>\n";
		echo "<option value='normal'>Normal</option>\n";
		echo "<option value='chairman'>Chairman</option>\n";
		echo "<option value='observer'>Coordinator</option>\n";
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
			echo "<li>  $assname ($assemail)  <font color='blue'>$assstatus</font> ";
			echo "<form action='admin.php?c=$c' method='post' name='formdelass' style='display: inline;'>";
			echo "<input type='hidden' name='action' value='del_assessor'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='c' value='$c'>";
			echo "<input type='hidden' name='a' value='$a'>";
			echo "<input type='submit' value='Delete'>";
			echo "</form>";
			echo "</li>";
		}
		echo "</ul>";
		echo "</div>";
		echo "<br>";
		echo "<form action='admin.php?c=$c' method='post' name='formresults'>";
		echo "<input type='hidden' name='action' value='results'>";
		echo "<input type='hidden' name='c' value='$c'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='submit' value='View results'>";
		echo "</form>";
		echo "<hr>";
	}
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
        $c = $_POST['c'];
	valid_c($c) or die("Invalid Admin URL");

	$do_page = true;

        $action = $_POST['action'];
	switch ($action) {

		case "closeposition":
		        $p = $_POST['p'];
			valid_p($p) or die("Invalid Admin Position URL");
			$fh = fopen(file_jobstatus($p), 'w+');
			fwrite($fh, "close");
			fclose($fh);
			$msg="Position ". get_jobtitle($p). " closed";
			$error="";

			break;

		case "openposition":
		        $p = $_POST['p'];
			valid_p($p) or die("Invalid Admin Position URL");
			$fh = fopen(file_jobstatus($p), 'w+') or die("can't open file");
			fwrite($fh, "open");
			fclose($fh);
			$msg="Position ". get_jobtitle($p). " open";
			$error="";
			break;


		case "deletenonapplicantsposition":
		        $p = $_POST['p'];
			valid_p($p) or die("Invalid Admin Position URL");
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

		case "addposition" :
		        $jobtitle     = $_POST['jobtitle'];
		        $jobdesc      = $_POST['jobdesc'];
		        $jobrefnumber = $_POST['jobrefnumber'];
			# TODO: Validate admin input

			$p = time();

			if (!is_dir(file_positionsdir()."/$p/")) mkdir(file_positionsdir()."/$p/") or die("can't make position folder");

			$fh = fopen(file_jobtitle($p), 'w+') or die("can't open file for job title");
			fwrite($fh, $jobtitle);
			fclose($fh);

			$fh = fopen(file_jobdesc($p), 'w+') or die("can't open file for job description");
			fwrite($fh, $jobdesc);
			fclose($fh);

			$fh = fopen(file_jobrefnumber($p), 'w+') or die("can't open file for job referee number");
			fwrite($fh, $jobrefnumber);
			fclose($fh);

			$fh = fopen(file_jobstatus($p), 'w+') or die("can't open file for job status");
			fwrite($fh, "open");
			fclose($fh);

			$msg="Position ". get_jobtitle($p). " added";
			$error="";
			break;

		case "add_assessor" :
		        $p = $_POST['p'];
			valid_p($p) or die("Invalid Admin Position URL");

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
		        $p = $_POST['p'];
			valid_p($p) or die("Invalid Admin Position URL");

		        $a  = $_POST['a'];
			valid_p_a($p, $a) or die("Invalid URL");

			$msg="Reminder sent to assessor";
			$error="";
			sendassmail($p, $a);
			break;

		case "del_assessor" :
		        $p = $_POST['p'];
			valid_p($p) or die("Invalid Admin Position URL");

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

		case "results" :
		        $p = $_POST['p'];
			valid_p($p) or die("Invalid Admin Position URL");

			get_results_page($p, $c, "admin" );
			$do_page=false;
			break;

		case "app_response" :
		        $p = $_POST['p'];
		        $h = $_POST['h'];
			valid_p_h($p, $h) or die("Invalid Applicant");

			$jobtitle = get_jobtitle($p);
			$appname  = get_appname($p, $h );
			$appemail = get_appemail($p, $h );
		
			mail_applicant_response($appname, $appemail, $jobtitle, $p, $h);

			$fh = fopen(file_appresponse($p, $h), 'w+') or die("Can't open response file");
			fwrite($fh, $c);
			fclose($fh);

			get_results_page($p, $c, "admin");

			$do_page=false;
			break;

		case "app_reminder" :
		        $p = $_POST['p'];
		        $h = $_POST['h'];
			valid_p_h($p, $h) or die("Invalid Applicant");

			$jobtitle = get_jobtitle($p);
			$appname  = get_appname($p, $h );
			$appemail = get_appemail($p, $h );
		
			mail_applicant_reminder($appname, $appemail, $jobtitle, $p, $h);

			get_results_page($p, $c, "admin");

			$do_page=false;
			break;

	}

	if ( $do_page )
		get_page($c, $msg, $error);
}

