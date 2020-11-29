<!-- HW Farm project (c) Dmitry Grigoryev, 2020 -->
<!-- Released under the terms of GNU AGPLv3 -->
<html><head><title>HW Farm</title><meta charset="UTF-8">
<script src="lib/codemirror.js"></script>
<link rel="stylesheet" href="lib/codemirror.css">
<script src="mode/clike/clike.js"></script>
</head><body>
<h3>HW Farm - Code</h3>
<?php
session_start();
$sid = session_id();

if(isset($_POST['board'])) $_SESSION['board'] = $_POST['board'];
#else $_SESSION['board'] = "ATmega328P";
if(isset($_SESSION['board'])) {

#$board = escapeshellarg($_SESSION['board']);
$board = $_SESSION['board'];
$hwroot = trim(shell_exec("schroot -c hwfarm --location"));
echo("<pre><b>session ID</b>: $sid, <b>board</b>: $board</pre>");

mkdir("$hwroot/sessions/$sid");
mkdir("$hwroot/sessions/$sid/build");
mkdir("$hwroot/configs/$board/build");
mkdir("$hwroot/configs/$board/build/core");
mkdir("$hwroot/configs/$board/build/libs");
mkdir("$hwroot/configs/$board/build/userlibs");
mkdir("$hwroot/configs/$board/build/platformlibs");
symlink("/configs/$board/build/core", "$hwroot/sessions/$sid/build/core");
symlink("/configs/$board/build/libs", "$hwroot/sessions/$sid/build/libs");
symlink("/configs/$board/build/userlibs", "$hwroot/sessions/$sid/build/userlibs");
symlink("/configs/$board/build/platformlibs", "$hwroot/sessions/$sid/build/platformlibs");
symlink("/configs/$board/Makefile", "$hwroot/sessions/$sid/Makefile");
if(isset($_SESSION['sketch']))

{

$sketch = $_SESSION['sketch'];
} else {
    $sketch = file_get_contents("$hwroot/configs/$board/sketch.ino");
}

} else {
    echo("<b>Session expired</b>");
    echo("<form action=select.php method=post><p><input type=submit value=\"< Select config\"></p></form>");
    echo("</body></html>");
    exit;
}

?>

<table><tr><td valign=bottom>
<form action="select.php" method="post">
    <p><input type="submit" value="< Select config"></p>
</form>
</td><td>
<form action="build.php" method="post">
    <p><b>Sketch</b>:<br/><textarea id="cpp" name="sketch" cols="80" rows="20" style="font-family:monospace;"><?php echo("$sketch"); ?></textarea></p>
    <p><input type="submit" value="Build sketch >"></p>
</form>
</td></tr></table>
<script>
var te = document.getElementById("cpp");
CodeMirror.fromTextArea(te, {lineNumbers: true, matchBrackets: true, mode:  "text/x-c++src"});
</script>
</body></html>
