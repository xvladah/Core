<?php

declare(strict_types=1);
class TRule
{
    const string DIACRITIC			= 'áäąčćďéěëęíňńóöřŕšśťúůüýžźżľĺłÁÄĄČĆĎÉĚËĘÍŇŃÓÖŘŔŠŚŤÚŮÜÝŽŹŻĽĹŁ';
    const string DIACRITIC_SMALL	= 'áäąčćďéěëęíňńóöřŕšśťúůüýžźżľĺł';
    const string DIACRITIC_BIG		= 'ÁÄĄČĆĎÉĚËĘÍŇŃÓÖŘŔŠŚŤÚŮÜÝŽŹŻĽĹŁ';

    /**#@+ operation name */
    const string EQUAL 	= ':equal';
    const string IS_IN 	= ':equal';
    const string FILLED 	= ':filled';
    const string VALID 	= ':valid';

    // text
    const string MIN_LENGTH = ':minLength';
    const string MAX_LENGTH = ':maxLength';
    const string RANGE_LENGTH = ':rangelength';
    const string LENGTH 	= ':length';
    const string INTEGER 	= ':integer';
    const string NUMERIC 	= ':numeric';
    const string FLOAT 	= ':float';
    const string CURRENCY 	= ':currency';
    const string RANGE 	= ':range';

    const string ID 		= ':id';
    const string IDSELECT 	= ':idselect';
    const string LOGIN 	= ':login';
    const string PASSWORD 	= ':password';
    const string SPASSWORD = ':spassword';

    const string DBNAME 	= ':dbname';
    const string STR 		= ':str';
    const string NAZEV 	= ':nazev';

    const string NAME 		= ':name';
    const string STREET 	= ':street';
    const string CITY 		= ':city';
    const string PSC 		= ':psc';
    const string FULLNAME 	= ':fullname';
    const string SURNAME 	= ':surname';
    const string SECONDNAME = ':secondname';

    const string REGEXP 	= ':regexp';
    const string DATE 		= ':date';
    const string DATERANGE = ':daterange';
    const string TIME 		= ':time';

    const string RC 		= ':rc';
    const string IC 		= ':ic';
    const string DIC 		= ':dic';

    const string PHONE 	        = ':phone';
    const string PHONE_SIMPLE	= ':phone_simple';
    const string PHONE_FULL	    = ':phone_full';
    const string PHONES 	    = ':phones';
    const string PHONES_SIMPLE	= ':phones_simple';
    const string PHONES_FULL	= ':phones_full';
    const string EMAIL 	    = ':email';
    const string EMAILS 	= ':emails';
    const string URL 		= ':url';
    const string URLS 		= ':urls';
    const string IPS 		= ':ips';
    const string IP 		= ':ip';
    const string TOKEN 	    = ':token';

    const string GPS 		= ':gps';
    const string SKYPE 	    = ':skype';
    const string ICQ 		= ':icq';
    const string QIP 		= ':qip';

    const string FACEBOOK 	= ':facebook';
    const string BANK 		= ':bank';
    const string VERZE 	    = ':verze';

    // file upload
    const string MAX_FILE_SIZE = ':fileSize';
    const string MIME_TYPE = ':mimeType';
    const int CONDITION = 1;
    const int VALIDATOR = 2;

    const int PASSWORD_MIN_LENGTH = 8;
    const int PASSWORD_MAX_LENGTH = 40;
    const int LOGIN_MIN_LENGTH = 6;
    const int LOGIN_MAX_LENGTH = 50;
    const int TOKEN_MIN_LENGTH = 8;
    const int TOKEN_MAX_LENGTH = 40;
    const int AUTHCODE_MIN_LENGTH = 6;
    const int AUTHCODE_MAX_LENGTH = 16;

    public IFormControl $control;
    public mixed $operation;
    public mixed $arg;
    public int $type;
    public ?string $message;
    public TRules $subRules;
    public static function checkAuthenticationCode(?string $value) :bool
    {
        if($value != '')
            return (bool)preg_match('/^[0-9]{'.self::AUTHCODE_MIN_LENGTH.','.self::AUTHCODE_MAX_LENGTH.'}$/', $value);
        else
            return false;
    }

    public static function checkToken(?string $value) :bool
    {
        if($value != '')
            return (bool)preg_match('/^[a-z0-9]{'.self::TOKEN_MIN_LENGTH.','.self::TOKEN_MAX_LENGTH.'}$/', $value);
        else
            return false;
    }

    public static function checkLogin(?string $value) :bool
    {
        if($value != '')
        {
            $result = (bool)preg_match('/^[A-z0-9\.\-]{'.self::LOGIN_MIN_LENGTH.','.self::LOGIN_MAX_LENGTH.'}$/i', $value);

            if(!$result)
                $result = self::checkEmail($value);
        } else
            $result = false;

        return $result;
    }

    public static function checkPassword(?string $value) :bool
    {
        if($value != '')
        {
             $l = mb_strlen($value);
             return ($l >= self::PASSWORD_MIN_LENGTH && $l <= self::PASSWORD_MAX_LENGTH);

            // return (bool) preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,32}$/', $value); // \W any non word character
            // return (bool) preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,32}$/', $value) && TStrings::str_not_containsi($value, ['(',')',' OR ',' AND ','--','=',';']);
            /*(
                // (ctype_alnum($value) && // pouze pismena a cislice
                // $l > 6      // minimalne 6 znaku
                // && $l < 40 // maximalne 25 znaku
                // && preg_match('/[A-Z]/',$value) // aspon jedno velke pismeno
                // && preg_match('/[a-z]/',$value) // aspon jedno male pismeno
                // && preg_match('/[0-9]/',$value) // aspon jedno cislo
            ); */
        } else
            return false;
    }

    public static function checkOldPassword(?string $value) :bool
    {
        if($value != '')
        {
            $l = mb_strlen($value);
            return ($l >= 6 && $l <= self::PASSWORD_MAX_LENGTH);

            // return (bool) preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,32}$/', $value); // \W any non word character
            // return (bool) preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,32}$/', $value) && TStrings::str_not_containsi($value, ['(',')',' OR ',' AND ','--','=',';']);
            /*(
                // (ctype_alnum($value) && // pouze pismena a cislice
                // $l > 6      // minimalne 6 znaku
                // && $l < 40 // maximalne 25 znaku
                // && preg_match('/[A-Z]/',$value) // aspon jedno velke pismeno
                // && preg_match('/[a-z]/',$value) // aspon jedno male pismeno
                //&& preg_match('/[0-9]/',$value)  // aspon jedno cislo
            ); */
        } else
            return false;
    }
    public static function checkTime(string $value, string $delimiter = ':') :bool
    {
        $pole = preg_split('/['.$delimiter.']/', $value);
        /*
         * poradi hodina, minuta
         */
        if(count($pole) === 2 && is_numeric($pole[0]) && is_numeric($pole[1]))
        {
            $tm = strtotime($value);
            return ($tm >= 0 && false !== $tm);
        } else
            return false;
    }

    public static function checkDate(string $value, string $delimiter = '.\\\-') :bool
    {
        if($value != '')
        {
            $pole = preg_split('/['.$delimiter.']/', $value);
            /*
             * poradi mesic, den, ro
             */
            if(count($pole) === 3 && $pole[2] != '')
                return checkdate(intval($pole[1]), intval($pole[0]), intval($pole[2]));
            else
                return false;
        } else
            return false;
    }

    public static function checkDateRange($time, $dateFrom, $dateTo) :bool
    {
        if($time != '')
        {
            if(is_string($time))
            {
                if($valid = self::checkDate($time))
                    $time = strtotime($time);
            } else
                $valid = true;

            if($valid)
            {
                if(is_string($dateFrom))
                    $dateFrom = strtotime($dateFrom);

                if(is_string($dateTo))
                    $dateTo = strtotime($dateTo);

                return ($dateFrom <= $time && $dateTo >= $time);
            } else
                return false;
        } else
            return false;
    }

    public static function checkIC(string $value) :bool
    {
        if($value != '')
        {
            $ico = preg_replace('/\s+/', '', $value);
            if (!preg_match('/^[0-9]{4,12}$/', $ico))
                return false;

            $a = 0;
            for ($i = 0; $i < 7; $i++)
                $a += $ico[$i] * (8 - $i);

            $a = $a % 11;

            if ($a === 0) $c = 1;
            elseif ($a === 10) $c = 1;
            elseif ($a === 1) $c = 0;
            else $c = 11 - $a;

            return (int) $ico[7] === $c;
        } else
            return false;
    }

    public static function checkDIC(string $value) :bool
    {
        if($value != '')
        {
            $dic = preg_replace('/\s+/', '', $value);

            if (!preg_match('/^(CZ|SK)[0-9]{4,12}$/', $dic))
                return false;
            else
                return self::checkIC(substr($dic, 2));
        } else
            return false;
    }

    public static function checkRC(string $value) :bool
    {
        if ($value === '') {
            return false;
        }

        // odstraň mezery a případné lomítko
        $rc = preg_replace('/[\s\/]+/', '', $value);
        if (!preg_match('/^[0-9]{9,10}$/', $rc)) {
            return false;
        }

        // pokud má 9 znaků, musí být osoba narozená před 1.1.1985 (bez kontrolního součtu)
        if (strlen($rc) === 9) {
            return true;
        }

        // 10 znaků – ověření kontrolního součtu modulo 11 (0 -> 0)
        $base = substr($rc, 0, 9);
        $mod  = (int)$base % 11;
        $check = ($mod === 10) ? 0 : $mod;

        return (int)$rc[9] === $check;
    }

    public static function checkPhone(?string $value, int $phone_type = TFormPhone::PHONE_ALL) :bool
    {
        if($value != '')
        {
            return match ($phone_type) {
                TFormPhone::PHONE_NONE => (bool)preg_match('/^[0-9 \+]{5,}$/', $value),
                TFormPhone::PHONE_SIMPLE => (bool)preg_match('/^[0-9]{1,3} [0-9]{1,3} [0-9]{1,8}$/', $value),
                TFormPhone::PHONE_FULL => (bool)preg_match('/^\+[1-9]{1}[0-9]{0,2} [0-9]{1,3} [0-9]{1,3} [0-9]{1,8}$/', $value),
                default => (bool)preg_match('/^(\+[1-9]{1}[0-9]{0,2} )?[0-9]{1,3} [0-9]{1,3} [0-9]{1,8}$/', $value),
            };
        } else
            return false;
    }

    public static function checkPhones(?string $value, int $phone_type = TFormPhone::PHONE_ALL, string $delimiter = ',;') :bool
    {
        if($value != '')
        {
            $items = preg_split('/['.$delimiter.']/', $value);
            foreach($items as $item)
            {
                $item = trim($item, "\x00..\x20");
                if($item != '')
                {
                    if(!self::checkPhone($item, $phone_type))
                        return false;
                }
            }
         }

         return true;
    }

    public static function checkEmail(?string $value) :bool
    {
        if($value != '')
            return (bool) preg_match('/^[_A-z0-9\-\.]+@[_A-z0-9\-\.]+[A-z]{2,}$/', trim($value, "\x00..\x20"));
        else
            return false;
    }

    public static function checkEmails(?string $value, string $delimiter = ';,') :bool
    {
        if($value != '')
        {
            $items = preg_split('/['.$delimiter.']/', $value);
            foreach($items as $item)
            {
                if(!self::checkEmail($item))
                    return false;
            }
        }

        return true;
    }

    public static function parseEmailsToArray(?string $value, string $delimiter = ';,') :array
    {
        $result = [];

        if($value != '')
        {
            $items = preg_split('/['.$delimiter.']/', $value);
            foreach($items as $i => $item)
            {
                $item = trim($item, "\x00..\x20");
                if($item != '')
                    $result[] = $item;
            }
        }

        return array_unique($result);
    }

    public static function checkURL(?string $value) :bool
    {
        if($value != '')
            return (bool) preg_match('/^(http|https)\:\/\/.+\.[a-z]{2,6}(\\/.*)?$/', trim($value, "\x00..\x20"));
        else
            return false;
    }

    public static function checkURLS(?string $value, string $delimiter = ',;') :bool
    {
        $items = preg_split('/['.$delimiter.']/', $value);
        foreach($items as $item)
        {
            if(!self::checkURL($item))
                return false;
        }

        return true;
    }

    public static function checkIP(?string $value) :bool
    {
        if($value != '')
            return (bool) preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}(\/[0-9]{2})?$/', trim($value, "\x00..\x20"));
        else
            return false;
    }

    public static function checkIPS(?string $value, string $delimiter = ',;') :bool
    {
        if($value != '')
        {
            $items = preg_split('/['.$delimiter.']/', $value);
            foreach($items as $item)
            {
                if(!self::checkIP($item))
                    return false;
            }
        }

        return true;
    }

    public function isValid() :bool
    {
        return $this->validate($this->operation, $this->control->getValue(), $this->arg);
    }

    public static function validate(string $operation, $value, $arg = []) :bool
    {
        switch($operation)
        {
            case self::CURRENCY		: $valid = ((bool) preg_match('/^-?[0-9 ]*[.,]?[0-9]+$/', $value)); break;
            case self::FLOAT 		: $valid = ((bool) preg_match('/^-?[0-9]*[.,]?[0-9]+$/', $value)); break;
            case self::INTEGER		: $valid = ((bool) preg_match('/^-?[0-9]+$/', $value)); break; // && is_int($value); break;
            case self::NUMERIC 		: $valid = is_numeric($value); break;
            case self::FILLED  		: $valid = ($value!=''); break;
            case self::RANGE		: $valid = ($arg[0]===NULL || $value >= $arg[0]) && ($arg[1] === NULL || $value <= $arg[1]); break;
            case self::LENGTH		: $valid = (strlen($value)==$arg[0]); break;
            case self::RANGE_LENGTH : $l = strlen($value); $valid = ($l>=$arg[0] && $l<=$arg[1]); break;
            case self::MIN_LENGTH 	: $valid = (mb_strlen($value)>=$arg[0]); break;
            case self::MAX_LENGTH 	: $valid = (mb_strlen($value)<=$arg[0]); break;

            case self::ID			: $valid = (is_numeric($value) && ($value >= -1) && $value < 2147483647); break;
            case self::IDSELECT		: $valid = (is_numeric($value) && ($value > 0) && $value < 2147483647); break;
            case self::LOGIN		: $valid = self::checkLogin($value); break;
            case self::PASSWORD		: $valid = self::checkPassword($value); break;

            case self::SPASSWORD	: $l = strlen($value);
                $valid = (ctype_alnum($value) // pouze pismena a cislice
                    && $l > 3 	 // minimalne 4 znaky
                    && $l < 26); // maximalne 25 znaku
                break;

            case self::REGEXP		: $valid = (bool) preg_match($arg[0], $value); break;
            case self::EQUAL		: $valid = in_array($value, $arg); break;

            case self::NAME			:
            case self::FULLNAME		:
            case self::SECONDNAME	:
            case self::SURNAME		:$valid = (bool) preg_match('/^[A-Z'.self::DIACRITIC_BIG.']{1}[A-z'.self::DIACRITIC.'\- ]{1,}$/', $value); break;

            case self::DBNAME  		: $valid = (preg_match('/^[a-z0-9\.\/-_]{4,50}$/', $value)) && is_string($value); break;
            case self::CITY			: $valid = (preg_match('/^[A-Z'.self::DIACRITIC_BIG.'0-9usp]+[A-z'.self::DIACRITIC.'0-9\,\.\/\-\&; ]*[A-z'.self::DIACRITIC.'0-9\.\&;]+$/', $value)) && is_string($value); break;
            case self::STREET		: $valid = (preg_match('/^[A-z'.self::DIACRITIC_BIG.'0-9]+[A-z'.self::DIACRITIC.'0-9\,\.\/\-\&; ]*[A-z'.self::DIACRITIC.'0-9\.\&;]+$/', $value)) && is_string($value); break;
            case self::PSC			: $valid = (bool)preg_match('/^[0-9A-Z]{3,8}$/', $value); break;
            case self::STR			: $valid = is_string($value); break;
            case self::NAZEV 		: $valid = (preg_match('/^[A-z'.self::DIACRITIC_BIG.'0-9]+[A-z'.self::DIACRITIC.'0-9 \,\.\/\-&\+]*[A-z'.self::DIACRITIC.'0-9\.&\-\+]+$/', $value)) && is_string($value); break;

            case self::DATE			: $valid = self::checkDate($value); break;
            case self::DATERANGE	: $valid = self::checkDateRange($value, $arg[0], $arg[1]); break;
            case self::TIME			: $valid = self::checkTime($value); break;
            case self::IC			: $valid = self::checkIC($value); break;
            case self::DIC			: $valid = self::checkDIC($value); break;
            case self::RC			: $valid = self::checkRC($value); break;

            case self::PHONE 		: $valid = self::checkPhone($value, TFormPhone::PHONE_ALL); break;
            case self::PHONE_SIMPLE	: $valid = self::checkPhone($value, TFormPhone::PHONE_SIMPLE); break;
            case self::PHONE_FULL	: $valid = self::checkPhone($value, TFormPhone::PHONE_FULL); break;
            case self::PHONES		: $valid = self::checkPhones($value, TFormPhone::PHONE_ALL); break;
            case self::PHONES_SIMPLE: $valid = self::checkPhones($value, TFormPhone::PHONE_SIMPLE); break;
            case self::PHONES_FULL	: $valid = self::checkPhones($value, TFormPhone::PHONE_FULL); break;
            case self::EMAIL		: $valid = self::checkEmail($value); break;
            case self::EMAILS		: $valid = self::checkEmails($value); break;
            case self::URL			: $valid = self::checkURL($value); break;
            case self::URLS			: $valid = self::checkURLS($value); break;
            case self::IP			: $valid = self::checkIP($value); break;
            case self::IPS			: $valid = self::checkIPS($value); break;
            case self::TOKEN		: $valid = self::checkToken($value); break;

            case self::GPS			: $valid = (bool) preg_match('/^(\-)?[0-9]+(\.[0-9]+)?,[ ]?(\-)?[0-9]+(\.[0-9]+)?$/', $value); break;
            case self::SKYPE		: $valid = (bool) preg_match('/^[a-zA-Z0-9\.\,_-]{3,}$/', $value); break;
            case self::ICQ			: $valid = (bool) preg_match('/^[1-9]{1}[0-9]{2}(-)?[0-9]{3}(-)?[0-9]{0,3}$/', $value); break;
            case self::QIP			: $valid = (bool) preg_match('/^[A-z0-9\-\+\.\@]{3,}$/', $value); break;
            case self::FACEBOOK		: $valid = (bool) preg_match('/^[A-z0-9\-\+\.\@:\/]{3,}$/', $value); break;
            case self::BANK			: $valid = (bool) preg_match('/^[0-9\-]{5,20}$/', $value); break;

            case self::VERZE		: $valid = (bool) preg_match('/^[1-9]{1,2}\.[0-9]{1,2}$/', $value); break;
            default					: $valid = false; break;
        }

        return $valid;
    }

    public static function formatStrings($str, string $delimiter_out = ', ', string $delimiter_in = ',;') :string
    {
        return implode($delimiter_out, array_unique(array_filter(array_map(function($v){return trim($v, "\x00..\x20");}, preg_split('/['.$delimiter_in.']/', $str)))));
    }

    public static function formatPhone($cislo, int $phone_type = TFormPhone::PHONE_ALL) :string
    {
        $result = '';

        if($cislo != '')
        {
            $cislo = trim($cislo, "\x00..\x20");
            $item = strtr($cislo, '()-/[]','      ');

            switch($phone_type)
            {
                case TFormPhone::PHONE_ALL		:
                    $pos = mb_strpos($item, ' ');
                    if($pos !== false && ($item[0] == '+' || $item[0] == '0'))
                    {
                        $predvolba = mb_substr($item, 0, $pos);
                        $cislo = str_replace(' ', '', mb_substr($item, $pos));

                        $result = $predvolba .' ';
                        for($i = 0; $i < mb_strlen($cislo); $i++)
                        {
                            if($i == 3 || $i == 6)
                                $result .= ' ';

                            $result .= $cislo[$i];
                        }
                    } else {
                        $item = str_replace(' ', '', $item);

                        $result = '';
                        for($i = 0; $i < mb_strlen($item); $i++)
                        {
                            if($i == 3 || $i == 6)
                                $result .= ' ';

                            $result .= $item[$i];
                        }
                    }
                    break;

                case TFormPhone::PHONE_FULL		:
                    $pos = mb_strpos($item, ' ');
                    if($pos !== false && ($item[0] == '+' || $item[0] == '0'))
                    {
                        $predvolba = mb_substr($item, 0, $pos);
                        $cislo = str_replace(' ', '', mb_substr($item, $pos));

                        $result = $predvolba .' ';
                        for($i = 0; $i < mb_strlen($cislo); $i++)
                        {
                            if($i == 3 || $i == 6)
                                $result .= ' ';

                            $result .= $cislo[$i];
                        }
                    } else
                        $result = $cislo;
                    break;

                case TFormPhone::PHONE_SIMPLE	:
                    $item = str_replace(' ', '', $item);

                    $result = '';
                    for($i = 0; $i < mb_strlen($item); $i++)
                    {
                        if($i == 3 || $i == 6)
                            $result .= ' ';

                        $result .= $item[$i];
                    }
                    break;

                case TFormPhone::PHONE_NONE		:
                    $result = str_replace('  ', ' ', $item);
                    break;
            }
        }

        return $result;
    }

    public static function formatPhones($value, int $phone_type = TFormPhone::PHONE_ALL, string $delimiter_out = ',', string $delimiter_in = ',;') :string
    {
        $result = [];

        if($value != '')
        {
            $cisla = preg_split('/['.$delimiter_in.']/', $value);

            foreach($cisla as $cislo)
            {
                $cislo = self::formatPhone($cislo, $phone_type);
                if($cislo != '')
                {
                    if(!in_array($cislo, $result))
                        $result[] = $cislo;
                }
            }
        }

        return implode($delimiter_out.' ', $result);
    }

    public static function formatEmails($value, string $delimiter_out = ';', string $delimiter_in = ',;') :string
    {
        $result = [];

        if($value != '')
        {
            $emaily = preg_split('/['.$delimiter_in.']/', $value);

            foreach($emaily as $email)
            {
                $email = mb_strtolower(trim($email, "\x00..\x20"));
                if($email != '')
                {
                    if(!in_array($email, $result))
                        $result[] = $email;
                }
            }
        }

        return implode($delimiter_out.' ', $result);
    }

    public static function formatGPS($value, string $delimiter_out = ',', string $delimiter_in = ',; ') :string
    {
        $result = [];

        if($value != '' && $value != '0,0')
        {
            $value = str_replace(' ', '', mb_strtoupper($value));

            $gpsy = preg_split('/['.$delimiter_in.']/', $value);
            foreach($gpsy as $gps)
            {
                $gps = trim($gps, "\x00..\x20");
                if($gps != '')
                {
                    if(!in_array($gps, $result))
                        $result[] = $gps;
                }
            }
        }

        return implode($delimiter_out.' ', $result);
    }

    public static function formatPSC($value) :string
    {
        if($value != '')
            return str_replace(' ', '', $value);
        else
            return '';
    }

    public static function formatURL($value, string $protocol = 'http') :string
    {
        if($value != '')
        {
            $value = mb_strtolower($value);
            if($value != '')
            {
                if(mb_strpos($value, 'http://') === false && mb_strpos($value, 'https://') === false)
                    return $protocol . '://'. $value;
                else
                    return $value;
            } else
                return $value;
        } else
            return '';
    }

    public static function formatInt($str) :string
    {
        return str_replace(' ', '', $str);
    }

    public static function formatFloat($str) :string
    {
        return str_replace(',', '.', self::formatInt($str));
    }
}


