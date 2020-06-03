CREATE OR REPLACE FUNCTION public.set_histroy_no()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
                BEGIN
                    IF (TG_OP = 'INSERT' AND TG_RELNAME = 't_moldhistory') THEN

                        NEW.historyno := get_next_value_history_no(NEW.MoldNo);

                    END IF;
                    RETURN NEW;
                END;
                $function$
;

CREATE TRIGGER trg_bi_history_no_mold_history BEFORE INSERT ON t_moldhistory FOR EACH ROW EXECUTE PROCEDURE set_histroy_no();

