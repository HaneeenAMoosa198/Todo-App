<?php

namespace App\Models;

// Interactive Database

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// OOP Inheritans 
class Todo extends Model
{
  use HasFactory,SoftDeletes;
  protected $fillable=['title','description','toggle-completed','user_id'];

  //protected $dates=['deleted_at'];
  public function user ()
  {
    //Relationship with User Model
    // بتطلعللي سهم في الداتابيس
    return $this->belongsTo(User::class,'user_id','id' );
  
  }
}
