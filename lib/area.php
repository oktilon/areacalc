<?php
require_once 'vendor/autoload.php';

$fname = '../kolod5.dat';

$fp = fopen($fname, 'r');
$pts = [];
while(($line = fgets($fp)) !== FALSE) {
    $pts[] = trim($line);
}
fclose($fp);

$loop = implode(',', $pts);
$geophp = new geoPHP;
$mapper = new Spinen\Geometry\Support\TypeMapper;

$t = new Spinen\Geometry\Geometry($geophp, $mapper);
$geom = $t->parseWkt("SRID=4326;POLYGON(({$loop}))");

// $geom = geoPHP::load("SRID=4326;POLYGON(({$loop}))", 'wkt');
$area = $geom->getSquareMeters();
echo $area . PHP_EOL;