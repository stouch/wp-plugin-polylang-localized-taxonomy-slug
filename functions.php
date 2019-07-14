<?php

// ...
// I spend so many hours to find this hack ! I want to share to you <3

// for example, my taxonomy are boards (planches in french) :

// some static slug that will be replaced
define('STATIC_BOARDS_TAXONOMY_SLUG', 'PLANCHES_SLUG');

// my dynamic base slug for my boards will be stored in this Polylang string :
define('PLL_BOARDS_TAXONOMY_SLUG_KEY', 'planches_slug'); // "planches_slug" in Polylang/Translation Strings

function wpm_add_taxonomies()
{
    // ...
    
    $args_cat_serie = array(
        'hierarchical' => false,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array(
            'slug' => STATIC_BOARDS_TAXONOMY_SLUG // some static random string in base slug, that will be replaced
        ),
    );
    
    register_taxonomy('planche', 'image', $args_cat_serie);
}

add_action('init', 'wpm_add_taxonomies', 0);

// Rewrite rules
add_filter('option_rewrite_rules', 'show_rewrite_rules');
function show_rewrite_rules($rules)
{
    $k = 0;
    $v = pll__(PLL_BOARDS_TAXONOMY_SLUG_KEY);
    if ($rules) {
        foreach ($rules as $regex => $rewrite) {
            if (strstr($regex, STATIC_BOARDS_TAXONOMY_SLUG . '/([^/]+)')) {
                unset($rules[$regex]);
                // the idea is to replace the [regex => rewrite] at the same place of the keystring-value array
                $rules = array_slice($rules, 0, $k, true) +
                    array(str_replace(STATIC_BOARDS_TAXONOMY_SLUG . '/([^/]+)', $v . '/([^/]+)', $regex) => $rewrite) +
                    array_slice($rules, $k, count($rules) - 1, true);
            }
            $k++;
        }
        return $rules;
    }
}

// Don't forget the permalinks
add_filter('term_link', 'term_link_filter', 10, 3);
function term_link_filter($url, $term, $taxonomy)
{
    $lang = pll_get_term_language($term->term_id);
    $v = pll_translate_string(PLL_BOARDS_TAXONOMY_SLUG_KEY, $lang);
    return str_replace(STATIC_BOARDS_TAXONOMY_SLUG, $v, $url);
}

// ...