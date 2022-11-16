<?php
$path = dirname(__DIR__);
exec("/usr/bin/find {$path} -type f", $lines);

$currentSize = 0;
$limit = pow(1024, 2) * 500;
$skip = pow(1024, 2) * 100;
$zip = new ZipArchive();

foreach ($lines as $line) {
    $size = filesize($line);
    $p = pathinfo($line);
    $zipFile = $p['dirname'] . '/' . $p['filename'] . '.zip';
    if ($size > $skip) {
        if (!file_exists($zipFile)) {
            $zip->open($zipFile,  ZipArchive::CREATE);
            $zip->addFile($line);
            $zip->close();
        }
    }
    if (!file_exists($zipFile)) {
        $currentSize += $size;
        exec("/usr/bin/git add '{$line}'");
    } else {
        $size = filesize($zipFile);
        if ($size > $skip) {
            continue;
        } else {
            $currentSize += $size;
            exec("/usr/bin/git add '{$zipFile}'");
        }
    }

    if ($currentSize > $limit) {
        $now = date('Y-m-d H:i:s');
        exec("cd {$path} && /usr/bin/git commit --author 'auto commit <noreply@localhost>' -m 'auto update @ {$now}'");
        exec("cd {$path} && /usr/bin/git push origin master");
        $currentSize = 0;
    }
}

exec("cd {$path} && /usr/bin/git commit --author 'auto commit <noreply@localhost>' -m 'auto update @ {$now}'");
exec("cd {$path} && /usr/bin/git push origin master");
