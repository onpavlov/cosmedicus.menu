<?php

class CosmedicusMenu extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    public function executeComponent()
    {
        $this->includeComponentTemplate();
    }
}