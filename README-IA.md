# Configuración del Asistente de IA

El sistema SAES incluye un asistente de IA que utiliza Groq como proveedor de IA. Este documento explica cómo configurarlo.

## Variables de Entorno Principales

Añade las siguientes variables a tu archivo `.env`:

```env
GROQ_API_KEY=tu_clave_api_aqui          # Clave API de Groq (requerida)
GROQ_DEF_MODEL=llama3-8b-8192           # Modelo predeterminado de Groq
```

## Configuración de Groq

Para utilizar Groq, necesitas obtener una API key de Groq (https://console.groq.com) y añadirla a tu archivo `.env`:

```env
GROQ_API_KEY=tu_clave_api_aquí
GROQ_DEF_MODEL=llama3-8b-8192  # Modelo predeterminado de Groq
```

### Modelos disponibles en Groq:
- `llama3-8b-8192` - Llama 3 8B (recomendado para la mayoría de casos)
- `llama3-70b-8192` - Llama 3 70B (para tareas más complejas)
- `mixtral-8x7b-32768` - Mixtral 8x7B
- `gemma-7b-it` - Gemma 7B
- `gemma2-9b-it` - Gemma 2 9B

## Uso

Una vez configurado, el asistente de IA estará disponible en:
- Dashboard principal → Asistente IA
- Generación automática de etiquetas para auditorías
- Resúmenes ejecutivos para tarjetas informativas

## Solución de Problemas

### Error: La clave API de Groq no está configurada
- Verifica que `GROQ_API_KEY` esté en tu archivo `.env`
- Asegúrate de haber ejecutado `php artisan config:cache` después de añadir la variable

### Respuestas lentas o errores de timeout
- Considera usar un modelo más pequeño como `llama3-8b-8192`
- Verifica tu conexión a internet

### Rate limits
- Groq tiene límites de uso. Si los excedes, espera unos minutos antes de hacer más solicitudes
- Para uso intensivo, considera obtener una cuenta con límites más altos

## Funcionalidades Principales

1. **Chat Interactivo**: Conversaciones con contexto del sistema SAES
2. **Generación de Etiquetas**: Categorización automática de observaciones de auditoría
3. **Resúmenes Ejecutivos**: Generación automática de resúmenes para tarjetas informativas
4. **Análisis de Datos**: Consultas sobre estadísticas y métricas del sistema 