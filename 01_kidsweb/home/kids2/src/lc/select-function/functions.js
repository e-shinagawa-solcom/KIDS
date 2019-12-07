

//@-------------------------------------------------------------------------------------------------------------------
/**
* �ե����복�� : LC��Ϣ����
*
*
*
* @package k.i.d.s.
* @license http://www.wiseknot.co.jp/
* @copyright Copyright &copy; 2004, Wiseknot, Inc.
* @author Ryosuke Tomita <r-tomita@wiseknot.co.jp>
* @access public
* @version 0.1
*/
//--------------------------------------------------------------------------------------------------------------------

//�����ѿ�
var session_id;

//---------------------------------------------------
// ���̽������
//---------------------------------------------------
function lcInit( json_obj )
{
	var phpData = JSON.parse(json_obj);
	session_id = phpData.session_id;

	//userAuth��2���ܤ�1�ξ��ϡ�L/C�����ѹ��פ����Ѳ�ǽ
	var auth = phpData.login_user_auth.substr(1, 1);
	if(auth != "1")
	{
		$("#lcsetBtn").prop("disabled", true);
	}

	//Ʊ�츢�¤Υ桼������������Ѥߤξ��Υ���ե�����
	if(phpData.logined_flg == true){
		if( res = confirm("�����Υ��������ϥ������Ȥ��Ƥ��ޤ���\r\n" + phpData.lcInfoDate.lcgetdate + " " + phpData.lcInfoDate.lgusrname  + "�����������Ȥ�¹Ԥ��ޤ�����") )
		{
			$.ajax({
				url:'../lcModel/lcinfo_ajax.php',
				type:'POST',
				data:{
					'method': 'logoutState',
					'lgno': phpData.login_state.login_obj.lgno
				}
			})
			// Ajax�ꥯ�����Ȥ�����������ȯư
			.done( (data) => {
			})
			// Ajax�ꥯ�����Ȥ����Ԥ�����ȯư
			.fail( (data) => {
			})
			// Ajax�ꥯ�����Ȥ����������Ԥɤ���Ǥ�ȯư
			.always( (data) => {

			});
		}
	}
}

/**
 * L/C��������������
 * @param {} sessionId 
 */
function initLcinfo(sessionId)
{
	$.ajax({
		url:'../info/init.php',
		type:'POST',
		data:{
			'strSessionID': sessionId
		}
	})
	.done(function(data) {
		// Ajax�ꥯ�����Ȥ�����
		var data = JSON.parse(data);
		if (data.lcgetdate != "" && data.lcgetdate != null) {
			if( res = confirm("����L/C����μ�����ԤäƤ��ޤ����ǿ��ξ����������ޤ�����")) 
			{
				location.href="/lc/info/index.php?strSessionID=" + data.strSessionID + "&aclcinitFlg=true"
			} else {
				location.href="/lc/info/index.php?strSessionID=" + data.strSessionID + "&aclcinitFlg=false"			
			}
		}
		else
		{
			location.href="/lc/info/index.php?strSessionID=" + data.strSessionID + "&aclcinitFlg=true"
		}
	})
	.fail(function() {
		alert("fail");
		// Ajax�ꥯ�����Ȥ�����
	});
}
