<?php
include 'func.php';

$msg = "";
$error = "";

function get_page($p, $a, $msg = "", $error = "") {
	$assname   = get_assname($p, $a);
	$assemail  = get_assemail( $p, $a); 
	$assstatus = get_assstatus( $p, $a); 

	echo "<head>";
	echo "<link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css'>";
	echo "<script type='text/javascript' src='https://code.jquery.com/jquery-3.3.1.js'></script>";
	echo "<script type='text/javascript' src='https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js'></script>";
	echo "</head>";
	echo "<body>";

	switch ($assstatus) {
		case "chairman":
			echo "<h2> Welcome Chairman $assname, you will be able to configure this position </h2>\n";
			break;
		case "coordinator":
			echo "<h2> Welcome $assname, you will be able to coordinate the applicants </h2>\n";
			break;
		case "normal":
			echo "<h2> Welcome $assname, you will be able to review the applicants </h2>\n";
			break;
		case "deleted":
		case "expired":
			echo "<h2> Dear $assname, your link has now expired </h2>\n";
			die("");
			break;
	}

	if ($msg != "") {
		echo "<h4> <font color=green>$msg</font> </h4>";
	}
	if ($error != "") {
		echo "<h4> <font color=red>$error</font> </h4>";
	}

	if (($assstatus == "chairman")or($assstatus == "coordinator")) {
		$status = get_jobcondition($p);
		if ( $status == "open" ) {
			echo "Status: <font color=green> Open </font> \n";
			echo "<form style='display: inline;' action='assessor.php?a=$a' method='post' name='formcloseposition'>";
			echo "<input type='hidden' name='action' value='closeposition'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='a' value='$a'>";
			echo "<input type='submit' value='Close'>";
			echo "</form>";
		} else {
			echo "Status: <font color=red> Closed </font> \n";
			echo "<form style='display: inline;' action='assessor.php?a=$a' method='post' name='formopenposition'>";
			echo "<input type='hidden' name='action' value='openposition'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='a' value='$a'>";
			echo "<input type='submit' value='Open'>";
			echo "</form>";
		}
		echo "<br>";
		echo "<br>";
		echo "<form action='assessor.php?a=$a' method='post' name='formresults'>";
		echo "<input type='hidden' name='action' value='results'>";
		echo "<input type='hidden' name='a' value='$a'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='submit' value='As $assstatus, you can view the results'>";
		echo "</form>";
		echo "<br>";
	}
	
	#echo "<h3> Applicants </h3>\n";
	#echo "<h5> List of current referees </h5>\n";

	$applicants = get_applicants($p);
	$applicants = explode( "\n", $applicants );
	echo "<table id='applicants' style='border: 1px solid lightgray; border-collapse: collapse;'>\n";
	echo "<thead><tr style='border: 1px solid lightgray; padding:10px;'><th align=right>#</th><th>Applicants</th><th>Referees</th>";
	if ($assstatus != "coordinator") 
		echo "<th align=center>Qualifies ?<br><font color=green>Yes</font> / <font color=orange>Maybe</font> / <font color=red>No</font></th>";
	echo "</tr></thead>\n";
	echo "<tbody>\n";

	$counter=1;
	foreach ($applicants as $appemail) {
		$h=do_hash($appemail);
		if (!valid_p_h( $p, $h )) continue;

		$appname = get_appname($p, $h);
		echo "<tr style='border: 1px solid lightgray; padding:10px;'><td valign=middle align=right><a name='applicant$counter'>$counter</a></td><td valign=middle style='padding:10px'>";
		if (file_exists(file_apppdf($p, $h))) {
			echo "<div><a href=". file_apppdf($p, $h)."?t=".time()." > $appname &lt;$appemail&gt; <font color=red size=+2>&#128442;</font><!--img height=20px width=20px src=pdf.png--> </a></div>\n";
		} else {
			echo "<div alt='No CV available yet'> $appname &lt;$appemail&gt;</div>\n";
		}
		echo "</td><td valign=middle style='padding:10px'><ul style='margin:0px'>";

		$referees = get_referees($p, $h);
		$referees = explode( "\n", $referees );
		foreach ($referees as $refemail) {
			$rh=do_hash($refemail);
			if (!valid_p_h_rh( $p, $h, $rh )) continue;

			$refname  = get_refname($p, $h, $rh);
			$refemail = get_refemail($p, $h, $rh);
			if (file_exists(file_refpdf($p, $h, $rh))) {
				echo "<li><div><a href=".file_refpdf($p, $h, $rh)."?t=".time()." > $refname &lt;$refemail&gt; <font color=red size=+2>&#128442;</font><!--img height=20px width=20px src=pdf.png--></a></div></li>\n";

			} else {
				if (($assstatus == "chairman")or($assstatus == "coordinator")) {
					echo "<li><div alt='no reference letter available yet'>$refname &lt;$refemail&gt;";
					echo "<form style='display:inline-block; float:right;' action='assessor.php?p=$p&a=$a#applicant$counter' method='post' name='formsendrefmail'>";
					echo "<input type='hidden' name='action' value='sendrefmail'>";
					echo "<input type='hidden' name='p' value='$p'>";
					echo "<input type='hidden' name='a' value='$a'>";
					echo "<input type='hidden' name='h' value='$h'>";
					echo "<input type='hidden' name='rh' value='$rh'>";
					echo "<input type='submit' style='background-color:darkblue; color:white;' onclick=\"return confirm('Send email to referee?')\" value='Email Referee'>";
					echo "</form>";
					echo "</div><div style='clear:right'></div></li>";
				} else {
					echo "<li><div alt='no reference letter available yet'>$refname &lt;$refemail&gt;</div></li>";
				}
			}
		}
		echo "</ul></td>\n";

		if ($assstatus != "coordinator") {
			echo "<td align=center valign=middle style='padding:10px'>";
			$v = get_appscore($p, $h, $a);
			if ($v == "Y") { $ycolor = "#24ff24"; }else{ $ycolor = "#004b00"; }
			if ($v == "M") { $mcolor = "#ff9224"; }else{ $mcolor = "#b75c00"; }
			if ($v == "N") { $ncolor = "#ff2424"; }else{ $ncolor = "#4b0000"; }

			echo "<form style='display: inline;' action='assessor.php?p=$p&a=$a#applicant$counter' method='post' name='formqualifies'>";
			echo "<input type='hidden' name='action' value='app_qualifies'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='a' value='$a'>";
			echo "<input type='hidden' name='h' value='$h'>";
			echo "<input type='hidden' name='v' value='Y'>";
			echo "<input type='submit' style='background-color:$ycolor; color:white;' value=' '>";
			echo "</form>";

			echo "<form style='display: inline;' action='assessor.php?p=$p&a=$a#applicant$counter' method='post' name='formqualifies'>";
			echo "<input type='hidden' name='action' value='app_qualifies'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='a' value='$a'>";
			echo "<input type='hidden' name='h' value='$h'>";
			echo "<input type='hidden' name='v' value='M'>";
			echo "<input type='submit' style='background-color:$mcolor; color:white;' value=' '>";
			echo "</form>";

			echo "<form style='display: inline;' action='assessor.php?p=$p&a=$a#applicant$counter' method='post' name='formqualifies'>";
			echo "<input type='hidden' name='action' value='app_qualifies'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='a' value='$a'>";
			echo "<input type='hidden' name='h' value='$h'>";
			echo "<input type='hidden' name='v' value='N'>";
			echo "<input type='submit' style='background-color:$ncolor; color:white;' value=' '>";
			echo "</form>";
			echo "</td>";
		}
		echo "</tr>";
		$counter++;
	}
	echo "</tbody>";
	echo "</table>";
	echo '<script type="text/javascript">
		$(document).ready(function() {
			$("#applicants").DataTable({
				"paging": false,
				"order": [ [2, "desc"] ],
			});
		} );
		</script>';
	echo "<br>";
	echo "<br>";
	echo "<br>";
	echo "<form action='assessor.php?p=$p&a=$a' method='post' name='formrefresh'>";
	echo "<input type='hidden' name='action' value='refresh'>";
	echo "<input type='hidden' name='p' value='$p'>";
	echo "<input type='hidden' name='a' value='$a'>";
	echo "<input type='submit' value='Refresh page'>";
	echo "</form>";
	
	echo "<hr>\n";
	
	if ($assstatus == "chairman") {
		echo "<div style='border: 1px solid lightgray; padding: 10px;'>";
		echo "<h5> Add an assessor </h5>\n";
		echo "<form action='assessor.php?a=$a' method='post' name='formaddass'>\n";
		echo "Name <input type='text' name='assname' maxlength='50' value=''><br>\n";
		echo "Email <input type='email' name='assemail' maxlength='50' value=''><br>\n";
		echo "Role: <select name='assstatus'>\n";
		echo "<option value='normal'>Normal</option>\n";
		echo "<option value='chairman'>Chairman</option>\n";
		echo "<option value='coordinator'>Coordinator</option>\n";
		echo "</select>\n";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='a' value='$a'>";
		echo '<input type="hidden" name="action" value="add_assessor">';
		echo "<input type='submit' value='Add'>";
		echo "</form>";

		echo "<h5> List of current assessors </h5>\n";

		$assessors = get_assessors($p);
		$assessors = explode( "\n", $assessors );
		echo "<ul>\n";
		foreach ($assessors as $assemail) {
			if ( $assemail == "" ) continue;

			$ass=do_hash($assemail);
			if (!valid_p_a($p, $ass)) continue;

			$assname   = get_assname($p, $ass);
			$assstatus = get_assstatus($p, $ass);
			echo "<li>  $assname ($assemail)  <font color='blue'>$assstatus</font> ";
			echo "<form action='assessor.php?a=$a' method='post' name='formdelass' style='display: inline;'>";
			echo "<input type='hidden' name='action' value='del_assessor'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='a' value='$a'>";
			echo "<input type='hidden' name='assdel' value='$ass'>";
			echo "<input type='submit' value='Delete'>";
			echo "</form>";
			echo "</li>";
		}
		echo "</ul>";
		echo "</div>";


		$jobtitle = get_jobtitle($p);
		$jobdesc  = get_jobdesc($p);
		$jobdue   = get_jobdue($p);
		$jobrefearly = get_jobrefearly($p);

		echo "<br>";
		echo "<br>";
		echo "<form style='display: inline;' action='assessor.php?a=$a' method='post' name='formupdatedesc'>";
		echo "Description <textarea rows='6' cols='50' name='jobdesc'>$jobdesc</textarea>\n";
		echo "<input type='hidden' name='action' value='updatedescposition'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='a' value='$a'>";
		echo "<input type='submit' value='Update'>";
		echo "</form>";
		echo "<br>";
		echo "<br>";
		echo "<form style='display: inline;' action='assessor.php?a=$a' method='post' name='formupdatedue'>";
		echo "Due date <input type='text' name='jobdue' maxlength='12' value='$jobdue'>\n";
		echo "<input type='hidden' name='action' value='updatedueposition'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='a' value='$a'>";
		echo "<input type='submit' value='Update'>";
		echo "</form>";
		echo "<br>";
		echo "<br>";
		echo "<form style='display: inline;' action='assessor.php?a=$a' method='post' name='formupdaterefearly'>";
		echo "Get referees letters early in the process: <select name='jobrefearly'>\n";
		for ($i = 0; $i<=1; $i++)
			echo "<option value='$i' ".(($i==$jobrefearly)?"selected":"").">".($i=="0"?"Off":"On"). "</option>\n";
		echo "</select>\n";
		echo "<input type='hidden' name='action' value='updaterefearly'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='a' value='$a'>";
		echo "<input type='submit' value='Update'>";
		echo "</form>";
	}

	echo "<hr>\n";
	show_job_title_description($p);
}



if ($_SERVER['REQUEST_METHOD'] == "GET") {
        $p = $_GET['p'];
        $a = $_GET['a'];
	valid_p_a($p, $a) or die("Invalid URL");

	get_page($p, $a);

} else if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $p = $_POST['p'];
        $a = $_POST['a'];
	valid_p($p) or die("Invalid Position URL");
	valid_p_a($p, $a) or die("Invalid Assessor URL");

	$do_page = true;

        $action = $_POST['action'];
	switch ($action) {
		case "closeposition":
			# TODO: test if assessor is chairman
			$fh = fopen(file_jobstatus($p), 'w+');
			fwrite($fh, "close");
			fclose($fh);
			$msg="Position ". get_jobtitle($p). " closed";
			$error="";
			break;

		case "openposition":
			# TODO: test if assessor is chairman
			$fh = fopen(file_jobstatus($p), 'w+') or die("can't open file");
			fwrite($fh, "open");
			fclose($fh);
			$msg="Position ". get_jobtitle($p). " open";
			$error="";
			break;

		case "updatedescposition" :
		        $jobdesc      = $_POST['jobdesc'];
			# TODO: test if assessor is chairman
			$fh = fopen(file_jobdesc($p), 'w+') or die("can't open file for job description");
			fwrite($fh, $jobdesc);
			fclose($fh);

			$msg="Position ". get_jobtitle($p). " description updated";
			$error="";
			break;

		case "updatedueposition" :
		        $jobdue      = $_POST['jobdue'];
			# TODO: test if assessor is chairman
			$fh = fopen(file_jobdue($p), 'w+') or die("can't open file for job due date");
			fwrite($fh, $jobdue);
			fclose($fh);

			$msg="Position ". get_jobtitle($p). " due date updated";
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


		case "app_qualifies":
		        $h = $_POST['h'];
		        $v = $_POST['v'];
			valid_p_h($p, $h) or die("Invalid Applicant");
			if (!(( $v == "Y" ) || ( $v == "N" ) || ( $v == "M" )) ) die("Invalid score");

			$fh = fopen(file_appscore($p, $h, $a), 'w+') or die("Can't open score file");
			fwrite($fh, $v);
			fclose($fh);

			break;

		case "results" :
			get_results_page($p, $a, "assessor");
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
				fwrite($fh, $a);
				fclose($fh);

				$msg="Response sent to applicant";
				$error="";

			} else {
				$msg="";
				$error="Applicant is in shortlist";
			}

			get_results_page($p, $a, "assessor", $msg, $error);

			$do_page=false;
			break;

		case "app_massresponse" :
			$msg="";
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
					fwrite($fh, $a);
					fclose($fh);

					$msg .= "<br>Response sent to Applicant $appname";
				}
			}

			get_results_page($p, $a, "assessor", $msg, $error);

			$do_page=false;
			break;

		case "app_shortlist" :
		        $h = $_POST['h'];
			valid_p_h($p, $h) or die("Invalid Applicant");

			# No email sent when placed on shortlist

			$appresponse  = get_appresponse($p, $h);
			if ($appresponse == "") {

				$fh = fopen(file_appshortlist($p, $h), 'w+') or die("Can't open shortlist file");
				fwrite($fh, $a);
				fclose($fh);

				$msg="Applicant added to shortlist";
				$error="";

			} else {
				$msg="";
				$error="Applicant has already received a response";
			}

			get_results_page($p, $a, "assessor", $msg, $error);

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

			get_results_page($p, $a, "assessor", $msg, $error);

			$do_page=false;
			break;


		case "app_reminder" :
		        $h = $_POST['h'];
			valid_p_h($p, $h) or die("Invalid Applicant");

			$jobtitle = get_jobtitle($p);
			$appname  = get_appname($p, $h );
			$appemail = get_appemail($p, $h );
		
			mail_applicant_reminder($appname, $appemail, $jobtitle, $p, $h);

			$msg="Applicant reminded";

			get_results_page($p, $a, "assessor", $msg, $error);

			$do_page=false;
			break;

		case "add_assessor" :
			# TODO: check assessor is chairman

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
				$assadd=do_hash($assemail);
				if (!is_dir(file_jobpanel($p))) mkdir(file_jobpanel($p) ) or die("can't make panel folder");
				if (!is_dir(file_assdir($p, $assadd))) mkdir(file_assdir($p, $assadd) ) or die("can't make assessor folder");
				$fh = fopen(file_assname($p, $assadd), 'w+') or die("can't open file");
				set_assname($p, $assadd, $assname);
				set_assemail($p, $assadd, $assemail);
				set_assstatus($p, $assadd, $assstatus);

				sendassmail($p, $assadd);
				$msg="Assessor added and email sent";
				$error="";
			} else {
				$msg="";
				$error="Assessor already in the list";
			}

			break;
			
		case "del_assessor" :
			# TODO: check assessor is chairman

		        $assdel  = $_POST['assdel'];
			valid_p_a($p, $assdel) or die("Invalid assessor to delete URL");

			$assemail = get_assemail($p, $assdel);

			# del address from assessors
			$assessors = explode( "\n", get_assessors($p) );
			$key = array_search( $assemail, $assessors );
			if ($key !== false) {
				unset($assessors[$key]);
				$assessors = implode( "\n", $assessors );
				$fh = fopen(file_assessors($p), 'w+') or die("can't open file");
				fwrite($fh, $assessors);
				fclose($fh);

				rrmdir(file_assdir($p, $assdel));

				$msg="Assessor has been deleted";
				$error="";
			}else{
				$msg="";
				$error="Assessor $assemail not found";
			}
			break;

		case "sendrefmail" :
		        $h = $_POST['h'];
		        $rh = $_POST['rh'];
			valid_p_h_rh($p, $h, $rh) or die("Invalid Referee URL");

			$msg="Email sent to referee";
			$error="";
			sendrefmail($p, $h, $rh);

			break;


		case "refresh" : break;
	}

	if ( $do_page )
		get_page($p, $a, $msg, $error);

}

