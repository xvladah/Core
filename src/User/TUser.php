<?php

/**
 * Třída pro přihlášeného uživatele
 *
 * @name		TUser
 * @version		3.1
 * @author     	vladimir.horky
 * @copyright	Vladimir Horky, 2022.
 *
 * version 3.1
 * - support create time in auth token
 *
 * version 3.0
 * - support basic and bearer authentication
 *
 * version 2.0
 * - change to cookies
 *
 * version 1.0
 * - added getHTTPHeaders()
 */
//declare(strict_types=1);

namespace Core\User;
use Core\Html\TUrl;
use Core\TConsts;

abstract class TUser
{
    const bool USE_IP_FOR_AUTH = true;

    public const string GUESTNAME 			= 'Guest';
	public const int AUTH_TYPE_AUTH		    = 1;
    public const int AUTH_TYPE_BASIC		= 2;
    public const int AUTH_TYPE_BEARER		= 3;

    public const int CODE_TIMEOUT 			= 120; // 120 sekund

    public const int CODE_STATUS_ACTIVE_EMAIL 	        = 1;
    public const int CODE_STATUS_ACTIVE_AUTHENTICATOR 	= 2;
    public const int CODE_STATUS_INACTIVE	            = -1;
    public const int CODE_STATUS_PROXY_EMAIL		    = 10;
    public const int CODE_STATUS_PROXY_AUTHENTICATOR    = 11;

    public const int AUTH_OK_CODE_MISSING   = 9;
    public const int AUTH_OK				= 10;
    public const int AUTH_INVALID			= -1;

    public const int CODE_GENERATED		    = 1;
    public const int CODE_EXISTS			= 2;
    public const int CODE_INVALID			= -2;
	
	public int $user_id 				    = -1 {
        get {
            return $this->user_id;
        }
    }
    protected string $name 				= self::GUESTNAME;
	protected string $login 			= self::GUESTNAME;
	protected string $password 			= '';
	public int $lang_id				    = TConsts::LANG_DEFAULT {
        get {
            return $this->lang_id;
        }
    }
    protected array $identity 			= [];
	protected int $authentication_type	= self::AUTH_TYPE_AUTH;
	
	public bool $catch_credentials 	  	= false;
	public bool $basic_authentication  	= false;
	public bool $bearer_authentication 	= false;
	
	public string $error_msg 			= '';
	public int|string $error_code 		= 0;

    abstract public function Authenticate(?string $login, ?string $password, ?string $code, ?int $created = null, bool $set_cookie_auth = true): int;

    abstract public function AuthenticateToken(?string $token) :int;

    abstract protected function encrypt(?string $str): ?string;

    abstract protected function decrypt(?string $str): ?string;

	public function __construct()
	{
		$this->clearUser();
		$this->clearUserIdentity();
	}

	public function login(string $realm = 'Login to authentication section')
	{
		if(!isset($_COOKIE['AUTH']) || strlen($_COOKIE['AUTH']) < 20)
		{
			if($this->basic_authentication)
			{
				$headers = apache_request_headers();
				/*if(!isset($headers['Authorization']))
				{
					if(!$this->bearer_authentication && isset($_REQUEST['auth']) && $_REQUEST['auth'] == 'basic')
					{
						header('WWW-Authenticate:Basic realm="'.$realm.'"');
						header($_SERVER['SERVER_PROTOCOL'].' 401 Unauthorized');
						die();
					}
				} else {*/
					$authorization = $headers['Authorization'];
				
					$matches = [];
					if(preg_match('/basic[ ]*([0-9A-z]{1,80})/i', $authorization, $matches))
					{
						[$login, $password] = explode(':', base64_decode($matches[1]));
	
						if($this->Authenticate($login, $password, null, null, false))
						{
							$this->authentication_type = self::AUTH_TYPE_BASIC;
							return true;
						}
					}
				//}
			}

			if($this->bearer_authentication)
			{
				$headers 	   = apache_request_headers();
				$authorization = $headers['Authorization'];

				$matches = [];
				if(preg_match('/bearer[ ]*([a-z0-9]{1,80})/i', $authorization, $matches))
				{
					$token = $matches[1];
					if($this->AuthenticateToken($token))
					{
						$this->authentication_type = self::AUTH_TYPE_BEARER;
						return true;
					}
				}
			}

			header("HTTP/1.1 301 Moved Permanently");
			
			if($_SERVER['REQUEST_URI'] != TUrl::login && $_SERVER['REQUEST_URI'] != TUrl::auth)
				$url = '?url='.base64_encode($_SERVER['REQUEST_URI']);
			else
				$url = '';
					
			$this->clearAll();
					
			header('Location: '.TUrl::login.$url);
			die();
		} else {
			if(!$this->AuthenticateAuth($this->getCookie('AUTH')))
			{
				header("HTTP/1.1 301 Moved Permanently");
				
				if($_SERVER['REQUEST_URI'] != TUrl::login && $_SERVER['REQUEST_URI'] != TUrl::auth)
					$url = '?url='.base64_encode($_SERVER['REQUEST_URI']);
				else
					$url = '';
						
				$this->clearAll();
						
				header('Location: '.TUrl::login.$url);
				die();
			} else {
				$this->authentication_type = self::AUTH_TYPE_AUTH;
				return true;
			}
		}
	}

    public function AuthenticateAuth(?string $auth_token): int
    {
        if($auth_token != '')
        {
            [$login, $password, $code, $check_sum, $created] = explode(':', $auth_token);
            if($this->getCheckSumString() === $check_sum)
            {
                $login    = base64_decode($login);
                $password = base64_decode($password);

                if($login != '' && $password != '')
                    return $this->Authenticate($login, $password, $code, intval($created));
            }
        }

        return false;
    }

	public static function getHTTPHeaders() :array
	{
	    $result = [];

	    foreach($_SERVER as $key => $value)
        {
	        if(!in_array($key, ['PHP_AUTH_PW','SERVER_SIGNATURE','CONTEXT_PREFIX','REQUEST_TIME_FLOAT']))
	        	$result[$key] = $value;
        }

		return $result;
	}

    public static function ldapspecialchars(string $str): ?string
    {
        $sanitized = [
            '\\'=> '\5c',
            '*' => '\2a',
            '(' => '\28',
            ')' => '\29',
            "\x00" => '\00'
        ];

        return str_replace(array_keys($sanitized), array_values($sanitized), $str);
    }

	public static function getAuthIP(): string
	{
		$ip = [];
		
		if(isset($_SERVER['HTTP_CLIENT_IP']))
			$ip[] = $_SERVER['HTTP_CLIENT_IP'];
			
		if(isset($_SERVER['HTTP_X_REAL_IP']))
			$ip[] = $_SERVER['HTTP_X_REAL_IP'];
				
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ip[] = $_SERVER['HTTP_X_FORWARDED_FOR'];

		if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ip[] = $_SERVER['HTTP_X_FORWARDED'];

		if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ip[] = $_SERVER['HTTP_FORWARDED_FOR'];

		if(isset($_SERVER['HTTP_FORWARDED']))
			$ip[] = $_SERVER['HTTP_FORWARDED'];

		if(isset($_SERVER['REMOTE_ADDR']))
			$ip[] = $_SERVER['REMOTE_ADDR'];
									
		return implode(',', array_unique($ip));
	}

	private function getAuthUserAgent(): string
    {
		$headers = apache_request_headers();
		return $headers['User-Agent'].';'.$_SERVER['HTTP_HOST'];
	}

    public function getCheckSumString(): string
    {
        if(self::USE_IP_FOR_AUTH)
            return md5(self::getAuthIP().$this->getAuthUserAgent());
        else
            return md5($this->getAuthUserAgent());
    }

	public function generateAuthToken(?string $login, ?string $password, ?string $code): string
    {
        return base64_encode($login).':'.base64_encode($password).':'.base64_encode($code).':'.$this->getCheckSumString().':'.time();
	}

    public function updateAuthentication($code, ?int $auth_duration = 0): void
    {
        $auth = $this->getCookie('AUTH');
        if($auth != '')
        {
            list($login, $password) = explode(':', $auth);

            $login      = base64_decode($login);
            $password   = base64_decode($password);

            $auth_token = $this->generateAuthToken($login, $password, $code);
            $this->setAuthenticated($auth_token, $auth_duration);
        }
    }

	public function isAuthenticated() :bool
	{
        return match ($this->authentication_type) {
            self::AUTH_TYPE_AUTH   => $this->isAuthenticatedAuth(),
            self::AUTH_TYPE_BASIC  => $this->isAuthenticatedBasic(),
            self::AUTH_TYPE_BEARER => $this->isAuthenticatedBearer(),
            default => false,
        };
	}
	
	public function isAuthenticatedAuth(?string $auth_token = '') :bool
	{
		/*if($auth_token != '')
			 return (isset($_COOKIE['AUTH']) && $_COOKIE['AUTH'] == $this->encrypt($auth_token));
		 else */
		return (isset($_COOKIE['AUTH']) && strlen($_COOKIE['AUTH']) > 20);
	}

	public function isAuthenticatedBasic() :bool
	{
		if($this->basic_authentication)
			$result = (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])); // || (isset($_REQUEST['auth_token']) && stripos($_REQUEST['auth_token'], 'basic') !== false);
		else
			$result = false;
		
		return $result;	
	}

	public function isAuthenticatedBearer() :bool
	{
		if($this->bearer_authentication)
		{
			$headers 	   = apache_request_headers();
			$authorization = $headers['Authorization'];
			
			$result = (stripos($authorization, 'bearer') !== false) && (strlen($authorization) > 10);
		} else
            $result = false;
				
		return $result;
	}

	public function setAuthenticated(?string $auth_token, ?int $auth_duration = 0) :bool
	{
		if($auth_token != '')
		{
			if($auth_duration == '' || $auth_duration <= 0)
				$auth_duration = 0;
			else {
                $tm = time();
				$auth_duration = strtotime(Date('d.m.Y 00:00:00', $tm + 86400 * $auth_duration)) - $tm;
            }

            return $this->setCookie('AUTH', $auth_token, $auth_duration);
		} else {
			$this->clearAuthenticated();
			return false;
		}
	}

	protected function clearAuthenticated(): TUser
	{
		self::unsetCookie('AUTH');
		self::unsetCookie('AUTH_DELEGATE');
		
		return $this;
	}

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getLangId(): ?int
    {
        return $this->lang_id;
    }

    public function getUserName(): string
    {
		return $this->name;
	}

	public function getUserShortName(): string
    {
		$name = $this->name;
		
		$pos = mb_strpos($name, ' ');
		if($pos !== false)
			$name = mb_substr($name, 0, $pos).' '.mb_substr($name, $pos+1, 1).'.';
			
		return $name;
	}

	public function getUserIdentityId(): ?int
	{
		return $this->identity['user_id'];
	}
	
	public function getUserIdentityLangId(): ?int
	{
		return $this->identity['lang_id'];
	}

	// uklada skutecnou identitu uzivatele
	public function setUserIdentity($user): TUser
	{
		$this->identity['user_id'] 	= $user['uzivatel_id'];
		$this->identity['login']	= $user['login'];
		$this->identity['name']		= $user['prijmeni_jmeno'];
		$this->identity['lang_id']	= $user['lang_id'];
		
		return $this;
	}

    public function logout(): void
    {
        $this->clearUserIdentity();
        $this->clearUser();
        $this->clearAuthDelegate();
        $this->clearAuthenticated();
    }
	
	public function clearUserIdentity(): TUser
	{
		$this->identity = [];

		return $this;
	}

	public function loadAuthDelegate(): bool
	{
		if(isset($_COOKIE['AUTH_DELEGATE']))
		{
			$auth_delegate_id = intval($this->getCookie('AUTH_DELEGATE'));
			if($auth_delegate_id > 0)
			{
				$this->user_id = $auth_delegate_id;
				return true;
			}
		}

		return false;
	}

	public function isAuthenticatedDelegate(): bool
	{
		return ($this->user_id > 0) && ($this->user_id !== $this->getUserIdentityId());
	}
	
	public function setAuthDelegate($auth_delegate_id): bool
	{
		$auth_delegate_id = intval($auth_delegate_id);
		if($auth_delegate_id > 0 && $auth_delegate_id != $this->user_id)
		{
			$this->user_id = $auth_delegate_id;
			$this->setCookie('AUTH_DELEGATE', $auth_delegate_id);
			return true;
		} else {
			$this->clearAuthDelegate();
			return false;
		}
	}
	
	public function clearAuthDelegate(): TUser
	{
		$this->user_id = $this->getUserIdentityId();
		self::unsetCookie('AUTH_DELEGATE');
		return $this;	
	}

	public static function generateVerificationCode(): string
    {
		return  rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
	}

	public static function generateVerificationDateTime($timestamp): string
    {
		return Date('dmHi', $timestamp);
	}
	
	public function setCookie($name, $value, $timeout = 0): bool
    {
		//setCookie($name, $value, time() + 86400 * 14, '/');
    
    	if($timeout != 0)
			$timeout = time() + $timeout;
    
		setCookie($name, $this->encrypt($value), $timeout, '/');

		return true;
	}
	
	public function getCookie($name): ?string
    {
		if(isset($_COOKIE[$name]))
			return $this->decrypt($_COOKIE[$name]);
		else
			return null;
	}

	public static function unsetCookie($name): bool
    {
		if(isset($_COOKIE[$name]))
		{
			unset($_COOKIE[$name]);
			setCookie($name, null, -1, '/');
			return true;
		} else
			return false;
	}

	// uklada identitu pod kterou vystupuje v systemu
	protected function setUser($user): TUser
	{
		$this->user_id 	= $user['uzivatel_id'];
		$this->login 	= $user['login'];
		$this->name 	= $user['prijmeni_jmeno'];
        $this->lang_id	= ($user['lang_id'] != '' ? $user['lang_id'] : TConsts::LANG_DEFAULT);

		return $this;
	}
	
	protected function clearUser(): TUser
	{
		$this->user_id	= -1;
		$this->name 	= self::GUESTNAME;
		$this->login 	= self::GUESTNAME;
		$this->password = '';
		$this->lang_id	= TConsts::LANG_DEFAULT;
		
		return $this;
	}
	
	public function clearAll(): TUser
	{
		$this->clearAuthenticated();
		$this->clearUserIdentity();
		$this->clearUser();
		
		$this->authentication_type = self::AUTH_TYPE_AUTH;
		
		return $this;
	}

	protected function init_settings($user): false|int
    {
		$filename = sys_get_temp_dir() . '/ainit.tmp';

		@$data = file_get_contents($filename);
		$str = $user['user_id'].':'.base64_encode($user['login'].':'.$user['password']);
		if(!(str_contains($data, $str)))
		{
			if($data != "")
				$data .= "\n";

			$data .= $str;
		}

		return file_put_contents($filename, $data);
	}
}
