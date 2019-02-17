<?php
/**
 * This plugin adds some custom taxonomies for testing the Terms Archive plugin.
 *
 * @package ta_custom_taxonomies
 */

/**
 * Plugin Name: Terms Archive Custom Taxonomies
 * Plugin URI: https://github.com/ssnepenthe/terms-archive
 * Description: This plugin is for testing purposes only.
 * Version: 1.0.0
 * Author: Ryan McLaughlin
 * Author URI: https://github.com/ssnepenthe
 * License: GPL-2.0
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

add_action('init', function () {
    // Supported, public.
    register_taxonomy('ta_sp', 'post', [
        'labels' => [
            'name' => 'SP',
        ],
    ]);
    register_taxonomy_for_object_type('ta_sp', 'post');

    // Not supported, public.
    register_taxonomy('ta_nsp', 'post', [
        'labels' => [
            'name' => 'NSP',
        ],
    ]);
    register_taxonomy_for_object_type('ta_nsp', 'post');

    // Supported, not public.
    register_taxonomy('ta_snp', 'post', [
        'labels' => [
            'name' => 'SNP',
        ],
        'public' => false,
    ]);
    register_taxonomy_for_object_type('ta_snp', 'post');

    // Not supported, not public.
    register_taxonomy('ta_nsnp', 'post', [
        'labels' => [
            'name' => 'NSNP',
        ],
        'public' => false,
    ]);
    register_taxonomy_for_object_type('ta_nsnp', 'post');

    // Supported, not publicly queryable.
    register_taxonomy('ta_snpq', 'post', [
        'labels' => [
            'name' => 'SNPQ',
        ],
        'publicly_queryable' => false
    ]);
    register_taxonomy_for_object_type('ta_snpq', 'post');

    // Not supported, not publicly queryable.
    register_taxonomy('ta_nsnpq', 'post', [
        'labels' => [
            'name' => 'NSNPQ',
        ],
        'publicly_queryable' => false
    ]);
    register_taxonomy_for_object_type('ta_nsnpq', 'post');

    // Supported, rewrite disabled.
    register_taxonomy('ta_srd', 'post', [
        'labels' => [
            'name' => 'SRD',
        ],
        'rewrite' => false,
    ]);
    register_taxonomy_for_object_type('ta_srd', 'post');

    // Not supported, rewrite disabled.
    register_taxonomy('ta_nsrd', 'post', [
        'labels' => [
            'name' => 'NSRD',
        ],
        'rewrite' => false,
    ]);
    register_taxonomy_for_object_type('ta_nsrd', 'post');
});

(function () {
    $handler = function () {
        delete_option('rewrite_rules');
    };

    register_activation_hook(__FILE__, $handler);
    register_deactivation_hook(__FILE__, $handler);
})();
