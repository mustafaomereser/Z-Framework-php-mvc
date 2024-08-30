<?php

namespace zFramework\Core\Helpers;

class _Array
{
    /**
     * Paginate for array.
     * @param array $data
     * @param int $per_page
     * @return array
     */
    public static function paginate(array $data, int $per_page = 20, string $page_id = 'page')
    {
        $uniqueID     = uniqid();
        $items_count  = count($data);
        $page_count   = ceil($items_count / $per_page);
        $current_page = request($page_id) ?? 1;
        $last_page    = false;

        if ($current_page <= 0) $current_page = 1;
        elseif ($current_page > $page_count) $current_page = $page_count;
        if ($current_page == $page_count) $last_page = true;

        $stop        = ($per_page * $current_page);
        $start       = $stop - $per_page;
        $items       = array_slice($data, $start, $per_page, true);
        $actual_stop = (!$last_page ? $stop : $start + count($items));

        parse_str(@$_SERVER['QUERY_STRING'], $queryString);
        $queryString[$page_id] = "change_page_$uniqueID";
        $url = "?" . http_build_query($queryString);

        return [
            'items'        => $items,
            'is_last_page' => $last_page,
            'current_page' => $current_page,
            'per_page'     => $per_page,
            'page_count'   => $page_count,

            'start'        => $start,
            'stop'         => $stop,
            'actual_stop'  => $actual_stop,

            'shown'        => "$actual_stop of $items_count",

            'links'        => function (string $view = null) use ($page_count, $current_page, $url, $uniqueID) {
                if (!$view) $view = config('app.pagination.default-view');

                $pages = [];
                $dots  = false;
                if (!$dots) {
                    for ($x = 1; $x <= $page_count; $x++) {
                        $pages[$x] = [
                            'type'    => 'page',
                            'page'    => $x,
                            'current' => $x == $current_page,
                            'url'     => str_replace("change_page_$uniqueID", $x, $url)
                        ];
                    }
                } else {
                    $shown = 20;
                    $x      = $current_page - $shown;
                    if ($x <= 0) $x = 1;

                    $stop   = false;
                    $finish = false;

                    while (!$stop) {
                        $pages[] = [
                            'type'    => 'page',
                            'page'    => $x,
                            'current' => $x == $current_page,
                            'url'     => str_replace("change_page_$uniqueID", $x, $url)
                        ];
                        if ($x == $page_count) {
                            $finish = true;
                            break;
                        }
                        $x++;
                        if (($shown / 2) + 1 == count($pages)) $stop = true;
                    }

                    if (!$finish) {
                        $pages[] = ['type' => 'dot'];
                        $x += $shown;
                        if ($x > $page_count) $x = $page_count;
                        $stop = false;
                        while (!$stop) {
                            $pages[] = [
                                'type'    => 'page',
                                'page'    => $x,
                                'current' => $x == $current_page,
                                'url'     => str_replace("change_page_$uniqueID", $x, $url)
                            ];
                            if ($x == $page_count) break;

                            $x++;
                            if (($shown + 1) == count($pages)) $stop = true;
                        }
                    }
                }

                return view($view, compact('pages'));
            }
        ];
    }

    /**
     * Array Compare. 
     * desctiption: Array keys must ineteger
     * @param array $array1
     * @param array $array2
     * @param \Closure $callback
     * @return array
     */
    public static function compare(array $array1, array $array2, \Closure $callback): array
    {
        $output = [];
        foreach ($array1 as $key => $value) if ($callback($value, $array2[$key])) $output[$key] = $value;
        return $output;
    }
}
