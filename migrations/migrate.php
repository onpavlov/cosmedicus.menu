<?php

require_once 'lib/include.php';

use Custom\Migrations\IblockMigration;
use Custom\Migrations\PropertyMigration;
use Custom\Migrations\SectionMigration;
use Custom\Migrations\UserFieldMigration;

define('IBLOCK_MENU_CODE', 'main_menu');

/* Создание инфоблока "Основное меню" */
$iblock = new IblockMigration(
    'Создание инфоблока Основного меню',
    [
        'ACTIVE'           => 'Y',
        'NAME'             => 'Основное меню',
        'CODE'             => IBLOCK_MENU_CODE,
        'LIST_PAGE_URL'    => '',
        'DETAIL_PAGE_URL'  => '',
        'IBLOCK_TYPE_ID'   => 'content',
        'SITE_ID'          => ['s1'],
        'SORT'             => 100,
        'VERSION'          => 2,
        'GROUP_ID'         => [
            '1' => 'X',
            '2' => 'R'
        ],
        'FIELDS' => []
    ]
);

$iblock->add();

if ($menuIblockId = $iblock->isExist()) {
    $properties = [
        new PropertyMigration('Создание свойства "Относительный URL" для инфоблока теговых страниц', [
            'NAME'          => 'Относительный URL',
            'ACTIVE'        => 'Y',
            'SORT'          => 100,
            'CODE'          => 'FULL_URL',
            'PROPERTY_TYPE' => 'S',
            'IS_REQUIRED'   => 'Y',
            'IBLOCK_ID'     => $menuIblockId,
            'HINT'          => 'Задайте относительный URL',
            'DEFAULT_VALUE' => '/catalog/'
        ]),
    ];

    foreach ($properties as $property) {
        $property->add();
    }

    /* Создание разделов для инфоблока Основного меню */
    $sections = [
        new SectionMigration('Создание раздела "Медтехника"', [
            'IBLOCK_ID' => $menuIblockId,
            'IBLOCK_CODE' => IBLOCK_MENU_CODE,
            'CODE' => 'medtehnika',
            'NAME' => 'Медтехника',
            'ACTIVE' => 'Y'
        ]),

        new SectionMigration('Создание раздела "Массажное оборудование"', [
            'IBLOCK_ID' => $menuIblockId,
            'CODE' => 'massazhery',
            'NAME' => 'Массажное оборудование',
            'ACTIVE' => 'Y'
        ]),

        new SectionMigration('Создание раздела "Оборудование и мебель для салонов"', [
            'IBLOCK_ID' => $menuIblockId,
            'CODE' => 'oborudovanie-dlya-salonov-krasoty',
            'NAME' => 'Оборудование и мебель для салонов',
            'ACTIVE' => 'Y'
        ]),

        new SectionMigration('Создание раздела "Спорт и фитнес"', [
            'IBLOCK_ID' => $menuIblockId,
            'CODE' => 'sportivnye-tovary',
            'NAME' => 'Спорт и фитнес',
            'ACTIVE' => 'Y'
        ]),

        new SectionMigration('Создание раздела "Косметология"', [
            'IBLOCK_ID' => $menuIblockId,
            'CODE' => 'kosmetologiya',
            'NAME' => 'Косметология',
            'ACTIVE' => 'Y'
        ]),

        new SectionMigration('Создание раздела "Еще"', [
            'IBLOCK_ID' => $menuIblockId,
            'CODE' => '',
            'NAME' => 'Еще',
            'ACTIVE' => 'Y'
        ])
    ];

    foreach ($sections as $section) {
        $section->add();
    }

    // Пользовательское св-во "URL раздела"
    $userField = new UserFieldMigration(
        'Добавление свойства разделов URL раздела',
        array(
            'ENTITY_ID' => 'IBLOCK_' . $menuIblockId . '_SECTION',
            'FIELD_NAME' => 'UF_MAIN_MENU_URL',
            'USER_TYPE_ID' => "string",
            'SORT' => '100',
            'XML_ID' => 'UF_MAIN_MENU_URL',
            'SHOW_FILTER' => 'Y',
            'SETTINGS' => [
                'IBLOCK_TYPE_ID' => 'content',
                'IBLOCK_ID' => $menuIblockId,
                'DISPLAY' => 'LIST',
                'ACTIVE_FILTER' => 'Y'
            ],
            'EDIT_FORM_LABEL' => [
                'ru' => 'URL раздела',
                'en' => "Section URL",
            ],
            'LIST_COLUMN_LABEL' => ['ru'=>'','en'=>''],
            'LIST_FILTER_LABEL' => ['ru'=>'','en'=>''],
            'ERROR_MESSAGE' => ['ru'=>'','en'=>''],
            'HELP_MESSAGE' => ['ru'=>'','en'=>''],
        )
    );
    $userField->add();

    // Пользовательское св-во "Раздел брендов"
    $userField = new UserFieldMigration(
        'Добавление свойства разделов Раздел брендов',
        array(
            'ENTITY_ID' => 'IBLOCK_' . $menuIblockId . '_SECTION',
            'FIELD_NAME' => 'UF_MAIN_MENU_BRANDS',
            'USER_TYPE_ID' => "boolean",
            'SORT' => '100',
            'XML_ID' => 'UF_MAIN_MENU_BRANDS',
            'SHOW_FILTER' => 'Y',
            'SETTINGS' => [
                'IBLOCK_TYPE_ID' => 'content',
                'IBLOCK_ID' => $menuIblockId,
                'DISPLAY' => 'LIST',
                'ACTIVE_FILTER' => 'Y'
            ],
            'EDIT_FORM_LABEL' => [
                'ru' => 'Раздел брендов',
                'en' => "Brand's section",
            ],
            'LIST_COLUMN_LABEL' => ['ru'=>'','en'=>''],
            'LIST_FILTER_LABEL' => ['ru'=>'','en'=>''],
            'ERROR_MESSAGE' => ['ru'=>'','en'=>''],
            'HELP_MESSAGE' => ['ru'=>'','en'=>''],
        )
    );
    $userField->add();

    // Пользовательское св-во "Колонка"
    $userField = new UserFieldMigration(
        'Добавление свойства разделов Колонка',
        array(
            'ENTITY_ID' => 'IBLOCK_' . $menuIblockId . '_SECTION',
            'FIELD_NAME' => 'UF_MAIN_MENU_COLUMN',
            'USER_TYPE_ID' => "enumeration",
            'SORT' => '100',
            'XML_ID' => 'UF_MAIN_MENU_COLUMN',
            'SHOW_FILTER' => 'Y',
            'SETTINGS' => [
                'IBLOCK_TYPE_ID' => 'content',
                'IBLOCK_ID' => $menuIblockId,
                'DISPLAY' => 'LIST',
                'ACTIVE_FILTER' => 'Y'
            ],
            'EDIT_FORM_LABEL' => [
                'ru' => 'В какой колонке выводить раздел',
                'en' => "Which column display the section",
            ],
            'LIST_COLUMN_LABEL' => ['ru'=>'','en'=>''],
            'LIST_FILTER_LABEL' => ['ru'=>'','en'=>''],
            'ERROR_MESSAGE' => ['ru'=>'','en'=>''],
            'HELP_MESSAGE' => ['ru'=>'','en'=>''],
        )
    );

    if ($uFieldId = $userField->add()) {
        $enum = new CUserFieldEnum();
        $values = array(
            'n0' => array(
                'XML_ID' => 'first_menu_column',
                'VALUE' => 'Колонка 1',
                'DEF' => 'N',
                'SORT' => '100'
            ),
            'n1' => array(
                'XML_ID' => 'second_menu_column',
                'VALUE' => 'Колонка 2',
                'DEF' => 'N',
                'SORT' => '200'
            ),
            'n2' => array(
                'XML_ID' => 'third_menu_column',
                'VALUE' => 'Колонка 3',
                'DEF' => 'N',
                'SORT' => '300'
            ),
        );

        if (!$enum->SetEnumValues($uFieldId, $values)) {
            $userField->writeLine('Не удалось добавить значения к пользовательскому свойству');
        }
    }
}