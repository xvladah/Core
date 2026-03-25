<?php

class TImageVerificator extends TText implements IFormControl
{
    protected bool $caseSensitive = true;
    protected string $sessionName;

    public function setCaseSensitive(bool $value) :static
    {
        $this->caseSensitive = $value;
        return $this;
    }

    public function setSessionName(string $sessionName) :static
    {
        $this->sessionName = $sessionName;
        return $this;
    }

    public function html() :string
    {
        $this->setValue('');
        return '<table><tr><td><img src="gd_image.php" alt="gd_image" /></td><td>'.parent::html().'</td></tr></table>';
    }

    public function validate() :bool
    {
        $imageCode = new TImageCode();

        if($this->sessionName != '')
            $imageCode->setSessionName($this->sessionName);

        $code =$imageCode->getCode();
        $args = [$code];

        if(!$this->caseSensitive)
            $args[] = strtolower($code);

        $this->addRule(TRule::EQUAL, 'Chybně zadaný ověřovací kód', $args);
        return parent::validate();
    }
}
