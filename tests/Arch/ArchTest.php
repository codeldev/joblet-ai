<?php

/** @noinspection PhpUndefinedMethodInspection */

declare(strict_types=1);

arch()
    ->preset()
    ->laravel()
    ->expect('App')
    ->toUseStrictTypes()
    ->toUseStrictEquality();
