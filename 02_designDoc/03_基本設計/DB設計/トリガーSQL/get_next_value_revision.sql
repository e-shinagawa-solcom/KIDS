CREATE OR REPLACE FUNCTION public.get_next_value_revision(identifier text)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$
                DECLARE
                    lastValue integer;
                    nextValue integer;
                BEGIN
                    SELECT INTO lastValue max(revision) from m_moldreport where moldreportid = identifier;
                    
                    IF (lastValue IS NULL) THEN

                        nextValue := 0;

                    ELSEIF (0 <= lastValue) THEN

                        nextValue := lastValue + 1;

                    ELSE
                    
                        nextValue := 0;
                    
                    END IF;
                    
                    RETURN nextValue;
                END;
                $function$
;
