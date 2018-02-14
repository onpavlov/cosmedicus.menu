<?php

require_once 'lib/include.php';

use Alcodream\Migrations\IblockMigration;
use Alcodream\Migrations\PropertyMigration;
use Alcodream\Migrations\SectionMigration;

define('IBLOCK_MENU_CODE', 'main_menu');

/* Создание инфоблока "Основное меню" */
$iblock = new IblockMigration(
    'Создание инфоблока теговых страниц',
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
        'FIELDS'            => [
            'CODE' => [
                'IS_REQUIRED' => 'Y'
            ]
        ]
    ]
);

$iblock->add();

if ($menuIblockId = $iblock->isExist()) {
    $properties = [
        new PropertyMigration('Создание свойства "URL" для инфоблока теговых страниц', [
            'NAME'          => 'URL',
            'ACTIVE'        => 'Y',
            'SORT'          => 100,
            'CODE'          => 'URL',
            'PROPERTY_TYPE' => 'S',
            'IS_REQUIRED'   => 'Y',
            'IBLOCK_ID'     => $menuIblockId,
            'HINT'          => 'Задайте относительный URL'
        ]),
    ];

    foreach ($properties as $property) {
        $property->add();
    }

    /* Создание разделов для инфоблока Основного меню */
    $sections = [
        new SectionMigration('Создание раздела "Медтехника"', [
            'IBLOCK_ID' => $menuIblockId,
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
        ])
    ];

    foreach ($sections as $section) {
        $section->add();
    }
}