<?php

include "func.php";

$msg = "";
$error = "";

function get_page($c, $only_p, $msg = "", $error = "") {

	global $COPYRIGHT;

	echo "<a href=index.php><img src=sitelogo.png></a>\n";
	#echo "<h1> $COMPANY </h1>\n";

	if ($msg != "") {
		echo "<h1> <font color=green><i>$msg</i></font> </h1>"; exit ;
	}
	if ($error != "") {
		echo "<h1> <font color=red><i>$error</i></font> </h1>"; exit ;
	}

	if ($only_p == "")
		echo "<h2> Positions listed: </h2>\n";

	echo "<hr>";

	$nb_job_open = 0;
	$positions = scandir(file_positionsdir());
	rsort($positions);
	foreach ($positions as $p) {
		if ( !is_numeric($p) ) continue;
		if (($only_p != "") and ( $p != $only_p )) continue;

		$nb_job_open ++;

		$status = get_jobstatus($p);
		if ( $status ) {
			$jobtitle = get_jobtitle($p);
			$jobdesc  = get_jobdesc($p);

			if ($only_p != "") {
#				echo "<br><br>\n";
#н				echo "<b>To start the application process, please fill the following</b>:\n <br>";
				echo "<b><i>To start the application process, please enter your details below. You will then be sent a link where you can upload your application. Please note that once you enter your details below your name will appear on the job application system as a prospective candidate up until the closing date. If you haven’t uploaded an application by the closing date, your details will be deleted from the system.</i></b>\n <br>";
				echo "<form action='index.php' method='post' name='form".$p."'>\n";
				echo "Name <input type='text' name='appname' maxlength='50' value=''><br>\n";
				echo "Email <input type='email' name='appemail' maxlength='50' value=''><br>\n";
				echo "Anti-spam: ". antispam_str($p) ." = ? <input type='text' name='antispam' size='5' maxlength='5' value=''><br>\n";
				echo "<input type='hidden' name='p' value='$p'>";
				echo "<input type='submit' value='Submit'>";
				echo "<br><br>\n";
				echo "</form>";
			}
			echo "<h3> <a href=index.php?p=".$p.">" . $jobtitle . "</a></h3>\n";
			echo "<div style='width:700px'><pre style='word-wrap: break-word;white-space: pre-wrap;' >" . $jobdesc . "</pre></div>\n";
		} else {
			echo "<font color=gray>\n";
			show_job_title_description($p);
			echo "</font>\n";
			echo "<br><br>\n";
			echo "<h4> <font color=orange>This application process is now closed</font></h4>\n";
		}


		echo "<hr>";
	}

	# No job open
	if ( $nb_job_open == 0 ) {
		echo "There is currently no position open\n";
		echo "<hr>";
	}

	echo "<h6><font color=lightgray>$COPYRIGHT</font></h6>";
}


$only_p = "";

if ($_SERVER['REQUEST_METHOD'] == "GET") {

	if (array_key_exists('p', $_GET)) {
	        $p = $_GET['p'];
		valid_p($p) or die("Invalid position URL");

		$only_p = $p;
	}

} else if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $p = $_POST['p'];
	valid_p($p) or die("Invalid position URL");

	$jobtitle = get_jobtitle($p);

	$appname   = filter_var($_POST['appname'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_LOW|FILTER_FLAG_ENCODE_AMP );
        $appemail = $_POST['appemail'];
        $antispam = $_POST['antispam'];

	valid_name_email($appname, $appemail);

	$status = get_jobstatus($p);
	if ( ! $status ) {
		die("Dear $appname, the position is now closed");
	}

	if ($antispam != antispam_result($p) ) {
		die("Wrong antispam response, try again.");
	}


	$h=do_hash($appemail);

	# add address to applicants
	$applicants = explode( "\n", get_applicants($p) );
	$key = array_search( $appemail, $applicants );
	if ($key === false) {
		$fh = fopen(file_applicants($p), 'a') or die("can't open file");
		fwrite($fh, $appemail . "\n");
		fclose($fh);

		# store name/email
		if (!is_dir(file_jobdocuments($p))) mkdir( file_jobdocuments($p) );
		if (!is_dir(file_appdir($p, $h))) mkdir( file_appdir($p, $h) );
		$fh = fopen( file_appname($p, $h), 'w+') or die("can't open file");
		fwrite($fh, $appname);
		fclose($fh);
		$fh = fopen( file_appemail($p, $h), 'w+') or die("can't open file");
		fwrite($fh, $appemail);
		fclose($fh);

#		$msg="Thank you $appname for considering an application to $jobtitle, please check your email ($appemail) for a link to continue with your application.";
		$msg="Thank you for your interest in the position of $jobtitle Please check your email ($appemail) for a link to submit your application.";
	}else{
		$error="Application link already sent to $appemail , sending it again now.";
	}

	# email unique link to fill details
	mail_applicant($appname, $appemail, $jobtitle, $p, $h);

}

get_page("", $only_p, $msg, $error);

?>
