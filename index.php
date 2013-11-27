<?php
require_once 'core/load.php';
require_once \um\Load::helper('debug');
require_once \um\Load::package('antispam', 'SpamFilter');
require_once \um\Load::package('antispam', 'HoneyPotField');
require_once \um\Load::package('antispam', 'MultiOptionHoneyPotField');

if(!empty($_POST))
{
	/* @var $sf \um\SpamFilter */$sf = \um\SpamFilter::retrieve();
	if($sf->test())
	{
		echo 'Woohoo!';
	}
	else
	{
		die('inafire');
	}
}
else
{
	\um\MultiOptionHoneyPotField::setOptionsArray('I do not', 'Very', 'Mostly', 'Undecided', 'Other', 'Yes', 'No', 'Monday', '5', 'Clinical');
	$sf = new \um\SpamFilter(array('honey_pots'=>12, 'verifiers'=>12, 'field_names'=>array('email', 'phone', 'question', 'commentary', 'profession', 'title', 'MI', 'firstname', 'lastname', 'murphy', 'middle', 'fullname', 'username', 'month', 'year', 'day')));
	$sf->store();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
		<form action="" method="post">
		<?php
		echo $sf->output();
		?>
			<input type="submit" name="hercules" value="Safe to click here" />
		</form>
    </body>
</html>
<?php
}
?>