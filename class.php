<?php

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Loader;
use Bitrix\Main\Context;

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
        if ($this->arParams['TEST_MODE']) {
            $request = Context::getCurrent()->getRequest();

            if (is_null($request->get('on_new_menu'))) return;
        }

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

        $cols = $this->getUserFields('UF_MAIN_MENU_COLUMN');
        $filter = [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $this->iblockId
        ];
        $sort = [
            'sort' => 'asc',
            'UF_MAIN_MENU_COLUMN' => 'asc',
            'id' => 'asc'
        ];
        $sections = $s->GetList($sort, $filter, ['ELEMENT_SUBSECTIONS' => 'Y'], ['ID', 'NAME', 'CODE', 'IBLOCK_SECTION_ID', 'UF_*']);

        while ($section = $sections->Fetch()) {
            if (!empty($section['UF_MAIN_MENU_COLUMN'])) {
                $section['UF_MAIN_MENU_COLUMN_CODE'] = $cols[$section['UF_MAIN_MENU_COLUMN']]['XML_ID'];
            }

            if ($this->arParams['ADD_ROOT'] == 'Y') {
                $section['UF_MAIN_MENU_URL'] = $this->getFullUrl($section['UF_MAIN_MENU_URL']);
            }

            $allSections[$section['ID']] = isset($allSections[$section['ID']])
                ? array_merge($section, $allSections[$section['ID']])
                : $section;

            if ($section['UF_MAIN_MENU_BRANDS'] == 1) {
                $allSections[$section['IBLOCK_SECTION_ID']]['SUBSECTIONS']['brands'] = $section;
            } elseif (!empty($section['IBLOCK_SECTION_ID'])) {
                $allSections[$section['IBLOCK_SECTION_ID']]['SUBSECTIONS'][$section['UF_MAIN_MENU_COLUMN_CODE']][$section['ID']] = $section;
            } else {
                $result[$section['ID']] = &$allSections[$section['ID']];
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getElements()
    {
        $result = [];
        $e = new CIBlockElement();

        $filter = [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $this->iblockId
        ];
        $select = ['ID', 'IBLOCK_ID', 'CODE', 'NAME', 'IBLOCK_SECTION_ID', 'PROPERTY_FULL_URL'];
        $elements = $e->GetList([], $filter, false, false, $select);

        while ($element = $elements->Fetch()) {
            if ($this->arParams['ADD_ROOT'] == 'Y') {
                $element['PROPERTY_FULL_URL_VALUE'] = $this->getFullUrl($element['PROPERTY_FULL_URL_VALUE']);
            }

            $result[$element['IBLOCK_SECTION_ID']][$element['ID']] = $element;
        }

        return $result;
    }

    /**
     * @param $ufCode
     * @return array
     */
    private function getUserFields($ufCode)
    {
        if (empty($ufCode)) return [];

        $result = [];
        $fields = new CUserFieldEnum();

        $props = $fields->GetList([], ['USER_FIELD_NAME' => $ufCode]);

        while ($prop = $props->Fetch()) {
            $result[$prop['ID']] = $prop;
        }

        return $result;
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

    /**
     * @param $url
     * @return mixed|string
     */
    private function getFullUrl($url)
    {
        if (!empty($url) && strpos($url, $this->arParams['ROOT_PATH']) === false) {
            if ($url[0] === '/') {
                $url = substr_replace($url, '', 0, 1);
            }

            $url = $this->arParams['ROOT_PATH'] . $url;
        }

        return $url;
    }
}