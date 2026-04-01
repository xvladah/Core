<?php

namespace Core\Forms;
class TFormURL extends TFormControlText implements IFormControl
{
    private string $protocol = 'http';

    public function __construct($name)
    {
        parent::__construct('input', ['type'=>'text','name'=>$name]);
        $this->addCondition(TRule::FILLED)
            ->addRule(TRule::URL, __('err.bad_url_format', 'Chybný formát URL adresy'));
    }

    public function setValue($text) :TFormControl
    {
        $text = TRule::formatURL($text, $this->protocol);
        parent::setValue($text);
        return $this;
    }

    public function setProtocol($protocol) :TFormURL
    {
        $this->protocol = $protocol;
        return $this;
    }
}
