FROM
(
	SELECT DISTINCT
		/* to_char(mo.dtmInsertDate,'YYYY/MM/DD')	AS dtmInsertDate */
		'' 	AS dtmInsertDate
		,mo.strOrderCode						AS strOrderCode
		,mo.strReviseCode						AS strReviseCode
		,mo.lnggroupcode						AS lnggroupcode
		,mo.lngusercode							AS lngusercode
		,tod.strproductcode						AS strproductcode
		,tod.lngStockSubjectCode				AS lngStockSubjectCode
		,tod.lngStockItemCode					AS lngStockItemCode
		,msi.strStockItemName					AS strStockItemName
		,tod.strNote							AS strDetailNote
		,mo2.lngWorkflowStatusCode				AS lngWorkflowStatusCode
		,CASE 	WHEN mo2.lngWorkflowStatusCode = 99    THEN '#FF0000'
				WHEN mo2.lngWorkflowStatusCode = 4     THEN '#008000'
				WHEN mo2.lngWorkflowStatusCode is null THEN '#999999'
				ELSE '#000000'
		END AS strOrderCodeColor
	FROM
		m_order mo LEFT JOIN
		(
			/* 有効な最新発注データのワークフローステータス */
			SELECT
				mo1.*
				,tw.lngWorkflowStatusCode
			FROM
				m_workflow mw JOIN t_workflow tw ON mw.lngworkflowcode = tw.lngworkflowcode
				,(
					/* 有効な最新発注データ */
					SELECT 
						*
					FROM
						m_order mos
					WHERE
						 mos.bytInvalidFlag = false
						AND mos.strOrderCode not in (select strOrderCode from m_order where lngRevisionNo < 0)
						AND mos.lngRevisionNo = (select max(lngRevisionNo) from m_order where mos.strOrderCode = strOrderCode)
				) mo1
			WHERE
				mo1.lngOrderNo = to_number(mw.strWorkflowKeyCode, '99999999')
				AND tw.lngWorkflowSubCode = (select max(lngWorkflowSubCode) from t_workflow where mw.lngWorkflowCode = lngWorkflowCode)
		) mo2 ON mo.lngOrderNo = mo2.lngOrderNo
		,t_orderdetail tod
		,m_StockSubject mss
		,m_StockItem msi
	WHERE
			mo.lngorderno = tod.lngorderno
		AND mo.bytInvalidFlag = false
		AND mo.strordercode NOT IN (select strordercode from m_order where lngrevisionno < 0)
		AND mo.lngrevisionno = (select max(lngrevisionno) from m_order where mo.strordercode = strordercode)
		AND mss.lngstocksubjectcode = msi.lngstocksubjectcode
		AND tod.lngStockSubjectCode = mss.lngStockSubjectCode
		AND tod.lngStockItemCode = msi.lngStockItemCode
		_%column_lngorderstatuscode_enable%_ AND mo.lngOrderStatusCode = _%lngorderstatuscode%_
) m1 RIGHT JOIN m_product mp ON m1.strproductcode = mp.strproductcode
JOIN m_User mu ON mp.lngInChargeUserCode = mu.lngUserCode
WHERE
	mp.bytInvalidFlag <> true
	AND mp.lngInChargeGroupCode = _%lnggroupcode%_
	_%column_lngproductno_in_disable%_ AND mp.dtmDeliveryLimitDate between '_%date_from%_' AND '_%date_to%_'
	_%column_lngusercode_enable%_ AND mp.lngInChargeUserCode = _%lngusercode%_
 
