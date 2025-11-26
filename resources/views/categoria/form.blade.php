<div class="row padding-1 p-1">
    <div class="col-md-12">
        <div class="form-group mb-2 mb20">
            <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $categoria?->nombre) }}" id="nombre" placeholder="Nombre">
            {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
    </div>
    @php
        $opcionesCategoria = collect($categoriasPadre ?? [])->filter()->unique()->sort()->values();
        $categoriaActual = old('categoria', $categoria?->categoria);
        $esPersonalizada = $categoriaActual && !$opcionesCategoria->contains($categoriaActual);
    @endphp
    <div class="col-md-12">
        <div class="form-group mb-2 mb20">
            <label for="categoria_select" class="form-label">{{ __('Categoría') }}</label>
            <input type="hidden" name="categoria" id="categoria_value" value="{{ $categoriaActual }}">
            <select id="categoria_select" class="form-control @error('categoria') is-invalid @enderror">
                <option value="">Selecciona una categoría</option>
                @foreach($opcionesCategoria as $opcion)
                    <option value="{{ $opcion }}" {{ !$esPersonalizada && $categoriaActual === $opcion ? 'selected' : '' }}>{{ $opcion }}</option>
                @endforeach
                <option value="__custom" {{ $esPersonalizada ? 'selected' : '' }}>Nueva categoría…</option>
            </select>
            {!! $errors->first('categoria', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
    </div>
    <div class="col-md-12 {{ $esPersonalizada ? '' : 'd-none' }}" id="categoria_custom_group">
        <div class="form-group mb-2 mb20">
            <label for="categoria_custom_input" class="form-label">{{ __('Nombre de la nueva categoría') }}</label>
            <input type="text" id="categoria_custom_input" class="form-control" value="{{ $esPersonalizada ? $categoriaActual : '' }}" placeholder="Ej: Servicios Comerciales">
            <small class="form-text text-muted">Se guardará automáticamente al enviar el formulario.</small>
        </div>
    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
    </div>
</div>

@once
    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var select = document.getElementById('categoria_select');
                var customGroup = document.getElementById('categoria_custom_group');
                var customInput = document.getElementById('categoria_custom_input');
                var hidden = document.getElementById('categoria_value');

                if (!select || !hidden) {
                    return;
                }

                var syncFromSelect = function () {
                    if (select.value === '__custom') {
                        if (customGroup) {
                            customGroup.classList.remove('d-none');
                        }
                        if (customInput) {
                            hidden.value = customInput.value.trim();
                            customInput.focus();
                        }
                    } else {
                        if (customGroup) {
                            customGroup.classList.add('d-none');
                        }
                        hidden.value = select.value;
                    }
                };

                select.addEventListener('change', syncFromSelect);

                if (customInput) {
                    customInput.addEventListener('input', function () {
                        if (select.value === '__custom') {
                            hidden.value = customInput.value.trim();
                        }
                    });
                }

                syncFromSelect();
            });
        </script>
    @endpush
@endonce