<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CatUaa;
use App\Models\CatSiglasAuditoriaEspecial;
use App\Models\Apartado;
use App\Models\Auditorias;
use App\Models\AuditoriasHistory;
use App\Models\ChecklistApartadoHistory;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;   // para obtener el user actual
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Normaliza el estatus del checklist a las 4 categorías originales
     *
     * @param string $estatus
     * @return string
     */
    private function normalizeChecklistStatus($estatus)
    {
        // Si el estatus es null o vacío, devolvemos "Sin Revisar"
        if (empty($estatus)) {
            return "Sin Revisar";
        }
        
        // Mapeamos los sinónimos y nuevas categorías a las 4 originales
        if (str_contains($estatus, 'Sin Revisar')) {
            return "Sin Revisar (No entregados + Entregados sin revisar)";
        } elseif ($estatus === "Aceptado") {
            return "Aceptado";
        } elseif ($estatus === "Devuelto") {
            return "Devuelto";
        } else {
            // Todos los demás son sinónimos de "En Proceso"
            return "En Proceso de Revisión del Checklist";
        }
    }
    
    /**
     * Aplica la normalización del estatus a una colección de resultados
     *
     * @param \Illuminate\Support\Collection $collection
     * @param string $estatusField
     * @return \Illuminate\Support\Collection
     */
    private function normalizeCollection($collection, $estatusField = 'estatus_checklist')
    {
        return $collection->map(function($item) use ($estatusField) {
            if (is_object($item) && property_exists($item, $estatusField)) {
                $item->$estatusField = $this->normalizeChecklistStatus($item->$estatusField);
            } elseif (is_array($item) && isset($item[$estatusField])) {
                $item[$estatusField] = $this->normalizeChecklistStatus($item[$estatusField]);
            }
            return $item;
        });
    }
    
    public function dashboardIndex(Request $request)
    {
        $validated = $request->validate([
            'entrega' => 'nullable|exists:cat_entrega,id',
            'cuenta_publica' => 'nullable|exists:cat_cuenta_publica,id',
            'uaa_id' => 'nullable|exists:cat_uaa,id',
            'dg_id' => 'nullable|exists:cat_dgseg_ef,id',
            'sae_id' => 'nullable|exists:cat_siglas_auditoria_especial,id',
        ]);

        // Si el usuario es Director General SEG, forzamos su dg_id en la URL
        if (Auth::user()->hasRole(['Director General SEG'])) {
            $dgReal = CatUaa::find(Auth::user()->uaa_id)->dgseg_ef_id;
            
            // Chequear si ya existe un dg_id en la petición o si es distinto
            // para evitar bucles de redirección
            if ($request->dg_id != $dgReal) {
                // Redirigir a la misma ruta con dg_id forzado
                // Mantenemos los demás parámetros que tenía la petición (entrega, cuenta_publica, etc.)
                return redirect()->route('dashboard.charts.index', array_merge($request->query(), [
                    'dg_id' => $dgReal
                ]));
            }
        } else if (Auth::user()->hasRole(['AECF', 'AED', 'AEGF'])) {
            if(!empty(Auth::user()->uaa_id)){
                return null;
            }

            $saeReal = CatSiglasAuditoriaEspecial::where('valor', auth()->user()->roles->pluck('name')->first())->first()->id;

            // Chequear si ya existe un dg_id en la petición o si es distinto
            // para evitar bucles de redirección
            if ($request->sae_id != $saeReal) {
                // Redirigir a la misma ruta con dg_id forzado
                // Mantenemos los demás parámetros que tenía la petición (entrega, cuenta_publica, etc.)
                return redirect()->route('dashboard.charts.index', array_merge($request->query(), [
                    'sae_id' => $saeReal
                ]));
            }
        } else if (Auth::user()->hasRole(['DGUAA'])) {
            if ($request->uaa_id != Auth::user()->uaa_id) {
                return redirect()->route('dashboard.charts.index', array_merge($request->query(), [
                    'uaa_id' => Auth::user()->uaa_id
                ]));
            }
        }
        
        // Obtener solo las UAA que tienen registros en aditorias
        $uaas = DB::table('cat_uaa')
            ->join('aditorias', 'cat_uaa.id', '=', 'aditorias.uaa')
            ->select('cat_uaa.id', 'cat_uaa.valor')
            ->distinct()
            ->get();

        // Obtener solo las DGSEG EF que tienen registros en aditorias
        $dgsegs = DB::table('cat_dgseg_ef')
            ->join('aditorias', 'cat_dgseg_ef.id', '=', 'aditorias.dgseg_ef')
            ->select('cat_dgseg_ef.id', 'cat_dgseg_ef.valor')
            ->distinct()
            ->get();

        // Obtener solo las Entregas que tienen registros en aditorias
        $entregas = DB::table('cat_entrega')
            ->join('aditorias', 'cat_entrega.id', '=', 'aditorias.entrega')
            ->select('cat_entrega.id', 'cat_entrega.valor')
            ->distinct()
            ->get();

        // Obtener solo las Cuentas Públicas que tienen registros en aditorias
        $cuentasPublicas = DB::table('cat_cuenta_publica')
            ->join('aditorias', 'cat_cuenta_publica.id', '=', 'aditorias.cuenta_publica')
            ->select('cat_cuenta_publica.id', 'cat_cuenta_publica.valor')
            ->distinct()
            ->get();

        // ======================= 1) Expedientes por estatus =======================
        // Obtenemos los datos originales y normalizamos
        $rawCountsByStatus = Auditorias::select(DB::raw('IFNULL(estatus_checklist, "Sin Revisar") as estatus_checklist'), DB::raw('COUNT(*) as total'))
            ->when($request->filled('uaa_id'), function($q) use ($request) {
                $q->where('uaa', $request->uaa_id);
            })
            ->when($request->filled('dg_id'), function($q) use ($request) {
                $q->where('dgseg_ef', $request->dg_id);
            })
            ->when($request->filled('sae_id'), function($q) use ($request) {
                $q->where('siglas_auditoria_especial', $request->sae_id);
            })
            ->when($request->filled('entrega'), function($q) use ($request) {
                $q->where('entrega', $request->entrega);
            })
            ->when($request->filled('cuenta_publica'), function($q) use ($request) {
                $q->where('cuenta_publica', $request->cuenta_publica);
            })
            ->groupBy('estatus_checklist')
            ->get();
            
        // Normalizamos y reagrupamos manteniendo el formato original
        $countsByStatus = collect();
        foreach ($rawCountsByStatus as $item) {
            $normalizedStatus = $this->normalizeChecklistStatus($item->estatus_checklist);
            
            // Buscamos si ya existe un registro con este estatus normalizado
            $existingIndex = $countsByStatus->search(function($existing) use ($normalizedStatus) {
                return $existing->estatus_checklist === $normalizedStatus;
            });
            
            if ($existingIndex !== false) {
                // Si existe, sumamos el total
                $countsByStatus[$existingIndex]->total += $item->total;
            } else {
                // Si no existe, añadimos un nuevo objeto con el estatus normalizado
                $countsByStatus->push((object)[
                    'estatus_checklist' => $normalizedStatus,
                    'total' => $item->total
                ]);
            }
        }
        
        // Ordenamos por total descendente como lo hacía el código original
        $countsByStatus = $countsByStatus->sortByDesc('total')->values();

        // ======================= 2) Expedientes por Ente Fiscalizado =======================
        $countsByEnteFiscalizado = Auditorias::with('catEnteFiscalizado')
            ->select('ente_fiscalizado', DB::raw('COUNT(*) as total'))
            ->when($request->filled('uaa_id'), function($q) use ($request) {
                $q->where('uaa', $request->uaa_id);
            })
            ->when($request->filled('dg_id'), function($q) use ($request) {
                $q->where('dgseg_ef', $request->dg_id);
            })
            ->when($request->filled('sae_id'), function($q) use ($request) {
                $q->where('siglas_auditoria_especial', $request->sae_id);
            })
            // NUEVO: Filtros por entrega y cuenta_publica
            ->when($request->filled('entrega'), function($q) use ($request) {
                $q->where('entrega', $request->entrega);
            })
            ->when($request->filled('cuenta_publica'), function($q) use ($request) {
                $q->where('cuenta_publica', $request->cuenta_publica);
            })
            ->groupBy('ente_fiscalizado')
            ->get();

        // ======================= 3) Expedientes por Siglas de Auditoría Especial =======================
        $rawCountsBySiglasAuditoriaEspecial = Auditorias::with('catSiglasAuditoriaEspecial')
            ->select(
                'siglas_auditoria_especial',
                'estatus_checklist',
                DB::raw('COUNT(*) as total')
            )
            ->when($request->filled('uaa_id'), function($q) use ($request) {
                $q->where('uaa', $request->uaa_id);
            })
            ->when($request->filled('dg_id'), function($q) use ($request) {
                $q->where('dgseg_ef', $request->dg_id);
            })
            ->when($request->filled('sae_id'), function($q) use ($request) {
                $q->where('siglas_auditoria_especial', $request->sae_id);
            })
            ->when($request->filled('entrega'), function($q) use ($request) {
                $q->where('entrega', $request->entrega);
            })
            ->when($request->filled('cuenta_publica'), function($q) use ($request) {
                $q->where('cuenta_publica', $request->cuenta_publica);
            })
            ->groupBy('siglas_auditoria_especial', 'estatus_checklist')
            ->get();

        // Fusionar (sumar) los estatus que se normalicen a igual valor, similar a lo hecho en el punto 1
        $countsBySiglasAuditoriaEspecialEstatus = collect();
        foreach ($rawCountsBySiglasAuditoriaEspecial as $item) {
            // Normalizamos el estatus usando tu función
            $normalizedStatus = $this->normalizeChecklistStatus($item->estatus_checklist);
            $sigla = $item->siglas_auditoria_especial;
            $key = $sigla . '|' . $normalizedStatus;
            
            // Buscar si ya existe este "key" en la colección
            $existingIndex = $countsBySiglasAuditoriaEspecialEstatus->search(function($existing) use ($key) {
                return $existing->key === $key;
            });
            
            if ($existingIndex !== false) {
                // Sumamos el total al que ya estaba
                $countsBySiglasAuditoriaEspecialEstatus[$existingIndex]->total += $item->total;
            } else {
                // Creamos un objeto nuevo con el key, sigla, estatus normalizado y total
                $obj = new \stdClass;
                $obj->key = $key;
                $obj->siglas_auditoria_especial = $sigla;
                $obj->estatus_checklist = $normalizedStatus;
                $obj->total = $item->total;
                // Opcional: Si necesitas mantener la relación, puedes asignar la propiedad del catálogo
                $obj->catSiglasAuditoriaEspecial = $item->catSiglasAuditoriaEspecial;
                $countsBySiglasAuditoriaEspecialEstatus->push($obj);
            }
        }

        // Ordenar descendente por total
        $countsBySiglasAuditoriaEspecial = $countsBySiglasAuditoriaEspecialEstatus->sortByDesc('total')->values();

        // ======================= 4) Expedientes por Siglas Tipo Acción (Apilado por Estatus) =======================
        // Nota: Corregido nombre de tabla de "aditorias" a "auditorias" en el JOIN y WHERE
        $rawCountsBySiglasTipoAccion = Auditorias::join('cat_siglas_tipo_accion as csta', 'aditorias.siglas_tipo_accion', '=', 'csta.id')
        ->select(
            'csta.valor as sigla_nombre',
            'aditorias.estatus_checklist',
            DB::raw('COUNT(*) as total')
        )
        ->when($request->filled('uaa_id'), function($q) use ($request) {
            $q->where('aditorias.uaa', $request->uaa_id);
        })
        ->when($request->filled('dg_id'), function($q) use ($request) {
            $q->where('aditorias.dgseg_ef', $request->dg_id);
        })
        ->when($request->filled('sae_id'), function($q) use ($request) {
            $q->where('aditorias.siglas_auditoria_especial', $request->sae_id);
        })
        // Filtros por entrega y cuenta_publica
        ->when($request->filled('entrega'), function($q) use ($request) {
            $q->where('aditorias.entrega', $request->entrega);
        })
        ->when($request->filled('cuenta_publica'), function($q) use ($request) {
            $q->where('aditorias.cuenta_publica', $request->cuenta_publica);
        })
        ->groupBy('csta.valor', 'aditorias.estatus_checklist')
        ->orderByDesc('total')
        ->get();

        // Normalizar estatus y agrupar
        $normalizedCountsBySiglasTipoAccion = $rawCountsBySiglasTipoAccion->map(function($item) {
        return [
            'sigla_nombre' => $item->sigla_nombre,
            'estatus_checklist' => $this->normalizeChecklistStatus($item->estatus_checklist),
            'total' => $item->total
        ];
        })->groupBy(function($item) {
        return $item['sigla_nombre'] . '|' . $item['estatus_checklist'];
        })->map(function($group) {
        $first = $group->first();
        return [
            'sigla_nombre' => $first['sigla_nombre'],
            'estatus_checklist' => $first['estatus_checklist'],
            'total' => $group->sum('total')
        ];
        })->values();

        // Estructurar datos para que coincidan con el frontend
        $siglasTipoAccionData = [];
        foreach ($normalizedCountsBySiglasTipoAccion as $row) {
        $sigla  = $row['sigla_nombre'];
        $estatus = $row['estatus_checklist'];
        $total  = $row['total'];

        if (!isset($siglasTipoAccionData[$sigla])) {
            $siglasTipoAccionData[$sigla] = [];
        }
        $siglasTipoAccionData[$sigla][$estatus] = $total;
        }

        // Verificar que los datos no estén vacíos
        if (empty($siglasTipoAccionData)) {
        $siglasTipoAccionData = new \stdClass(); // Objeto vacío para evitar errores de JSON
        }

        $countsBySiglasTipoAccion = $siglasTipoAccionData;

        // ======================= 5) Expedientes por DGSEG EF y Estatus =======================
        $rawCountsByDgsegEfEstatus = Auditorias::with('catDgsegEf')
            ->select('dgseg_ef', 'estatus_checklist', DB::raw('COUNT(*) as total'))
            ->when($request->filled('uaa_id'), function($q) use ($request) {
                $q->where('uaa', $request->uaa_id);
            })
            ->when($request->filled('dg_id'), function($q) use ($request) {
                $q->where('dgseg_ef', $request->dg_id);
            })
            ->when($request->filled('sae_id'), function($q) use ($request) {
                $q->where('siglas_auditoria_especial', $request->sae_id);
            })
            // NUEVO: Filtros por entrega y cuenta_publica
            ->when($request->filled('entrega'), function($q) use ($request) {
                $q->where('entrega', $request->entrega);
            })
            ->when($request->filled('cuenta_publica'), function($q) use ($request) {
                $q->where('cuenta_publica', $request->cuenta_publica);
            })
            ->groupBy('dgseg_ef', 'estatus_checklist')
            ->get();
            
        // Normalizar estatus y reagrupar
        $normalizedDgsegEfEstatus = $rawCountsByDgsegEfEstatus->map(function($item) {
            return [
                'dgseg_ef' => $item->dgseg_ef,
                'estatus_checklist' => $this->normalizeChecklistStatus($item->estatus_checklist),
                'total' => $item->total,
                'catDgsegEf' => $item->catDgsegEf
            ];
        })->groupBy(function($item) {
            return $item['dgseg_ef'] . '|' . $item['estatus_checklist'];
        })->map(function($group) {
            $first = $group->first();
            return [
                'dgseg_ef' => $first['dgseg_ef'],
                'estatus_checklist' => $first['estatus_checklist'],
                'total' => $group->sum('total'),
                'catDgsegEf' => $first['catDgsegEf']
            ];
        })->values();

        // Convertir la data para incluir el nombre de la DGSEG EF
        $countsByDgsegEf = collect($normalizedDgsegEfEstatus)->map(function($item) {
            return [
                'dgseg_ef_valor' => $item['catDgsegEf']->valor ?? 'Sin Datos',
                'estatus_checklist' => $item['estatus_checklist'],
                'total' => $item['total']
            ];
        })->toArray();


        // ======================= 6) Cambios en Expedientes (Últimos 30 días) =======================
        $last30Days = Carbon::now()->subDays(30);
        $auditoriasChangesByDay = AuditoriasHistory::join('aditorias', 'aditorias.id', '=', 'auditorias_histories.auditoria_id')
            ->select(
                DB::raw('DATE(auditorias_histories.created_at) as date'),
                DB::raw('COUNT(*) as total_changes')
            )
            ->where('auditorias_histories.created_at', '>=', $last30Days)
            // NUEVO: Filtros por entrega y cuenta_publica en la tabla "aditorias"
            ->when($request->filled('uaa_id'), function($q) use ($request) {
                $q->where('aditorias.uaa', $request->uaa_id);
            })
            ->when($request->filled('dg_id'), function($q) use ($request) {
                $q->where('aditorias.dgseg_ef', $request->dg_id);
            })
            ->when($request->filled('sae_id'), function($q) use ($request) {
                $q->where('aditorias.siglas_auditoria_especial', $request->sae_id);
            })
            ->when($request->filled('entrega'), function($q) use ($request) {
                $q->where('aditorias.entrega', $request->entrega);
            })
            ->when($request->filled('cuenta_publica'), function($q) use ($request) {
                $q->where('aditorias.cuenta_publica', $request->cuenta_publica);
            })
            ->groupBy(DB::raw('DATE(auditorias_histories.created_at)'))
            ->orderBy(DB::raw('DATE(auditorias_histories.created_at)'))
            ->get();


        $query = DB::table('aditorias')
            ->join('cat_dgseg_ef as dg', 'dg.id', '=', 'aditorias.dgseg_ef')
            ->select(
                // Suponemos que el campo 'responsable' en la tabla auditorias identifica al usuario.
                'aditorias.seguimiento_nombre as changed_by',
                'dg.valor as dgseg_ef_valor',
                'aditorias.id as auditoria_id'
            );
        if($request->filled('uaa_id')){
            $query->where('aditorias.uaa', $request->uaa_id);
        }
        if($request->filled('dg_id')){
            $query->where('aditorias.dgseg_ef', $request->dg_id);
        }
        if($request->filled('sae_id')){
            $query->where('aditorias.siglas_auditoria_especial', $request->dg_id);
        }
        if($request->filled('entrega')){
            $query->where('aditorias.entrega', $request->entrega);
        }
        if($request->filled('cuenta_publica')){
            $query->where('aditorias.cuenta_publica', $request->cuenta_publica);
        }
    
        // Obtenemos los registros agrupando por (responsable, dg)
        $rawData = $query->groupBy('aditorias.seguimiento_nombre','dg.valor','aditorias.id')
            ->get()
            // Agrupamos en PHP: para cada combinación de responsable y dgseg_ef
            ->groupBy(function ($item) {
                return $item->changed_by . '|' . $item->dgseg_ef_valor;
            })
            ->map(function ($group) {
                // Se cuenta 1 por cada auditoria (agrupado por aditorias.id)
                return [
                    'changed_by'     => $group->first()->changed_by,
                    'dgseg_ef_valor' => $group->first()->dgseg_ef_valor,
                    'count_exp'      => $group->count()
                ];
            })
            ->values();
    
        // Obtener el nombre del usuario (suponiendo que el campo 'changed_by' es el ID de usuario)
        $topUsersByDG = $rawData->map(function($item){
            $user = \App\Models\User::find($item['changed_by']);
            return [
                'dgseg_ef_valor' => $item['dgseg_ef_valor'],
                'user_name'      => $user ? $user->name : "{$item['changed_by']}",
                'total_changes'  => $item['count_exp']
            ];
        })->sortByDesc('total_changes')->values();


        // ======================= 8) Apartados más modificados (No duplicar) =======================
        // Se une con "aditorias" como 'auds', se aplican mismos filtros
        $apartadosDataRaw = DB::table('checklist_apartado_histories as cah')
            ->join('checklist_apartados as ca', 'ca.id', '=', 'cah.checklist_apartado_id')
            ->join('apartados as a', 'a.id', '=', 'ca.apartado_id')
            ->join('aditorias as auds', 'auds.id', '=', 'ca.auditoria_id') // Relación con "expediente"
            ->select(
                'a.id as apartado_id',
                'a.nombre as apartado_nombre',
                // 1) Contar auditoria_id ÚNICOS
                DB::raw('COUNT(DISTINCT auds.id) as total_changes'),
                // 2) Sumar cuántas veces se cambió "observaciones"
                DB::raw('SUM(
                    CASE WHEN JSON_UNQUOTE(JSON_EXTRACT(cah.changes, "$.after.observaciones")) IS NOT NULL
                    THEN 1 ELSE 0 END
                ) as total_obs_changes')
            )
            ->whereRaw("
                JSON_UNQUOTE(JSON_EXTRACT(cah.changes, '$.after.se_integran')) IS NOT NULL
                OR JSON_UNQUOTE(JSON_EXTRACT(cah.changes, '$.after.observaciones')) IS NOT NULL
            ")
            // Filtros
            ->when($request->filled('uaa_id'), function($q) use ($request) {
                $q->where('auds.uaa', $request->uaa_id);
            })
            ->when($request->filled('dg_id'), function($q) use ($request) {
                $q->where('auds.dgseg_ef', $request->dg_id);
            })
            ->when($request->filled('sae_id'), function($q) use ($request) {
                $q->where('auds.siglas_auditoria_especial', $request->sae_id);
            })
            ->when($request->filled('entrega'), function($q) use ($request) {
                $q->where('auds.entrega', $request->entrega);
            })
            ->when($request->filled('cuenta_publica'), function($q) use ($request) {
                $q->where('auds.cuenta_publica', $request->cuenta_publica);
            })
            ->groupBy('a.id','a.nombre')
            ->orderByDesc('total_obs_changes')
            ->get();

        $apartadosData = $apartadosDataRaw->mapWithKeys(function($item) {
            return [
                $item->apartado_id => [
                    'nombre'         => $item->apartado_nombre,
                    'observaciones'  => $item->total_obs_changes,
                ]
            ];
        });

        // ======================= (Otros) =======================
        // 9) countsByUaaAndStatus 
        $rawCountsByUaaAndStatus = Auditorias::with('catUaa')
            ->select('uaa', 'estatus_checklist', DB::raw('count(*) as total'))
            // Filtros
            ->when($request->filled('uaa_id'), function($q) use ($request) {
                $q->where('uaa', $request->uaa_id);
            })
            ->when($request->filled('dg_id'), function($q) use ($request) {
                $q->where('dgseg_ef', $request->dg_id);
            })
            ->when($request->filled('sae_id'), function($q) use ($request) {
                $q->where('aditorias.siglas_auditoria_especial', $request->sae_id);
            })
            ->when($request->filled('entrega'), function($q) use ($request) {
                $q->where('entrega', $request->entrega);
            })
            ->when($request->filled('cuenta_publica'), function($q) use ($request) {
                $q->where('cuenta_publica', $request->cuenta_publica);
            })
            ->groupBy('uaa', 'estatus_checklist')
            ->orderBy('total','desc')
            ->get();
            
        // Normalizar el estatus y reagrupar
        $countsByUaaAndStatus = $rawCountsByUaaAndStatus->map(function($item) {
            return [
                'uaa' => $item->uaa,
                'estatus_checklist' => $this->normalizeChecklistStatus($item->estatus_checklist),
                'total' => $item->total,
                'catUaa' => $item->catUaa
            ];
        })->groupBy(function($item) {
            return $item['uaa'] . '|' . $item['estatus_checklist'];
        })->map(function($group) {
            $first = $group->first();
            return (object)[
                'uaa' => $first['uaa'],
                'estatus_checklist' => $first['estatus_checklist'],
                'total' => $group->sum('total'),
                'catUaa' => $first['catUaa']
            ];
        })->values();

        // 10) countsByUaaStatus
        $rawCountsByUaaStatus = Auditorias::with('catUaa')
            ->select('uaa', 'estatus_checklist', DB::raw('COUNT(*) as total'))
            // Filtros
            ->when($request->filled('uaa_id'), function($q) use ($request) {
                $q->where('uaa', $request->uaa_id);
            })
            ->when($request->filled('dg_id'), function($q) use ($request) {
                $q->where('dgseg_ef', $request->dg_id);
            })
            ->when($request->filled('sae_id'), function($q) use ($request) {
                $q->where('aditorias.siglas_auditoria_especial', $request->sae_id);
            })
            ->when($request->filled('entrega'), function($q) use ($request) {
                $q->where('entrega', $request->entrega);
            })
            ->when($request->filled('cuenta_publica'), function($q) use ($request) {
                $q->where('cuenta_publica', $request->cuenta_publica);
            })
            ->groupBy('uaa', 'estatus_checklist')
            ->get();
            
        // Normalizar el estatus y reagrupar
        $countsByUaaStatus = $rawCountsByUaaStatus->map(function($item) {
            return [
                'uaa' => $item->uaa,
                'estatus_checklist' => $this->normalizeChecklistStatus($item->estatus_checklist),
                'total' => $item->total,
                'catUaa' => $item->catUaa
            ];
        })->groupBy(function($item) {
            return $item['uaa'] . '|' . $item['estatus_checklist'];
        })->map(function($group) {
            $first = $group->first();
            return (object)[
                'uaa' => $first['uaa'],
                'estatus_checklist' => $first['estatus_checklist'],
                'total' => $group->sum('total'),
                'catUaa' => $first['catUaa']
            ];
        })->values();

        // 11) countsByAeUaaStatus
        // Unimos con cat_siglas_auditoria_especial y cat_uaa
        $rawCountsByAeUaaStatus = Auditorias::join('cat_siglas_auditoria_especial as ae', 'ae.id', '=', 'aditorias.siglas_auditoria_especial')
            ->join('cat_uaa as cu', 'cu.id', '=', 'aditorias.uaa')
            ->select(
                'ae.valor as ae_valor',
                'cu.nombre as uaa_valor',
                'aditorias.estatus_checklist',
                DB::raw('COUNT(aditorias.id) as total')
            )
            // Filtros
            ->when($request->filled('uaa_id'), function($q) use ($request) {
                $q->where('aditorias.uaa', $request->uaa_id);
            })
            ->when($request->filled('dg_id'), function($q) use ($request) {
                $q->where('aditorias.dgseg_ef', $request->dg_id);
            })
            ->when($request->filled('sae_id'), function($q) use ($request) {
                $q->where('aditorias.siglas_auditoria_especial', $request->sae_id);
            })
            ->when($request->filled('entrega'), function($q) use ($request) {
                $q->where('aditorias.entrega', $request->entrega);
            })
            ->when($request->filled('cuenta_publica'), function($q) use ($request) {
                $q->where('aditorias.cuenta_publica', $request->cuenta_publica);
            })
            ->groupBy('ae.valor', 'cu.nombre', 'aditorias.estatus_checklist')
            ->orderBy('ae.valor')
            ->get();
            
        // Normalizar el estatus y reagrupar
        $normalizedAeUaaStatus = collect($rawCountsByAeUaaStatus)->map(function($item) {
            return [
                'ae_valor' => $item->ae_valor,
                'uaa_valor' => $item->uaa_valor,
                'estatus_checklist' => $this->normalizeChecklistStatus($item->estatus_checklist),
                'total' => $item->total
            ];
        })->groupBy(function($item) {
            return $item['ae_valor'] . '|' . $item['uaa_valor'] . '|' . $item['estatus_checklist'];
        })->map(function($group) {
            $first = $group->first();
            return (object)[
                'ae_valor' => $first['ae_valor'],
                'uaa_valor' => $first['uaa_valor'],
                'estatus_checklist' => $first['estatus_checklist'],
                'total' => $group->sum('total')
            ];
        })->values();

        // Estructurar en array para AE
        $aeChartsData = $normalizedAeUaaStatus->groupBy('ae_valor')->toArray();

        // Agrupar en un solo array final
        $dashboardData = [
            'countsByStatus'                => $countsByStatus,
            'countsByDgsegEf'               => $countsByDgsegEf,
            'countsBySiglasTipoAccion'      => $countsBySiglasTipoAccion,
            'countsBySiglasAuditoriaEspecialEstatus' => $countsBySiglasAuditoriaEspecial,
            'aeChartsData'                  => $aeChartsData,
            'countsByUaaStatus'             => $countsByUaaStatus,
            'countsByEnteFiscalizado'       => $countsByEnteFiscalizado,
            'apartadosData'                 => $apartadosData,
            'dgUsersComparative'            => $topUsersByDG,
            'auditoriasChangesByDay'        => $auditoriasChangesByDay,
            'countsByUaaAndStatus'          => $countsByUaaAndStatus,
        ];

        return view('admin.stats.index', compact('dashboardData', 'uaas', 'dgsegs', 'entregas', 'cuentasPublicas'));
    }
}