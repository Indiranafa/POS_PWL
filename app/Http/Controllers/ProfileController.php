<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $activeMenu = 'profile';
        $breadcrumb = (object)[
            'title' => 'Profile',
            'list' => ['Home', 'Profile']
        ];

        return view('profile.index', compact('user', 'activeMenu', 'breadcrumb'));
    }

    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = auth()->user();

        // Hapus foto lama jika ada
        if ($user->foto && Storage::exists("public/foto/{$user->foto}")) {
            Storage::delete("public/foto/{$user->foto}");
        }

        // Simpan foto baru
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('public/foto');
            $namaFile = basename($path);

            $user->foto = $namaFile;
            $user->save();

            return back()->with('success', 'Foto berhasil diubah');
        }

        return back()->with('error', 'Foto gagal diunggah');
    }
}
