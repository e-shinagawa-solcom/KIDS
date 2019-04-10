<!--


function fncDataPreview( strURL )
{
	dataW = window.open( strURL , 'dataWin' , 'width=800,height=600,status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no,top=5,left=5' );
	return false;
}



g_aryFrm    = new Array();
g_aryFrm[0] = new Array();
g_aryFrm[1] = new Array();
g_aryFrm[2] = new Array();
g_aryFrm[3] = new Array();

function fncDataFormPreview( objFrm , strURL )
{

	var j = 0;
	var k = 0;

	g_aryFrm	= null;
	g_aryFrm    = new Array();

	/* 定義済みの配列を初期化する（同じ画面で条件を変更された場合に対応） */
	for ( i = 0; i < 4; i++ )
	{
		g_aryFrm[i] = null;
		g_aryFrm[i] = new Array();
	}

	/* フォームのデータを取得する */
	if( typeof(objFrm) != 'undefined' )
	{

		for ( i = 0; i < objFrm.elements.length; i++ )
		{

			if( objFrm.elements[i].type == 'radio' || objFrm.elements[i].type == 'checkbox')
			{
				if( objFrm.elements[i].checked == true )
				{
					g_aryFrm[2][k] = objFrm.elements[i].name;
					g_aryFrm[3][k] = objFrm.elements[i].value;

					k++;
				}

			}

			else
			{

				g_aryFrm[0][j] = objFrm.elements[i].name;
				g_aryFrm[1][j] = objFrm.elements[i].value;

				j++;
			}

		}

	}


	fncDataPreview( strURL );

	return false;
}


//-->