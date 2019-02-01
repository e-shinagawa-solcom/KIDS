/*
	���ס������ʥ����ɡפ��������̾�Ρפ����
	�оݡ�ȯ��������������������������������
	������watanabe
	��������ƣ�»�
	���͡��֥����ɡפ�����פ����̾�Ρפ����
*/
SELECT mp.lngproductno,
	CASE WHEN mp.strproductname IS NULL THEN '������̾�Τ����Ǥ���'
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
