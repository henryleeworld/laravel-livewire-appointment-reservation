<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['start_time', 'confirmed', 'reserved_at'])]
class Appointment extends Model
{
}
