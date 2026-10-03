<?php

use Illuminate\Support\Facades\Schedule;

// Remove códigos 2FA já usados
Schedule::command('user-two-factor-codes:delete-used')
    ->dailyAt('23:00');
