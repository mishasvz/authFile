<?php
class AuthFile
{
    /**
     * Класс для создания и обработки ключ-файлов
     * по которым происходит авторизация пользователей.
     * */
    private $chars = 'abcdefghijklmnopqrstuwyzxv1234567890';
    private $login_length;
    private $start_salt;
    private $salt_start_length;
    private $end_salt;
    private $login;
    private $email;
    private $password;
    
    public function createFile($login,$password,$email){
        $this->login = $login;
        $this->email = $email;
        $this->password = $password;
        $this->login_length = strlen($login);
        if($this->login_length > 9){
            $this->salt_start_length = 25496 - ($this->login_length*5) - 3;
        }
        else{
            $this->salt_start_length = 25496 - ($this->login_length*5) - 2;
        }
        return $this;
    }
    private function generateData(){
        $data $this->login_length.'a'.$this->generateLogin().$this->generateSalt($this->salt_start_length);
        return $data;
    }
    public function generateMasterFile(){
        $data = $this->generateData().$this->generateHash($this->login.$this->email.$this->password).$this->generateSalt(25496);
        return $data;
    }
    public function generateKeyFile(){
        $data = $this->generateData().$this->generateHash($this->password).$this->generateSalt(25496);
        return $data;
    }
    private function generateLogin(){
        for($i = 0; $i < $this->login_length; $i++){
            $data .= substr($this->login,$i,1).substr($this->generateSalt($this->login_length*4),$j,4);
        }
        return $data;
    }
    private function generateHash($data){
        $j = 0;
        for($i = 0; $i <= 80; $i += 20){
            $hash .= substr($this->generateSalt(128),$j,32).substr(hash('ripemd320',$data),$i,20);
            $j += 32;
        }
        return $hash;
    }
    private function generateSalt($length){
        for($i = 0; $i < $length; $i++)
            $salt .= substr($this->chars, rand(1, 35), 1);
        return $salt;
    }
    public function readFile($file,$that){
        $data = file_get_contents($file);
        switch($that){
            case 'login':
                if(substr($data,1,1) == 'a'){
                    $login_length = substr($data,0,1);
                    $login_temp = substr($data,2,$login_length*5);
                }
                else{
                    $login_length = substr($data,0,2);
                    $login_temp = substr($data,3,$login_length*5);
                }
                for($i = 0; $i < $login_length*5; $i +=5){
                    $output .= substr($login_temp,$i,1);
                }
                break;
            case 'hash':
                $hash_temp = substr($data,25496,208);
                for($i = 32; $i < 208; $i += 52){
                    $output .=substr($hash_temp,$i,20);
                }
                break;
            default:
                $output = null;
                break;
        }
        return $output;
    }
}
