<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AvatarController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();

        // Eliminar avatar anterior si existe
        if ($user->avatar) {
            $oldPath = 'avatars/' . $user->avatar;
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        // Guardar nuevo avatar con nombre único
        $file = $request->file('avatar');
        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        // Guardar en storage/app/public/avatars/
        $path = $file->storeAs('avatars', $filename, 'public');

        if (!$path) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la imagen'
            ], 500);
        }

        // Actualizar el campo avatar en la base de datos
        $user->avatar = $filename;
        $user->save();

        return response()->json([
            'success' => true,
            'avatar_url' => $user->avatar_url,
            'message' => 'Foto de perfil actualizada'
        ]);
    }

    public function remove()
    {
        $user = auth()->user();

        if ($user->avatar) {
            $oldPath = 'avatars/' . $user->avatar;
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $user->avatar = null;
        $user->save();

        return response()->json([
            'success' => true,
            'avatar_url' => $user->avatar_url,
            'message' => 'Foto de perfil eliminada'
        ]);
    }
}