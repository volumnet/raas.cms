<?php
$outputNav = function($Pages, array $options = array())
{
    $pages_list = array();
    $default = array();
    $default['pattern_active'] = '<li class="active"><a>{text}</a></li>';
    $default['pattern'] = '<li><a href="' . \SOME\HTTP::queryString('page={link}') . '">{text}</a></li>';
    $default['trace'] = 2;
    $default['ellipse'] = '<li class="disabled"><a>...</a></li>';
    $default['prev'] = '«';
    $default['next'] = '»';
    $default['sep'] = ' ';
    $options = array_merge($default, $options);
    extract($options);

    if ($Pages->page > 1) {
        $pages_list[] = strtr($pattern, array(urlencode('{link}') => $Pages->page - 1, '{text}' => $prev));
    }
    if ($Pages->page > 1 + $trace) {
        $pages_list[] = strtr($pattern, array(urlencode('{link}') => 1, '{text}' => 1));
    }
    if ($Pages->page > 2 + $trace) {
        if ($Pages->page == 3 + $trace) {
            $pages_list[] = strtr($pattern, array(urlencode('{link}') => 2, '{text}' => 2));
        } else {
            $pages_list[] = $ellipse;
        }
    }
    for ( $i = max(1, $Pages->page - $trace);
          $i <= min($Pages->page + $trace, $Pages->pages);
          $i++) {
        $pages_list[] = strtr(($i == $Pages->page ? $pattern_active : $pattern),
                        array(urlencode('{link}') => $i, '{text}' => $i));
    }
    if ($Pages->page < $Pages->pages - $trace - 1) {
        if ($Pages->page == $Pages->pages - $trace - 2) {
            $pages_list[] = strtr($pattern, array(urlencode('{link}') =>  $Pages->pages - 1,
                                            '{text}' =>  $Pages->pages - 1));
        } else {
            $pages_list[] = $ellipse;
        }
    }
    if ($Pages->page < $Pages->pages - $trace) {
        $pages_list[] = strtr($pattern, array(urlencode('{link}') => $Pages->pages, '{text}' => $Pages->pages));
    }
    if ($Pages->page < $Pages->pages) {
        $pages_list[] = strtr($pattern, array(urlencode('{link}') => $Pages->page + 1, '{text}' => $next));
    }

    $pages_list = implode($sep, $pages_list);
    return $pages_list;
};
