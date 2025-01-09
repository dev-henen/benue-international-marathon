<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Register extends Model
{
    protected $table = 'registrations';
    protected $primaryKey = 'id';
    public $timestamps = false; 
}
