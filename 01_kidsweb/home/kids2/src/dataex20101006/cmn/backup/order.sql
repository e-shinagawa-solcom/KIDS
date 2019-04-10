/*
	���ס�����(L/Cͽ��ɽ����)����L/C�ǡ����סַ׾��������
	�оݡ��ǡ����������ݡ���
	������chiba
	���͡�

	��������
	2004.04.07	LCͽ��ɽ�˰ʲ��Υ������ɲ�	���ٹ��ֹ�ʥ����ȥ����ˡ���Х��������ɡ���ʧ���
	2004.04.07	ORDER BY �ˤ�ȯ��No�ξ��硢���ٹ��ֹ�ξ���˥����Ȥ����褦���ѹ�
	2004.04.27	�ǿ�ȯ��μ������Ƥ���
	2004.05.13	Ǽ�ʾ����ɲ�
	2004.05.13	���������ѹ��������Τʾ�������ꤹ��褦����
	2004.05.19	LCͽ��ɽ�Ͽ����ʥ�ӥ�����ֹ� 0�ˤΤ� ��Х����˹����Υǡ����ʺ���ǡ����ޤ�ˤ���Ф���褦�˽���
				�ޤ���ȯ��ξ��֤�ɽ������褦�˽���
*/
SELECT
	o.strOrderCode
	,od.lngSortKey
	,o.strReviseCode
	,
	/* ����ǡ������ɤ������� */
	CASE WHEN o.lngRevisionNo < 0 AND o.bytInvalidFlag = FALSE
	       THEN '���'
	  WHEN o.lngRevisionNo < 0 AND o.bytInvalidFlag = TRUE
	       THEN '�����ͭ����'
	  WHEN o.lngOrderStatusCode = 1
	       THEN '̤��ǧ'
	  WHEN o.lngOrderStatusCode = 3
	       THEN 'Ǽ��'
	  WHEN o.lngOrderStatusCode = 4
	       THEN 'Ǽ��'
	  WHEN o.lngOrderStatusCode = 99
	       THEN 'Ǽ��'
	  WHEN 0 = 
	      (
	        SELECT COUNT ( * ) 
	        FROM m_Workflow mw 
	        WHERE to_number ( mw.strWorkflowKeyCode, '9999999') = o.lngOrderNo
	         AND mw.lngFunctionCode = 501
	      )
	       THEN '��ǧ��'
	  WHEN 10 = 
	      (
	        SELECT tw.lngWorkflowStatusCode
	        FROM m_Workflow mw2, t_Workflow tw
	        WHERE to_number ( mw2.strWorkflowKeyCode, '9999999') = o.lngOrderNo
	         AND mw2.lngFunctionCode = 501
	         AND mw2.lngWorkflowCode = tw.lngWorkflowCode
	         AND tw.lngWorkflowSubCode =
	        (
	          SELECT MAX ( tw2.lngWorkflowSubCode ) FROM t_Workflow tw2 WHERE tw.lngWorkflowCode = tw2.lngWorkflowCode
	        )
	      )
	       THEN '��ǧ��'
	  WHEN 4 = 
	      (
	        SELECT tw.lngWorkflowStatusCode
	        FROM m_Workflow mw2, t_Workflow tw
	        WHERE to_number ( mw2.strWorkflowKeyCode, '9999999') = o.lngOrderNo
	         AND mw2.lngFunctionCode = 501
	         AND mw2.lngWorkflowCode = tw.lngWorkflowCode
	         AND tw.lngWorkflowSubCode =
	        (
	          SELECT MAX ( tw2.lngWorkflowSubCode ) FROM t_Workflow tw2 WHERE tw.lngWorkflowCode = tw2.lngWorkflowCode
	        )
	      )
	       THEN '̤��ǧ'
	  WHEN 99 = 
	      (
	        SELECT tw.lngWorkflowStatusCode
	        FROM m_Workflow mw2, t_Workflow tw
	        WHERE to_number ( mw2.strWorkflowKeyCode, '9999999') = o.lngOrderNo
	         AND mw2.lngFunctionCode = 501
	         AND mw2.lngWorkflowCode = tw.lngWorkflowCode
	         AND tw.lngWorkflowSubCode =
	        (
	          SELECT MAX ( tw2.lngWorkflowSubCode ) FROM t_Workflow tw2 WHERE tw.lngWorkflowCode = tw2.lngWorkflowCode
	        )
	      )
	       THEN '̤��ǧ'
	  ELSE '̤��ǧ'
	END AS lngOrderStatus
	,pa.strPayConditionName
	,NULL
	,c.strCompanyDisplayCode
	,NULL
	,p.strProductCode
	,p.strProductEnglishName
	,od.lngProductQuantity
	,pu.strProductUnitName
	,od.curProductPrice
	,od.curSubTotalPrice
	,od.dtmDeliveryDate
	,od.dtmDeliveryDate
	,o.dtmAppropriationDate
	,To_Date(o.dtmInsertDate, 'YYYY/MM/DD' )
	,dp.strCompanyDisplayName
	,NULL
	,NULL
	,NULL
	,NULL
	,NULL
	,NULL
	,mu.strMonetaryUnitName
	,o.strNote
FROM
	m_Order o 
	LEFT JOIN m_PayCondition pa 
		ON o.lngPayConditionCode = pa.lngPayConditionCode
		LEFT JOIN m_Company dp
			ON o.lngDeliveryPlaceCode = dp.lngCompanyCode
			LEFT JOIN m_Company c
				ON o.lngCustomerCompanyCode = c.lngCompanyCode
				LEFT JOIN m_MonetaryUnit mu
					ON o.lngMonetaryUnitCode = mu.lngMonetaryUnitCode
					LEFT JOIN 
						(
							t_OrderDetail od
								LEFT JOIN m_Product p 
									ON od.strProductCode = p.strProductCode
									LEFT JOIN m_ProductUnit pu
										ON od.lngProductUnitCode = pu.lngProductUnitCode
						)
						ON o.lngOrderNo = od.lngOrderNo
/* ��1.L/C�Υǡ��� 2.��ӥ����NO��0�ʾ�Υǡ��� */
WHERE
	_%lngExportConditions%_
	AND o.strOrderCode >= '_%strOrderCodeFrom%_'
	AND o.strOrderCode <= '_%strOrderCodeTo%_'
	AND o.bytInvalidFlag        = FALSE
/* ORDER BY ������ */
 ORDER BY o.strOrderCode, od.lngSortKey
