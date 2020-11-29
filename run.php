<!-- HW Farm project (c) Dmitry Grigoryev, 2020 -->
<!-- Released under the terms of GNU AGPLv3 -->
<html><head><title>HW Farm</title><meta charset="UTF-8">
<style>
.upload_log-content {max-height: 0px; overflow-y: scroll;}
.upload_log-toggle:checked + .upload_log-label + .upload_log-content {max-height: 400px;}
.run_log-content {max-height: 0px; overflow-y: scroll;}
.run_log-toggle:checked + .run_log-label + .run_log-content {max-height: 400px;}
</style>
<script src="lib/codemirror.js"></script>
<link rel="stylesheet" href="lib/codemirror.css">
<script src="mode/tcl/expect.js"></script>
</head><body>
<h3>HW Farm - Run</h3>
<?php
session_start();
$sid = session_id();

if(isset($_SESSION['board']))

{

$board = $_SESSION['board'];
$hwroot = trim(shell_exec("schroot -c hwfarm --location"));

$hwlist = array_map('str_getcsv', file("hwlist.csv"));
foreach($hwlist as $data) {
    if($board == $data[0]) {
        $uart = $data[1];
    }
}
if(isset($_POST['baudrate'])) $_SESSION['baudrate'] = $_POST['baudrate'];
if(isset($_POST['timeout'])) $_SESSION['timeout'] = $_POST['timeout'];
if(isset($_POST['input'])) $_SESSION['input'] = $_POST['input'];
$baudrate = $_SESSION['baudrate'];
$baudrate = intval($baudrate);
if($baudrate == 0) $baudrate = 9600;
$timeout = $_SESSION['timeout'];
$timeout = intval($timeout);
if($timeout == 0) $timeout = 10;
if($timeout > 20) $timeout = 20;
echo("<pre><b>session ID</b>: $sid");
echo(", <b>board</b>: $board");
echo(", <b>baudrate</b>: $baudrate bps");
echo(", <b>timeout</b>: $timeout s");
#echo(", <b>uart</b>: $uart</pre>");
echo("</pre>");
if(isset($_SESSION['input'])) $input = $_SESSION['input'];
else $input = "expect eof";

file_put_contents("$hwroot/sessions/$sid/input.exp", $input);

exec("schroot -c hwfarm -d / -- lsof $uart", $output, $retval);
if($retval == 1)
{
    unset($output);
    exec("UART=$uart schroot -c hwfarm -d /sessions/$sid timeout 10 make upload 2>&1", $output, $retval);
    $output = htmlspecialchars(implode("\n", $output));
    if($retval == 0) echo "<hr><p><b>Upload done</b></p>";
    else echo "<hr><p><b>Upload failed</b></p>";
    echo <<<EOF

    <div><input id="upload_log" class="upload_log-toggle" type="checkbox">
    <label for="upload_log" class="upload_log-label">Upload log:</label>
    <div class="upload_log-content"><div>
    EOF;
    echo "<pre>$output</pre></div></div></div>";

    if($retval == 0) {
        $output = shell_exec("schroot -c hwfarm -d / -- stty -F $uart $baudrate cooked");
        #$output = shell_exec("schroot -c hwfarm -d / -- timeout $timeout stdbuf -oL cat $uart");
        $output = shell_exec("schroot -c hwfarm -d /sessions/$sid -- timeout $timeout stdbuf -oL expect /safe.exp $uart input.exp 2>&1");
        $output = htmlspecialchars($output);
        echo "<hr><p><b>Output</b>:</p><pre>$output</pre>";
    }
} else echo "<hr><p><b>Board busy</b></p>";

} else {
    echo("<b>Session expired</b>");
    echo("<form action=select.php method=post><p><input type=submit value=\"< Select config\"></p></form>");
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
<form action="sketch.php" method="post">
    <input type="submit" value="< Edit sketch">
</form>
</td><td>
<form action="run.php" method="post">
    <p><b>Input</b> (<a href=expect.html>syntax help</a>):<br/><textarea id="expect" name="input" cols="80" rows="10"><?php echo($input);?></textarea></p>
    <p>Baudrate: <input type="text" name="baudrate" value="<?php echo($baudrate);?>"> bps</p>
    <p>Timeout: <input type="text" name="timeout" value="<?php echo($timeout);?>"> s</p>
    <input type="submit" value="Re-run">
</form>
</td></tr></table>
<script>
var te = document.getElementById("expect");
CodeMirror.fromTextArea(te, {lineNumbers: true, matchBrackets: true, mode:  "text/x-expect"});
</script>
</body></html>