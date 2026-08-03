<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentFolder extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'employee_id',
        'name',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'folder_id');
    }
}
