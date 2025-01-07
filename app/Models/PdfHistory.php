<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'auditoria_id',
        'clave_de_accion',
        'pdf_path',
        'generated_by',
    ];

    /**
     * Relación con la auditoría.
     */
    public function auditoria()
    {
        return $this->belongsTo(Auditorias::class, 'auditoria_id');
    }

    /**
     * Relación con el usuario que generó el PDF.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
