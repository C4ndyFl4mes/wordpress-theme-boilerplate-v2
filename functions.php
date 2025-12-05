<?php

// Används för att kolla om det körs lokalt eller inte.
function vite_server_running($host = 'localhost', $port = 5173) {
    $connection = @fsockopen($host, $port, $errno, $errstr, 0.2);

    if ($connection) {
        fclose($connection);
        return true; // Vite dev server is running
    }
    return false; // Vite is not running
}

// Slår ihop själva projektet till en fungerande webbplats.
function mytheme_enqueue_assets() {

    $theme_dir  = get_template_directory_uri();
    $theme_path = get_template_directory();
    $manifest_path = $theme_path . '/dist/manifest.json';

    // Detect Vite dev server
    $is_dev = vite_server_running();

    if ($is_dev) {
        // DEV MODE
        wp_enqueue_script(
            'mytheme-js',
            'http://localhost:5173/main.js',
            [],
            null,
            true
        );

        wp_enqueue_style(
            'mytheme-css',
            'http://localhost:5173/main.css'
        );

        return;
    }

    // PROD MODE
    if (!file_exists($manifest_path)) return;

    $manifest = json_decode(file_get_contents($manifest_path), true);
    $entry = $manifest['main.js'];

    wp_enqueue_script(
        'mytheme-js',
        $theme_dir . '/dist/' . $entry['file'],
        [],
        null,
        true
    );

    if (!empty($entry['css'][0])) {
        wp_enqueue_style(
            'mytheme-css',
            $theme_dir . '/dist/' . $entry['css'][0],
            [],
            null
        );
    }
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_assets');