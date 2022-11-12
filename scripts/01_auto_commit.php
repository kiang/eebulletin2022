<?php
$path = dirname(__DIR__);
exec("/usr/bin/find {$path} -type f", $lines);

$currentSize = 0;
$limit = pow(1024, 2) * 500;
foreach ($lines as $line) {
    $size = filesize($line);
    $currentSize += $size;
    exec("/usr/bin/git add '{$line}'");
    if ($currentSize > $limit) {
        $now = date('Y-m-d H:i:s');
        exec("cd {$path} && /usr/bin/git commit --author 'auto commit <noreply@localhost>' -m 'auto update @ {$now}'");
        exec("cd {$path} && /usr/bin/git push origin master");
        $currentSize = 0;
    }
}
