<?php
global $print;
global $stats;
global $all_services_dief;

$all_services_dief = array();
$stats = array(
		'counter' 	=> 0,
		'tmima' 	=> 0,	
		'dief' 		=> 0,
		'gendief' 	=> 0,
		'stubs'		=> 0,
		'noncat'	=> 0,
		'notices'	=> array(),
	);

include('../in/config.php');
include('../in/modules/helpers.php');
include('functions.php');

$db = new PDOTester('mysql:host='. DB_HOST .';dbname=pdm_dev;charset=utf8', DB_USER, DB_PASS);

get_gen_dief_from_wiki();

echo 'All Services: '.$stats['counter'].'<br />';
echo 'Non Tmima '.$stats['noncat'].'<br />';
echo 'Gen. Dief. '.$stats['gendief'].'<br />';
echo 'Dief. '.$stats['dief'].'<br />';
echo 'Tmimata '.$stats['tmima'].'<br />';

echo '<hr />';
foreach( $stats['notices'] as $notice) echo  $notice.'<br />';

echo '<hr />'.$print;

?>