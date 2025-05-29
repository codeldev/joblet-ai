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

Schedule::command(command: 'blog:ideas:process')
    ->everyTwoHours()
    ->between('10.00', '22:00')
    ->withoutOverlapping();

Schedule::command(command: 'blog:ideas:queue')
    ->everyTwoHours()
    ->between('10.00', '22:00')
    ->withoutOverlapping();

Schedule::command(command: 'blog:posts:publish')
    ->dailyAt(time: '1.00')
    ->withoutOverlapping();

Schedule::command(command: 'blog:posts:youtube')
    ->weekly()
    ->sundays()
    ->at(time: '05:00')
    ->withoutOverlapping();
