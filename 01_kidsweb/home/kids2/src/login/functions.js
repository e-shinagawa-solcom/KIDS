

function fncPasswdRemind( strURL )
{

	retVal = window.showModalDialog( strURL , '1' , 'dialogWidth:698px; dialogHeight:660px; scroll:no; edge:raised; center:; help:no; resizable:no; status:no; unadorned:yes;' );

	//remindW = window.open( strURL , 'passWin' , 'width=698 , height=630 , status=no , scrollbars=no , directories=no , menubar=no , resizable=no , location=no , toolbar=no , left=10 , top=10' );

	//location.href = strURL;

	return false;
}