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

	
	echo "<h3> Applicants </h3>\n";
	
	#echo "<h5> List of current referees </h5>\n";

	$applicants = get_applicants($p);
	$applicants = explode( "\n", $applicants );
	echo "<ul>\n";
	foreach ($applicants as $appemail) {
		$h=do_hash($appemail);
		if (!valid_p_h( $p, $h )) continue;

		$rh=do_hash($refemail);

		$appname = get_appname($p, $h);
		echo "<li>";
		if (file_exists(file_apppdf($p, $h))) {
			echo "<div><a href=". file_apppdf($p, $h)." > <img height=30px widht=30px src=pdf.png>$appname ($appemail) - CV </a></div>\n";
		} else {
			echo "<div> $appname ($appemail) - No CV available yet</div>\n";
		}

		echo "List of referees:\n";
		$referees = get_referees($p, $h);
		$referees = explode( "\n", $referees );
		echo "<ul>\n";
		foreach ($referees as $refemail) {
			$rh=do_hash($refemail);
			if (!valid_p_h_rh( $p, $h, $rh )) continue;

			$refname  = get_refname($p, $h, $rh);
			$refemail = get_refemail($p, $h, $rh);
			echo "<li>";
			if (file_exists(file_refpdf($p, $h, $rh))) {
				echo "<div><a href=".file_refpdf($p, $h, $rh)." > <img height=20px widht=20px src=pdf.png>$refname ($refemailCover) - Cover letter</a></div>\n";

			} else {
				echo "<div>$refname ($refemail) - No cover letter yet</div>";
			}
			echo "</li>";
		}
		echo "</ul><br><br>";
		/*

		echo "<form action='applicant.php' method='post' name='formsendrefmail'>";
		echo "<input type='hidden' name='action' value='sendrefmail'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='h' value='$h'>";
		echo "<input type='hidden' name='rh' value='$rh'>";
		echo "<input type='submit' value='Re-Send link'>";
		echo "</form>";
		
		echo "<form action='applicant.php' method='post' name='formdelref'>";
		echo "<input type='hidden' name='action' value='delref'>";
		echo "<input type='hidden' name='p' value='$p'>";
		echo "<input type='hidden' name='h' value='$h'>";
		echo "<input type='hidden' name='rh' value='$rh'>";
		echo "<input type='submit' value='Delete'>";
		echo "</form>";
		*/

		echo "</li>";
	}
	echo "</ul>";
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

