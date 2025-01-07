<div style="background: #fff; border-radius: 20px; padding: 25px">
<!-- Encabezado del Formulario -->
<div class="mb-6">
    <h2 class="text-lg font-medium text-gray-900">
        {{ __('Información del Perfil') }}
    </h2>
    <p class="mt-1 text-sm text-gray-600">
        {{ __('Actualiza la información básica de tu perfil.') }}
    </p>
</div>

<form method="POST" action="{{ route('user-profile-information.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <!-- Contenedor del Formulario -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Foto de Perfil -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div class="md:col-span-2">
                <x-label for="photo" value="{{ __('Foto de Perfil') }}" />
                <div x-data="{ photoName: null, photoPreview: null }" class="flex items-center mt-2">
                    <!-- Foto Actual -->
                    <div class="shrink-0 mr-4">
                        <img x-show="!photoPreview" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="h-20 w-20 rounded-full object-cover">
                        <img x-show="photoPreview" x-bind:src="photoPreview" class="h-20 w-20 rounded-full object-cover">
                    </div>

                    <!-- Botones de Acción -->
                    <div>
                        <input type="file" class="hidden"
                               name="photo"
                               x-ref="photo"
                               x-on:change="
                                   photoName = $refs.photo.files[0].name;
                                   const reader = new FileReader();
                                   reader.onload = (e) => {
                                       photoPreview = e.target.result;
                                   };
                                   reader.readAsDataURL($refs.photo.files[0]);
                               " />

                        <x-button type="button" class="mr-2" x-on:click.prevent="$refs.photo.click()">
                            {{ __('Seleccionar una nueva foto') }}
                        </x-button>

                        @if (Auth::user()->profile_photo_path)
                            <x-secondary-button type="button" onclick="event.preventDefault(); document.getElementById('remove-photo-form').submit();">
                                {{ __('Eliminar foto') }}
                            </x-secondary-button>
                        @endif

                        <x-input-error for="photo" class="mt-2" />
                    </div>
                </div>

                <!-- Formulario para eliminar la foto -->
                @if (Auth::user()->profile_photo_path)
                    <form method="POST" action="{{ route('current-user-photo.destroy') }}" id="remove-photo-form">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif
            </div>
        @endif

        <!-- Nombre Completo -->
        <div>
            <x-label for="name" value="{{ __('Nombre Completo') }}" />
            <x-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', Auth::user()->name) }}" required autofocus autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <!-- Puesto -->
        <div>
            <x-label for="puesto" value="{{ __('Puesto') }}" />
            <x-input id="puesto" name="puesto" type="text" class="mt-1 block w-full" value="{{ old('puesto', Auth::user()->puesto) }}" autocomplete="puesto" />
            <x-input-error for="puesto" class="mt-2" />
        </div>

        <!-- Correo Electrónico -->
        <div class="md:col-span-2">
            <x-label for="email" value="{{ __('Correo Electrónico') }}" />
            <x-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email', Auth::user()->email) }}" required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! Auth::user()->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-gray-600">
                        {{ __('Tu correo electrónico no está verificado.') }}
                        <button form="send-verification" type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Haz clic aquí para reenviar el correo de verificación.') }}
                        </button>
                    </p>
                    @if (session('status') == 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('Se ha enviado un nuevo enlace de verificación a tu correo electrónico.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

    </div>

    <!-- Botón de Guardar -->
    <div class="flex items-center justify-end mt-6">
        <x-action-message class="mr-3" on="saved">
            {{ __('Guardado.') }}
        </x-action-message>

        <x-button>
            {{ __('Guardar') }}
        </x-button>
    </div>
</form>
</div>