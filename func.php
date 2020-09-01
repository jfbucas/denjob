<?php

include "config.php";

# Obsucrity

function do_hash($str) {
	global $HASH_SALT;
	return hash("sha256", $HASH_SALT.$str);
}

# layer

function file_positionsdir(){
	global $CFG_POSITIONS_DIR;
	return $CFG_POSITIONS_DIR;
}

function file_admins(){
	return file_positionsdir()."/".do_hash("admins").".admins";
}
function file_jobdocuments($p){ 
	return file_positionsdir()."/$p/documents/";
}
function file_jobpanel($p){ 
	return file_positionsdir()."/$p/panel/";
}
function file_jobtitle($p){ 
	return file_positionsdir()."/$p/title"; #.do_hash("title");
}
function file_jobdesc($p){ 
	return file_positionsdir()."/$p/desc"; #.do_hash("desc");
}
function file_jobrefnumber($p){ 
	return file_positionsdir()."/$p/refnumber"; #.do_hash("desc");
}
function file_jobstatus($p){ 
	return file_positionsdir()."/$p/status"; #".do_hash("status");
}
function file_applicants($p){
	return file_positionsdir()."/$p/".do_hash("applicants").".applicants";
}
function file_assessors($p){
	return file_positionsdir()."/$p/".do_hash("assessors").".assessors";
}
function file_appdir($p, $h){ 
	return file_jobdocuments($p)."/$h";
}
function file_appname($p, $h){ 
	return file_jobdocuments($p)."/$h/".do_hash("name").".name";
}
function file_appemail($p, $h){ 
	return file_jobdocuments($p)."/$h/".do_hash("email").".email";
}
function file_apppdf($p, $h){
	return file_jobdocuments($p)."/$h/".do_hash($h."pdf").".pdf";
}
function file_appresponse($p, $h){ 
	return file_jobdocuments($p)."/$h/".do_hash("response").".response";
}
function file_appscore($p, $h, $a){ 
	return file_jobdocuments($p)."/$h/$a.score";
}
function file_referees($p, $h) {
	return file_jobdocuments($p)."/$h/".do_hash("referees").".referees";
}
function file_refdir($p, $h, $rh){
	return file_jobdocuments($p)."/$h/$rh";
}
function file_refname($p, $h, $rh){
	return file_jobdocuments($p)."/$h/$rh/".do_hash("name").".name";
}
function file_refemail($p, $h, $rh){
	return file_jobdocuments($p)."/$h/$rh/".do_hash("email").".email";
}
function file_refpdf($p, $h, $rh){
	return file_jobdocuments($p)."/$h/$rh/".do_hash($rh."pdf").".pdf";
}
function file_assdir($p, $a){ 
	return file_jobpanel($p)."/$a";
}
function file_assname($p, $a){ 
	return file_jobpanel($p)."/$a/".do_hash("name").".name";
}
function file_assemail($p, $a){ 
	return file_jobpanel($p)."/$a/".do_hash("email").".email";
}
function file_assstatus($p, $a){ 
	return file_jobpanel($p)."/$a/".do_hash("status").".status";
}


function get_admins(){
	if (!file_exists(file_admins())) return "";
	if (filesize(file_admins()) == 0) return "";
	$s = file_get_contents(file_admins());
	if ( $s === false ) die("Unable to open file admins!");
	return $s;
}
function get_jobtitle($p){ 
	$s = file_get_contents(file_jobtitle($p));
	if ( $s === false ) die("Unable to open file jobtitle! ".file_jobtitle($p));
	return $s;
}
function get_jobdesc($p){ 
	$s = file_get_contents(file_jobdesc($p));
	if ( $s === false ) die("Unable to open file jobdesc! ".file_jobdesc($p));
	return $s;
}
function get_jobrefnumber($p){ 
	$s = file_get_contents(file_jobrefnumber($p));
	if ( $s === false ) die("Unable to open file jobrefnumber! ".file_jobrefnumber($p));
	return $s;
}
function get_jobstatus($p){ 
	$s = file_get_contents(file_jobstatus($p));
	if ( $s === false ) die("Unable to open file jobstatus! ".file_jobstatus($p));
	$s = strpos($s, "open" );
	return ($s !== false);
}
function get_applicants($p){
	if (!file_exists(file_applicants($p))) return "";
	if (filesize(file_applicants($p)) == 0) return "";
	$s = file_get_contents(file_applicants($p));
	if ( $s === false ) die("Unable to open file applicants!");
	return $s;
}
function get_appname($p, $h){ 
	$s = file_get_contents(file_appname($p, $h));
	if ( $s === false ) die("Unable to open file applicant name!");
	return $s;
}
function get_appemail($p, $h){ 
	$s = file_get_contents(file_appemail($p, $h));
	if ( $s === false ) die("Unable to open file applicant email!");
	return $s;
}
function get_appresponse($p, $h){ 
	if (!file_exists(file_appresponse($p, $h))) return "";
	if (filesize(file_appresponse($p, $h)) == 0) return "";
	$s = file_get_contents(file_appresponse($p, $h));
	return $s;
}
function get_appscore($p, $h, $a){ 
	if (!file_exists(file_appscore($p, $h, $a))) return "";
	if (filesize(file_appscore($p, $h, $a)) == 0) return "";
	$s = file_get_contents(file_appscore($p, $h, $a));
	if ( $s === false ) die("Unable to open file applicant score!");
	return $s;
}
function get_referees($p, $h) {
	if (!file_exists(file_referees($p, $h))) return "";
	if (filesize(file_referees($p, $h)) == 0) return "";
	$s = file_get_contents(file_referees($p, $h));
	if ( $s === false ) die("Unable to open file referees!");
	return $s;
}
function get_refname($p, $h, $rh){
	$s = file_get_contents(file_refname($p, $h, $rh));
	if ( $s === false ) die("Unable to open file referee name!");
	return $s;
}
function get_refemail($p, $h, $rh){
	$s = file_get_contents(file_refemail($p, $h, $rh));
	if ( $s === false ) die("Unable to open file referee email!");
	return $s;
}
function get_assessors($p){
	if (!file_exists(file_assessors($p))) return "";
	if (filesize(file_assessors($p)) == 0) return "";
	$s = file_get_contents(file_assessors($p));
	if ( $s === false ) die("Unable to open file assessors!");
	return $s;
}
function get_assname($p, $a){ 
	$s = file_get_contents(file_assname($p, $a));
	if ( $s === false ) die("Unable to open file assessors name!");
	return $s;
}
function get_assemail($p, $a){ 
	$s = file_get_contents(file_assemail($p, $a));
	if ( $s === false ) die("Unable to open file assessor email!");
	return $s;
}
function get_assstatus($p, $a){ 
	$s = "";
	if (file_exists(file_assstatus($p, $a)))
		$s = file_get_contents(file_assstatus($p, $a));
	#if ( $s === false ) die("Unable to open file assessor status!");
	return $s;
}


#  Write functions 

function set_assname($p, $a, $name){ 
	$fh = fopen(file_assname($p, $a), 'w+') or die("Unable to write file assessor name!");
	fwrite($fh, $name);
	fclose($fh);
}
function set_assemail($p, $a, $email){ 
	$fh = fopen(file_assemail($p, $a), 'w+') or die("Unable to write file assessor email!");
	fwrite($fh, $email);
	fclose($fh);
}
function set_assstatus($p, $a, $status){ 
	$fh = fopen(file_assstatus($p, $a), 'w+') or die("Unable to write file assessor status!");
	fwrite($fh, $status);
	fclose($fh);
}

# Validation for parameters

function valid_p($ptest) {
	if ( !is_numeric($ptest) ) return false;
	$positions = scandir(file_positionsdir());
	foreach ($positions as $p) {
		if (!file_exists(file_jobtitle($p))) continue;
		if (!file_exists(file_jobdesc($p))) continue;
		if (!file_exists(file_jobrefnumber($p))) continue;
		if ($p == $ptest) return true;
	}
	return false;
}
function valid_h($p, $htest) {
	if (strlen($htest) != 64) return false;
	if ( !ctype_xdigit($htest) ) return false;
	$apphashes = scandir(file_jobdocuments($p));
	foreach ($apphashes as $h) {
		if (!file_exists(file_appname($p, $h)))  continue;
		if (!file_exists(file_appemail($p, $h))) continue;
		if ($h == $htest) return true;
	}
	return false;
}
function valid_rh($p, $h, $rhtest) {
	if (strlen($rhtest) != 64) return false;
	if ( !ctype_xdigit($rhtest) ) return false;
	$refhashes = scandir(file_appdir($p, $h));
	foreach ($refhashes as $rh) {
		if (!file_exists(file_refname($p, $h, $rh))) continue;
		if (!file_exists(file_refemail($p, $h, $rh))) continue;
		if ($rh == $rhtest) return true;
	}
	return false;
}
function valid_a($p, $atest) {
	if (strlen($atest) != 64) return false;
	if ( !ctype_xdigit($atest) ) return false;
	$asshashes = scandir(file_jobpanel($p));
	foreach ($asshashes as $a) {
		if (!file_exists(file_assname($p, $a))) continue;
		if (!file_exists(file_assemail($p, $a))) continue;
		if ($a == $atest) return true;
	}
	return false;
}

function valid_p_h( $ptest, $htest ) {
	return valid_p( $ptest ) and valid_h( $ptest, $htest );
}
function valid_p_a( $ptest, $atest ) {
	return valid_p( $ptest ) and valid_a( $ptest, $atest );
}
function valid_p_h_rh( $ptest, $htest, $rhtest ) {
	return valid_p( $ptest ) and valid_h( $ptest, $htest ) and valid_rh( $ptest, $htest, $rhtest );
}

function valid_name($name) {
	if (strlen($name) >= 64) return false;
	return true;
}
function valid_email($email) {
	return filter_var($email, FILTER_VALIDATE_EMAIL);
}
function valid_status($status) {
	switch ($status) {
		case "":
		case "chairman":
		case "observer":
		case "normal":
			return true;
	}
	return false;
}
function valid_name_email($name, $email){
	return valid_name($name) and valid_email($email);
}
function valid_name_email_status($name, $email, $status){
	return valid_name($name) and valid_email($email) and valid_status($status);
}

function valid_c($ctest) {
	if (strlen($ctest) != 64) return false;
	if ( !ctype_xdigit($ctest) ) return false;
	$admins = get_admins();
	$admins = explode( "\n", $admins );
	foreach ($admins as $adminemail) {
		if ( $ctest == do_hash($adminemail) )  return true;
	}
	return false;
}

# Anti spam

function antispam_str( $p ) {
	$s = do_hash(get_jobtitle($p));
	$s = preg_replace("/[a-zA-Z]/", "", $s);
	return substr($s, 2, 2) . " + " . substr($s, -2);
}
function antispam_result( $p ) {
	$s = do_hash(get_jobtitle($p));
	$s = preg_replace("/[a-zA-Z]/", "", $s);
	return intval(substr($s, 2, 2))  +  intval(substr($s, -2));
}


# Show job title/description
function show_job_title_description($p) {
	$jobtitle = get_jobtitle($p);
	$jobdesc  = get_jobdesc($p);
	echo "<h3>" . $jobtitle . "</h3>\n";
	echo "<div style='width:700px'><pre style='word-wrap: break-word;white-space: pre-wrap;' >" . $jobdesc . "</pre></div>\n";
}

# Sending emails

function sendrefmail( $p, $h, $rh ) {
	$appname  = get_appname($p, $h);
	$appemail = get_appemail($p, $h);
	$refname  = get_refname($p, $h, $rh);
	$refemail = get_refemail($p, $h, $rh);
	$jobtitle = get_jobtitle($p);

	# email unique link to fill details
	mail_referee($refname, $refemail, $appname, $appemail, $jobtitle, $p, $h, $rh);
}


function sendassmail( $p, $a ) {

	$assname  = get_assname($p, $a);
	$assemail = get_assemail($p, $a);
	$jobtitle = get_jobtitle($p);

	# email unique link to fill details
	mail_assessor($assname, $assemail, $jobtitle, $p, $a);
}

function mail_applicant($appname, $appemail, $jobtitle, $p, $h) {
	global $EMAIL_HEADER, $APP_SUBJECT, $APP_MAIN;

	$to = "$appemail";

	$main = $APP_MAIN;
	$main = str_replace( "%appname", $appname, $main );
	$main = str_replace( "%appemail", $appemail, $main );
	$main = str_replace( "%jobtitle", $jobtitle, $main );
	$main = str_replace( "%p", $p, $main );
	$main = str_replace( "%h", $h, $main );

	do_mail ($to, $APP_SUBJECT, $main, $EMAIL_HEADER ) ;
}

function mail_applicant_response($appname, $appemail, $jobtitle, $p, $h) {
	global $EMAIL_HEADER, $RESPONSE_SUBJECT, $RESPONSE_MAIN;

	$to = "$appemail";

	$main = $RESPONSE_MAIN;
	$main = str_replace( "%appname", $appname, $main );
	$main = str_replace( "%appemail", $appemail, $main );
	$main = str_replace( "%jobtitle", $jobtitle, $main );
	$main = str_replace( "%p", $p, $main );
	$main = str_replace( "%h", $h, $main );

	do_mail ($to, $RESPONSE_SUBJECT, $main, $EMAIL_HEADER ) ;
}

function mail_applicant_reminder($appname, $appemail, $jobtitle, $p, $h) {
	global $EMAIL_HEADER, $REMINDER_SUBJECT, $REMINDER_MAIN;

	$to = "$appemail";

	$main = $REMINDER_MAIN;
	$main = str_replace( "%appname", $appname, $main );
	$main = str_replace( "%appemail", $appemail, $main );
	$main = str_replace( "%jobtitle", $jobtitle, $main );
	$main = str_replace( "%p", $p, $main );
	$main = str_replace( "%h", $h, $main );

	do_mail ($to, $REMINDER_SUBJECT, $main, $EMAIL_HEADER ) ;
}



function mail_referee($refname, $refemail, $appname, $appemail, $jobtitle, $p, $h, $rh) {
	global $EMAIL_HEADER, $REF_SUBJECT, $REF_MAIN;

	$to = "$refemail";

	$main = $REF_MAIN;
	$main = str_replace( "%refname", $refname, $main );
	$main = str_replace( "%refemail", $refemail, $main );
	$main = str_replace( "%appname", $appname, $main );
	$main = str_replace( "%appemail", $appemail, $main );
	$main = str_replace( "%jobtitle", $jobtitle, $main );
	$main = str_replace( "%p", $p, $main );
	$main = str_replace( "%h", $h, $main );
	$main = str_replace( "%rh", $rh, $main );

	do_mail ($to, $REF_SUBJECT, $main, $EMAIL_HEADER ) ;
}


function mail_assessor($assname, $assemail, $jobtitle, $p, $a) {
	global $EMAIL_HEADER, $ASS_SUBJECT, $ASS_MAIN;

	$to = "$assemail";

	$main = $ASS_MAIN;
	$main = str_replace( "%assname", $assname, $main );
	$main = str_replace( "%assemail", $assemail, $main );
	$main = str_replace( "%jobtitle", $jobtitle, $main );
	$main = str_replace( "%p", $p, $main );
	$main = str_replace( "%a", $a, $main );

	do_mail ($to, $ASS_SUBJECT, $main, $EMAIL_HEADER ) ;
}

function mail_admin($adminemail) {
	global $EMAIL_HEADER, $ADMIN_SUBJECT, $ADMIN_MAIN;

	$to = "$adminemail";

	$main = $ADMIN_MAIN;
	$main = str_replace( "%c", do_hash($adminemail), $main );

	do_mail ($to, $ADMIN_SUBJECT, $main, $EMAIL_HEADER ) ;
}



function do_mail( $to, $subject, $main, $header ) {

	global $URL, $EMAIL_FROM, $EMAIL_SUBJECT_PREFIX, $EMAIL_SIGNATURE;

	$header  = str_replace( "%from", $EMAIL_FROM, $header );
	$subject = str_replace( "%subject", $EMAIL_SUBJECT_PREFIX, $subject );
	$main    = str_replace( "%url", $URL, $main );
	$main    = str_replace( "%signature", $EMAIL_SIGNATURE, $main );

	mail( $to, $subject, $main, $header );
}






function get_results_page($p, $user, $user_type) {

	echo "<head>";
	echo "<link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css'>";
	echo "<script type='text/javascript' src='https://code.jquery.com/jquery-3.3.1.js'></script>";
	echo "<script type='text/javascript' src='https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js'></script>";
	echo "</head>";
	echo "<body>";

	echo "<h2> Results for position: ". get_jobtitle($p) . "<h2>\n";

	$applicants = get_applicants($p);
	$applicants = explode( "\n", $applicants );
	echo "<table id='results' class='display' >\n";
	echo "<thead><tr><th>Applicants</th><th align=center>Assessors</th><th align=center>Score</th><th align=center>Response</th></tr></thead>\n";
	echo "<tbody>\n";
	foreach ($applicants as $appemail) {
		$h=do_hash($appemail);
		if (!valid_p_h( $p, $h )) continue;

		$appname = get_appname($p, $h);
		echo "<tr><td valign=middle>";
		if (file_exists(file_apppdf($p, $h))) {
			echo "<div><a href=". file_apppdf($p, $h)."?t=".time()." > $appname &lt;$appemail&gt;  <font color=red size=+2>&#128442;</font><!--img height=20px width=20px src=pdf.png--> </a></div>\n";
		} else {
			echo "<div> $appname &lt;$appemail&gt;</div>\n";
		}
		echo "</td><td align=center valign=middle>";

		$score = 0;
		$assessors = get_assessors($p);
		$assessors = explode( "\n", $assessors );
		foreach ($assessors as $assemail) {
			$assemailshort = explode("@", $assemail)[0];
			$ah=do_hash($assemail);
			if (!valid_p_a( $p, $ah )) continue;

			$v = get_appscore($p, $h, $ah);
			$color = "white";
			$text =  "_";
			if ($v == "Y") { $color = "#24ff24"; $text = "O"; $score += 1; } 
			if ($v == "M") { $color = "#ff9224"; $text = "-"; }
			if ($v == "N") { $color = "#ff2424"; $text = "X"; $score -= 1; }
			echo "<input type='submit' style='background-color:$color;' value='$assemailshort' alt='$assemail'>";

		}
		echo "</td><td align=center valign=middle>";

		echo "$score";
		echo "</td><td>";

		$appresponse = get_appresponse($p, $h);

		if ($appresponse == "") {
			echo "<form style='display: inline;' action='$user_type.php?p=$p' method='post' name='formresponse'>";
			echo "<input type='hidden' name='action' value='app_response'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='c' value='$user'>";
			echo "<input type='hidden' name='a' value='$user'>";
			echo "<input type='hidden' name='h' value='$h'>";
			echo "<input type='hidden' name='v' value='Y'>";
			echo "<input type='submit' style='background-color:black; color:white;' onclick=\"return confirm('Are you sure?')\" value='Decline'>";
			echo "</form>";

			echo "<form style='display: inline;' action='$user_type.php?p=$p' method='post' name='formreminder'>";
			echo "<input type='hidden' name='action' value='app_reminder'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='c' value='$user'>";
			echo "<input type='hidden' name='a' value='$user'>";
			echo "<input type='hidden' name='h' value='$h'>";
			echo "<input type='hidden' name='v' value='Y'>";
			echo "<input type='submit' style='background-color:darkblue; color:white;' onclick=\"return confirm('Send reminder?')\" value='Reminder'>";
			echo "</form>";
		} else {
			echo "Responded";
		}


		echo "</td></tr>";
	}
	echo "</tbody>\n";
	echo "</table>";
	echo '<script type="text/javascript">
		$(document).ready(function() {
			$("#results").DataTable({
				"paging": false,
				"order": [ [2, "desc"] ],
			});
		} );
		</script>';
	echo "<br>";
	echo "<form action='$user_type.php?p=$p' method='post' name='formresults'>";
	echo "<input type='hidden' name='action' value='results'>";
	echo "<input type='hidden' name='a' value='$user'>";
	echo "<input type='hidden' name='c' value='$user'>";
	echo "<input type='hidden' name='p' value='$p'>";
	echo "<input type='submit' value='Refresh'>";
	echo "</form>";
	echo "<br>";

	echo "<hr>\n";
	show_job_title_description($p);
	echo "</body>";
}







?>
