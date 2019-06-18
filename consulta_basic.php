<?php 
include("exportTo.php");

$obj = $_REQUEST;
switch ($obj['action']) {
	case 'pdf':
		exportTo("html",[
			"thead"=>["Nombre","Celular"]
			,"data"=>array([
				"camp2"=>"luis"
				,"camp3"=>78986454
			],[
				"camp2"=>"Jose"
				,"camp3"=>78986454
			])
			,"fileName"=>"ejemplo"
			,"config"=>[
				"sequence"=>true
			]
		]);
		break;
	
	default:
		# code...
		break;
}
?>