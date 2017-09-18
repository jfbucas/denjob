<?php
include 'func.php';


function get_page($p, $h, $msg = "", $error = "") {
	$appname  = get_appname($p, $h );
	$appemail = get_appemail($p, $h );

	echo "<h2>Welcome $appname, you will be able to manage your application below <h2>\n";

	if ($msg != "") {
		echo "<h4> <font color=green>$msg</font> </h4>";
	}
	if ($error != "") {
		echo "<h4> <font color=red>$error</font> </h4>";
	}

	echo "<h3> Resume </h3>\n";
	echo '<form enctype="multipart/form-data" action="applicant.php?p='.$p.'&h='.$h.'" method="post" name="form_resume">';
	echo 'Please select CV PDF file to upload (Max:20MB) <input name="resume" size="40" maxlength="50" type="file">';
	echo '<input type="hidden" name="action" value="upload_resume">';
	echo '<input type="hidden" name="p" value="' . $p . '">';
	echo '<input type="hidden" name="h" value="' . $h . '">';
	echo '<input type="hidden" name="MAX_FILE_SIZE" value="20000000" />';
	echo "<input type='submit' value='Upload'>";
	echo '</form>';

	if (file_exists(file_apppdf($p, $h))) {
		echo "<a target=_blank href=".file_apppdf($p, $h)." > <img height=50px widht=50px src=pdf.png><br> CV </a><br>";
	}
	
	echo "<h3> Referees </h3>\n";
	
	echo "<h5> Add a referee </h5>\n";
	echo "<form action='applicant.php?p=$p&h=$h' method='post' name='formaddref'>\n";
	echo "Referee Name <input type='text' name='refname' maxlength='50' value=''><br>\n";
	echo "Referee Email <input type='email' name='refemail' maxlength='50' value=''><br>\n";
	echo "<input type='hidden' name='p' value='$p'>";
	echo "<input type='hidden' name='h' value='$h'>";
	echo '<input type="hidden" name="action" value="add_referee">';
	echo "<input type='submit' value='Submit'>";
	echo "</form>";

	echo "<h5> List of current referees </h5>\n";

	$referees = get_referees($p, $h);
	$referees = explode( "\n", $referees );
	echo "<ul>\n";
	foreach ($referees as $refemail) {
		if ( $refemail == "" ) continue;

		$rh=do_hash($refemail);

		$refname = get_refname($p, $h, $rh);
		echo "<li>  $refname ($refemail) ";
		if (file_exists(file_refpdf($p, $h, $rh))) {
			echo "<font color=green>Cover letter received</font>";
		} else {
			echo "<form action='applicant.php?p=$p&h=$h' method='post' name='formsendrefmail'>";
			echo "<input type='hidden' name='action' value='sendrefmail'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='h' value='$h'>";
			echo "<input type='hidden' name='rh' value='$rh'>";
			echo "<input type='submit' value='Re-Send link'>";
			echo "</form>";
		}
		echo "<form action='applicant.php?p=$p&h=$h' method='post' name='formdelref'>";
		echo "<input type='hidden' name='action' value='del_referee'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='h' value='$h'>";
		echo "<input type='hidden' name='rh' value='$rh'>";
		echo "<input type='submit' value='Delete'>";
		echo "</form>";

		echo "</li>";
	}
	echo "</ul>";
	echo "<br>";
	echo "<br>";
	echo "<br>";
	echo "<form action='applicant.php?p=$p&h=$h' method='post' name='formrefresh'>";
	echo "<input type='hidden' name='action' value='refresh'>";
	echo "<input type='hidden' name='p' value='$p'>";
	echo "<input type='hidden' name='h' value='$h'>";
	echo "<input type='hidden' name='rh' value='$rh'>";
	echo "<input type='submit' value='Refresh page'>";
	echo "</form>";

	$jobtitle = get_jobtitle($p);
	$jobdesc  = get_jobdesc($p);
	echo "<hr>\n";
	echo "<h3>" . $jobtitle . "</h3>\n";
	echo "<pre>" . $jobdesc . "</pre>\n";
}


function sendrefmail( $p, $h, $rh ) {

	$appname  = get_appname($p, $h);
	$appemail = get_appemail($p, $h);
	$refname  = get_refname($p, $h, $rh);
	$refemail = get_refemail($p, $h, $rh);
	$jobtitle = get_jobtitle($p);

	# email unique link to fill details
	$to = "$refemail";
	$subject = "DIAS School of Theoretical Physics applicant cover letter request";
	$main  = "Dear $refname,\n\n";
	$main .= "$appname ($appemail) is applying to the position $jobtitle (see: https://jobs.stp.dias.ie). He/She has nominated you as referee.\n\n";
	$main .= "Please upload a PDF cover letter using the following link:\n\n";
	$main .= "https://www.stp.dias.ie/jobs/referee.php?p=$p&h=$h&rh=$rh\n\n";
	$main .= "All the best,\n";
	$main .= "STP School Administrator\n";
	$header = "From: STP School administrator <schooladministrator@stp.dias.ie>\n";

	mail ($to, $subject, $main, $header ) ;
}



if ($_SERVER['REQUEST_METHOD'] == "GET") {
        $p = $_GET['p'];
        $h = $_GET['h'];
	valid_p_h($p, $h) or die("Invalid URL");

	get_page($p, $h);

} else if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $p = $_POST['p'];
        $h = $_POST['h'];
	valid_p_h($p, $h) or die("Invalid URL");

        $action = $_POST['action'];
	switch ($action) {
		case "upload_resume" :
			$msg="CV uploaded";
			$error="";
			if (! move_uploaded_file( $_FILES['resume']['tmp_name'], file_apppdf($p, $h)) ) {
				$msg="";
				$error="CV couldn't be uploaded";
			}			
			break;
		case "add_referee" :

		        $refname  = $_POST['refname'];
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

				sendrefmail($p, $h, $rh);
				$msg="Referee added and email sent";
				$error="";
			} else {
				$msg="";
				$error="Referee already in the list";
			}

			break;
		case "sendrefmail" :

		        $p  = $_POST['p'];
		        $h  = $_POST['h'];
		        $rh = $_POST['rh'];
			valid_p_h_rh($p, $h, $rh) or die("Invalid URL");

			$msg="Reminder sent to referee";
			$error="";
			sendrefmail($p, $h, $rh);

			break;

		case "del_referee" :

		        $p  = $_POST['p'];
		        $h  = $_POST['h'];
		        $rh = $_POST['rh'];
			valid_p_h_rh($p, $h, $rh) or die("Invalid URL");

			$refemail = get_refemail($p, $h, $rh);

			# add address to referees
			$referees = get_referees($p, $h);
			if(strpos($referees, $refemail) !== false) {
				$referees = str_replace( $refemail, "", $referees );
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
		case "refresh" : break;
	}

	get_page($p, $h, $msg, $error);

}

