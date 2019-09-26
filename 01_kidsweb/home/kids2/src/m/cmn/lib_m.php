<?
/**
 *	マスタ管理用ライブラリ
 *
 *	マスタ管理用関数ライブラリ
 *	注) 仕入科目(m_StockSubject)、仕入部品(m_StockItem)マスタは特殊処理有り
 *
 *	@package   KIDS
 *	@license   http://www.wiseknot.co.jp/
 *	@copyright Copyright &copy; 2003, Wiseknot
 *	@author    Kenji Chiba <k-chiba@wiseknot.co.jp>
 *	@access    public
 *	@version   1.00
 *
 */

//////////////////////////////////////////////////////////////////////////////
// 設定
//////////////////////////////////////////////////////////////////////////////
// 処理ID定義
define ( "DEF_ACTION_INSERT", 1 ); // 追加
define ( "DEF_ACTION_UPDATE", 2 ); // 変更
define ( "DEF_ACTION_DELETE", 3 ); // 削除


// 一覧マスターテーブル定義
$aryListTableName = Array (
    "m_StockClass"       => "仕入区分マスタ管理",
    "m_StockSubject"     => "仕入科目マスタ管理",
    "m_StockItem"        => "仕入部品マスタ管理",
    "m_AccessIPAddress"  => "アクセスIPアドレスマスタ管理",
    "m_CertificateClass" => "証紙種類マスタ管理",
    "m_Country"          => "国マスタ管理",
    "m_Copyright"        => "版権元マスタ管理",
    "m_Organization"     => "組織マスタ管理",
    "m_ProductForm"      => "商品形態マスタ管理",
    "m_SalesClass"       => "売上区分マスタ管理",
    "m_TargetAge"        => "対象年齢マスタ管理",
    "m_DeliveryMethod"   => "運搬方法マスタ管理"
);


// 検索マスターテーブル定義
$arySearchTableName = Array (
    "m_Company"          => "会社マスタ管理",
    "m_Group"            => "グループマスタ管理",
    "m_MonetaryRate"     => "通貨レートマスタ管理"
);


/**
 *	マスターテーブルクラス
 *
 *	setMasterTable     テーブル情報の取得、設定
 *	setAryMasterInfo   各マスタのカラムチェック法、追加・削除チェッククエリ、プルダウンカラムの設定
 *	getColumnHtmlTable カラムのHTMLを取得(<td>~</td>・・・)
 *
 *	@package k.i.d.s.
 *	@license http://www.wiseknot.co.jp/
 *	@copyright Copyright &copy; 2003, Wiseknot
 *	@author Kenji Chiba <k-chiba@wiseknot.co.jp>
 *	@access public
 *	@version 0.1
 */
class clsMaster
{
    /**
     *	テーブル名
     *	@var string
     */
    var $strTableName;

    /**
     *	行データ $aryData[行番号][カラム名]
     *	@var array
     */
    var $aryData;

    /**
     *	行数
     *	@var integer
     */
    var $lngRecordRow;

    /**
     *	カラム名 $aryColumnName[フィールド番号]
     *	@var array
     */
    var $aryColumnName;

    /**
     *	カラムの型配列 $aryType[フィールド番号]
     *	@var array
     */
    var $aryType;

    /**
     *	カラムのチェック配列 $aryCheck[カラム名]
     *	@var array
     */
    var $aryCheck;

    /**
     *	カラムのプルダウンメニュー配列 $aryMasterMenu[カラム名][行番号][(VALUE|TEXT)]
     *	@var array
     */
    var $aryMasterMenu;

    /**
     *	チェック処理クエリ $aryCheckQuery[(INSERT|UPDATE|DELETE)][クエリ番号]
     *	@var array
     */
    var $aryCheckQuery;

    /**
     *	処理テーブル $aryDeleteTable[連番]
     *	@var array
     */
    var $aryDeleteTable;

    /**
     *	クラス内の初期化を行う
     *
     *	@return void
     *	@access public
     */
    function __construct()
    {
        $this->strTableName   = "";
        $this->lngRecordRow   = 0;
        $this->aryColumnName  = Array();
        $this->aryType        = Array();
        $this->aryCheck       = Array();
        $this->aryMasterMenu  = Array();
        $this->aryCheckQuery  = Array();
        $this->aryDeleteTable = Array();
    }

    // -----------------------------------------------------------------
    /**
     *	マスタテーブルに関するクエリ生成し、情報を取得、設定
     *	@param	string  $strTableName  マスタテーブル名
     *	@param	string  $strKeyName    キーのカラム名
     *	@param	int     $lngKeyCode    キーの値
     *	@param	Array   $arySubCode    FORM VALUE
     *	@param	object  $objDB         DBオブジェクト
     *	@return	boolean 失敗、成功
     */
    // -----------------------------------------------------------------
    function setMasterTable( $strTableName, $strKeyName, $lngKeyCode, $arySubCode, $objDB )
    {
        // マスタテーブル名のセット
        $this->strTableName = $strTableName;

        // クエリ生成開始
        $strQuery = "SELECT * FROM " . $this->strTableName;

        // コード指定(修正、削除処理の場合)
        if ( $strKeyName && $lngKeyCode != "" )
        {
            $strQuery .= " WHERE $strKeyName = $lngKeyCode";

            // 仕入部品マスタの場合、条件を追加
            if ( $this->strTableName == "m_StockItem" )
            {
                $strQuery .= " AND lngStockSubjectCode = " . $arySubCode["lngstocksubjectcode"];
            }

            // 通貨レートマスタの場合、条件を追加
			elseif ( $this->strTableName == "m_MonetaryRate" )
            {
                $strQuery .= " AND lngMonetaryUnitCode = " . $arySubCode["lngmonetaryunitcode"] .
                    " AND dtmApplyStartDate = '" . $arySubCode["dtmapplystartdate"] . "'" .
                    " AND dtmApplyEndDate > now()";
            }
        }

        // 仕入部品マスタの場合、2カラム目でソートする特殊処理
        if ( $this->strTableName == "m_StockItem" )
        {
            $strQuery .= " ORDER BY 2, 1";
        }
        else
        {
            $strQuery .= " ORDER BY 1";
        }
        // echo $strQuery;
        // return;
        $this->setMasterTableData( $strQuery, $objDB );
        return TRUE;
    }



    // -----------------------------------------------------------------
    /**
     *	マスタテーブルに関する情報を取得、設定
     *	@param	string  $strQuery     クエリ
     *	@param	object  $objDB        DBオブジェクト
     *	@return	int     $lngResultNum 結果行数
     */
    // -----------------------------------------------------------------
    function setMasterTableData( $strQuery, $objDB )
    {
        // データの取得とセット
        list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

        // カラム関連データの取得とセット
        $lngColumnNum = $objDB->getFieldsCount ( $lngResultID );

        for ( $i = 0; $i < $lngColumnNum; $i++ )
        {
            // カラム名の読み込みとセット
            $this->aryColumnName[$i] = pg_field_name ( $lngResultID, $i);

            // 型の読み込みとセット
            $this->aryType[$i]       = pg_field_type ( $lngResultID, $i);
        }

        if ( $lngResultNum )
        {
            $this->aryData = pg_fetch_all ( $lngResultID );

            // インクリメントシリアルコード取得処理
            // レコード数から1を引いた数(最後の配列番号)を取得
            $lngRecordRow = ( count ( $this->aryData ) - 1 );

            for ( $i = 0; $i < $lngRecordRow; $i++ )
            {
                // 最終レコードの第1カラムが99以外かつ9999以下の場合、
                // その数値をインクリメントしたものを $this->lngRecordRow にセット
                if ( $this->aryData[$lngRecordRow - $i][$this->aryColumnName[0]] != 99 && $this->aryData[$lngRecordRow - $i][$this->aryColumnName[0]] < 9999 )
                {
                    $this->lngRecordRow = $this->aryData[$lngRecordRow - $i][$this->aryColumnName[0]] + 1;
                    break;
                }
            }
            $objDB->freeResult( $lngResultID );
        }

        return $lngResultNum;
    }



    // -----------------------------------------------------------------
    /**
     *	チェック配列の設定
     *	@param	int     キーコードの値
     *	@param	Array   サブキーコードの値(一部のテーブルにて必要)
     *	@return	boolean 失敗、成功
     */
    // -----------------------------------------------------------------
    function setAryMasterInfo( $lngKeyCodeValue, $lngSubCode )
    {
        $this->aryCheckQuery["INSERT"] = "SELECT " . $this->aryColumnName[0] . " FROM " . $this->strTableName . " WHERE " . $this->aryColumnName[0] . " = $lngKeyCodeValue";

        // 各マスタの設定
        switch ( $this->strTableName )
        {
            //////////////////////////////////////////////////////
            // 通貨レートマスタ管理
            case "m_MonetaryRate":

                // 登録チェッククエリ設定
                $this->aryCheckQuery["INSERT"] = "SELECT " . $this->aryColumnName[0] . " FROM " . $this->strTableName . " WHERE " . $this->aryColumnName[0] . " = $lngKeyCodeValue AND " . $this->aryColumnName[1] . " = $lngSubCode";

                // 更新チェッククエリ設定
                $this->aryCheckQuery["UPDATE"] = "SELECT " . $this->aryColumnName[0] . " FROM " . $this->strTableName . " WHERE " . $this->aryColumnName[0] . " = $lngKeyCodeValue AND " . $this->aryColumnName[1] . " = $lngSubCode";

                break;

            //////////////////////////////////////////////////////
            // 支払様式マスタ管理
            case "m_PayCondition":
                // 文字列チェック設定
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "length(1,100)";

                // 削除チェッククエリ設定
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_Order WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // 仕入区分マスタ管理
            case "m_StockClass":
                // 文字列チェック設定
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "length(1,100)";

                // 削除チェッククエリ設定
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_StockSubject WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // 仕入科目マスタ管理
            case "m_StockSubject":
                // 文字列チェック設定
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[2]] = "length(1,100)";

                // 登録チェッククエリ設定
                $this->aryCheckQuery["INSERT"] = "SELECT " . $this->aryColumnName[0] . " FROM " . $this->strTableName . " WHERE " . $this->aryColumnName[0] . " = $lngKeyCodeValue";

                // 削除チェッククエリ設定
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_StockItem WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;
                $this->aryCheckQuery["DELETE"][1] = "SELECT * FROM m_ProductPrice WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;
                $this->aryCheckQuery["DELETE"][2] = "SELECT * FROM t_StockDetail WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;
                $this->aryCheckQuery["DELETE"][3] = "SELECT * FROM t_OrderDetail WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;
                $this->aryCheckQuery["DELETE"][4] = "SELECT * FROM t_Product WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // 仕入部品マスタ管理
            case "m_StockItem":
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,214748				// 文字列チェック設定
3647)";
                $this->aryCheck[$this->aryColumnName[1]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[2]] = "length(1,100)";

                // 登録チェッククエリ設定
                $this->aryCheckQuery["INSERT"] = "SELECT " . $this->aryColumnName[0] . " FROM " . $this->strTableName . " WHERE " . $this->aryColumnName[0] . " = $lngKeyCodeValue AND " . $this->aryColumnName[1] . " = $lngSubCode";

                // 削除チェッククエリ設定
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_ProductPrice WHERE " . $this->aryColumnName[0] . " = $lngKeyCodeValue AND lngStockSubjectCode = $lngSubCode";
                $this->aryCheckQuery["DELETE"][1] = "SELECT * FROM t_OrderDetail WHERE " . $this->aryColumnName[0] . " = $lngKeyCodeValue AND lngStockSubjectCode = $lngSubCode";
                $this->aryCheckQuery["DELETE"][2] = "SELECT * FROM t_StockDetail WHERE " . $this->aryColumnName[0] . " = $lngKeyCodeValue AND lngStockSubjectCode = $lngSubCode";
                $this->aryCheckQuery["DELETE"][3] = "SELECT * FROM t_Product WHERE " . $this->aryColumnName[0] . " = $lngKeyCodeValue AND lngStockSubjectCode = $lngSubCode";
                // $this->aryCheckQuery["DELETE"][4] = "SELECT * FROM m_StockSubject WHERE lngStockSubjectCode = $lngSubCode";


                break;

            //////////////////////////////////////////////////////
            // アクセスIPアドレスマスタ管理
            case "m_AccessIPAddress":
                // 文字列チェック設定
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(-1,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "IP(1,100,',')";
                $this->aryCheck[$this->aryColumnName[2]] = "length(1,100)";

                // 削除チェッククエリ設定
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_User WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // 証紙種類マスタ管理
            case "m_CertificateClass":
                // 文字列チェック設定
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "length(1,100)";

                // 削除チェッククエリ設定
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_Product WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // 国マスタ管理
            case "m_Country":
                // 文字列チェック設定
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "length(1,100)";
                $this->aryCheck[$this->aryColumnName[2]] = "english(1,100)";

                // 削除チェッククエリ設定
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_Company WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // 版権元マスタ管理
            case "m_Copyright":
                // 文字列チェック設定
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "length(1,100)";

                // 削除チェッククエリ設定
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_Product WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // 組織マスタ管理
            case "m_Organization":
                // 文字列チェック設定
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "length(1,100)";

                // 削除チェッククエリ設定
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_Company WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // 商品形態マスタ管理
            case "m_ProductForm":
                // 文字列チェック設定
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "length(1,100)";

                // 削除チェッククエリ設定
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_Product WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // 売上区分マスタ管理
            case "m_SalesClass":
                // 文字列チェック設定
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "length(1,100)";

                // 削除チェッククエリ設定
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM t_ReceiveDetail WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;
                $this->aryCheckQuery["DELETE"][1] = "SELECT * FROM t_SalesDetail WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // 対象年齢マスタ管理
            case "m_TargetAge":
                // 文字列チェック設定
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "length(1,100)";

                // 削除チェッククエリ設定
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM m_Product WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;

            //////////////////////////////////////////////////////
            // 運搬方法マスタ管理
            case "m_DeliveryMethod":
                // 文字列チェック設定
                $this->aryCheck[$this->aryColumnName[0]] = "null:number(0,2147483647)";
                $this->aryCheck[$this->aryColumnName[1]] = "length(1,100)";

                // 削除チェッククエリ設定
                $this->aryCheckQuery["DELETE"][0] = "SELECT * FROM t_OrderDetail WHERE " . $this->aryColumnName[0] . " = " . $lngKeyCodeValue;

                break;
        }

        return TRUE;
    }



    // -----------------------------------------------------------------
    /**
     *	カラムのHTMLを取得(<td>~</td>・・・)
     *	@param	Int    $lngColumnNum  カラム数(指定がない場合、属性から取得)
     *	@return	String $strColumnHtml カラム行(<td>~</td>・・・)
     */
    // -----------------------------------------------------------------
    function getColumnHtmlTable( $lngColumnNum )
    {
        // カラム数指定がない場合、属性から取得
        if ( !$lngColumnNum )
        {
            $lngColumnNum = count ( $this->aryColumnName );
        }

        // カラム数分<td></td>を生成
        for ( $i = 0; $i < $lngColumnNum; $i++ )
        {
            $strColumnHtml .= "		<td id=\"Column$i\" nowrap>Column$i</td>\n";
        }

        // ワークフロー順番マスタ以外の場合、修正カラム表示
        if ( $this->strTableName != "m_WorkflowOrder" )
        {
            $strColumnHtml .= "		<td id=\"FixColumn\" nowrap>修正</td>\n";
        }
        // 通貨レートマスタ以外の場合、削除カラム表示
        if ( $this->strTableName != "m_MonetaryRate" )
        {
            $strColumnHtml .= "		<td id=\"DeleteColumn\" nowrap>削除</td>\n";
        }
        return $strColumnHtml;
    }
}



// -----------------------------------------------------------------
/**
 *	配列からGETで渡すためのURL文字列を生成
 *	@param	Array  $aryData 変数名をキーに持つ連想配列
 *	@return	String $strURL  URL(&***=***・・・)
 */
// -----------------------------------------------------------------
function fncGetUrl( $aryData )
{
    if ( count ( $aryData ) > 0 )
    {
        $aryKeys = array_keys ( $aryData );
        foreach ( $aryKeys as $strKey )
        {
            $strURL .= "&" . $strKey . "=" . $aryData["$strKey"];
        }
    }
    return $strURL;
}



return TRUE;
?>
