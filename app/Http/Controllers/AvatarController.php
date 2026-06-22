<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();

        // Eliminar avatar anterior si existe
        if ($user->avatar && Storage::disk('public')->exists('avatars/' . $user->avatar)) {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }

        // Guardar nuevo avatar
        $file = $request->file('avatar');
        $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('avatars', $filename, 'public');

        $user->update(['avatar' => $filename]);

        return response()->json([
            'success' => true,
            'avatar_url' => $user->avatar_url,
            'message' => 'Foto de perfil actualizada'
        ]);
    }

    public function remove()
    {
        $user = auth()->user();

        if ($user->avatar && Storage::disk('public')->exists('avatars/' . $user->avatar)) {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }

        $user->update(['avatar' => null]);

        return response()->json([
            'success' => true,
            'avatar_url' => $user->avatar_url,
            'message' => 'Foto de perfil eliminada'
        ]);
    }
}