<?php
require_once 'antispam/Spamalizer.php';
?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
		<?php
		$spamalizer = new \um\Spamalizer();
		$spamalizer->spamalize();

?>
		<form action="index.php" method="post">
		<input type="text" name="legitvalue" value="legit"/>
		<input type="submit" />
		</form>
    </body>
</html>
