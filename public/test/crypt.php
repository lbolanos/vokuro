<?php


use Phalcon\Crypt;

// Create an instance
$crypt = new Crypt();

// Use your own key!
//$key = "T4\xb1\x8d\xa9\x98\x05\\\x8c\xbe\x1d\x07&[\x99\x18\xa4~Lc1\xbeW\xb3";
$cryptSalt = 'eEAfR|_&G&f,+vU]:jFr!!A&+71w1Ms9~8_4L!<@[N@DyaIP_2My|:+.u>/6m,$D';
$text = '12345678';
$crypt->setKey($cryptSalt);
echo $crypt->getCipher();
echo "\n";
echo $encrypt = $crypt->encryptBase64($text, $key);
echo "\n";
echo $crypt->decryptBase64($encrypt, $key);
echo "\n\n";
echo $encrypt = $crypt->encrypt($text);
echo "\n";
echo $crypt->decrypt($encrypt);


$key = "eEAfR|_&G&f,+vU]";

function encrypt($text,$key){
	$block = mcrypt_get_block_size('rijndael_128', 'ecb');
	$pad = $block - (strlen($text) % $block);
	$text .= str_repeat(chr($pad), $pad);
	return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_ECB));
}

function decrypt($str, $key){
	$str = base64_decode($str);
	$str = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_ECB);
	$block = mcrypt_get_block_size('rijndael_128', 'ecb');
	$pad = ord($str[($len = strlen($str)) - 1]);
	$len = strlen($str);
	$pad = ord($str[$len-1]);
	return substr($str, 0, strlen($str) - $pad);
}

$enc =  encrypt("12345678",$GLOBALS['key']);
echo "\nEncrypted : ".$enc;
$dec = decrypt($enc,$GLOBALS['key']);
echo "\nDecrypted : ".$dec;
echo "\nmd5 : ".md5($enc);