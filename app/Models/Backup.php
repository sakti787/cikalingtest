<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table(name: 'backups', key: 'backup_id', timestamps: false)]
#[Fillable(['backup_date', 'backup_location', 'status', 'file_path'])]
class Backup extends Model
{
    //
}
