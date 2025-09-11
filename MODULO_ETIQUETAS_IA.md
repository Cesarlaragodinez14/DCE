# 🚀 Módulo de Etiquetas IA - SAES (v2.0 OPTIMIZADO)

## 📋 Descripción General

El **Módulo de Etiquetas IA v2.0** es un sistema inteligente **OPTIMIZADO** que categoriza automáticamente los comentarios y observaciones de auditorías utilizando Inteligencia Artificial. 

### 🎯 **NUEVA VERSIÓN 2.0 - OPTIMIZACIÓN MASIVA**
- 💰 **87.3% REDUCCIÓN EN COSTOS DE IA** por agrupación de tipos de apartado
- ⚡ **De 165 a 21 llamadas de IA** en pruebas reales (ejemplo: 5 auditorías)
- 💵 **Ahorro monetario**: de $8.25 a $1.05 USD por lote procesado
- 🎯 **Calidad mantenida** en las etiquetas generadas
- 🔄 **Detección mejorada** de duplicados

### ✨ **Características Principales:**
- 🤖 **Generación automática optimizada** usando IA (Groq)
- 🏷️ **Categorización inteligente** por TIPOS de apartados (no individuales)
- 📊 **Sistema de confianza** mejorado para cada etiqueta generada
- 🔄 **Procesamiento automático** programado cada 24 horas con máxima eficiencia
- 👥 **Control de acceso** restringido a usuarios específicos (ID: 1, 2, 3)
- 📱 **Interfaz visual** integrada en el Resumen de Auditorías

---

## 🚀 Instalación

### 1. **Ejecutar Migraciones**
```bash
php artisan migrate
```

### 2. **Poblar Etiquetas Iniciales**
```bash
php artisan db:seed --class=EtiquetasSeeder
```

### 3. **Verificar Instalación**
```bash
php artisan tinker --execute="
echo '✅ Etiquetas disponibles: ' . App\Models\CatEtiqueta::count() . PHP_EOL;
echo '✅ Sistema listo para usar' . PHP_EOL;
"
```

---

## 💰 **OPTIMIZACIÓN DE COSTOS v2.0**

### 🎯 **Cómo Funciona la Optimización**

#### **📊 Método Anterior (v1.x):**
- Procesaba cada apartado individualmente
- 1 apartado = 1 llamada a IA
- **Ejemplo:** 33 apartados = 33 llamadas

#### **🚀 Método Optimizado (v2.0):**  
- Agrupa apartados por **TIPO** (nombre del apartado)
- 1 tipo de apartado = 1 llamada a IA para TODOS los apartados de ese tipo
- **Ejemplo:** 33 apartados de 7 tipos = 7 llamadas

### 📈 **Resultados Reales de la Optimización**
```
🔍 AUDITORÍA ASIAPO-DI-DSP-A-0004-2024:
   ➤ Apartados individuales: 20
   ➤ Tipos únicos: 3  
   ➤ Ahorro: 85.0% (20→3 llamadas)

🔍 AUDITORÍA ASIAPO-DI-PEO-A-0003-2024:  
   ➤ Apartados individuales: 65
   ➤ Tipos únicos: 7
   ➤ Ahorro: 89.2% (65→7 llamadas)

💰 TOTAL: 165 llamadas → 21 llamadas (87.3% menos)
💵 COSTO: $8.25 → $1.05 USD (reducción de $7.20)
```

### 🛠️ **Verificar Ahorro en tu Sistema**
```bash
# Ver estimación de ahorro para una auditoría específica
php artisan etiquetas:generar --auditoria-id=123 --mostrar-ahorro

# Ver estimación para las próximas 3 auditorías
php artisan etiquetas:generar --mostrar-ahorro
```

---

## ⚙️ Configuración

### 1. **Variables de Entorno**
Asegúrate de tener configurada la API de Groq en tu `.env`:
```env
GROQ_API_KEY=tu-clave-api-aqui
GROQ_DEF_MODEL=llama3-8b-8192
```

### 2. **Configurar Queue Worker (Producción)**
```bash
# Supervisor o similar
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

### 3. **Configurar Cron Job (Ejecución Automática)**
Agregar al crontab del servidor:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔄 **NUEVO: Procesamiento por Apartado Completo**

### **📋 Cambio Importante**
A partir de la versión 1.1, el sistema cambió de procesar **comentarios individuales** a **apartados completos**:

#### **✅ Antes (v1.0):**
- Procesaba cada comentario por separado
- Generaba múltiples etiquetas redundantes
- No consideraba el contexto histórico

#### **🚀 Ahora (v1.1):**
- **Analiza todo el apartado** como una unidad completa
- **Incluye historial** de cambios y comentarios históricos
- **Límite dinámico** de etiquetas = cantidad de comentarios
- **Marca "Procesado"** si no hay incidencias relevantes
- **Evita redundancia** con análisis contextual

### **📊 Beneficios del Nuevo Enfoque:**
- 🎯 **Más preciso**: Contexto completo del apartado
- 📉 **Menos redundancia**: Elimina etiquetas duplicadas
- 📈 **Mejor calidad**: Análisis más profundo e inteligente
- ⚡ **Más eficiente**: Menor uso de tokens de IA
- 🕒 **Incluye tiempo**: Marca cuando fue procesado

---

## 🎯 Uso del Sistema

### 📱 **Interfaz Web (Recomendado)**

#### **1. Acceder al Resumen de Auditorías**
- Navega a la sección **"Resumen de Auditorías"**
- Busca auditorías en la tabla de resultados

#### **2. Generar Etiquetas**
- **Sin etiquetas**: Haz clic en **"Generar etiquetas para este expediente"**
- **Con etiquetas**: Haz clic en **"Regenerar"** para actualizar

#### **3. Ver Detalles de Etiquetas**
- Haz clic en cualquier **etiqueta coloreada**
- Se abrirá un modal con:
  - Razón de asignación
  - Comentario fuente
  - Apartado relacionado
  - Confianza de IA (%)
  - Usuario que procesó

### 💻 **Línea de Comandos**

#### **Comandos Disponibles (v2.0 Optimizados)**
```bash
# Generar todas las auditorías pendientes (OPTIMIZADO)
php artisan etiquetas:generar

# Generar una auditoría específica con ahorro estimado
php artisan etiquetas:generar --auditoria-id=123 --mostrar-ahorro

# Generar con usuario específico
php artisan etiquetas:generar --usuario-id=1

# Ejecutar sincrónicamente con reporte de optimización
php artisan etiquetas:generar --sync --mostrar-ahorro

# Ver ahorro sin procesar (solo estimación)
php artisan etiquetas:generar --mostrar-ahorro --usuario-id=1

# Combinar todas las opciones
php artisan etiquetas:generar --auditoria-id=123 --usuario-id=1 --sync --mostrar-ahorro
```

#### **Gestión de Colas**
```bash
# Ver trabajos en cola
php artisan queue:work --once --verbose

# Limpiar cola (si hay errores)
php artisan queue:clear

# Procesar trabajos continuamente
php artisan queue:work
```

---

## 🏷️ Etiquetas Predefinidas

El sistema incluye **22 etiquetas iniciales** organizadas por categorías:

### **📄 Documentación**
- Documentación faltante
- Documentación incompleta  
- Documentación incorrecta

### **📅 Fechas y Plazos**
- Fecha vencida
- Plazo incumplido

### **💰 Montos y Cálculos**
- Error de cálculo
- Monto inconsistente
- Diferencia monetaria

### **⚖️ Cumplimiento Normativo**
- Incumplimiento normativo
- Proceso irregular
- Falta de autorización

### **📊 Registros Contables**
- Error contable
- Registro incompleto
- Clasificación incorrecta

### **🔍 Transparencia**
- Falta de transparencia
- Información inconsistente

### **🛡️ Controles Internos**
- Control interno deficiente
- Segregación de funciones

### **⚙️ Procesos**
- Procedimiento inadecuado
- Proceso pendiente

### **👥 Recursos Humanos**
- Personal no autorizado
- Responsabilidad unclear

---

## 🔧 Sistema Automático

### **⏰ Programación Automática**
- **Frecuencia**: Cada 24 horas
- **Hora**: 2:00 AM
- **Función**: Procesa todas las auditorías pendientes automáticamente

### **🎯 Criterios de Procesamiento**
El sistema identifica automáticamente auditorías que:
- Tienen comentarios en apartados (`observaciones` o `comentarios_uaa`)
- No han sido procesadas recientemente
- Tienen cambios desde el último procesamiento

---

## 🛠️ Troubleshooting

### **❌ Error: Rate Limit (429)**
```
Solución: El sistema tiene manejo automático de rate limits
- 3 reintentos automáticos
- Delays exponenciales (5s, 10s, 20s)
- Si persiste, esperar 1 hora y reintentar
```

### **❌ Error: No se generan etiquetas**
```bash
# 1. Verificar que hay comentarios
php artisan tinker --execute="
echo App\Models\Auditorias::whereHas('checklistApartados', function(\$q) { 
    \$q->whereNotNull('observaciones'); 
})->count() . ' auditorías con comentarios';
"

# 2. Verificar configuración de IA
php artisan tinker --execute="
echo 'API Key configurada: ' . (env('GROQ_API_KEY') ? 'SI' : 'NO');
"

# 3. Probar manualmente
php artisan etiquetas:generar --auditoria-id=1 --sync
```

### **❌ Error: Usuario sin permisos**
Solo usuarios con ID 1, 2, o 3 pueden generar etiquetas manualmente.

### **❌ Error: Jobs no se procesan**
```bash
# Verificar worker de colas
php artisan queue:work --once --verbose

# Si no hay worker activo
php artisan queue:work
```

---

## 📊 Monitoreo y Logs

### **📁 Ubicación de Logs**
- **General**: `storage/logs/laravel.log`
- **Etiquetas**: `storage/logs/etiquetas_generacion.log`

### **📈 Métricas del Sistema**
```bash
# Ver estadísticas
php artisan tinker --execute="
echo 'Total etiquetas en catálogo: ' . App\Models\CatEtiqueta::count() . PHP_EOL;
echo 'Total relaciones creadas: ' . App\Models\AuditoriaEtiqueta::count() . PHP_EOL;
echo 'Etiqueta más usada: ' . App\Models\CatEtiqueta::orderBy('veces_usada', 'desc')->first()->nombre . PHP_EOL;
"
```

### **🔍 Consultas Útiles**
```bash
# Auditorías con etiquetas
php artisan tinker --execute="
echo App\Models\Auditorias::has('auditoriaEtiquetas')->count() . ' auditorías etiquetadas';
"

# Etiquetas por confianza alta
php artisan tinker --execute="
echo App\Models\AuditoriaEtiqueta::where('confianza_ia', '>=', 0.8)->count() . ' etiquetas con alta confianza';
"
```

---

## 🏗️ Arquitectura Técnica

### **📊 Base de Datos**

#### **Tabla: `cat_etiquetas`**
```sql
- id (PK)
- nombre (varchar, unique)
- descripcion (text)
- color (varchar) - Para UI
- activo (boolean)
- veces_usada (integer) - Contador
- timestamps
```

#### **Tabla: `auditoria_etiquetas`**
```sql
- id (PK)
- auditoria_id (FK → aditorias)
- etiqueta_id (FK → cat_etiquetas)
- checklist_apartado_id (FK → checklist_apartados)
- razon_asignacion (text)
- comentario_fuente (text)
- confianza_ia (decimal 0.00-1.00)
- validado_manualmente (boolean)
- procesado_por (FK → users)
- procesado_en (timestamp)
- timestamps
```

### **🔄 Flujo de Procesamiento**

1. **Job Dispatcher** → `GenerarEtiquetasJob`
2. **Análisis** → Encuentra auditorías con comentarios
3. **Procesamiento IA** → Llama a Groq
4. **Parseo** → Procesa respuesta JSON
5. **Almacenamiento** → Crea/actualiza etiquetas
6. **UI Update** → Actualiza interfaz

### **🤖 Prompt de IA (ACTUALIZADO: Procesamiento por Apartado)**
```
Analiza este apartado de auditoría y asigna etiquetas relevantes.

APARTADO: {nombreApartado}
AUDITORÍA: {claveAccion}

HISTORIAL DE COMENTARIOS:
[2025-06-17 10:30] Observaciones actuales por Sistema:
{comentario_contenido}

INSTRUCCIONES:
- Analiza TODO el historial como conjunto
- Máximo {maxEtiquetas} etiquetas (una por incidencia detectada)
- Prioriza etiquetas existentes cuando apliquen
- Si NO requiere etiquetas, responde: "Procesado" con timestamp
- Cada etiqueta debe tener razón específica y confianza 0.6-1.0

Responde JSON:
{"etiquetas":[{"nombre":"etiqueta","razon":"motivo específico","confianza":0.8}]}
O para apartado sin etiquetas:
{"etiquetas":[{"nombre":"Procesado","razon":"Sin incidencias relevantes","confianza":1.0}]}
```

---

## 📝 Ejemplos de Uso

### **🆕 Ejemplo Real: Resultados del Nuevo Enfoque**
Con el procesamiento por apartado, una auditoría generó estas etiquetas inteligentes:

```
🏷️ Documentación incompleta 
   Apartado: Informe de Auditoría publicado
   Razón: El Informe no se anexó en su totalidad, terminando en una coma

🏷️ Proceso pendiente
   Apartado: Mecanismos de atención de la recomendación  
   Razón: No se anexaron los mecanismos de atención

🏷️ Documentación faltante
   Apartado: Anexos de recomendaciones
   Razón: No se anexa el apartado de mecanismos de atención

🏷️ Incumplimiento normativo
   Apartado: Mecanismos de atención  
   Razón: La recomendación no fue acordada
```

**Resultado:** 4 apartados procesados → 7 etiquetas relevantes (promedio 1.75 por apartado)

### **Ejemplo 1: Generación Manual**
```php
// Desde un controlador
use App\Jobs\GenerarEtiquetasJob;

// Generar etiquetas para auditoría específica
GenerarEtiquetasJob::dispatch($auditoriaId, Auth::id(), true);
```

### **Ejemplo 2: Consultar Etiquetas**
```php
// Obtener etiquetas de una auditoría
$auditoria = Auditorias::with('auditoriaEtiquetas.etiqueta')->find(1);

foreach ($auditoria->auditoriaEtiquetas as $rel) {
    echo $rel->etiqueta->nombre . ' - ' . $rel->razon_asignacion;
}
```

### **Ejemplo 3: Crear Etiqueta Personalizada**
```php
use App\Models\CatEtiqueta;

$etiqueta = CatEtiqueta::crearOObtener(
    'Nueva Etiqueta',
    'Descripción de la etiqueta',
    'blue'
);
```

---

## 🔒 Seguridad y Permisos

### **🛡️ Control de Acceso**
- Solo usuarios ID **1, 2, 3** pueden generar etiquetas manualmente
- Verificación en middleware de Livewire
- Logs de auditoría para todos los procesamientos

### **🔐 Validaciones**
- Verificación de existencia de auditorías
- Validación de formato JSON de IA
- Sanitización de inputs
- Rate limiting automático

---

## 🚀 Futuras Mejoras

### **🎯 Funcionalidades Planeadas**
- [ ] Filtrado por etiquetas en el resumen
- [ ] Dashboard de estadísticas de etiquetas
- [ ] Exportación de reportes por categorías
- [ ] Validación manual de etiquetas
- [ ] Machine learning para mejores categorías

### **⚡ Optimizaciones**
- [ ] Cache de etiquetas frecuentes
- [ ] Procesamiento en paralelo
- [ ] Integración con más proveedores de IA
- [ ] API REST para etiquetas

---

## 📞 Soporte

### **🐛 Reportar Errores**
1. Revisar logs en `storage/logs/`
2. Ejecutar comando de diagnóstico
3. Documentar pasos para reproducir
4. Incluir logs relevantes

### **💡 Mejoras Sugeridas**
- Crear issue con descripción detallada
- Incluir casos de uso específicos
- Proponer solución técnica si es posible

---

## 📚 Referencias Técnicas

- **Laravel Jobs**: https://laravel.com/docs/queues
- **Groq API**: https://console.groq.com/docs
- **Livewire Components**: https://laravel-livewire.com/
- **TailwindCSS**: https://tailwindcss.com/

---

*Documentación generada para SAES - Sistema de Auditoría Especial de la Federación*  
*Versión: 2.0 | Fecha: Diciembre 2024 | 🚀 OPTIMIZACIÓN MASIVA: 87.3% Reducción en Costos de IA* 