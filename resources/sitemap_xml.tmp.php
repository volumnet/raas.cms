<?php
$getChangeFreq = function($row) {
    $text = '';
    if ($row->changefreq) {
        $text .= '<changefreq>' . htmlspecialchars($row->changefreq) . '</changefreq>';
    } else {
        $d0 = max(0, strtotime($row->post_date));
        $s = ((time() - $d0) / $row->modify_counter);
        $text .= '<changefreq>';
        if ($s < 1800) {
            $text .= 'always';
        } elseif ($s < 2 * 3600) {
            $text .= 'hourly';
        } elseif ($s < 2 * 86400) {
            $text .= 'daily';
        } elseif ($s < 2 * 7 * 86400) {
            $text .= 'weekly';
        } elseif ($s < 2 * 30 * 86400) {
            $text .= 'monthly';
        } elseif ($s < 2 * 365 * 86400) {
            $text .= 'yearly';
        } else {
            $text .= 'never';
        }
        $text .= '</changefreq>';
    }
    return $text;
};
$showMenu = function(\RAAS\CMS\Page $page) use (&$showMenu, &$getChangeFreq) {
    $children = $page->visChildren;
    for ($i = 0; $i < count($children); $i++) {
        $row = $children[$i];
        if (!$row->response_code) {
            $text .= '<url>
                        <loc>http://' . htmlspecialchars($_SERVER['HTTP_HOST'] . $row->url) . '</loc>';
            if (strtotime($row->last_modified) > 0) {
                $text .= '<lastmod>' . date(DATE_W3C, strtotime($row->last_modified)) . '</lastmod>';
            }
            $text .= $getChangeFreq($row);
            $text .= '<priority>' . str_replace(',', '.', (float)$row->sitemaps_priority) . '</priority>';
            $text .= '</url>';
            foreach ($row->affectedMaterials as $row2) {
                if ($row2->parent->id == $row->id) {
                    $text .= '<url>
                                <loc>http://' . htmlspecialchars($_SERVER['HTTP_HOST'] . $row2->url) . '</loc>';
                    if (strtotime($row->last_modified) > 0) {
                        $text .= '<lastmod>' . date(DATE_W3C, strtotime($row2->last_modified)) . '</lastmod>';
                    }
                    $text .= $getChangeFreq($row2);
                    $text .= '<priority>' . str_replace(',', '.', (float)$row2->sitemaps_priority) . '</priority>';
                    $text .= '</url>';
                }
            }
            $text .= $showMenu($row);
        }
    }
    return $text;
};

header('Content-Type: application/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?' . '>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . $showMenu(new \RAAS\CMS\Page()) . '</urlset>';