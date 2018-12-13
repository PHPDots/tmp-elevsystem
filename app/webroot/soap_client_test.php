<?
ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 900);
ini_set('default_socket_timeout', 600);


$params = array('KundeID'=>'2795cd76-0a62-4f1c-994b-f5bfbdbf24d1', 'Lokationsnummer'=>99);


$wsdl = 'http://elevdata.jb-edb.dk/debitor/debitorservice.asmx?wsdl';

$options = array(
		'uri'=>'http://schemas.xmlsoap.org/soap/envelope/',
		'style'=>SOAP_RPC,
		'use'=>SOAP_ENCODED,
		'soap_version'=>SOAP_1_1,
		'cache_wsdl'=>WSDL_CACHE_NONE,
		'connection_timeout'=>15,
		'trace'=>true,
		'encoding'=>'UTF-8',
		'exceptions'=>true,
	);
try {
	$soap = new SoapClient($wsdl, $options);
	$data = $soap->GetDebitorListe($params);
}
catch(Exception $e) {
	die($e->getMessage());
}

echo "<pre>";
print_r($data);
//var_dump($data);
die;

?>