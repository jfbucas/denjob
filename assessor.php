<?php
include 'func.php';


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
	echo "<table style='border: 1px solid gray; border-collapse: collapse;'>\n";
	echo "<tr style='border: 1px solid gray; padding:10px;'><th>Applicants</th><th>Referees</th><th>Qualifies ?</th></tr>\n";
	foreach ($applicants as $appemail) {
		$h=do_hash($appemail);
		if (!valid_p_h( $p, $h )) continue;

		$appname = get_appname($p, $h);
		echo "<tr style='border: 1px solid gray; padding:10px;'><td style='padding:10px'>";
		if (file_exists(file_apppdf($p, $h))) {
			echo "<div><a href=". file_apppdf($p, $h)." > $appname ($appemail) - CV <img height=30px widht=30px src=pdf.png> </a></div>\n";
		} else {
			echo "<div> $appname ($appemail) - No CV available yet</div>\n";
		}
		echo "</td><td style='padding:10px'><ul>";

		$referees = get_referees($p, $h);
		$referees = explode( "\n", $referees );
		foreach ($referees as $refemail) {
			$rh=do_hash($refemail);
			if (!valid_p_h_rh( $p, $h, $rh )) continue;

			$refname  = get_refname($p, $h, $rh);
			$refemail = get_refemail($p, $h, $rh);
			if (file_exists(file_refpdf($p, $h, $rh))) {
				echo "<li><div><a href=".file_refpdf($p, $h, $rh)." > $refname ($refemail) - Cover letter <img height=20px widht=20px src=pdf.png></a></div></li>\n";

			} else {
				echo "<li><div>$refname ($refemail) - No cover letter yet</div></li>";
			}
		}
		echo "</ul></td><td style='padding:10px'>";

		echo "<form style='display: inline;' action='assessor.php?p=$p&a=$a' method='post' name='formqualifies'>";
		echo "<input type='hidden' name='action' value='app_qualifies'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='a' value='$a'>";
		echo "<input type='hidden' name='h' value='$h'>";
		echo "<input type='hidden' name='v' value='Y'>";
		echo "<input type='submit' style='background-color:darkgreen; color:white;' value=' Yes '>";
		echo "</form>";

		echo "<form style='display: inline;' action='assessor.php?p=$p&a=$a' method='post' name='formqualifies'>";
		echo "<input type='hidden' name='action' value='app_qualifies'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='a' value='$a'>";
		echo "<input type='hidden' name='h' value='$h'>";
		echo "<input type='hidden' name='v' value='M'>";
		echo "<input type='submit' style='background-color:darkorange; color:white;' value='Maybe'>";
		echo "</form>";

		echo "<form style='display: inline;' action='assessor.php?p=$p&a=$a' method='post' name='formqualifies'>";
		echo "<input type='hidden' name='action' value='app_qualifies'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='a' value='$a'>";
		echo "<input type='hidden' name='h' value='$h'>";
		echo "<input type='hidden' name='v' value='N'>";
		echo "<input type='submit' style='background-color:darkred; color:white;' value=' No  '>";
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
		case "refresh" : break;
	}

	get_page($p, $a, $msg, $error);

}

