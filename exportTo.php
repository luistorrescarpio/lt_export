<?php 
/*
	Nombre: lt_export
	Versión: 1.0
	Autor del Script: Luis Torres Carpio
	Correo: luis.torres.carpio1@gmail.com
	Descripción: 
		Script para facilitar exportaciones en diferentes formatos desde PHP de forma simple y eficaz
		Pensado para el desarrollo agil.

	Funcional [Word, Excel and PDF]

	Link de explicación parte 1: https://web.facebook.com/developer.ltc/videos/1579042302226427/
*/
function exportTo($type, $content){
	$dt = (object)$content;
	$fileName = $dt->fileName;
	$data = $dt->data;
	$header = isset($dt->header)?$dt->header:false;
	$thead = isset($dt->thead)?$dt->thead:false;
	$footer = isset($dt->footer)?$dt->footer:false;
	$conf = isset($dt->config)?(object)$dt->config:false;

	switch ($type) {
		case 'json': //Aun en fase Experimental
			// Obtenemos los 100 primeros registros desde la siguiente consulta
			// $r = query("SELECT * FROM personal ORDER BY nombres ASC LIMIT 100 ");
			// Nombre del archivo a generar
			$fileName = "exportJSON.json";
			// Convertirmos el objeto obtenido desde MYSQL a JSON
			$textJson = json_encode($r);   
			// Procedemos a crear el archivo e ingresar los datos en el
			$handle = fopen($fileName, "w");
		    fwrite($handle, $textJson);
		    fclose($handle);
		    // Establecemos el tipo de archivo a generar
		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename='.$fileName);
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    readfile($fileName);

			break;
		case 'html':
			if(!$conf) $conf = (object)array();
			if(!isset($conf->sequence)) $conf->sequence = false;
		  	// Creamos una tabla con todos los datos obtenidos de MYSQL
			$rows = $data;

		  	$html = '
			  	<html lang="en">
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<title>Reporte en PDF</title>
					<style>
						.table_base,.table_base td,.table_base th {    
						    border: 1px solid #ddd;
						    text-align: left;
						}

						.table_base {
						    border-collapse: collapse;
						    width: 100%;
						}

						.table_base th,.table_base td {
						    padding: 10px 5px;
						}
					</style>
				</head>
				<body>';
			if($header){
		    	$html.= '<table style="margin:auto;width:100%;" border="0">';
		    	$html.= '<tr><td>'.$header.'</td></tr>';
		    	$html.= '</table>';
		    }
		    if(is_array($rows)){
				$html.= '<table class="table_base" style="margin:auto;width:100%;">';
				if($thead){
					if($thead!= 1){
						if($thead == "col_name"){
							$thead = array();
							$row_aux = $rows[0];
							if($conf->sequence)
			    				array_unshift($thead, "N°");
							foreach ($row_aux as $i => $row) {
								array_push($thead, $i);
							}
						}else{
							if($conf->sequence)
			    				array_unshift($thead, "N°");
						}
						array_unshift($rows, $thead);
					}
				}
				$cc = 0;
				foreach ($rows as $i => $row) {
					if($i==0 && $thead){
						$html.='<thead><tr>';
						foreach ($row as $j => $col) {
				    		$html.='<th>'.$col.'</th>';
						}
						$html.='</tr></thead>';
						continue;
					}
					$cc++;
			    	$html.= '<tr>';
			    	if($conf->sequence)
			    		$html.='<td>'.$cc.'</td>';

			    	foreach ($row as $j => $col) {
			    		$html.='<td>'.$col.'</td>';
					}
					$html.= '</tr>';
			    }
						
				$html.='</table>';
			}else if(is_string($rows)){
				$html.=$rows;
			}else{
				$html.='Body no reconocible';
			}

			if($footer){
		    	$html.= '<table style="margin:auto;width:100%;" border="0">';
		    	$html.= '<tr><td>'.$footer.'</td></tr>';
		    	$html.= '</table>';
		    }

			$html.='</body>
				</html>
			</body>';
			echo $html;

		  	break;
		
		case 'pdf':
			//Default
			if(!$conf) $conf = (object)array();
			if(!isset($conf->set_paper)) $conf->set_paper = "portrait";
			if(!isset($conf->sequence)) $conf->sequence = false;

			// Add Libreria Dompdf 
			require_once("lib/dompdf/dompdf_config.inc.php");
		  	// Creamos una tabla con todos los datos obtenidos de MYSQL
			$rows = $data;
		  	$html = '
			  	<html lang="en">
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<title>Reporte en PDF</title>
					<style>
						.table_base,.table_base td,.table_base th {    
						    border: 1px solid #ddd;
						    text-align: left;
						}

						.table_base {
						    border-collapse: collapse;
						    width: 100%;
						}

						.table_base th,.table_base td {
						    padding: 10px 5px;
						}
					</style>
				</head>
				<body>';
			if($header){
		    	$html.= '<table style="margin:auto;width:100%;" border="0">';
		    	$html.= '<tr><td>'.$header.'</td></tr>';
		    	$html.= '</table>';
		    }
		    if(is_array($rows)){
				$html.= '<table class="table_base" style="margin:auto;width:100%;">';
				if($thead){
					if($thead!= 1){
						if($thead == "col_name"){
							$thead = array();
							$row_aux = $rows[0];
							if($conf->sequence)
			    				array_unshift($thead, "N°");

							foreach ($row_aux as $i => $row) {
								array_push($thead, $i);
							}
						}else{
							if($conf->sequence)
			    				array_unshift($thead, "N°");
						}
						array_unshift($rows, $thead);
					}
				}
				$cc = 0;
				foreach ($rows as $i => $row) {
					if($i==0 && $thead){
						$html.='<thead><tr>';
						foreach ($row as $j => $col) {
				    		$html.='<th>'.$col.'</th>';
						}
						$html.='</tr></thead>';
						continue;
					}
					$cc++;
			    	$html.= '<tr>';
			    	if($conf->sequence)
			    		$html.='<td>'.$cc.'</td>';

			    	foreach ($row as $j => $col) {
			    		$html.='<td>'.$col.'</td>';
					}
					$html.= '</tr>';
			    }
						
				$html.='</table>';
			}else if(is_string($rows)){
				$html.=$rows;
			}else{
				$html.='Body no reconocible';
			}

			if($footer){
		    	$html.= '<table style="margin:auto;width:100%;" border="0">';
		    	$html.= '<tr><td>'.$footer.'</td></tr>';
		    	$html.= '</table>';
		    }

			$html.='</body>
				</html>
			</body>';
		    $dhg=utf8_encode(utf8_decode($html));
		    
			$dompdf=new DOMPDF();
			$dompdf->set_paper('A4', $conf->set_paper); //Hoja en Vertical

			$dompdf->load_html($html);

			ini_set("memory_limit","128M");
			$dompdf->render();
			header('Content-Type: application/pdf; charset=utf-8');
			$dompdf->stream($fileName.".pdf",array('Attachment'=>0));

		  	break;
		case 'word':
			// Establecemos un nombre al archivo
			$fileName = $fileName.".doc";
			// estructuramos los datos en una tabla
			$html = '<table style="margin:auto;" border="1">';
			$rows = $data;
		    if($header){
		    	$col_length = count($rows[0]);
		    	$html.= '<tr><td colspan="'.$col_length.'">'.$header.'</td></tr>';
		    }
			foreach ($rows as $key => $row) {
		    	$html.= '<tr>';
		    	foreach ($row as $j => $col) {
		    		$html.='<td>'.$col.'</td>';
				}
				$html.= '</tr>';
		    }

			$html.='</table>';
			// Establecemos el tipo de archivo a exportar
			header("Content-type: application/vnd.ms-word");
			header("Content-Disposition: attachment;Filename=".$fileName);
			// Imprimimos los datos
			echo "\xEF\xBB\xBF";
			echo $html;

	    	break;
		case 'excel':
			$rows = $data;

			$fileName = $fileName.".xls";
			header("Content-Type: application/vnd.ms-excel; charset=utf-16");
    		header("Content-Disposition: attachment; filename=\"$fileName\"");

			$html = '
			<style>
				body {
				    font-size: 0.95em;
				    font-family: arial;
				    color: #212121;
				    background-color: #212121;
				}
			</style>';
			
			if(is_array($rows)){
				$html.= '<table style="margin:auto;" border="1">';
				if($thead){
					if($thead!= 1){
						if($thead == "col_name"){
							$thead = array();
							$row_aux = $rows[0];
							if($conf->sequence)
			    				array_unshift($thead, "N°");

							foreach ($row_aux as $i => $row) {
								array_push($thead, $i);
							}
						}else{
							if($conf->sequence)
			    				array_unshift($thead, "N°");
						}
						array_unshift($rows, $thead);
					}
				}
				if($header){
			    	// $html.= '<tr><td>'.$header.'</td></tr>';
			    	$html.= $header;
			    }

				$cc = 0;
				foreach ($rows as $i => $row) {
					if($i==0 && $thead){
						$html.='<thead><tr>';
						foreach ($row as $j => $col) {
				    		$html.='<th>'.$col.'</th>';
						}
						$html.='</tr></thead>';
						continue;
					}
					$cc++;
			    	$html.= '<tr>';
			    	if($conf->sequence)
			    		$html.='<td>'.$cc.'</td>';

			    	foreach ($row as $j => $col) {
			    		$html.='<td>'.$col.'</td>';
					}
					$html.= '</tr>';
			    }
						
				$html.='</table>';
			}else if(is_string($rows)){
				$html.=$rows;
			}else{
				$html.='Body no reconocible';
			}
			echo "\xEF\xBB\xBF";
			echo $html;
		  	break;
		case 'csv': ////Aun en fase Experimental

		  	//Obtenemos los 100 primeros registros por medio de la siguiente consulta
		    // $rows = query("SELECT * FROM personal ORDER BY nombres ASC LIMIT 100");
			//Establecemos un rombre al documento que vamos a generar
			$filename = $fileName.".csv";
			//Establecemos el tipo de archivo que vamos a exportar
		    header('Content-Type: text/csv');
		    header('Content-Disposition: attachment; filename="' . $filename . '";');
		    
			//create a file pointer
		    $fl = fopen('php://memory', 'w');
		    //Deliminar por comas
			$delimiter = ",";
			//Establecemos la cabecera del documento
		    // $rowArr = array('ID', 'NOMBRES', 'FRECUENCIA', 'SERIE');
		    $rows = $data;
		    // $rowArr = $rows[0];
		    //Añadimos al archivo la cabecera
		    // fputcsv($fl, $rowArr, $delimiter);
		    //Recorremos por todos los registros
		    foreach ($rows as $i => $row) {
		    	// if($i==0) continue;
		    	//Adjuntamos en una variable el registro array
		    	$rowArr = array();
		    	foreach ($row as $j => $col) {
		    		array_push($rowArr, $col);
		    	}
		    	// $rowArr = array( $row[0], $row[1], $row[2] );
		    	//Añadimos al arhivo csv los registros en formato array
		        fputcsv($fl, $rowArr, $delimiter);
		    }
			//Volver al principio del archivo
		    fseek($fl, 0);

		    //generar todos los datos restantes en un archivo
		    fpassthru($fl);
	    
	    	break; 

	}
}
 ?>