<div>
    @if ($getState())
        <div class="grid place-items-center">
            <div class="flag-icon">{!! $getRecord()->country_flag !!}</div>
            <small class="">{{ $getRecord()->countryName() }}</small>
        </div>
    @else
        <span>-</span>
    @endif
</div>
