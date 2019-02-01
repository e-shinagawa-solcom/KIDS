<?

/*
名称: CListOutput クラス

概要: 設定ファイルに基づきテンプレート置き換えつつ出力する

*/

	// 処理タイプの定義
	define("CLISTOUTPUT_COMMENT",	"#");		// コメント
	define("CLISTOUTPUT_TYPE",		"%");		// 処理タイプ
	define("CLISTOUTPUT_DATANAME",	"&");		// データ処理名
	define("CLISTOUTPUT_REFERENCE",	"@");		// データ参照
	define("CLISTOUTPUT_FILE",		"@@");		// ファイル参照
	define("CLISTOUTPUT_DATA",		"");		// データ
	
	// 置き換えリスト動作定義
	define("CLISTOUTPUT_REPLACE_ALL",	1);		// 置き換えリストで設定ファイル全体を置き換える
	define("CLISTOUTPUT_REPLACE_SQL",	2);		// 置き換えリストで設定ファイルのSQL部分のみ置き換える
	
	// EVALの実行モードの動作定義
	define("CLISTOUTPUT_EVAL_LINE",		1);		// 行毎にeval()を実行する
	define("CLISTOUTPUT_EVAL_CLASS",	2);		// クラスを作成してeval()を一度だけ実行する
	
	// EVALのキャッシュの動作定義
	define("CLISTOUTPUT_EVAL_CACHE_OFF",	1);		// キャッシュを利用しない
	define("CLISTOUTPUT_EVAL_CACHE_ON",		2);		// キャッシュを利用する
	
	// EVALのキャッシュのまとめ生成の動作定義(LoadConfig()時にまとめてEVALのキャッシュを作成する)
	define("CLISTOUTPUT_BIND_EVAL_CACHE_OFF",	1);		// まとめ生成を利用しない
	define("CLISTOUTPUT_BIND_EVAL_CACHE_ON",	2);		// まとめ生成を利用する
	
	// ファイル書き込みの際のVerify処理のレベル
	// 0: Verifyなし
	// 1: ファイルサイズチェック
	// 2: ファイル内容チェック
	define("CLISTOUTPUT_WRITE_VERIFY_LEVEL", 2);

	// 処理エンコーディング
	define("CLISTOUTPUT_HANDLINGENCODING", "EUC-JP");


//
//	CListOutputクラス
//
class CListOutput{
	var $strOutputDir;				// 出力先ディレクトリ
	var $strOutputFile;				// 出力ファイル
	var $aryConfig;					// 設定内容を保存
	var $strConfigDir;				// 設定ファイルの入ったディレクトリ
	var $strConfigFile;				// 設定ファイル
	var $aryResult;					// DBから取得したデータの配列
	var $aryColumnResult;			// DBから取得したデータのカラム配列
	var $aryReplaceList;			// テンプレート置き換え用配列
	var $aryColumnList;				// テンプレート置き換え用カラム配列
	var $strTemplateDir;			// テンプレートのあるディレクトリ
	var $aryFileCache;				// テンプレートファイルのキャッシュを保存
	var $strErrorMessage;			// 最新のエラーメッセージを保存
	var $lngMaxToken;				// このクラスから使用可能な最大トークン
	var $lngAlertToken;				// 残りトークンがこの数以下になったら警告を発する
	var $lngCurrentToken;			// 現時点で使用したトークン
	var $bytReplaceMode;			// 置き換え動作
	var $bytEvalMode;				// EVALの実行モード
	var $bytEvalCacheMode;			// EVALのキャッシュ動作モード
	var $aryEvalCache;				// EVALのキャッシュ
	var $bytBindEvalCacheMode;		// EVALキャッシュのまとめ生成
	var $objContext;				// ListOutputのオブジェクトが生成された時点から持っているコンテキスト(値等を保存しておける)
	
	//	関数名:		CListOutput
	//
	//	概要:		コンストラクタ
	//
	//	引数:		なし
	//				
	//	戻り値:		なし
	//
	//	エラー:		返さない
	//
	function CListOutput(){
		// 出力先ディレクトリが未定義ならばデフォルトを定義する
		if(empty($this->strOutputDir)){
			$this->strOutputDir = './';
		}
		
		// 設定ディレクトリが未定義ならばデフォルトを定義する
		if(empty($this->strConfigDir)){
			$this->strConfigDir = './';
		}

		// テンプレートディレクトリが未定義ならばデフォルトを定義する
		if(empty($this->strTemplateDir)){
			$this->strTemplateDir = './';
		}
		
		// 設定ファイルが定義されていればデータ展開を行う
		if(!empty($this->strConfigFile)){
			// 設定ファイルからデータ展開
			$this->LoadConfigFile();
		}
		
		// 置き換えリストの初期化
		if(!is_array($this->aryReplaceList) or !is_array($this->aryColumnList)){
			$this->ClearReplaceList();
		}
		
		// 最大トークンをセットする
		$this->SetMaxToken(4000);
		
		// 警告を発する残りトークンをセットする
		$this->lngAlertToken = 200;
		
		// 使用済トークンを 0 とする
		$this->SetCurrentToken(0);
		
		// 置き換えリストのデフォルト動作
		$this->SetReplaceMode(CLISTOUTPUT_REPLACE_SQL);
		
		// EVALの実行モードのデフォルト動作
		$this->SetEvalMode(CLISTOUTPUT_EVAL_LINE);
		
		// EVALのキャッシュ動作モード
		$this->SetEvalCacheMode(CLISTOUTPUT_EVAL_CACHE_ON);
		
		// EVALキャッシュの統合生成
		$this->SetBindEvalCacheMode(CLISTOUTPUT_BIND_EVAL_CACHE_ON);
		
		// EVALキャッシュを初期化する
		$this->ClearEvalCache();

		// コンテキストを初期化する
		$this->ClearContext();
	}
	
	//	関数名:		ClearExecute
	//
	//	概要:		実行結果の初期化を行う
	//
	//	引数:		なし
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function ClearExecute(){
		// 実行結果の初期化
		$this->aryResult = array();
		// 実行結果のカラム配列の初期化
		$this->aryColumnResult = array();
		
		return TRUE;
	}
	
	//	関数名:		ClearConfig
	//
	//	概要:		設定データの初期化を行う
	//
	//	引数:		なし
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function ClearConfig(){
		// 設定データの初期化
		unset($this->aryConfig);
		
		return TRUE;
	}
	
	//	関数名:		ClearReplaceList
	//
	//	概要:		置き換えリストの初期化を行う
	//
	//	引数:		なし
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function ClearReplaceList(){
		// 置き換えリストの初期化
		$this->aryReplaceList = array();
		// 置き換えかラムリストの初期化
		$this->aryColumnList = array();
		
		return TRUE;
	}
	
	//	関数名:		ClearErrorMessage
	//
	//	概要:		エラーメッセージの初期化を行う
	//
	//	引数:		なし
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function ClearErrorMessage(){
		// エラーメッセージの初期化
		$this->strErrorMessage = '';
		
		return TRUE;
	}
	
	//	関数名:		ClearEvalCache
	//
	//	概要:		EVALキャッシュを初期化する
	//
	//	引数:		なし
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function ClearEvalCache(){
		// EVALキャッシュを初期化する
		$this->aryEvalCache = array();
		
		return TRUE;
	}

	//	関数名：	ClearContext
	//
	//	概要:		コンテキストを初期化する
	//
	//	引数:		なし
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function ClearContext() {
		// コンテキストを初期化する
		$this->objContext = new CListOutputContext();

		return TRUE;
	}
	
	//	関数名:		GetErrorMessage
	//
	//	概要:		エラーメッセージを取得する
	//
	//	引数:		なし
	//
	//	戻り値:		最新のエラーメッセージの内容
	//
	//	エラー:		返さない
	//
	function GetErrorMessage(){
		// エラーメッセージの取得
		return $this->strErrorMessage;
	}
	
	//	関数名:		GetCurrentToken
	//
	//	概要:		使用済みトークン数を取得
	//
	//	引数:		なし
	//
	//	戻り値:		トークン数
	//
	//	エラー:		返さない
	//
	function GetCurrentToken(){
		// トークン数の取得
		return $this->lngCurrentToken;
	}
	
	//	関数名:		SetCurrentToken
	//
	//	概要:		使用済みトークン数をセット
	//
	//	引数:		トークン数
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function SetCurrentToken($lngCurrentToken){
		// トークン数のセット
		$this->lngCurrentToken = $lngCurrentToken;
		
		return TRUE;
	}
	
	//	関数名:		ExportEvalCache
	//
	//	概要:		EVALキャッシュをエクスポートする
	//
	//	引数:		なし
	//
	//	戻り値:		EVALキャッシュ配列
	//
	//	エラー:		返さない
	//
	function ExportEvalCache(){
		// EVALキャッシュ配列を取得する
		return $this->aryEvalCache;
	}
	
	//	関数名:		ImportEvalCache
	//
	//	概要:		EVALキャッシュをインポートする
	//
	//	引数:		$aryEvalCache	EVALキャッシュ配列
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function ImportEvalCache($aryEvalCache){
		// EVALキャッシュ配列をインポートする
		$this->aryEvalCache = $aryEvalCache;
		
		return TRUE;
	}
	
	//	関数名:		GetEvalCache
	//
	//	概要:		EVALキャッシュからオブジェクトを取得する
	//
	//	引数:		$strScript	スクリプト
	//				&$objEvalClass	クラスオブジェクト(戻り値)
	//
	//	戻り値:		TRUE:	取得成功
	//				FALSE:	取得失敗(エラーではないので呼び出し元でオブジェクトを生成する)
	//
	//	エラー:		返す
	//
	function GetEvalCache($strScript, &$objEvalClass){
		// スクリプトのダイジェストを取得する
		$strScriptDigest = $this->GetDigest($strScript);
		
		if(isset($this->aryEvalCache[$strScriptDigest]['OBJECT']) == FALSE){
			// 存在するはずのオブジェクトのキャッシュが存在しない
			$this->strErrorMessage = 'No eval cache exist';
			return FALSE;
		}
		
		// EVALキャッシュからオブジェクトを取得する
		$objEvalClass = $this->aryEvalCache[$strScriptDigest]['OBJECT'];
		
		return TRUE;
	}
	
	//	関数名:		SetEvalCache
	//
	//	概要:		EVALキャッシュからオブジェクトをセットする
	//
	//	引数:		$strScript	スクリプト
	//				&$objEvalClass	クラスオブジェクト
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function SetEvalCache($strScript, &$objEvalClass){
		// EVALキャッシュモードが有効ならばキャッシュする
		if($this->bytEvalCacheMode == CLISTOUTPUT_EVAL_CACHE_ON){
			// スクリプトのダイジェストを取得する
			$strScriptDigest = $this->GetDigest($strScript);
			
			// クラスのスクリプトをセットする
			$this->aryEvalCache[$strScriptDigest]['SCRIPT'] = $strScript;
			
			// クラスのオブジェクトをセットする
			$this->aryEvalCache[$strScriptDigest]['OBJECT'] = $objEvalClass;
		}
		
		return TRUE;
	}

	//	関数名:		SetReplaceMode
	//
	//	概要:		置き換えリスト動作定義をセットする
	//
	//	引数:		$bytReplaceMode	置き換えリスト動作定義
	//					CLISTOUTPUT_REPLACE_ALL	置き換えリストで設定ファイル全体を置き換える
	//					CLISTOUTPUT_REPLACE_SQL	置き換えリストで設定ファイルのSQL部分のみ置き換える
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function SetReplaceMode($bytReplaceMode){
		// 置き換えリストの動作定義をセット
		$this->bytReplaceMode = $bytReplaceMode;
		
		return TRUE;
	}

	//	関数名:		SetEvalMode
	//
	//	概要:		EVALの実行モードをセットする
	//
	//	引数:		$bytEvalMode	EVALの実行モード
	//					CLISTOUTPUT_EVAL_LINE	行毎にeval()を実行する
	//					CLISTOUTPUT_EVAL_CLASS	クラスを作成してeval()を一度だけ実行する
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function SetEvalMode($bytEvalMode){
		// EVALの実行モードをセット
		$this->bytEvalMode = $bytEvalMode;
		
		return TRUE;
	}

	//	関数名:		SetEvalCacheMode
	//
	//	概要:		EVALのキャッシュ動作モードをセットする
	//
	//	引数:		$bytEvalCacheMode	EVALの実行モード
	//					CLISTOUTPUT_EVAL_CACHE_OFF	キャッシュを利用しない
	//					CLISTOUTPUT_EVAL_CACHE_ON	キャッシュを利用する
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function SetEvalCacheMode($bytEvalCacheMode){
		// EVALの実行モードをセット
		$this->bytEvalCacheMode = $bytEvalCacheMode;
		
		// EVALキャッシュを初期化する
		$this->ClearEvalCache();
		
		return TRUE;
	}

	//	関数名:		SetBindEvalCacheMode
	//
	//	概要:		EVALキャッシュのまとめ生成モードをセットする
	//
	//	引数:		$bytBindEvalCacheMode	EVALのまとめ生成モード
	//					CLISTOUTPUT_BIND_EVAL_CACHE_OFF	まとめ生成を利用しない
	//					CLISTOUTPUT_BIND_EVAL_CACHE_ON	まとめ生成を利用する
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function SetBindEvalCacheMode($bytBindEvalCacheMode){
		// EVALキャッシュのまとめ生成モードをセット
		$this->bytBindEvalCacheMode = $bytBindEvalCacheMode;
		
		return TRUE;
	}

	//	関数名:		LoadConfig
	//
	//	概要:		設定テキストからデータ展開
	//
	//	引数:		$strConfig		読み込む設定テキスト
	//
	//	戻り値:		TRUE:	正常終了
	//				FALSE:	異常終了
	//
	//	エラー:		返す
	//
	function LoadConfig($strConfig){
		// 設定ファイルに対する初期置き換え動作
		switch($this->bytReplaceMode){
			case CLISTOUTPUT_REPLACE_ALL:
				// CLISTOUTPUT_REPLACE_ALL が指定されていたら設定テキスト全体を置き換える
				$strConfig = $this->ReplaceStrings($this->aryColumnList, $this->aryReplaceList, $strConfig);
				break;
		}
		
		// 設定データを消す
		$this->DeleteConfig();
		
		// <CR><LF>, <CR>, <LF> いずれかで区切る
		$aryConfigLine = split("\x0D\x0A|\x0A|\x0A", $strConfig);
		
		// デフォルトの処理タイプ・処理名の設定
		$strNowType = 'DEFAULT';
		$strNowName = 'DEFAULT';
		
		// 全ての行を取得する
		reset($aryConfigLine);
		while(list($strKey, $strValue) = each($aryConfigLine)){
			if(preg_match('/^[ \n\r\v\f]*$/', $strValue)){
				// タブの入っていない空行は無視
				continue;
			}
			
			// 行の取得
			list($strType, $strHandle) = split("\t", $strValue, 2);
			switch($strType){
				case CLISTOUTPUT_COMMENT:
					// コメント
					break;
				case CLISTOUTPUT_TYPE:
					// 処理タイプの設定
					$strNowType = $strHandle;
					break;
				case CLISTOUTPUT_DATANAME:
					// データ処理名の設定
					$strNowName = $strHandle;
					// 処理順番用のインデックスの作成
					if(isset($this->aryConfig['INDEX'][$strNowName]) == FALSE){
						$this->aryConfig['INDEX'][$strNowName] = 1;
					}
					break;
				case CLISTOUTPUT_REFERENCE:
					// データ参照
					// 値が入っていたら改行で区切る
					if(isset($this->aryConfig[$strNowType][$strNowName])){
						$this->aryConfig[$strNowType][$strNowName] .= "\n";
					}
					// 参照している値を取出す
					$this->aryConfig[$strNowType][$strNowName] .= $this->aryConfig[$strNowType][$strHandle];
					break;
				case CLISTOUTPUT_FILE:
					// ファイル参照
					// 指定ファイルを読み込む
					if(!$this->LoadFile($this->strConfigDir . trim($strHandle), $strSubConfig)){
						// エラー
						// LoadConfigFile()がエラーを返すのでここではエラーは定義しない
						return FALSE;
					}
					// 設定ファイルに対する初期置き換え動作
					switch($this->bytReplaceMode){
						case CLISTOUTPUT_REPLACE_ALL:
							// 設定ファイルのキーワード置き換え
							$strSubConfig = $this->ReplaceStrings($this->aryColumnList, $this->aryReplaceList, $strSubConfig);
							break;
					}
					
					// 値が入っていたら改行で区切る
					if(isset($this->aryConfig[$strNowType][$strNowName])){
						$this->aryConfig[$strNowType][$strNowName] .= "\n";
					}
					// ファイル内の値をセットする
					$this->aryConfig[$strNowType][$strNowName] .= $strSubConfig;
					break;
				case CLISTOUTPUT_DATA:
					// データのセット
					// 値が入っていたら改行で区切る
					if(isset($this->aryConfig[$strNowType][$strNowName])){
						$this->aryConfig[$strNowType][$strNowName] .= "\n";
					}
					$this->aryConfig[$strNowType][$strNowName] .= $strHandle;
					break;
				default:
					break;
			}
		}
		
		// EVALキャッシュのまとめ生成を行う
		if($this->CreateBindEvalCache() == FALSE){
			// CreateBindEvalCache()のエラーメッセージをそのまま利用する
			return FALSE;
		}
		
		return TRUE;
	}

	//	関数名:		LoadConfigFile
	//
	//	概要:		設定ファイルからデータ展開
	//
	//	引数:		$strCodeEncoding	読み込む設定テキスト
	//
	//	戻り値:		TRUE:	正常終了
	//				FALSE:	異常終了
	//
	//	エラー:		返す
	//
	function LoadConfigFile($strCodeEncoding = CLISTOUTPUT_HANDLINGENCODING){
		if(!$this->LoadFile($this->strConfigDir . $this->strConfigFile, $strConfig, $strCodeEncoding)){
			// エラー
			// LoadConfigFile()がエラーを返すのでここではエラーは定義しない
			return FALSE;
		}
		
		// 読み込んだ内容からデータ展開
		if(!$this->LoadConfig($strConfig)){
			// LoadConfig()のエラーメッセージをそのまま使用する
			return FALSE;
		}
		
		return TRUE;
	}

	//	関数名:		DeleteConfig
	//
	//	概要:		設定データを初期化
	//
	//	引数:		なし
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function DeleteConfig(){
		// 設定データを初期化
		$this->aryConfig = array();
		
		return TRUE;
	}

	//	関数名:		SetConfigDir
	//
	//	概要:		設定ファイルのディレクトリをセットする
	//
	//	引数:		$strDir		ディレクトリパス
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function SetConfigDir($strDir){
		$this->strConfigDir = $strDir;
		
		return TRUE;
	}
	
	//	関数名:		SetTemplateDir
	//
	//	概要:		テンプレートのディレクトリをセットする
	//
	//	引数:		$strDir		ディレクトリパス
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function SetTemplateDir($strDir){
		$this->strTemplateDir = $strDir;
		
		return TRUE;
	}
	
	//	関数名:		SetMaxToken
	//
	//	概要:		使用可能な最大トークン数を設定する
	//
	//	引数:		$lngMaxToken		最大トークン
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function SetMaxToken($lngMaxToken){
		$this->lngMaxToken = $lngMaxToken;
		
		return TRUE;
	}
	
	//	関数名:		CreateChildObject
	//
	//	概要:		子オブジェクトを作る
	//
	//	引数:		なし
	//
	//	戻り値:		作成した子オブジェクト
	//
	//	エラー:		返さない
	//
	function CreateChildObject(){
		// 子オブジェクトを作る
		$objChildObject = $this;
		
		// 実行結果の初期化
		$objChildObject->ClearExecute();
		
		// エラーメッセージの初期化
		$objChildObject->ClearErrorMessage();
		
		// 設定データの初期化
		$objChildObject->ClearConfig();
		
		// 置き換えリストの初期化
		$objChildObject->ClearReplaceList();

		return $objChildObject;
	}
	
	
	
	//	関数名:		ListExecute
	//
	//	概要:		SQL・テンプレート置き換え処理を実行
	//
	//	引数:		&$objDatabase		データベース操作オブジェクト
	//				&$strPage			出力結果(戻り値)
	//				$bytInitializePageContextFlag	ページコンテキストの初期化を行ってから実行する(Default: TRUE)
	//
	//	戻り値:		TRUE:	正常終了
	//				FALSE:	異常終了
	//
	//	エラー:		返す
	//
	function ListExecute(&$objDatabase, &$strPage, $bytInitializePageContextFlag = TRUE){
		// 前回の実行結果のクリア
		$this->ClearExecute();
		if($bytInitializePageContextFlag == TRUE) {
			// ページコンテキストの初期化
			$this->objContext->initializePageContext();
		}
		
		// 返す値の初期化
		$strPage = '';
		
		// 配列ではない、または count() が 0 ならばエラー
		if(!is_array($this->aryConfig) or count($this->aryConfig) == 0){
			// 設定が存在しないエラー
			$this->strErrorMessage = 'No config data exist';
			return FALSE;
		}
		
		// テンプレートの読み込み
		reset($this->aryConfig['INDEX']);
		while(list($strTempName) = each($this->aryConfig['INDEX'])){
			// 先頭の処理名の取り出し
			$aryName[] = $strName = $strTempName;
			
			if(empty($this->aryConfig['SQL'][$strName])){
				// テンプレートはあるが、SQL処理が未定義なので処理なし
				continue;
			}
			
			// 変数の初期化
			unset($aryTemplate);
			unset($intFinishLine);
			unset($intLine);
			while(1){
				// キャッシュの使用制御
				$bolUseCache = ($this->aryConfig['CACHE'][$strName] == 1) ? TRUE : FALSE;
				
				// ページ表示に使用するテンプレートの読み込み
				if(empty($this->aryConfig['TEMPLATESTRING'][$strName]) == FALSE){
					// 設定ファイルに直接かかれた文字列をテンプレートとする
					$aryTemplate[] = empty($this->aryConfig['TEMPLATESTRING'][$strName]) ? '' : $this->aryConfig['TEMPLATESTRING'][$strName];
				}
				elseif(empty($this->aryConfig['RESULTTEMPLATE'][$strName]) == FALSE){
					// 実行結果をテンプレートとして利用する
					$aryTemplate[] = empty($this->aryResult[$this->aryConfig['RESULTTEMPLATE'][$strName]]) ? '' : $this->aryResult[$this->aryConfig['RESULTTEMPLATE'][$strName]];
				}
				elseif(!$this->LoadFileWithCacheControl($this->strTemplateDir . trim($this->aryConfig['TEMPLATE'][$strName]), $aryTemplate[], $bolUseCache)){
					// テンプレート読み込みエラー
					// LoadFileWithCacheControl()のエラーメッセージを使用する
					return FALSE;
				}
				
				// 最小のOFFSETの取り出し
				if(empty($this->aryConfig['OFFSET'][$strName])){
					// OFFSET未定義
					$intLine = 0;
				}
				else{
					// OFFSETが定義されている
					// $intLine が定義されていないまたは今回のOFFSETが$intLineより小さい場合はOFFSETをセットする
					if((isset($intLine) == FALSE) or ($intLine > $this->aryConfig['OFFSET'][$strName])){
						// OFFSETのセット
						$intLine = $this->aryConfig['OFFSET'][$strName];
					}
				}
				
				// 最大取り出し行数制限の設定
				if(empty($this->aryConfig['LIMIT'][$strName])){
					// 取り出し行数制限なし
					$intFinishLine = 0;
				}
				else{
					// 取り出し行数制限あり
					// 今回のオフセットを取得
					$intThisOffset = (isset($this->aryConfig['OFFSET'][$strName]) == TRUE) ? $this->aryConfig['OFFSET'][$strName] : 0;
					if((isset($intFinishLine) == FALSE) or ($intFinishLine != 0 and $intFinishLine < ($intThisOffset + $this->aryConfig['LIMIT'][$strName]))){
						$intFinishLine = $intThisOffset + $this->aryConfig['LIMIT'][$strName];
					}
				}
				
				// パラレルSQLの検出
				if($this->aryConfig['PARALLEL'][$strName] == 1){
					if((list($strTempName) = each($this->aryConfig['INDEX'])) == FALSE){
						// パラレルSQLのはずが次のブロックが存在しない
						$this->strErrorMessage = 'Invalid parallel SQL : ' . $strName;
						return FALSE;
					}
					
					// 次の処理名をセット
					$aryName[] = $strName = $strTempName;
				}
				else{
					// パラレルSQLは存在しないのでテンプレート読み込み終了
					break;
				}
			}
			
			// パラレルSQLの先頭の処理名を取出す
			$strName = $aryName[0];
			
			// 設定ファイルに対する初期置き換え動作
			switch($this->bytReplaceMode){
				case CLISTOUTPUT_REPLACE_ALL:
				case CLISTOUTPUT_REPLACE_SQL:
				default:
					// SQLのキーワード置き換え
					$strSQL = $this->ReplaceStrings($this->aryColumnList, $this->aryReplaceList, $this->aryConfig['SQL'][$strName]);
					break;
			}
			
			// SQLのキーワード置き換え（上のブロックでの実行結果を取得する）
			if(is_array($this->aryColumnResult) and is_array($this->aryResult)){
				$strSQL = $this->ReplaceStrings($this->aryColumnResult, $this->aryResult, $strSQL);
			}
			
			// SQLの実行
			$strResultID = $objDatabase->Execute($strSQL);
			if($strResultID == FALSE){
				// SQL実行エラー
				$this->strErrorMessage = 'Invalid SQL : ' . $strSQL;
				return FALSE;
			}
			// 実行コンテキストを初期化する
			$this->objContext->initializeExecuteContext();
			
			// 行ごとに取り出し
			unset($aryReturnValue);
			unset($arySQLResult);
			unset($aryCount);
			unset($aryNoRepeat);
			unset($aryNoRepeatColumn);
			
			// NOREPEATが指定されている場合はテンプレートを用意しておく
			reset($aryTemplate);
			while(list($intKey) = each($aryTemplate)){
				if(trim($this->aryConfig['NOREPEAT'][$aryName[$intKey]]) == 1 or
						(empty($this->aryConfig['NOREPEAT'][$aryName[$intKey]]) != TRUE and isset($this->aryConfig['NOREPEATVALUE'][$aryName[$intKey]]))) {
					$aryReturnValue[$intKey][0] = $aryTemplate[$intKey];
				}
			}
			
			// EVALのクラス生成が指示されていたらクラスを作成することができる
			// ただし、CheckClassToken()でクラス用のトークンが残っているかチェックする必要がある
			if($this->bytEvalMode == CLISTOUTPUT_EVAL_CLASS and $this->CheckClassToken() == TRUE){
				// EVALが定義されていたら任意のPHPソースを元にクラスを生成する
				if(strlen(trim($this->aryConfig['EVAL'][$strName])) > 0){
					// EVALのクラスオブジェクトを作成する
					if($this->GetClassObject($this->aryConfig['EVAL'][$strName], $objEvalClass) == FALSE){
						// エラー発生
						// GetClassObject()のエラーメッセージをそのまま使用する
						// 結果IDを閉じる
						$objDatabase->FreeResult($strResultID);
						return FALSE;
					}
				}
			}
			
			while($objDatabase->SafeFetch($strResultID, $arySQLResult, $intLine)){
				// 行カウントのインクリメント
				$intLine++;
				
				// LIMITによる終了判定
				if($intFinishLine != 0 and $intFinishLine < $intLine){
					// LIMITの値に達したので終了
					break;
				}
				
				// 行数取得の命令
				if(empty($this->aryConfig['ROWNUM'][$strName]) == FALSE){
					// 行番号取得
					$arySQLResult[$this->aryConfig['ROWNUM'][$strName]] = $intLine;
				}
				
				// EVALが定義されていたら任意のPHPソースを実行する
				if(isset($objEvalClass) == TRUE){
					if($objEvalClass->ExtendSQLResult($arySQLResult, $this->objContext) == FALSE){
						// evalで生成された関数内でエラー発生
						$this->strErrorMessage = 'Eval script returns false : ' . $this->aryConfig['EVAL'][$strName];
						// 結果IDを閉じる
						$objDatabase->FreeResult($strResultID);
						return FALSE;
					}
				}
				elseif(strlen(trim($this->aryConfig['EVAL'][$strName])) > 0){
					// 使用済トークンをチェックする
					if($this->lngCurrentToken >= $this->lngMaxToken){
						// トークンの使いすぎエラー
						$this->strErrorMessage = 'Reach max token cache : ' . $this->lngMaxToken;
						// 結果IDを閉じる
						$objDatabase->FreeResult($strResultID);
						return FALSE;
					}
					
					// エラー終了フラグを立てる
					$bolErrorExit = TRUE;
					eval($this->aryConfig['EVAL'][$strName] . "\n" . '$bolErrorExit = FALSE;');
					// 使用済トークンをインクリメント
					$this->lngCurrentToken++;
					
					if($bolErrorExit == TRUE){
						// eval内でエラー発生
						$this->strErrorMessage = 'Invalid script in eval : ' . $this->aryConfig['EVAL'][$strName];
						// 結果IDを閉じる
						$objDatabase->FreeResult($strResultID);
						return FALSE;
					}
				}
				
				// ENCODEPREFIX が定義されていたらすべての結果に対してHTMLのエンコードを行う
				if(strlen(trim($this->aryConfig['ENCODEPREFIX'][$strName])) > 0){
					// OVERWRITEとなっていたら項目を上書きする(スピード向上のためローカル変数に代入)
					$strEncodePrefix = (strcmp($this->aryConfig['ENCODEPREFIX'][$strName], 'OVERWRITE') == 0) ? '' : $this->aryConfig['ENCODEPREFIX'][$strName];
					
					reset($arySQLResult);
					while(list($strSQLResultKey) = each($arySQLResult)){
						if(strlen($strEncodePrefix) > 0 and strcmp(substr($strSQLResultKey, 0, strlen($strEncodePrefix)), $strEncodePrefix) == 0){
							// すでにエンコード済みのものは飛ばす
							continue;
						}
						// 配列ではなかったら処理をする
						if(is_array($arySQLResult[$strSQLResultKey]) == FALSE){
							$arySQLResult[$strEncodePrefix . $strSQLResultKey] = htmlspecialchars($arySQLResult[$strSQLResultKey], ENT_QUOTES);
						}
					}
				}
				
				// CHILDOBJECT が指定されていたら上の結果から子オブジェクトを作る
				if(strlen(trim($this->aryConfig['CHILDOBJECT'][$strName])) > 0){
					// 指定された設定ファイルから子オブジェクトを作る
					$objChildObject = $this->CreateChildObject();
					
					// コンテキストを引き上げる(ParentsExecute -> ChildPage, ParentsPage -> ChildSession)
					$objChildObject->objContext->raise();

					// 子オブジェクトにSQLの結果を置き換えリストとしてインポートする
					
					// CHILDOBJECTIMPORTREPLACELISTが指定されていた場合はimportする(優先順位はどうしよう・・・)
					if (trim($this->aryConfig['CHILDOBJECTIMPORTREPLACELIST'][$strName]) == 1) {
						$objChildObject->ImportReplaceList($this->array_merge($this->aryReplaceList, $arySQLResult));
					} else {
						// それ以外はいつもどおり
						$objChildObject->ImportReplaceList($arySQLResult);
					}
					// 子オブジェクトに設定ファイルを読み込む
					if($objChildObject->LoadConfig(trim($this->aryConfig['CHILDOBJECT'][$strName])) == FALSE){
						// 子オブジェクトのエラーをもとに生成する
						$this->strErrorMessage = 'Child object returns : ' . $objChildObject->GetErrorMessage();
						// 結果IDを閉じる
						$objDatabase->FreeResult($strResultID);
						return FALSE;
					}
					
					// 子オブジェクトを実行する
					if($objChildObject->ListExecute($objDatabase, $strChildPage, FALSE) == FALSE){
						// 子オブジェクトのエラーをもとに生成する
						$this->strErrorMessage = 'Child object returns : ' . $objChildObject->GetErrorMessage();
						// 結果IDを閉じる
						$objDatabase->FreeResult($strResultID);
						return FALSE;
					}
					
					// 結果を読み込む
					$objChildObject->GetResult($aryChildResult);
					$arySQLResult = $this->array_merge($arySQLResult, $aryChildResult);
					
					// 使用済みトークン数を取得
					$this->lngCurrentToken = $objChildObject->GetCurrentToken();
					
					// 子オブジェクトのEVALキャッシュを取得して適用する
					$this->ImportEvalCache($objChildObject->ExportEvalCache());

					// phpのバージョンが4以下の場合はコンテキストをコピーする
					if (phpversion() < 4) {
						// コンテキストをコピーする
						$this->objContext = $objChildObject->objContext;
					}
					// コンテキストを引き下げる(ChildSession -> ParentsPage, ChildPage ->ParentsExecute)
					$this->objContext->lower();
					
					// 子オブジェクトを削除する
					unset($objChildObject);
				}
				
				// 置き換え用カラムリストを取得する
				$arySQLColumn = $this->GetKeyArray($arySQLResult, '/_%', '%_/');
				
				// キーワード置き換えを行う
				reset($aryTemplate);
				while(list($intKey) = each($aryTemplate)){
					$lngLimit = $this->aryConfig['OFFSET'][$aryName[$intKey]] + $this->aryConfig['LIMIT'][$aryName[$intKey]];
					
					// LIMITによる終了判定
					if($this->aryConfig['LIMIT'][$aryName[$intKey]] != '' and $lngLimit < $intLine){
						// LIMITの値に達したので終了
						continue;
					}
					
					// OFFSETによる判定
					if($this->aryConfig['OFFSET'][$aryName[$intKey]] >= $intLine){
						// OFFSET以下の値なので次へ
						continue;
					}
					
					// NOREPEAT == 1 が指定されていたらテンプレートを繰り返さず同じテンプレートに適用
					if(trim($this->aryConfig['NOREPEAT'][$aryName[$intKey]]) == 1){
						// 配列の要素ごとに置き換えする（同じテンプレートに適用する）
						$aryReturnValue[$intKey][0] = $this->ReplaceStrings($arySQLColumn, $arySQLResult, $aryReturnValue[$intKey][0]);
					}
					elseif(empty($this->aryConfig['NOREPEAT'][$aryName[$intKey]]) != TRUE and isset($this->aryConfig['NOREPEATVALUE'][$aryName[$intKey]])) {
						for($intNoRepeatIndex = ''; TRUE; ++$intNoRepeatIndex) {
							if(empty($this->aryConfig['NOREPEAT' . $intNoRepeatIndex][$aryName[$intKey]]) == TRUE or isset($this->aryConfig['NOREPEATVALUE' . $intNoRepeatIndex][$aryName[$intKey]]) != TRUE) {
								break;
							}
							$aryNoRepeat[$intKey][$this->ReplaceStrings($arySQLColumn, $arySQLResult, $this->aryConfig['NOREPEAT' . $intNoRepeatIndex][$aryName[$intKey]])] = $this->ReplaceStrings($arySQLColumn, $arySQLResult, $this->aryConfig['NOREPEATVALUE' . $intNoRepeatIndex][$aryName[$intKey]]);
						}
						// NOREPEATLIMIT が定義されている場合はそのLIMIT毎に置き換えを行う(省メモリ)
						if(isset($this->aryConfig['NOREPEATLIMIT'][$aryName[$intKey]]) and $intLine % $this->aryConfig['NOREPEATLIMIT'][$aryName[$intKey]] == 0) {
							$aryNoRepeatColumn = $this->GetKeyArray($aryNoRepeat[$intKey], '/_%', '%_/');
							$aryReturnValue[$intKey][0] = $this->ReplaceStrings($aryNoRepeatColumn, $aryNoRepeat[$intKey], $aryReturnValue[$intKey][0]);
							unset($aryNoRepeat[$intKey]);
						}
					}
					else{
						if(count($aryReturnValue[$intKey]) > 0){
							// すでに値が入っていたらセパレータを入れる
							$aryReturnValue[$intKey][] = $this->aryConfig['SEPARATOR'][$aryName[$intKey]];
						}
						// 配列の要素ごとに置き換えする
						$aryReturnValue[$intKey][] = $this->ReplaceStrings($arySQLColumn, $arySQLResult, $aryTemplate[$intKey]);
					}
					
					// カウントをインクリメント
					$aryCount[$intKey]++;
				}
				
				// SQL実行結果の消去
				unset($arySQLResult);
			}
			
			// EVAL用に作成したクラスオブジェクトを削除する
			unset($objEvalClass);
			
			// カウントが 0 の処理単位に対してから NORECORD の値を適用する
			reset($aryTemplate);
			while(list($intKey) = each($aryTemplate)){
				// NOREPEATが指定されていた場合はレコードの有無にかかわらないので除外する
				if($aryCount[$intKey] <= 0
						and ($this->aryConfig['NOREPEAT'][$aryName[$intKey]] != 1 and (empty($this->aryConfig['NOREPEAT'][$aryName[$intKey]]) == TRUE or isset($this->aryConfig['NOREPEATVALUE'][$aryName[$intKey]]) != TRUE))){
					$aryReturnValue[$intKey][0] = empty($this->aryConfig['NORECORD'][$aryName[$intKey]]) ? '' : $this->aryConfig['NORECORD'][$aryName[$intKey]];
				}
				// $intKey に対して $aryNoRepeat が存在したらNOREPEATで置き換える文字列が配列内にたまっている。
				if(isset($aryNoRepeat[$intKey])) {
					$aryNoRepeatColumn = $this->GetKeyArray($aryNoRepeat[$intKey], '/_%', '%_/');
					$aryReturnValue[$intKey][0] = $this->ReplaceStrings($aryNoRepeatColumn, $aryNoRepeat[$intKey], $aryReturnValue[$intKey][0]);
				}
			}
			
			
			// 結果IDを閉じる
			$objDatabase->FreeResult($strResultID);
			
			if(is_array($aryReturnValue) == TRUE){
				reset($aryReturnValue);
				
				while(list($intKey) = each($aryReturnValue)){
					$strReturnValue = join('', $aryReturnValue[$intKey]);

					if(is_array($this->aryColumnResult) and is_array($this->aryResult)){
						$strReturnValue = $this->ReplaceStrings($this->aryColumnResult, $this->aryResult, $strReturnValue);
					}
					
					// 返す値の構築
					$strPage = $strReturnValue;
					
					// データ登録
					$this->aryResult[$aryName[$intKey]] = $strReturnValue;
					
					// カウント数の取得
					if(empty($this->aryConfig['COUNT'][$aryName[$intKey]]) == FALSE){
						$this->aryResult[$this->aryConfig['COUNT'][$aryName[$intKey]]] = isset($aryCount[$intKey]) ? $aryCount[$intKey] : 0;
					}
					
					// データのカラム配列の再構築
					$this->aryColumnResult = $this->GetKeyArray($this->aryResult, '/_%', '%_/');
				}
			}
			else{
				reset($aryName);
				while(list($intKey, $strName) = each($aryName)){
					if(isset($this->aryConfig['COUNT'][$aryName[$intKey]]) == TRUE){
						// データ登録
						if(empty($this->aryResult[$this->aryConfig['COUNT'][$aryName[$intKey]]]) == TRUE){
							$this->aryResult[$this->aryConfig['COUNT'][$aryName[$intKey]]] = 0;
						}
						
						// データのカラム配列の再構築
						$this->aryColumnResult = $this->GetKeyArray($this->aryResult, '/_%', '%_/');
					}
				}
				$strPage = '';
			}
			
			reset($aryName);
			while(list($intKey, $strName) = each($aryName)){
				// データ登録
				if(isset($this->aryResult[$strName]) == FALSE){
					$this->aryResult[$strName] = '';
				}
				
				// データのカラム配列の再構築
				$this->aryColumnResult = $this->GetKeyArray($this->aryResult, '/_%', '%_/');
			}
			
			unset($aryName);
		}
		
		// 処理に成功
		return TRUE;
	}
	
	//	関数名:		GetResult
	//
	//	概要:		ListExecute(FileOutputExecute)を実行した後に各実行結果データ処理名ごとに取得する
	//
	//	引数:		&$aryResult		戻り値配列
	//
	//	注意事項:	ListExecute(FileOutputExecute)を行った直後に行わないと正しく取れない可能性があります。
	//
	//	戻り値:		TRUE:	常に
	//
	function GetResult(&$aryResult){
		// 戻り値に代入
		$aryResult = $this->aryResult;
		
		return TRUE;
	}
	
	//	関数名:		FileOutputExecute
	//
	//	概要:		SQL・テンプレート置き換え処理を実行してファイルに保存
	//
	//	引数:		&$objDatabase		データベース操作オブジェクト
	//
	//	戻り値:		TRUE:	正常終了
	//				FALSE:	異常終了
	//
	//	エラー:		返す
	//
	function FileOutputExecute(&$objDatabase){
		// まずは処理実行
		if(!$this->ListExecute($objDatabase, $strPage)){
			// エラー発生
			// ListExecute()のエラーメッセージをそのまま使用する
			return FALSE;
		}
		
		if(!$this->WriteFile($this->strOutputDir . $this->strOutputFile, $strPage)){
			// 書き込みエラー
			// WriteFile()のエラーメッセージをそのまま使用する
			return FALSE;
		}
		
		return TRUE;
	}
	
	//	関数名:		DeleteAnyConfig
	//
	//	概要:		任意の設定を削除する
	//
	//	引数:		$strProcessType		処理タイプ
	//				$strProcessName		データ処理名
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function DeleteAnyConfig($strProcessType, $strProcessName){
		unset($this->aryConfig[$strProcessType][$strProcessName]);
		
		return TRUE;
	}
	
	//	関数名:		DeleteFetchLimit
	//
	//	概要:		処理名に対応するSQLで出力するレコード数制限を削除する
	//
	//	引数:		$strProcessName		データ処理名
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function DeleteFetchLimit($strProcessName){
		$this->DeleteAnyConfig('LIMIT', $strProcessName);
		
		return TRUE;
	}
	
	//	関数名:		DeleteFetchOffset
	//
	//	概要:		処理名に対応するSQLで出力するレコードのオフセットを削除する
	//
	//	引数:		$strProcessName		データ処理名
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function DeleteFetchOffset($strProcessName){
		$this->DeleteAnyConfig('OFFSET', $strProcessName);
		
		return TRUE;
	}
	
	//	関数名:		SetAnyConfig
	//
	//	概要:		任意の設定を設定する
	//
	//	引数:		$strProcessType		処理タイプ
	//				$strProcessName		データ処理名
	//				$strSettingValue	設定する値
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function SetAnyConfig($strProcessType, $strProcessName, $strSettingValue){
		$this->aryConfig[$strProcessType][$strProcessName] = $strSettingValue;
		
		return TRUE;
	}
	
	//	関数名:		SetFetchLimit
	//
	//	概要:		処理名に対応するSQLで出力するレコード数制限を設定する
	//
	//	引数:		$strProcessName		データ処理名
	//				$intFetchLimit		出力制限数
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function SetFetchLimit($strProcessName, $intFetchLimit){
		$this->SetAnyConfig('LIMIT', $strProcessName, $intFetchLimit);
		
		return TRUE;
	}
	
	//	関数名:		SetFetchOffset
	//
	//	概要:		処理名に対応するSQLで出力するレコードのオフセットを設定する
	//
	//	引数:		$strProcessName		データ処理名
	//				$intFetchOffset		出力オフセット
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function SetFetchOffset($strProcessName, $intFetchOffset){
		$this->SetAnyConfig('OFFSET', $strProcessName, $intFetchOffset);
		
		return TRUE;
	}
	
	//	関数名:		SetOutputFile
	//
	//	概要:		出力ファイルをセット
	//
	//	引数:		なし
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function SetOutputFile($strFileName){
		$this->strOutputFile = $strFileName;
		
		return TRUE;
	}
	
	//	関数名:		SetConfigFile
	//
	//	概要:		設定ファイルをセット
	//
	//	引数:		$strFileName	設定ファイル名
	//
	//	戻り値:		TRUE:	正常終了
	//				FALSE:	異常終了
	//
	//	エラー:		返さない
	//
	function SetConfigFile($strFileName){
		if($this->strConfigFile == $strFileName){
			// 設定ファイル名が同じなので読み込まない
			return TRUE;
		}
		
		$this->strConfigFile = $strFileName;
		
		// 設定ファイルを展開
		if(!$this->LoadConfigFile()){
			// LoadConfigFile()のエラーメッセージをそのまま使用する
			return FALSE;
		}
		
		return TRUE;
	}
	
	//	関数名:		GetKeyArray
	//
	//	概要:		配列のキー名のみ抽出した配列を取得
	//
	//	引数:		$aryOriginal		基となる配列
	//				$strPrefix			キー名に付加する接頭語
	//				$strSuffix			キー名に付加する接尾語
	//
	//	戻り値:		キー名の配列
	//
	//	エラー:		返さない
	//
	function GetKeyArray(&$aryOriginal, $strPrefix = '', $strSuffix = ''){
		// 戻り値の初期化
		$aryResult = array();

		reset($aryOriginal);
		while(list($strKey, $strValue) = each($aryOriginal)){
			$aryResult[] = $strPrefix . $strKey . $strSuffix;
			$aryNew[$strKey] = $strValue;
		}
		// 信頼性を増すために配列の再構築
		$aryOriginal = $aryNew;
		return $aryResult;
	}
	
	//	関数名:		LoadFileWithCacheControl
	//
	//	概要:		ファイルを読み込む(フラグでキャッシュを使うか制御する)
	//
	//	引数:		$strFileName			ファイル名
	//				&$strFileValue			読み込んだファイルの内容
	//				$bolCacheControlFlag	キャッシュの制御(TRUE: ON, FALSE: OFF)
	//				$strCodeEncoding		テンプレートソース文字コード
	//
	//	戻り値:		TRUE:	正常終了
	//				FALSE:	異常終了
	//
	//	エラー:		返す
	//
	function LoadFileWithCacheControl($strFileName, &$strFileValue, $bolCacheControlFlag = FALSE, $strCodeEncoding = CLISTOUTPUT_HANDLINGENCODING){
		
		if($bolCacheControlFlag == TRUE){
			// キャッシュ使用
			if($this->LoadFileUseCache($strFileName, $strFileValue, $strCodeEncoding) == FALSE){
				// LoadFileUseCache()のエラーメッセージをそのまま使用する
				return FALSE;
			}
		}
		else{
			// キャッシュ使用せず
			if($this->LoadFile($strFileName, $strFileValue, $strCodeEncoding) == FALSE){
				// LoadFile()のエラーメッセージをそのまま使用する
				return FALSE;
			}
		}
		
		// 正常終了
		return TRUE;
	}
	
	//	関数名:		LoadFileUseCache
	//
	//	概要:		可能ならキャッシュを用いてファイルを読み込む
	//
	//	引数:		$strFileName		ファイル名
	//				&$strFileValue		読み込んだファイルの内容
	//				$strCodeEncoding	テンプレートソース文字コード
	//
	//	戻り値:		TRUE:	正常終了
	//				FALSE:	異常終了
	//
	//	エラー:		返す
	//
	function LoadFileUseCache($strFileName, &$strFileValue, $strCodeEncoding = CLISTOUTPUT_HANDLINGENCODING){
		// キャッシュが存在するかチェックする
		if(empty($this->aryFileCache[$strFileName][$strCodeEncoding]) == FALSE){
			// キャッシュにヒット！
			
			// キャッシュから読み込み
			$strFileValue = $this->aryFileCache[$strFileName][$strCodeEncoding];
			
			// 正常終了
			return TRUE;
		}
		
		// キャッシュにヒットしなかったためにファイルから読み込む
		if($this->LoadFile($strFileName, $strFileValue, $strCodeEncoding) == FALSE){
			// LoadFile()のエラーメッセージをそのまま使用する
			return FALSE;
		}
		
		// キャッシュに保存
		$this->aryFileCache[$strFileName][$strCodeEncoding] = $strFileValue;
		
		// 正常終了
		return TRUE;
	}
	
	//	関数名:		LoadFile
	//
	//	概要:		ファイルを読み込む
	//
	//	引数:		$strFileName		ファイル名
	//				&$strFileValue		読み込んだファイルの内容
	//				$strCodeEncoding	テンプレートソース文字コード
	//
	//	戻り値:		TRUE:	正常終了
	//				FALSE:	異常終了
	//
	//	エラー:		返す
	//
	function LoadFile($strFileName, &$strFileValue, $strCodeEncoding = CLISTOUTPUT_HANDLINGENCODING){
		// ファイルの存在チェック
		if(!file_exists($strFileName)){
			// ファイル存在せず
			$this->strErrorMessage = 'No such file or directory : ' . $strFileName;
			return FALSE;
		}
		// 読みとりでオープン
		$fp = fopen($strFileName, 'rb');
		if($fp == FALSE){
			// オープンエラー
			$this->strErrorMessage = 'File open failed : ' . $strFileName;
			return FALSE;
		}
		
		// ファイルサイズのチェック
		$intFileSize = filesize($strFileName);
		if($intFileSize == FALSE){
			// ファイルサイズが0または取得エラー
			$this->strErrorMessage = 'Invalid file size : ' . $strFileName;
			return FALSE;
		}

		// 読み込み
		$strFileValue = fread($fp, $intFileSize);

		// ファイルを閉じる
		fclose($fp);
		
		// 内部コードへ文字コード変換
		$strFileValue = i18n_convert($strFileValue, i18n_internal_encoding(), $strCodeEncoding);

		return TRUE;
	}
	
	//	関数名:		WriteFile
	//
	//	概要:		ファイルを書き込みVerifyを行う
	//
	//	引数:		$strFileName		ファイル名
	//				$strFaileValue		書き込むファイルの内容
	//				$strWriteEncoding	書き込む文字コード
	//
	//	戻り値:		TRUE:	正常終了
	//				FALSE:	異常終了
	//
	//	エラー:		返す
	//
	function WriteFile($strFileName, $strFileValue, $strWriteEncoding = CLISTOUTPUT_HANDLINGENCODING){
		// ファイルの存在チェック
		if(file_exists($strFileName)){
			// すでにファイル存在するエラー
			$this->strErrorMessage = 'Already exist : ' . $strFileName;
			return FALSE;
		}
		
		// 書き込みでファイルオープン
		$fp = fopen($strFileName, 'wb');
		if($fp == FALSE){
			// オープンエラー
			$this->strErrorMessage = 'Permission denied : ' . $strFileName;
			return FALSE;
		}
		// ファイルロック
		if(!flock($fp, 2)){
			// ファイルロックエラー
			$this->strErrorMessage = 'File lock failed : ' . $strFileName;
			return FALSE;
		}
		
		// 内部コードから文字コード変換
		$strTempFileValue = i18n_convert($strFileValue, $strWriteEncoding, i18n_internal_encoding());
		
		// 書き込み
		if(!fputs($fp, $strTempFileValue)){
			// 書き込みエラー
			$this->strErrorMessage = 'Write file failed : ' . $strFileName;
			return FALSE;
		}
		
		// ファイルを閉じる
		fclose($fp);
		
		switch(CLISTOUTPUT_WRITE_VERIFY_LEVEL){
			case 0:
				// チェックなし
				break;
			case 1:
				// ファイルサイズチェック
				$intFileSize = filesize($strFileName);
				if($intFileSize == FALSE){
					// ファイルサイズが0または取得エラー
					$this->strErrorMessage = 'Verify file size failed : ' . $strFileName;
					return FALSE;
				}
				
				if(strlen($strFileValue) != $intFileSize){
					// ファイルサイズが一致しないエラー
					$this->strErrorMessage = 'Verify file size unconformable : ' . $strFileName;
					return FALSE;
				}
				break;
			case 2:
			default:
				// ファイル内容チェック
				// ファイル読み込み
				if(!$this->LoadFile($strFileName, $strVerifyFileValue, $strWriteEncoding)){
					// 読み込みエラー
					// LoadFile()のエラーメッセージを使用する
					return FALSE;
				}
				
				// 内容が一致するか確認する
				if($strVerifyFileValue != $strFileValue){
					// 一致しないエラー
					$this->strErrorMessage = 'Verify file value unconformable : ' . $strFileName;
					return FALSE;
				}
				break;
		}
		
		// 書き込み処理及びすべてのチェック項目クリア
		return TRUE;
	}
	
	//	関数名:		SetReplaceList
	//
	//	概要:		テンプレート置き換えリストにキーをセットする
	//
	//	引数:		$strKeyName		セットするキー(すでにある場合は上書き)
	//				$strValue		セットする値
	//
	//	戻り値:		TRUE:	常に
	//
	//	エラー:		返さない
	//
	function SetReplaceList($strKeyName, $strValue){
		// 値をセット
		$this->aryReplaceList[$strKeyName] = $strValue;
		
		// カラムの配列を再構築
		$this->aryColumnList = $this->GetKeyArray($this->aryReplaceList, '/_%', '%_/');
		
		return TRUE;
	}
	
	//	関数名:		ImportReplaceList
	//
	//	概要:		テンプレート置き換えリストにインポートする(既存のリストはすべて消える)
	//
	//	引数:		$strKeyName		セットするキー(すでにある場合は上書き)
	//				$strValue		セットする値
	//
	//	戻り値:		TRUE:	正常終了
	//				FALSE:	異常終了
	//
	//	エラー:		返す
	//
	function ImportReplaceList($aryImportList){
		if(!is_array($aryImportList)){
			// 配列でないエラー
			$this->strErrorMessage = 'Invalid import list argument';
			return FALSE;
		}
		
		// 値をインポート
		$this->aryReplaceList = $aryImportList;
		
		// カラムの配列を再構築
		$this->aryColumnList = $this->GetKeyArray($this->aryReplaceList, '/_%', '%_/');
		
		return TRUE;
	}
	
	//	関数名:		ReplaceStrings
	//
	//	概要:		preg_replaceの \0 出力に対応したラッパー
	//
	//	引数:		$aryPattern			置き換え前配列(stringでもOK)
	//				$aryReplacement		置き換え後配列(stringでもOK)
	//				$strSubject			置き換え対象
	//
	//	戻り値:		置き換え後文字列
	//
	//	エラー:		返さない
	//
	function ReplaceStrings($aryPattern, $aryReplacement, $strSubject){
		// 一時的に \ の後に 0x01 を入れる
		$aryReplacement = preg_replace('/' . '\x5C' . '/' , "\\0" . "\x01", $aryReplacement);
		
		// 一時的に挿入した 0x01 をすべて削除する
		return str_replace("\x01", '', preg_replace($aryPattern, $aryReplacement, $strSubject));
	}
	
	//	関数名:		CheckToken
	//
	//	概要:		トークン数がオーバーしていないかチェックする。
	//				(この関数は lngAlertToken を元に判断する)
	//
	//	引数:		なし
	//
	//	戻り値:		TRUE:	オーバーしていない
	//				FALSE:	オーバーしている
	//
	//	エラー:		返さない
	//
	function CheckToken(){
		if($this->lngAlertToken >= ($this->lngMaxToken - $this->lngCurrentToken)){
			// 残りトークンが警告トークン以下になっている
			return FALSE;
		}
		
		return TRUE;
	}

	//	関数名:		CheckClassToken
	//
	//	概要:		クラス用のトークン数がオーバーしていないかチェックする。
	//				(この関数は lngMaxToken を元に判断する)
	//
	//	引数:		なし
	//
	//	戻り値:		TRUE:	オーバーしていない
	//				FALSE:	オーバーしている
	//
	//	エラー:		返さない
	//
	function CheckClassToken(){
		if($this->lngCurrentToken >= ((int) $this->lngMaxToken / 2)){
			// eval()でクラスを定義する場合は最大トークン数の半分以上が空いている必要がある。
			return FALSE;
		}
		
		return TRUE;
	}

	//	関数名:		CreateUniqueClassName
	//
	//	概要:		ユニークなクラス名を生成する
	//
	//	引数:		なし
	//
	//	戻り値:		ユニークなクラス名
	//
	//	エラー:		返さない
	//
	function CreateUniqueClassName(){
		// uniqid()で一意なクラス名を生成する
		return 'CLISTOUTPUT_' . uniqid('');
	}

	//	関数名:		CreateBindEvalCache
	//
	//	概要:		EVALキャッシュをまとめて生成する(トークンの節約のため)
	//
	//	引数:		なし
	//
	//	戻り値:		TRUE:	正常終了
	//				FALSE:	異常終了
	//
	//	エラー:		返す
	//
	function CreateBindEvalCache(){
		// スクリプト配列を初期化する
		$aryScript = array();
		
		// 1. EVALキャッシュモードがキャッシュ有効(CLISTOUTPUT_EVAL_CACHE_ON)になっていること
		// 2. EVALのまとめ生成モードが有効(CLISTOUTPUT_BIND_EVAL_CACHE_ON)になっていること
		// 3. EVALの実行モードがクラス生成(CLISTOUTPUT_EVAL_CLASS)になっていること
		// 以上の2つのいずれかが FALSE ならばEVALキャッシュのまとめ生成を利用しない
		if($this->bytEvalCacheMode == CLISTOUTPUT_EVAL_CACHE_ON and $this->bytBindEvalCacheMode == CLISTOUTPUT_BIND_EVAL_CACHE_ON and $this->bytEvalMode == CLISTOUTPUT_EVAL_CLASS){
			
			if(is_array($this->aryConfig['EVAL']) == TRUE){
				reset($this->aryConfig['EVAL']);
				while(list($strProcessName) = each($this->aryConfig['EVAL'])){
					// EVALキャッシュが存在しない場合のみキャッシュを生成する(CheckEvalCache()がFALSEを返す)
					if($this->CheckEvalCache($this->aryConfig['EVAL'][$strProcessName]) == FALSE){
						// キャッシュが存在しないので生成リストに入れる
						$aryScript[] = $this->aryConfig['EVAL'][$strProcessName];
					}
				}
			}
		}
		
		// 対象スクリプトが存在する場合はキャッシュを生成する
		if(count($aryScript) >= 1){
			// クラスオブジェクトの生成
			if($this->CreateClassObject($aryScript, $objEvalClass) == FALSE){
				// CreateClassObject()のエラーメッセージを使用する
				return FALSE;
			}
			
			// 対象スクリプトの数を取得
			$intMaxLoopIndex = count($aryScript);
			for($intLoopIndex = 0; $intLoopIndex < $intMaxLoopIndex; ++$intLoopIndex){
				// EVALキャッシュをセットする
				$this->SetEvalCache($aryScript[$intLoopIndex], $objEvalClass[$intLoopIndex]);
			}
		}
		
		return TRUE;
	}

	//	関数名:		CreateClassObject
	//
	//	概要:		クラス定義のスクリプトを元にオブジェクトを生成する
	//
	//	引数:		$aryScript		スクリプトの配列(文字列にも対応)
	//				&$objEvalClass	クラスオブジェクトの配列(戻り値)
	//
	//	戻り値:		TRUE:	正常終了
	//				FALSE:	異常終了
	//
	//	エラー:		返す
	//
	function CreateClassObject($aryScript, &$objEvalClass){
		// クラス用のトークンをチェックする
		if($this->CheckClassToken() == FALSE){
			// クラス用のトークンの使いすぎエラー
			$this->strErrorMessage = 'Reach max token cache for class : ' . (int) ($this->lngMaxToken / 2);
			return FALSE;
		}
		
		// 配列でなければ配列化する
		if(is_array($aryScript) == FALSE){
			// 文字列で渡されたフラグを立てる
			$bolStringFlag = TRUE;
			// 配列化
			$aryScript = array($aryScript);
		}
		
		// 対象スクリプトの数を取得
		$intMaxLoopIndex = count($aryScript);
		
		// クラススクリプトを初期化する
		$strClassScript = '';
		
		for($intLoopIndex = 0; $intLoopIndex < $intMaxLoopIndex; ++$intLoopIndex){
			// 使い捨てのクラス名を取得する
			$strClassName = $this->CreateUniqueClassName();
			
			// クラス定義を生成する
			$strClassScript .= 'class ' . $strClassName . "{\n";
			$strClassScript .= 'function ExtendSQLResult(&$arySQLResult, &$objContext)' . "{\n";
			$strClassScript .= $aryScript[$intLoopIndex] . "\n";
			$strClassScript .= "return TRUE;\n";
			$strClassScript .= "}\n";
			$strClassScript .= "}\n";
			if($bolStringFlag == TRUE){
				// 文字列で渡されているので文字列で返す
				$strClassScript .= '$objEvalClass = new ' . $strClassName . ";\n";
			}
			else{
				// 配列で渡されたので配列で返す
				$strClassScript .= '$objEvalClass[' . $intLoopIndex . '] = new ' . $strClassName . ";\n";
			}
		}
		
		// エラー終了フラグを立てる
		$bolErrorExit = TRUE;
		eval($strClassScript . "\n" . '$bolErrorExit = FALSE;');
		// 使用済トークンをインクリメント
		$this->lngCurrentToken++;
		
		if($bolErrorExit == TRUE){
			// eval内でエラー発生
			$this->strErrorMessage = 'Invalid script in eval : ' . join("\n", $aryScript);
			return FALSE;
		}
		
		return TRUE;
	}

	//	関数名:		GetClassObject
	//
	//	概要:		クラス定義のスクリプトを元にオブジェクトを生成する(キャッシュを利用)
	//
	//	引数:		$strScript	スクリプト
	//				&$objEvalClass	クラスオブジェクト(戻り値)
	//
	//	戻り値:		TRUE:	正常終了
	//				FALSE:	異常終了
	//
	//	エラー:		返す
	//
	function GetClassObject($strScript, &$objEvalClass){
		// 1. EVALキャッシュモードがキャッシュ有効(CLISTOUTPUT_EVAL_CACHE_ON)になっていること
		// 2. EVALキャッシュが存在すること(CheckEvalCache()がTRUEを返す)
		// 3. EVALキャッシュが取得できること(GetEvalCache()がTRUEを返す)
		// 以上の3つのいずれかが FALSE ならばキャッシュを利用できないので生成する
		if((($this->bytEvalCacheMode == CLISTOUTPUT_EVAL_CACHE_ON) and ($this->CheckEvalCache($strScript) == TRUE) and ($this->GetEvalCache($strScript, $objEvalClass) == TRUE)) == FALSE){
			// キャッシュは利用しない
			if($this->CreateClassObject($strScript, $objEvalClass) == FALSE){
				// クラスの作成失敗
				// CreateClassObject()のエラーメッセージをそのまま使用する
				return FALSE;
			}
			
			// EVALキャッシュをセットする
			$this->SetEvalCache($strScript, $objEvalClass);
		}
		
		// 正常終了
		return TRUE;
	}
	
	//	関数名:		CheckEvalCache
	//
	//	概要:		EVALのキャッシュが存在するかチェックする
	//
	//	引数:		$strScript	スクリプト
	//
	//	戻り値:		TRUE:	キャッシュ利用可能
	//				FALSE:	キャッシュ利用不可能
	//
	//	エラー:		返さない
	//
	function CheckEvalCache($strScript){
		// スクリプトのダイジェストを取得する
		$strScriptDigest = $this->GetDigest($strScript);
		
		// スクリプトのダイジェストをキーにした配列をチェックする
		if(isset($this->aryEvalCache[$strScriptDigest]) == FALSE){
			// キャッシュは存在しない
			return FALSE;
		}
		
		// ダイジェストは一致しているので実際の内容をチェックする
		if(strcmp($this->aryEvalCache[$strScriptDigest]['SCRIPT'], $strScript) != 0){
			// 内容が一致しない
			return FALSE;
		}
		
		// オブジェクトの存在チェック
		if(isset($this->aryEvalCache[$strScriptDigest]['OBJECT']) == FALSE){
			// オブジェクトが存在しない
			return FALSE;
		}
		
		// キャッシュ利用可能
		return TRUE;
	}

	//	関数名:		GetDigest
	//
	//	概要:		ダイジェストを生成する
	//
	//	引数:		$strTarget	ダイジェストを生成する対象
	//
	//	戻り値:		ダイジェスト
	//
	//	エラー:		返さない
	//
	function GetDigest($strTarget){
		// MD5でダイジェストを生成する
		return md5($strTarget);
	}

	//	関数名:		array_merge
	// 
	//	概要:		二つの配列をマージする
	//				PHP4の array_merge と同じ動作
	//				ただし渡せる引数は二つ
	//
	//	引数:		$Array			マージする配列
	//				$MergeArray		マージする配列
	//
	//	戻り値:		マージ済みの配列
	//
	//	エラー:		返さない
	//
	function array_merge($Array, $MergeArray){
		// 配列でない場合は配列化する
		if(!is_array($Array)){
			$Array = array($Array);
		}
		
		// $MergeArray が配列でなければ $Array をそのまま返す
		// $Array も配列ではない場合は配列化する
		if(!is_array($MergeArray)){
			return $Array;
		}
		
		reset($MergeArray);
		
		while(list($key, $value) = each($MergeArray)){
			$Array[$key] = $value;
		}
		
		return $Array;
	}
}

class CListOutputContext {
	var $aryContext;
	var $intOffset;

	//	関数名:		CListOutputContext
	//
	//	概要:		コンストラクタ
	//
	//	引数:		なし
	//				
	//	戻り値:		なし
	//
	//	エラー:		返さない
	//
	function CListOutputContext() {
		$this->initializeContext();
	}

	//	アクセサ:	private
	//
	//	関数名:		getExecuteContextIndex
	//
	//	概要:		$aryContext内の実行コンテキストのインデックスを取得します。
	//
	//	引数:		なし
	//
	//	戻り値:		実行コンテキストのインデックス
	//
	//	エラー:		返さない
	//
	function getExecuteContextIndex() {
		return $this->intOffset + 2;
	}

	//	アクセサ:	private
	//
	//	関数名:		getPageContextIndex
	//
	//	概要:		$aryContext内のページコンテキストのインデックスを取得します。
	//
	//	引数:		なし
	//
	//	戻り値:		ページコンテキストのインデックス
	//
	//	エラー:		返さない
	//
	function getPageContextIndex() {
		return $this->intOffset + 1;
	}

	//	アクセサ:	private
	//
	//	関数名:		getSessionContextIndex
	//
	//	概要:		$aryContext内のセッションコンテキストのインデックスを取得します。
	//
	//	引数:		なし
	//
	//	戻り値:		セッションコンテキストのインデックス
	//
	//	エラー:		返さない
	//
	function getSessionContextIndex() {
		return $this->intOffset;
	}

	//	関数名:		raise
	//
	//	概要:		コンテキストの引き上げ
	//
	//	引数:		なし
	//
	//	戻り値:		TRUE
	//
	//	エラー:		返さない
	//
	function raise() {
		$this->intOffset++;
		return TRUE;
	}

	//	関数名:		lower
	//
	//	概要:		コンテキストの引き下げ
	//
	//	引数:		なし
	//
	//	戻り値:		TRUE:	成功
	//				FALSE:	失敗(それ以上引き下げられない場合)
	//
	//	エラー:		返さない
	//
	function lower() {
		if ($this->intOffset <= 0) {
			return FALSE;
		}
		// 引き下げによってスコープから外れる実行コンテキストを初期化する
		$this->initializeExecuteContext();
		$this->intOffset--;
		return TRUE;
	}

	//	関数名:		initializeContext
	//
	//	概要:		コンテキストを初期化する
	//
	//	引数:		なし
	//
	//	戻り値:		TRUE
	//
	//	エラー:		返さない
	//
	function initializeContext() {
		$this->aryContext = array();
		$this->intOffset = 0;

		$this->initializeExecuteContext();
		$this->initializePageContext();
		$this->initializeSessionContext();
		return TRUE;
	}

	//	関数名:		setExecuteContext
	//
	//	概要：		実行コンテキストに値をセットする
	//
	//	引数:		$Name		変数名
	//				$Value		変数値
	//
	//	戻り値:		TRUE
	//
	//	エラー:		返さない
	//
	function setExecuteContext($Name, $Value) {
		$this->aryContext[$this->getExecuteContextIndex()][$Name] = $Value;
		return TRUE;
	}

	//	関数名:		getExecuteContext
	//
	//	概要：		実行コンテキストから値を取得する
	//
	//	引数:		$Name		変数名
	//
	//	戻り値:		変数値(存在しない場合は空文字)
	//
	//	エラー:		返さない
	//
	function getExecuteContext($Name) {
		return $this->aryContext[$this->getExecuteContextIndex()][$Name];
	}

	//	関数名:		isSetExecuteContext
	//
	//	概要:		実行コンテキストに変数が存在するかチェックする
	//
	//	引数:		$Name		変数名
	//
	//	戻り値:		TRUE: 存在する  FALSE: 存在しない
	//
	//	エラー:		返さない
	//
	function isSetExecuteContext($Name) {
		return isset($this->aryContext[$this->getExecuteContextIndex()][$Name]);
	}

	//	関数名:		clearExecuteContext
	//
	//	概要:		実行コンテキストから変数を削除する
	//
	//	引数:		$Name		変数名
	//
	//	戻り値:		TRUE
	//
	//	エラー:		返さない
	//
	function clearExecuteContext($Name) {
		unset($this->aryContext[$this->getExecuteContextIndex()][$Name]);
		return TRUE;
	}

	//	関数名:		initializeExecuteContext
	//
	//	概要:		実行コンテキストを初期化する
	//
	//	引数:		なし
	//
	//	戻り値:		TRUE
	//
	//	エラー:		返さない
	//
	function initializeExecuteContext() {
		$this->aryContext[$this->getExecuteContextIndex()] = array();
		return TRUE;
	}

	//	関数名:		setPageContext
	//
	//	概要：		ページコンテキストに値をセットする
	//
	//	引数:		$Name		変数名
	//				$Value		変数値
	//
	//	戻り値:		TRUE
	//
	//	エラー:		返さない
	//
	function setPageContext($Name, $Value) {
		$this->aryContext[$this->getPageContextIndex()][$Name] = $Value;
		return TRUE;
	}

	//	関数名:		getPageContext
	//
	//	概要：		ページコンテキストから値を取得する
	//
	//	引数:		$Name		変数名
	//
	//	戻り値:		変数値(存在しない場合は空文字)
	//
	//	エラー:		返さない
	//
	function getPageContext($Name) {
		return $this->aryContext[$this->getPageContextIndex()][$Name];
	}

	//	関数名:		isSetPageContext
	//
	//	概要:		ページコンテキストに変数が存在するかチェックする
	//
	//	引数:		$Name		変数名
	//
	//	戻り値:		TRUE: 存在する  FALSE: 存在しない
	//
	//	エラー:		返さない
	//
	function isSetPageContext($Name) {
		return isset($this->aryContext[$this->getPageContextIndex()][$Name]);
	}

	//	関数名:		clearPageContext
	//
	//	概要:		ページコンテキストから変数を削除する
	//
	//	引数:		$Name		変数名
	//
	//	戻り値:		TRUE
	//
	//	エラー:		返さない
	//
	function clearPageContext($Name) {
		unset($this->aryContext[$this->getPageContextIndex()][$Name]);
		return TRUE;
	}

	//	関数名:		initializePageContext
	//
	//	概要:		ページコンテキストを初期化する
	//
	//	引数:		なし
	//
	//	戻り値:		TRUE
	//
	//	エラー:		返さない
	//
	function initializePageContext() {
		$this->aryContext[$this->getPageContextIndex()] = array();
		return TRUE;
	}

	//	関数名:		setSessionContext
	//
	//	概要：		セッションコンテキストに値をセットする
	//
	//	引数:		$Name		変数名
	//				$Value		変数値
	//
	//	戻り値:		TRUE
	//
	//	エラー:		返さない
	//
	function setSessionContext($Name, $Value) {
		$this->aryContext[$this->getSessionContextIndex()][$Name] = $Value;
		return TRUE;
	}

	//	関数名:		getSessionContext
	//
	//	概要：		セッションコンテキストから値を取得する
	//
	//	引数:		$Name		変数名
	//
	//	戻り値:		変数値(存在しない場合は空文字)
	//
	//	エラー:		返さない
	//
	function getSessionContext($Name) {
		return $this->aryContext[$this->getSessionContextIndex()][$Name];
	}

	//	関数名:		isSetSessionContext
	//
	//	概要:		セッションコンテキストに変数が存在するかチェックする
	//
	//	引数:		$Name		変数名
	//
	//	戻り値:		TRUE: 存在する  FALSE: 存在しない
	//
	//	エラー:		返さない
	//
	function isSetSessionContext($Name) {
		return isset($this->aryContext[$this->getSessionContextIndex()][$Name]);
	}

	//	関数名:		clearSessionContext
	//
	//	概要:		セッションコンテキストから変数を削除する
	//
	//	引数:		$Name		変数名
	//
	//	戻り値:		TRUE
	//
	//	エラー:		返さない
	//
	function clearSessionContext($Name) {
		unset($this->aryContext[$this->getSessionContextIndex()][$Name]);
		return TRUE;
	}

	//	関数名:		initializeSessionContext
	//
	//	概要:		セッションコンテキストを初期化する
	//
	//	引数:		なし
	//
	//	戻り値:		TRUE
	//
	//	エラー:		返さない
	//
	function initializeSessionContext() {
		$this->aryContext[$this->getSessionContextIndex()] = array();
		return TRUE;
	}
}
?>
