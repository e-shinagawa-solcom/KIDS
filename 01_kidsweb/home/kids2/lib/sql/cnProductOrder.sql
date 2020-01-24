/*
	概要：「製品コード」から「製品名称」を取得
	対象：発注管理、仕入管理、受注管理、売上管理
	作成：watanabe
	更新：斎藤和志
	備考：「コード」から一致する「名称」を取得
*/
SELECT mp.lngproductno,
	CASE WHEN mp.strproductname IS NULL THEN '（製品名称が空です）'
		ELSE mp.strproductname
	END
FROM m_product mp
WHERE mp.bytinvalidflag = false
	AND mp.strproductcode = 
	(
		SELECT distinct on (e.strProductCode) e.strProductCode
		 FROM m_Estimate e
		 LEFT JOIN m_Workflow w ON ( e.lngEstimateNo = to_number(w.strWorkflowKeyCode, '9999999') AND w.lngFunctionCode = 1500)
		 LEFT JOIN m_WorkflowOrder wo ON ( w.lngWorkflowOrderCode = wo.lngWorkflowOrderCode )
		 LEFT JOIN m_User inchag_u2 ON ( wo.lngInChargeCode = inchag_u2.lngUserCode )
		 WHERE e.strProductCode = '_%strFormValue0%_'
		 AND (
		      10 = 
		      (
		        SELECT distinct on (tw.lngWorkflowStatusCode) lngWorkflowStatusCode 
		        FROM m_Workflow mw2, t_Workflow tw
		        WHERE to_number ( mw2.strWorkflowKeyCode, '9999999') = e.lngEstimateNo
		         AND mw2.lngFunctionCode = 1500
		         AND mw2.lngWorkflowCode = 
		         (
		           SELECT MAX ( mw3.lngWorkflowCode ) FROM m_Workflow mw3 
		           WHERE mw2.strWorkflowKeyCode = mw3.strWorkflowKeyCode 
		         )
		         AND tw.lngWorkflowSubCode =
		        (
		          SELECT MAX ( tw2.lngWorkflowSubCode ) FROM t_Workflow tw2 WHERE tw.lngWorkflowCode = tw2.lngWorkflowCode
		        )
		         AND mw2.lngWorkflowCode = tw.lngWorkflowCode
		      )
		    )
	)
