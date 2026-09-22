<?php

$config = new Prooph\CS\Config\Prooph();
$config->getFinder()
    ->in(__DIR__)
    ->exclude('vendor')
    ->exclude('test/Command/Fixture/var')
    // New file authored for this fork; it must not carry the upstream authors' copyright header.
    ->notPath('src/Resources/config/event_store.php');

return $config;
