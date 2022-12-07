<?php

namespace App\Models;
use App\Lib\Model;
use App\Exceptions\ClassException;
use App\Exceptions\MailException;
use App\Lib\Logger;
use App\Lib\Mail;
class User extends Model
{
    protected static $table_name = "users";
    protected $id = 0;
    protected $username;
    protected $password;
    protected $email;
    protected $verify = "";
    protected $active = 0;
    public static $errorArray = array(
        'pass' => 'Passwords do not match!',
        'taken' => 'Username taken, please use another.',
        'no' => 'Incorrect login details!',
        'failedlogin' => 'Incorrect login, please try again!'
    );

    public function __construct($username, $password, $email){
        $this->username = $username;
        $this->password = password_hash($password ?? "", PASSWORD_BCRYPT, ['cost' => 10]);
        $this->email = $email;
        $this->verify = $this->randStr();
    }
    public static function auth(string $email, string $password) {
        try {
            $user = User::findFirst(["email" => $email]);
            return $user;

            if(password_verify($password, $user->get('password')))
                return $user;
        } catch(ClassException $e) {
        }
        return false;
    }
    private function randStr():string {
        return substr(md5(rand()),0,16);
    }

    public function mailUser():bool{
        $verifystring = urlencode($this->verify);
        $email = urlencode($this->email);
        $url = CONFIG_URL;
        $mail_body = <<<_MAIL_
Hi $this->username,\n\n

Please click on the following link to verify your new account :
<a href="{$url}/verify.php?email=$email&verify=$verifystring">Click Here</a.
_MAIL_;
try{
    return Mail::sendMail($this->email, CONFIG_AUCTIONNAME .
                                                " user verification", $mail_body);
}catch(MailException $e){
    Logger::getLogger()->critical("could not send mail: " , ['exception'=>$e]);
    return false;
}
    }
}