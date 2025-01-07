<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfHash extends Model
{
    use HasFactory;

    protected $table = 'pdf_hashes';

    protected $fillable = [
        'auditoria_id',
        'hash',
        'email',
        'ip_address',
        'generated_at',
    ];

    public function auditoria()
    {
        return $this->belongsTo(Auditorias::class);
    }
}
