@props([
    'head',
    'body',
    'foot'
])
<style>
    thead tr th {
        white-space: nowrap;
        padding: 9px 18px;
    }
    table{
        min-width: 100%;
    }
    .overflow-x-auto{
        max-width: 100%;
        overflow-x: auto !important;
    }
    .min-w-full{
        min-width: 100%;
    }
</style>
<div class="overflow-hidden border border-gray-200 rounded-lg shadow-lg">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            @if(isset($head))
            <thead class="bg-gray-800 text-white">
                <tr class="text-center">
                    {{ $head }}
                </tr>
            </thead>
            @endif

            @if(isset($body))
            <tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-700 text-center">
                {{ $body }}
            </tbody>
            @endif

            @if(isset($foot))
            <tfoot class="bg-gray-50 text-gray-700 text-center">
                <tr>
                    {{ $foot }}
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>



