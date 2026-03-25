<?php

/**
* Třída pro generování formuláře
*
* @TForm
* @version 2.1
* @author Vladimir Horky
* @copyright Vladimír Horký, 2018
*
* version 2.1
* added function AddHtmlLn
*
* version 2.0
* added support for MultiComboBox
*
* version 1.1
* added new function addNote
*/

class TForm extends TElement
{
    const string FORM_SESSION_NAME = 'fsession';

    const string GET = 'get';
    const string POST = 'post';
    const string MULTIPART = 'multipart/form-data';

    protected string $caption = '';
    protected string $description = '';
    protected string $session = '';

    public static function genSession(?string $salt = ''): string
    {
        if($salt == '')
            $salt = 'session';

        return md5(Date('d.m.Y').$salt);
    }

    public function setAutocomplete($state): static
    {
        if($state == '1' || $state === true || $state == 'on')
            $value = 'on';
        else
            $value = 'off';

        $this->attributes->add('autocomplete', $value);
        return $this;
    }
    private static function strtoupper(?string $str): string
    {
        return StrTr(mb_strtoupper($str), 'áäąčćďéěëęíňńóöřŕšśťúůüýžźżľĺł', 'ÁÄĄČĆĎÉĚËĘÍŇŃÓÖŘŔŠŚŤÚŮÜÝŽŹŻĽĹŁ');
    }

    private static function strtolower(?string $str): string
    {
        return StrTr(mb_strtolower($str), 'ÁÄĄČĆĎÉĚËĘÍŇŃÓÖŘŔŠŚŤÚŮÜÝŽŹŻĽĹŁ', 'áäąčćďéěëęíňńóöřŕšśťúůüýžźżľĺł');
    }

    public function __construct(string $name, string $method = self::POST)
    {
        parent::__construct('form');
        $this->attributes->add('name', $name);
        $this->attributes->add('method', $method);
        $this->attributes->add('action', $_SERVER['SCRIPT_NAME']);
    }

    public function setCaption(string $caption) :TForm
    {
        $this->caption = $caption;
        return $this;
    }

    public function setDescription(string $description) :TForm
    {
        $this->description = $description;
        return $this;
    }

    public function setSession(string $session) :TForm
    {
        $this->addHidden(self::FORM_SESSION_NAME, $session);
        $this->session = $session;
        return $this;
    }

    public function getSession(): ?string
    {
        return $this->session;
    }

    public function setAction(string $action) :TForm
    {
        $this->attributes->add('action', $action);
        return $this;
    }

    public function setEnctype(string $enctype = self::MULTIPART) :TForm
    {
        $this->attributes->add('enctype', $enctype);
        return $this;
    }

    public function onSubmit(string $javascript) :TForm
    {
        $this->attributes->add('onsubmit', $javascript);
        return $this;
    }

    public function isSubmitted(string $encoding = '', bool $load = true) :bool
    {
        $result = $_POST[self::FORM_SESSION_NAME] == $this->session;
        if($result)
            $result &= self::_isSubmitted($encoding, $load);

        return $result;
    }

    public function _isSubmitted(string $encoding = '', bool $load = true) :bool
    {
        $submitted = isset($_POST) && count($_POST) > 0;

        if($submitted && $load)
        {
            $values = [];
            if(is_array($_POST))
            {
                foreach($this->childNodes->items as $key => $item)
                {
                    if(key_exists($key, $_POST))
                    {
                        $val = $_POST[$key];

                        if(!is_array($val))
                            $values[$key] = THttpResponse::RemoveSpecials($val);
                        else
                            $values[$key] = $val;
                    } else
                        if($item instanceof TMultiComboBox)
                            $values[$key] = [];
                }
            }

            $this->setValues($values);
        }

        return $submitted;
    }

    public function validate() :bool
    {
        $valid = true;

        foreach($this->childNodes->items as $item)
        {
            $vysledek = $item->validate();
            $valid &= $vysledek;
        }

        return $valid;
    }

    public function getErrors() :array
    {
        $result = [];

        foreach($this->childNodes->items as $item)
            foreach($item->errors as $name => $value)
                $result[] = $value;

        return $result;
    }

    public function setValues(?array $values) :TForm
    {
        foreach($this->childNodes->items as $item)
            if($item instanceof TCheckBox)
                $item->setValue(0);

        if(is_array($values) && !empty($values))
        {
            foreach($values as $name => $value)
            {
                $item = $this->childNodes->items[$name];
                if(!($item instanceof TButton ||
                     $item instanceof TSubmit ||
                     $item instanceof TReset ||
                     $item instanceof TLabel ||
                     $item instanceof THtmlCode))

                if(!empty($item) && !$item->isDisabled())
                {
                    if($item instanceof TFormControlText)
                    {
                        if($item->uppercase)
                            $value = self::strtoupper($value);

                        if($item->lowercase)
                            $value = self::strtolower($value);

                        if($item->ucfirst)
                            $value = UCFirst($value);

                        $item->setValue($value);
                    } else
                        $item->setValue($value);
                }
            }
        }

        return $this;
    }

    public function setDefaults(?array $defaults) :TForm
    {
        foreach($this->childNodes->items as $item)
            if($item instanceof TCheckBox)
                $item->setValue(0);

        if(is_array($defaults) && !empty($defaults))
        {
            foreach($defaults as $name => $value)
            {
                $item = $this->childNodes->items[$name];
                if(!($item instanceof TButton ||
                     $item instanceof TSubmit ||
                     $item instanceof TReset ||
                     $item instanceof TLabel ||
                     $item instanceof THtmlCode))

                    if(!empty($item))
                        $item->setValue($value);
            }
        }

        return $this;
    }

    public function getDefaults() :array
    {
        $result = [];

        foreach($this->childNodes->items as $item)
            if(!($item instanceof TButton ||
                 $item instanceof TSubmit ||
                 $item instanceof TReset ||
                 $item instanceof TLabel ||
                 $item instanceof THtmlCode))

            $result[$item->getName()] = $item->getValue();

        return $result;
    }

    public function addText(string $name, string $label = '', bool $for_label = false) :TText
    {
        $text = new TText($name);
        $this->childNodes->add($text, $name);
        $text->setLabel($label, $for_label);
        return $text;
    }

    public function addPassword(string $name, string $label = '', bool $for_label = false) :TPassword
    {
        $password = new TPassword($name);
        $this->childNodes->add($password, $name);
        $password->setLabel($label, $for_label);
        return $password;
    }

    public function addSubmit(string $name, mixed $value) :TSubmit
    {
        $submit = new TSubmit($name);
        $this->childNodes->add($submit, $name);
        $submit->setValue($value);
        return $submit;
    }

    public function addButton(string $name, mixed $value) :TButton
    {
        $button = new TButton($name);
        $this->childNodes->add($button, $name);
        $button->setValue($value);
        return $button;
    }

    public function addReset(string $name, mixed $value) :TReset
    {
        $reset = new TReset($name);
        $this->childNodes->add($reset, $name);
        $reset->setValue($value);
        $reset->attributes->add('tabindex', -1);
        return $reset;
    }

    public function addComboBox(string $name, array $options = [], string $label = '', bool $for_label = false) :TComboBox
    {
        $combobox = new TComboBox($name, $options);
        $this->childNodes->add($combobox, $name);
        $combobox->setLabel($label, $for_label);
        return $combobox;
    }

    public function addMultiComboBox(string $name, array $options = [], string $label = '', bool $for_label = false) :TMultiComboBox
    {
        $combobox = new TMultiComboBox($name, $options);
        $this->childNodes->add($combobox, $name);
        $combobox->setLabel($label, $for_label);
        return $combobox;
    }

    public function addRadioList(string $name, array $options = [], string $label = '', bool $for_label = false) :TRadioList
    {
        $radiolist = new TRadioList($name, $options);
        $this->childNodes->add($radiolist, $name);
        $radiolist->setLabel($label, $for_label);
        return $radiolist;
    }

    public function addCheckBox(string $name, string $label = '', bool $for_label = false) :TCheckBox
    {
        $checkbox = new TCheckBox($name);
        $this->childNodes->add($checkbox, $name);
        $checkbox->setLabel($label, $for_label);
        return $checkbox;
    }

    public function addDate(string $name, string $label = '', string $format = 'd.m.Y', bool $for_label = false) :TFormDate
    {
        $date = new TFormDate($name, $format);
        $this->childNodes->add($date, $name);
        $date->setLabel($label, $for_label);
        return $date;
    }

    public function addTime(string $name, string $label = '', string $format = 'H:i', bool $for_label = false) :TFormTime
    {
        $date = new TFormTime($name, $format);
        $this->childNodes->add($date, $name);
        $date->setLabel($label, $for_label);
        return $date;
    }

    public function addInteger(string $name, string $label = '', bool $for_label = false) :TFormInteger
    {
        $integer = new TFormInteger($name);
        $this->childNodes->add($integer, $name);
        $integer->setLabel($label, $for_label);
        return $integer;
    }

    public function addFloat(string $name, string $label = '', int $decimals = 2, bool $for_label = false) :TFormFloat
    {
        $integer = new TFormFloat($name, $decimals);
        $this->childNodes->add($integer, $name);
        $integer->setLabel($label, $for_label);
        return $integer;
    }

    public function addCurrency(string $name, string $label = '', int $decimals = 2, bool $for_label = false) :TFormCurrency
    {
        $currency = new TFormCurrency($name, $decimals);
        $this->childNodes->add($currency, $name);
        $currency->setLabel($label, $for_label);
        return $currency;
    }

    public function addEmail(string $name, string $label = '', bool $for_label = false) :TFormEmail
    {
        $email = new TFormEmail($name);
        $this->childNodes->add($email, $name);
        $email->setLabel($label, $for_label);
        return $email;
    }

    public function addEmails(string $name, string $label = '', bool $for_label = false) :TFormEmails
    {
        $emails = new TFormEmails($name);
        $this->childNodes->add($emails, $name);
        $emails->setLabel($label, $for_label);
        return $emails;
    }

    public function addPhone(string $name, string $label = '', bool $for_label = false) :TFormPhone
    {
        $phone = new TFormPhone($name);
        $this->childNodes->add($phone, $name);
        $phone->setLabel($label, $for_label);
        return $phone;
    }

    public function addPhones(string $name, string $label = '', bool $for_label = false) :TFormPhones
    {
        $phones = new TFormPhones($name);
        $this->childNodes->add($phones, $name);
        $phones->setLabel($label, $for_label);
        return $phones;
    }

    public function addURL(string $name, string $label = '', bool $for_label = false) :TFormURL
    {
        $url = new TFormURL($name);
        $this->childNodes->add($url, $name);
        $url->setLabel($label, $for_label);
        return $url;
    }

    public function addGPS(string $name, string $label = '', bool $for_label = false) :TFormGPS
    {
        $gps = new TFormGPS($name);
        $this->childNodes->add($gps, $name);
        $gps->setLabel($label, $for_label);
        return $gps;
    }

    public function addHidden(string $name, string $value = '') :THidden
    {
        $hidden = new THidden($name, $value);
        $this->childNodes->add($hidden, $name);
        return $hidden;
    }

    public function addTextArea(string $name, string|int|null $cols, string|int|null $rows, string $label = '', bool $for_label = false) :TTextArea
    {
        $textarea = new TTextArea($name);
        $this->childNodes->add($textarea, $name);
        if($cols != '' && $cols > 0) $textarea->setCols($cols);
        if($rows != '' && $rows > 0) $textarea->setRows($rows);
        $textarea->setLabel($label, $for_label);
        return $textarea;
    }

    public function addFile(string $name, string $label = '', bool $for_label = false) :TFile
    {
        $file = new TFile($name);
        $this->childNodes->add($file, $name);
        $file->setLabel($label);
        return $file;
    }

    public function addImageVerificator(?string $name, string $label = '', bool $for_label = false) :TImageVerificator
    {
        $verificator = new TImageVerificator($name);
        $this->childNodes->add($verificator, $name);
        $verificator->setLabel($label, $for_label);
        return $verificator;
    }

    public function addLabel(?string $name, string $value, string $label = '', bool $for_label = false) :TLabel
    {
        $popisek = new TLabel($name);
        $this->childNodes->add($popisek, $name);
        $popisek->nodeValue = $value;
        $popisek->setLabel($label, $for_label);
        return $popisek;
    }

    public function addHtmlItem(?string $name, string $html, string $label = '', bool $for_label = false) :THtmlCode
    {
        $code = new THtmlCode($name);
        $this->childNodes->add($code, $name);
        $code->nodeValue = $html;
        $code->setLabel($label, $for_label);
        return $code;
    }

    public function addHtmlCode(?string $name, string $html) :THtmlCode
    {
        $code = new THtmlCode($name);
        $this->childNodes->add($code, $name);
        $code->nodeValue = $html;
        return $code;
    }

    public function AddHtmlLn(?string $name = null) :THtmlCode
    {
        return $this->addHtmlCode($name, '<br />');
    }

    public function addNote(string $name, ?string $caption, mixed $notes) :THtmlCode
    {
        $code = new THtmlCode();
        $this->childNodes->add($code, $name);

        $nodeValue = '';

        if($caption != '')
            $nodeValue .= '<div class="section">'.$caption.'</div>';

        if(is_array($notes))
        {
            foreach($notes as $note)
                $nodeValue .= '<p>'.$note.'</p>';
        } else
            if($notes != '')
                $nodeValue .= '<p>'.$notes.'</p>';

        $code->nodeValue = '<div class="form_title">'.$nodeValue.'</div>';

        return $code;
    }

    public function _html() :string
    {
        if($this->caption != '')
        {
            $cap = '<div class="title">'.$this->caption.'</div>';

            if($this->description != '')
                $cap .= '<p>'.$this->description.'</p>';

            return '<div class="form_title">'.$cap.'</div>';
        } else
            return '';
    }

    public function html() :string
    {
        return '<'.$this->nodeName.$this->attributes->html().'>'.$this->_html() . $this->innerHtml().'</'.$this->nodeName.'>';
    }
}