<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubMenu extends Model
{
    use HasFactory;

    public function routeUrl()
    {
        return $this->belongsTo(AllRouteUrl::class);
    }
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
