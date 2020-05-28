SELECT
  moldreportid
  , revision
  , reportcategory
  , status
  , requestdate
  , sendto
  , attention
  , carboncopy
  , productcode
  , strrevisecode
  , goodscode
  , requestcategory
  , actionrequestdate
  , actiondate
  , transfermethod
  , sourcefactory
  , mm_sour.strfactorydisplaycode as sour_strfactorydisplaycode
  , mm_sour.strfactorydisplayname as sour_strfactorydisplayname
  , mm_sour.strinchargeattention as sour_strinchargeattention
  , mm_sour.strinchargecc as sour_strinchargecc
  , mm_sour.strforeignfactoryname as sour_strforeignfactoryname
  , mm_sour.strforeignfactoryaddress as sour_strforeignfactoryaddress
  , mm_sour.stractionusertel as sour_stractionusertel
  , mm_sour.strmoveactionuser as sour_strmoveactionuser
  , mm_desc.strfactorydisplaycode as desc_strfactorydisplaycode
  , mm_desc.strfactorydisplayname as desc_strfactorydisplayname
  , mm_desc.strinchargeattention as desc_strinchargeattention
  , mm_desc.strinchargecc as desc_strinchargecc
  , mm_desc.strforeignfactoryname as desc_strforeignfactoryname
  , mm_desc.strforeignfactoryaddress as desc_strforeignfactoryaddress
  , mm_desc.stractionusertel as desc_stractionusertel
  , mm_desc.strmoveactionuser as desc_strmoveactionuser
  , destinationfactory
  , instructioncategory
  , customercode
  , kuwagatagroupcode
  , kuwagatausercode
  , note
  , finalkeep
  , returnschedule
  , marginalnote
  , printed
  , created
  , createby
  , updated
  , updateby
  , version
  , deleteflag 
FROM
  m_moldreport 
  left join m_moldmoverequestfactory mm_sour 
    on sourcefactory = mm_sour.lngfactorycode 
  left join m_moldmoverequestfactory mm_desc 
    on destinationfactory = mm_desc.lngfactorycode 
WHERE
    moldreportid = $1
AND revision = $2
AND version = $3
AND deleteflag = false
;
