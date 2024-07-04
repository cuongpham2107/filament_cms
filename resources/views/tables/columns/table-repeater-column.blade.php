<div >

    @php
        $name = $getRecord()->name;
        $state = $getState() ?? [];
    @endphp
    <div class="flex flex-col space-y-1 my-2">
      
        @foreach ($state as $item)
        <div >
            @if (isset($item['type_ralation']))
                <p style=" 
                        background-color: rgb(174 173 178 / 10%);
                        color: rgb(83 83 83);
                        border: 1px solid rgb(185 185 185);
                    "
                    class="rounded-md px-2 py-1 text-xs font-semibold ">
                    {{ formatRelationshipMethod($item['type_ralation']) }} {{ $item['name'] }} as sole
                    {{ $name }}
                </p>
            @else
                <p style="
                        @if ($item['name'] === 'id') background-color: rgb(84 224 75 / 10%);
                            color: rgb(144 178 44);
                            border: 1px solid rgb(185 185 185);
                        
                        @elseif($item['name'] === 'created_at' || $item['name'] === 'updated_at' || $item['name'] === 'deleted_at')
                            background-color: rgb(174 173 178 / 10%);
                            color: rgb(83 83 83);
                            border: 1px solid rgb(185 185 185);
                        @else
                            background-color: rgb(124 58 237 / 10%);
                            color: rgb(124 58 237);
                            border: 1px solid rgb(185 185 185); @endif
                    "
                    class="rounded-md px-2 py-1 text-xs font-semibold ">
                    {{ $item['name'] }}</p>
            @endif
        </div>
            
        @endforeach
    </div>
</div>
