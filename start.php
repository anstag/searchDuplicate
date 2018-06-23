<?php
ini_set('memory_limit', '200M');

if (empty($argv[1])) {
    exit('choose folder');
}

$folder = $argv[1];
$dir = __DIR__ . DIRECTORY_SEPARATOR . $folder;

$pathFiles = scanDirectories($dir, $allData = []);
$hashImg = [];
$duplicate = [];

foreach ($pathFiles as $path) {
	echo 'Hash ' . $path . PHP_EOL;
    $type = mime_content_type($path);

    if ($type == 'image/jpeg' || $type == 'image/png') {
        $hashImg[$path] = hash_file('md5', $path);
    }
}

foreach ($hashImg as $path1 => $item) {
	echo 'Compare ' . $path1 . PHP_EOL;

    foreach ($hashImg as $path2 => $match) {
        if ($path1 == $path2) {
            continue;
        }

        if ($item == $match) {
            if (!in_array($path1, $duplicate[$item])) {
                $duplicate[$item][] = $path1;
            }

            if (!in_array($path2, $duplicate[$item])) {
                $duplicate[$item][] = $path2;
            }
        }
    }
}

// write log file
if (!empty($duplicate)) {
    $f = fopen('log.txt', 'w');

    foreach ($duplicate as $item) {
        foreach ($item as $path) {
            fwrite($f, $path . PHP_EOL);
        }
    }

    fclose($f);
}

exit('[done] look log.txt');





/**
 * @param $dir
 * @param array $allData
 * @return array
 */
function scanDirectories($dir, $allPath = []) {
    $ignore = [".", ".."];
    $dirContent = scandir($dir);

    foreach($dirContent as $key => $content) {
        $path = $dir . DIRECTORY_SEPARATOR . $content;

        if (!in_array($content, $ignore)) {
            if (is_file($path) && is_readable($path)) {
                $allPath[] = $path;
            } elseif (is_dir($path) && is_readable($path)) {
                $allPath = scanDirectories($path, $allPath);
            }
        }
    }
    return $allPath;
}