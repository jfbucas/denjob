<?php
include 'func.php';

$msg = "";
$error = "";

function get_page($p, $h, $msg = "", $error = "") {
	$appname  = get_appname($p, $h );
	$appemail = get_appemail($p, $h );
		
	$status = get_jobcondition($p);
	if ( ! $status ) {
		echo "<h2>Welcome $appname, the position is now closed </h2>\n";
		return;
	}

	echo "<h2>Welcome $appname, you will be able to manage your application below </h2>\n";

	if ($msg != "") {
		echo "<h4> <font color=green><i>$msg</i></font> </h4>";
	}
	if ($error != "") {
		echo "<h4> <font color=red><i>$error</i></font> </h4>";
	}

	echo "<div style='border: 1px solid lightgray; padding: 10px;'>";
	echo "<h3> File upload </h3>\n";
	echo '<form enctype="multipart/form-data" action="applicant.php?p='.$p.'&h='.$h.'" method="post" name="form_resume">';
	echo 'Please upload the required documents as one PDF file (Max:20MB) (refer to job description below) <br>';
	echo 'You can use online resources like <a href="https://smallpdf.com/merge-pdf">SmallPDF</a> to merge your PDFs<br>';
	echo '<br>';
	echo '<br>';
	echo '<input type="hidden" name="action" value="upload_resume">';
	echo '<input type="hidden" name="p" value="' . $p . '">';
	echo '<input type="hidden" name="h" value="' . $h . '">';
	echo '<input type="hidden" name="MAX_FILE_SIZE" value="20000000" />';
	echo '<input name="resume" size="40" maxlength="50" type="file" accept="application/pdf">';
	echo "<input type='submit' value='Upload'>";
	echo '</form>';

	if (file_exists(file_apppdf($p, $h))) {
		echo "<a target=_blank href=".file_apppdf($p, $h)."?t=".time()." >  <font color=red size=+8>&#128442;</font><!--img height=50px width=50px src=pdf.png--><br>Application PDF </a><br>";
	}
	echo "</div>";
	echo "<br>";
	
	$jobrefnumber = get_jobrefnumber($p);
	$refcount = 0;
	if ($jobrefnumber > 0) {
		echo "<div style='border: 1px solid lightgray; padding: 10px;'>";
		echo "<h3> $jobrefnumber Referee".($jobrefnumber>1?"s":"")." required for this position</h3>\n";
		
		$jobrefearly = get_jobrefearly($p);
		echo "<h5> Add a referee </h5>\n";
		if ( $jobrefearly == "1" ) {
			echo "An email will be sent automatically to the address provided.\n";
		} else {
			echo "<i>Your nominated referees will be contacted at the appropriate stage of the assessment process by our HR section.<br>You will be notified in advance of us contacting your referees.</i><br><br>\n";
		}
		echo "<form action='applicant.php?p=$p&h=$h' method='post' name='formaddref'>\n";
		echo "Referee Name <input type='text' name='refname' maxlength='50' value=''><br>\n";
		echo "Referee Email <input type='email' name='refemail' maxlength='50' value=''><br>\n";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='h' value='$h'>";
		echo '<input type="hidden" name="action" value="add_referee">';
		echo "<input type='submit' value='Add'>";
		echo "</form>";

		echo "<h5> List of current referees </h5>\n";

		$referees = get_referees($p, $h);
		$referees = explode( "\n", $referees );
		$refname = "";
		echo "<ul>\n";
		foreach ($referees as $refemail) {
			if ( $refemail == "" ) continue;

			$rh=do_hash($refemail);

			$refname = get_refname($p, $h, $rh);
			echo "<li>  $refname ($refemail) ";
			if (file_exists(file_refpdf($p, $h, $rh))) {
				echo "<font color=green>Cover letter received</font>";
			} else {
				if ( $jobrefearly == "1" ) {
					echo "<form action='applicant.php?p=$p&h=$h' method='post' name='formsendrefmail'>";
					echo "<input type='hidden' name='action' value='sendrefmail'>";
					echo "<input type='hidden' name='p' value='$p'>";
					echo "<input type='hidden' name='h' value='$h'>";
					echo "<input type='hidden' name='rh' value='$rh'>";
					echo "<input type='submit' value='Re-Send link'>";
					echo "</form>";
				}
			}

			echo "<form action='applicant.php?p=$p&h=$h' method='post' name='formdelref'>";
			echo "<input type='hidden' name='action' value='del_referee'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='h' value='$h'>";
			echo "<input type='hidden' name='rh' value='$rh'>";
			echo "<input type='submit' value='Delete'>";
			echo "</form>";

			echo "</li>";
			$refcount ++;
		}
		echo "</ul>";
		echo "</div>";
	}

	if (!file_exists(file_appmonitoring($p, $h))) {
		echo "<br>";
		echo "<div style='border: 1px solid lightgray; padding: 10px;'>";
		echo "<h3> Recruitment Equality Monitoring Information (Optional) </h3>\n";
		echo "<i>Candidates are requested to complete this information which is required for us to monitor implementation of our Gender, Diversity and Inclusion Strategy.<br>";
		echo "Please note this data is solely for administrative records to enable us to measure achievement of Gender, Diversity & Inclusion objectives.<br>";
		echo "It does not form part of the candidate's application and is not presented or considered at any stage of the selection process.</i><br>\n";
		echo '<br>';
		echo '<form action="applicant.php?p='.$p.'&h='.$h.'" method="post" name="formmonitoring">';
		echo "Nationality ".select_nationality()."<br>\n";
		echo '<br>';
		echo "Gender ".select_gender()." ";
		echo "if you selected Other, please specify: <input type='text' name='genderother' maxlength='50' value=''><br>\n";
		echo '<br>';
		echo '<input type="hidden" name="action" value="submit_monitoring">';
		echo '<input type="hidden" name="p" value="' . $p . '">';
		echo '<input type="hidden" name="h" value="' . $h . '">';
		echo "<input type='submit' value='Submit'>";
		echo '</form>';

		echo "</div>";
		echo "<br>";
	}
	
	$finalizable="disabled";
	if (file_exists(file_apppdf($p, $h)) && ($refcount >= $jobrefnumber)) {
		$finalizable="";
	}
	echo "<br>";
	echo "<form action='applicant.php?p=$p&h=$h' method='post' name='formfinish'>";
	echo "<input type='hidden' name='action' value='finish'>";
	echo "<input type='hidden' name='p' value='$p'>";
	echo "<input type='hidden' name='h' value='$h'>";
	echo "<input type='submit' value='Finalize' $finalizable>";
	echo "</form>";

	echo "<br>";
	echo "<br>";
	echo "<br>";
	echo "<form action='applicant.php?p=$p&h=$h' method='post' name='formrefresh'>";
	echo "<input type='hidden' name='action' value='refresh'>";
	echo "<input type='hidden' name='p' value='$p'>";
	echo "<input type='hidden' name='h' value='$h'>";
	echo "<input type='submit' value='Refresh page'>";
	echo "</form>";

	echo "<hr>\n";
	show_job_title_description($p);
}



if ($_SERVER['REQUEST_METHOD'] == "GET") {
        $p = $_GET['p'];
        $h = $_GET['h'];
	valid_p_h($p, $h) or die("Invalid URL GET");

	get_page($p, $h);

} else if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $p = $_POST['p'];
        $h = $_POST['h'];
	valid_p_h($p, $h) or die("Invalid URL POST - check php.ini : post_max_size");

	$action = "";
	$status = get_jobcondition($p);
	if ( $status ) {
	        $action = $_POST['action'];
	}

	switch ($action) {
		case "upload_resume" :
#			$msg="CV uploaded";
			$msg="Application uploaded. Please use the Application PDF link below to check that your upload is correct and complete, and re-upload the application if necessary";
			$error="";
			if (! move_uploaded_file( $_FILES['resume']['tmp_name'], file_apppdf($p, $h)) ) {
				$msg="";
				$error="CV couldn't be uploaded";
			}			
			if (! is_pdf( file_apppdf($p, $h) ) ) {
				unlink( file_apppdf($p, $h) );
				$msg="";
				$error="CV was not in PDF format";
			}

			if ( $error == "") {
				mail_applicant_uploaded($p, $h);
			}
			break;
		case "add_referee" :

			$refname   = filter_var($_POST['refname'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_LOW|FILTER_FLAG_ENCODE_AMP );
		        $refemail = $_POST['refemail'];
			valid_name_email($refname, $refemail) or die("Invalid Referee Name/Email $refname/$refemail");

			# add address to referees
			$referees = get_referees($p, $h);
			if (strpos($referees, $refemail) === false) {
				$fh = fopen(file_referees($p, $h), 'a') or die("can't open file");
				fwrite($fh, $refemail . "\n");
				fclose($fh);

				# add name to position/$p/$app/$ref/name
				$rh = do_hash( $refemail );
				if (!is_dir(file_refdir($p, $h, $rh))) mkdir(file_refdir($p, $h, $rh)) or die("can't make referee folder");
				$fh = fopen(file_refname($p, $h, $rh), 'w+') or die("can't open file");
				fwrite($fh, $refname);
				fclose($fh);
				$fh = fopen(file_refemail($p, $h, $rh), 'w+') or die("can't open file");
				fwrite($fh, $refemail);
				fclose($fh);

				$jobrefearly = get_jobrefearly($p);
				if ($jobrefearly == "1") {
					sendrefmail($p, $h, $rh);
					$msg="Referee added and email sent";
				} else {
					$msg="Referee added";
				}
				$error="";
			} else {
				$msg="";
				$error="Referee already in the list";
			}

			break;

		case "sendrefmail" :

		        $rh = $_POST['rh'];
			valid_p_h_rh($p, $h, $rh) or die("Invalid URL");

			$jobrefearly = get_jobrefearly($p);
			if ($jobrefearly == "1") {
				sendrefmail($p, $h, $rh);
				$msg="Reminder sent to referee";
			} else {
				$msg="Referee reminders are disabled";
			}
			$error="";

			break;

		case "del_referee" :

		        $rh = $_POST['rh'];
			valid_p_h_rh($p, $h, $rh) or die("Invalid URL");

			$refemail = get_refemail($p, $h, $rh);

			# deld address from referees
			$referees = explode( "\n", get_referees($p, $h) );
			$key = array_search( $refemail, $referees );
			if ($key !== false) {
				unset($referees[$key]);
				$referees = implode( "\n", $referees );
				$fh = fopen(file_referees($p, $h), 'w+') or die("can't open file");
				fwrite($fh, $referees);
				fclose($fh);

				$msg="Referee has been deleted";
				$error="";
			}else{
				$msg="";
				$error="Referee $refemail not found";
			}
			break;


		case "submit_monitoring":
		        $nationality = clean($_POST['nationality']);
		        $gender      = clean($_POST['gender']);
		        $genderother = clean($_POST['genderother']);

			# TODO validate nationality, gender

			$fh = fopen(file_jobmonitoringnationality($p), 'a') or die("can't open file monitoring nationality");
			fwrite($fh, $nationality . "\n");
			fclose($fh);

			$fh = fopen(file_jobmonitoringgender($p), 'a') or die("can't open file monitoring gender");
			fwrite($fh, $gender. ($genderother!=""? " ". $genderother:"") . "\n");
			fclose($fh);

			$fh = fopen(file_appmonitoring($p, $h), 'w+') or die("Can't open monitoring file");
			fwrite($fh, $h);
			fclose($fh);

			$msg="Thank you for sharing this information with us.";
			$error="";

			break;


		case "finish" :

#			$msg="Thank you for your application. This page will be accessible until the final review by the assessors. You may come back and change your CV and referees until then.";

			$jobtitle = get_jobtitle($p);
			$msg="Thank you for your application for the position of $jobtitle We will be in touch in due course.";
			$error="";

			break;

		case "refresh" : break;
	}

	get_page($p, $h, $msg, $error);

}


