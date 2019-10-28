<?php
/**
*	システム定数宣言
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*/

// HTTP/1.1
//header("Cache-Control: no-store, no-cache, must-revalidate");
//header("Cache-Control: post-check=0, pre-check=0", false);

// HTTP/1.0
//header("Pragma: no-cache");

// ホスト名を自動取得
$strHost="";
if(isset($_SERVER['HTTPS']))
{
	$_SERVER["HTTPS"]=="on" ? $strHost="https://".$_SERVER["HTTP_HOST"] : $strHost="http://".$_SERVER["HTTP_HOST"];
}
else
{
	$strHost="http://".$_SERVER["HTTP_HOST"];
}

// 実行サーバー名
define ( "EXECUTE_HOST_NAME_DEV",	"kids.devs.biz" );
//define ( "EXECUTE_HOST_NAME_KWG_WEBv2",       "192.168.10.229" );
define ( "EXECUTE_HOST_NAME_KWG_WEBv2",	"192.168.10.228" );
define ( "EXECUTE_HOST_NAME_KWG_WEBv1",	"192.168.10.235" );
define ( "EXECUTE_HOST_NAME_KWG_BACK",	"192.168.10.230" );
// define ( "EXECUTE_HOST_NAME_KWG_DB",	"192.168.1.82" );
define ( "EXECUTE_HOST_NAME_KWG_DB",	"192.168.1.113" );
//define ( "EXECUTE_HOST_NAME_KWG_DB",	"127.0.0.1" ); // ローカル確認用

// 実行サーバー名称の取得
$strExcuteHttpHostName = $_SERVER["HTTP_HOST"];


define ( "TOP_URL",	$strHost."/" );

// 開発環境と本番環境をホスト名で振り分け
switch( $strExcuteHttpHostName )
{
	case EXECUTE_HOST_NAME_DEV:
		// URL
//		define ( "TOP_URL", "https://".EXECUTE_HOST_NAME_DEV."/" );
		// DB設定
		define ( "POSTGRESQL_HOSTNAME", "localhost" );
		define ( "POSTGRESQL_HOSTPORT", "5432" );
		define ( "DB_LOGIN_USERNAME", "kids" );
		define ( "DB_LOGIN_PASSWORD", "kids" );
		define ( "DB_NAME", "kidscore2" );
		// エラーメール送信先
		define ( "ERROR_MAIL_TO", "devs.biz@gmail.com" );
		//define ( "ERROR_MAIL_TO", "miyata@testdomain.local" );
		define ( "SEND_MAIL_FLAG", true);
		// 各ルートパス
		define ( "PATH_HOME",	"/home/kids2/" );
		// 通貨(日本円)記号コード
		define ( "DEF_MONETARY_CODE_YEN", "\\" );
		break;

	case EXECUTE_HOST_NAME_KWG_WEBv2:
		// URL
//		define ( "TOP_URL", "https://".EXECUTE_HOST_NAME_KWG_WEBv2."/" );
		// DB設定
//		define ( "POSTGRESQL_HOSTNAME", EXECUTE_HOST_NAME_KWG_DB );
		define ( "POSTGRESQL_HOSTNAME", "localhost" );
//                define ( "POSTGRESQL_HOSTPORT", "5432" );
		define ( "POSTGRESQL_HOSTPORT", "9999" );
		define ( "DB_LOGIN_USERNAME", "kids" );
		define ( "DB_LOGIN_PASSWORD", "kids" );
		define ( "DB_NAME", "kidscore2" );
		// エラーメール送信先
		define ( "ERROR_MAIL_TO", "kids@kuwagata.co.jp" );
		define ( "SEND_MAIL_FLAG", true);
		// 各ルートパス
		define ( "PATH_HOME",	"/home/kids2" );
		// 通貨(日本円)記号コード
		//define ( "DEF_MONETARY_CODE_YEN", "\\\\" );
		define ( "DEF_MONETARY_CODE_YEN", "\\" );
		break;

	case EXECUTE_HOST_NAME_KWG_WEBv1:
		// URL
//		define ( "TOP_URL", "https://".EXECUTE_HOST_NAME_KWG_WEBv1."/" );
		// DB設定
		define ( "POSTGRESQL_HOSTNAME", EXECUTE_HOST_NAME_KWG_DB );
		define ( "POSTGRESQL_HOSTPORT", "5432" );
		define ( "DB_LOGIN_USERNAME", "kids" );
		define ( "DB_LOGIN_PASSWORD", "kids" );
		define ( "DB_NAME", "kidscore" );
		// エラーメール送信先
		define ( "ERROR_MAIL_TO", "kids@kuwagata.co.jp" );
		define ( "SEND_MAIL_FLAG", false);
		// 各ルートパス
		define ( "PATH_HOME",	"/home/kids2" );
		// 通貨(日本円)記号コード
		//define ( "DEF_MONETARY_CODE_YEN", "\\\\" );
		define ( "DEF_MONETARY_CODE_YEN", "\\" );
		break;
	case EXECUTE_HOST_NAME_KWG_BACK:
		// URL
//		define ( "TOP_URL", "https://".EXECUTE_HOST_NAME_KWG_BACK."/" );
		// DB設定
		define ( "POSTGRESQL_HOSTNAME", EXECUTE_HOST_NAME_KWG_BACK );
		define ( "POSTGRESQL_HOSTPORT", "5432" );
		define ( "DB_LOGIN_USERNAME", "kids" );
		define ( "DB_LOGIN_PASSWORD", "kids" );
		define ( "DB_NAME", "kidscore2" );
		// エラーメール送信先
		define ( "ERROR_MAIL_TO", "kids@kuwagata.co.jp" );
		define ( "SEND_MAIL_FLAG", true);
		// 各ルートパス
		define ( "PATH_HOME",	"/home/kids2" );
		// 通貨(日本円)記号コード
		//define ( "DEF_MONETARY_CODE_YEN", "\\\\" );
		define ( "DEF_MONETARY_CODE_YEN", "\\" );
		break;
	default:
		// DB設定
		define ( "POSTGRESQL_HOSTNAME", EXECUTE_HOST_NAME_KWG_DB );
		define ( "POSTGRESQL_HOSTPORT", "5432" );
		define ( "DB_LOGIN_USERNAME", "kids" );
		define ( "DB_LOGIN_PASSWORD", "kids" );
		define ( "DB_NAME", "kidscore2" );
		define ( "LC_POSTGRESQL_HOSTNAME", EXECUTE_HOST_NAME_KWG_DB );
		define ( "LC_POSTGRESQL_HOSTPORT", "5432" );
		define ( "LC_DB_LOGIN_USERNAME", "ackids" );
		define ( "LC_DB_LOGIN_PASSWORD", "x7zv1jid" );
		define ( "LC_DB_NAME", "ackids" );
		// エラーメール送信先
		define ( "ERROR_MAIL_TO", "kids@kuwagata.co.jp" );
		define ( "SEND_MAIL_FLAG", true);
		// 各ルートパス
		define ( "PATH_HOME",	"/Users/solcom/Desktop/Git/KIDS/01_kidsweb/home/kids2" );
		//define ( "PATH_HOME",	"/Users/solcom-ad/Desktop/Git/KIDS/01_kidsweb/home/kids2" ); // ローカル確認用
		// define ( "PATH_HOME",	"E:/Source/Repos/solcom-net/KIDS/01_kidsweb/home/kids2" );
        // define ( "PATH_HOME",    "/Users/esash/Desktop/solcom/git/KIDS2/01_kidsweb/home/kids2" ); // ローカル確認用
        //define ( "LOG_FILE",     PATH_HOME ."/src/logs/debug.log" ); // ローカル確認用

		break;
}


// ログ出力先
define ("DEF_DEBUG_DIR",	PATH_HOME . "/log/");

// ログインURL
define ( "LOGIN_URL",	TOP_URL );

// システムバージョン
define ( "DEF_SYSTEM_VERSION",	"2.1" );

// 直接登録可能権限グループコード
define ( "DEF_DIRECT_REGIST_AUTHORITY_CODE",	4 );

// 設定
// タイムアウト時間(分)
define ( "REMINDER_LIMIT",	1440 );

// タイムアウト警告表示(秒)
define ( "LIMIT_REMAIN_SECONDS",	300 );


define ( "IMAGE_LIMIT",	102400 );
define ( "LAYOUT_CODE",	"type01" );

define ( "TMP_ROOT",	PATH_HOME . "/tmp/" );
define ( "LIB_ROOT",	PATH_HOME . "/lib/" );
define ( "CLS_ROOT",	PATH_HOME . "/lib/" );
define ( "SRC_ROOT",	PATH_HOME . "/src/" );
define ( "SQLLOG_ROOT",	PATH_HOME . "/temp/" );
define ( "VENDOR_ROOT",	PATH_HOME . "/vendor/" );

// クラス・ライブラリパス
define ( "LIB_FILE",			LIB_ROOT . "lib.php" );
define ( "LIB_DEBUGFILE",		LIB_ROOT . "libdebug.php" );			// added by saito 20051017
define ( "LIB_EXCELPARSER_FILE",	LIB_ROOT . "excelparser/excelparser.php" );	// Excel Parser Module
define ( "CLS_DB_FILE",			LIB_ROOT . "clsdb.php" );
define ( "CLS_AUTH_FILE",		LIB_ROOT . "clsauth.php" );
define ( "CLS_TEMPLATE_FILE",		LIB_ROOT . "clstemplate.php" );
define ( "CLS_CACHE_FILE",		LIB_ROOT . "clscache.php" );
define ( "CLS_EXCELMAP_FILE",		LIB_ROOT . "mapping/clsMapping.php" );		// Excel Mapping Object
define ( "CLS_TABLETEMP_FILE",		LIB_ROOT . "tabletemp/clstabletemp.php" );	// Temporary DB Object
define ( "CLS_IMAGELO_FILE",		LIB_ROOT . "clsImageLo.php" );
define ( "VENDOR_AUTOLOAD_FILE",	VENDOR_ROOT . "autoload.php" ); // Composerのautoloadのパス

// 画像ディレクトリパス
define ( "USER_IMAGE_TMPDIR",		PATH_HOME . "/image_tmp/" );	// 仮保存場所
define ( "USER_IMAGE_DIR",		SRC_ROOT . "face/" );		// 画像置き場
define ( "USER_IMAGE_TYPE",		"pjpeg|jpeg|gif" );		// 画像 MIME-TYPE
define ( "USER_IMAGE_URL",		TOP_URL . "face/" );
define ( "USER_IMAGE_DEFAULT_URL",	USER_IMAGE_URL . "default.gif" );

// 商品仕様詳細・画像UP・DEST場所
define ( "DEF_PEDIT_IMGTMP", 		"/p/edit/image_tmp/" );			// URL参照用パス
define ( "USER_IMAGE_PEDIT_TMPDIR",	SRC_ROOT . "p/edit/image_tmp/" );	// システムファイル参照用パス

// ファイルアップロードディレクトリパス
define ( "FILE_UPLOAD_TMPDIR",	PATH_HOME . "/upload_tmp/" );	// ファイルアップロード仮保存場所

// ファイルダウンロードディレクトリパス
define ( "FILE_DOWNLOAD_TMPDIR",	PATH_HOME . "/download_tmp/" );	// ファイルダウンロード仮保存場所

// ワークフロー状態コードが指す状態配列の生成
$aryWorkflowStatus = Array ( "0"=>"初期値", "1"=>"申請中", "2"=>"検討中", "3"=>"差戻し", "4"=>"申請取消", "10"=>"承認", "99"=>"否認" );

// t_Workflow.lngWorkflowStatusCode
define ( "DEF_STATUS_VOID",		0 );	// 初期値
define ( "DEF_STATUS_ORDER",	1 );	// 申請中
define ( "DEF_STATUS_CONSIDER",	2 );	// 検討中
define ( "DEF_STATUS_BACK",		3 );	// 差し戻し
define ( "DEF_STATUS_CANCELL",	4 );	// 申請申請取消
define ( "DEF_STATUS_APPROVE",	10 );	// 承認
define ( "DEF_STATUS_DENIAL",	99 );	// 否認

// m_WorkflowOrder.lngWorkflowStatusCode
define ( "DEF_APPROVE_VOID",	0 );	// 初期値
define ( "DEF_APPROVER",		1 );	// 承認者
define ( "DEF_FINAL_APPROVER",	2 );	// 最終承認者

// m_Product.lngProductStatusCode
define ( "DEF_PRODUCT_NORMAL",		0 );	// マスタ正常
define ( "DEF_PRODUCT_APPLICATE",	1 );	// 申請中

// t_GoodsPlan.lngGoodsPlanProgressCode
define ( "DEF_GOODSPLAN_AFOOT",	1 );	// 企画進行中
define ( "DEF_GOODSPLAN_HOLD",	2 );	// 保留
define ( "DEF_GOODSPLAN_HALT",	3 );	// 中止
define ( "DEF_GOODSPLAN_END",	4 );	// 納品済

// m_Estimate.lngEstimateStatusCode
define ( "DEF_ESTIMATE_TEMPORARY",	1 );	// 仮保存
define ( "DEF_ESTIMATE_APPLICATE",	2 );	// 申請中
define ( "DEF_ESTIMATE_CANCELL",	3 );	// 申請取消
define ( "DEF_ESTIMATE_APPROVE",	4 );	// 承認
define ( "DEF_ESTIMATE_DENIAL",		5 );	// 否認

// m_receivestatus.lngReceiveStatusCode
define ( "DEF_RECEIVE_APPLICATE",	1 );	// 仮受注
define ( "DEF_RECEIVE_ORDER",		2 );	// 受注
define ( "DEF_RECEIVE_DELIVER",		3 );	// 納品中
define ( "DEF_RECEIVE_END",			4 );	// 納品済
define ( "DEF_RECEIVE_CLOSED",		99 );	// 締め済

// m_Sales.lngSalesStatusCode
//直売上登録で納品済みにする修正By　kou　2007/10/2
define ( "DEF_SALES_APPLICATE",	1 );	// 申請中
define ( "DEF_SALES_ORDER",		4 );	// 売上
define ( "DEF_SALES_DELIVER",	3 );	// 納品中
define ( "DEF_SALES_END",		4 );	// 納品済
define ( "DEF_SALES_DENIAL",	5 );	// 否認
define ( "DEF_SALES_CLOSED",	99 );	// 締め済

// m_orderstatus.lngOrderStatusCode
define ( "DEF_ORDER_APPLICATE",	1 );	// 仮受注
define ( "DEF_ORDER_ORDER",		2 );	// 発注
define ( "DEF_ORDER_DELIVER",	3 );	// 納品中
define ( "DEF_ORDER_END",		4 );	// 納品済
define ( "DEF_ORDER_CLOSED",	99 );	// 締め済

// m_Stock.lngStockStatusCode
define ( "DEF_STOCK_APPLICATE",	1 );	// 申請中
define ( "DEF_STOCK_ORDER",		2 );	// 仕入
define ( "DEF_STOCK_DELIVER",	3 );	// 納品中
define ( "DEF_STOCK_END",		4 );	// 納品済
define ( "DEF_STOCK_DENIAL",	5 );	// 否認
define ( "DEF_STOCK_CLOSED",	99 );	// 締め済

// m_AuthorityGroup.lngAuthorityLevel
define ( "DEF_AUTHG_ADMIN",		10 );	// システム管理者
define ( "DEF_AUTHG_OPERATOR",	20 );	// システム運用者
define ( "DEF_AUTHG_DIRECTOR",	100 );	// ディレクター
define ( "DEF_AUTHG_MANAGER",	110 );	// マネージャー
define ( "DEF_AUTHG_USER",		120 );	// ユーザー
define ( "DEF_AUTHG_GUEST",		130 );	// ゲスト

// m_Attribute.lngAttributeCode
define ( "DEF_ATTRIBUTE_HEADOFFICE",	1 );	// 本社
define ( "DEF_ATTRIBUTE_CLIENT",		2 );	// 顧客
define ( "DEF_ATTRIBUTE_SUPPLIER",      3 );    // 仕入先
define ( "DEF_ATTRIBUTE_DELIVERY",      5 );    // 納品場所・倉庫

// m_ReportClass.lngReportClassCode
define ( "DEF_REPORT_PRODUCT",	1 );	// 商品化企画書
define ( "DEF_REPORT_ORDER",	2 );	// PurchaseOrder(発注)
define ( "DEF_REPORT_ESTIMATE",	3 );	// 見積原価計算
define ( "DEF_REPORT_MOLD",	4 );		// 金型依頼書
define ( "DEF_REPORT_SLIP",	5 );		// 納品書
define ( "DEF_REPORT_INV",	6 );		// 請求書

// m_Organization.lngOrganizationCode
define ( "DEF_ORGANIZATION_FOREIGN",	11 ); // 海外

// m_Function.lngFunctionCode
define ( "DEF_FUNCTION_LOGIN0",	100 );	//  100 ログイン
define ( "DEF_FUNCTION_LOGIN1",	101 );	//  101 ログイン後
define ( "DEF_FUNCTION_LOGIN2",	102 );	//  102 パスワードリマインダー
define ( "DEF_FUNCTION_MENU0",	200 );	//  200 メニュー表示

define ( "DEF_FUNCTION_P0",	300 );      //  300 商品管理
define ( "DEF_FUNCTION_P1",	301 );      //  301 商品管理（商品登録）
define ( "DEF_FUNCTION_P2",	302 );      //  302 商品管理（商品検索）
define ( "DEF_FUNCTION_P3",	303 );      //  303 商品管理（商品検索　削除データの表示）
define ( "DEF_FUNCTION_P4",	304 );      //  304 商品管理（商品情報詳細）
define ( "DEF_FUNCTION_P5",	305 );      //  305 商品管理（商品情報詳細　削除データの表示）
define ( "DEF_FUNCTION_P6",	306 );      //  306 商品管理（商品修正）
define ( "DEF_FUNCTION_P7",	307 );      //  307 商品管理（商品削除）

define ( "DEF_FUNCTION_SO0",	400 );     //  400 受注管理
define ( "DEF_FUNCTION_SO1",	401 );     //  401 受注管理（受注検索）
define ( "DEF_FUNCTION_SO2",	402 );     //  402 受注管理（受注検索　管理モード）
define ( "DEF_FUNCTION_SO3",	403 );     //  403 受注管理（受注詳細表示）
define ( "DEF_FUNCTION_SO4",	404 );     //  404 受注管理（受注確定）
define ( "DEF_FUNCTION_SO5",	405 );     //  405 受注管理（受注確定取消）

define ( "DEF_FUNCTION_PO0",	500 );     //  500 発注管理
define ( "DEF_FUNCTION_PO1",	501 );     //  501 発注管理（発注登録）
define ( "DEF_FUNCTION_PO2",	502 );     //  502 発注管理（発注検索）
define ( "DEF_FUNCTION_PO3",	503 );     //  503 発注管理（発注検索　管理モード）
define ( "DEF_FUNCTION_PO4",	504 );     //  504 発注管理（発注詳細表示）
define ( "DEF_FUNCTION_PO5",	505 );     //  505 発注管理（発注修正）
define ( "DEF_FUNCTION_PO6",	506 );     //  506 発注管理（発注削除）
define ( "DEF_FUNCTION_PO7",	507 );     //  507 発注管理（発注の無効化処理）
define ( "DEF_FUNCTION_PO8",	508 );     //  508 発注管理（商品マスタダイレクト修正）
define ( "DEF_FUNCTION_PO9",	509 );     //  509 発注管理（発注確定）
define ( "DEF_FUNCTION_PO10",	510 );     //  510 発注管理（発注書検索）
define ( "DEF_FUNCTION_PO11",	511 );     //  511 発注管理（発注書検索　管理モード）
define ( "DEF_FUNCTION_PO12",	512 );     //  512 発注管理（発注書修正）

define ( "DEF_FUNCTION_SC0",	600 );     //  600 売上管理
define ( "DEF_FUNCTION_SC1",	601 );     //  601 売上管理（売上(納品書)登録）
define ( "DEF_FUNCTION_SC2",	602 );     //  602 売上管理（売上検索）
define ( "DEF_FUNCTION_SC3",	603 );     //  603 売上管理（売上検索　管理モード）
define ( "DEF_FUNCTION_SC4",	604 );     //  604 売上管理（売上(納品書)詳細表示）
define ( "DEF_FUNCTION_SC5",	605 );     //  605 売上管理（売上(納品書)修正）
define ( "DEF_FUNCTION_SC6",	606 );     //  606 売上管理（売上(納品書)削除）
define ( "DEF_FUNCTION_SC7",	607 );     //  607 売上管理（売上の無効化処理）
define ( "DEF_FUNCTION_SC8",	608 );     //  608 売上管理（売上の受注Noを指定しない登録）
define ( "DEF_FUNCTION_SC9",	609 );     //  609 売上管理（売上の受注Noを指定しない設定金額（DEF_MONEY_NO_CODE）以上の登録）
define ( "DEF_FUNCTION_SC10",	610 );     //  610 売上管理（行追加・行削除）
define ( "DEF_FUNCTION_SC11",	611 );     //  611 売上管理（売上詳細表示）
define ( "DEF_FUNCTION_SC12",	612 );     //  612 売上管理（売上納品書検索）


define ( "DEF_FUNCTION_PC0",	700 );     //  700 仕入管理
define ( "DEF_FUNCTION_PC1",	701 );     //  701 仕入管理（仕入登録）
define ( "DEF_FUNCTION_PC2",	702 );     //  702 仕入管理（仕入検索）
define ( "DEF_FUNCTION_PC3",	703 );     //  703 仕入管理（仕入検索　管理モード）
define ( "DEF_FUNCTION_PC4",	704 );     //  704 仕入管理（仕入詳細表示）
define ( "DEF_FUNCTION_PC5",	705 );     //  705 仕入管理（仕入修正）
define ( "DEF_FUNCTION_PC6",	706 );     //  706 仕入管理（仕入削除）
define ( "DEF_FUNCTION_PC7",	707 );     //  707 仕入管理（仕入の無効化処理）
define ( "DEF_FUNCTION_PC8",	708 );     //  708 仕入管理（仕入の発注Noを指定しない登録）
define ( "DEF_FUNCTION_PC9",	709 );     //  709 仕入管理（仕入の発注Noを指定しない設定金額（DEF_MONEY_NO_CODE）以上の登録）
define ( "DEF_FUNCTION_PC10",	710 );     //  710 仕入管理（行追加・行削除）

define ( "DEF_FUNCTION_WF0",	800 );     //  800 ワークフロー管理
define ( "DEF_FUNCTION_WF1",	801 );     //  801 ワークフロー管理（案件一覧）
define ( "DEF_FUNCTION_WF2",	802 );     //  802 ワークフロー管理（案件検索）
define ( "DEF_FUNCTION_WF3",	803 );     //  803 ワークフロー管理（案件検索２）
define ( "DEF_FUNCTION_WF4",	804 );     //  804 ワークフロー管理（案件情報詳細）
define ( "DEF_FUNCTION_WF5",	805 );     //  805 ワークフロー管理（案件情報詳細２）
define ( "DEF_FUNCTION_WF6",	806 );     //  806 ワークフロー管理（案件の処理）

define ( "DEF_FUNCTION_LO0",	900 );     //  900 帳票出力
define ( "DEF_FUNCTION_LO1",	901 );     //  901 帳票出力(商品化企画書)
define ( "DEF_FUNCTION_LO2",	902 );     //  902 帳票出力(発注書ＰＯ)
define ( "DEF_FUNCTION_LO3",	903 );     //  903 帳票出力(商品化企画書)コピー解除権限
define ( "DEF_FUNCTION_LO4",	904 );     //  904 帳票出力(発注書ＰＯ)コピー解除権限
define ( "DEF_FUNCTION_LO5",	905 );     //  905 帳票出力(納品書)
define ( "DEF_FUNCTION_LO6",	906 );     //  906 帳票出力(納品書)コピー解除権限
define ( "DEF_FUNCTION_LO7",	907 );     //  907 帳票出力(請求書)
define ( "DEF_FUNCTION_LO8",	908 );     //  908 帳票出力(請求書)コピー解除権限

define ( "DEF_FUNCTION_DE0",	1000 );    // 1000 データエクスポート
define ( "DEF_FUNCTION_DE1",	1001 );    // 1001 データエクスポート(発注管理/L/C 予定表)
define ( "DEF_FUNCTION_DE2",	1002 );    // 1002 データエクスポート(売上管理/売上レシピ)
define ( "DEF_FUNCTION_DE3",	1003 );    // 1003 データエクスポート(仕入管理/仕入一覧表)
define ( "DEF_FUNCTION_DE4",	1004 );    // 1004 データエクスポート(仕入管理/Purchase recipe file)
define ( "DEF_FUNCTION_DE5",	1005 );    // 1005 見積原価書
define ( "DEF_FUNCTION_DE6",	1006 );    // 1006 社内統計データ（売上見込）
define ( "DEF_FUNCTION_DE7",	1007 );    // 1007 社内統計データ（概算売上）
define ( "DEF_FUNCTION_DE8",	1008 );    // 1008 データエクスポート(商品計画書)

define ( "DEF_FUNCTION_UC0",	1100 );    // 1100 ユーザー管理
define ( "DEF_FUNCTION_UC1",	1101 );    // 1101 ユーザー管理（ユーザー情報）
define ( "DEF_FUNCTION_UC2",	1102 );    // 1102 ユーザー管理（ユーザー登録）
define ( "DEF_FUNCTION_UC3",	1103 );    // 1103 ユーザー管理（ユーザー検索）
define ( "DEF_FUNCTION_UC4",	1104 );    // 1104 ユーザー管理（ユーザー詳細）
define ( "DEF_FUNCTION_UC5",	1105 );    // 1105 ユーザー管理（ユーザー修正）

define ( "DEF_FUNCTION_M0" ,	1200 );   // 1200 マスタ管理
define ( "DEF_FUNCTION_SYS0",	1300 );   // 1300 システム管理
define ( "DEF_FUNCTION_SYS1",	1301 );   // 1301 システム管理(お知らせ)
define ( "DEF_FUNCTION_SYS2",	1302 );   // 1302 システム管理(サーバー管理)
define ( "DEF_FUNCTION_SYS3",	1303 );   // 1303 システム管理(管理者メール)
define ( "DEF_FUNCTION_SYS4",	1304 );   // 1304 システム管理(セッション)
define ( "DEF_FUNCTION_SYS5",	1305 );   // 1305 システム管理(月次バッチ処理)

define ( "DEF_FUNCTION_CLD0",	1400 );	// 1400 締め処理

define ( "DEF_FUNCTION_E0",	1500 );	// 1500 見積原価管理
define ( "DEF_FUNCTION_E1",	1501 );	// 1501 見積原価管理（登録）
define ( "DEF_FUNCTION_E2",	1502 );	// 1502 見積原価管理（検索）
define ( "DEF_FUNCTION_E3",	1503 );	// 1503 見積原価管理（修正）
define ( "DEF_FUNCTION_E4",	1504 );	// 1504 見積原価管理（削除）

define ( "DEF_FUNCTION_UP0",	1600 );	// 1600 アップロード管理
define ( "DEF_FUNCTION_UP1",	1601 );	// 1601 アップロード管理（登録）

define ( "DEF_FUNCTION_BG02",	1702 );	// 1702 バッググラウンド処理（公開システム・進捗管理機能連携／WF承認後メール送信）

define ( "DEF_FUNCTION_MM0", 1800 );	//金型管理
define ( "DEF_FUNCTION_MM1", 1801 );	//金型管理(登録)
define ( "DEF_FUNCTION_MM2", 1802 );	//金型管理(検索)
define ( "DEF_FUNCTION_MM3", 1803 );	//金型管理(詳細)
define ( "DEF_FUNCTION_MM4", 1804 );	//金型管理(修正)
define ( "DEF_FUNCTION_MM5", 1805 );	//金型管理(削除)

define ( "DEF_FUNCTION_MR0", 1900 );	//金型帳票管理
define ( "DEF_FUNCTION_MR1", 1901 );	//金型帳票管理(登録)
define ( "DEF_FUNCTION_MR2", 1902 );	//金型帳票管理(検索)
define ( "DEF_FUNCTION_MR3", 1903 );	//金型帳票管理(詳細)
define ( "DEF_FUNCTION_MR4", 1904 );	//金型帳票管理(修正)
define ( "DEF_FUNCTION_MR5", 1905 );	//金型帳票管理(削除)

define ( "DEF_FUNCTION_DWN",	2001 );	// 2001 ダウンロード管理

define ( "DEF_FUNCTION_LC0",	2100 );	// 2100 L/C管理
define ( "DEF_FUNCTION_LC1",	2101 );	// 2101 L/C情報
define ( "DEF_FUNCTION_LC2",	2102 );	// 2102 L/C設定変更
define ( "DEF_FUNCTION_LC3",	2103 );	// 2103 L/C編集
define ( "DEF_FUNCTION_LC4",	2104 );	// 2104 L/C帳票出力

define ( "DEF_FUNCTION_INV0",	2200 );	// 2200 請求管理
define ( "DEF_FUNCTION_INV1",	2201 );	//  請求書発行
define ( "DEF_FUNCTION_INV2",	2202 );	// 2202 請求書検索
define ( "DEF_FUNCTION_INV3",	2203 );	// 2203 請求集計

// エラー種類
define ( "DEF_WARNING",		1 );           //  1 注意レベル（入力ミスなど）
define ( "DEF_ERROR",		2 );           //  2 エラーレベル
define ( "DEF_FATAL",		3 );           //  3 システムエラーレベル
define ( "DEF_ANNOUNCE",	99 );          //  99 システムアナウンス

// デバッグモード
//define ( "DEF_DEBUG_MODE",	0 );         //  本稼動モード　　エラー画面表示
define ( "DEF_DEBUG_MODE",	1 );      //  デバッグモード　エラー画面非表示

// 発注登録
define ("DEF_M_WORKFLOW_FUNCTIONCODE",	1);		// 機能コード
define ("DEF_T_WORKFLOW_SUBCODE",		1);		// ワークフローサブコード
define ("DEF_T_WORKFLOW_ORDERNO",		1);		// ワークフロー順序番号
define ("DEF_T_WORKFLOW_STATUS",		1);		// ワークフロー状態コード

// 通貨タイプ
define ("DEF_MONETARY_YEN",	1);				// 日本円
define ("DEF_MONETARY_USD",	2);				// USドル
define ("DEF_MONETARY_HKD",	3);				// HKドル

// 社内レート定義
define ("DEF_MONETARYCLASS_SHANAI", 	2);		// 社内

define ("DEF_MONEY_USER",		0);			// ユーザはなし
define ("DEF_MONEY_MANAGER",	50000);		// マネージャーは5万まで
define ("DEF_MONEY_DIRECTOR",	200000);	// ディレクターは20万まで 

// 2004.03.01 suzukaze update start
define ("DEF_MONEY_NO_CODE",	50000);		// 仕入売上での発注受注Noなしでの登録可能金額  上記コードを使用しないように修正
// 2004.03.01 suzukaze update end

// 商品の価格のデフォルト通貨
define ("DEF_PRODUCT_MONETARYSIGN",	"\\");	// 商品情報表示時の通貨表記

// 検索件数の表示ＭＡＸ件数
define ("DEF_SEARCH_MAX",	1000);			// デフォルトで1000件以上の場合はエラー表示する

// エラー時の動作
define ("DEF_ERROR_CLOSE",		0);				// エラー時にウィンドウを閉じる
define ("DEF_ERROR_BACK",		1);				// エラー時に前の画面に戻る

// 月次バッチ処理
define ("DEF_BATCH_DAY",	   	"01");		// 月次バッチ処理実行日付
define ("DEF_BATCH_USERID",		"batsys");	// （未使用）バッチ処理実行ユーザー
define ("DEF_BATCH_USERCODE",	 0);		// バッチ処理実行ユーザーのユーザーコード

// 2004/03/12 suzukaze update start
// 換算区分
define ("DEF_CONVERSION_SEIHIN",		1);		// 製品単位計上
define ("DEF_CONVERSION_NISUGATA",		2);		// 荷姿単位計上

// 単位
define ("DEF_PRODUCTUNIT_PCS",		1);			// Piece
define ("DEF_PRODUCTUNIT_CTN",		2);			// Carton
define ("DEF_PRODUCTUNIT_SET",		3);			// Set
// 2004/03/12 suzukaze update end

// 2004/03/12 watanabe update start
define ( "DEF_EN_MARK",	"\\");				// 商品登録の確認画面で「￥」を表示
// watanabe update end

// 2004.03.24 suzukaze update start
// 受注削除時に受注Noに追加する文字
define ( "DEF_RECEIVE_DEL_START",	"*");		// 開始文字
define ( "DEF_RECEIVE_DEL_END",		"*");		// 終了文字
// 2004.03.24 suzukaze update end

// 2004.03.25 suzukaze update start
// 2004.05.31 suzukaze update start
// 金型番号を取得する仕入科目、仕入部品
define ( "DEF_MOLD_STOCK_SUBJECT",	433);		// 仕入科目　４３３（金型海外償却）
define ( "DEF_MOLD_STOCK_ITEM",		1);			// 仕入部品　１（Injection Mold）
// 金型番号を取得する仕入科目、仕入部品 追加分
define ( "DEF_MOLD_STOCK_SUBJECT_ADD",	431);	// 仕入科目　４３１（金型償却高）
define ( "DEF_MOLD_STOCK_ITEM_ADD",		8);		// 仕入部品　８（金型）
// 2004.05.31 suzukaze update end
// 2004.03.25 suzukaze update end

// 2004.03.30 suzukaze update start
// 税区分
define ("DEF_TAXCLASS_HIKAZEI",	1);				// 非課税
define ("DEF_TAXCLASS_SOTOZEI",	2);				// 外税
define ("DEF_TAXCLASS_UCHIZEI",	3);				// 内税
// 2004.03.30 suzukaze update end

// 2004.04.08 suzukaze update start
// 端数処理方法
define ("DEF_CALC_SISHAGONYU",	0);				// 四捨五入
define ("DEF_CALC_KIRISUTE",	1);				// 切捨て
define ("DEF_CALC_KIRIAGE",		2);				// 切上げ
// 2004.04.08 suzukaze update end

// 2004.04.09 suzukaze update start
// 通貨に対する端数処理対象桁数
define ("DEF_CALCMONETARY_YEN",		0);				// 円の場合、端数処理は０桁 Ex. 1234.56-->1234.00
define ("DEF_CALCMONETARY_OTHER",	2);				// 円以外の場合、端数処理は小数点以下２桁 Ex. 1234.5678-->1234.58
// 2004.04.09 suzukaze update end

// 2004.05.13 suzukaze update start
// 支払条件
define ("DEF_PAYCONDITION_LC",		1);		// LC
define ("DEF_PAYCONDITION_TT",		2);		// TT
// 2004.05.13 suzukaze update end

// 商品化企画書フォーマットコード
define ( "DEF_P_FORMAT_CODE",	"2004-12-1" );

// 対象エリアコード
define ( "DEF_AREA_PRODUCT_SALES",     1);  // 製品売上
define ( "DEF_AREA_FIXED_COST_SALES",  2);  // 固定費売上
define ( "DEF_AREA_FIXED_COST_ORDER",  3);  // 固定費
define ( "DEF_AREA_PARTS_COST_ORDER",  4);  // 部材費
define ( "DEF_AREA_OTHER_COST_ORDER",  5);  // その他費用

// 通貨レート区分
define ( "DEF_MONETARY_RATE_CODE_TTM",           1); // TTM
define ( "DEF_MONETARY_RATE_CODE_COMPANY_LOCAL", 2); // 社内

// // メッセージコード
define ( "DEF_MESSAGE_CODE_RATE_UNCAUGHT_WARNING",     9201); // 通貨レート取得不可
define ( "DEF_MESSAGE_CODE_RATE_DIFFER",               9202); // 通貨レート差異
define ( "DEF_MESSAGE_CODE_NOT_ENTRY_ERROR",           9203); // 未入力エラー
define ( "DEF_MESSAGE_CODE_FORMAT_ERROR",              9204); // 形式エラー
define ( "DEF_MESSAGE_CODE_MASTER_CHECK_ERROR",        9205); // マスターチェックエラー
define ( "DEF_MESSAGE_CODE_PRODUCT_CODE_ERROR",        9206); // 製品コードチェックエラー
define ( "DEF_MESSAGE_CODE_FILE_UPLOAD_ERROR",         9207); // ファイルアップロードエラー
define ( "DEF_MESSAGE_CODE_VALID_SHEET_ABSENT_ERROR",  9208); // 有効シート非存在エラー
define ( "DEF_MESSAGE_CODE_CURRENT_REVISION_ERROR",    9209); // 最新リビジョンチェックエラー
define ( "DEF_MESSAGE_CODE_EXCLUSIVE_CHECK_ERROR",     9210); // 排他チェックエラー
define ( "DEF_MESSAGE_CODE_DELETE_CHECK_ERROR",        9211); // 削除チェックエラー
define ( "DEF_MESSAGE_CODE_DELETED_ERROR",             9212); // 削除済エラー

// 仕入区分マスタコード
define ("DEF_STOCK_CLASS_CODE_PARTS",      1); // 材料費
define ("DEF_STOCK_CLASS_CODE_PRODUCTION", 2); // 製造費用
define ("DEF_STOCK_CLASS_CODE_TEST",       3); // テスト費用

// 納品伝票種類マスタコード
define ("DEF_SLIP_KIND_EXCLUSIVE", 1);   // 専用帳票
define ("DEF_SLIP_KIND_COMM",      2);   // 市販帳票
define ("DEF_SLIP_KIND_DEBIT",     3);   // DEBIT NOTE

// 売上分類マスタコード
define ("DEF_SALES_DIVISION_CODE_FIXED_COST_SALES",    1); // 固定費売上
define ("DEF_SALES_DIVISION_CODE_PRODUCT_SALES",       2); // 製品売上

// 売上区分マスタコード
define ("DEF_SALES_CLASS_CODE_MAIN_PRODUCT",    1); // 本荷

// 仕入科目マスタコード
define ("DEF_STOCK_SUBJECT_CODE_OVERSEA_MANUFACTURE",       420); // 外注加工費
define ("DEF_STOCK_SUBJECT_CODE_MOLD_DEPRECIATION",         431); // 金型償却高
define ("DEF_STOCK_SUBJECT_CODE_OVERSEA_MOLD_DEPRECIATION", 433); // 金型海外償却
define ("DEF_STOCK_SUBJECT_CODE_MATERIAL_PARTS_COST",       401); // 材料パーツ仕入高
define ("DEF_STOCK_SUBJECT_CODE_IMPORT_PARTS_COST",         402); // 輸入パーツ仕入高
define ("DEF_STOCK_SUBJECT_CODE_CHARGE",                   1224); // チャージ
define ("DEF_STOCK_SUBJECT_CODE_EXPENSE" ,                 1230); // 経費
define ("DEF_STOCK_SUBJECT_CODE_MATERIAL_TOOLS_COST",       403); // 材料ツール仕入高

// 仕入部品マスタコード
define ("DEF_STOCK_ITEM_CODE_MASS_PRODUCT", 1); // Mass Product

define ("DEF_STOCK_ITEM_CODE_IMPORT_COST",  2); // 輸入費用
define ("DEF_STOCK_ITEM_CODE_TARIFF",       3); // 関税

// 帳票テンプレートディレクトリパス
define ( "REPORT_TMPDIR",	PATH_HOME . "/report_tmp/" );
// L/C帳票テンプレートファイル
define ( "REPORT_LC_TMPFILE",	"帳票テンプレート.xls" );
// L/C帳票テンプレートファイル
define ( "REPORT_LC_OUTPUTFILE",	"帳票テンプレート_copy.xls" );

// ワークシートテンプレートディレクトリパス
define ( "EXCEL_TMP_ROOT", PATH_HOME . "/excel_tmp/");

// 会社コード
define ( "DEF_COMPANY_CODE_OTHERS", 0); // その他

// 表示用会社コード
define ( "DEF_DISPLAY_COMPANY_CODE_OTHERS", '0000'); // その他

// グループ属性コード
define ( "DEF_GROUP_ATTRIBUTE_CODE_SALES_GROUP",              1);  // 営業部門
define ( "DEF_GROUP_ATTRIBUTE_CODE_DEVELOP_GROUP",            2);  // 開発部門
define ( "DEF_GROUP_ATTRIBUTE_CODE_SALES_SUPPORT",            3);  // 営業サポート
define ( "DEF_GROUP_ATTRIBUTE_CODE_ACCOUNTING",               4);  // 経理
define ( "DEF_GROUP_ATTRIBUTE_CODE_QUALITY_ASSURANCE",        5);  // 品質保証

// データベースの格納サイズ
define ( "DEF_DB_INTEGER_MAX_LIMIT",                 2147483647);  // integerの最大値
define ( "DEF_DB_INTEGER_MIN_LIMIT",                -2147483647);  // integerの最小値

// 排他制御テーブルの機能コード
define ("EXCLUSIVE_CONTROL_FUNCTION_CODE_SC_RENEW", 1);		// 納品書修正機能

?>
