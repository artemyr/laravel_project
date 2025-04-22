<!-- Filter item -->
<div>
    <h5 class="mb-4 text-sm 2xl:text-md font-bold">{{ $filter->title() }}</h5>

    @foreach($filter->values() as $id => $label)
        <div class="form-checkbox">
            <input
                name="{{ $filter->name($id) }}"
                value="{{ $id }}"
                @checked($filter->requestValue($id))
                type="checkbox"
                id="{{ $filter->id($id) }}"
            >
            <label for="{{ $filter->name($id) }}" class="form-checkbox-label">
                {{ $label }}
            </label>
        </div>
    @endforeach
</div>
