<?php

require_once ('conf.inc');

// ワークシート定数定義クラス

class workSheetConst {

    private function __construct() {

    }
    
    // ワークシートセル名称 -------------------------------------------------------------------------------------------

    // 出力エリア位置決め用セル
    // 上端左端           
    const TOP_LEFT = 'top_left';
    // 上端右端
    const TOP_RIGHT = 'top_right';
    // 下端左端
    const BOTTOM_LEFT = 'bottom_left';

    // ヘッダー部
    // 製品コードタイトル
    const PRODUCT_CODE_HEADER = 'productcode_header';
    // 製品コード
    const PRODUCT_CODE = 'productcode';
    // 製品名タイトル
    const PRODUCT_NAME_HEADER = 'productname_header';
    // 製品名
    const PRODUCT_NAME = 'productname';
    // 製品名(英語)タイトル
    const PRODUCT_ENGLISH_NAME_HEADER = 'productenglishname_header';
    // 製品名(英語)
    const PRODUCT_ENGLISH_NAME = 'productenglishname';
    // 上代タイトル
    const RETAIL_PRICE_HEADER = 'retailprice_header';
    // 上代
    const RETAIL_PRICE = 'retailprice';
    // 営業部署タイトル
    const INCHARGE_GROUP_CODE_HEADER = 'inchargegroupcode_header';
    // 営業部署
    const INCHARGE_GROUP_CODE = 'inchargegroupcode';
    // 担当タイトル
    const CUSTOMER_USER_NAME_HEADER = 'customerusername_header';
    // 担当
    const CUSTOMER_USER_NAME = 'customerusername';
    // 担当開発者タイトル
    const USER_CODE_HEADER = 'usercode_header';
    // 担当開発者
    const USER_CODE = 'usercode';
    // カートン入り数タイトル
    const CARTON_QUANTITY_HEADER = 'cartonquantity_header';
    // カートン入り数
    const CARTON_QUANTITY = 'cartonquantity';
    // 償却数 pcsタイトル
    const PRODUCTION_QUANTITY_HEADER = 'productionquantity_header';
    // 償却数 pcs
    const PRODUCTION_QUANTITY = 'productionquantity';
    
    // 受注明細
    // 製品売上
    // 売上分類
    const RECEIVE_PRODUCT_SALES_DIVISION_CODE ='receive_p_salesdivisioncode';
    // 売上区分
    const RECEIVE_PRODUCT_SALES_CLASS_CODE = 'receive_p_salesclasscode';
    // 顧客先
    const RECEIVE_PRODUCT_CUSTOMER_COMPANY_CODE = 'receive_p_customercompanycode';
    // 数量
    const RECEIVE_PRODUCT_PRODUCT_QUANTITY = 'receive_p_productquantity';
    // 通貨
    const RECEIVE_PRODUCT_MONETARY_UNIT_CODE = 'receive_p_monetaryunitcode';
    // 通貨コード
    const RECEIVE_PRODUCT_MONETARY_RATE_CODE = 'receive_p_rate_code';
    // 単価
    const RECEIVE_PRODUCT_PRODUCT_PRICE = 'receive_p_productprice';
    // 適用レート
    const RECEIVE_PRODUCT_CONVERSION_RATE = 'receive_p_conversionrate';
    // 小計
    const RECEIVE_PRODUCT_SUBTOTAL_PRICE = 'receive_p_subtotalprice';
    // 納期
    const RECEIVE_PRODUCT_DELIVERY_DATE = 'receive_p_deliverydate';
    // 備考
    const RECEIVE_PRODUCT_NOTE = 'receive_p_note';
    // 製品売上合計タイトル
    const RECEIVE_PRODUCT_TOTAL_PRICE_HEADER = 'receive_p_totalprice_header';
    // 製品売上合計数
    const RECEIVE_PRODUCT_TOTAL_QUANTITY = 'receive_p_totalquantity';
    // 製品売上合計
    const RECEIVE_PRODUCT_TOTAL_PRICE = 'receive_p_totalprice';

    // 固定費売上
    // 売上分類
    const RECEIVE_FIXED_COST_SALES_DIVISION_CODE ='receive_f_salesdivisioncode';
    // 売上区分
    const RECEIVE_FIXED_COST_SALES_CLASS_CODE = 'receive_f_salesclasscode';
    // 顧客先
    const RECEIVE_FIXED_COST_CUSTOMER_COMPANY_CODE = 'receive_f_customercompanycode';
    // 数量
    const RECEIVE_FIXED_COST_PRODUCT_QUANTITY = 'receive_f_productquantity';
    // 通貨
    const RECEIVE_FIXED_COST_MONETARY_UNIT_CODE = 'receive_f_monetaryunitcode';
    // 通貨コード
    const RECEIVE_FIXED_COST_MONETARY_RATE_CODE = 'receive_f_rate_code';
    // 単価
    const RECEIVE_FIXED_COST_PRODUCT_PRICE = 'receive_f_productprice';
    // 適用レート
    const RECEIVE_FIXED_COST_CONVERSION_RATE = 'receive_f_conversionrate';
    // 小計
    const RECEIVE_FIXED_COST_SUBTOTAL_PRICE = 'receive_f_subtotalprice';
    // 納期
    const RECEIVE_FIXED_COST_DELIVERY_DATE = 'receive_f_deliverydate';
    // 備考
    const RECEIVE_FIXED_COST_NOTE = 'receive_f_note';
    // 固定費売上合計タイトル
    const RECEIVE_FIXED_COST_TOTAL_PRICE_HEADER = 'receive_f_totalprice_header';
    // 固定費売上合計数
    const RECEIVE_FIXED_COST_TOTAL_QUANTITY = 'receive_f_totalquantity';
    // 固定費売上合計
    const RECEIVE_FIXED_COST_TOTAL_PRICE = 'receive_f_totalprice';

    // 発注明細
    // 固定費
    // 仕入科目
    const ORDER_FIXED_COST_STOCK_SUBJECT_CODE = 'order_f_stocksubjectcode';
    // 仕入部品
    const ORDER_FIXED_COST_STOCK_ITEM_CODE =  'order_f_stockitemcode';
    // 仕入先
    const ORDER_FIXED_COST_CUSTOMER_COMPANY_CODE = 'order_f_customercompanycode';
    // 償却
    const ORDER_FIXED_COST_PAYOFF_TARGET_FLAG = 'order_f_payofftargetflag';
    // 計画個数
    const ORDER_FIXRD_COST_PRODUCT_QUANTITY = 'order_f_productquantity';
    // 通貨
    const ORDER_FIXED_COST_MONETARY_UNIT_CODE = 'order_f_monetaryunitcode';
    // 通貨コード
    const ORDER_FIXED_COST_MONETARY_RATE_CODE = 'order_f_rate_code';
    // 単価
    const ORDER_FIXED_COST_PRODUCT_PRICE = 'order_f_productprice';
    // 適用ﾚｰﾄ
    const ORDER_FIXED_COST_CONVERSION_RATE = 'order_f_conversionrate';
    // 計画原価
    const ORDER_FIXED_COST_SUBTOTAL_PRICE = 'order_f_subtotalprice';
    // 納期
    const ORDER_FIXED_COST_DELIVERY_DATE = 'order_f_deliverydate';
    // 備考
    const ORDER_FIXED_COST_NOTE = 'order_f_note';
    // 固定費合計タイトル
    const ORDER_FIXED_COST_FIXED_COST_HEADER = 'order_f_fixedcost_header';
    // 固定費小計
    const ORDER_FIXED_COST_FIXED_COST = 'order_f_fixedcost';
    // 償却対象外合計
    const ORDER_FIXED_COST_COST_NOT_DEPRECIATION = 'order_f_cost_not_depreciation';

    // 部材費
    // 仕入科目
    const ORDER_ELEMENTS_COST_STOCK_SUBJECT_CODE = 'order_e_stocksubjectcode';
    // 仕入部品
    const ORDER_ELEMENTS_COST_STOCK_ITEM_CODE =  'order_e_stockitemcode';
    // 仕入先
    const ORDER_ELEMENTS_COST_CUSTOMER_COMPANY_CODE = 'order_e_customercompanycode';
    // 償却
    const ORDER_ELEMENTS_COST_PAYOFF_TARGET_FLAG = 'order_e_payofftargetflag';
    // 計画個数
    const ORDER_ELEMENTS_COST_PRODUCT_QUANTITY = 'order_e_productquantity';
    // 通貨
    const ORDER_ELEMENTS_COST_MONETARY_UNIT_CODE = 'order_e_monetaryunitcode';
    // 通貨コード
    const ORDER_ELEMENTS_COST_MONETARY_RATE_CODE = 'order_e_rate_code';
    // 単価
    const ORDER_ELEMENTS_COST_PRODUCT_PRICE = 'order_e_productprice';
    // 適用ﾚｰﾄ
    const ORDER_ELEMENTS_COST_CONVERSION_RATE = 'order_e_conversionrate';
    // 計画原価
    const ORDER_ELEMENTS_COST_SUBTOTAL_PRICE = 'order_e_subtotalprice';
    // 納期
    const ORDER_ELEMENTS_COST_DELIVERY_DATE = 'order_e_deliverydate';
    // 備考
    const ORDER_ELEMENTS_COST_NOTE = 'order_e_note';
    // 明細リスト終了
    const LIST_END = 'list_end';

    // 計算結果
    // 製品売上高タイトル
    const PRODUCT_TOTAL_PRICE_HEADER = 'product_totalprice_header';
    // 製品売上高
    const PRODUCT_TOTAL_PRICE = 'product_totalprice';
    // 固定費売上高タイトル
    const FIXED_COST_TOTAL_PRICE_HEADER = 'fixedcost_totalprice_header';
    // 固定費売上高
    const FIXED_COST_TOTAL_PRICE = 'fixedcost_totalprice';
    // 総売上高タイトル
    const SALES_AMOUNT_HEADER = 'salesamount_header';
    // 総売上高
    const SALES_AMOUNT = 'salesamount';
    // 製品利益タイトル
    const PRODUCT_PROFIT_HEADER = 'product_profit_header';
    // 製品利益
    const PRODUCT_PROFIT = 'product_profit';
    // 製品利益率
    const PRODUCT_PROFIT_RATE = 'product_profit_rate';
    // 固定費利益タイトル
    const FIXED_COST_PROFIT_HEADER = 'fixedcost_profit_header';
    // 固定費利益
    const FIXED_COST_PROFIT = 'fixedcost_profit';
    // 固定費利益率
    const FIXED_COST_PROFIT_RATE = 'fixedcost_profit_rate';
    // 売上総利益タイトル
    const PROFIT_HEADER = 'profit_header';
    // 売上総利益
    const PROFIT = 'profit';
    // 利益率
    const PROFIT_RATE = 'profit_rate';
    // 間接製造経費タイトル
    const INDIRECT_COST_HEADER = 'indirect_cost_header';
    // 間接製造経費
    const INDIRECT_COST = 'indirect_cost';
    // 標準割合
    const STANDARD_RATE = 'standard_rate';
    // 営業利益タイトル
    const OPERATING_PROFIT_HEADER = 'operating_profit_header';
    // 営業利益
    const OPERATING_PROFIT = 'operating_profit';
    // 営業利益率
    const OPERATING_PROFIT_RATE = 'operating_profit_rate';
    // 部材費タイトル
    const MEMBER_COST_HEADER = 'membercost_header';
    // 部材費対象数
    const MEMBER_QUANTITY = 'member_quantity';
    // pcs部材費用
    const MEMBER_UNIT_COST = 'member_unit_cost';
    // 部材費合計
    const MEMBER_COST = 'membercost';
    // 償却費タイトル
    const DEPRECIATION_COST_HEADER = 'depreciation_cost_header';
    // 償却対象数
    const DEPRECIATION_QUANTITY = 'depreciation_quantity';
    // pcs償却費用
    const DEPRECIATION_UNIT_COST = 'depreciation_unit_cost';
    // 償却費合計
    const DEPRECIATION_COST = 'depreciation_cost';
    // 製造費用タイトル
    const MANUFACTURING_COST_HEADER = 'manufacturingcost_header';
    // 製造対象数
    const MANUFACTURING_QUANTITY = 'manufacturing_quantity';
    // pcsコスト
    const MANUFACTURING_UNIT_COST = 'manufacturing_unit_cost';
    // 製造費用合計
    const MANUFACTURING_COST = 'manufacturingcost';
    // 償却対象外固定費タイトル
    const COST_NOT_DEPRECIATION_HEADER = 'cost_not_depreciation_header';
    // 償却対象外固定費
    const COST_NOT_DEPRECIATION = 'cost_not_depreciation';

    // 欄外に設定されたセルリスト（行に紐付くデータ除く）
    // 関税計算用セル
    const CALCULATION_TARIFF = 'calculation_tariff';
    // 償却選択肢：○
    const HIDDEN_PAYOFF_CIRCLE = 'hdn_payoff_circle';
    // 本荷参照セル
    const HIDDEN_MAIN_PRODUCT = 'hdn_main_product';
    
    // ----------------------------------------------------------------------------------------------------


    // 明細欄のセル名称接頭辞
    // 売上(受注明細)
    const PREFIX_RECEIVE = 'receive';
    // 仕入(発注明細)
    const PREFIX_ORDER = 'order';
    // 製品(製品売上)
    const PREFIX_PRODUCT = 'p';
    // 固定費(固定費売上、固定費)
    const PREFIX_FIXED_COST = 'f';
    // 部材費(部材費)
    const PREFIX_ELEMENTS_COST = 'e';


    // 対象エリア関連の定数 -------------------------------------------------------------------------------------------

    // 対象エリア名
    const TARGET_AREA_NAME = [
        DEF_AREA_PRODUCT_SALES => '製品売上',
        DEF_AREA_FIXED_COST_SALES => '固定費売上',
        DEF_AREA_FIXED_COST_ORDER => '固定費',
        DEF_AREA_PARTS_COST_ORDER => '部材費'
    ];

    // 対象エリアの表示用名称リスト（エラーメッセージ表示用)
    const TARGET_AREA_DISPLAY_NAME_LIST = [
        DEF_AREA_PRODUCT_SALES => '製品受注明細部',
        DEF_AREA_FIXED_COST_SALES => '固定費受注明細部',
        DEF_AREA_FIXED_COST_ORDER => '固定費/償却対象外発注明細部',
        DEF_AREA_PARTS_COST_ORDER => '部材発注明細部'
    ];

    // 対象エリアで使用する顧客先、仕入先の分類
    const ORDER_ATTRIBUTE_FOR_TARGET_AREA = [
        // 顧客先を使用するエリア
        DEF_ATTRIBUTE_CLIENT => array(
            DEF_AREA_PRODUCT_SALES => true,
            DEF_AREA_FIXED_COST_SALES => true
        ),
        // 仕入先を使用するエリア
        DEF_ATTRIBUTE_SUPPLIER => array(
            DEF_AREA_FIXED_COST_ORDER => true,
            DEF_AREA_PARTS_COST_ORDER => true
        )
    ];


    // 名称リスト（項目別の括り) -------------------------------------------------------------------------------------------

    // ヘッダーセル名称リスト
    const DETAIL_HEADER_CELL_NAME_LIST = [
        DEF_AREA_PRODUCT_SALES => self::PRODUCT_SALES_HEADER_NAME_LIST,
        DEF_AREA_FIXED_COST_SALES => self::RECEIVE_FIXED_COST_HEADER_CELL,
        DEF_AREA_FIXED_COST_ORDER => self::RECEIVE_FIXED_COST_HEADER_CELL,
        DEF_AREA_PARTS_COST_ORDER => self::ORDER_ELEMENT_COST_HEADER_CELL
    ];

    // 製品売上ヘッダー名称リスト
    const PRODUCT_SALES_HEADER_NAME_LIST = [
        'divisionSubject' => self::RECEIVE_PRODUCT_SALES_DIVISION_CODE,
        'classItem' => self::RECEIVE_PRODUCT_SALES_CLASS_CODE,
        'customerCompany' => self::RECEIVE_PRODUCT_CUSTOMER_COMPANY_CODE,
        'quantity' => self::RECEIVE_PRODUCT_PRODUCT_QUANTITY,
        'monetaryDisplay' => self::RECEIVE_PRODUCT_MONETARY_UNIT_CODE,
        'monetary' => self::RECEIVE_PRODUCT_MONETARY_RATE_CODE,
        'price' => self::RECEIVE_PRODUCT_PRODUCT_PRICE,
        'conversionRate' => self::RECEIVE_PRODUCT_CONVERSION_RATE,
        'subtotal' => self::RECEIVE_PRODUCT_SUBTOTAL_PRICE,
        'delivery' => self::RECEIVE_PRODUCT_DELIVERY_DATE,
        'note' => self::RECEIVE_PRODUCT_NOTE
    ];

    // 製品売上計算結果名称リスト
    const PRODUCT_SALES_RESULT_NAME_LIST = [
        'total_header' => self::RECEIVE_PRODUCT_TOTAL_PRICE_HEADER,
        'total_quantity' => self::RECEIVE_PRODUCT_TOTAL_QUANTITY,
        'total_price' => self::RECEIVE_PRODUCT_TOTAL_PRICE
    ];

    // 固定費売上ヘッダーセル名称リスト
    const RECEIVE_FIXED_COST_HEADER_CELL = [
        'divisionSubject' => self::RECEIVE_FIXED_COST_SALES_DIVISION_CODE,
        'classItem' => self::RECEIVE_FIXED_COST_SALES_CLASS_CODE,
        'customerCompany' => self::RECEIVE_FIXED_COST_CUSTOMER_COMPANY_CODE,
        'quantity' => self::RECEIVE_FIXED_COST_PRODUCT_QUANTITY,
        'monetaryDisplay' => self::RECEIVE_FIXED_COST_MONETARY_UNIT_CODE,
        'monetary' => self::RECEIVE_FIXED_COST_MONETARY_RATE_CODE,
        'price' => self::RECEIVE_FIXED_COST_PRODUCT_PRICE,
        'conversionRate' => self::RECEIVE_FIXED_COST_CONVERSION_RATE,
        'subtotal' => self::RECEIVE_FIXED_COST_SUBTOTAL_PRICE,
        'delivery' => self::RECEIVE_FIXED_COST_DELIVERY_DATE,
        'note' => self::RECEIVE_FIXED_COST_NOTE
    ];

    // 固定費売上計算結果セル名称リスト
    const RECEIVE_FIXED_COST_RESULT_CELL = [
        'total_header' => self::RECEIVE_FIXED_COST_TOTAL_PRICE_HEADER,
        'total_quantity' => self::RECEIVE_FIXED_COST_TOTAL_QUANTITY,
        'total_price' => self::RECEIVE_FIXED_COST_TOTAL_PRICE
    ];
    
    // 固定費ヘッダーセル名称リスト
    const ORDER_FIXED_COST_HEADER_CELL = [
        'divisionSubject' => self::ORDER_FIXED_COST_STOCK_SUBJECT_CODE,
        'classItem' => self::ORDER_FIXED_COST_STOCK_ITEM_CODE,
        'customerCompany' => self::ORDER_FIXED_COST_CUSTOMER_COMPANY_CODE,
        'payoff' => self::ORDER_FIXED_COST_PAYOFF_TARGET_FLAG,
        'quantity' => self::ORDER_FIXRD_COST_PRODUCT_QUANTITY,
        'monetaryDisplay' => self::ORDER_FIXED_COST_MONETARY_UNIT_CODE,
        'monetary' => self::ORDER_FIXED_COST_MONETARY_RATE_CODE,
        'price' => self::ORDER_FIXED_COST_PRODUCT_PRICE,
        'conversionRate' => self::ORDER_FIXED_COST_CONVERSION_RATE,
        'subtotal' => self::ORDER_FIXED_COST_SUBTOTAL_PRICE,
        'delivery' => self::ORDER_FIXED_COST_DELIVERY_DATE,
        'note' => self::ORDER_FIXED_COST_NOTE
    ];

    // 固定費計算結果セル名称リスト
    const ORDER_FIXED_COST_RESULT_CELL = [
        'total_header' => self::ORDER_FIXED_COST_FIXED_COST_HEADER,
        'fixed_cost_total' => self::ORDER_FIXED_COST_FIXED_COST,
        'not_depreciation' => self::ORDER_FIXED_COST_COST_NOT_DEPRECIATION
    ];

    // 部材費ヘッダーセル名称リスト
    const ORDER_ELEMENT_COST_HEADER_CELL = [
        'divisionSubject' => self::ORDER_ELEMENTS_COST_STOCK_SUBJECT_CODE,
        'classItem' => self::ORDER_ELEMENTS_COST_STOCK_ITEM_CODE,
        'customerCompany' => self::ORDER_ELEMENTS_COST_CUSTOMER_COMPANY_CODE,
        'payoff' => self::ORDER_ELEMENTS_COST_PAYOFF_TARGET_FLAG,
        'quantity' => self::ORDER_ELEMENTS_COST_PRODUCT_QUANTITY,
        'monetaryDisplay' => self::ORDER_ELEMENTS_COST_MONETARY_UNIT_CODE,
        'monetary' => self::ORDER_ELEMENTS_COST_MONETARY_RATE_CODE,
        'conversionRate' => self::ORDER_ELEMENTS_COST_CONVERSION_RATE,
        'subtotal' => self::ORDER_ELEMENTS_COST_SUBTOTAL_PRICE,
        'price' => self::ORDER_ELEMENTS_COST_PRODUCT_PRICE,
        'delivery' => self::ORDER_ELEMENTS_COST_DELIVERY_DATE,
        'note' => self::ORDER_ELEMENTS_COST_NOTE
    ];

    // 部材費フッターセル名称リスト
    const ORDER_ELEMENT_COST_RESULT_CELL = [
        'list' => self::LIST_END
    ];

    // ワークシートヘッダー入力項目セル名称
    const WORK_SHEET_HEADER_DATA_CELL = [
        'productCode' => self::PRODUCT_CODE,
        'productName' => self::PRODUCT_NAME,
        'productEnglishName' => self::PRODUCT_ENGLISH_NAME,
        'retailPrice' =>self::RETAIL_PRICE,
        'inchargeGroupCode' => self::INCHARGE_GROUP_CODE,
        'customerUserName' => self::CUSTOMER_USER_NAME,
        'userCode' => self::USER_CODE,
        'cartonQuantity' => self::CARTON_QUANTITY,
        'productionQuantity' => self::PRODUCTION_QUANTITY
    ];

    // ワークシートヘッダー項目タイトルセル名称
    const WORK_SHEET_HEADER_TITLE_CELL = [
        'productCode' => self::PRODUCT_CODE_HEADER,
        'productName' => self::PRODUCT_NAME_HEADER,
        'productEnglishName' => self::PRODUCT_ENGLISH_NAME_HEADER,
        'retailPrice' => self::RETAIL_PRICE_HEADER,
        'inchargeGroupCode' => self::INCHARGE_GROUP_CODE_HEADER,
        'customerUserName' => self::CUSTOMER_USER_NAME_HEADER,
        'userCode' => self::USER_CODE_HEADER,
        'cartonQuantity' => self::CARTON_QUANTITY_HEADER,
        'productionQuantity' => self::PRODUCTION_QUANTITY_HEADER
    ];

    // ワークシートヘッダーセル全体
    const WORK_SHEET_HEADER_CELL = [
        'header_data' => self::WORK_SHEET_HEADER_DATA_CELL,
        'header_title' => self::WORK_SHEET_HEADER_TITLE_CELL
    ];

    // シートの範囲決定用のセル名称
    const SET_POSITION_NAME_LIST =  [
        self::TOP_LEFT,
        self::TOP_RIGHT,
        self::BOTTOM_LEFT
    ];

    // ワークシートフッター入力項目セル名称
    const WORK_SHEET_FOOTER_DATA_CELL = [
        'productTotalPrice' => self::PRODUCT_TOTAL_PRICE,
        'fixedCostTotalPrice' => self::FIXED_COST_TOTAL_PRICE,
        'salseAmount' => self::SALES_AMOUNT,
        'productProfit' => self::PRODUCT_PROFIT,
        'productProfitRate' => self::PRODUCT_PROFIT_RATE,
        'fixedCostProfit' => self::FIXED_COST_PROFIT,
        'fixedCostProfitRate' => self::FIXED_COST_PROFIT_RATE,
        'profit' => self::PROFIT,
        'profitRate' => self::PROFIT_RATE,
        'indirectCost' => self::INDIRECT_COST,
        'standardRate' => self::STANDARD_RATE,
        'operatingProfit' => self::OPERATING_PROFIT,
        'operatingProfitRate' => self::OPERATING_PROFIT_RATE,
        'memberQuantity' => self::MEMBER_QUANTITY,
        'memberUnitCost' => self::MEMBER_UNIT_COST,
        'memberCost' => self::MEMBER_COST,
        'depreciationQuantity' => self::DEPRECIATION_QUANTITY,
        'depreciationUnitCost' => self::DEPRECIATION_UNIT_COST,
        'depreciationCost' => self::DEPRECIATION_COST,
        'manufacturingQuantity' => self::MANUFACTURING_QUANTITY,
        'manufacturingUnitCost' => self::MANUFACTURING_UNIT_COST,
        'manufacturingCost' => self::MANUFACTURING_COST,
        'costNotDepreciation' => self::COST_NOT_DEPRECIATION,
    ];

    // ワークシートフッター入力項目セル名称
    const WORK_SHEET_FOOTER_TITLE_CELL = [
        'productTotalPriceHeader' => self::PRODUCT_TOTAL_PRICE_HEADER,
        'fixedCostTotalPriceHeader' => self::FIXED_COST_TOTAL_PRICE_HEADER,
        'salesAmountHeader' => self::SALES_AMOUNT_HEADER,
        'productProfitHeader' => self::PRODUCT_PROFIT_HEADER,
        'fixedCostProfitHeader' => self::FIXED_COST_PROFIT_HEADER,
        'profitHeader' => self::PROFIT_HEADER,
        'indirectCostHeader' => self::INDIRECT_COST_HEADER,
        'operatingProfitHeader' => self::OPERATING_PROFIT_HEADER,
        'memberCostHeader' => self::MEMBER_COST_HEADER,
        'depreciationCostHeader' => self::DEPRECIATION_COST_HEADER,
        'manufacturingCostHeader' => self::MANUFACTURING_COST_HEADER,
        'costNotDepreciationHeader' => self::COST_NOT_DEPRECIATION_HEADER
    ];

    // 欄外セルの名称リスト(行ごとのデータ除く)
    const HIDDEN_NAME_LIST= [
        'calculationTariff' => self::CALCULATION_TARIFF,
        'hiddenPayoffCircle' => self::HIDDEN_PAYOFF_CIRCLE,
        'hiddenMainProduct' => self::HIDDEN_MAIN_PRODUCT
    ];


    // ワークシート全てのセル名称リスト
    const ALL_WORK_SHEET_CELL_NAME_LIST = [
        self::SET_POSITION_NAME_LIST,
        self::PRODUCT_SALES_HEADER_NAME_LIST,
        self::PRODUCT_SALES_RESULT_NAME_LIST,
        self::RECEIVE_FIXED_COST_HEADER_CELL,
        self::RECEIVE_FIXED_COST_RESULT_CELL,
        self::ORDER_FIXED_COST_HEADER_CELL,
        self::ORDER_FIXED_COST_RESULT_CELL,
        self::ORDER_ELEMENT_COST_HEADER_CELL,
        self::ORDER_ELEMENT_COST_RESULT_CELL,
        self::WORK_SHEET_HEADER_DATA_CELL,
        self::WORK_SHEET_HEADER_TITLE_CELL,
        self::WORK_SHEET_FOOTER_DATA_CELL,
        self::WORK_SHEET_FOOTER_TITLE_CELL,
        self::HIDDEN_NAME_LIST
    ];

    // 全てのセル名称を並列で使用する場合
    public static function getAllNameList() {
        $beforeNameList = self::ALL_WORK_SHEET_CELL_NAME_LIST;
        foreach ($beforeNameList as $cellNameList) {
            foreach ($cellNameList as $cellName) {
                $afterCellNameList[] = $cellName;
            }
        }
        return $afterCellNameList;
    }

    /**
    * 対象エリアのセル名称リストの取得
    * 
    * @param array   $areaCode  対象エリアのコード
    * 
    * @return array  $insertRow
    */
    public static function getCellNameOfTargetArea($areaCode) {
        switch($areaCode) {
            case DEF_AREA_PRODUCT_SALES:
                $headerList = self::PRODUCT_SALES_HEADER_NAME_LIST;
                $resultList = self::PRODUCT_SALES_RESULT_NAME_LIST;
                break;
            case DEF_AREA_FIXED_COST_SALES:
                $headerList = self::RECEIVE_FIXED_COST_HEADER_CELL;
                $resultList = self::RECEIVE_FIXED_COST_RESULT_CELL;
                break;
            case DEF_AREA_FIXED_COST_ORDER:
                $headerList = self::ORDER_FIXED_COST_HEADER_CELL;
                $resultList = self::ORDER_FIXED_COST_RESULT_CELL;
                break;
            case DEF_AREA_PARTS_COST_ORDER:
                $headerList = self::ORDER_ELEMENT_COST_HEADER_CELL;
                $resultList = self::ORDER_ELEMENT_COST_RESULT_CELL;
                break;
            default:
                break;
        }
        $cellNameListOfTarget = array(
            'headerList' => $headerList,
            'resultList' => $resultList
        );
        return $cellNameListOfTarget;
    }
    
    const TABLE_NAME_LIST = [
        'm_salesdivision' => '売上分類マスタ',
        'm_salesclass' => '売上区分マスタ',
        'm_stocksubject' => '仕入科目マスタ',
        'm_stockitem' => '仕入部品マスタ',
        'm_company' => '会社マスタ',
        'm_monetaryrate' => '通貨レートマスタ'
    ];

    // その他の定数 -------------------------------------------------------------------------------------------
    // ワークシートの行数（表示部分）
    const WORK_SHEET_COLUMN_NUMBER = 16;

    // 単価の小数点以下の桁数(キーは通貨コード)
    const PRICE_DECIMAL_DIGIT = [
        DEF_MONETARY_YEN => 2,
        DEF_MONETARY_USD => 4,
        DEF_MONETARY_HKD => 4,
    ];

}