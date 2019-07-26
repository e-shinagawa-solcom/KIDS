<?php

require_once ('conf.inc');

// ������������������饹

class workSheetConst {

    private function __construct() {

    }
    
    // ��������ȥ���̾�� -------------------------------------------------------------------------------------------

    // ���ϥ��ꥢ���ַ���ѥ���
    // ��ü��ü           
    const TOP_LEFT = 'top_left';
    // ��ü��ü
    const TOP_RIGHT = 'top_right';
    // ��ü��ü
    const BOTTOM_LEFT = 'bottom_left';

    // �إå�����
    // ���ʥ����ɥ����ȥ�
    const PRODUCT_CODE_HEADER = 'productcode_header';
    // ���ʥ�����
    const PRODUCT_CODE = 'productcode';
    // ����̾�����ȥ�
    const PRODUCT_NAME_HEADER = 'productname_header';
    // ����̾
    const PRODUCT_NAME = 'productname';
    // ����̾(�Ѹ�)�����ȥ�
    const PRODUCT_ENGLISH_NAME_HEADER = 'productenglishname_header';
    // ����̾(�Ѹ�)
    const PRODUCT_ENGLISH_NAME = 'productenglishname';
    // ���奿���ȥ�
    const RETAIL_PRICE_HEADER = 'retailprice_header';
    // ����
    const RETAIL_PRICE = 'retailprice';
    // �Ķ����𥿥��ȥ�
    const INCHARGE_GROUP_CODE_HEADER = 'inchargegroupcode_header';
    // �Ķ�����
    const INCHARGE_GROUP_CODE = 'inchargegroupcode';
    // ô�������ȥ�
    const CUSTOMER_USER_NAME_HEADER = 'customerusername_header';
    // ô��
    const CUSTOMER_USER_NAME = 'customerusername';
    // ô����ȯ�ԥ����ȥ�
    const USER_CODE_HEADER = 'usercode_header';
    // ô����ȯ��
    const USER_CODE = 'usercode';
    // �����ȥ�����������ȥ�
    const CARTON_QUANTITY_HEADER = 'cartonquantity_header';
    // �����ȥ������
    const CARTON_QUANTITY = 'cartonquantity';
    // ���ѿ� pcs�����ȥ�
    const PRODUCTION_QUANTITY_HEADER = 'productionquantity_header';
    // ���ѿ� pcs
    const PRODUCTION_QUANTITY = 'productionquantity';
    
    // ��������
    // �������
    // ���ʬ��
    const RECEIVE_PRODUCT_SALES_DIVISION_CODE ='receive_p_salesdivisioncode';
    // ����ʬ
    const RECEIVE_PRODUCT_SALES_CLASS_CODE = 'receive_p_salesclasscode';
    // �ܵ���
    const RECEIVE_PRODUCT_CUSTOMER_COMPANY_CODE = 'receive_p_customercompanycode';
    // ����
    const RECEIVE_PRODUCT_PRODUCT_QUANTITY = 'receive_p_productquantity';
    // �̲�
    const RECEIVE_PRODUCT_MONETARY_UNIT_CODE = 'receive_p_monetaryunitcode';
    // �̲ߥ�����
    const RECEIVE_PRODUCT_MONETARY_RATE_CODE = 'receive_p_rate_code';
    // ñ��
    const RECEIVE_PRODUCT_PRODUCT_PRICE = 'receive_p_productprice';
    // Ŭ�ѥ졼��
    const RECEIVE_PRODUCT_CONVERSION_RATE = 'receive_p_conversionrate';
    // ����
    const RECEIVE_PRODUCT_SUBTOTAL_PRICE = 'receive_p_subtotalprice';
    // Ǽ��
    const RECEIVE_PRODUCT_DELIVERY_DATE = 'receive_p_deliverydate';
    // ����
    const RECEIVE_PRODUCT_NOTE = 'receive_p_note';
    // ��������ץ����ȥ�
    const RECEIVE_PRODUCT_TOTAL_PRICE_HEADER = 'receive_p_totalprice_header';
    // ��������׿�
    const RECEIVE_PRODUCT_TOTAL_QUANTITY = 'receive_p_totalquantity';
    // ���������
    const RECEIVE_PRODUCT_TOTAL_PRICE = 'receive_p_totalprice';

    // ���������
    // ���ʬ��
    const RECEIVE_FIXED_COST_SALES_DIVISION_CODE ='receive_f_salesdivisioncode';
    // ����ʬ
    const RECEIVE_FIXED_COST_SALES_CLASS_CODE = 'receive_f_salesclasscode';
    // �ܵ���
    const RECEIVE_FIXED_COST_CUSTOMER_COMPANY_CODE = 'receive_f_customercompanycode';
    // ����
    const RECEIVE_FIXED_COST_PRODUCT_QUANTITY = 'receive_f_productquantity';
    // �̲�
    const RECEIVE_FIXED_COST_MONETARY_UNIT_CODE = 'receive_f_monetaryunitcode';
    // �̲ߥ�����
    const RECEIVE_FIXED_COST_MONETARY_RATE_CODE = 'receive_f_rate_code';
    // ñ��
    const RECEIVE_FIXED_COST_PRODUCT_PRICE = 'receive_f_productprice';
    // Ŭ�ѥ졼��
    const RECEIVE_FIXED_COST_CONVERSION_RATE = 'receive_f_conversionrate';
    // ����
    const RECEIVE_FIXED_COST_SUBTOTAL_PRICE = 'receive_f_subtotalprice';
    // Ǽ��
    const RECEIVE_FIXED_COST_DELIVERY_DATE = 'receive_f_deliverydate';
    // ����
    const RECEIVE_FIXED_COST_NOTE = 'receive_f_note';
    // ����������ץ����ȥ�
    const RECEIVE_FIXED_COST_TOTAL_PRICE_HEADER = 'receive_f_totalprice_header';
    // ����������׿�
    const RECEIVE_FIXED_COST_TOTAL_QUANTITY = 'receive_f_totalquantity';
    // �����������
    const RECEIVE_FIXED_COST_TOTAL_PRICE = 'receive_f_totalprice';

    // ȯ������
    // ������
    // ��������
    const ORDER_FIXED_COST_STOCK_SUBJECT_CODE = 'order_f_stocksubjectcode';
    // ��������
    const ORDER_FIXED_COST_STOCK_ITEM_CODE =  'order_f_stockitemcode';
    // ������
    const ORDER_FIXED_COST_CUSTOMER_COMPANY_CODE = 'order_f_customercompanycode';
    // ����
    const ORDER_FIXED_COST_PAYOFF_TARGET_FLAG = 'order_f_payofftargetflag';
    // �ײ�Ŀ�
    const ORDER_FIXRD_COST_PRODUCT_QUANTITY = 'order_f_productquantity';
    // �̲�
    const ORDER_FIXED_COST_MONETARY_UNIT_CODE = 'order_f_monetaryunitcode';
    // �̲ߥ�����
    const ORDER_FIXED_COST_MONETARY_RATE_CODE = 'order_f_rate_code';
    // ñ��
    const ORDER_FIXED_COST_PRODUCT_PRICE = 'order_f_productprice';
    // Ŭ�юڎ���
    const ORDER_FIXED_COST_CONVERSION_RATE = 'order_f_conversionrate';
    // �ײ踶��
    const ORDER_FIXED_COST_SUBTOTAL_PRICE = 'order_f_subtotalprice';
    // Ǽ��
    const ORDER_FIXED_COST_DELIVERY_DATE = 'order_f_deliverydate';
    // ����
    const ORDER_FIXED_COST_NOTE = 'order_f_note';
    // �������ץ����ȥ�
    const ORDER_FIXED_COST_FIXED_COST_HEADER = 'order_f_fixedcost_header';
    // �����񾮷�
    const ORDER_FIXED_COST_FIXED_COST = 'order_f_fixedcost';
    // �����оݳ����
    const ORDER_FIXED_COST_COST_NOT_DEPRECIATION = 'order_f_cost_not_depreciation';

    // ������
    // ��������
    const ORDER_ELEMENTS_COST_STOCK_SUBJECT_CODE = 'order_e_stocksubjectcode';
    // ��������
    const ORDER_ELEMENTS_COST_STOCK_ITEM_CODE =  'order_e_stockitemcode';
    // ������
    const ORDER_ELEMENTS_COST_CUSTOMER_COMPANY_CODE = 'order_e_customercompanycode';
    // ����
    const ORDER_ELEMENTS_COST_PAYOFF_TARGET_FLAG = 'order_e_payofftargetflag';
    // �ײ�Ŀ�
    const ORDER_ELEMENTS_COST_PRODUCT_QUANTITY = 'order_e_productquantity';
    // �̲�
    const ORDER_ELEMENTS_COST_MONETARY_UNIT_CODE = 'order_e_monetaryunitcode';
    // �̲ߥ�����
    const ORDER_ELEMENTS_COST_MONETARY_RATE_CODE = 'order_e_rate_code';
    // ñ��
    const ORDER_ELEMENTS_COST_PRODUCT_PRICE = 'order_e_productprice';
    // Ŭ�юڎ���
    const ORDER_ELEMENTS_COST_CONVERSION_RATE = 'order_e_conversionrate';
    // �ײ踶��
    const ORDER_ELEMENTS_COST_SUBTOTAL_PRICE = 'order_e_subtotalprice';
    // Ǽ��
    const ORDER_ELEMENTS_COST_DELIVERY_DATE = 'order_e_deliverydate';
    // ����
    const ORDER_ELEMENTS_COST_NOTE = 'order_e_note';
    // ���٥ꥹ�Ƚ�λ
    const LIST_END = 'list_end';

    // �׻����
    // �������⥿���ȥ�
    const PRODUCT_TOTAL_PRICE_HEADER = 'product_totalprice_header';
    // ��������
    const PRODUCT_TOTAL_PRICE = 'product_totalprice';
    // ���������⥿���ȥ�
    const FIXED_COST_TOTAL_PRICE_HEADER = 'fixedcost_totalprice_header';
    // ����������
    const FIXED_COST_TOTAL_PRICE = 'fixedcost_totalprice';
    // �����⥿���ȥ�
    const SALES_AMOUNT_HEADER = 'salesamount_header';
    // ������
    const SALES_AMOUNT = 'salesamount';
    // �������ץ����ȥ�
    const PRODUCT_PROFIT_HEADER = 'product_profit_header';
    // ��������
    const PRODUCT_PROFIT = 'product_profit';
    // ��������Ψ
    const PRODUCT_PROFIT_RATE = 'product_profit_rate';
    // ���������ץ����ȥ�
    const FIXED_COST_PROFIT_HEADER = 'fixedcost_profit_header';
    // ����������
    const FIXED_COST_PROFIT = 'fixedcost_profit';
    // ����������Ψ
    const FIXED_COST_PROFIT_RATE = 'fixedcost_profit_rate';
    // ��������ץ����ȥ�
    const PROFIT_HEADER = 'profit_header';
    // ���������
    const PROFIT = 'profit';
    // ����Ψ
    const PROFIT_RATE = 'profit_rate';
    // ������¤���񥿥��ȥ�
    const INDIRECT_COST_HEADER = 'indirect_cost_header';
    // ������¤����
    const INDIRECT_COST = 'indirect_cost';
    // ɸ����
    const STANDARD_RATE = 'standard_rate';
    // �Ķ����ץ����ȥ�
    const OPERATING_PROFIT_HEADER = 'operating_profit_header';
    // �Ķ�����
    const OPERATING_PROFIT = 'operating_profit';
    // �Ķ�����Ψ
    const OPERATING_PROFIT_RATE = 'operating_profit_rate';
    // �����񥿥��ȥ�
    const MEMBER_COST_HEADER = 'membercost_header';
    // �������оݿ�
    const MEMBER_QUANTITY = 'member_quantity';
    // pcs��������
    const MEMBER_UNIT_COST = 'member_unit_cost';
    // ��������
    const MEMBER_COST = 'membercost';
    // �����񥿥��ȥ�
    const DEPRECIATION_COST_HEADER = 'depreciation_cost_header';
    // �����оݿ�
    const DEPRECIATION_QUANTITY = 'depreciation_quantity';
    // pcs��������
    const DEPRECIATION_UNIT_COST = 'depreciation_unit_cost';
    // ��������
    const DEPRECIATION_COST = 'depreciation_cost';
    // ��¤���ѥ����ȥ�
    const MANUFACTURING_COST_HEADER = 'manufacturingcost_header';
    // ��¤�оݿ�
    const MANUFACTURING_QUANTITY = 'manufacturing_quantity';
    // pcs������
    const MANUFACTURING_UNIT_COST = 'manufacturing_unit_cost';
    // ��¤���ѹ��
    const MANUFACTURING_COST = 'manufacturingcost';
    // �����оݳ������񥿥��ȥ�
    const COST_NOT_DEPRECIATION_HEADER = 'cost_not_depreciation_header';
    // �����оݳ�������
    const COST_NOT_DEPRECIATION = 'cost_not_depreciation';

    // �󳰤����ꤵ�줿����ꥹ�ȡʹԤ�ɳ�դ��ǡ���������
    // ���Ƿ׻��ѥ���
    const CALCULATION_TARIFF = 'calculation_tariff';
    // ��������衧��
    const HIDDEN_PAYOFF_CIRCLE = 'hdn_payoff_circle';
    // �ܲٻ��ȥ���
    const HIDDEN_MAIN_PRODUCT = 'hdn_main_product';
    
    // ----------------------------------------------------------------------------------------------------


    // ������Υ���̾����Ƭ��
    // ���(��������)
    const PREFIX_RECEIVE = 'receive';
    // ����(ȯ������)
    const PREFIX_ORDER = 'order';
    // ����(�������)
    const PREFIX_PRODUCT = 'p';
    // ������(��������塢������)
    const PREFIX_FIXED_COST = 'f';
    // ������(������)
    const PREFIX_ELEMENTS_COST = 'e';


    // �оݥ��ꥢ��Ϣ����� -------------------------------------------------------------------------------------------

    // �оݥ��ꥢ̾
    const TARGET_AREA_NAME = [
        DEF_AREA_PRODUCT_SALES => '�������',
        DEF_AREA_FIXED_COST_SALES => '���������',
        DEF_AREA_FIXED_COST_ORDER => '������',
        DEF_AREA_PARTS_COST_ORDER => '������'
    ];

    // �оݥ��ꥢ��ɽ����̾�Υꥹ�ȡʥ��顼��å�����ɽ����)
    const TARGET_AREA_DISPLAY_NAME_LIST = [
        DEF_AREA_PRODUCT_SALES => '���ʼ���������',
        DEF_AREA_FIXED_COST_SALES => '���������������',
        DEF_AREA_FIXED_COST_ORDER => '������/�����оݳ�ȯ��������',
        DEF_AREA_PARTS_COST_ORDER => '����ȯ��������'
    ];

    // �оݥ��ꥢ�ǻ��Ѥ���ܵ��衢�������ʬ��
    const ORDER_ATTRIBUTE_FOR_TARGET_AREA = [
        // �ܵ������Ѥ��륨�ꥢ
        DEF_ATTRIBUTE_CLIENT => array(
            DEF_AREA_PRODUCT_SALES => true,
            DEF_AREA_FIXED_COST_SALES => true
        ),
        // ���������Ѥ��륨�ꥢ
        DEF_ATTRIBUTE_SUPPLIER => array(
            DEF_AREA_FIXED_COST_ORDER => true,
            DEF_AREA_PARTS_COST_ORDER => true
        )
    ];


    // ̾�Υꥹ�ȡʹ����̤γ��) -------------------------------------------------------------------------------------------

    // �إå�������̾�Υꥹ��
    const DETAIL_HEADER_CELL_NAME_LIST = [
        DEF_AREA_PRODUCT_SALES => self::PRODUCT_SALES_HEADER_NAME_LIST,
        DEF_AREA_FIXED_COST_SALES => self::RECEIVE_FIXED_COST_HEADER_CELL,
        DEF_AREA_FIXED_COST_ORDER => self::RECEIVE_FIXED_COST_HEADER_CELL,
        DEF_AREA_PARTS_COST_ORDER => self::ORDER_ELEMENT_COST_HEADER_CELL
    ];

    // �������إå���̾�Υꥹ��
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

    // �������׻����̾�Υꥹ��
    const PRODUCT_SALES_RESULT_NAME_LIST = [
        'total_header' => self::RECEIVE_PRODUCT_TOTAL_PRICE_HEADER,
        'total_quantity' => self::RECEIVE_PRODUCT_TOTAL_QUANTITY,
        'total_price' => self::RECEIVE_PRODUCT_TOTAL_PRICE
    ];

    // ���������إå�������̾�Υꥹ��
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

    // ���������׻���̥���̾�Υꥹ��
    const RECEIVE_FIXED_COST_RESULT_CELL = [
        'total_header' => self::RECEIVE_FIXED_COST_TOTAL_PRICE_HEADER,
        'total_quantity' => self::RECEIVE_FIXED_COST_TOTAL_QUANTITY,
        'total_price' => self::RECEIVE_FIXED_COST_TOTAL_PRICE
    ];
    
    // ������إå�������̾�Υꥹ��
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

    // ������׻���̥���̾�Υꥹ��
    const ORDER_FIXED_COST_RESULT_CELL = [
        'total_header' => self::ORDER_FIXED_COST_FIXED_COST_HEADER,
        'fixed_cost_total' => self::ORDER_FIXED_COST_FIXED_COST,
        'not_depreciation' => self::ORDER_FIXED_COST_COST_NOT_DEPRECIATION
    ];

    // ������إå�������̾�Υꥹ��
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

    // ������եå�������̾�Υꥹ��
    const ORDER_ELEMENT_COST_RESULT_CELL = [
        'list' => self::LIST_END
    ];

    // ��������ȥإå������Ϲ��ܥ���̾��
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

    // ��������ȥإå������ܥ����ȥ륻��̾��
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

    // ��������ȥإå�����������
    const WORK_SHEET_HEADER_CELL = [
        'header_data' => self::WORK_SHEET_HEADER_DATA_CELL,
        'header_title' => self::WORK_SHEET_HEADER_TITLE_CELL
    ];

    // �����Ȥ��ϰϷ����ѤΥ���̾��
    const SET_POSITION_NAME_LIST =  [
        self::TOP_LEFT,
        self::TOP_RIGHT,
        self::BOTTOM_LEFT
    ];

    // ��������ȥեå������Ϲ��ܥ���̾��
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

    // ��������ȥեå������Ϲ��ܥ���̾��
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

    // �󳰥����̾�Υꥹ��(�Ԥ��ȤΥǡ�������)
    const HIDDEN_NAME_LIST= [
        'calculationTariff' => self::CALCULATION_TARIFF,
        'hiddenPayoffCircle' => self::HIDDEN_PAYOFF_CIRCLE,
        'hiddenMainProduct' => self::HIDDEN_MAIN_PRODUCT
    ];


    // ������������ƤΥ���̾�Υꥹ��
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

    // ���ƤΥ���̾�Τ�����ǻ��Ѥ�����
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
    * �оݥ��ꥢ�Υ���̾�Υꥹ�Ȥμ���
    * 
    * @param array   $areaCode  �оݥ��ꥢ�Υ�����
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
        'm_salesdivision' => '���ʬ��ޥ���',
        'm_salesclass' => '����ʬ�ޥ���',
        'm_stocksubject' => '�������ܥޥ���',
        'm_stockitem' => '�������ʥޥ���',
        'm_company' => '��ҥޥ���',
        'm_monetaryrate' => '�̲ߥ졼�ȥޥ���'
    ];

    // ����¾����� -------------------------------------------------------------------------------------------
    // ��������ȤιԿ���ɽ����ʬ��
    const WORK_SHEET_COLUMN_NUMBER = 16;

    // ñ���ξ������ʲ��η��(�������̲ߥ�����)
    const PRICE_DECIMAL_DIGIT = [
        DEF_MONETARY_YEN => 2,
        DEF_MONETARY_USD => 4,
        DEF_MONETARY_HKD => 4,
    ];

}