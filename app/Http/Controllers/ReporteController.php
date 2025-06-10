<?php

namespace App\Http\Controllers;

use App\Exports\ReporteAuditoriasExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Apartado;
use App\Models\Auditorias;
use App\Models\ChecklistApartado;
use App\Models\CatUaa;
use App\Models\CatSiglasAuditoriaEspecial;
use Illuminate\Support\Facades\DB;
use App\Models\CatEntrega;

class ReporteController extends Controller
{
    public function exportarReporte()
    {
        return Excel::download(new ReporteAuditoriasExport, 'reporte_auditorias.xlsx');
    }

    public function observacionesApartados(Request $request, $entregaId = null)
    {
        // Obtener entregas del catálogo
        $entregas = CatEntrega::orderBy('id')->get();
        
        // Si no se proporciona ID de entrega, usar la del request o la primera entrega disponible
        $entregaId = $entregaId ?? $request->input('entrega_id');
        
        // Si aún no hay entregaId y hay entregas disponibles, tomar la primera
        if (!$entregaId && $entregas->isNotEmpty()) {
            $entregaId = $entregas->first()->id;
        } else if (!$entregaId) {
            // Si no hay entregas, usar 18 como valor por defecto
            $entregaId = 18;
        }

        // Primero obtenemos los IDs de auditorías para la entrega especificada
        // para hacer más eficiente la consulta posterior
        $auditoriaIds = Auditorias::where('entrega', $entregaId)->pluck('id');

        if ($auditoriaIds->isEmpty()) {
            return view('reportes.observaciones-apartados', [
                'resultadosFormateados' => collect([]),
                'entregaId' => $entregaId,
                'entregas' => $entregas
            ]);
        }

        // Obtenemos las observaciones únicas de manera más eficiente
        $observacionesUnicas = DB::table('checklist_apartados')
            ->selectRaw('observaciones, MIN(id) as min_id')
            ->whereIn('auditoria_id', $auditoriaIds)
            ->whereNotNull('observaciones')
            ->groupBy('observaciones')
            ->pluck('min_id');

        // Consultamos la información principal con eager loading para reducir consultas
        $resultados = ChecklistApartado::with([
                'apartado' => function($query) {
                    $query->select('id', 'nombre', 'parent_id', 'nivel');
                    // Cargar también los padres de cada apartado para construir la jerarquía completa
                    $query->with(['parent' => function($q) {
                        $q->select('id', 'nombre', 'parent_id', 'nivel')
                          ->with(['parent' => function($q2) {
                              $q2->select('id', 'nombre', 'parent_id', 'nivel');
                          }]);
                    }]);
                },
                'auditoria' => function($query) {
                    $query->select('id', 'clave_de_accion', 'uaa', 'siglas_auditoria_especial');
                },
                'auditoria.catUaa:id,valor',
                'auditoria.catSiglasAuditoriaEspecial:id,valor'
            ])
            ->whereIn('id', $observacionesUnicas)
            ->whereIn('auditoria_id', $auditoriaIds)
            ->get();
        
        // Procesamos los resultados para construir la estructura jerárquica
        $resultadosFormateados = collect($resultados)->map(function($item) {
            // Construimos el nombre completo del apartado según su jerarquía
            $nombreFormateado = $this->construirNombreJerarquico($item->apartado);
            
            return [
                'id_checklist' => $item->id,
                'apartado_id' => $item->apartado->id,
                'nombre_apartado' => $nombreFormateado,
                'nombre_original' => $item->apartado->nombre,
                'auditoria_clave' => $item->auditoria->clave_de_accion,
                'uaa' => $item->auditoria->catUaa->valor ?? '',
                'siglas_aud_esp' => $item->auditoria->catSiglasAuditoriaEspecial->valor ?? '',
                'nivel' => $item->apartado->nivel,
                'parent_id' => $item->apartado->parent_id,
            ];
        });
        
        // Primero obtenemos todos los apartados de nivel 1
        $apartadosNivel1 = Apartado::whereNull('parent_id')
                                  ->where('nivel', 1)
                                  ->orderBy('id')
                                  ->get(['id', 'nombre', 'parent_id', 'nivel']);
        
        // Generamos un array de IDs en el orden correcto para la jerarquía
        $ordenApartados = [];
        $numeracionApartados = [];
        $jerarquiaApartados = []; // Para mantener relación entre padres e hijos
        $contadorNivel1 = 0;
        
        // Primero procesamos todos los apartados de nivel 1
        foreach($apartadosNivel1 as $apartadoNivel1) {
            // Asignar numeración
            if ($contadorNivel1 < 2) {
                $numeracionApartados[$apartadoNivel1->id] = '0';
            } else {
                $numeracionApartados[$apartadoNivel1->id] = (string)($contadorNivel1 - 1);
            }
            $contadorNivel1++;
            
            // Añadir al orden
            $ordenApartados[] = $apartadoNivel1->id;
            
            // Inicializar array para mantener relación jerárquica
            $jerarquiaApartados[$apartadoNivel1->id] = [
                'hijos' => [] // Apartados nivel 2 hijos de este
            ];
            
            // Buscar subapartados (nivel 2)
            $apartadosNivel2 = Apartado::where('parent_id', $apartadoNivel1->id)
                                        ->where('nivel', 2)
                                        ->orderBy('id')
                                        ->get(['id', 'nombre', 'parent_id', 'nivel']);
            
            // Contador para subapartados
            $contadorNivel2 = 1;
            
            foreach($apartadosNivel2 as $apartadoNivel2) {
                // Asignar numeración para nivel 2
                $parentNumero = $numeracionApartados[$apartadoNivel1->id];
                $numeracionApartados[$apartadoNivel2->id] = $parentNumero . '.' . $contadorNivel2;
                $contadorNivel2++;
                
                // Añadir al orden
                $ordenApartados[] = $apartadoNivel2->id;
                
                // Agregar a la jerarquía
                $jerarquiaApartados[$apartadoNivel1->id]['hijos'][] = $apartadoNivel2->id;
                $jerarquiaApartados[$apartadoNivel2->id] = [
                    'hijos' => [], // Apartados nivel 3 hijos de este
                    'padre' => $apartadoNivel1->id
                ];
                
                // Buscar sub-subapartados (nivel 3)
                $apartadosNivel3 = Apartado::where('parent_id', $apartadoNivel2->id)
                                           ->where('nivel', 3)
                                           ->orderBy('id')
                                           ->get(['id', 'nombre', 'parent_id', 'nivel']);
                
                // Contador para sub-subapartados
                $contadorNivel3 = 1;
                
                foreach($apartadosNivel3 as $apartadoNivel3) {
                    // Asignar numeración para nivel 3
                    $parentNumero = $numeracionApartados[$apartadoNivel2->id];
                    $numeracionApartados[$apartadoNivel3->id] = $parentNumero . '.' . $contadorNivel3;
                    $contadorNivel3++;
                    
                    // Añadir al orden
                    $ordenApartados[] = $apartadoNivel3->id;
                    
                    // Agregar a la jerarquía
                    $jerarquiaApartados[$apartadoNivel2->id]['hijos'][] = $apartadoNivel3->id;
                    $jerarquiaApartados[$apartadoNivel3->id] = [
                        'hijos' => [],
                        'padre' => $apartadoNivel2->id
                    ];
                }
            }
        }
        
        // Ahora filtramos y ordenamos los resultados formateados según el orden correcto
        $resultadosFinales = collect();
        
        // Solo incluimos en resultados finales los apartados que tienen observaciones
        $apartadosConObservaciones = $resultadosFormateados->pluck('apartado_id')->unique()->toArray();
        
        // Contadores para estadísticas de apartados
        $conteoTotalPorApartado = [];
        $distribucionPorApartado = [];
        
        // Inicializar contadores en cero para todos los apartados
        foreach($ordenApartados as $apartadoId) {
            $conteoTotalPorApartado[$apartadoId] = [
                'claves_accion' => [],
                'total' => 0
            ];
        }
        
        // Calcular conteos para apartados con observaciones
        foreach($resultadosFormateados as $item) {
            $apartadoId = $item['apartado_id'];
            $claveAccion = $item['auditoria_clave'];
            
            // Agregar la clave de acción si no existe ya
            if (!in_array($claveAccion, $conteoTotalPorApartado[$apartadoId]['claves_accion'])) {
                $conteoTotalPorApartado[$apartadoId]['claves_accion'][] = $claveAccion;
                $conteoTotalPorApartado[$apartadoId]['total']++;
                
                // Propagar conteo hacia arriba en la jerarquía
                $this->propagarConteoHaciaArriba($apartadoId, $claveAccion, $conteoTotalPorApartado, $jerarquiaApartados);
            }
        }
        
        // Calcular la distribución por apartado (porcentaje del total)
        $totalObservaciones = array_sum(array_map(function($item) {
            return count($item['claves_accion']);
        }, $conteoTotalPorApartado));
        
        foreach($conteoTotalPorApartado as $apartadoId => $conteo) {
            $distribucionPorApartado[$apartadoId] = $totalObservaciones > 0 
                ? round(($conteo['total'] / $totalObservaciones) * 100, 2) 
                : 0;
        }
        
        // Ordenar apartados de nivel 1 por número de observaciones (de mayor a menor)
        $apartadosNivel1Ordenados = collect($apartadosNivel1)->sortByDesc(function($apartado) use ($conteoTotalPorApartado) {
            return $conteoTotalPorApartado[$apartado->id]['total'] ?? 0;
        })->values();
        
        // Recorrer todos los apartados de nivel 1 ordenados por total de observaciones
        foreach($apartadosNivel1Ordenados as $apartadoNivel1) {
            $apartadoId = $apartadoNivel1->id;
            $tieneObservaciones = in_array($apartadoId, $apartadosConObservaciones);
            
            // Si tiene observaciones, agregar sus resultados
            if ($tieneObservaciones) {
                $items = $resultadosFormateados->where('apartado_id', $apartadoId);
                foreach($items as $item) {
                    // Añadir numeración y estadísticas
                    $item['numeracion'] = $numeracionApartados[$apartadoId] ?? '0';
                    $item['total_apartado'] = $conteoTotalPorApartado[$apartadoId]['total'];
                    $item['distribucion_apartado'] = $distribucionPorApartado[$apartadoId];
                    $resultadosFinales->push($item);
                }
            } else {
                // Si no tiene observaciones pero es nivel 1, crear un registro vacío para mostrarlo
                $resultadosFinales->push([
                    'id_checklist' => null,
                    'apartado_id' => $apartadoId,
                    'nombre_apartado' => $apartadoNivel1->nombre,
                    'nombre_original' => $apartadoNivel1->nombre,
                    'auditoria_clave' => null,
                    'uaa' => '',
                    'siglas_aud_esp' => '',
                    'nivel' => 1,
                    'parent_id' => null,
                    'numeracion' => $numeracionApartados[$apartadoId] ?? '0',
                    'total_apartado' => $conteoTotalPorApartado[$apartadoId]['total'],
                    'distribucion_apartado' => $distribucionPorApartado[$apartadoId],
                    'sin_observaciones' => true
                ]);
            }
            
            // Continuamos con la lógica para nivel 2 y 3
            foreach($ordenApartados as $otroApartadoId) {
                // Saltamos apartados de nivel 1 (ya procesados) y los que no son descendientes del actual
                if ($otroApartadoId == $apartadoId || !isset($jerarquiaApartados[$otroApartadoId]['padre'])) {
                    continue;
                }
                
                // Verificar si es descendiente del apartado nivel 1 actual
                $esDescendiente = false;
                $padreActual = $otroApartadoId;
                while (isset($jerarquiaApartados[$padreActual]['padre'])) {
                    $padreActual = $jerarquiaApartados[$padreActual]['padre'];
                    if ($padreActual == $apartadoId) {
                        $esDescendiente = true;
                        break;
                    }
                }
                
                if ($esDescendiente && in_array($otroApartadoId, $apartadosConObservaciones)) {
                    $items = $resultadosFormateados->where('apartado_id', $otroApartadoId);
                    foreach($items as $item) {
                        // Añadir numeración y estadísticas
                        $item['numeracion'] = $numeracionApartados[$otroApartadoId] ?? '0';
                        $item['total_apartado'] = null; // Solo mostramos total en nivel 1
                        $item['distribucion_apartado'] = null; // Solo mostramos distribución en nivel 1
                        $resultadosFinales->push($item);
                    }
                }
            }
        }
        
        // Retornamos la vista con los resultados
        return view('reportes.observaciones-apartados', [
            'resultadosFormateados' => $resultadosFinales, 
            'entregaId' => $entregaId, 
            'entregas' => $entregas,
            'numeracionApartados' => $numeracionApartados,
            'conteoTotalPorApartado' => $conteoTotalPorApartado,
            'distribucionPorApartado' => $distribucionPorApartado,
            'totalObservaciones' => $totalObservaciones
        ]);
    }
    
    /**
     * Construye el nombre jerárquico de un apartado basado en su nivel y padres
     */
    private function construirNombreJerarquico($apartado)
    {
        if (!$apartado) {
            return '';
        }
        
        $nombre = $apartado->nombre;
        
        // Si es un subapartado (nivel 2 o 3), agregar formato apropiado
        if ($apartado->nivel == 1) {
            return $nombre;
        } elseif ($apartado->nivel == 2) {
            $padre = $apartado->parent;
            if ($padre) {
                return "{$padre->nombre} > {$nombre}";
            }
        } elseif ($apartado->nivel == 3) {
            $padre = $apartado->parent;
            if ($padre && $padre->parent) {
                return "{$padre->parent->nombre} > {$padre->nombre} > {$nombre}";
            }
        }
        
        return $nombre;
    }

    /**
     * Propaga el conteo de claves de acción hacia arriba en la jerarquía
     */
    private function propagarConteoHaciaArriba($apartadoId, $claveAccion, &$conteoTotalPorApartado, $jerarquiaApartados)
    {
        // Verificar si tiene padre
        if (isset($jerarquiaApartados[$apartadoId]['padre'])) {
            $padreId = $jerarquiaApartados[$apartadoId]['padre'];
            
            // Si la clave de acción no está ya en el conteo del padre, agregarla
            if (!in_array($claveAccion, $conteoTotalPorApartado[$padreId]['claves_accion'])) {
                $conteoTotalPorApartado[$padreId]['claves_accion'][] = $claveAccion;
                $conteoTotalPorApartado[$padreId]['total']++;
                
                // Continuar la propagación hacia arriba
                $this->propagarConteoHaciaArriba($padreId, $claveAccion, $conteoTotalPorApartado, $jerarquiaApartados);
            }
        }
    }
}
