<div class="space-y-4">
    @foreach($hitos as $fecha => $items)
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-white/5">
            <div class="flex items-center gap-x-3 border-b border-gray-200 px-4 py-2 bg-gray-50/50 dark:border-white/10 dark:bg-white/5">
                <span class="text-sm font-bold text-gray-700 dark:text-gray-200">
                    📅 {{ $fecha }}
                </span>
                <span class="text-xs text-gray-500">
                    ({{ count($items) }} {{ count($items) > 1 ? 'hitos' : 'hito' }})
                </span>
            </div>
            
            <div class="divide-y divide-gray-100 dark:divide-white/5">
                @foreach($items as $hito)
                    <div class="px-4 py-3">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-primary-600 dark:text-primary-400">
                                {{ $hito->titulo_o_categoria }} {{-- Ajustá según tu columna --}}
                            </span>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 italic">
                                "{{ $hito->descripcion }}"
                            </p>
                            @if($hito->usuario)
                                <span class="mt-2 text-[10px] text-gray-400 uppercase tracking-wider">
                                    Cargado por: {{ $hito->usuario->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>