<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsLog extends Model
{
    protected $fillable = ['agent_type', 'action', 'data', 'insight'];
}
