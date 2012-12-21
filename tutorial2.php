<?php
ini_set("memory_limit","64M");
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
$shp = new ShapeFile("map/bou2_4p.shp", $options); // along this file the class will use file.shx and file.dbf

$result = array(
	'type' => 'FeatureCollection',
	'features' => array()
);

$xminArr = array();
$xmaxArr = array();
$yminArr = array();
$ymaxArr = array();

while ($record = $shp->getNext()) {
	$record = $shp->getNext();
	
	// read meta data
	if(empty($record)) continue;
	$dbf_data = $record->getDbfData();
	$data = array(
		'type' => 'Feature',
		'properties' => array('name' => iconv('gb2312','utf-8', trim($dbf_data['NAME'])))
	);

	// read shape data
	$shp_data = $record->getShpData();

	array_push($xminArr, $shp_data["xmin"]);
	array_push($xmaxArr, $shp_data["xmax"]);
	array_push($yminArr, $shp_data["ymin"]);
	array_push($ymaxArr, $shp_data["ymax"]);

	foreach ($shp_data['parts'] as $part) {
		$coords = array();
		foreach ($part['points'] as $point) {
			$coords[] = array($point['x'], $point['y']);
		}
		$data['geometry']['coordinates'] = array($coords);
	}
	$data['geometry']['type'] = 'Polygon';
	$result['features'][] = $data;
}

$bbox = array(
	min($xminArr), min($yminArr), max($xmaxArr), max($ymaxArr)
);

$result['bbox'] = $bbox;

$content = json_encode($result);
file_put_contents("china.json", $content);
dump($result);
?>

