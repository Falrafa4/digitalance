@props([
    'columns' => 5,
    'rows' => 5,
])

<div class="space-y-3">
    @for($i = 0; $i < $rows; $i++)
        <div class="flex items-center gap-4 p-4 bg-white border border-slate-100 rounded-xl">
            @for($j = 0; $j < $columns; $j++)
                <div class="flex-1">
                    @if($j === 0)
                        <div class="flex items-center gap-3">
                            <div class="skeleton w-10 h-10 rounded-lg"></div>
                            <div class="space-y-1.5 flex-1">
                                <div class="skeleton h-4 w-32"></div>
                                <div class="skeleton h-3 w-20"></div>
                            </div>
                        </div>
                    @elseif($j === $columns - 1)
                        <div class="flex justify-end gap-2">
                            <div class="skeleton h-8 w-16 rounded-lg"></div>
                            <div class="skeleton h-8 w-16 rounded-lg"></div>
                        </div>
                    @else
                        <div class="skeleton h-4 w-full max-w-[120px]"></div>
                    @endif
                </div>
            @endfor
        </div>
    @endfor
</div>
