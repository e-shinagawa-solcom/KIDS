

function OpenWin2(wUrl,wWidth,wHeight)
{
   scWidthCenter=screen.availWidth/2;
   scHeightCenter=screen.availHeight/2;
   wOption = 
"scrollbars=yes,resizable=yes,width="+wWidth+",height="+wHeight+",left="+(scWidthCenter-(wWidth/2))+",top="+(scHeightCenter-(wHeight/2));
   window.open(wUrl,'window1',wOption);
}



function GoResult2()
{
	document.PS.target = "window1";
alert("test");
	OpenWin2('body.html',900,600);
	document.PS.action = "../result/index.php";
	document.PS.submit();
}
