<x-filament::page :widget-data="['record' => $record]" :class="\Illuminate\Support\Arr::toCssClasses([
        'filament-resources-view-record-page',
        'filament-resources-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'filament-resources-record-' . $record->getKey(),
    ])">
    {{-- @php
    $relationManagers = $this->getRelationManagers();
    @endphp

    @if ((! $this->hasCombinedRelationManagerTabsWithForm()) || (! count($relationManagers)))
    {{ $this->form }}
    @endif

    @if (count($relationManagers))
    @if (! $this->hasCombinedRelationManagerTabsWithForm())
    <x-filament::hr />
    @endif

    <x-filament::resources.relation-managers :active-manager="$activeRelationManager" :form-tab-label="$this->getFormTabLabel()" :managers="$relationManagers" :owner-record="$record" :page-class="static::class">
        @if ($this->hasCombinedRelationManagerTabsWithForm())
        <x-slot name="form">
            {{ $this->form }}
        </x-slot>
        @endif
    </x-filament::resources.relation-managers>
    @endif --}}
    <div class="container">
        <div class="flex sticky gap-4">
            <div class="p-4 bg-white rounded-xl dark:bg-gray-800">
                <img class="rounded " src="{{$this->record->getMedia('pets-main-image')[0]->getUrl()}}" alt="Main Image">
                <h1 class="font-bold text-xl my-1">{{$this->data['name']}}</h1>
            </div>
            <div class="p-4 bg-white rounded-xl dark:bg-gray-800">
                {{ var_dump($this->data)}}
            </div>
        </div>
    </div>
</x-filament::page>