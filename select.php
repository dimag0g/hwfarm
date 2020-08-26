<!-- HW Farm project (c) Dmitry Grigoryev, 2020 -->
<!-- Released under the terms of GNU AGPLv3 -->
<html><head><title>HW Farm</title><meta charset="UTF-8">
</head><body>
<h3>HW Farm - Select</h3>
<?php
session_id(uniqid());
session_start();

$sid = session_id();
echo("<pre><b>session ID</b>: $sid </pre>");

$hwlist = array_map('str_getcsv', file("hwlist.csv"));

?>

<hr><table><tr><td>
<p><b>Board configs</b>:</p>
<form action="sketch.php" method="post">
    <select name='board' onchange='this.form.submit()'>
    <option value=''>Select...</option>
    <?php
    foreach($hwlist as $data) {
        echo("<option value='$data[0]'> $data[0] </option>");
    }
    ?>
    </select>
    <noscript><input type="submit" value="Submit"></noscript>
</form>
</td></tr></table>
<hr><a href="https://github.com/dimag0g/hwfarm">Project page</a>
- <a href="terms.txt">Usage terms</a>
</body></html>