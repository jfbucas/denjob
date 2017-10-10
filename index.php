<?php

include "func.php";

$msg = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $p = $_POST['p'];
	valid_p($p) or die("Invalid URL");

	$jobtitle = get_jobtitle($p);

        $appname  = $_POST['appname'];
        $appemail = $_POST['appemail'];
        $antispam = $_POST['antispam'];

	valid_name_email($appname, $appemail);

	$status = get_jobstatus($p);
	if ( $status == "close" ) {
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

		$msg="Thank you $appname for applying to $jobtitle, please check your email ($appemail) for a link to continue with your application.";
	}else{
		$error="$appemail already applied, sending email again.";
	}

	# email unique link to fill details
	mail_applicant($appname, $appemail, $jobtitle, $p, $h);

}

echo "<img src=sitelogo.png>\n";
echo "<h1> $EMAIL_SUBJECT </h1>\n";
echo "<h2> Positions currently open: </h2>\n";

if ($msg != "") {
	echo "<h4> <font color=green>$msg</font> </h4>";
}
if ($error != "") {
	echo "<h4> <font color=red>$error</font> </h4>";
}

echo "<hr>";

$nb_job_open = 0;
$positions = scandir(file_positionsdir());
foreach ($positions as $p) {
	if ( !is_numeric($p) ) continue;

	$status = get_jobstatus($p);
	$open = strpos($status, "open" );
	if ( $open !== false ) {
		$nb_job_open ++;

		show_job_title_description($p);

		echo "<br><br>\n";
		echo "To Apply, please fill the following:\n <br>";
		echo "<form action='index.php' method='post' name='form".$p."'>\n";
		echo "Name <input type='text' name='appname' maxlength='50' value=''><br>\n";
		echo "Email <input type='email' name='appemail' maxlength='50' value=''><br>\n";
		echo "Anti-spam: ". antispam_str($p) ." = ? <input type='text' name='antispam' size='5' maxlength='5' value=''><br>\n";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='submit' value='Submit'>";
		echo "</form>";

		echo "<hr>";
	}
}

# No job open
if ( $nb_job_open == 0 ) {
	echo "There is currently no position open\n";
	echo "<hr>";
}

echo "<h6><font color=lightgray>$COPYRIGHT</font></h6>";

?>
