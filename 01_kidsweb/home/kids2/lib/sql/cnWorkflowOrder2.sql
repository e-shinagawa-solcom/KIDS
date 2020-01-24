/*
	概要：「ワークフローグループコード」から「ユーザー」を取得
	対象：マスタ管理
	作成：chiba
*/
SELECT u.lngUserCode, u.strUserDisplayName || ':' || ag.strAuthorityGroupName
FROM m_User u, m_GroupRelation gr, m_AuthorityGroup ag
WHERE gr.lngGroupCode = _%strFormValue0%_
 AND ag.lngAuthorityLevel >= 100
 AND ag.lngAuthorityLevel <= 
(
  SELECT ag2.lngAuthorityLevel
  FROM m_User u2, m_AuthorityGroup ag2
  WHERE u2.lngUserCode = _%strFormValue1%_ 
   AND u2.lngAuthorityGroupCode = ag2.lngAuthorityGroupCode
)
_%strFormValue2%_
 AND u.bytInvalidFlag = FALSE
 AND u.lngUserCode = gr.lngUserCode
 AND u.lngAuthorityGroupCode = ag.lngAuthorityGroupCode
ORDER BY u.lngAuthorityGroupCode
