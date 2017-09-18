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

function file_chairmans(){
	return file_positionsdir()."/".do_hash("chairmans");
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
function file_jobstatus($p){ 
	return file_positionsdir()."/$p/status"; #".do_hash("status");
}
function file_applicants($p){
	return file_positionsdir()."/$p/".do_hash("applicants");
}
function file_assessors($p){
	return file_positionsdir()."/$p/".do_hash("assessors");
}
function file_appdir($p, $h){ 
	return file_jobdocuments($p)."/$h";
}
function file_appname($p, $h){ 
	return file_jobdocuments($p)."/$h/".do_hash("name");
}
function file_appemail($p, $h){ 
	return file_jobdocuments($p)."/$h/".do_hash("email");
}
function file_apppdf($p, $h){
	return file_jobdocuments($p)."/$h/".do_hash($h."pdf").".pdf";
}
function file_referees($p, $h) {
	return file_jobdocuments($p)."/$h/".do_hash("referees");
}
function file_refdir($p, $h, $rh){
	return file_jobdocuments($p)."/$h/$rh";
}
function file_refname($p, $h, $rh){
	return file_jobdocuments($p)."/$h/$rh/".do_hash("name");
}
function file_refemail($p, $h, $rh){
	return file_jobdocuments($p)."/$h/$rh/".do_hash("email");
}
function file_refpdf($p, $h, $rh){
	return file_jobdocuments($p)."/$h/$rh/".do_hash($rh."pdf").".pdf";
}
function file_assdir($p, $a){ 
	return file_jobpanel($p)."/$a";
}
function file_assname($p, $a){ 
	return file_jobpanel($p)."/$a/".do_hash("name");
}
function file_assemail($p, $a){ 
	return file_jobpanel($p)."/$a/".do_hash("email");
}


function get_chairmans(){
	if (!file_exists(file_chairmans())) return "";
	$s = file_get_contents(file_chairmans()) or die("Unable to open file chairmans!");
	return $s;
}
function get_jobtitle($p){ 
	$s = file_get_contents(file_jobtitle($p)) or die("Unable to open file jobtitle! ".file_jobtitle($p));
	return $s;
}
function get_jobdesc($p){ 
	$s = file_get_contents(file_jobdesc($p)) or die("Unable to open file jobdesc! ".file_jobdesc($p));
	return $s;
}
function get_jobstatus($p){ 
	$s = file_get_contents(file_jobstatus($p)) or die("Unable to open file jobstatus! ".file_jobstatus($p));
	return $s;
}
function get_applicants($p){
	if (!file_exists(file_applicants($p))) return "";
	$s = file_get_contents(file_applicants($p)) or die("Unable to open file applicants!");
	return $s;
}
function get_appname($p, $h){ 
	$s = file_get_contents(file_appname($p, $h)) or die("Unable to open file applicant name!");
	return $s;
}
function get_appemail($p, $h){ 
	$s = file_get_contents(file_appemail($p, $h)) or die("Unable to open file applicant email!");
	return $s;
}
function get_referees($p, $h) {
	if (!file_exists(file_referees($p, $h))) return "";
	$s = file_get_contents(file_referees($p, $h)) or die("Unable to open file referees!");
	return $s;
}
function get_refname($p, $h, $rh){
	$s = file_get_contents(file_refname($p, $h, $rh)) or die("Unable to open file referee name!");
	return $s;
}
function get_refemail($p, $h, $rh){
	$s = file_get_contents(file_refemail($p, $h, $rh)) or die("Unable to open file referee email!");
	return $s;
}
function get_assessors($p){
	if (!file_exists(file_assessors($p))) return "";
	$s = file_get_contents(file_assessors($p)) or die("Unable to open file assessors!");
	return $s;
}
function get_assname($p, $a){ 
	$s = file_get_contents(file_assname($p, $a)) or die("Unable to open file assessors name!");
	return $s;
}
function get_assemail($p, $a){ 
	$s = file_get_contents(file_assemail($p, $a)) or die("Unable to open file assessor email!");
	return $s;
}


# Validation for parameters

function valid_p($ptest) {
	if ( !is_numeric($ptest) ) return false;
	$positions = scandir(file_positionsdir());
	foreach ($positions as $p) {
		if (!file_exists(file_jobtitle($p))) continue;
		if (!file_exists(file_jobdesc($p))) continue;
		if ($p == $ptest) return true;
	}
	return false;
}
function valid_h($p, $htest) {
	if (strlen($htest) != 64) return false;
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
function valid_name_email($name, $email){
	return valid_name($name) and valid_email($email);
}

function valid_c($ctest) {
	if (strlen($ctest) != 64) return false;
	$chairmans = get_chairmans();
	$chairmans = explode( "\n", $chairmans );
	foreach ($chairmans as $chairemail) {
		if ( $ctest == do_hash($chairemail) )  return true;
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


# Sending emails

function mail_applicant($appname, $appemail, $jobtitle, $p, $h) {
	global $EMAIL_HEADER, $APP_SUBJECT, $APP_MAIN;

	$to = "$appemail";

	$main = $APP_MAIN;
	$main = str_replace( "%appname", $appname, $main );
	$main = str_replace( "%appemail", $appemail, $main );
	$main = str_replace( "%jobtitle", $jobtitle, $main );
	$main = str_replace( "%p", $p, $main );
	$main = str_replace( "%h", $h, $main );

	mail ($to, $APP_SUBJECT, $main, $EMAIL_HEADER ) ;
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

	mail ($to, $REF_SUBJECT, $main, $EMAIL_HEADER ) ;
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

	mail ($to, $ASS_SUBJECT, $main, $EMAIL_HEADER ) ;
}

function mail_chairman($chairemail) {
	global $EMAIL_HEADER, $CHAIR_SUBJECT, $CHAIR_MAIN;

	$to = "$chairemail";

	$main = $CHAIR_MAIN;
	$main = str_replace( "%c", do_hash($chairemail), $main );

	mail ($to, $CHAIR_SUBJECT, $main, $EMAIL_HEADER ) ;
}
?>
