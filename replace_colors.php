<?php
$file = 'resources/views/layouts/app.blade.php';
$content = file_get_contents($file);

$content = str_replace(
    ['--theme-white: #2e3b4e', '--theme-bg: #1a222c', '#3b4b5e', '#4b5b6e', '#2e3b4e'],
    ['--theme-white: #1e1e1e', '--theme-bg: #121212', '#2d2d2d', '#3d3d3d', '#1a1a1a'],
    $content
);

file_put_contents($file, $content);
echo "Colors replaced successfully.";
