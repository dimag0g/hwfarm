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

$board = $_SESSION['board'];
echo("<pre><b>session ID</b>: $sid, <b>board</b>: $board</pre>");
$work_path="hw";
mkdir("$work_path/sessions/$sid");
mkdir("$work_path/sessions/$sid/build");
exec("ln -s \"$work_path/configs/$board/build/core\" \"$work_path/sessions/$sid/build\"");
exec("ln -s \"$work_path/configs/$board/build/libs\" \"$work_path/sessions/$sid/build\"");
exec("ln -s \"$work_path/configs/$board/build/userlibs\" \"$work_path/sessions/$sid/build\"");
exec("ln -s \"$work_path/configs/$board/Makefile\" \"$work_path/sessions/$sid\"");
if(isset($_SESSION['sketch']))

{

$sketch = $_SESSION['sketch'];
} else {
    $file = fopen("$work_path/configs/$board/sketch.ino", "r");
    $sketch = fread($file, filesize("$work_path/configs/$board/sketch.ino"));
    fclose($file);
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
