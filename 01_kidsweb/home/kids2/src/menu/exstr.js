<!--


function ChgEtoJ()
{


////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngClickCode == 0 )
	{

		///// SET COOKIE /////
		SetlngLanguageCode();



		///// E TO J /////
		EtoJ.innerHTML = etojJbt;


		///// MAIN TITLE /////
		MainTitle.innerHTML = maintitleE;


		///// SYSTEM MESSAGE /////
		if (typeof(ColumnAdminMessage)!='undefined')
		{
			ColumnAdminMessage.innerText = 'SYSTEM MESSAGE';
		}


		///// OBJECT JUDGE /////
		if (typeof(ESnavi)!='undefined')
		{
			ESnavi.innerHTML = esNaviE1;
		}

		if (typeof(Pnavi)!='undefined')
		{
			Pnavi.innerHTML = pNaviE1;
		}

		if (typeof(SOnavi)!='undefined')
		{
			SOnavi.innerHTML = soNaviE1;
		}

		if (typeof(POnavi)!='undefined')
		{
			POnavi.innerHTML = poNaviE1;
		}

		if (typeof(SCnavi)!='undefined')
		{
			SCnavi.innerHTML = scNaviE1;
		}

		if (typeof(PCnavi)!='undefined')
		{
			PCnavi.innerHTML = pcNaviE1;
		}

		if (typeof(WFnavi)!='undefined')
		{
			WFnavi.innerHTML = wfNaviE1;
		}

		/*
		if (typeof(UCnavi)!='undefined')
		{
			UCnavi.innerHTML = ucNaviE1;
		}

		if (typeof(Mnavi)!='undefined')
		{
			Mnavi.innerHTML = mNaviE1;
		}
		*/

		if (typeof(LISTnavi)!='undefined')
		{
			LISTnavi.innerHTML = listNaviE1;
		}

		if (typeof(DATAEXnavi)!='undefined')
		{
			DATAEXnavi.innerHTML = dataexNaviE1;
		}


		if (typeof(UPLOADnavi)!='undefined')
		{
			UPLOADnavi.innerHTML = uploadNaviE1;
		}

		if (typeof(MMnavi)!='undefined')
		{
			MMnavi.innerHTML = mmNaviE1;
		}

		if (typeof(MRnavi)!='undefined')
		{
			MRnavi.innerHTML = mrNaviE1;
		}

		lngLanguageCode = 0;
		lngClickCode = 1;

	}


////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( lngClickCode == 1 )
	{

		///// SET COOKIE /////
		SetlngLanguageCode();


		///// E TO J /////
		EtoJ.innerHTML = etojEbt;


		///// MAIN TITLE /////
		MainTitle.innerHTML = maintitleJ;


		///// SYSTEM MESSAGE /////
		if (typeof(ColumnAdminMessage)!='undefined')
		{
			ColumnAdminMessage.innerText = 'システム メッセージ'
		}


		///// OBJECT JUDGE /////
		if (typeof(ESnavi)!='undefined')
		{
			ESnavi.innerHTML = esNaviJ1;
		}

		if (typeof(Pnavi)!='undefined')
		{
			Pnavi.innerHTML = pNaviJ1;
		}

		if (typeof(SOnavi)!='undefined')
		{
			SOnavi.innerHTML = soNaviJ1;
		}

		if (typeof(POnavi)!='undefined')
		{
			POnavi.innerHTML = poNaviJ1;
		}

		if (typeof(SCnavi)!='undefined')
		{
			SCnavi.innerHTML = scNaviJ1;
		}

		if (typeof(PCnavi)!='undefined')
		{
			PCnavi.innerHTML = pcNaviJ1;
		}

		if (typeof(WFnavi)!='undefined')
		{
			WFnavi.innerHTML = wfNaviJ1;
		}

		/*
		if (typeof(UCnavi)!='undefined')
		{
			UCnavi.innerHTML = ucNaviJ1;
		}

		if (typeof(Mnavi)!='undefined')
		{
			Mnavi.innerHTML = mNaviJ1;
		}
		*/

		if (typeof(LISTnavi)!='undefined')
		{
			LISTnavi.innerHTML = listNaviJ1;
		}

		if (typeof(DATAEXnavi)!='undefined')
		{
			DATAEXnavi.innerHTML = dataexNaviJ1;
		}


		if (typeof(UPLOADnavi)!='undefined')
		{
			UPLOADnavi.innerHTML = uploadNaviJ1;
		}

		if (typeof(MMnavi)!='undefined')
		{
			MMnavi.innerHTML = mmNaviJ1;
		}

		if (typeof(MRnavi)!='undefined')
		{
			MRnavi.innerHTML = mrNaviJ1;
		}

		lngLanguageCode = 1;
		lngClickCode = 0;

	}

	return false;

}


//-->