<?php
namespace App\Lib;

use App\Exceptions\MailException;

class Mail{

    public static function sendMail(string $to , string $subject, string $body):bool{
        $headers = 'MIME-Version: 1.0'."\r\n";
        $headers .= 'Content-Type: text/html; charset=iso-8859-1'."\r\n";
        $result= mail($to,$subject,$body,$headers);
        if(!$result) throw new MailException("Internal Error: Cannot send mail");
        return $result;
    }
}