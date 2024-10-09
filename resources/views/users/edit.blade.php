<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-center text-gray-800">{{ __('Editar Usuario') }}</h2>
        <!-- Grid de Información del Usuario -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
            <!-- Información del Usuario -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Datos del Usuario</h3>
                <ul class="space-y-2 text-gray-700">
                    <li><strong>Nombre:</strong> {{ $user->name }}</li>
                    <li><strong>Email:</strong> {{ $user->email }}</li>
                    <li><strong>Roles Actuales:</strong> 
                        @foreach($user->roles as $role)
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">{{ $role->name }}</span>
                        @endforeach
                    </li>
                </ul>
            </div>

            <!-- Actualización del Usuario -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Actualizar Información</h3>
                <form method="POST" action="{{ route('users.update', $user->id) }}" id="user-form">
                    @csrf
                    @method('PUT')
                    
                    <!-- Nombre -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                               class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                               @if(!$isAdmin) disabled @endif>
                        @error('name')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                               class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                               @if(!$isAdmin) disabled @endif>
                        @error('email')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="uaa_id" class="block text-sm font-medium text-gray-700">UAA</label>
                        <select id="uaa_id" name="uaa_id" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Ninguna</option>
                            @foreach($uaas as $uaa)
                                <option value="{{ $uaa->id }}" {{ $user->uaa_id == $uaa->id ? 'selected' : '' }}>{{ $uaa->valor }}</option>
                            @endforeach
                        </select>
                        @error('uaa_id')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>
                    

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Contraseña (opcional)</label>
                        <input type="password" id="password" name="password" autocomplete="new-password" 
                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        @error('password')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>


                    <!-- Confirmar Password -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" 
                               class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                               @if(!$isAdmin) disabled @endif>
                    </div>

                    <!-- Roles (solo admin puede ver y modificar roles) -->
                    @if($isAdmin)
                        <div class="mb-8">
                            <label for="roles" class="block text-lg font-semibold text-gray-900 mb-2">Asignar Roles</label>
                            <div class="bg-white shadow rounded-lg p-4">
                                <p class="text-sm text-gray-600 mb-4">Selecciona los roles que deseas asignar al usuario:</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                                    @foreach($roles as $role)
                                        <div class="flex items-center bg-gray-50 border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-300 ease-in-out">
                                            <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                                {{ in_array($role->id, $user->roles->pluck('id')->toArray()) ? 'checked' : '' }} 
                                                class="form-checkbox h-5 w-5 text-indigo-600 transition-colors duration-200 ease-in-out">
                                            <label class="ml-3 text-gray-700 font-medium text-sm">
                                                {{ $role->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('roles')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif


                    <div class="flex items-center justify-between" style="margin-top: 20px">
                        <x-ui.button type="button" id="confirm-update">Actualizar</x-ui.button>
                    
                        <a href="{{ route('users.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </x-slot>

    <!-- Modal de Confirmación -->
    <div id="confirmation-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <!-- Fondo -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Contenido del Modal -->
            <div class="inline-block overflow-hidden text-left align-middle transition-all transform bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="px-6 py-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                        Confirmación
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            ¿Está seguro que desea actualizar la información del usuario?
                        </p>
                    </div>
                </div>
                <div class="px-6 py-3 sm:flex sm:flex-row-reverse">
                    <button id="confirm-save" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirmar
                    </button>
                    <button id="cancel-save" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const confirmButton = document.getElementById('confirm-update');
            const modal = document.getElementById('confirmation-modal');
            const form = document.getElementById('user-form');
            const confirmSave = document.getElementById('confirm-save');
            const cancelSave = document.getElementById('cancel-save');

            confirmButton.addEventListener('click', (e) => {
                modal.classList.remove('hidden');
            });

            confirmSave.addEventListener('click', () => {
                form.submit();
            });

            cancelSave.addEventListener('click', () => {
                modal.classList.add('hidden');
            });
        });
    </script>
</x-app-layout>
