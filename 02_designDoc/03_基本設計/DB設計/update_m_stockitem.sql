update m_stockitem
set lngtargetarea = 3
where lngstocksubjectcode in (403,431,433);

update m_stockitem
set lngtargetarea = 4
where lngstocksubjectcode in (401,402,420);

update m_stockitem
set lngtargetarea = 5
where lngstocksubjectcode in (1224,1230);

update m_stockitem
set lngtargetarea = 5
where lngstocksubjectcode = 401
and lngstockitemcode = 1;

