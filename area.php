<?php

const PI = '3.14159265358979323846';

function bcabs($x) {
    if(bccomp($x, '0') < 0) {
        return bcsub('0', $x);
    }
    return $x;
}

function bcdeg2rad($deg) {
    return bcmul($deg, bcdiv(PI, '180'));
}

function bcfact($n) {
    $r = $n--;
    while($n>1) $r=bcmul($r, $n--);
    return $r;
}

function bcsin($a) {
    $or= $a;
    $r = bcsub($a,bcdiv(bcpow($a,3),6));
    $i = 2;
    while(bccomp($or,$r)) {
        $or=$r;
        $ix = $i * 2 + 1;
        switch($i%2) {
          case 0:  $r = bcadd($r,bcdiv(bcpow($a,$ix),bcfact($ix))); break;
          default: $r = bcsub($r,bcdiv(bcpow($a,$ix),bcfact($ix))); break;
        }
        $i++;
    }
    return $r;
}

function bccos($a) {
    $or= $a;
    $r = bcsub(1,bcdiv(bcpow($a,2),2));
    $i = 2;
    while(bccomp($or,$r)) {
        $or=$r;
        $ix = $i * 2;
        switch($i%2) {
          case 0:  $r = bcadd($r,bcdiv(bcpow($a,$ix),bcfact($ix))); break;
          default: $r = bcsub($r,bcdiv(bcpow($a,$ix),bcfact($ix))); break;
        }
        $i++;
    }
    return $r;
}

function bcatan($x) {
    $ret = 0;
    for($n = 0; $n < 50; $n++) {
        // SUM(n=0;inf) (((-1)^n)/(2n+1))*x^(2n+1)
        $n2 = 2 * $n + 1;
        $num = bcmul(bcpow('-1', $n), bcpow($x, $n2));
        $ret = bcadd($ret, bcdiv($num, $n2));
    }
    return $ret;
}

function bcatan2($y, $x) {
    $add = bccomp($x, 0) < 0;
    $ret = bcatan(bcdiv($y, $x));
    if($add) {
        $ret = bcadd($ret, PI);
    }
    return $ret;
}

// степенной ряд
function powSeries($x, $p1, $p2, $p3) {
    return bcmul(bcadd($p1, bcmul(bcadd($p2, bcmul($p3, $x)), $x)), $x);
    // return ($p1 + ($p2 + ($p3 * $x)) * $x) * $x;
}

// тригонометрический ряд
function trigSeries($x, $t2, $t4, $t6) {
    return bcadd(
        bcadd(
            bcadd($x, bcmul($t2, bcsin(bcmul('2.0', $x)))),
            bcmul($t4, bcsin(bcmul('4.0', $x)))
        ),
        bcmul($t6, bcsin(bcmul('6.0', $x)))
    );
}

// инициализация эквивалентной сферы
function init($a, $f) {
    $b  = bcmul($a, bcsub('1.0', $f));
    $e2 = bcmul($f, bcsub('2.0', $f));
    $R_auth = $b * bcsqrt(bcadd('1.0', powSeries($e2, bcdiv('2.0','3.0'), bcdiv('3.0','5.0'), bcdiv('4.0','7.0'))));
    $to_auth_2 = powSeries($e2, bcdiv('-1.0','3.0'), bcdiv('-31.0','180.0'), bcdiv('-59.0','560.0'));
    $to_auth_4 = powSeries($e2, '0.0', bcdiv('17.0','360.0'), bcdiv('61.0','1260.0'));
    $to_auth_6 = powSeries($e2, '0.0', '0.0', bcdiv('-383.0','45360.0'));
    return [$R_auth, $to_auth_2, $to_auth_4, $to_auth_6];
}

function spherToCart($lat, $lon) {
    $x = bcmul(bccos($lat), bccos($lon));
    $y = bcmul(bccos($lat), bcsin($lon));
    $z = bcsin($lat);
    return [$x, $y, $z];
}

function cartToSpher($x, $y, $z) {
    $lat = bcatan2($z, bcsqrt(bcadd(bcmul($x, $x), bcmul($y, $y))));
    $lon = bcatan2($y, $x);
    return [$lat, $lon];
}

function rotate($x, $y, $a) {
    $c = bccos($a);
    $s = bcsin($a);
    $u = bcadd(bcmul($x, $c), bcmul($y, $s));
    $v = bcadd(bcmul(bcsub(0, $x), $s), bcmul($y,$c));
    return [$u, $v];
}

function inverse($lat1, $lon1, $lat2, $lon2) {
    list($x, $y, $z) = spherToCart($lat2, $lon2);
    list($x, $y) = rotate($x, $y, $lon1);
    list($z, $x) = rotate($z, $x, bcsub(bcdiv(PI, '2.0'), $lat1));
    list($lat, $lon) = cartToSpher($x, $y, $z);
    $dist = bcsub(bcdiv(PI, '2.0'), $lat);
    $azi = bcsub(PI, $lon);
    return [$dist, $azi];
}


list($script, $fname) = $argv;

bcscale(16);

echo '  atan2( 0.1,  0.3) = ' .   atan2( 0.1,  0.3) . PHP_EOL;
echo 'bcatan2( 0.1,  0.3) = ' . bcatan2( 0.1,  0.3) . PHP_EOL;
echo '  atan2(-0.1,  0.3) = ' .   atan2(-0.1,  0.3) . PHP_EOL;
echo 'bcatan2(-0.1,  0.3) = ' . bcatan2(-0.1,  0.3) . PHP_EOL;
echo '  atan2( 0.1, -0.3) = ' .   atan2( 0.1, -0.3) . PHP_EOL;
echo 'bcatan2( 0.1, -0.3) = ' . bcatan2( 0.1, -0.3) . PHP_EOL;
echo '  atan2(-0.1, -0.3) = ' .   atan2(-0.1, -0.3) . PHP_EOL;
echo 'bcatan2(-0.1, -0.3) = ' . bcatan2(-0.1, -0.3) . PHP_EOL;
die();

$a = '6378137';  //# большая полуось
$f = bcdiv('1', '298.257223563'); //# сжатие

//# инициализировать эквивалентную сферу
list($r_auth, $to_auth_2, $to_auth_4, $to_auth_6) = init($a, $f);
printf("SPH R=%s, a2=%s, a4=%s, a6=%s\n", $r_auth, $to_auth_2, $to_auth_4, $to_auth_6);

$fp = fopen($fname, 'r');
$tau = 0.;
$i = 1;
$azi0 = '-';
$azi1 = '-';
$azi2 = '-';
while(($line = fgets($fp)) !== FALSE) {
    $arr = explode(' ', $line);
    $alon = trim($arr[0]);
    $alat = trim($arr[1]);
    $lon = bcdeg2rad($alon);
    $lat = bcdeg2rad($alat);
    $lonx = $lon;
    $latx = $lat;

    # вычислить эквивалентную широту
    $lat = trigSeries($lat, $to_auth_2, $to_auth_4, $to_auth_6);
    if($i > 1) {
        # вычислить прямой азимут Qi - Qi+1
        list($dist, $azi1) = inverse($lat1, $lon1, $lat, $lon);
        if($i == 2) {
            # запомнить азимут Q1 - Q2
            $azi0 = $azi1;
        } else {
            # вычислить поворот в i-й вершине
            $tau_i = bcsub('0.5', bcdiv(bcdiv(bcsub($azi2, $azi1), '2.0'), PI));
            # нормализовать величину поворота
            $dt = bcadd($tau_i, '0.5');
            $dt = explode('.', $dt);
            $tau_i = bcsub($tau_i, $dt[0]);
            # добавить поворот к сумме поворотов
            $tau = bcadd($tau, $tau_i);
        }
        # вычислить обратный азимут Qi+1 - Qi
        list($dist, $azi2) = inverse($lat, $lon, $lat1, $lon1);
    }
    $lon1 = $lon;
    $lat1 = $lat;
    printf("%03d=%s %s, r=%s %s, l=%s, t=%s, a0=%s, a1=%s, a2=%s\n",
            $i, $alon, $alat, $lonx, $latx, $lat, $tau, $azi0, $azi1, $azi2);
    $i++;
}
fclose($fp);

# вычислить поворот в 1-й вершине
$tau_i = bcsub('0.5', bcdiv(bcdiv(bcsub($azi2, $azi0), '2.0'), PI));
# нормализовать величину поворота
$tau_i = bcsub($tau_i, floor(bcadd($tau_i, '0.5')));
# добавить поворот к сумме поворотов
$tau = bcadd($tau, $tau_i);

# вычислить площадь
$pi2 = bcmul('2', PI);
// $area = 2. * $pi * (1. - abs($tau)) * $r_auth ** 2;
$area = bcmul(bcmul($pi2, bcsub('1', bcabs($tau))), bcpow($r_auth, 2));
$ha = bcsub($area, 10000);
printf("area = %s\n", $ha);