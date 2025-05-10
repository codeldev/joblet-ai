<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::command(command: 'backup:create')
    ->daily()
    ->at(time: '01:00')
    ->withoutOverlapping();

Schedule::command(command: 'backup:clean')
    ->daily()
    ->at(time: '03:00')
    ->withoutOverlapping();
