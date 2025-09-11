# Manual Técnico - Sistema SAES

## Índice
1. [Descripción General del Sistema](#1-descripción-general-del-sistema)
2. [Arquitectura del Sistema](#2-arquitectura-del-sistema)
3. [Requisitos del Sistema](#3-requisitos-del-sistema)
4. [Instalación y Configuración](#4-instalación-y-configuración)
5. [Mantenimiento Preventivo y Correctivo](#5-mantenimiento-preventivo-y-correctivo)
6. [Consideraciones de Seguridad](#6-consideraciones-de-seguridad)
7. [Troubleshooting](#7-troubleshooting)
8. [Apéndices](#8-apéndices)

---

## 1. Descripción General del Sistema

### 1.1 Propósito
SAES (Sistema de Auditorías y Entregas) es una aplicación web desarrollada en Laravel 11 que gestiona auditorías, apartados, entregas y reportes para entidades gubernamentales. El sistema incluye funcionalidades de IA, generación de PDFs, validación de documentos y gestión de usuarios con roles y permisos.

### 1.2 Funcionalidades Principales
- **Gestión de Auditorías**: Creación, edición y seguimiento de auditorías
- **Apartados**: Gestión de apartados y checklist asociados
- **Entregas**: Sistema de recepción y validación de entregas
- **Reportes**: Generación de reportes en Excel y PDF
- **IA Integrada**: Chatbot y análisis inteligente de contenido
- **Gestión de Usuarios**: Sistema de roles y permisos
- **Validación de Documentos**: Sistema de hash para verificación de integridad

---

## 2. Arquitectura del Sistema

### 2.1 Stack Tecnológico
- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Livewire 3, Blade Templates
- **Base de Datos**: MySQL/MariaDB/SQLite
- **Cache**: Redis/Database/File
- **Autenticación**: Laravel Jetstream + Sanctum
- **Panel Admin**: Filament 3
- **Generación PDF**: DomPDF + FPDI
- **Excel**: Maatwebsite Excel
- **IA**: Claude API (Anthropic)

### 2.2 Estructura de la Base de Datos

#### 2.2.1 Tablas Principales
- **`aditorias`**: Tabla central de auditorías
- **`apartados`**: Apartados asociados a auditorías
- **`entregas`**: Sistema de entregas y recepciones
- **`users`**: Usuarios del sistema
- **`permissions`**: Permisos del sistema
- **`roles`**: Roles de usuario

#### 2.2.2 Tablas de Catálogos
- **`cat_auditoria_especial`**: Tipos de auditorías especiales
- **`cat_clave_accion`**: Claves de acción
- **`cat_cuenta_publica`**: Cuentas públicas
- **`cat_ente_fiscalizado`**: Entes fiscalizados
- **`cat_uaa`**: Unidades administrativas
- **`cat_tipo_de_auditoria`**: Tipos de auditoría

#### 2.2.3 Tablas de Historial
- **`auditorias_histories`**: Historial de cambios en auditorías
- **`pdf_histories`**: Historial de PDFs generados
- **`entregas_histories`**: Historial de entregas

### 2.3 Estructura de Directorios
```
saes/
├── app/
│   ├── Actions/          # Acciones de Fortify/Jetstream
│   ├── Console/          # Comandos Artisan
│   ├── Exports/          # Exportaciones Excel
│   ├── Filament/         # Recursos del panel admin
│   ├── Helpers/          # Helpers personalizados
│   ├── Http/             # Controladores y middleware
│   ├── Imports/          # Importaciones Excel
│   ├── Jobs/             # Jobs en cola
│   ├── Livewire/         # Componentes Livewire
│   ├── Mail/             # Plantillas de correo
│   ├── Models/           # Modelos Eloquent
│   └── Policies/         # Políticas de autorización
├── config/               # Archivos de configuración
├── database/             # Migraciones y seeders
├── resources/            # Vistas y assets
└── routes/               # Definición de rutas
```

---

## 3. Requisitos del Sistema

### 3.1 Requisitos del Servidor
- **Sistema Operativo**: Linux (Ubuntu 20.04+), Windows Server 2019+, macOS 12+
- **PHP**: 8.2 o superior
- **Base de Datos**: MySQL 8.0+, MariaDB 10.5+, SQLite 3.0+
- **Servidor Web**: Apache 2.4+ o Nginx 1.18+
- **Memoria RAM**: Mínimo 2GB, recomendado 4GB+
- **Espacio en Disco**: Mínimo 10GB para aplicación + datos

### 3.2 Extensiones PHP Requeridas
- BCMath PHP Extension
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

### 3.3 Dependencias del Sistema
- Composer 2.0+
- Node.js 16+ (para compilación de assets)
- Git (para control de versiones)

---

## 4. Instalación y Configuración

### 4.1 Preparación del Entorno

#### 4.1.1 Clonar el Repositorio
```bash
git clone [URL_DEL_REPOSITORIO] saes
cd saes
```

#### 4.1.2 Instalar Dependencias PHP
```bash
composer install --no-dev --optimize-autoloader
```

#### 4.1.3 Instalar Dependencias Node.js (opcional)
```bash
npm install
npm run build
```

### 4.2 Configuración del Entorno

#### 4.2.1 Archivo de Variables de Entorno
```bash
cp .env.example .env
```

#### 4.2.2 Configuración Básica (.env)
```env
APP_NAME="SAES - Sistema de Auditorías"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=saes_db
DB_USERNAME=saes_user
DB_PASSWORD=password_seguro

CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

CLAUDE_API=sk-ant-api03-...
CLAUDE_MODEL=claude-3-5-haiku-20241022
```

#### 4.2.3 Generar Clave de Aplicación
```bash
php artisan key:generate
```

### 4.3 Configuración de la Base de Datos

#### 4.3.1 Crear Base de Datos
```sql
CREATE DATABASE saes_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'saes_user'@'localhost' IDENTIFIED BY 'password_seguro';
GRANT ALL PRIVILEGES ON saes_db.* TO 'saes_user'@'localhost';
FLUSH PRIVILEGES;
```

#### 4.3.2 Ejecutar Migraciones
```bash
php artisan migrate --force
```

#### 4.3.3 Ejecutar Seeders (opcional)
```bash
php artisan db:seed --force
```

### 4.4 Configuración del Servidor Web

#### 4.4.1 Apache (Virtual Host)
```apache
<VirtualHost *:80>
    ServerName saes.tudominio.com
    DocumentRoot /var/www/saes/public
    
    <Directory /var/www/saes/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/saes_error.log
    CustomLog ${APACHE_LOG_DIR}/saes_access.log combined
</VirtualHost>
```

#### 4.4.2 Nginx
```nginx
server {
    listen 80;
    server_name saes.tudominio.com;
    root /var/www/saes/public;
    
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    
    index index.php;
    
    charset utf-8;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    
    error_page 404 /index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 4.5 Configuración de Permisos
```bash
sudo chown -R www-data:www-data /var/www/saes
sudo chmod -R 755 /var/www/saes
sudo chmod -R 775 /var/www/saes/storage
sudo chmod -R 775 /var/www/saes/bootstrap/cache
```

### 4.6 Configuración de Colas (Jobs)
```bash
# Crear tabla de jobs
php artisan queue:table
php artisan migrate

# Configurar supervisor para procesar colas
sudo nano /etc/supervisor/conf.d/saes-worker.conf
```

**Contenido del archivo supervisor:**
```ini
[program:saes-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/saes/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/saes/storage/logs/worker.log
stopwaitsecs=3600
```

### 4.7 Configuración de Caché
```bash
# Limpiar caché inicial
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 5. Mantenimiento Preventivo y Correctivo

### 5.1 Mantenimiento Preventivo

#### 5.1.1 Tareas Programadas (Cron)
```bash
# Editar crontab
crontab -e

# Agregar las siguientes líneas:
* * * * * cd /var/www/saes && php artisan schedule:run >> /dev/null 2>&1
0 2 * * * cd /var/www/saes && php artisan backup:run >> /dev/null 2>&1
0 3 * * * cd /var/www/saes && php artisan cache:clear >> /dev/null 2>&1
```

#### 5.1.2 Monitoreo del Sistema
```bash
# Verificar logs del sistema
tail -f /var/www/saes/storage/logs/laravel.log

# Verificar logs de Apache/Nginx
tail -f /var/log/apache2/saes_error.log
tail -f /var/log/nginx/saes_error.log

# Verificar estado de servicios
sudo systemctl status apache2
sudo systemctl status mysql
sudo systemctl status redis
sudo systemctl status supervisor
```

#### 5.1.3 Respaldo Automático
```bash
# Crear script de respaldo
sudo nano /usr/local/bin/saes-backup.sh
```

**Contenido del script:**
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/saes"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="saes_db"
DB_USER="saes_user"
DB_PASS="password_seguro"

# Crear directorio de respaldo
mkdir -p $BACKUP_DIR

# Respaldo de base de datos
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql

# Respaldo de archivos
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz /var/www/saes

# Mantener solo los últimos 7 respaldos
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Respaldo completado: $DATE" >> $BACKUP_DIR/backup.log
```

### 5.2 Mantenimiento Correctivo

#### 5.2.1 Problemas Comunes y Soluciones

**Error: "Class not found"**
```bash
# Limpiar autoloader
composer dump-autoload

# Limpiar caché
php artisan config:clear
php artisan cache:clear
```

**Error: "Permission denied"**
```bash
# Corregir permisos
sudo chown -R www-data:www-data /var/www/saes
sudo chmod -R 755 /var/www/saes
sudo chmod -R 775 /var/www/saes/storage
```

**Error: "Database connection failed"**
```bash
# Verificar conexión a base de datos
php artisan tinker
DB::connection()->getPdo();

# Verificar variables de entorno
php artisan config:show database
```

**Error: "Queue not working"**
```bash
# Reiniciar supervisor
sudo supervisorctl restart saes-worker:*

# Verificar estado de colas
php artisan queue:work --once
```

#### 5.2.2 Procedimientos de Recuperación

**Recuperación de Base de Datos:**
```bash
# Restaurar desde respaldo
mysql -u$DB_USER -p$DB_PASS $DB_NAME < /var/backups/saes/db_backup_YYYYMMDD_HHMMSS.sql

# Verificar integridad
php artisan migrate:status
php artisan db:seed --force
```

**Recuperación de Archivos:**
```bash
# Restaurar archivos desde respaldo
sudo tar -xzf /var/backups/saes/files_backup_YYYYMMDD_HHHMMSS.tar.gz -C /

# Corregir permisos
sudo chown -R www-data:www-data /var/www/saes
sudo chmod -R 755 /var/www/saes
```

### 5.3 Monitoreo de Rendimiento

#### 5.3.1 Comandos de Diagnóstico
```bash
# Verificar estado del sistema
php artisan about

# Verificar caché
php artisan cache:table
php artisan cache:show

# Verificar colas
php artisan queue:table
php artisan queue:failed

# Verificar logs
php artisan log:clear
```

#### 5.3.2 Optimización de Base de Datos
```bash
# Analizar tablas
php artisan tinker
DB::select('SHOW TABLE STATUS');

# Optimizar tablas
php artisan tinker
DB::statement('OPTIMIZE TABLE aditorias');
DB::statement('OPTIMIZE TABLE apartados');
```

---

## 6. Consideraciones de Seguridad

### 6.1 Seguridad de la Aplicación

#### 6.1.1 Autenticación y Autorización
- **Laravel Jetstream**: Sistema robusto de autenticación
- **Laravel Sanctum**: API tokens seguros
- **Spatie Laravel Permission**: Control granular de permisos
- **Verificación de Email**: Obligatoria para nuevos usuarios
- **Autenticación de Dos Factores**: Opcional pero recomendada

#### 6.1.2 Protección de Datos
- **Validación de Entrada**: Sanitización de datos de entrada
- **Prevención de Inyección SQL**: Uso de Eloquent ORM
- **Protección CSRF**: Tokens automáticos en formularios
- **Sanitización XSS**: Escape automático en Blade
- **Encriptación**: Claves AES-256-CBC

#### 6.1.3 Gestión de Sesiones
```php
// Configuración de sesiones en config/session.php
'lifetime' => env('SESSION_LIFETIME', 120),
'expire_on_close' => false,
'secure' => env('SESSION_SECURE_COOKIE', true),
'http_only' => true,
'same_site' => 'lax',
```

### 6.2 Seguridad de la Infraestructura

#### 6.2.1 Configuración del Servidor
```bash
# Configurar firewall
sudo ufw enable
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw deny 3306/tcp

# Configurar fail2ban
sudo apt install fail2ban
sudo systemctl enable fail2ban
```

#### 6.2.2 Seguridad de la Base de Datos
```sql
-- Crear usuario con permisos limitados
CREATE USER 'saes_app'@'localhost' IDENTIFIED BY 'password_complejo';
GRANT SELECT, INSERT, UPDATE, DELETE ON saes_db.* TO 'saes_app'@'localhost';
REVOKE CREATE, DROP, ALTER ON saes_db.* FROM 'saes_app'@'localhost';

-- Configurar SSL para conexiones remotas
GRANT SELECT, INSERT, UPDATE, DELETE ON saes_db.* TO 'saes_app'@'%' REQUIRE SSL;
```

#### 6.2.3 Configuración de SSL/TLS
```apache
# Configuración Apache SSL
<VirtualHost *:443>
    ServerName saes.tudominio.com
    DocumentRoot /var/www/saes/public
    
    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/saes.crt
    SSLCertificateKeyFile /etc/ssl/private/saes.key
    SSLCertificateChainFile /etc/ssl/certs/saes-chain.crt
    
    # Configuraciones de seguridad SSL
    SSLProtocol all -SSLv2 -SSLv3 -TLSv1 -TLSv1.1
    SSLCipherSuite ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512
    SSLHonorCipherOrder on
    SSLCompression off
    
    # Headers de seguridad
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
    Header always set X-Frame-Options DENY
    Header always set X-Content-Type-Options nosniff
</VirtualHost>
```

### 6.3 Cumplimiento y Auditoría

#### 6.3.1 Logs de Seguridad
```bash
# Configurar rotación de logs
sudo nano /etc/logrotate.d/saes

# Contenido:
/var/www/saes/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
    postrotate
        /usr/bin/systemctl reload apache2
    endscript
}
```

#### 6.3.2 Monitoreo de Accesos
```bash
# Verificar accesos fallidos
grep "Failed login" /var/www/saes/storage/logs/laravel.log

# Verificar intentos de SQL injection
grep -i "sql" /var/log/apache2/saes_error.log

# Verificar accesos no autorizados
grep "Unauthorized" /var/www/saes/storage/logs/laravel.log
```

#### 6.3.3 Políticas de Seguridad
- **Contraseñas**: Mínimo 8 caracteres, mayúsculas, minúsculas, números
- **Sesiones**: Timeout automático después de 2 horas de inactividad
- **API Keys**: Rotación mensual de claves de API
- **Backups**: Encriptación de respaldos con GPG
- **Auditoría**: Logs de todas las acciones críticas del sistema

#### 6.3.4 Sugerencias de Seguridad y Desarrollo Continuo

##### 6.3.4.1 Mejoras de Seguridad Recomendadas

**Implementación de WAF (Web Application Firewall)**
```bash
# Instalación de ModSecurity para Apache
sudo apt install libapache2-mod-security2
sudo a2enmod security2

# Configuración básica de ModSecurity
sudo cp /etc/modsecurity/modsecurity.conf-recommended /etc/modsecurity/modsecurity.conf
sudo nano /etc/modsecurity/modsecurity.conf
```

**Configuración de ModSecurity:**
```apache
# /etc/modsecurity/modsecurity.conf
SecRuleEngine On
SecResponseBodyAccess On
SecRequestBodyAccess On
SecRequestBodyLimit 13107200
SecRequestBodyNoFilesLimit 131072
SecRequestBodyInMemoryLimit 131072
SecRequestBodyLimitAction Reject
SecRule REQUEST_HEADERS:Content-Type "text/xml" \
     "id:'200000',phase:1,t:none,t:lowercase,pass,nolog,ctl:requestBodyProcessor=XML"
```

**Implementación de Rate Limiting**
```php
// En app/Http/Kernel.php
protected $middlewareGroups = [
    'web' => [
        // ... otros middleware
        \App\Http\Middleware\RateLimitRequests::class,
    ],
];

// Crear middleware personalizado para rate limiting
php artisan make:middleware RateLimitRequests
```

**Middleware de Rate Limiting:**
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RateLimitRequests
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveRequestSignature($request);
        
        if ($this->limiter->tooManyAttempts($key, $this->maxAttempts())) {
            return $this->buildResponse($key);
        }

        $this->limiter->hit($key, $this->decayMinutes() * 60);

        $response = $next($request);

        return $this->addHeaders(
            $response, $this->maxAttempts(),
            $this->calculateRemainingAttempts($key)
        );
    }

    protected function maxAttempts(): int
    {
        return 60; // 60 intentos por minuto
    }

    protected function decayMinutes(): int
    {
        return 1; // Por minuto
    }

    protected function resolveRequestSignature(Request $request): string
    {
        return sha1(
            $request->method() .
            '|' . $request->server('SERVER_NAME') .
            '|' . $request->ip() .
            '|' . $request->userAgent()
        );
    }
}
```

##### 6.3.4.2 Monitoreo Avanzado de Seguridad

**Implementación de SIEM (Security Information and Event Management)**
```bash
# Instalación de OSSEC HIDS
sudo apt install ossec-hids

# Configuración de OSSEC
sudo /var/ossec/bin/ossec-control start
sudo /var/ossec/bin/manage_agents
```

**Configuración de OSSEC para Laravel:**
```xml
<!-- /var/ossec/etc/ossec.conf -->
<localfile>
    <log_format>syslog</log_format>
    <location>/var/www/saes/storage/logs/laravel.log</location>
</localfile>

<localfile>
    <log_format>syslog</log_format>
    <location>/var/log/apache2/saes_error.log</location>
</localfile>

<localfile>
    <log_format>syslog</log_format>
    <location>/var/log/nginx/saes_error.log</location>
</localfile>
```

**Script de Monitoreo de Seguridad:**
```bash
#!/bin/bash
# /usr/local/bin/security-monitor.sh

LOG_FILE="/var/log/security-monitor.log"
ALERT_EMAIL="admin@tudominio.com"

# Verificar intentos de login fallidos
FAILED_LOGINS=$(grep -c "Failed login" /var/www/saes/storage/logs/laravel.log)

if [ $FAILED_LOGINS -gt 10 ]; then
    echo "$(date): ALERTA - Múltiples intentos de login fallidos: $FAILED_LOGINS" >> $LOG_FILE
    echo "Se detectaron $FAILED_LOGINS intentos de login fallidos" | mail -s "Alerta de Seguridad SAES" $ALERT_EMAIL
fi

# Verificar accesos a archivos sensibles
SENSITIVE_ACCESS=$(grep -c "GET.*\.env\|GET.*\.git\|GET.*\.sql" /var/log/apache2/saes_access.log)

if [ $SENSITIVE_ACCESS -gt 0 ]; then
    echo "$(date): ALERTA - Acceso a archivos sensibles detectado: $SENSITIVE_ACCESS" >> $LOG_FILE
    echo "Se detectaron $SENSITIVE_ACCESS accesos a archivos sensibles" | mail -s "Alerta de Seguridad SAES" $ALERT_EMAIL
fi

# Verificar cambios en archivos críticos
find /var/www/saes -name "*.php" -mtime -1 -exec md5sum {} \; > /tmp/current_hashes.txt

if [ -f /tmp/previous_hashes.txt ]; then
    if ! cmp -s /tmp/current_hashes.txt /tmp/previous_hashes.txt; then
        echo "$(date): ALERTA - Cambios detectados en archivos PHP" >> $LOG_FILE
        echo "Se detectaron cambios en archivos PHP del sistema" | mail -s "Alerta de Seguridad SAES" $ALERT_EMAIL
    fi
fi

mv /tmp/current_hashes.txt /tmp/previous_hashes.txt
```

##### 6.3.4.3 Auditoría de Código y Análisis de Vulnerabilidades

**Implementación de Análisis Estático de Código**
```bash
# Instalación de herramientas de análisis
composer require --dev phpstan/phpstan
composer require --dev phpcs/phpcs
composer require --dev phpmd/phpmd

# Configuración de PHPStan
# phpstan.neon
parameters:
    level: 8
    paths:
        - app
        - config
    excludePaths:
        - app/Console
        - app/Exceptions
    checkMissingIterableValueType: false
```

**Script de Análisis Automático:**
```bash
#!/bin/bash
# /usr/local/bin/code-analysis.sh

PROJECT_DIR="/var/www/saes"
REPORT_DIR="/var/www/saes/storage/reports/security"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $REPORT_DIR

echo "=== Análisis de Seguridad del Código SAES - $DATE ===" > $REPORT_DIR/security_analysis_$DATE.txt

# Análisis con PHPStan
echo "--- Análisis PHPStan ---" >> $REPORT_DIR/security_analysis_$DATE.txt
cd $PROJECT_DIR && vendor/bin/phpstan analyse app config --level=8 >> $REPORT_DIR/security_analysis_$DATE.txt 2>&1

# Análisis con PHP_CodeSniffer
echo "--- Análisis PHP_CodeSniffer ---" >> $REPORT_DIR/security_analysis_$DATE.txt
cd $PROJECT_DIR && vendor/bin/phpcs app config --standard=PSR12 >> $REPORT_DIR/security_analysis_$DATE.txt 2>&1

# Análisis con PHPMD
echo "--- Análisis PHPMD ---" >> $REPORT_DIR/security_analysis_$DATE.txt
cd $PROJECT_DIR && vendor/bin/phpmd app text cleancode,codesize,controversial,design,naming,unusedcode >> $REPORT_DIR/security_analysis_$DATE.txt 2>&1

# Verificar dependencias vulnerables
echo "--- Verificación de Dependencias ---" >> $REPORT_DIR/security_analysis_$DATE.txt
cd $PROJECT_DIR && composer audit >> $REPORT_DIR/security_analysis_$DATE.txt 2>&1

# Limpiar reportes antiguos (mantener solo los últimos 30 días)
find $REPORT_DIR -name "security_analysis_*.txt" -mtime +30 -delete

echo "Análisis completado. Reporte guardado en: $REPORT_DIR/security_analysis_$DATE.txt"
```

##### 6.3.4.4 Implementación de Honeypots y Trampas

**Honeypot para Detectar Bots y Ataques**
```php
// Crear middleware honeypot
php artisan make:middleware HoneypotMiddleware

// app/Http/Middleware/HoneypotMiddleware.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class HoneypotMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Campo honeypot oculto
        if ($request->has('website') && !empty($request->input('website'))) {
            Log::warning('Honeypot activado', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'data' => $request->all()
            ]);
            
            // Bloquear IP temporalmente
            $this->blockIP($request->ip());
            
            return response('', 200); // Respuesta falsa
        }
        
        return $next($request);
    }
    
    private function blockIP($ip)
    {
        $key = "blocked_ip_{$ip}";
        Cache::put($key, true, now()->addHours(24));
        
        // Agregar a lista negra temporal
        Log::alert('IP bloqueada por honeypot', ['ip' => $ip]);
    }
}
```

**Implementación en Formularios:**
```blade
{{-- En formularios de login/registro --}}
<div style="display: none;">
    <input type="text" name="website" tabindex="-1" autocomplete="off">
</div>
```

##### 6.3.4.5 Desarrollo Continuo de Seguridad

**Pipeline de CI/CD con Verificaciones de Seguridad**
```yaml
# .github/workflows/security-check.yml
name: Security Check

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  security:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        extensions: mbstring, xml, ctype, iconv, intl, pdo_sqlite, dom, filter, gd, iconv, json, mbstring, openssl, pdo, phar, tokenizer, xml, zip
        
    - name: Install dependencies
      run: composer install --prefer-dist --no-progress
        
    - name: Run PHPStan
      run: vendor/bin/phpstan analyse app config --level=8
        
    - name: Run PHP_CodeSniffer
      run: vendor/bin/phpcs app config --standard=PSR12
        
    - name: Run PHPMD
      run: vendor/bin/phpmd app text cleancode,codesize,controversial,design,naming,unusedcode
        
    - name: Check for vulnerable dependencies
      run: composer audit
        
    - name: Run security tests
      run: php artisan test --filter=SecurityTest
```

**Tests de Seguridad Automatizados:**
```php
// tests/Feature/SecurityTest.php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_sql_injection_prevention()
    {
        $response = $this->post('/login', [
            'email' => "'; DROP TABLE users; --",
            'password' => 'password'
        ]);
        
        $this->assertDatabaseHas('users', ['id' => 1]);
    }

    public function test_xss_prevention()
    {
        $response = $this->post('/dashboard/ai/send', [
            'message' => '<script>alert("XSS")</script>'
        ]);
        
        $this->assertStringNotContainsString('<script>', $response->getContent());
    }

    public function test_csrf_protection()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);
        
        $this->assertTrue($response->status() === 419 || $response->status() === 200);
    }

    public function test_rate_limiting()
    {
        for ($i = 0; $i < 100; $i++) {
            $response = $this->post('/dashboard/ai/send', [
                'message' => 'test message'
            ]);
        }
        
        $this->assertTrue($response->status() === 429);
    }
}
```

##### 6.3.4.6 Monitoreo de Amenazas y Actualizaciones

**Sistema de Alertas de Seguridad**
```php
// app/Console/Commands/SecurityMonitorCommand.php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SecurityMonitorCommand extends Command
{
    protected $signature = 'security:monitor';
    protected $description = 'Monitorear amenazas de seguridad y actualizaciones';

    public function handle()
    {
        $this->checkLaravelUpdates();
        $this->checkDependencyVulnerabilities();
        $this->checkSecurityAdvisories();
        $this->analyzeLogs();
        
        $this->info('Monitoreo de seguridad completado');
    }

    private function checkLaravelUpdates()
    {
        try {
            $response = Http::get('https://packagist.org/packages/laravel/framework.json');
            $data = $response->json();
            
            $currentVersion = app()->version();
            $latestVersion = $data['package']['versions']['dev-master']['version'];
            
            if (version_compare($currentVersion, $latestVersion, '<')) {
                Log::warning('Nueva versión de Laravel disponible', [
                    'current' => $currentVersion,
                    'latest' => $latestVersion
                ]);
                
                $this->sendSecurityAlert('Nueva versión de Laravel disponible', [
                    'current' => $currentVersion,
                    'latest' => $latestVersion
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error verificando actualizaciones de Laravel', ['error' => $e->getMessage()]);
        }
    }

    private function checkDependencyVulnerabilities()
    {
        $output = shell_exec('cd ' . base_path() . ' && composer audit --format=json');
        $vulnerabilities = json_decode($output, true);
        
        if (!empty($vulnerabilities['advisories'])) {
            Log::alert('Vulnerabilidades detectadas en dependencias', $vulnerabilities);
            
            $this->sendSecurityAlert('Vulnerabilidades en dependencias', $vulnerabilities);
        }
    }

    private function checkSecurityAdvisories()
    {
        $advisories = [
            'https://github.com/laravel/framework/security/advisories',
            'https://github.com/livewire/livewire/security/advisories',
            'https://github.com/filamentphp/filament/security/advisories'
        ];
        
        foreach ($advisories as $advisory) {
            try {
                $response = Http::get($advisory);
                if ($response->successful()) {
                    // Analizar contenido para nuevas amenazas
                    $this->parseSecurityAdvisory($response->body());
                }
            } catch (\Exception $e) {
                Log::error('Error verificando advisory', ['url' => $advisory, 'error' => $e->getMessage()]);
            }
        }
    }

    private function analyzeLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        $suspiciousPatterns = [
            'sql injection' => '/\b(union|select|insert|update|delete|drop|create|alter)\b/i',
            'xss attempt' => '/<script|javascript:|vbscript:|onload=|onerror=/i',
            'path traversal' => '/\.\.\/|\.\.\\|%2e%2e/i',
            'command injection' => '/\b(system|exec|shell_exec|passthru|eval)\b/i'
        ];
        
        foreach ($suspiciousPatterns as $threat => $pattern) {
            $matches = shell_exec("grep -c '$pattern' $logFile 2>/dev/null");
            if ($matches > 0) {
                Log::alert("Patrón sospechoso detectado: $threat", ['count' => $matches]);
            }
        }
    }

    private function sendSecurityAlert($title, $data)
    {
        // Enviar alerta por email
        \Mail::raw("Alerta de Seguridad: $title\n\n" . json_encode($data, JSON_PRETTY_PRINT), function($message) use ($title) {
            $message->to(config('mail.admin_address'))
                    ->subject("Alerta de Seguridad SAES: $title");
        });
        
        // Enviar notificación a Slack/Discord si está configurado
        if (config('services.slack.webhook_url')) {
            Http::post(config('services.slack.webhook_url'), [
                'text' => "🚨 Alerta de Seguridad: $title\n" . json_encode($data, JSON_PRETTY_PRINT)
            ]);
        }
    }
}
```

**Programación del Monitoreo:**
```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Monitoreo de seguridad cada hora
    $schedule->command('security:monitor')->hourly();
    
    // Análisis de logs cada 6 horas
    $schedule->command('security:analyze-logs')->everyFourHours();
    
    // Verificación de integridad de archivos diariamente
    $schedule->command('security:file-integrity')->daily();
}
```

##### 6.3.4.7 Recomendaciones de Implementación Prioritaria

**Fase 1 (Inmediata - 1-2 semanas)**
1. Implementar rate limiting en endpoints críticos
2. Configurar ModSecurity con reglas básicas
3. Implementar honeypots en formularios de login
4. Configurar alertas de seguridad básicas

**Fase 2 (Corto plazo - 1 mes)**
1. Implementar análisis estático de código
2. Configurar OSSEC para monitoreo de logs
3. Implementar tests de seguridad automatizados
4. Configurar pipeline de CI/CD con verificaciones

**Fase 3 (Mediano plazo - 2-3 meses)**
1. Implementar SIEM completo
2. Desarrollar dashboard de seguridad
3. Implementar respuesta automática a incidentes
4. Configurar monitoreo de amenazas en tiempo real

**Fase 4 (Largo plazo - 6 meses)**
1. Implementar machine learning para detección de amenazas
2. Desarrollar sistema de respuesta a incidentes automatizado
3. Implementar auditoría de seguridad continua
4. Configurar integración con servicios de inteligencia de amenazas

---

## 7. Troubleshooting

### 7.1 Problemas de Rendimiento

#### 7.1.1 Lenta Respuesta del Sistema
```bash
# Verificar uso de memoria
free -h
htop

# Verificar uso de CPU
top
iostat

# Verificar espacio en disco
df -h
du -sh /var/www/saes/storage/*
```

#### 7.1.2 Problemas de Base de Datos
```bash
# Verificar conexiones activas
mysql -u root -p -e "SHOW PROCESSLIST;"

# Verificar estado de tablas
php artisan tinker
DB::select('SHOW TABLE STATUS');

# Optimizar consultas lentas
php artisan tinker
DB::enableQueryLog();
// Ejecutar operación
DB::getQueryLog();
```

### 7.2 Problemas de Caché

#### 7.2.1 Caché no Funciona
```bash
# Verificar estado de Redis
redis-cli ping
redis-cli info

# Limpiar caché manualmente
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Verificar configuración de caché
php artisan config:show cache
```

### 7.3 Problemas de Colas

#### 7.3.1 Jobs No Se Procesan
```bash
# Verificar estado de supervisor
sudo supervisorctl status

# Reiniciar workers
sudo supervisorctl restart saes-worker:*

# Verificar logs de workers
tail -f /var/www/saes/storage/logs/worker.log

# Procesar colas manualmente
php artisan queue:work --once
```

---

## 8. Apéndices

### 8.1 Comandos Artisan Útiles
```bash
# Información del sistema
php artisan about

# Listar rutas
php artisan route:list

# Verificar estado de migraciones
php artisan migrate:status

# Ejecutar seeders
php artisan db:seed

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Crear usuario administrador
php artisan make:user

# Generar clave de aplicación
php artisan key:generate

# Verificar estado del sistema
php artisan system:check
```

### 8.2 Variables de Entorno Importantes
```env
# Aplicación
APP_NAME="SAES - Sistema de Auditorías"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

# Base de Datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=saes_db
DB_USERNAME=saes_user
DB_PASSWORD=password_seguro

# Caché
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Colas
QUEUE_CONNECTION=database
QUEUE_FAILED_DRIVER=database-uuids

# Correo
MAIL_MAILER=smtp
MAIL_HOST=smtp.tudominio.com
MAIL_PORT=587
MAIL_USERNAME=noreply@tudominio.com
MAIL_PASSWORD=password_correo
MAIL_ENCRYPTION=tls

# IA (Claude)
CLAUDE_API=sk-ant-api03-...
CLAUDE_MODEL=claude-3-5-haiku-20241022

# Logs
LOG_CHANNEL=stack
LOG_LEVEL=error
```

### 8.3 Estructura de Archivos de Log
```
storage/logs/
├── laravel.log          # Log principal de la aplicación
├── worker.log           # Log de workers de colas
├── queue.log            # Log de colas
└── daily/               # Logs rotados diariamente
    ├── laravel-2024-01-01.log
    └── laravel-2024-01-02.log
```

### 8.4 Contactos de Soporte
- **Desarrollador Principal**: [Nombre] - [Email]
- **Administrador de Sistemas**: [Nombre] - [Email]
- **DBA**: [Nombre] - [Email]
- **Soporte Técnico**: [Email] - [Teléfono]

---

## Conclusión

Este manual técnico proporciona una guía completa para la instalación, configuración, mantenimiento y seguridad del Sistema SAES. Es importante revisar y actualizar este documento regularmente para reflejar cambios en el sistema y nuevas funcionalidades.

Para cualquier consulta adicional o soporte técnico, contacte al equipo de desarrollo o al administrador del sistema.

---

**Versión del Manual**: 1.0  
**Fecha de Última Actualización**: [Fecha]  
**Responsable**: [Nombre del Responsable]  
**Revisado por**: [Nombre del Revisor]
