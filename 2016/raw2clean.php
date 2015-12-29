<?php

require_once __DIR__ . '/libs.php';

foreach (glob(__DIR__ . '/raw/*/*.txt') AS $file) {
    cleanFile($file);
}