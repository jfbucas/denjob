<?php

include "func.php";

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $p = $_POST['p'];
	valid_p($p) or die("Invalid URL");

        $appname  = $_POST['appname'];
        $appemail = $_POST['appemail'];
        $antispam = $_POST['antispam'];

	valid_name_email($appname, $appemail);

	if ($antispam != antispam_result($p) ) {
		die("Wrong antispam response, try again.");
	}

	$h=do_hash($appemail);

	# add address to applicants
	$applicants = get_applicants($p);
	if(strpos($applicants, $appemail) === false) {
		$fh = fopen(file_applicants($p), 'a') or die("can't open file");
		fwrite($fh, $appemail . "\n");
		fclose($fh);
	}

	# store name/email
	if (!is_dir(file_appdir($p, $h))) mkdir( file_appdir($p, $h) );
	$fh = fopen( file_appname($p, $h), 'w+') or die("can't open file");
	fwrite($fh, $appname);
	fclose($fh);
	$fh = fopen( file_appemail($p, $h), 'w+') or die("can't open file");
	fwrite($fh, $appemail);
	fclose($fh);

	$jobtitle = get_jobtitle($p);

	# email unique link to fill details
	mail_applicant($appname, $appemail, $jobtitle, $p, $h);

	$msg="<font color=green>Thank you $appname for applying to $jobtitle, please check your email ($appemail) for a link to continue with your application.</font>";
}

echo "<img src=sitelogo.png>\n";
echo "<h1> $EMAIL_SUBJECT </h1>\n";
echo "<h2> Positions currently open: </h2>\n";

if ( $msg != "" ) {
	echo "<h3>$msg</h3>\n";
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
		$jobtitle = get_jobtitle($p);
		$jobdesc  = get_jobdesc($p);
		echo "<h3>" . $jobtitle . "</h3>\n";
		echo "<div style='width:500px;overflow:auto'><pre>" . $jobdesc . "</pre></div>\n";

		echo "<br><br>\n";
		echo "To Apply, please fill the following:\n <br>";
		echo "<form action='index.php' method='post' name='form".$p."'>\n";
		echo "Name <input type='text' name='appname' maxlength='50' value=''><br>\n";
		echo "Email <input type='email' name='appemail' maxlength='50' value=''><br>\n";
		echo "Anti-spam: ". antispam_str($p) ." = ? <input type='text' name='antispam' maxlength='50' value=''><br>\n";
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

echo "<!-- h6>$COPYRIGHT</h6 -->";

?>
