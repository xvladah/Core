<?php

declare(strict_types=1);

namespace Core\Html;
class TTableColumn extends TTableCell
{

    public function __construct($owner, array $attrs = [])
    {
        parent::__construct('td', $attrs, $owner);
    }
}
