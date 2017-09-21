<?php
include 'func.php';

$msg = "";
$error = "";

function get_page($p, $h, $rh, $msg = "", $error = "") {
	$appname  = get_appname($p, $h);
	$appemail = get_appemail($p, $h);
	$refname  = get_refname($p, $h, $rh);
	$refemail = get_refemail($p, $h, $rh);

	echo "<h2>Welcome $refname, Please upload the cover letter for $appname ($appemail) </h2>\n";

	if ($msg != "") {
		echo "<h4> <font color=green>$msg</font> </h4>";
	}
	if ($error != "") {
		echo "<h4> <font color=red>$error</font> </h4>";
	}


	echo "<div style='border: 1px solid lightgray; padding: 10px;'>";
	echo "<h3> Reference Letter </h3>\n";
	echo '<form enctype="multipart/form-data" action="referee.php?p='.$p.'&h='.$h.'&rh='.$rh.'" method="post" name="form_cover">';
	echo 'Please select reference letter as a PDF file to upload (Max:20MB) <input name="cover" size="40" type="file" accept="application/pdf">';
	echo '<input type="hidden" name="action" value="upload_cover">';
	echo '<input type="hidden" name="p" value="' . $p . '">';
	echo '<input type="hidden" name="h" value="' . $h . '">';
	echo '<input type="hidden" name="rh" value="' . $rh . '">';
	echo '<input type="hidden" name="MAX_FILE_SIZE" value="20000000" />';
	echo "<input type='submit' value='Upload'>";
	echo '</form>';

	if (file_exists(file_refpdf($p, $h, $rh))) {
		echo "<a href=".file_refpdf($p, $h, $rh)."> <img height=50px width=50px src=pdf.png><br> Reference letter </a><br>";
	}
	echo "</div>";

	$finalizable="disabled";
	if (file_exists(file_refpdf($p, $h, $rh))) {
		$finalizable="";
	}
	echo "<br>";
	echo "<form action='referee.php?p=$p&h=$h&rh=$rh' method='post' name='formfinish'>";
	echo "<input type='hidden' name='action' value='finish'>";
	echo "<input type='hidden' name='p' value='$p'>";
	echo "<input type='hidden' name='h' value='$h'>";
	echo "<input type='hidden' name='rh' value='$rh'>";
	echo "<input type='submit' value='Finalize' $finalizable>";
	echo "</form>";

	echo "<hr>\n";
	show_job_title_description($p);
}

if ($_SERVER['REQUEST_METHOD'] == "GET") {
        $p  = $_GET['p'];
        $h  = $_GET['h'];
        $rh = $_GET['rh'];
	valid_p_h_rh($p, $h, $rh) or die("Invalid URL");

	get_page($p, $h, $rh);

} else if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $p  = $_POST['p'];
        $h  = $_POST['h'];
        $rh = $_POST['rh'];
	valid_p_h_rh($p, $h, $rh) or die("Invalid URL");

        $action = $_POST['action'];
	$msg="";
	$error="";

	switch ($action) {
		case "upload_cover" :
			$msg="Reference letter uploaded";
			$error="";
			if (! move_uploaded_file( $_FILES['cover']['tmp_name'], file_refpdf($p, $h, $rh)) ) {
				$msg="";
				$error="Reference letter couldn't be uploaded";
			}			
			break;

		case "finish" :

			$msg="Thank you for your reference letter. You may come back and change it until the review process has started.";
			$error="";

			break;

	}
	get_page($p, $h, $rh, $msg, $error);
}

