<div role="alert" class="shout-component border 
rounded-lg p-4 
{{ match($type) {
                'success' => 'bg-success-200 border-success-300 text-success-900 dark:border-success-300 dark:bg-success-200',
                'warning' => 'bg-warning-100 border-warning-300 text-warning-900 dark:border-warning-300 dark:bg-warning-200',
                'danger' => 'bg-danger-100 border-danger-300 text-danger-900 dark:border-danger-300 dark:bg-danger-200',
                default => 'bg-info-100 border-info-300 text-info-900 dark:border-info-300 dark:bg-info-200',
            }
        }}">
    <div class="flex">
        <div class="flex-shrink-0 ltr:mr-3 rtl:ml-3 text-{{$type}}-500">
            @switch($type)
            @case('success')
            <x-heroicon-o-check-circle class="h-5 w-5 shrink-0" />
            @break

            @case('info')
            <x-heroicon-o-information-circle class="h-5 w-5 shrink-0"/>
            @break

            @case('warning')
            <x-heroicon-o-exclamation-circle class="h-5 w-5 shrink-0"/>
            @break

            @case('danger')
            <x-heroicon-o-x-circle class="h-5 w-5 shrink-0"/>
            @break

            @endswitch
        </div>
        <div class="text-sm font-medium">
            {{$content}}
        </div>
    </div>
</div>