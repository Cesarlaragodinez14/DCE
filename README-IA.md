# Configuración de Proveedores de IA en SAES

El sistema SAES incluye un asistente de IA que puede utilizar diferentes proveedores (Anthropic Claude, OpenAI GPT y Google Gemini). Este documento explica cómo configurar cada uno de ellos.

## Configuración General

En el archivo `.env` se deben configurar las siguientes variables generales:

```
DEFAULT_AI_PROVIDER=openai     # Proveedor predeterminado (anthropic, openai, gemini)
DEFAULT_AI_MODEL=gpt-4.1-nano  # Modelo predeterminado
```

## Configuración de Anthropic Claude

Para utilizar Claude, necesitas obtener una API key de Anthropic (https://www.anthropic.com) y añadirla a tu archivo `.env`:

```
CLAUDE_API=tu_clave_api_aquí
CLAUDE_MODEL=claude-3-haiku-20240307  # Modelo predeterminado de Claude
```

Modelos disponibles:
- `claude-3-haiku-20240307` - Modelo más rápido y económico
- `claude-3-sonnet-20240229` - Equilibrio entre rendimiento y costo
- `claude-3-opus-20240229` - Modelo más potente

## Configuración de OpenAI GPT

Para utilizar GPT, necesitas obtener una API key de OpenAI (https://platform.openai.com) y añadirla a tu archivo `.env`:

```
OPENAI_API_KEY=tu_clave_api_aquí
OPENAI_MODEL=gpt-4.1-nano  # Modelo predeterminado de OpenAI
```

Modelos disponibles:
- `gpt-4.1-nano` - Modelo optimizado para velocidad y costo
- `gpt-4-1106-preview` / `gpt-4-0613` - Modelos de la familia GPT-4
- `gpt-3.5-turbo` - Modelo más económico

## Configuración de Google Gemini

Para utilizar Gemini, necesitas obtener una API key de Google AI Studio (https://makersuite.google.com) y añadirla a tu archivo `.env`:

```
GEMINI_API_KEY=tu_clave_api_aquí
GEMINI_MODEL=gemini-pro  # Modelo predeterminado de Gemini
```

Modelos disponibles:
- `gemini-pro` - Modelo estándar de Gemini
- `gemini-ultra` - Modelo más avanzado (si está disponible)

## Cambio de Proveedor en Tiempo Real

El asistente de IA permite cambiar entre proveedores y modelos en tiempo real desde la interfaz. Selecciona el proveedor y el modelo deseado en los menús desplegables ubicados en la parte inferior del chat.

## Resolución de Problemas

Si encuentras errores al utilizar alguno de los proveedores, verifica:

1. Que la clave API correspondiente esté correctamente configurada en el archivo `.env`
2. Que tengas saldo o cuota disponible en la plataforma del proveedor
3. Que el modelo seleccionado esté disponible para tu cuenta

Los errores detallados se registran en el archivo de log de Laravel (`storage/logs/laravel.log`). 