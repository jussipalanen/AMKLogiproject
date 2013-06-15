<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
        private $_id;
        
	public function authenticate()
	{
            $username=strtolower($this->username);
            $user = User::model()->find('LOWER(username)=?',array($username));
            if($user == null)
            {
                //echo "Väärät nimi";
                $this->errorCode=self::ERROR_USERNAME_INVALID;
            }
            else if($this->username != $user->username)
            {
                echo "Väärät tunnukset";
                $this->errorCode=self::ERROR_USERNAME_INVALID;
            }
            else if(md5($this->password) != $user->password)
            {
                echo "Väärät salasanat!";
                $this->errorCode=self::ERROR_PASSWORD_INVALID;
            }
            else
            {
                $this->_id=$user->uid;
                $this->username=$user->username;
                $this->setState("roles", $user->role);
                $this->errorCode=self::ERROR_NONE;
            }
            return $this->errorCode==self::ERROR_NONE;
	}
        
        public function getID()
        {
            return $this->_id;
        }
}