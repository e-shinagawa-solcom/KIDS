<?
// $price = 78.0000;
// echo floor($price) . "<br>";
// $price = 78.3201;
// echo number_format(floor($price * 100) / 100, 2, '.', ',') . "<br>";
// $price = 78.32001;
// echo number_format(floor($price * 100) / 100, 2, '.', ',') . "<br>";
// $price = 78.3200;
// echo number_format(floor($price * 10000) / 10000, 4, '.', ',') . "<br>";
// $price = 78.3201;
// echo number_format(floor($price * 10000) / 10000, 4, '.', ',') . "<br>";
// $price = 78.32001;
// echo number_format(floor($price * 10000) / 10000, 4, '.', ',') . "<br>";
// $price = 8.3200;
// echo number_format(floor($price * 100) / 100, 2, '.', ',') . "<br>";

$price = 78.3200;
echo number_format(floor_plus($price, 0), 0, '.', ',') . "<br>";
echo number_format(floor_plus($price, 1), 1, '.', ',') . "<br>";
echo number_format(floor_plus($price, 2), 2, '.', ',') . "<br>";
echo number_format(floor_plus($price, 3), 3, '.', ',') . "<br>";
echo number_format(floor_plus($price, 4), 4, '.', ',') . "<br>";

$price = 0.0000;
echo number_format(floor_plus($price, 0), 0, '.', ',') . "<br>";
echo number_format(floor_plus($price, 1), 1, '.', ',') . "<br>";
echo number_format(floor_plus($price, 2), 2, '.', ',') . "<br>";
echo number_format(floor_plus($price, 3), 3, '.', ',') . "<br>";
echo number_format(floor_plus($price, 4), 4, '.', ',') . "<br>";
$price = 78.5600;
echo number_format(floor_plus($price, 0), 0, '.', ',') . "<br>";
echo number_format(floor_plus($price, 1), 1, '.', ',') . "<br>";
echo number_format(floor_plus($price, 2), 2, '.', ',') . "<br>";
echo number_format(floor_plus($price, 3), 3, '.', ',') . "<br>";
echo number_format(floor_plus($price, 4), 4, '.', ',') . "<br>";
$price = 78.5655;
echo number_format(floor_plus($price, 0), 0, '.', ',') . "<br>";
echo number_format(floor_plus($price, 1), 1, '.', ',') . "<br>";
echo number_format(floor_plus($price, 2), 2, '.', ',') . "<br>";
echo number_format(floor_plus($price, 3), 3, '.', ',') . "<br>";
echo number_format(floor_plus($price, 4), 4, '.', ',') . "<br>";

function floor_plus($value, $precision = 1)
{
	$value += 0.00001;
    return round($value - 0.5 * pow(0.1, $precision), $precision, PHP_ROUND_HALF_UP);
}
