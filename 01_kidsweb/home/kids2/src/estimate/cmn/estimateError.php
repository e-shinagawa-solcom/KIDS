<?php

require ( 'conf.inc' );										// �����ɤ߹���
require ( LIB_DEBUGFILE );									// Debug�⥸�塼��

require ( LIB_FILE );										// �饤�֥���ɤ߹���

$aryData = $_POST;

$message = $aryData['message'];

// [lngLanguageCode]�񤭽Ф�
$aryHtml["lngLanguageCode"] = $aryData["lngLanguageCode"] ? $aryData["lngLanguageCode"] : '0';

// [strErrorMessage]�񤭽Ф�
$aryHtml["strErrorMessage"] = "ERROR! ". $message;

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "/result/error/parts.tmpl" );

// �ƥ�ץ졼������
$objTemplate->replace( $aryHtml );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;

exit;