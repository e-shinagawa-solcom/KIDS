<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=EUC-JP">
</head>
<?
// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryQuery = split ( ";", preg_replace ( "/;.+?$/", "", $_POST["strQuery"] ) );
$lngQueryNumber = count ( $aryQuery );

echo "<p>Query Number:" . $lngQueryNumber . "</b>";

for ( $lngQueryCount = 0; $lngQueryCount < $lngQueryNumber; $lngQueryCount++ )
{
	echo "<p>No." . ( $lngQueryCount + 1 ) . " Query:" . preg_replace ( "/\n/", "<br>\n", $aryQuery[$lngQueryCount] ) . "</p>\n";

	$aryQuery[$lngQueryCount] = preg_replace ( "/\n/m", "", $aryQuery[$lngQueryCount] );

	if ( $aryQuery[$lngQueryCount] != "" )
	{
		$lngResultID = $objDB->execute( $aryQuery[$lngQueryCount] );
		$lngResultNum = pg_num_rows ( $lngResultID );
	}

	echo "<p>Resukt Num:" . $lngResultNum . "</p>\n";

	if ( $lngResultNum > 0 )
	{
		echo "<table border>";

		$lngFieldNum = $objDB->getFieldsCount( $lngResultID );
		$aryColumns = $objDB->fncColumnsArray( $lngResultID, $lngFieldNum );

		echo "<tr bgcolor=#99CCFF>";
		echo "<th>No</th>";

		for ( $i = 0; $i < $lngFieldNum; $i++ )
		{
			echo "<th>" . $aryColumns[$i] . "</th>";
		}
		echo "</tr>";

		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			echo "<tr>";

			echo "<th>$i</th>";

			$aryResult = $objDB->fetchArray( $lngResultID, $i );
			for ( $j = 0; $j < $lngFieldNum; $j++ )
			{
				echo "<td>" . $aryResult[$j] . "</td>";
			}

			echo "</tr>";
		}

		echo "</table>";
	}

	unset ( $lngResultID );
	unset ( $lngResultNum );
	unset ( $lngFieldNum );
	unset ( $aryColumns );
}

$objDB->close();
?>
<form action="test.php" method="POST">
<textarea name="strQuery" cols="100" rows="10">
<? echo $_POST["strQuery"]; ?>
</textarea>
<input type="submit">
</form>
</html>
