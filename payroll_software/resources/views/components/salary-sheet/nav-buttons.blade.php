{{-- Button group component for section navigation --}}
<div class="mb-4 mt-3">
    <div class="btn-group" role="group">
        @foreach ($buttons as $button)
            <button
                type="button"
                onclick="{{ $button['onclick'] }}"
                class="btn {{ $button['class'] }}"
                {!! isset($button['attributes']) ? $button['attributes'] : '' !!}}
            >
                {!! $button['icon'] !!} {{ $button['label'] }}
            </button>
        @endforeach
    </div>
</div>
