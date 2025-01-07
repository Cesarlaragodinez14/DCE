<!-- resources/views/pdf/hash_info.blade.php -->
@php
    use Carbon\Carbon;

    /**
     * Función para formatear la fecha intentando primero con parse() y luego con createFromFormat()
     *
     * @param string $dateString
     * @return string
     */
    function formatGeneratedAt($dateString) {
        try {
            // Primer intento con Carbon::parse()
            $parsedDate = Carbon::parse($dateString);
            return $parsedDate->format('d/m/Y H:i:s');
        } catch (\Exception $e) {
            // Si falla, intentar con Carbon::createFromFormat()
            try {
                $parsedDate = Carbon::createFromFormat('d/m/Y H:i:s', $dateString);
                return $parsedDate->format('d/m/Y H:i:s');
            } catch (\Exception $e) {
                // Si ambos intentos fallan, devolver un texto por defecto
                return 'Fecha inválida';
            }
        }
    }

    // Utilizar la función para formatear la fecha
    $formattedDate = formatGeneratedAt($generatedAt);
@endphp
<!-- resources/views/pdf/hash_info_uaa.blade.php -->
<div style="margin-top: 20px;">
    <table style="width:100%; border-collapse: collapse;">
        <tr>
            <!-- Columna del Código QR -->
            <td style="width:50%; text-align: center; vertical-align: top;">
                <img src="{{ $qrCodeDataUriUAA }}" alt="Código QR de Conformidad de la UAA">
            </td>
            <!-- Columna de la Información del Hash -->
            <td style="width:50%; vertical-align: top;">
                <h3>Información de Conformidad de la UAA</h3>
                <p><strong>Hash de Seguimiento Anterior:</strong> {{ $hashSeguimiento }}</p>
                <p><strong>Nuevo Hash de Conformidad de la UAA:</strong> {{ $hashUAA }}</p>
                <p><strong>Generado por:</strong> {{ $currentUserName }} ({{ $currentUserRole }})</p>
                <p><strong>Correo del Generador:</strong> {{ Auth::user()->email }}</p>
                <p><strong>Dirección IP:</strong> {{ $ipAddress }}</p>
                <p><strong>Fecha y Hora de Generación:</strong> {{ $formattedDate }}</p>
            </td>
        </tr>
    </table>
</div>
