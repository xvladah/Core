<?php

namespace Core\Forms;
class TFormPhones extends TFormControlText implements IFormControl
{
    private $separator = ',';
    private $phone_type = TFormPhone::PHONE_NONE;

    public function __construct($name, string $separator = ',')
    {
        parent::__construct('input', ['type'=>'text','name'=>$name]);
        $this->setSeparator($separator);
        $this->addCondition(TRule::FILLED)
            ->addRule(TRule::PHONES, __('err.bad_phones_format', 'Chybný formát telefonního čísla/čísel'));
    }

    public function setValue($text) :TFormControl
    {
        $text = TRule::formatPhones($text, $this->phone_type, $this->separator);
        parent::setValue($text);
        return $this;
    }

    public function setSeparator(string $separator) :TFormControl
    {
        $this->separator = $separator;
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
