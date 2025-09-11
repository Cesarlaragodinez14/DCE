## Diccionario de datos (SAES)

Este documento describe el esquema de base de datos actual según las migraciones en `database/migrations`.

Nota: La tabla principal de auditorías se llama `aditorias` (con d), de acuerdo con las migraciones y modelos.

---

### Tabla: aditorias

Claves y restricciones:
- PK: `id`
- UK: `clave_de_accion`
- FK: `cuenta_publica -> cat_cuenta_publica(id)`, `entrega -> cat_entrega(id)`, `auditoria_especial -> cat_auditoria_especial(id)`, `tipo_de_auditoria -> cat_tipo_de_auditoria(id)`, `siglas_auditoria_especial -> cat_siglas_auditoria_especial(id)`, `uaa -> cat_uaa(id)`, `ente_fiscalizado -> cat_ente_fiscalizado(id)`, `ente_de_la_accion -> cat_ente_de_la_accion(id)`, `clave_accion -> cat_clave_accion(id)`, `siglas_tipo_accion -> cat_siglas_tipo_accion(id)`, `dgseg_ef -> cat_dgseg_ef(id)`

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| id | bigIncrements | no | - | PK |
| clave_de_accion | string | no | - | unique |
| cuenta_publica | bigInteger unsigned | no | - | FK |
| entrega | bigInteger unsigned | no | - | FK |
| auditoria_especial | bigInteger unsigned | no | - | FK |
| tipo_de_auditoria | bigInteger unsigned | no | - | FK |
| siglas_auditoria_especial | bigInteger unsigned | no | - | FK |
| uaa | bigInteger unsigned | no | - | FK |
| titulo | string | no | - | |
| ente_fiscalizado | bigInteger unsigned | no | - | FK |
| numero_de_auditoria | bigInteger unsigned | no | - | |
| ente_de_la_accion | bigInteger unsigned | no | - | FK |
| clave_accion | bigInteger unsigned | no | - | FK |
| siglas_tipo_accion | bigInteger unsigned | no | - | FK |
| dgseg_ef | bigInteger unsigned | no | - | FK |
| nombre_director_general | string | no | - | |
| direccion_de_area | string | no | - | |
| nombre_director_de_area | string | no | - | |
| sub_direccion_de_area | string | no | - | |
| nombre_sub_director_de_area | string | no | - | |
| jd | string | no | - | |
| jefe_de_departamento | string | no | - | |
| estatus_checklist | string | no | - | |
| estatus_entrega | string | no | - | |
| auditor_nombre | string | no | - | |
| auditor_puesto | string | no | - | |
| seguimiento_nombre | string | no | - | |
| seguimiento_puesto | string | no | - | |
| comentarios | text | no | - | |
| created_at | timestamp | sí | null | |
| updated_at | timestamp | sí | null | |

---

### Tabla: apartados

Claves y restricciones:
- PK: `id`
- FK: `parent_id -> apartados(id)` ON DELETE CASCADE

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| id | bigIncrements | no | - | PK |
| nombre | string | no | - | |
| descripcion | text | sí | null | |
| parent_id | unsignedBigInteger | sí | null | FK |
| nivel | integer | no | 1 | |
| created_at | timestamp | sí | null | |
| updated_at | timestamp | sí | null | |

---

### Tabla: checklist_apartados

Claves y restricciones:
- PK: `id`
- FK: `apartado_id -> apartados(id)`, `auditoria_id -> aditorias(id)`

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| id | bigIncrements | no | - | PK |
| apartado_id | unsignedBigInteger | no | - | FK |
| auditoria_id | unsignedBigInteger | no | - | FK |
| se_aplica | boolean | sí | null | |
| es_obligatorio | boolean | no | false | |
| se_integra | boolean | sí | null | |
| observaciones | text | sí | null | |
| comentarios_uaa | text | sí | null | |
| created_at | timestamp | sí | null | |
| updated_at | timestamp | sí | null | |

---

### Tabla: apartado_plantillas

Claves y restricciones:
- PK: `id`
- FK: `apartado_id -> apartados(id)`

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| id | bigIncrements | no | - | PK |
| apartado_id | foreignId | no | - | FK |
| plantilla | string | no | - | |
| es_aplicable | boolean | no | false | |
| es_obligatorio | boolean | no | false | |
| se_integra | boolean | no | false | |
| created_at | timestamp | sí | null | |
| updated_at | timestamp | sí | null | |

---

### Tabla: auditoria_etiquetas

Claves y restricciones:
- PK: `id`
- FKs: `auditoria_id -> aditorias(id)`, `etiqueta_id -> cat_etiquetas(id)`, `checklist_apartado_id -> checklist_apartados(id)`, `procesado_por -> users(id)`, `apartado_id -> apartados(id)`
- Índices: unique(`auditoria_id`,`etiqueta_id`,`apartado_id`) `unique_auditoria_etiqueta_apartado_nuevo`; index(`auditoria_id`,`etiqueta_id`); index(`procesado_en`)

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| id | bigIncrements | no | - | PK |
| auditoria_id | foreignId | no | - | FK |
| etiqueta_id | foreignId | no | - | FK |
| checklist_apartado_id | foreignId | no | - | FK (no nullable por migración 2025-06-17) |
| razon_asignacion | text | no | - | |
| comentario_fuente | text | sí | null | |
| respuesta_ia | text | sí | null | |
| confianza_ia | decimal(3,2) | no | 0.00 | |
| validado_manualmente | boolean | no | false | |
| procesado_por | foreignId | sí | null | FK, ON DELETE SET NULL |
| procesado_en | timestamp | no | - | |
| apartado_id | foreignId | sí | null | FK |
| created_at | timestamp | sí | null | |
| updated_at | timestamp | sí | null | |

---

### Tabla: entregas

Claves y restricciones:
- PK: `id`
- FK: `auditoria_id -> aditorias(id)`, `confirmado_por -> users(id)`, `recibido_por -> users(id)` ON DELETE SET NULL

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| id | bigIncrements | no | - | PK |
| auditoria_id | unsignedBigInteger | no | - | FK |
| clave_accion | string | no | - | |
| tipo_accion | string | no | - | |
| CP | string | no | - | |
| entrega | string | no | - | |
| fecha_entrega | date | no | - | |
| responsable | string | no | - | |
| numero_legajos | integer | no | - | |
| confirmado_por | unsignedBigInteger | no | - | FK |
| estado | string | no | "Pendiente" | |
| recibido_por | unsignedBigInteger | sí | null | FK |
| fecha_real_entrega | timestamp | sí | null | |
| created_at | timestamp | sí | null | |
| updated_at | timestamp | sí | null | |

---

### Tabla: recepcion_entregas

Claves y restricciones:
- PK: `id`
- FK: `entrega_id -> entregas(id)`

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| id | bigIncrements | no | - | PK |
| entrega_id | unsignedBigInteger | no | - | FK |
| nombre_servidor_uaa | string | no | - | |
| puesto_servidor_uaa | string | no | - | |
| firma_servidor_uaa | string | no | - | |
| nombre_servidor_dce | string | no | - | |
| puesto_servidor_dce | string | no | - | |
| firma_servidor_dce | string | no | - | |
| created_at | timestamp | sí | null | |
| updated_at | timestamp | sí | null | |

---

### Tabla: entregas_historial

Claves y restricciones:
- PK: `id`
- FK: `entrega_id -> entregas(id)`

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| id | bigIncrements | no | - | PK |
| entrega_id | unsignedBigInteger | no | - | FK |
| estado | string(255) | no | - | |
| fecha_estado | timestamp | no | now() | |
| pdf_path | string | sí | null | |
| created_at | timestamp | sí | null | |
| updated_at | timestamp | sí | null | |

---

### Tabla: entregas_pdf_histories

Claves y restricciones:
- PK: `id`
- FK: `entrega_id -> entregas(id)`, `generated_by -> users(id)`

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| id | bigIncrements | no | - | PK |
| entrega_id | unsignedBigInteger | no | - | FK |
| hash | string | sí | null | |
| pdf_path | string | no | - | |
| generated_by | unsignedBigInteger | no | - | FK |
| created_at | timestamp | sí | null | |
| updated_at | timestamp | sí | null | |

---

### Tabla: pdf_histories

Claves y restricciones:
- PK: `id`
- FK: `auditoria_id -> aditorias(id)`, `generated_by -> users(id)`

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| id | bigIncrements | no | - | PK |
| auditoria_id | unsignedBigInteger | no | - | FK |
| clave_de_accion | string | no | - | |
| pdf_path | string | no | - | |
| generated_by | unsignedBigInteger | no | - | FK |
| created_at | timestamp | sí | null | |
| updated_at | timestamp | sí | null | |

---

### Tabla: pdf_hashes

Claves y restricciones:
- PK: `id`
- UK: `hash`
- FK: `auditoria_id -> aditorias(id)`

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| id | bigIncrements | no | - | PK |
| auditoria_id | unsignedBigInteger | no | - | FK |
| hash | string | no | unique | |
| email | string | no | - | |
| ip_address | string | no | - | |
| generated_at | timestamp | no | - | |
| created_at | timestamp | sí | null | |
| updated_at | timestamp | sí | null | |

---

### Tabla: auditorias_histories

Claves y restricciones:
- PK: `id`
- FK: `auditoria_id -> aditorias(id)`, `changed_by -> users(id)`

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| id | bigIncrements | no | - | PK |
| auditoria_id | unsignedBigInteger | no | - | FK |
| changed_by | unsignedBigInteger | no | - | FK |
| changes | json | no | - | |
| created_at | timestamp | sí | null | |
| updated_at | timestamp | sí | null | |

---

### Tabla: checklist_apartado_histories

Claves y restricciones:
- PK: `id`
- FK: `checklist_apartado_id -> checklist_apartados(id)`, `changed_by -> users(id)`

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| id | bigIncrements | no | - | PK |
| checklist_apartado_id | unsignedBigInteger | no | - | FK |
| changed_by | unsignedBigInteger | no | - | FK |
| changes | json | no | - | |
| created_at | timestamp | sí | null | |
| updated_at | timestamp | sí | null | |

---

### Tabla: imports

Claves y restricciones:
- PK: `id`

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| id | bigIncrements | no | - | PK |
| file_path | string | no | - | |
| processed_rows | integer | no | 0 | |
| total_rows | integer | sí | null | |
| status | enum('pending','processing','completed','failed') | no | 'pending' | |
| created_at | timestamp | sí | null | |
| updated_at | timestamp | sí | null | |

---

### Tablas catálogo (patrón común)

Aplica a: `cat_cuenta_publica`, `cat_dgseg_ef`, `cat_clave_accion`, `cat_ente_de_la_accion`, `cat_ente_fiscalizado`, `cat_siglas_auditoria_especial`, `cat_tipo_de_auditoria`, `cat_uaa`, `cat_auditoria_especial`, `cat_entrega`.

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| id | bigIncrements | no | - | PK |
| valor | string | no | - | unique |
| descripcion | text | sí | null | |
| activo | boolean | no | true | |
| created_at | timestamp | sí | null | |
| updated_at | timestamp | sí | null | |

### Tabla: cat_siglas_tipo_accion (variación)

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| id | bigIncrements | no | - | PK |
| valor | string | no | - | unique |
| description | text | sí | null | (nombre de columna en inglés) |
| activo | boolean | no | true | |
| created_at | timestamp | sí | null | |
| updated_at | timestamp | sí | null | |

---

### Tabla: users (y auxiliares)

Claves y restricciones:
- PK: `id`
- FK: `uaa_id -> cat_uaa(id)` ON DELETE SET NULL

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| id | bigIncrements | no | - | PK |
| name | string | no | - | |
| email | string | no | - | unique |
| uaa_id | unsignedBigInteger | sí | null | FK |
| email_verified_at | timestamp | sí | null | |
| password | string | no | - | |
| remember_token | string | sí | null | |
| current_team_id | foreignId | sí | null | |
| profile_photo_path | string(2048) | sí | null | |
| user_ap_accepted | boolean | no | false | |
| user_ap_accepted_date | dateTime | sí | null | |
| user_ap_version | string(50) | sí | null | |
| user_ap_ip | string(45) | sí | null | |
| user_ap_user_agent | text | sí | null | |
| created_at | timestamp | sí | null | |
| updated_at | timestamp | sí | null | |

Tablas auxiliares:
- `password_reset_tokens(email PK, token, created_at)`
- `sessions(id PK, user_id FK nullable, ip_address, user_agent, payload, last_activity)`

---

### Tablas de permisos y roles (Spatie)

- `permissions`: `id PK`, `name`, `guard_name`, `timestamps`, unique(`name`, `guard_name`)
- `roles`: `id PK`, `[team_foreign_key nullable si aplica]`, `name`, `guard_name`, `timestamps`, unique compuesto según configuración
- `model_has_permissions`: `permission_id FK -> permissions(id)`, `model_type`, `model_id`, PK compuesto; índice en `(model_id, model_type)`
- `model_has_roles`: `role_id FK -> roles(id)`, `model_type`, `model_id`, PK compuesto; índice en `(model_id, model_type)`
- `role_has_permissions`: `permission_id FK -> permissions(id)`, `role_id FK -> roles(id)`, PK compuesto

---

### Tabla: personal_access_tokens

| Columna | Tipo | Nulo | Default | Notas |
|---|---|---|---|---|
| id | bigIncrements | no | - | PK |
| tokenable_type | string | no | - | |
| tokenable_id | unsignedBigInteger | no | - | |
| name | string | no | - | |
| token | string(64) | no | - | unique |
| abilities | text | sí | null | |
| last_used_at | timestamp | sí | null | |
| expires_at | timestamp | sí | null | |
| created_at | timestamp | sí | null | |
| updated_at | timestamp | sí | null | |

---

Tablas de infraestructura como `jobs`, `failed_jobs`, `cache`, etc., no se listan por brevedad.



