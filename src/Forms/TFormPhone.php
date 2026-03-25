<?php

class TFormPhone extends TFormControlText implements IFormControl
{
    const PHONE_NONE 	= 0;
    const PHONE_SIMPLE 	= 1;
    const PHONE_FULL	= 2;
    const PHONE_ALL		= 3;

    private $phone_type = self::PHONE_NONE;

    public function __construct($name)
    {
        parent::__construct('input', ['type'=>'text','name'=>$name]);
        $this->addCondition(TRule::FILLED)
            ->addRule(TRule::PHONE, __('err.bad_phone_format', 'Chybný formát telefonního čísla'));
    }

    public function setValue($text) :TFormControl
    {
        $text = TRule::formatPhone($text, $this->phone_type);
        parent::setValue($text);
        return $this;
    }

    public function setType(int $phone_type) :TFormControl
    {
        switch($phone_type)
        {
            case TFormPhone::PHONE_SIMPLE	:
                $this->addCondition(TRule::FILLED)
                    ->addRule(TRule::PHONES_SIMPLE, __('err.bad_phones_format', 'Chybný formát telefonního čísla/čísel'));
                break;

            case TFormPhone::PHONE_FULL		:
                $this->addCondition(TRule::FILLED)
                    ->addRule(TRule::PHONES_FULL, __('err.bad_phones_format', 'Chybný formát telefonního čísla/čísel'));
                break;
        }

        $this->phone_type = $phone_type;
        return $this;
    }

    public function getType() :int
    {
        return $this->phone_type;
    }
}
