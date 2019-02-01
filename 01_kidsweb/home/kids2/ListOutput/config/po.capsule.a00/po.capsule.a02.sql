SELECT 
	TO_CHAR(now(), 'YYYY/MM/DD HH24:MI:SS') AS querydate
	,TO_CHAR(to_date('_%date_from%_','YYYY-MM-DD'), 'YYYY年MM月DD日')    AS date_from
	,TO_CHAR(to_date('_%date_to%_','YYYY-MM-DD'), 'YYYY年MM月DD日')      AS date_to
	,'_%productno_in%_'         AS productno_in
	,'_%template%_'             AS templatename
	,'_%date_from%_'            AS get_date_from
	,'_%date_to%_'              AS get_date_to
	,mg.lngGroupCode			AS lngGroupCode
	,CASE WHEN _%column_lngusercode_flag%_
		THEN mu.lngUserCode
		ELSE '0'
	END AS lngUserCode
	,mg.strGroupDisplayName		AS strGroupDisplayName
	,CASE WHEN _%column_lngusercode_flag%_
		THEN mu.strUserDisplayName
		ELSE '＜全て＞'
	END AS strUserDisplayName
	,_%lngorderstatuscode%_     AS lngOrderStatusCode
	,CASE WHEN _%column_strorderstatusname_flag%_
		THEN (select strOrderStatusName from m_OrderStatus where lngOrderStatusCode = _%lngorderstatuscode%_)
		ELSE '＜全て＞'
	END AS strOrderStatusName
FROM
	m_grouprelation mgr
	,m_group mg
	,m_user mu
WHERE
	mgr.lnggroupcode = mg.lnggroupcode
	AND mgr.lngusercode = mu.lngusercode
	AND mg.lngGroupCode = '_%lnggroupcode%_'
	_%column_lngusercode_enable%_ AND mu.lngUserCode = '_%lngusercode%_'
limit 1
