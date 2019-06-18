<?php 
include("exportTo.php");
include("heasy_mysql.php");

$obj = $_REQUEST;
switch ($obj['action']) {
	case 'pdf':
		$rows = query("SELECT nombres, apellidos, celular FROM personal");

		exportTo("pdf",[
			"thead"=>["Nombre","Apellidos","Celular"]
			,"header"=>'
				<div align="center">
					<table width="100%" border="0">
						<tr>
							<td width="20%" align="center">
								<img src="img/Koala.jpg" width="120">
							</td>
							<td align="center" width="60%">
								<span style="font-size:30px;">Relación de Trabajadores</span>
							</td>
							<td width="20%">
								
							</td>
						</tr>
					</table>
				</div>
			'
			,"data"=>$rows
			,"footer"=>'
				<div align="center">
					<h3>Pie de Pagina</h3>
					<p>Mas detalles aqui...</p>
				</div>
			'
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