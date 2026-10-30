<?PHP

$config = array('config'=>"C:\wamp64\bin\apache\apache2.4.65\conf\openssl.cnf");
$pkey = openssl_pkey_new($config);
$crs = openssl_csr_new('MyCRS',$pkey,$config);


?>