<?php

class ctlBase extends libTpl
{
    public function __construct()
    {
        $this->assign('version', VERSION);
    }
}