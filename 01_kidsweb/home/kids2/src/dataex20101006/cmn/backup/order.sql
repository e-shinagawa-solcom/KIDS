/*
	概要：経理(L/C予定表情報)　「L/Cデータ」「計上日指定」
	対象：データエクスポート
	作成：chiba
	備考：

	更新履歴：
	2004.04.07	LC予定表に以下のカラムを追加	明細行番号（ソートキー）、リバイズコード、支払条件
	2004.04.07	ORDER BY にて発注Noの昇順、明細行番号の昇順にソートされるように変更
	2004.04.27	最新発注の取得内容を修正
	2004.05.13	納品場所の追加
	2004.05.13	入力日、変更日に正確な情報を設定するよう修正
	2004.05.19	LC予定表は新規（リビジョン番号 0）のみ リバイズに更新のデータ（削除データ含む）を抽出するように修正
				また、発注の状態を表示するように修正
*/
SELECT
	o.strOrderCode
	,od.lngSortKey
	,o.strReviseCode
	,
	/* 削除データかどうか示す */
	CASE WHEN o.lngRevisionNo < 0 AND o.bytInvalidFlag = FALSE
	       THEN '削除'
	  WHEN o.lngRevisionNo < 0 AND o.bytInvalidFlag = TRUE
	       THEN '削除後有効化'
	  WHEN o.lngOrderStatusCode = 1
	       THEN '未承認'
	  WHEN o.lngOrderStatusCode = 3
	       THEN '納品'
	  WHEN o.lngOrderStatusCode = 4
	       THEN '納品'
	  WHEN o.lngOrderStatusCode = 99
	       THEN '納品'
	  WHEN 0 = 
	      (
	        SELECT COUNT ( * ) 
	        FROM m_Workflow mw 
	        WHERE to_number ( mw.strWorkflowKeyCode, '9999999') = o.lngOrderNo
	         AND mw.lngFunctionCode = 501
	      )
	       THEN '承認済'
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
	       THEN '承認済'
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
	       THEN '未承認'
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
	       THEN '未承認'
	  ELSE '未承認'
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
/* 条件：1.L/Cのデータ 2.リビジョンNOが0以上のデータ */
WHERE
	_%lngExportConditions%_
	AND o.strOrderCode >= '_%strOrderCodeFrom%_'
	AND o.strOrderCode <= '_%strOrderCodeTo%_'
	AND o.bytInvalidFlag        = FALSE
/* ORDER BY の設定 */
 ORDER BY o.strOrderCode, od.lngSortKey
