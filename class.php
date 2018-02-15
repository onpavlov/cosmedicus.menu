<?php

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Loader;

class CosmedicusMenu extends CBitrixComponent
{
    private $iblockId;
    private $iblockCode;
    private $cacheTime;

    const CACHE_DIR = 'main_menu';
    const CACHE_TIME = 3600;

    public function onPrepareComponentParams($arParams)
    {
        $modules = ['iblock'];

        foreach ($modules as $module) {
            if (!Loader::includeModule($module)) {
                die('Cannot include module ' . $module);
            }
        }
        
        $this->iblockCode = $arParams['IBLOCK_CODE'];
        $this->iblockId = $this->getIblockIdByCode($this->iblockCode);
        $this->cacheTime = $arParams['CACHE_TIME'] ? $arParams['CACHE_TIME'] : self::CACHE_TIME;

        return $arParams;
    }

    public function executeComponent()
    {
        $cacheId = serialize($this->arParams);
        $cache = Cache::createInstance();

        if ($cache->initCache($this->cacheTime, $cacheId, self::CACHE_DIR)) {
            $this->arResult = $cache->getVars();
        } elseif ($cache->startDataCache()) {
            $this->arResult = [
                'SECTIONS' => $this->getSectionsTree(),
                'ELEMENTS' => $this->getElements()
            ];

            $cache->endDataCache($this->arResult);
        }

        $this->includeComponentTemplate();
    }

    /**
     * @return array
     */
    private function getSectionsTree()
    {
        $result = $allSections = [];
        $s = new CIBlockSection();
        $filter = [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $this->iblockId
        ];
        $sort = ['sort' => 'asc', 'UF_MAIN_MENU_COLUMN' => 'desc', 'id' => 'asc'];
        $select = ['ID', 'NAME', 'CODE', 'IBLOCK_SECTION_ID', 'UF_*'];
        $sections = $s->GetList($sort, $filter, false, $select);

        while ($section = $sections->Fetch()) {
            $allSections[$section['ID']] = $section;

            if (!empty($section['IBLOCK_SECTION_ID'])) {
                $allSections[$section['IBLOCK_SECTION_ID']]['SUBSECTIONS'][$section['ID']] = $section;
            } else {
                $result[$section['ID']] = &$allSections[$section['ID']];
            }
        }

        return $result;
    }

    private function getElements()
    {

    }

    /**
     * @param $code
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    private function getIblockIdByCode($code)
    {
        if (empty($code)) return false;

        $cacheId = serialize(['IBLOCK_CODE' => $code]);
        $cache = Cache::createInstance();

        if ($cache->initCache($this->cacheTime, $cacheId, self::CACHE_DIR)) {
            $result = $cache->getVars();
        } elseif ($cache->startDataCache()) {
            $result = \Bitrix\Iblock\IblockTable::getList(['filter' => ['CODE' => $code], 'select' => ['ID']])->fetch();

            $cache->endDataCache($result);
        }

        return $result['ID'] ? $result['ID'] : false;
    }
}