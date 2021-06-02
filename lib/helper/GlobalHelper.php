<?php

function pager_navigation($pager, $uri)
{
    $navigation = '<div class="pagerDRUPAL">';

    if ($pager->haveToPaginate()) {
        $uri .= ( preg_match('/\?/', $uri) ? '&' : '?') . 'page=';

        // First and previous page
        if ($pager->getPage() != 1) {
            //$navigation .= link_to(' эхлэл', $uri . '1', array('class' => 'pager-next active'));
            //$navigation .= link_to(' өмнөх', $uri . $pager->getPreviousPage(), array('class' => 'pager-next active'));
        }

        $navigation .= link_to(' өмнөх', $uri . $pager->getPreviousPage(), array('class' => 'pager-next active'));

        $navigation .= '<div class="pager-list">';

        // Pages one by one
        $links = array();
        foreach ($pager->getLinks(15) as $page) {
            if ($page == $pager->getPage()) {
                $navigation .= '<strong>' . $page . '</strong>';
            } else {
                $navigation .= link_to($page, $uri . $page, array('class' => 'pager-next active'));
            }
        }

        $navigation .= '</div>';

        // Next and last page
        if ($pager->getPage() != $pager->getLastPage()) {
            //$navigation .= link_to('дараагийн', $uri . $pager->getNextPage(), array('class' => 'pager-next active'));
            //$navigation .= link_to('төгсгөл', $uri . $pager->getLastPage(), array('class' => 'pager-next active'));
        }

        $navigation .= link_to('дараагийн', $uri . $pager->getNextPage(), array('class' => 'pager-next active'));
    }

    $navigation .= '</div>';

    return $navigation;
}

function numberFormat($number)
{
    return number_format($number, 2, '.', ',');
}

/**
 * @uses sidebar/_mainmenu
 *
 * @param string $current
 * @param string $tab
 * @return string
 */
function isCurrentTab($current, $tab)
{
    if ($current == $tab) {
        return 'current';
    }

    return '';
}
