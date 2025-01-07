<?php

// app/Models/AuditoriasHistory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AuditoriasHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'auditoria_id',
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
     * Relación con la auditoría.
     */
    public function auditoria()
    {
        return $this->belongsTo(Auditorias::class, 'auditoria_id');
    }
}
