```mermaid
    erDiagram
        %% Entidades principales del sistema
        users {
            int id PK
            string name
            string email
            int uaa_id FK
            string password
            string profile_photo_path
            string firma_autografa
            string puesto
            timestamp created_at
            timestamp updated_at
        }

        aditorias {
            int id PK
            string clave_de_accion UK
            int cuenta_publica FK
            int entrega FK
            int auditoria_especial FK
            int tipo_de_auditoria FK
            int siglas_auditoria_especial FK
            int uaa FK
            string titulo
            int ente_fiscalizado FK
            int numero_de_auditoria
            int ente_de_la_accion FK
            int clave_accion FK
            int siglas_tipo_accion FK
            int dgseg_ef FK
            string nombre_director_general
            string direccion_de_area
            string nombre_director_de_area
            string sub_direccion_de_area
            string nombre_sub_director_de_area
            string jd
            string jefe_de_departamento
            string estatus_checklist
            string estatus_entrega
            string auditor_nombre
            string auditor_puesto
            string seguimiento_nombre
            string seguimiento_puesto
            text comentarios
            timestamp created_at
            timestamp updated_at
        }

        apartados {
            int id PK
            string nombre
            text descripcion
            int parent_id FK
            int nivel
            int auditoria_id FK
            timestamp created_at
            timestamp updated_at
        }

        checklist_apartados {
            int id PK
            int apartado_id FK
            int auditoria_id FK
            boolean se_aplica
            boolean es_obligatorio
            boolean se_integra
            text observaciones
            text comentarios_uaa
            timestamp created_at
            timestamp updated_at
        }

        entregas {
            int id PK
            int auditoria_id FK
            string clave_accion
            string tipo_accion
            string CP
            string entrega
            date fecha_entrega
            string responsable
            int numero_legajos
            int confirmado_por FK
            int recibido_por FK
            timestamp fecha_real_entrega
            string estado
            timestamp created_at
            timestamp updated_at
        }

        %% Sistema de etiquetado con IA
        cat_etiquetas {
            int id PK
            string nombre UK
            text descripcion
            string color
            boolean activo
            int veces_usada
            timestamp created_at
            timestamp updated_at
        }

        auditoria_etiquetas {
            int id PK
            int auditoria_id FK
            int etiqueta_id FK
            int apartado_id FK
            int checklist_apartado_id FK
            text razon_asignacion
            text comentario_fuente
            decimal confianza_ia
            boolean validado_manualmente
            int procesado_por FK
            timestamp procesado_en
            timestamp created_at
            timestamp updated_at
        }

        %% Catálogos del sistema
        cat_uaa {
            int id PK
            string nombre
            string descripcion
            timestamp created_at
            timestamp updated_at
        }

        cat_cuenta_publica {
            int id PK
            string nombre
            string descripcion
            timestamp created_at
            timestamp updated_at
        }

        cat_entrega {
            int id PK
            string nombre
            string descripcion
            timestamp created_at
            timestamp updated_at
        }

        cat_auditoria_especial {
            int id PK
            string nombre
            string descripcion
            timestamp created_at
            timestamp updated_at
        }

        cat_tipo_de_auditoria {
            int id PK
            string nombre
            string descripcion
            timestamp created_at
            timestamp updated_at
        }

        cat_ente_fiscalizado {
            int id PK
            string nombre
            string descripcion
            timestamp created_at
            timestamp updated_at
        }

        cat_ente_de_la_accion {
            int id PK
            string nombre
            string descripcion
            timestamp created_at
            timestamp updated_at
        }

        cat_clave_accion {
            int id PK
            string nombre
            string descripcion
            timestamp created_at
            timestamp updated_at
        }

        cat_dgseg_ef {
            int id PK
            string nombre
            string descripcion
            timestamp created_at
            timestamp updated_at
        }

        cat_siglas_tipo_accion {
            int id PK
            string nombre
            string descripcion
            timestamp created_at
            timestamp updated_at
        }

        cat_siglas_auditoria_especial {
            int id PK
            string nombre
            string descripcion
            timestamp created_at
            timestamp updated_at
        }

        %% Entidades de historial y control
        auditorias_histories {
            int id PK
            int auditoria_id FK
            int changed_by FK
            json changes
            timestamp created_at
            timestamp updated_at
        }

        checklist_apartado_histories {
            int id PK
            int checklist_apartado_id FK
            int changed_by FK
            json changes
            timestamp created_at
            timestamp updated_at
        }

        pdf_histories {
            int id PK
            int auditoria_id FK
            string clave_de_accion
            string pdf_path
            int generated_by FK
            timestamp created_at
            timestamp updated_at
        }

        apartado_plantillas {
            int id PK
            int apartado_id FK
            text plantilla
            boolean es_obligatorio
            boolean se_integra
            boolean es_aplicable
            timestamp created_at
            timestamp updated_at
        }

        %% Relaciones principales
        users ||--o{ aditorias : "uaa"
        users ||--o{ entregas : "confirmado_por/recibido_por"
        users ||--o{ auditoria_etiquetas : "procesado_por"
        users ||--o{ auditorias_histories : "changed_by"
        users ||--o{ checklist_apartado_histories : "changed_by"
        users ||--o{ pdf_histories : "generated_by"

        %% Relaciones de auditorías con catálogos
        aditorias ||--o{ cat_uaa : "uaa"
        aditorias ||--o{ cat_cuenta_publica : "cuenta_publica"
        aditorias ||--o{ cat_entrega : "entrega"
        aditorias ||--o{ cat_auditoria_especial : "auditoria_especial"
        aditorias ||--o{ cat_tipo_de_auditoria : "tipo_de_auditoria"
        aditorias ||--o{ cat_ente_fiscalizado : "ente_fiscalizado"
        aditorias ||--o{ cat_ente_de_la_accion : "ente_de_la_accion"
        aditorias ||--o{ cat_clave_accion : "clave_accion"
        aditorias ||--o{ cat_dgseg_ef : "dgseg_ef"
        aditorias ||--o{ cat_siglas_tipo_accion : "siglas_tipo_accion"
        aditorias ||--o{ cat_siglas_auditoria_especial : "siglas_auditoria_especial"

        %% Relaciones jerárquicas y funcionales
        aditorias ||--o{ apartados : "auditoria_id"
        aditorias ||--o{ checklist_apartados : "auditoria_id"
        aditorias ||--o{ entregas : "auditoria_id"
        aditorias ||--o{ auditoria_etiquetas : "auditoria_id"
        aditorias ||--o{ auditorias_histories : "auditoria_id"
        aditorias ||--o{ pdf_histories : "auditoria_id"

        apartados ||--o{ apartados : "parent_id"
        apartados ||--o{ checklist_apartados : "apartado_id"
        apartados ||--o{ apartado_plantillas : "apartado_id"
        apartados ||--o{ auditoria_etiquetas : "apartado_id"

        checklist_apartados ||--o{ checklist_apartado_histories : "checklist_apartado_id"
        checklist_apartados ||--o{ auditoria_etiquetas : "checklist_apartado_id"

        %% Sistema de etiquetado
        cat_etiquetas ||--o{ auditoria_etiquetas : "etiqueta_id"