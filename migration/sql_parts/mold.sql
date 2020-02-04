DO $$

declare
    cur_m_mold cursor for
    select * from dblink('con111',
        'select ' ||
            'moldno ' ||
           ',vendercode ' ||
           ',productcode ' ||
           ',created ' ||
           ',createby ' ||
           ',updated ' ||
           ',updateby ' ||
           ',version ' ||
           ',deleteflag ' ||
        'from m_mold '
    ) AS T1(
        moldno text
       ,vendercode integer
       ,productcode text
       ,created timestamp without time zone
       ,createby integer
       ,updated timestamp without time zone
       ,updateby integer
       ,version integer
       ,deleteflag boolean
    );
    
    cur_m_moldreport cursor for
    select * from dblink('con111',
        'select ' ||
            'moldreportid, ' ||
            'revision, ' ||
            'reportcategory, ' ||
            'status, ' ||
            'requestdate, ' ||
            'sendto, ' ||
            'attention, ' ||
            'carboncopy, ' ||
            'productcode, ' ||
            'goodscode, ' ||
            'requestcategory, ' ||
            'actionrequestdate, ' ||
            'actiondate, ' ||
            'transfermethod, ' ||
            'sourcefactory, ' ||
            'destinationfactory, ' ||
            'instructioncategory, ' ||
            'customercode, ' ||
            'kuwagatagroupcode, ' ||
            'kuwagatausercode, ' ||
            'note, ' ||
            'finalkeep, ' ||
            'returnschedule, ' ||
            'marginalnote, ' ||
            'printed, ' ||
            'created, ' ||
            'createby, ' ||
            'updated, ' ||
            'updateby, ' ||
            'version, ' ||
            'deleteflag ' ||
        'from m_moldreport '
    ) AS T1(
        moldreportid text,
        revision integer,
        reportcategory character(2),
        status character(2),
        requestdate date,
        sendto integer,
        attention integer,
        carboncopy integer,
        productcode text,
        goodscode text,
        requestcategory character(2),
        actionrequestdate date,
        actiondate date,
        transfermethod character(2),
        sourcefactory integer,
        destinationfactory integer,
        instructioncategory text,
        customercode integer,
        kuwagatagroupcode integer,
        kuwagatausercode integer,
        note text,
        finalkeep character(2),
        returnschedule date,
        marginalnote text,
        printed boolean,
        created timestamp without time zone,
        createby integer,
        updated timestamp without time zone,
        updateby integer,
        version integer,
        deleteflag boolean
    );
    
    cur_t_moldhistory cursor for
    select * from dblink('con111',
        'select ' ||
            'moldno ' ||
            ',historyno ' ||
            ',status ' ||
            ',actiondate ' ||
            ',sourcefactory ' ||
            ',destinationfactory ' ||
            ',remark1 ' ||
            ',remark2 ' ||
            ',remark3 ' ||
            ',remark4 ' ||
            ',created ' ||
            ',createby ' ||
            ',updated ' ||
            ',updateby ' ||
            ',version ' ||
            ',deleteflag ' ||
        'from t_moldhistory '
    ) AS T1(
        moldno text
       ,historyno integer
       ,status character(2)
       ,actiondate date
       ,sourcefactory integer
       ,destinationfactory integer
       ,remark1 text
       ,remark2 text
       ,remark3 text
       ,remark4 text
       ,created timestamp without time zone
       ,createby integer
       ,updated timestamp without time zone
       ,updateby integer
       ,version integer
       ,deleteflag boolean
    )
    ;

    cur_t_moldreportdetail cursor for
    select * from dblink('con111',
        'select ' ||
            'moldreportid, ' ||
            'revision, ' ||
            'listorder, ' ||
            'moldno, ' ||
            'molddescription, ' ||
            'created, ' ||
            'createby, ' ||
            'updated, ' ||
            'updateby, ' ||
            'version, ' ||
            'deleteflag ' ||
        'from t_moldreportdetail '
    ) AS T1(
        moldreportid text,
        revision integer,
        listorder integer,
        moldno text,
        molddescription text,
        created timestamp(6) without time zone,
        createby integer,
        updated timestamp(6) without time zone,
        updateby integer,
        version integer,
        deleteflag boolean
    )
    ;

    cur_t_moldreportrelation cursor for
    select * from dblink('con111',
        'select ' ||
            'moldreportrelationid, ' ||
            'moldno, ' ||
            'historyno, ' ||
            'moldreportid, ' ||
            'revision, ' ||
            'created, ' ||
            'createby, ' ||
            'updated, ' ||
            'updateby, ' ||
            'version, ' ||
            'deleteflag ' ||
        'from t_moldreportrelation '
    ) AS T1(
        moldreportrelationid integer,
        moldno text,
        historyno integer,
        moldreportid text,
        revision integer,
        created timestamp without time zone,
        createby integer,
        updated timestamp without time zone,
        updateby integer,
        version integer,
        deleteflag boolean
    )
    ;

    r RECORD;

begin

    delete from m_mold;
    open cur_m_mold;
    LOOP
        FETCH cur_m_mold into r;
        EXIT WHEN NOT FOUND;
        insert into m_mold
        (
            moldno,
            vendercode,
            productcode,
            strrevisecode,
            created,
            createby,
            updated,
            updateby,
            version,
            deleteflag
        )
        values
        (
            r.moldno,
            r.vendercode,
            r.productcode,
            '00',
            r.created,
            r.createby,
            r.updated,
            r.updateby,
            r.version,
            r.deleteflag
        );
    END LOOP;
    close cur_m_mold;

    delete from m_moldreport;
    open cur_m_moldreport;
    LOOP
        FETCH cur_m_moldreport into r;
        EXIT WHEN NOT FOUND;
        insert into m_moldreport
        (
            moldreportid,
            revision,
            reportcategory,
            status,
            requestdate,
            sendto,
            attention,
            carboncopy,
            productcode,
            strrevisecode,
            goodscode,
            requestcategory,
            actionrequestdate,
            actiondate,
            transfermethod,
            sourcefactory,
            destinationfactory,
            instructioncategory,
            customercode,
            kuwagatagroupcode,
            kuwagatausercode,
            note,
            finalkeep,
            returnschedule,
            marginalnote,
            printed,
            created,
            createby,
            updated,
            updateby,
            version,
            deleteflag
        )
        values
        (
            r.moldreportid,
            r.revision,
            r.reportcategory,
            r.status,
            r.requestdate,
            r.sendto,
            r.attention,
            r.carboncopy,
            r.productcode,
            '00',
            r.goodscode,
            r.requestcategory,
            r.actionrequestdate,
            r.actiondate,
            r.transfermethod,
            r.sourcefactory,
            r.destinationfactory,
            r.instructioncategory,
            r.customercode,
            r.kuwagatagroupcode,
            r.kuwagatausercode,
            r.note,
            r.finalkeep,
            r.returnschedule,
            r.marginalnote,
            r.printed,
            r.created,
            r.createby,
            r.updated,
            r.updateby,
            r.version,
            r.deleteflag
        );
    END LOOP;
    close cur_m_moldreport;

    delete from t_moldhistory;
    open cur_t_moldhistory;
    LOOP
        FETCH cur_t_moldhistory into r;
        EXIT WHEN NOT FOUND;
        insert into t_moldhistory
        (
            moldno,
            historyno,
            status,
            actiondate,
            sourcefactory,
            destinationfactory,
            remark1,
            remark2,
            remark3,
            remark4,
            created,
            createby,
            updated,
            updateby,
            version,
            deleteflag
        )
        values
        (
            r.moldno,
            r.historyno,
            r.status,
            r.actiondate,
            r.sourcefactory,
            r.destinationfactory,
            r.remark1,
            r.remark2,
            r.remark3,
            r.remark4,
            r.created,
            r.createby,
            r.updated,
            r.updateby,
            r.version,
            r.deleteflag
        );
    END LOOP;
    close cur_t_moldhistory;

    delete from t_moldreportdetail;
    open cur_t_moldreportdetail;
    LOOP
        FETCH cur_t_moldreportdetail into r;
        EXIT WHEN NOT FOUND;
        insert into t_moldreportdetail
        (
            moldreportid,
            revision,
            listorder,
            moldno,
            molddescription,
            created,
            createby,
            updated,
            updateby,
            version,
            deleteflag
        )
        values
        (
            r.moldreportid,
            r.revision,
            r.listorder,
            r.moldno,
            r.molddescription,
            r.created,
            r.createby,
            r.updated,
            r.updateby,
            r.version,
            r.deleteflag
        );
    END LOOP;
    close cur_t_moldreportdetail;

    delete from t_moldreportrelation;
    open cur_t_moldreportrelation;
    LOOP
        FETCH cur_t_moldreportrelation into r;
        EXIT WHEN NOT FOUND;
        insert into t_moldreportrelation
        (
            moldreportrelationid,
            moldno,
            historyno,
            moldreportid,
            revision,
            created,
            createby,
            updated,
            updateby,
            version,
            deleteflag
        )
        values
        (
            r.moldreportrelationid,
            r.moldno,
            r.historyno,
            r.moldreportid,
            r.revision,
            r.created,
            r.createby,
            r.updated,
            r.updateby,
            r.version,
            r.deleteflag
        );
    END LOOP;
    close cur_t_moldreportrelation;

    update t_moldhistory
    set moldno = substring(moldno,1,5) || '_00-' || substring(moldno,7,2);

    update t_moldreportdetail
    set moldno = substring(moldno,1,5) || '_00-' || substring(moldno,7,2);

    update t_moldreportrelation
    set moldno = substring(moldno,1,5) || '_00-' || substring(moldno,7,2);

    update m_mold
    set moldno = substring(moldno,1,5) || '_00-' || substring(moldno,7,2);

    update t_stockdetail
    set strmoldno = substring(strmoldno,1,5) || '_00-' || substring(strmoldno,7,2)
    where (strmoldno is not null) or (strmoldno = '');
    
    -- t_sequenceの金型番号移行
    update t_sequence 
    set strsequencename = strsequencename || '_00'
    where strsequencename like 'm_OrderDetail.strMoldNo._____';

END $$