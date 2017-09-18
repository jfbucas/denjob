<?php
include 'func.php';


function get_page($c, $msg = "", $error = "") {
	echo "<h2>Welcome Chairman, you will be able to manage the assessors for each positions below <h2>\n";

	if ($msg != "") {
		echo "<h4> <font color=green>$msg</font> </h4>";
	}
	if ($error != "") {
		echo "<h4> <font color=red>$error</font> </h4>";
	}

	echo "<h3> Positions </h3>\n";
	echo "<hr>";
	$positions = array_reverse(scandir(file_positionsdir()));
	foreach ($positions as $p) {
		if ( !is_numeric($p) ) continue;

		$jobtitle = get_jobtitle($p);
		$jobdesc  = get_jobdesc($p);
		echo "<h4>" . $jobtitle . "</h3>\n";

		$status = get_jobstatus($p);
		$open = strpos($status, "open" );
		if ( $open !== false ) {
			echo "Status: <font color=green> Open </font><br>\n";
			echo "<form action='chairman.php?c=$c' method='post' name='formcloseposition'>";
			echo "<input type='hidden' name='action' value='closeposition'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='c' value='$c'>";
			echo "<input type='submit' value='Close'>";
			echo "</form>";
		} else {
			echo "Status: <font color=red> Closed </font><br>\n";
			echo "<form action='chairman.php?c=$c' method='post' name='formopenposition'>";
			echo "<input type='hidden' name='action' value='openposition'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='c' value='$c'>";
			echo "<input type='submit' value='Open'>";
			echo "</form>";
		}
	
		echo "<h5> Add an assessor </h5>\n";
		echo "<form action='chairman.php?c=$c' method='post' name='formaddass'>\n";
		echo "Assessor Name <input type='text' name='assname' maxlength='50' value=''><br>\n";
		echo "Assessor Email <input type='email' name='assemail' maxlength='50' value=''><br>\n";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='c' value='$c'>";
		echo '<input type="hidden" name="action" value="add_assessor">';
		echo "<input type='submit' value='Submit'>";
		echo "</form>";

		echo "<h5> List of current assessors </h5>\n";

		$assessors = get_assessors($p);
		$assessors = explode( "\n", $assessors );
		echo "<ul>\n";
		foreach ($assessors as $assemail) {
			if ( $assemail == "" ) continue;

			$a=do_hash($assemail);
			if (!valid_p_a($p, $a)) continue;

			$assname = get_assname($p, $a);
			echo "<li>  $assname ($assemail) ";
			echo "<form action='chairman.php?c=$c' method='post' name='formdelass'>";
			echo "<input type='hidden' name='action' value='del_assessor'>";
			echo "<input type='hidden' name='p' value='$p'>";
			echo "<input type='hidden' name='c' value='$c'>";
			echo "<input type='hidden' name='a' value='$a'>";
			echo "<input type='submit' value='Delete'>";
			echo "</form>";
			echo "</li>";
		}
		echo "</ul>";
		echo "<hr>";
	}
}


function sendassmail( $p, $a ) {

	$assname  = get_assname($p, $a);
	$assemail = get_assemail($p, $a);
	$jobtitle = get_jobtitle($p);

	# email unique link to fill details
	mail_assessor($assname, $assemail, $jobtitle, $p, $a);
}

function sendchairmail( $chairemail ) {

	# email unique link to fill details
	mail_chairman($chairemail);
}

if ( isset($argv) ){ 
	if (!is_dir( file_positionsdir() ) ) {
		mkdir( file_positionsdir() ) or die( "Cannot create positions folder ".file_positionsdir() );
		chown( file_positionsdir(), "www-data" );
	}
	if (!file_exists( file_chairmans() ) ) { 
		echo "Creating chairman file ". file_chairmans() . "\n";
		echo "Please add email address per line and restart the command\n";
		$fh = fopen(file_chairmans(), 'w+') or die("can't open file ".file_chairmans());
		fclose($fh);
	} else {
		echo "Sending login addresses to chairman emails\n";
		$chairmans = get_chairmans();
		$chairmans = explode( "\n", $chairmans );
		foreach ($chairmans as $chairemail) {
			if ( valid_email($chairemail) ) {
				echo " -> $chairemail\n";
				sendchairmail( $chairemail );
			}
		}
	}
	
} else if ($_SERVER['REQUEST_METHOD'] == "GET") {
        $c = $_GET['c'];
	valid_c($c) or die("Invalid Chairman URL");

	get_page($c);

} else if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $p = $_POST['p'];
        $c = $_POST['c'];
	valid_p($p) or die("Invalid URL");
	valid_c($c) or die("Invalid Chairman URL");

        $action = $_POST['action'];
	switch ($action) {
		case "closeposition":
			$fh = fopen(file_jobstatus($p), 'w+');
			fwrite($fh, "close");
			fclose($fh);
			$msg="Position $p closed";
			$error="";
			break;
		case "openposition":
			$fh = fopen(file_jobstatus($p), 'w+') or die("can't open file");
			fwrite($fh, "open");
			fclose($fh);
			$msg="Position $p open";
			$error="";
			break;

		case "add_assessor" :

		        $assname  = $_POST['assname'];
		        $assemail = $_POST['assemail'];
			valid_name_email($assname, $assemail);

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
				fwrite($fh, $assname);
				fclose($fh);
				$fh = fopen(file_assemail($p, $a), 'w+') or die("can't open file");
				fwrite($fh, $assemail);
				fclose($fh);

				sendassmail($p, $a);
				$msg="Assessor added and email sent";
				$error="";
			} else {
				$msg="";
				$error="Assessor already in the list";
			}

			break;
		case "sendassmail" :

		        $p  = $_POST['p'];
		        $a  = $_POST['a'];
			valid_p_a($p, $a) or die("Invalid URL");

			$msg="Reminder sent to assessor";
			$error="";
			sendassmail($p, $a);

			break;

		case "del_assessor" :

		        $p  = $_POST['p'];
		        $c  = $_POST['c'];
		        $a  = $_POST['a'];
			valid_p_a($p, $a, $rh) or die("Invalid URL");

			$assemail = get_assemail($p, $a);

			# add address to referees
			$assessors = get_assessors($p);
			if(strpos($assessors, $assemail) === true) {
				$assessors = str_replace( $assmail."\n", "", $assessors );
				$fh = fopen(file_assessors($p), 'w+') or die("can't open file");
				fwrite($fh, $assessors);
				fclose($fh);

				$msg="Assessor has been deleted";
				$error="";
			}else{
				$msg="";
				$error="Assessor $assemail not found";
			}
			break;
		case "refresh" : break;
	}

	get_page($c, $msg, $error);
}

