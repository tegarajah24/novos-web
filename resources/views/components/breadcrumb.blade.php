<div class="breadcrumbs text-sm">
    <ul>
        @foreach($items as $item)
            @if($loop->last)
                <li class="font-semibold">{{ $item['label'] }}</li>
            @else
                <li><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
            @endif
        @endforeach
    </ul>
</div>