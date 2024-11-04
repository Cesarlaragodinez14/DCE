<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validar y actualizar la información de perfil del usuario dado.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        // Construir las reglas de validación
        $rules = [
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo'           => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'firma_autografa' => ['nullable', 'image', 'mimes:png', 'max:2048'],
            'puesto'          => ['nullable', 'string', 'max:255'],
        ];

        // Si el usuario tiene el rol 'Jefe de departamento', hacer que 'firma_autografa' y 'puesto' sean obligatorios
        if ($user->hasRole('Jefe de departamento')) {
            $rules['firma_autografa'][0] = 'required'; // Cambiar 'nullable' a 'required'
            $rules['puesto'][0] = 'required'; // Cambiar 'nullable' a 'required'
        }

        // Validar los datos
        Validator::make($input, $rules)->validateWithBag('updateProfileInformation');

        // Manejar la carga de la foto de perfil
        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        // Preparar los datos para actualizar
        $dataToUpdate = [
            'name'  => $input['name'],
            'email' => $input['email'],
            'puesto' => $input['puesto'] ?? $user->puesto,
        ];

        // Manejar la carga de la firma autógrafa
        if (request()->hasFile('firma_autografa')) {
            $firmaFile = request()->file('firma_autografa');
            $firmaPath = $firmaFile->store('firmas', 'public');
            $dataToUpdate['firma_autografa'] = $firmaPath;
        }

        // Actualizar el usuario
        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $dataToUpdate);
        } else {
            $user->forceFill($dataToUpdate)->save();
        }
    }

    /**
     * Actualizar la información de perfil del usuario verificado dado.
     *
     * @param  array<string, mixed>  $dataToUpdate
     */
    protected function updateVerifiedUser(User $user, array $dataToUpdate): void
    {
        $dataToUpdate['email_verified_at'] = null;

        $user->forceFill($dataToUpdate)->save();

        $user->sendEmailVerificationNotification();
    }
}
