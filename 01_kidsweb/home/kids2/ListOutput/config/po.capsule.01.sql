SELECT DISTINCT
	m1.dtmInsertDate
	,mp.strProductCode
	,mp.strproductname
	,m1.lngStockItemCode
	,m1.lngStockSubjectCode
	,m1.lnggroupcode
	,m1.lngusercode
	,m1.lngWorkflowStatusCode
	/* 原型 */
	,CASE WHEN m1.lngStockSubjectCode = 431 AND m1.lngStockItemCode = 1
		THEN m1.strOrderCode || '-' || strReviseCode || '<br />' || '(' || m1.strStockItemName || ')'
	END AS s431i1
	/* シリコン */
	,CASE WHEN m1.lngStockSubjectCode = 431 AND m1.lngStockItemCode = 4
		THEN m1.strOrderCode || '-' || strReviseCode || '<br />' || '(' || m1.strStockItemName || ')'
	END AS s431i4
	/* キャスト */
	,CASE WHEN m1.lngStockSubjectCode = 431 AND m1.lngStockItemCode = 3
		THEN m1.strOrderCode || '-' || strReviseCode || '<br />' || '(' || m1.strStockItemName || ')'
	END AS s431i3
	/* 分割 */
	,CASE WHEN 	(m1.lngStockSubjectCode = 431 AND m1.lngStockItemCode = 7) OR
				(m1.lngStockSubjectCode = 433 AND m1.lngStockItemCode = 4)
		THEN m1.strOrderCode || '-' || strReviseCode || '<br />' || '(' || m1.strStockItemName || ')'
	END AS s431i7_s433i4
	/* 色彩 */
	,CASE WHEN m1.lngStockSubjectCode = 403 AND m1.lngStockItemCode = 1
		THEN m1.strOrderCode || '-' || strReviseCode || '<br />' || '(' || m1.strStockItemName || ')'
	END AS s403i1
	/* マスク */
	,CASE WHEN m1.lngStockSubjectCode = 433 AND m1.lngStockItemCode = 2
		THEN m1.strOrderCode || '-' || strReviseCode || '<br />' || '(' || m1.strStockItemName || ')'
	END AS s433i2
	/* 金型 */
	,CASE WHEN 	(m1.lngStockSubjectCode = 431 AND m1.lngStockItemCode = 8) OR
				(m1.lngStockSubjectCode = 433 AND m1.lngStockItemCode = 1)
		THEN m1.strOrderCode || '-' || strReviseCode || '<br />' || '(' || m1.strStockItemName || ')'
	END AS s431i8_s433i1
	/* タンポ */
	,CASE WHEN m1.lngStockSubjectCode = 401 AND m1.lngStockItemCode = 8
		THEN m1.strOrderCode || '-' || strReviseCode || '<br />' || '(' || m1.strStockItemName || ')'
	END AS s401i8
	/* ミニブック */
	,CASE WHEN m1.lngStockSubjectCode = 401 AND m1.lngStockItemCode = 5
		THEN m1.strOrderCode || '-' || strReviseCode || '<br />' || '(' || m1.strStockItemName || ')'
	END AS s401i5
	/* カートン版 */
	,CASE WHEN m1.lngStockSubjectCode = 401 AND m1.lngStockItemCode = 12
		THEN m1.strOrderCode || '-' || strReviseCode || '<br />' || '(' || m1.strStockItemName || ')'
	END AS s401i12
	/* MassProduct */
	,CASE WHEN m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 1
		THEN m1.strOrderCode || '-' || strReviseCode || '<br />' || '(' || m1.strStockItemName || ')'
	END AS s402i1
	/* Smple (401-1 〜 401-13) */
	,CASE WHEN 	(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 2) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 3) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 4) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 5) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 6) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 7) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 8) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 9) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 10) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 11) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 12) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 13) 
		THEN m1.strOrderCode || '-' || strReviseCode || '<br />' || '(' || m1.strStockItemName || ')'
	END AS s402
	/* 上記の仕入科目を除く */
	,CASE WHEN	(m1.lngStockSubjectCode = 431 AND m1.lngStockItemCode = 1) OR
				(m1.lngStockSubjectCode = 431 AND m1.lngStockItemCode = 4) OR
				(m1.lngStockSubjectCode = 431 AND m1.lngStockItemCode = 3) OR
				(m1.lngStockSubjectCode = 431 AND m1.lngStockItemCode = 7) OR
				(m1.lngStockSubjectCode = 433 AND m1.lngStockItemCode = 4) OR
				(m1.lngStockSubjectCode = 403 AND m1.lngStockItemCode = 1) OR
				(m1.lngStockSubjectCode = 433 AND m1.lngStockItemCode = 2) OR
				(m1.lngStockSubjectCode = 431 AND m1.lngStockItemCode = 8) OR
				(m1.lngStockSubjectCode = 433 AND m1.lngStockItemCode = 1) OR
				(m1.lngStockSubjectCode = 401 AND m1.lngStockItemCode = 8) OR
				(m1.lngStockSubjectCode = 401 AND m1.lngStockItemCode = 5) OR
				(m1.lngStockSubjectCode = 401 AND m1.lngStockItemCode = 12) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 1) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 2) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 3) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 4) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 5) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 6) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 7) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 8) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 9) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 10) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 11) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 12) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 13) 
		THEN ''
		/* 99- その他 */
		WHEN 	(m1.lngStockSubjectCode = 401 AND m1.lngStockItemCode = 99) OR
				(m1.lngStockSubjectCode = 402 AND m1.lngStockItemCode = 99) OR
				(m1.lngStockSubjectCode = 403 AND m1.lngStockItemCode = 99) OR
				(m1.lngStockSubjectCode = 420 AND m1.lngStockItemCode = 99) OR
				(m1.lngStockSubjectCode = 431 AND m1.lngStockItemCode = 99) OR
				(m1.lngStockSubjectCode = 433 AND m1.lngStockItemCode = 99) 
		THEN m1.strOrderCode || '-' || strReviseCode || '<br />' || '(' || m1.strStockItemName || ')：' || strDetailNote
		ELSE m1.strOrderCode || '-' || strReviseCode || '<br />' || '(' || m1.strStockItemName || ')'
	END AS siother
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
WHERE
	mp.dtmDeliveryLimitDate between '_%date_from%_' AND '_%date_to%_'
	AND mp.lngInChargeGroupCode = _%lnggroupcode%_
	_%column_lngusercode_enable%_ AND mp.lngInChargeUserCode = _%lngusercode%_
ORDER BY mp.strproductcode
