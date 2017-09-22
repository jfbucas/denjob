<?php
include 'func.php';

$msg = "";
$error = "";

function get_page($p, $a, $msg = "", $error = "") {
	$assname  = get_assname($p, $a);
	$assemail = get_assemail( $p, $a); 

	echo "<h2> Welcome $assname, you will be able to review the applicants <h2>\n";

	if ($msg != "") {
		echo "<h4> <font color=green>$msg</font> </h4>";
	}
	if ($error != "") {
		echo "<h4> <font color=red>$error</font> </h4>";
	}

	
	#echo "<h3> Applicants </h3>\n";
	#echo "<h5> List of current referees </h5>\n";

	$applicants = get_applicants($p);
	$applicants = explode( "\n", $applicants );
	echo "<table style='border: 1px solid lightgray; border-collapse: collapse;'>\n";
	echo "<tr style='border: 1px solid lightgray; padding:10px;'><th>Applicants</th><th>Referees</th><th align=center>Qualifies ?<br><font color=green>Yes</font> / <font color=orange>Maybe</font> / <font color=red>No</font></th></tr>\n";
	foreach ($applicants as $appemail) {
		$h=do_hash($appemail);
		if (!valid_p_h( $p, $h )) continue;

		$appname = get_appname($p, $h);
		echo "<tr style='border: 1px solid lightgray; padding:10px;'><td valign=middle style='padding:10px'>";
		if (file_exists(file_apppdf($p, $h))) {
			echo "<div><a href=". file_apppdf($p, $h)." > $appname &lt;$appemail&gt; <img height=20px width=20px src=pdf.png> </a></div>\n";
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
				echo "<li><div><a href=".file_refpdf($p, $h, $rh)." > $refname &lt;$refemail&gt; <img height=20px width=20px src=pdf.png></a></div></li>\n";

			} else {
				echo "<li><div alt='no reference letter available yet'>$refname &lt;$refemail&gt;</div></li>";
			}
		}
		echo "</ul></td><td align=center valign=middle style='padding:10px'>";

		$v = get_appscore($p, $h, $a);
		if ($v == "Y") { $ycolor = "#24ff24"; }else{ $ycolor = "#004b00"; }
		if ($v == "M") { $mcolor = "#ff9224"; }else{ $mcolor = "#b75c00"; }
		if ($v == "N") { $ncolor = "#ff2424"; }else{ $ncolor = "#4b0000"; }

		echo "<form style='display: inline;' action='assessor.php?p=$p&a=$a' method='post' name='formqualifies'>";
		echo "<input type='hidden' name='action' value='app_qualifies'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='a' value='$a'>";
		echo "<input type='hidden' name='h' value='$h'>";
		echo "<input type='hidden' name='v' value='Y'>";
		echo "<input type='submit' style='background-color:$ycolor; color:white;' value=' '>";
		echo "</form>";

		echo "<form style='display: inline;' action='assessor.php?p=$p&a=$a' method='post' name='formqualifies'>";
		echo "<input type='hidden' name='action' value='app_qualifies'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='a' value='$a'>";
		echo "<input type='hidden' name='h' value='$h'>";
		echo "<input type='hidden' name='v' value='M'>";
		echo "<input type='submit' style='background-color:$mcolor; color:white;' value=' '>";
		echo "</form>";

		echo "<form style='display: inline;' action='assessor.php?p=$p&a=$a' method='post' name='formqualifies'>";
		echo "<input type='hidden' name='action' value='app_qualifies'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='a' value='$a'>";
		echo "<input type='hidden' name='h' value='$h'>";
		echo "<input type='hidden' name='v' value='N'>";
		echo "<input type='submit' style='background-color:$ncolor; color:white;' value=' '>";
		echo "</form>";


		echo "</td></tr>";
	}
	echo "</table>";
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
	valid_p_a($p, $a) or die("Invalid URL");

        $action = $_POST['action'];
	switch ($action) {
		case "app_qualifies":
		        $h = $_POST['h'];
		        $v = $_POST['v'];
			valid_p_h($p, $h) or die("Invalid Applicant");
			if (!(( $v == "Y" ) || ( $v == "N" ) || ( $v == "M" )) ) die("Invalid score");

			$fh = fopen(file_appscore($p, $h, $a), 'w+') or die("Can't open score file");
			fwrite($fh, $v);
			fclose($fh);

			break;
		case "refresh" : break;
	}

	get_page($p, $a, $msg, $error);

}

