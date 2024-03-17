<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Process extends Model
{

    use HasFactory;
    protected $table = 'processes'; // Assuming your table name is 'processes'


    protected $primaryKey = 'process_id'; // Specify the primary key column name

    protected $fillable = ['process_name', 'process_owner', 'prcdept_name', 'prc_desc', 'prc_doc', 'prc_QR'];

}
