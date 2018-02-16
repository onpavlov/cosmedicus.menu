<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>

<?
$sections = $arResult['SECTIONS'];
$elements = $arResult['ELEMENTS'];
?>

<? if (!empty($sections) && !empty($elements)): ?>
    <div class="wrapper header__wrapper">
        <div class="container">
            <nav class="topmenu topmenu--catalog">
                <ul class="topmenu__list">
                    <? foreach ($sections as $section):
                        $mainSectionUrl = empty($section['CODE']) ? '' : $arParams['ROOT_PATH'] . $section['CODE'] . '/';
                        $url = empty($section['UF_MAIN_MENU_URL']) ? $mainSectionUrl : $section['UF_MAIN_MENU_URL'];
                    ?>
                        <li class="topmenu__item js-topmenu-dropdown">
                            <a href="<?= $url ?>" class="topmenu__link"><?= $section['NAME'] ?></a>
                            <? if (!empty($section['SUBSECTIONS'])): ?>
                                <div class="child cat_menu">
                                    <? foreach ($section['SUBSECTIONS'] as $columnCode => $subsection):
                                        if ($section['ELEMENT_CNT'] == 0 || $columnCode === 'brands') continue;
                                    ?>
                                        <ul class="menu-colons <?= $columnCode ?>">
                                            <? foreach ($subsection as $group):
                                                $subSectionUrl = empty($group['CODE']) ? '' : $mainSectionUrl . $group['CODE'] . '/';
                                                $url = empty($group['UF_MAIN_MENU_URL']) ? $subSectionUrl : $group['UF_MAIN_MENU_URL'];
                                            ?>
                                                <li class="menu_title"><a href="<?= $url ?>"><?= $group['NAME'] ?></a></li>
                                                <? if (!empty($arResult['ELEMENTS'][$group['ID']])): ?>
                                                    <? foreach ($arResult['ELEMENTS'][$group['ID']] as $element):
                                                    $elementUrl = empty($element['CODE']) ? '' : $subSectionUrl . $element['CODE'] . '/';
                                                    $url = empty($element['PROPERTY_FULL_URL_VALUE']) ? $elementUrl : $element['PROPERTY_FULL_URL_VALUE'];
                                                    ?>
                                                        <li class="menu_item"><a href="<?= $url ?>"><?= $element['NAME'] ?></a></li>
                                                    <? endforeach; ?>
                                                <? endif; ?>
                                            <? endforeach; ?>
                                        </ul>
                                    <? endforeach; ?>
                                    <? if (!empty($section['SUBSECTIONS']['brands'])):
                                        $subSectionUrl = $mainSectionUrl . $section['SUBSECTIONS']['brands']['CODE'] . '/';
                                    ?>
                                        <ul class="menu-colons brands">
                                            <li class="menu_title"><a href="<?= $subSectionUrl ?>"><?= $section['SUBSECTIONS']['brands']['NAME'] ?></a></li>
                                            <? foreach ($arResult['ELEMENTS'][$group['ID']] as $element):
                                                $elementUrl = empty($element['CODE']) ? '' : $subSectionUrl . $element['CODE'] . '/';
                                                $url = empty($element['PROPERTY_FULL_URL_VALUE']) ? $elementUrl : $element['PROPERTY_FULL_URL_VALUE'];
                                                ?>
                                                <li class="menu_item"><a href="<?= $url ?>"><?= $element['NAME'] ?></a></li>
                                            <? endforeach; ?>
                                        </ul>
                                    <? endif; ?>
                                </div>
                            <? endif; ?>
                        </li>
                    <? endforeach; ?>
                </ul>
            </nav>
        </div>
    </div>
<? endif; ?>