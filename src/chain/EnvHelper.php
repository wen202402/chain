<?php

namespace wen202402\chain\chain;

class EnvHelper{
    public static function load($envFile){
        if (!is_file($envFile)) return false;
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) continue;

            [$k, $v] = array_pad(explode('=', $line, 2), 2, null);
            $k = trim($k);
            $v = trim($v ?? '');

            if ($k !== '') {
                putenv("$k=$v");
                $_ENV[$k] = $v;
            }
        }

        return true;
    }
}