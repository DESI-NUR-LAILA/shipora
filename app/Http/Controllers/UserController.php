<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\HakAkses;
use App\Models\Port;
use App\Models\Ttd;
use App\Exports\UserExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data User',
            'menuUser' => 'active',

            // Ambil user beserta relasi hak_akses dan port, urut berdasarkan email
            'user'  => User::with(['hak_akses', 'port'])
                    ->orderBy('email', 'asc')
                    ->get(),
        ];

        return view('user/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Data User',
            'menuUser' => 'active',
            'hak_akses' => HakAkses::orderBy('nama_hak_akses')->get(),
            'ports' => Port::orderBy('port')->get(),
        ];

        return view('user/create', $data);
    }

    public function store(Request $request)
    {
        // Validasi dulu
        $request->validate([
            'email'     => 'required|email|unique:users,email',
            'hak_akses_id'=> 'required|exists:hak_akses,id',
            'port_id'   => 'required|exists:ports,id',
            'password'  => 'required|confirmed|min:8',
        ], [
            'email.required'   => 'Email tidak boleh kosong',
            'email.email'      => 'Format email tidak valid',
            'email.unique'     => 'Email sudah terdaftar',
            'hak_akses_id.required' => 'Hak Akses harus dipilih',
            'hak_akses_id.exists'   => 'Hak Akses tidak valid',
            'port_id.required'    => 'Lokasi Port harus dipilih',
            'port_id.exists'      => 'Lokasi Port tidak valid',
            'password.required'   => 'Password tidak boleh kosong',
            'password.confirmed'  => 'Konfirmasi password tidak sama',
            'password.min'        => 'Password minimal 8 karakter',
        ]);

        // Ambil nama hak akses
        $hakAkses = HakAkses::find($request->hak_akses_id)->nama_hak_akses;

        // Daftar hak akses yang hanya boleh 1 user dalam sistem
        $globalRoles = ['hoa', 'sekretaris', 'supervisor', 'pic'];

        // Cek apakah hak akses termasuk global dan sudah ada user yang memakainya
        if (in_array(strtolower($hakAkses), $globalRoles)) {
            $exists = User::where('hak_akses_id', $request->hak_akses_id)->exists();

            if ($exists) {
                return back()->with('error', 'Hak akses ini hanya boleh satu user saja.')->withInput();
            }
        } else {
            // Jika bukan global (misal: Admin yaitu 1 user per port)
            $exists = User::where('hak_akses_id', $request->hak_akses_id)
                        ->where('port_id', $request->port_id)
                        ->exists();

            if ($exists) {
                return back()->with('error', 'Hak akses ini sudah memiliki user pada port tersebut.')->withInput();
            }
        }

        User::create([
            'email'     => $request->email,
            'hak_akses_id'=> $request->hak_akses_id,
            'port_id'   => $request->port_id,
            'password'  => Hash::make($request->password),
        ]);

        return redirect()->route('user')->with('success', 'Data user berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Data User',
            'menuUser' => 'active',
            'user' => User::findOrFail($id),
            'hak_akses' => HakAkses::orderBy('nama_hak_akses')->get(),
            'ports' => Port::orderBy('port')->get(),
        ];

        return view('user/edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'email'     => 'required|email|unique:users,email,' .$id,
            'hak_akses_id'=> 'required|exists:hak_akses,id',
            'port_id'   => [
                'required',
                'exists:ports,id',
                Rule::unique('users')->where(function ($query) use ($request, $id) {
                    return $query->where('hak_akses_id', $request->hak_akses_id)
                                ->where('id', '!=', $id);
                }),
            ],
            'password'  => 'nullable|confirmed|min:8',
        ], [
            'email.required'   => 'Email tidak boleh kosong',
            'email.email'      => 'Format email tidak valid',
            'email.unique'     => 'Email sudah terdaftar',
            'hak_akses_id.required' => 'Hak akses harus dipilih',
            'hak_akses_id.exists'   => 'Hak akses tidak valid',
            'port_id.required'    => 'Lokasi Port harus dipilih',
            'port_id.exists'      => 'Lokasi Port tidak valid',
            'port_id.unique' => 'Hak akses ini sudah memiliki user di port tersebut.',
            'password.confirmed'  => 'Konfirmasi password tidak sama',
            'password.min'        => 'Password minimal 8 karakter',
        ]);

        // Ambil user
        $user = User::findOrFail($id);

        // Update semua field 
        $user->email      = $request->email;
        $user->hak_akses_id = $request->hak_akses_id;
        $user->port_id    = $request->port_id;

        // Jika password diisi, update password
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('user')->with('success', 'Data berhasil diperbarui');
    }

    public function excel()
    {
        $filename = now()->format('d-m-Y_H-i-s');
        return Excel::download(new UserExport, 'DataUser_' . $filename . '.xlsx');
    }

    public function pdf($role)
    {
        $filename = now()->format('d-m-Y_H-i-s');

        // Filter user berdasarkan parameter role (hak_akses)
        if ($role === 'all') {
            $users = User::with(['hak_akses', 'port'])->get();
        } else {
            $users = User::with(['hak_akses', 'port'])
                ->whereHas('hak_akses', function ($query) use ($role) {
                    $query->where('nama_hak_akses', ucfirst($role)); // misal 'admin' jadi 'Admin'
                })
                ->get();
        }

        // Ambil hak akses Sekretaris
        $sekretarisHakAkses = HakAkses::where('nama_hak_akses', 'Sekretaris')->first();

        // Cari TTD aktif untuk Sekretaris (isarsip = 0)
        $sekretarisTTD = Ttd::where('hak_akses_id', $sekretarisHakAkses->id ?? null)
            ->where('isarsip', false)
            ->first();

        $data = [
            'user' => $users,
            'tanggal' => now()->format('d-m-Y'),
            'jam' => now()->format('H:i:s'),
            'nama_sekretaris' => $sekretarisTTD->nama ?? '-',
            'ttd_sekretaris' => $sekretarisTTD->ttd_path ?? null,
        ];

        $pdf = Pdf::loadView('user.pdf', $data);
        return $pdf->setPaper('a4', 'portrait')->stream('DataUser_' . $filename . '.pdf');
    }
}