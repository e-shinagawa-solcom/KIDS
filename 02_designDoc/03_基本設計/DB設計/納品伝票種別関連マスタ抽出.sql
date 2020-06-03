select 
att.lngcompanycode,
case company.lngcountrycode
when 81 then 1
else 3 end as typecode,
company.strcompanyname

from m_attributerelation att
inner join m_company company
    on company.lngcompanycode = att.lngcompanycode
where att.lngattributecode=2
