<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @if(is_null($getFileAttachmentUrl()))
        <div class="filament-editorjs">
            <div
                wire:ignore
                {{
                  $attributes
                    ->merge($getExtraAttributes())
                    ->class([
                        'editorjs-wrapper'
                    ])
                }}
                x-data="editorjs({
                state: $wire.entangle('{{ $getStatePath() }}'),
                statePath: '{{ $getStatePath() }}',
                placeholder: '{{ $getPlaceholder() }}',
                readOnly: {{ $isDisabled() ? 'true' : 'false' }},
                tools: @js($getTools()),
                minHeight: @js($getMinHeight())
            })"
            >
            </div>
        </div>
    @else
        <div class="filament-editorjs">
            <div
                wire:ignore
                {{
                  $attributes
                    ->merge($getExtraAttributes())
                    ->class([
                        'editorjs-wrapper'
                    ])
                }}
                x-data="editorjs({
                state: $wire.entangle('{{ $getStatePath() }}'),
                statePath: '{{ $getStatePath() }}',
                placeholder: '{{ $getPlaceholder() }}',
                readOnly: {{ $isDisabled() ? 'true' : 'false' }},
                imageUploadEndpoint: '{{ $getFileAttachmentUrl() }}',
                tools: @js($getTools()),
                minHeight: @js($getMinHeight())
            })"
            >
            </div>
        </div>
    @endif
</x-dynamic-component>
