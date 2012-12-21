<?php
function dump($var, $echo=true,$label=null, $strict=true)
{
    $label = ($label===null) ? '' : rtrim($label) . ' ';
    if(!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>'.$label.htmlspecialchars($output,ENT_QUOTES).'</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    }else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if(!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>'. $label. htmlspecialchars($output, ENT_QUOTES). '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}

require 'ShapeFile.inc.php';

$options = array('noparts' => false);
$shp = new ShapeFile('map/bou2_4p.shp', $options); // along this file the class will use file.shx and file.dbf
file_put_contents("china.json", '');

$result = array(
	'type' => 'FeatureCollection',
	'features' => array(),
	'bbox' => array(114.32, 30.52, 114.34, 30.54)
);

$i=0;
while ($record = $shp->getNext()) {
	$record = $shp->getNext();
	
	// read meta data
	if(empty($record)) continue;
	$dbf_data = $record->getDbfData();

	$data = array(
		//trim($dbf_data['ISO_3_CODE']), 
		//trim($dbf_data['ISO_2_CODE']),
		trim($dbf_data['NAME'])
	);
	
	// read shape data
	$shp_data = $record->getShpData();

	// store number of parts
	//$data[] = $shp_data['numparts'];
	$result['features'][] = array(
		'geometry' => array(
			'type' => 'Polygon',
			'coordinates' => array()
		),
		'type' => 'Feature',
		'name' => trim($dbf_data['NAME'])
	);

	foreach ($shp_data['parts'] as $part) {

		$coords = array();
		foreach ($part['points'] as $point) {
			//$result['features'][$i]['geometry']['coordinates'] = array($point['x'], $point['y']);
			$coords[] = array($point['x'], $point['y']);
		}
		
		$result['features'][$i]['geometry']['coordinates'] = $coords;
		// dump($result['features'][$i]);
		file_put_contents("china.json", json_encode($result['features'][$i]).'', FILE_APPEND);
	}
	$i++;
}
//dump($result);
//$content = json_encode($result);
// file_put_contents("china.json", $content, FILE_APPEND);


// file_put_contents("china.json", '12', FILE_APPEND);
// file_put_contents("china.json", '34', FILE_APPEND);
// file_put_contents("china.json", '56', FILE_APPEND);
// file_put_contents("china.json", '78', FILE_APPEND);
?>

