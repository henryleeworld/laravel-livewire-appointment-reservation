<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('appointment:clear-expired')->everySecond();
