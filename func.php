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
function file_emails_log($p){
	return file_positionsdir()."/$p/".do_hash("emails.log").".emails.log";
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
function file_jobdue($p){ 
	return file_positionsdir()."/$p/due"; #.do_hash("due");
}
function file_jobrefnumber($p){ 
	return file_positionsdir()."/$p/refnumber"; #.do_hash("desc");
}
function file_jobrefearly($p){ 
	return file_positionsdir()."/$p/refearly"; #.do_hash("desc");
}
function file_jobstatus($p){ 
	return file_positionsdir()."/$p/status"; #".do_hash("status");
}
function file_jobmonitoringnationality($p){ 
	return file_positionsdir()."/$p/".do_hash("monitoringnationality").".monitoringnationality";
}
function file_jobmonitoringgender($p){ 
	return file_positionsdir()."/$p/".do_hash("monitoringgender").".monitoringgender";
}
function file_template_applicant($p){ 
	return file_positionsdir()."/$p/template_applicant";
}
function file_template_uploaded($p){ 
	return file_positionsdir()."/$p/template_uploaded";
}
function file_template_response($p){ 
	return file_positionsdir()."/$p/template_response";
}
function file_template_reminder($p){ 
	return file_positionsdir()."/$p/template_reminder";
}
function file_template_ref($p){ 
	return file_positionsdir()."/$p/template_ref";
}
function file_template_ass($p){ 
	return file_positionsdir()."/$p/template_ass";
}
function file_applicants($p){
	return file_positionsdir()."/$p/".do_hash("applicants").".applicants";
}
function file_assessors($p){
	return file_positionsdir()."/$p/".do_hash("assessors").".assessors";
}
function file_appdir($p, $h){ 
	return file_jobdocuments($p)."$h";
}
function file_appname($p, $h){ 
	return file_jobdocuments($p)."$h/".do_hash("name").".name";
}
function file_appemail($p, $h){ 
	return file_jobdocuments($p)."$h/".do_hash("email").".email";
}
function file_apppdf($p, $h){
	return file_jobdocuments($p)."$h/".do_hash($h."pdf").".pdf";
}
function file_appresponse($p, $h){ 
	return file_jobdocuments($p)."$h/".do_hash("response").".response";
}
function file_appshortlist($p, $h){ 
	return file_jobdocuments($p)."$h/".do_hash("shortlist").".shortlist";
}
function file_appscore($p, $h, $a){ 
	return file_jobdocuments($p)."$h/$a.score";
}
function file_appmonitoring($p, $h){ 
	return file_jobdocuments($p)."$h/".do_hash("monitoring").".monitoring";
}
function file_referees($p, $h) {
	return file_jobdocuments($p)."$h/".do_hash("referees").".referees";
}
function file_refdir($p, $h, $rh){
	return file_jobdocuments($p)."$h/$rh";
}
function file_refname($p, $h, $rh){
	return file_jobdocuments($p)."$h/$rh/".do_hash("name").".name";
}
function file_refemail($p, $h, $rh){
	return file_jobdocuments($p)."$h/$rh/".do_hash("email").".email";
}
function file_refpdf($p, $h, $rh){
	return file_jobdocuments($p)."$h/$rh/".do_hash($rh."pdf").".pdf";
}
function file_assdir($p, $a){ 
	return file_jobpanel($p)."$a";
}
function file_assname($p, $a){ 
	return file_jobpanel($p)."$a/".do_hash("name").".name";
}
function file_assemail($p, $a){ 
	return file_jobpanel($p)."$a/".do_hash("email").".email";
}
function file_assstatus($p, $a){ 
	return file_jobpanel($p)."$a/".do_hash("status").".status";
}

// Check if a file is an actual PDF
// PDF document, version 1.5
function is_pdf($f){
	$output = exec("file -b ".$f, $fulloutput);
	if ($output === FALSE)
		return false;
	
	return (substr($output,0,12) ===  "PDF document");
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
function get_jobdue($p){ 
	$s = file_get_contents(file_jobdue($p));
	if ( $s === false ) die("Unable to open file jobdue! ".file_jobdue($p));
	return $s;
}
function get_jobrefnumber($p){ 
	$s = file_get_contents(file_jobrefnumber($p));
	if ( $s === false ) die("Unable to open file jobrefnumber! ".file_jobrefnumber($p));
	return $s;
}
function get_jobrefearly($p){ 
	$s = file_get_contents(file_jobrefearly($p));
	if ( $s === false ) die("Unable to open file jobrefearly! ".file_jobrefearly($p));
	return $s;
}
function get_jobstatus($p){ 
	$s = file_get_contents(file_jobstatus($p));
	if ( $s === false ) die("Unable to open file jobstatus! ".file_jobstatus($p));
	$s = strpos($s, "open" );
	return ($s !== false);
}
function get_jobcondition($p){ 
	$status = get_jobstatus($p);
	$due    = get_jobdue($p);
	$date   = date('Y-m-d');
	return (($status !== false)&&($due >= $date));
}
function get_template_applicant($p){ 
	global $APP_MAIN;
	$s = file_get_contents(file_template_applicant($p));
	if ( $s === false ) return $APP_MAIN;
	return $s;
}
function get_template_uploaded($p){ 
	global $UPLOADED_MAIN;
	$s = file_get_contents(file_template_uploaded($p));
	if ( $s === false ) return $UPLOADED_MAIN;
	return $s;
}
function get_template_response($p){ 
	global $RESPONSE_MAIN;
	$s = file_get_contents(file_template_response($p));
	if ( $s === false ) return $RESPONSE_MAIN;
	return $s;
}
function get_template_reminder($p){ 
	global $REMINDER_MAIN;
	$s = file_get_contents(file_template_reminder($p));
	if ( $s === false ) return $REMINDER_MAIN;
	return $s;
}
function get_template_ref($p){ 
	global $REF_MAIN;
	$s = file_get_contents(file_template_ref($p));
	if ( $s === false ) return $REF_MAIN;
	return $s;
}
function get_template_ass($p){ 
	global $ASS_MAIN;
	$s = file_get_contents(file_template_ass($p));
	if ( $s === false ) return $ASS_MAIN;
	return $s;
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
function get_appshortlist($p, $h){ 
	if (!file_exists(file_appshortlist($p, $h))) return "";
	if (filesize(file_appshortlist($p, $h)) == 0) return "";
	$s = file_get_contents(file_appshortlist($p, $h));
	return $s;
}
function get_appscore($p, $h, $a){ 
	if (!file_exists(file_appscore($p, $h, $a))) return "";
	if (filesize(file_appscore($p, $h, $a)) == 0) return "";
	$s = file_get_contents(file_appscore($p, $h, $a));
	if ( $s === false ) die("Unable to open file applicant score!");
	return $s;
}
function get_appmonitoring($p, $h){ 
	if (!file_exists(file_appmonitoring($p, $h))) return "";
	if (filesize(file_appmonitoring($p, $h)) == 0) return "";
	$s = file_get_contents(file_appmonitoring($p, $h));
	if ( $s === false ) die("Unable to open file applicant monitoring!");
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

function set_template_applicant($p, $t){ 
	$fh = fopen(file_template_applicant($p), 'w+') or die("Unable to write file template applicant!");
	fwrite($fh, $t);
	fclose($fh);
}
function set_template_uploaded($p, $t){ 
	$fh = fopen(file_template_uploaded($p), 'w+') or die("Unable to write file template uploaded!");
	fwrite($fh, $t);
	fclose($fh);
}
function set_template_response($p, $t){ 
	$fh = fopen(file_template_response($p), 'w+') or die("Unable to write file template response!");
	fwrite($fh, $t);
	fclose($fh);
}
function set_template_reminder($p, $t){ 
	$fh = fopen(file_template_reminder($p), 'w+') or die("Unable to write file template reminder!");
	fwrite($fh, $t);
	fclose($fh);
}
function set_template_ref($p, $t){ 
	$fh = fopen(file_template_ref($p), 'w+') or die("Unable to write file template ref!");
	fwrite($fh, $t);
	fclose($fh);
}
function set_template_ass($p, $t){ 
	$fh = fopen(file_template_ass($p), 'w+') or die("Unable to write file template ass!");
	fwrite($fh, $t);
	fclose($fh);
}

# Validation for parameters

function valid_p($ptest) {
	if ( !is_numeric($ptest) ) return false;
	$positions = scandir(file_positionsdir());
	foreach ($positions as $p) {
		if (!file_exists(file_jobtitle($p))) continue;
		if (!file_exists(file_jobdesc($p))) continue;
		if (!file_exists(file_jobdue($p))) continue;
		if (!file_exists(file_jobrefnumber($p))) continue;
		if (!file_exists(file_jobrefearly($p))) continue;
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
		case "coordinator":
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

# Clean a string
function clean($string) {
   return preg_replace('/[^A-Za-z0-9\- ]/', '', $string); // Removes special chars.
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
	$jobdue   = get_jobdue($p);
	echo "<h3>" . $jobtitle . " - close date is ". $jobdue . "</h3>\n";
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
	global $EMAIL_HEADER, $APP_SUBJECT;

	$to = "$appemail";

	$main = get_template_applicant($p);
	$main = str_replace( "%appname", $appname, $main );
	$main = str_replace( "%appemail", $appemail, $main );
	$main = str_replace( "%jobtitle", $jobtitle, $main );
	$main = str_replace( "%p", $p, $main );
	$main = str_replace( "%h", $h, $main );

	do_mail ( $p, $to, $APP_SUBJECT, $main, $EMAIL_HEADER ) ;
}

function mail_applicant_uploaded($p, $h) {
	global $EMAIL_HEADER, $UPLOADED_SUBJECT;

        $appname  = get_appname($p, $h);
        $appemail = get_appemail($p, $h);
        $jobtitle = get_jobtitle($p);

	$to = "$appemail";

	$main = get_template_uploaded($p);
	$main = str_replace( "%appname", $appname, $main );
	$main = str_replace( "%appemail", $appemail, $main );
	$main = str_replace( "%jobtitle", $jobtitle, $main );
	$main = str_replace( "%p", $p, $main );
	$main = str_replace( "%h", $h, $main );

	$cv = file_apppdf($p, $h);
	$main = str_replace( "%cv", $cv, $main );

	do_mail ($p, $to, $UPLOADED_SUBJECT, $main, $EMAIL_HEADER ) ;
}

function mail_applicant_response($appname, $appemail, $jobtitle, $p, $h) {
	global $EMAIL_HEADER, $RESPONSE_SUBJECT;

	$to = "$appemail";

	$main = get_template_response($p);
	$main = str_replace( "%appname", $appname, $main );
	$main = str_replace( "%appemail", $appemail, $main );
	$main = str_replace( "%jobtitle", $jobtitle, $main );
	$main = str_replace( "%p", $p, $main );
	$main = str_replace( "%h", $h, $main );

	do_mail ( $p, $to, $RESPONSE_SUBJECT, $main, $EMAIL_HEADER ) ;
}

function mail_applicant_reminder($appname, $appemail, $jobtitle, $p, $h) {
	global $EMAIL_HEADER, $REMINDER_SUBJECT;

	$to = "$appemail";

	$main = get_template_reminder($p);
	$main = str_replace( "%appname", $appname, $main );
	$main = str_replace( "%appemail", $appemail, $main );
	$main = str_replace( "%jobtitle", $jobtitle, $main );
	$main = str_replace( "%p", $p, $main );
	$main = str_replace( "%h", $h, $main );

	do_mail ( $p, $to, $REMINDER_SUBJECT, $main, $EMAIL_HEADER ) ;
}



function mail_referee($refname, $refemail, $appname, $appemail, $jobtitle, $p, $h, $rh) {
	global $EMAIL_HEADER, $REF_SUBJECT;

	$to = "$refemail";

	$main = get_template_ref($p);
	$main = str_replace( "%refname", $refname, $main );
	$main = str_replace( "%refemail", $refemail, $main );
	$main = str_replace( "%appname", $appname, $main );
	$main = str_replace( "%appemail", $appemail, $main );
	$main = str_replace( "%jobtitle", $jobtitle, $main );
	$main = str_replace( "%p", $p, $main );
	$main = str_replace( "%h", $h, $main );
	$main = str_replace( "%rh", $rh, $main );

	do_mail ( $p, $to, $REF_SUBJECT, $main, $EMAIL_HEADER ) ;
}


function mail_assessor($assname, $assemail, $jobtitle, $p, $a) {
	global $EMAIL_HEADER, $ASS_SUBJECT;

	$to = "$assemail";

	$main = get_template_ass($p);
	$main = str_replace( "%assname", $assname, $main );
	$main = str_replace( "%assemail", $assemail, $main );
	$main = str_replace( "%jobtitle", $jobtitle, $main );
	$main = str_replace( "%p", $p, $main );
	$main = str_replace( "%a", $a, $main );

	do_mail ( $p, $to, $ASS_SUBJECT, $main, $EMAIL_HEADER ) ;
}

function mail_admin($adminemail) {
	global $EMAIL_HEADER, $ADMIN_SUBJECT, $ADMIN_MAIN;

	$to = "$adminemail";

	$main = $ADMIN_MAIN;
	$main = str_replace( "%c", do_hash($adminemail), $main );

	do_mail ( null, $to, $ADMIN_SUBJECT, $main, $EMAIL_HEADER ) ;
}



function do_mail( $p, $to, $subject, $main, $header ) {

	global $URL, $EMAIL_FROM, $EMAIL_ONLY, $EMAIL_SUBJECT_PREFIX, $EMAIL_SIGNATURE;

	$header  = str_replace( "%from", $EMAIL_FROM, $header );
	$subject = str_replace( "%subject", $EMAIL_SUBJECT_PREFIX, $subject );
	$main    = str_replace( "%url", $URL, $main );
	$main    = str_replace( "%signature", $EMAIL_SIGNATURE, $main );

	if ($p !== null) {
		# Add to the emails log
		$fh = fopen(file_emails_log($p), 'a') or die("Can't open emails log file ".file_emails_log($p));
		fwrite($fh, "___________[ " . date("Y-m-d H:i:s") . " ]________________________________________________________________________\n");
		fwrite($fh, "\n");
		fwrite($fh, $header . "\n");
		fwrite($fh, $subject . "\n");
		fwrite($fh, "\n");
		fwrite($fh, $main . "\n");
		fwrite($fh, "\n");
		fwrite($fh, "\n");
		fclose($fh);
	}

	mail( $to, $subject, $main, $header, "-f $EMAIL_ONLY" );
}


function get_js_toggle() {
	echo "<style>";
	echo ".hide    { display: none;  }";
	echo ".visible { display: block; }";
	echo "</style>";
	echo "";
	echo "<script>";
	echo "function toggle(id){";
	echo "var x = document.getElementById(id);";
	echo 'if (x.getAttribute("class")=="hide") {';
	echo 'x.setAttribute("class", "visible");';
	echo '} else {';
	echo 'x.setAttribute("class", "hide");';
	echo '}';
	echo '}';
	echo "</script>";
}



function get_results_page($p, $user, $user_type, $msg = "", $error = "") {

	echo "<head>";
	echo "<link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css'>";
	echo "<script type='text/javascript' src='https://code.jquery.com/jquery-3.3.1.js'></script>";
	echo "<script type='text/javascript' src='https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js'></script>";
	echo "</head>";
	echo "<body>";

	echo "<h2> Results for position: ". get_jobtitle($p) . "<h2>\n";

	if ($msg != "") {
		echo "<h4> <font color=green>$msg</font> </h4>";
	}
	if ($error != "") {
		echo "<h4> <font color=red>$error</font> </h4>";
	}


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

		$appresponse  = get_appresponse($p, $h);
		$appshortlist = get_appshortlist($p, $h);

		if (($appshortlist == "")&&($appresponse == "")) {
			echo "<form style='display: inline;' action='$user_type.php?p=$p' method='post' name='formresponse'>";
			echo "<input type='hidden' name='action' value='app_response'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='c' value='$user'>";
			echo "<input type='hidden' name='a' value='$user'>";
			echo "<input type='hidden' name='h' value='$h'>";
			echo "<input type='submit' style='background-color:black; color:white;' onclick=\"return confirm('Are you sure?')\" value='Decline'>";
			echo "</form>";

			echo "<form style='display: inline;' action='$user_type.php?p=$p' method='post' name='formreminder'>";
			echo "<input type='hidden' name='action' value='app_reminder'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='c' value='$user'>";
			echo "<input type='hidden' name='a' value='$user'>";
			echo "<input type='hidden' name='h' value='$h'>";
			echo "<input type='submit' style='background-color:darkblue; color:white;' onclick=\"return confirm('Send reminder?')\" value='Reminder'>";
			echo "</form>";

			echo "<form style='display: inline;' action='$user_type.php?p=$p' method='post' name='formshortlist'>";
			echo "<input type='hidden' name='action' value='app_shortlist'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='c' value='$user'>";
			echo "<input type='hidden' name='a' value='$user'>";
			echo "<input type='hidden' name='h' value='$h'>";
			echo "<input type='submit' style='background-color:darkgreen; color:white;' onclick=\"return confirm('Place applicant on short list?')\" value='Shortlist'>";
			echo "</form>";
		} else {
			if ($appresponse  !== "") echo "Responded";
			if ($appshortlist !== "") {
				echo "<form style='display: inline;' action='$user_type.php?p=$p' method='post' name='formremoveshortlist'>";
				echo "<input type='hidden' name='action' value='app_removeshortlist'>";
				echo "<input type='hidden' name='p' value='$p'>";
				echo "<input type='hidden' name='c' value='$user'>";
				echo "<input type='hidden' name='a' value='$user'>";
				echo "<input type='hidden' name='h' value='$h'>";
				echo "<input type='submit' style='background-color:darkorange; color:white;' onclick=\"return confirm('Remove applicant from short list?')\" value='Remove from shortlist'>";
				echo "</form>";
			}
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
	echo "<br>";
	echo "<form style='display: inline;' action='$user_type.php?p=$p' method='post' name='formmassresponse'>";
	echo "<input type='hidden' name='action' value='app_massresponse'>";
	echo "<input type='hidden' name='p' value='$p'>";
	echo "<input type='hidden' name='c' value='$user'>";
	echo "<input type='hidden' name='a' value='$user'>";
	echo "<input type='submit' style='font-size:20px;background-color:black; color:white;' onclick=\"return confirm('This will decline all applicants that are not shortlisted - Are you sure?')\" value='Mass Decline'>";
	echo "</form>";
	echo "<br>";
	echo "<br>";
	echo "<form action='$user_type.php?p=$p' method='post' name='formresults'>";
	echo "<input type='hidden' name='action' value='results'>";
	echo "<input type='hidden' name='a' value='$user'>";
	echo "<input type='hidden' name='c' value='$user'>";
	echo "<input type='hidden' name='p' value='$p'>";
	echo "<input type='submit' value='Refresh'>";
	echo "</form>";
	echo "<form action='$user_type.php?p=$p#job$p' method='post' name='formback'>";
	echo "<input type='hidden' name='action' value=''>";
	echo "<input type='hidden' name='a' value='$user'>";
	echo "<input type='hidden' name='c' value='$user'>";
	echo "<input type='hidden' name='p' value='$p'>";
	echo "<input type='submit' value='Back'>";
	echo "</form>";
	echo "<br>";

	echo "<hr>\n";
	show_job_title_description($p);
	echo "</body>";
}



// Recursive delete of a folder
function rrmdir($src) {
	$dir = opendir($src);
	while(false !== ( $file = readdir($dir)) ) {
		if (( $file != '.' ) && ( $file != '..' )) {
			$full = $src . '/' . $file;
			if ( is_dir($full) ) {
				rrmdir($full);
			} else {
				unlink($full);
			}
		}
	}
	closedir($dir);
	rmdir($src);
}

// Recursively find the most recent file
function getMostRecentFile($dir) {
	$files = scandir($dir);
	$maxDate = null;
	$maxFile = null;
	foreach ($files as $file) {
		if ($file == '.' || $file == '..') {
			continue;
		}
		$filePath = $dir . '/' . $file;
		if (is_dir($filePath)) {
			$subFile = getMostRecentFile($filePath);
			if ($subFile !== null && ($maxDate === null || $subFile['date'] > $maxDate)) {
				$maxDate = $subFile['date'];
				$maxFile = $subFile['file'];
			}
		} else {
			$fileTime = filemtime($filePath);
			if ($maxDate === null || $fileTime > $maxDate) {
				$maxDate = $fileTime;
				$maxFile = $filePath;
			}
		}
	}
	return ($maxFile !== null && $maxDate !== null) ? array('file' => $maxFile, 'date' => $maxDate) : null;
}


function getMostRecentFileOfPosition($position, $expire_date) {

	$mostRecentFile = getMostRecentFile(file_positionsdir().'/'.$position);
	if ($mostRecentFile !== null && $mostRecentFile['date'] < $expire_date) {
		//echo 'The most recent file is more than 6 months old: ' . $mostRecentFile['file'];
		return True;
	} else {
		//echo 'The most recent file is not more than 6 months old.';
		return False;
	}
}







function select_gender() {

return '<select name="gender">
  <option value="">-- Select One --</option>
  <option value="male">Male</option>
  <option value="female">Female</option>
  <option value="other">Other</option>
  <option value="prefer not to say">Prefer not to say</option>
  </select>';

}

function select_nationality() {

return '<select name="nationality">
  <option value="">-- Select One --</option>
  <option value="afghan">Afghan</option>
  <option value="albanian">Albanian</option>
  <option value="algerian">Algerian</option>
  <option value="american">American</option>
  <option value="andorran">Andorran</option>
  <option value="angolan">Angolan</option>
  <option value="antiguans">Antiguans</option>
  <option value="argentinean">Argentinean</option>
  <option value="armenian">Armenian</option>
  <option value="australian">Australian</option>
  <option value="austrian">Austrian</option>
  <option value="azerbaijani">Azerbaijani</option>
  <option value="bahamian">Bahamian</option>
  <option value="bahraini">Bahraini</option>
  <option value="bangladeshi">Bangladeshi</option>
  <option value="barbadian">Barbadian</option>
  <option value="barbudans">Barbudans</option>
  <option value="batswana">Batswana</option>
  <option value="belarusian">Belarusian</option>
  <option value="belgian">Belgian</option>
  <option value="belizean">Belizean</option>
  <option value="beninese">Beninese</option>
  <option value="bhutanese">Bhutanese</option>
  <option value="bolivian">Bolivian</option>
  <option value="bosnian">Bosnian</option>
  <option value="brazilian">Brazilian</option>
  <option value="british">British</option>
  <option value="bruneian">Bruneian</option>
  <option value="bulgarian">Bulgarian</option>
  <option value="burkinabe">Burkinabe</option>
  <option value="burmese">Burmese</option>
  <option value="burundian">Burundian</option>
  <option value="cambodian">Cambodian</option>
  <option value="cameroonian">Cameroonian</option>
  <option value="canadian">Canadian</option>
  <option value="cape verdean">Cape Verdean</option>
  <option value="central african">Central African</option>
  <option value="chadian">Chadian</option>
  <option value="chilean">Chilean</option>
  <option value="chinese">Chinese</option>
  <option value="colombian">Colombian</option>
  <option value="comoran">Comoran</option>
  <option value="congolese">Congolese</option>
  <option value="costa rican">Costa Rican</option>
  <option value="croatian">Croatian</option>
  <option value="cuban">Cuban</option>
  <option value="cypriot">Cypriot</option>
  <option value="czech">Czech</option>
  <option value="danish">Danish</option>
  <option value="djibouti">Djibouti</option>
  <option value="dominican">Dominican</option>
  <option value="dutch">Dutch</option>
  <option value="east timorese">East Timorese</option>
  <option value="ecuadorean">Ecuadorean</option>
  <option value="egyptian">Egyptian</option>
  <option value="emirian">Emirian</option>
  <option value="equatorial guinean">Equatorial Guinean</option>
  <option value="eritrean">Eritrean</option>
  <option value="estonian">Estonian</option>
  <option value="ethiopian">Ethiopian</option>
  <option value="fijian">Fijian</option>
  <option value="filipino">Filipino</option>
  <option value="finnish">Finnish</option>
  <option value="french">French</option>
  <option value="gabonese">Gabonese</option>
  <option value="gambian">Gambian</option>
  <option value="georgian">Georgian</option>
  <option value="german">German</option>
  <option value="ghanaian">Ghanaian</option>
  <option value="greek">Greek</option>
  <option value="grenadian">Grenadian</option>
  <option value="guatemalan">Guatemalan</option>
  <option value="guinea-bissauan">Guinea-Bissauan</option>
  <option value="guinean">Guinean</option>
  <option value="guyanese">Guyanese</option>
  <option value="haitian">Haitian</option>
  <option value="herzegovinian">Herzegovinian</option>
  <option value="honduran">Honduran</option>
  <option value="hungarian">Hungarian</option>
  <option value="icelander">Icelander</option>
  <option value="indian">Indian</option>
  <option value="indonesian">Indonesian</option>
  <option value="iranian">Iranian</option>
  <option value="iraqi">Iraqi</option>
  <option value="irish">Irish</option>
  <option value="israeli">Israeli</option>
  <option value="italian">Italian</option>
  <option value="ivorian">Ivorian</option>
  <option value="jamaican">Jamaican</option>
  <option value="japanese">Japanese</option>
  <option value="jordanian">Jordanian</option>
  <option value="kazakhstani">Kazakhstani</option>
  <option value="kenyan">Kenyan</option>
  <option value="kittian and nevisian">Kittian and Nevisian</option>
  <option value="kuwaiti">Kuwaiti</option>
  <option value="kyrgyz">Kyrgyz</option>
  <option value="laotian">Laotian</option>
  <option value="latvian">Latvian</option>
  <option value="lebanese">Lebanese</option>
  <option value="liberian">Liberian</option>
  <option value="libyan">Libyan</option>
  <option value="liechtensteiner">Liechtensteiner</option>
  <option value="lithuanian">Lithuanian</option>
  <option value="luxembourger">Luxembourger</option>
  <option value="macedonian">Macedonian</option>
  <option value="malagasy">Malagasy</option>
  <option value="malawian">Malawian</option>
  <option value="malaysian">Malaysian</option>
  <option value="maldivan">Maldivan</option>
  <option value="malian">Malian</option>
  <option value="maltese">Maltese</option>
  <option value="marshallese">Marshallese</option>
  <option value="mauritanian">Mauritanian</option>
  <option value="mauritian">Mauritian</option>
  <option value="mexican">Mexican</option>
  <option value="micronesian">Micronesian</option>
  <option value="moldovan">Moldovan</option>
  <option value="monacan">Monacan</option>
  <option value="mongolian">Mongolian</option>
  <option value="moroccan">Moroccan</option>
  <option value="mosotho">Mosotho</option>
  <option value="motswana">Motswana</option>
  <option value="mozambican">Mozambican</option>
  <option value="namibian">Namibian</option>
  <option value="nauruan">Nauruan</option>
  <option value="nepalese">Nepalese</option>
  <option value="new zealander">New Zealander</option>
  <option value="ni-vanuatu">Ni-Vanuatu</option>
  <option value="nicaraguan">Nicaraguan</option>
  <option value="nigerien">Nigerien</option>
  <option value="north korean">North Korean</option>
  <option value="northern irish">Northern Irish</option>
  <option value="norwegian">Norwegian</option>
  <option value="omani">Omani</option>
  <option value="pakistani">Pakistani</option>
  <option value="palauan">Palauan</option>
  <option value="panamanian">Panamanian</option>
  <option value="papua new guinean">Papua New Guinean</option>
  <option value="paraguayan">Paraguayan</option>
  <option value="peruvian">Peruvian</option>
  <option value="polish">Polish</option>
  <option value="portuguese">Portuguese</option>
  <option value="qatari">Qatari</option>
  <option value="romanian">Romanian</option>
  <option value="russian">Russian</option>
  <option value="rwandan">Rwandan</option>
  <option value="saint lucian">Saint Lucian</option>
  <option value="salvadoran">Salvadoran</option>
  <option value="samoan">Samoan</option>
  <option value="san marinese">San Marinese</option>
  <option value="sao tomean">Sao Tomean</option>
  <option value="saudi">Saudi</option>
  <option value="scottish">Scottish</option>
  <option value="senegalese">Senegalese</option>
  <option value="serbian">Serbian</option>
  <option value="seychellois">Seychellois</option>
  <option value="sierra leonean">Sierra Leonean</option>
  <option value="singaporean">Singaporean</option>
  <option value="slovakian">Slovakian</option>
  <option value="slovenian">Slovenian</option>
  <option value="solomon islander">Solomon Islander</option>
  <option value="somali">Somali</option>
  <option value="south african">South African</option>
  <option value="south korean">South Korean</option>
  <option value="spanish">Spanish</option>
  <option value="sri lankan">Sri Lankan</option>
  <option value="sudanese">Sudanese</option>
  <option value="surinamer">Surinamer</option>
  <option value="swazi">Swazi</option>
  <option value="swedish">Swedish</option>
  <option value="swiss">Swiss</option>
  <option value="syrian">Syrian</option>
  <option value="taiwanese">Taiwanese</option>
  <option value="tajik">Tajik</option>
  <option value="tanzanian">Tanzanian</option>
  <option value="thai">Thai</option>
  <option value="togolese">Togolese</option>
  <option value="tongan">Tongan</option>
  <option value="trinidadian or tobagonian">Trinidadian or Tobagonian</option>
  <option value="tunisian">Tunisian</option>
  <option value="turkish">Turkish</option>
  <option value="tuvaluan">Tuvaluan</option>
  <option value="ugandan">Ugandan</option>
  <option value="ukrainian">Ukrainian</option>
  <option value="uruguayan">Uruguayan</option>
  <option value="uzbekistani">Uzbekistani</option>
  <option value="venezuelan">Venezuelan</option>
  <option value="vietnamese">Vietnamese</option>
  <option value="welsh">Welsh</option>
  <option value="yemenite">Yemenite</option>
  <option value="zambian">Zambian</option>
  <option value="zimbabwean">Zimbabwean</option>
  </select>';

}

?>
