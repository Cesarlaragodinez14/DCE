<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistApartadoHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'checklist_apartado_id',
        'changed_by',
        'changes',
    ];

     /**
     * Relación con el usuario que realizó el cambio.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Relación con el checklist apartado.
     */
    public function checklistApartado()
    {
        return $this->belongsTo(ChecklistApartado::class, 'checklist_apartado_id');
    }
}
