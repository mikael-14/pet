    <div id="qr-code-view">
        <div class="flex justify-between">
        </div>
        <div class="text-center">
            @if ($text_save)
            <x-tabler-qrcode class="w-32 h-32 mx-auto" style="opacity: 0.5" />
            <span>{{$text_save}}</span>
            @else
            @if ($qrcode)
            <img class="mx-auto" src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl={{ env('APP_URL') . '/' . $qrcode  }}&choe=UTF-8" alt="QR Code" />
            <span class="ml-8"><a href="{{ ENV('APP_URL') . '/' . $qrcode }}">Url</a></span>
            @endif
            @if ($text_empty)
            <x-tabler-qrcode-off class="w-32 h-32 mx-auto" style="opacity: 0.3" />
            <span>{{$text_empty}}</span>
            @endif
            @endif
        </div>
    </div>