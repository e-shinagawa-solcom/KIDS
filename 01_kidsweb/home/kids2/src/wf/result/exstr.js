<!--


function ChgEtoJ( lngSelfCode )
{
	if ( lngSelfCode == 0 )
	{

		if ( typeof(WF01) != 'undefined' )
		{
			WF01.innerHTML='Detail';
		}

		if ( typeof(WF02) != 'undefined' )
		{
			WF02.innerHTML='<a href="#">Application day</a>';
		}

		if ( typeof(WF03) != 'undefined' )
		{
			WF03.innerHTML='<a href="#">Issue information</a>';
		}

		if ( typeof(WF04) != 'undefined' )
		{
			WF04.innerHTML='<a href="#">Applicant</a>';
		}

		if ( typeof(WF05) != 'undefined' )
		{
			WF05.innerHTML='<a href="#">Input person</a>';
		}

		if ( typeof(WF06) != 'undefined' )
		{
			WF06.innerHTML='<a href="#">Recognition person</a>';
		}

		if ( typeof(WF07) != 'undefined' )
		{
			WF07.innerHTML='<a href="#">Limit date</a>';
		}

		if ( typeof(WF08) != 'undefined' )
		{
			WF08.innerHTML='<a href="#">State</a>';
		}

		if ( typeof(WF09) != 'undefined' )
		{
			WF09.innerHTML='Disposal';
		}

		if ( typeof(WF10) != 'undefined' )
		{
			WF10.innerHTML='Finish';
		}

	}

	else if ( lngSelfCode == 1 )
	{

		if ( typeof(WF01) != 'undefined' )
		{
			WF01.innerHTML='詳細';
		}

		if ( typeof(WF02) != 'undefined' )
		{
			WF02.innerHTML='<a href="#">申請日</a>';
		}

		if ( typeof(WF03) != 'undefined' )
		{
			WF03.innerHTML='<a href="#">案件情報</a>';
		}

		if ( typeof(WF04) != 'undefined' )
		{
			WF04.innerHTML='<a href="#">申請者</a>';
		}

		if ( typeof(WF05) != 'undefined' )
		{
			WF05.innerHTML='<a href="#">入力者</a>';
		}

		if ( typeof(WF06) != 'undefined' )
		{
			WF06.innerHTML='<a href="#">承認者</a>';
		}

		if ( typeof(WF07) != 'undefined' )
		{
			WF07.innerHTML='<a href="#">期限日</a>';
		}

		if ( typeof(WF08) != 'undefined' )
		{
			WF08.innerHTML='<a href="#">状態</a>';
		}

		if ( typeof(WF09) != 'undefined' )
		{
			WF09.innerHTML='処理';
		}

		if ( typeof(WF10) != 'undefined' )
		{
			WF10.innerHTML='完了日';
		}

	}

	return false;

}


//-->