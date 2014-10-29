<?php
$showMenu = function(\RAAS\CMS\Page $page) use (&$showMenu) {
    $children = $page->visChildren;
    for ($i = 0; $i < count($children); $i++) {
        $row = $children[$i];
        if (!$row->response_code) {
            $text .= '<url><loc>http://' . htmlspecialchars($_SERVER['HTTP_HOST'] . $row->url) . '</loc></url>';
            foreach ($row->affectedMaterials as $row2) {
                $text .= '<url><loc>http://' . htmlspecialchars($_SERVER['HTTP_HOST'] . $row->url . $row->urn) . '/</loc></url>';
            }
            $text .= $showMenu($row);
        }
    }
    return $text;
};

header('Content-Type: application/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?' . '>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . $showMenu(new \RAAS\CMS\Page()) . '</urlset>';