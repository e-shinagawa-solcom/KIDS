<?php

require_once ('conf.inc');

// ������������������饹

class workSheetConst {

    private function __construct() {

    }

    // Excel(xlsx)���ץꥱ������󥿥���
    const APP_EXCEL_TYPE = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
    
    // ��������ȥ���̾�� -------------------------------------------------------------------------------------------

    // ���ϥ��ꥢ���ַ���ѥ���
    // ��ü��ü           
    const TOP_LEFT = 'top_left';
    // ��ü��ü
    const TOP_RIGHT = 'top_right';
    // ��ü��ü
    const BOTTOM_LEFT = 'bottom_left';

    // �إå�����
    // �����������ȥ�
    const INSERT_DATE_HEADER = 'insert_date_header';
    // ������
    const INSERT_DATE = 'insert_date';
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
    const INCHARGE_USER_CODE_HEADER = 'inchargeusercode_header';
    // ô��
    const INCHARGE_USER_CODE = 'inchargeusercode';
    // ô����ȯ�ԥ����ȥ�
    const DEVELOP_USER_CODE_HEADER = 'developusercode_header';
    // ô����ȯ��
    const DEVELOP_USER_CODE = 'developusercode';
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
    // ���ǹ�ץ���(͢�����ѷ׻���)
    const TARIFF_TOTAL = 'tariff_total';
    // ͢�����ѷ׻��ѥ���
    const CALCULATION_IMPORT_COST = 'calculation_import_cost';
    // ��������衧��
    const HIDDEN_PAYOFF_CIRCLE = 'hdn_payoff_circle';
    // �ܲٻ��ȥ���
    const HIDDEN_MAIN_PRODUCT = 'hdn_main_product';
    // �̲ߥ졼��Ž���դ����ַ�᥻��
    const MONETARY_RATE_LIST = 'monetary_rate_list_header';

    // �����������ʬ��ɥ�åץ�����ꥹ�ȤΥ����ȥ륻��
    const RECEIVE_PRODUCT_SALES_DIVISION_DROPDOWN = 'receive_p_salesdivision_dropdown';
    // �������������ʬ��ɥ�åץ�����ꥹ�ȤΥ����ȥ륻��
    const RECEIVE_FIXED_COST_SALES_DIVISION_DROPDOWN = 'receive_f_salesdivision_dropdown';
    // ������λ������ܥɥ�åץ�����ꥹ�ȤΥ����ȥ륻��
    const ORDER_FIXED_COST_STOCK_SUBJECT_DROPDOWN = 'order_f_stocksubject_dropdown';
    // ������λ������ܥɥ�åץ�����ꥹ�ȤΥ����ȥ륻��
    const ORDER_ELEMENT_COST_STOCK_SUBJECT_DROPDOWN = 'order_e_stocksubject_dropdown';
    // ����¾���Ѥλ������ܥɥ�åץ�����ꥹ�ȤΥ����ȥ륻��
    const ORDER_OTHER_COST_STOCK_SUBJECT_DROPDOWN = 'order_o_stocksubject_dropdown';

    // ������������ʬ�ɥ�åץ�����ꥹ�ȤΥ����ȥ륻��
    const RECEIVE_PRODUCT_SALES_CLASS_DROPDOWN = 'receive_p_salesclass_dropdown';
    // ��������������ʬ�ɥ�åץ�����ꥹ�ȤΥ����ȥ륻��
    const RECEIVE_FIXED_COST_SALES_CLASS_DROPDOWN = 'receive_f_salesclass_dropdown';
    // ������λ������ʥɥ�åץ�����ꥹ�ȤΥ����ȥ륻��
    const ORDER_FIXED_COST_STOCK_ITEM_DROPDOWN = 'order_f_stockitem_dropdown';
    // ������λ������ʥɥ�åץ�����ꥹ�ȤΥ����ȥ륻��
    const ORDER_ELEMENT_COST_STOCK_ITEM_DROPDOWN = 'order_e_stockitem_dropdown';
    // ����¾���Ѥλ������ʥɥ�åץ�����ꥹ�ȤΥ����ȥ륻��
    const ORDER_OTHER_COST_STOCK_ITEM_DROPDOWN = 'order_o_stockitem_dropdown';

    // �Ķ�����Υɥ�åץ�����ꥹ�ȤΥ����ȥ륻��
    const INCHARGE_GROUP_DROPDOWN = 'incharge_group_dropdown';

    // ô���Υɥ�åץ�����ꥹ�ȤΥ����ȥ륻��
    const INCHARGE_USER_DROPDOWN = 'incharge_user_dropdown';

    // ��ȯô���ԤΥɥ�åץ�����ꥹ�ȤΥ����ȥ륻��
    const DEVELOP_USER_DROPDOWN = 'develop_user_dropdown';

    // �ܵ���Υɥ�åץ�����ꥹ�ȤΥ����ȥ륻��
    const CLIENT_DROPDOWN = 'client_dropdown';

    // ������Υɥ�åץ�����ꥹ�ȤΥ����ȥ륻��
    const SUPPLIER_DROPDOWN = 'supplier_dropdown';
    
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

    // ���Ѹ����ε�ǽ̾--------------------------------------------------------------------------------------
    // ���Ѹ����ץ�ӥ塼(����)
    const ESTIMATE_PREVIEW = 'preview';
    // ���Ѹ����ץ�ӥ塼(�Խ�)
    const ESTIMATE_EDIT = 'edit';
    // ���Ѹ������
    const ESTIMATE_DELETE = 'delete';
    // �������������
    const ESTIMATE_RESIST_SELECT = 'select';
    // �����������Ͽ(��ǧ)
    const ESTIMATE_RESIST_CONFIRM = 'confirm';
    // �����������Ͽ
    const ESTIMATE_RESIST_RESULT = 'regist';
    // ���Ѹ�������
    const ESTIMATE_SEARCH = 'search';
    // ���Ѹ�������
    const ESTIMATE_RESULT = 'result';



    // ���Ѹ����ν����⡼��--------------------------------------------------------------------------------------
    // ���Ѹ����ץ�ӥ塼(����)
    const MODE_ESTIMATE_PREVIEW = 'preview';
    // ���Ѹ����ץ�ӥ塼(�Խ�)
    const MODE_ESTIMATE_EDIT = 'edit';
    // ���Ѹ����ץ�ӥ塼(���������)
    const MODE_ESTIMATE_DOWNLOAD = 'download';
    // ���Ѹ������
    const MODE_ESTIMATE_DELETE = 'delete';
    // �������������
    const MODE_ESTIMATE_RESIST_SELECT = 'select';
    // �����������Ͽ(��ǧ)
    const MODE_ESTIMATE_RESIST_CONFIRM = 'confirm';
    // �����������Ͽ
    const MODE_ESTIMATE_RESIST_RESULT = 'regist';
    // ���Ѹ�������
    const MODE_ESTIMATE_SEARCH = 'search';
    // ���Ѹ�������
    const MODE_ESTIMATE_RESULT = 'result';
    // ���Ѹ�������
    const MODE_ESTIMATE_PRINT = 'print';



    // �оݥ��ꥢ��Ϣ����� -------------------------------------------------------------------------------------------

    // �оݥ��ꥢ̾
    const TARGET_AREA_NAME = [
        DEF_AREA_PRODUCT_SALES => '�������',
        DEF_AREA_FIXED_COST_SALES => '���������',
        DEF_AREA_FIXED_COST_ORDER => '������',
        DEF_AREA_PARTS_COST_ORDER => '������',
        DEF_AREA_OTHER_COST_ORDER => '����¾����'
    ];

    // �оݥ��ꥢ��ɽ����̾�Υꥹ�ȡʥ��顼��å�����ɽ����)
    const TARGET_AREA_DISPLAY_NAME_LIST = [
        DEF_AREA_PRODUCT_SALES => '���ʼ���������',
        DEF_AREA_FIXED_COST_SALES => '���������������',
        DEF_AREA_FIXED_COST_ORDER => '������/�����оݳ�ȯ��������',
        DEF_AREA_PARTS_COST_ORDER => '����ȯ��������',
        DEF_AREA_OTHER_COST_ORDER => '����¾������',
    ];

    // ����ޤ���ȯ��ο���ʬ��
    // �����°���륨�ꥢ������
    const RECEIVE_AREA_CODE = [
        DEF_AREA_PRODUCT_SALES => true,
        DEF_AREA_FIXED_COST_SALES => true
    ];
    // ȯ���°���륨�ꥢ������
    const ORDER_AREA_CODE = [
        DEF_AREA_FIXED_COST_ORDER => true,
        DEF_AREA_PARTS_COST_ORDER => true,
        DEF_AREA_OTHER_COST_ORDER => false
    ];

    // �оݥ��ꥢ�ǻ��Ѥ���ܵ��衢�������ʬ��
    const ORDER_ATTRIBUTE_FOR_TARGET_AREA = [
        // �ܵ������Ѥ��륨�ꥢ
        DEF_ATTRIBUTE_CLIENT => self::RECEIVE_AREA_CODE,
        // ���������Ѥ��륨�ꥢ
        DEF_ATTRIBUTE_SUPPLIER => self::ORDER_AREA_CODE
    ];



    // ̾�Υꥹ�ȡʹ����̤γ��) -------------------------------------------------------------------------------------------

    // �������إå�������̾�Υꥹ��
    const DETAIL_HEADER_CELL_NAME_LIST = [
        DEF_AREA_PRODUCT_SALES => self::PRODUCT_SALES_HEADER_NAME_LIST,
        DEF_AREA_FIXED_COST_SALES => self::RECEIVE_FIXED_COST_HEADER_CELL,
        DEF_AREA_FIXED_COST_ORDER => self::ORDER_FIXED_COST_HEADER_CELL,
        DEF_AREA_PARTS_COST_ORDER => self::ORDER_ELEMENT_COST_HEADER_CELL,
        DEF_AREA_OTHER_COST_ORDER => self::ORDER_ELEMENT_COST_HEADER_CELL
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
        'insertDate' => self::INSERT_DATE,
        'productCode' => self::PRODUCT_CODE,
        'productName' => self::PRODUCT_NAME,
        'productEnglishName' => self::PRODUCT_ENGLISH_NAME,
        'retailPrice' =>self::RETAIL_PRICE,
        'inchargeGroupCode' => self::INCHARGE_GROUP_CODE,
        'inchargeUserCode' => self::INCHARGE_USER_CODE,
        'developUserCode' => self::DEVELOP_USER_CODE,
        'cartonQuantity' => self::CARTON_QUANTITY,
        'productionQuantity' => self::PRODUCTION_QUANTITY
    ];

    // ��������ȥإå������ܥ����ȥ륻��̾��
    const WORK_SHEET_HEADER_TITLE_CELL = [
        'insertDate' => self::INSERT_DATE_HEADER,
        'productCode' => self::PRODUCT_CODE_HEADER,
        'productName' => self::PRODUCT_NAME_HEADER,
        'productEnglishName' => self::PRODUCT_ENGLISH_NAME_HEADER,
        'retailPrice' => self::RETAIL_PRICE_HEADER,
        'inchargeGroupCode' => self::INCHARGE_GROUP_CODE_HEADER,
        'inchargeUserCode' => self::INCHARGE_USER_CODE_HEADER,
        'developUserCode' => self::DEVELOP_USER_CODE_HEADER,
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
        'tariffTotal' => self::TARIFF_TOTAL,
        'calculationImportCost' => self::CALCULATION_IMPORT_COST,
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

    // ������������ƤΥ���̾�Υꥹ��
    const ALL_WORK_SHEET_CELL_NAME_LIST_DOWNLOAD = [
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
        self::HIDDEN_NAME_LIST,
        self::DIVISION_SUBJECT_DROPDOWN_CELL_NAME,
        self::CLASS_ITEM_DROPDOWN_CELL_NAME,
        self::OTHER_DROPDOWN_CELL_NAME
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

    // ���ƤΥ���̾�Τ�����ǻ��Ѥ�����
    public static function getAllNameListForDownload() {
        $beforeNameList = self::ALL_WORK_SHEET_CELL_NAME_LIST_DOWNLOAD;
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
            case DEF_AREA_OTHER_COST_ORDER:
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

    // �оݥ��ꥢ��°���������ʬ
    const AREA_ATTRIBUTE_TO_STOCK_CLASS_CODE = [
        DEF_AREA_FIXED_COST_ORDER => DEF_STOCK_CLASS_CODE_PRODUCTION,
        DEF_AREA_PARTS_COST_ORDER => DEF_STOCK_CLASS_CODE_PARTS,
        DEF_AREA_OTHER_COST_ORDER => DEF_STOCK_CLASS_CODE_PARTS
    ];

    // �̲ߤ�ɽ��������
    const MONETARY_DISPLAY_CODE = [
        DEF_MONETARY_YEN => 'JP',
        DEF_MONETARY_USD => 'US',
        DEF_MONETARY_HKD => 'HK',
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

    // �ԥ��ԡ����˥��ԡ�������Ԥ�����ϰ�
    const WORK_SHEET_COPY_COLUMN_NUMBER = 27;


    // ��������Ⱦ���Υǥե������
    // ��
    const WORK_SHEET_CELL_VALUE_DEFAULT = '';

    // ��
    const WORK_SHEET_FONT_FAMILY_DEFAULT = '�ͣ� �Х����å�';

    // ʸ��������
    const WORK_SHEET_FONT_SIZE_DEFAULT = 9;

    // �طʿ�
    const WORK_SHEET_BACKGROUND_COLOR_DEFAULT = 'FFFFFF';

    // ʸ����
    const WORK_SHEET_FONT_COLOR_DEFAULT = '000000';

    // �����ο�
    const WORK_SHEET_BORDER_COLOR_DEFAULT = '000000';

    // ��������η�����
    const WORK_SHEET_EXCEL_BORDER_STYLE_DEFAULT = 'none';

    // ������(css)
    const WORK_SHEET_BORDER_STYLE_DEFAULT = 'none';

    // ��������
    const WORK_SHEET_BORDER_WIDTH_DEFAULT = 0;

    // ����
    const WORK_SHEET_BORDER_DEFAULT = [
        'color' => self::WORK_SHEET_BORDER_COLOR_DEFAULT,
        'excelStyle' => self::WORK_SHEET_EXCEL_BORDER_STYLE_DEFAULT,
        'style' => self::WORK_SHEET_BORDER_STYLE_DEFAULT,
        'width' => self::WORK_SHEET_BORDER_WIDTH_DEFAULT
    ];

    // ��������(������)
    const WORK_SHEET_BORDER_INFO_DEFAULT = [
        'left' => self::WORK_SHEET_BORDER_DEFAULT,
        'right' => self::WORK_SHEET_BORDER_DEFAULT,
        'top' => self::WORK_SHEET_BORDER_DEFAULT,
        'bottom' => self::WORK_SHEET_BORDER_DEFAULT
    ];

    // ��ľ����
    const WORK_SHEET_VARTICAL_POSITION_DEFAULT = 'bottom';

    // ��ʿ����
    const WORK_SHEET_HORIZONTAL_POSITION = 'center';
    
    // ����
    const WORK_SHEET_EMPHASIS_BOLD_DEFAULT = false;

    // ����
    const WORK_SHEET_EMPHASIS_ITALIC_DEFAULT = false;

    // ʸ���ζ�Ĵ
    const WORK_SHEET_EMPHASIS_DEFAULT = [
        'bold' => self::WORK_SHEET_EMPHASIS_BOLD_DEFAULT,
        'italic' => self::WORK_SHEET_EMPHASIS_ITALIC_DEFAULT
    ];

    // ����Υǥե������
    const WORK_SHEET_CELL_DEFAULT = [
        'value' => self::WORK_SHEET_CELL_VALUE_DEFAULT,
        'fontFamily' => self::WORK_SHEET_FONT_FAMILY_DEFAULT,
        'fontSize' => self::WORK_SHEET_FONT_SIZE_DEFAULT,
        'backgroundColor' => self::WORK_SHEET_BACKGROUND_COLOR_DEFAULT,
        'fontColor' => self::WORK_SHEET_FONT_COLOR_DEFAULT,
        'border' => self::WORK_SHEET_BORDER_INFO_DEFAULT,
        'varticalPosition' => self::WORK_SHEET_VARTICAL_POSITION_DEFAULT,
        'horizontalPosition' => self::WORK_SHEET_HORIZONTAL_POSITION,
        'emphasis' => self::WORK_SHEET_EMPHASIS_DEFAULT
    ];

    /**
    * �Խ���ǽ�ʥ���ꥹ��(�оݥ��ꥢ)
    * 
    * @param array   $areaCode  �оݥ��ꥢ�Υ�����
    * 
    * @return array  $editableKeys
    */
    public static function getEditableKeys($areaCode) {
        $editableKeys = array(
            'divisionSubject' => true,
            'classItem' => true,
            'customerCompany' => true,
            'payoff' => true,
            'quantity' => true,
            'monetaryDisplay' => true,
            'price' => true,
            'conversionRate' => true,
            'subtotal' => false,
            'delivery' => true,
            'note' => true
        );
        return $editableKeys;
    }

    // �Խ���ǽ�ʥ���ꥹ��(�оݥ��ꥢ�ʳ�)
    const EDITABLE_KEY_EXPECT_FOR_TARGET_AREA = [
        self::PRODUCT_CODE => false,
        self::PRODUCT_NAME => true,
        self::PRODUCT_ENGLISH_NAME => true,
        self::RETAIL_PRICE => true,
        self::INCHARGE_GROUP_CODE => true,
        self::INCHARGE_USER_CODE => true,
        self::DEVELOP_USER_CODE => true,
        self::CARTON_QUANTITY => true,
        self::PRODUCTION_QUANTITY => false
    ];
    

    // �ߥޡ���ɽ������Υꥹ��
    const JPY_MARK_DISPLAY_CELLS = [
        self::RETAIL_PRICE => true,
        self::RECEIVE_PRODUCT_TOTAL_PRICE => true,
        self::RECEIVE_FIXED_COST_TOTAL_PRICE => true,
        self::ORDER_FIXED_COST_FIXED_COST => true,
        self::ORDER_FIXED_COST_COST_NOT_DEPRECIATION => true,
        self::PRODUCT_TOTAL_PRICE => true,
        self::FIXED_COST_TOTAL_PRICE => true,
        self::SALES_AMOUNT => true,
        self::PRODUCT_PROFIT => true,
        self::FIXED_COST_PROFIT => true,
        self::PROFIT => true,
        self::INDIRECT_COST => true,
        self::OPERATING_PROFIT => true,
        self::MEMBER_UNIT_COST => true,
        self::MEMBER_COST => true,
        self::DEPRECIATION_UNIT_COST => true,
        self::DEPRECIATION_COST => true,
        self::MANUFACTURING_UNIT_COST => true,
        self::MANUFACTURING_COST => true,
        self::COST_NOT_DEPRECIATION => true
    ];

    // ���̡�ñ����Ϣ�Υ���(�ߥޡ����ΤĤ��ʤ�����޶��ڤ�ο���)
    const QUANTITY_OR_PRICE_CELLS = [
        self::CARTON_QUANTITY => true,
        self::PRODUCTION_QUANTITY => true,
        self::RECEIVE_PRODUCT_TOTAL_QUANTITY => false,
        self::RECEIVE_FIXED_COST_TOTAL_QUANTITY => false,
        self::MEMBER_QUANTITY => true,
        self::DEPRECIATION_QUANTITY => true,
        self::MANUFACTURING_QUANTITY => true
    ];

    // ���饹̾��ɽ��ʸ����
    const AREA_CLASS_STRING = 'area';      // �оݥ��ꥢ�����ɤȥ��åȤǻ���
    const DETAIL_CLASS_STRING = 'detail';  // ���ٹԤξ���Ϳ���륯�饹̾

    // �̲ߥ������Ѵ������(���������)
    const MONETARY_UNIT_WORKSHEET = [
      'JP' => DEF_MONETARY_YEN,
      'US' => DEF_MONETARY_USD,
      'HK' => DEF_MONETARY_HKD
    ];

    // ���ѵ���
    const PAYOFF_CIRCLE_SIGN = '��';

    // ���ʬ�ࡢ�������ܥɥ�åץ�����ꥹ�ȤΥ����ȥ륻��ꥹ��
    const DIVISION_SUBJECT_DROPDOWN_CELL_NAME = [
        DEF_AREA_PRODUCT_SALES => self::RECEIVE_PRODUCT_SALES_DIVISION_DROPDOWN,
        DEF_AREA_FIXED_COST_SALES => self::RECEIVE_FIXED_COST_SALES_DIVISION_DROPDOWN,
        DEF_AREA_FIXED_COST_ORDER => self::ORDER_FIXED_COST_STOCK_SUBJECT_DROPDOWN,
        DEF_AREA_PARTS_COST_ORDER => self::ORDER_ELEMENT_COST_STOCK_SUBJECT_DROPDOWN,
        DEF_AREA_OTHER_COST_ORDER => self::ORDER_OTHER_COST_STOCK_SUBJECT_DROPDOWN
    ];

    const CLASS_ITEM_DROPDOWN_CELL_NAME = [
        DEF_AREA_PRODUCT_SALES => self::RECEIVE_PRODUCT_SALES_CLASS_DROPDOWN,
        DEF_AREA_FIXED_COST_SALES => self::RECEIVE_FIXED_COST_SALES_CLASS_DROPDOWN,
        DEF_AREA_FIXED_COST_ORDER => self::ORDER_FIXED_COST_STOCK_ITEM_DROPDOWN,
        DEF_AREA_PARTS_COST_ORDER => self::ORDER_ELEMENT_COST_STOCK_ITEM_DROPDOWN,
        DEF_AREA_OTHER_COST_ORDER => self::ORDER_OTHER_COST_STOCK_ITEM_DROPDOWN
    ];

    const OTHER_DROPDOWN_CELL_NAME = [
        self::INCHARGE_GROUP_DROPDOWN,
        self::INCHARGE_USER_DROPDOWN,
        self::DEVELOP_USER_DROPDOWN,
        self::CLIENT_DROPDOWN,
        self::SUPPLIER_DROPDOWN
    ];

}