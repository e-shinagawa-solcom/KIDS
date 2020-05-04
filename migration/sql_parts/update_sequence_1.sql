DO $$
declare
cur cursor for
    select * from dblink('con111','
select
    strsequencename
   ,lngsequence
from t_sequence
where strsequencename like ''%.20%''
') AS T1(
    strsequencename text
   ,lngsequence integer
);

rec RECORD;
begin

    open cur;
    LOOP
        FETCH cur INTO rec;
        EXIT WHEN NOT FOUND;
RAISE INFO '% %', rec.lngsequence, rec.strsequencename;
        update t_sequence
        set lngsequence = rec.lngsequence
        where strsequencename = rec.strsequencename;
    END LOOP;

    close cur;
END $$