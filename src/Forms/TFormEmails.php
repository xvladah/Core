<?php

namespace Core\Forms;
class TFormEmails extends TFormControlText implements IFormControl
{
    private $separator = ',';

    public function __construct($name, string $separator = ';')
    {
        parent::__construct('input', ['type'=>'text','name'=>$name]);
        $this->setSeparator($separator);
        $this->addCondition(TRule::FILLED)
            ->addRule(TRule::EMAILS, __('err.bad_emails_format', 'Chybný formát e-mailu/e-mailů'));
    }

    public function setValue($text) :TFormControl
    {
        $text = TRule::formatEmails($text, $this->separator);
        parent::setValue($text);
        return $this;
    }

    public function setSeparator(string $separator) :TFormControl
    {
        $this->separator = $separator;
        return $this;
    }
}
