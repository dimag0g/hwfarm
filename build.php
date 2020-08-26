<!-- HW Farm project (c) Dmitry Grigoryev, 2020 -->
<!-- Released under the terms of GNU AGPLv3 -->
<html><head><title>HW Farm</title><meta charset="UTF-8">
<style>
.build_log-content {max-height: 0px; overflow-y: scroll;}
.build_log-toggle:checked + .build_log-label + .build_log-content {max-height: 400px;}
</style>
<script src="lib/codemirror.js"></script>
<link rel="stylesheet" href="lib/codemirror.css">
<script src="mode/clike/clike.js"></script>
<script src="mode/tcl/expect.js"></script>
</head><body>
<h3>HW Farm - Build</h3>
<?php
session_start();
$sid = session_id();

if(isset($_POST['sketch'])) $_SESSION['sketch'] = $_POST['sketch'];
if(isset($_SESSION['board']))

{

$board = $_SESSION['board'];
echo("<pre><b>session ID</b>: $sid, <b>board</b>: $board</pre>");
if(isset($_POST['baudrate'])) $_SESSION['baudrate'] = $_POST['baudrate'];
else $_SESSION['baudrate'] = 9600;
if(isset($_POST['timeout'])) $_SESSION['timeout'] = $_POST['timeout'];
else $_SESSION['timeout'] = 10;
if(isset($_POST['input'])) $_SESSION['input'] = $_POST['input'];
$sketch = $_SESSION['sketch'];
$baudrate = $_SESSION['baudrate'];
$timeout = $_SESSION['timeout'];

if(isset($sketch))
{
    exec("printf %s " . escapeshellarg($sketch) . "| schroot -c hwfarm -d / -- tee /sessions/$sid/sketch.ino");
    unset($output);
    exec("UART=$uart schroot -c hwfarm -d /sessions/$sid -- make 2>&1", $output, $retval);
    $output = implode("\n", $output);
    if($retval == 0) echo "<hr><p><b>Build done</b></p>";
    else echo "<hr><p><b>Build failed</b></p>";
    echo <<<EOF

<div><input id="build_log" class="build_log-toggle" type="checkbox">
<label for="build_log" class="build_log-label">Build log:</label>
<div class="build_log-content"><div>
EOF;
    echo "<pre>$output</pre></div></div></div>";
}

} else {
    echo("<b>Session expired</b>");
    echo("<form action=\"select.php\" method=\"post\"><p><input type=\"submit\" value=\"< Select config\"></p></form>");
    echo("</body></html>");
    exit;
}
?>

<hr>
<table><tr><td valign=bottom>
<form action="select.php" method="post">
    <input type="submit" value="<< Select config">
</form>
</td><td valign=bottom>
<?php
if($retval == 0) {
    if(isset($_SESSION['input'])) {
        $input = $_SESSION['input'];
    } else {
        $input = shell_exec("schroot -c hwfarm -d / -- cat /configs/$board/input.exp");
    }
    echo "<form action=sketch.php method=post><input type=submit value=\"< Edit sketch\"></form></td><td valign=bottom>";
    echo "<form action=run.php method=post>";
    echo "<p><b>Input</b> (<a href=expect.html>syntax help</a>):<br/><textarea id=expect name=input cols=80 rows=10>$input</textarea></p>";
    echo "<p>Baudrate: <input type=text name=baudrate value=\"$baudrate\"> bps</p>";
    echo "<p>Timeout: <input type=text name=timeout value=\"$timeout\"> s</p>";
    echo "<p><input type=submit value=\"Run >\"></p></form>";
} else {
    echo "<form action=build.php method=post>";
    echo "<p><b>Sketch</b>:<br/><textarea id=cpp name=sketch cols=80 rows=20 style=\"font-family:monospace;\">$sketch</textarea></p>";
    echo "<p><input type=submit value=\"Rebuild sketch\"></p></form>";
}
?>
</td></tr></table>
<script>
var te = document.getElementById("cpp");
CodeMirror.fromTextArea(te, {lineNumbers: true, matchBrackets: true, mode:  "text/x-c++src"});
</script>
<script>
var te = document.getElementById("expect");
CodeMirror.fromTextArea(te, {lineNumbers: true, matchBrackets: true, mode:  "text/x-expect"});
</script>
</body></html>
