<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\File;

class FileManagerController extends Controller
{
    // Fungsi untuk memvalidasi path agar tetap di dalam extract_path
    private function getSecurePath(Request $request, $manualPath = null)
    {
        // Cek project_id di Query String (GET) atau di Input Form (POST)
        $projectId = $request->get('project_id') ?? $request->input('project_id');
        
        if (auth()->user()->usertype == 'admin') {
            if (!$projectId) {
                abort(403, 'Akses Ditolak: ID Proyek diperlukan untuk Admin.');
            }
            $project = Project::where('project_id', $projectId)->firstOrFail();
        } else {
            $project = Project::where('user_id', auth()->id())->firstOrFail();
        }

        $basePath = rtrim($project->extract_path, '/');
        $subPath = $manualPath ?? $request->get('path', '');
        
        // Gunakan ltrim agar tidak ada double slash //
        $targetPath = $basePath . '/' . ltrim($subPath, '/');
        $fullPath = realpath($targetPath);

        // Validasi Keamanan
        if (!$fullPath || strpos($fullPath, realpath($basePath)) !== 0) {
            // Jika file baru/masalah symlink, kita validasi string manual
            if (str_contains($subPath, '..')) {
                abort(403, 'Akses Ditolak!');
            }
            $fullPath = $targetPath;
        }

        return [
            'full' => $fullPath,
            'relative' => $subPath,
            'project_id' => $project->project_id
        ];
    }

    public function index(Request $request)
    {
        $pathData = $this->getSecurePath($request);
        $fullPath = $pathData['full'];

        // Ambil direktori
        $directories = File::directories($fullPath);
        
        // PERBAIKAN: Tambahkan parameter true untuk menampilkan hidden files seperti .env
        $files = File::files($fullPath, true); 

        return view('file-manager.index', [
            'directories' => $directories,
            'files' => $files,
            'currentPath' => $pathData['relative'],
            'projectId' => $pathData['project_id']
        ]);
    }

    public function edit(Request $request, $path) // Tambahkan parameter $path di sini
    {

        // Decode path dari Base64 kembali ke teks asli
        $decodedPath = base64_decode($path);

        // Gunakan $path yang ditangkap dari URL
        $pathData = $this->getSecurePath($request, $decodedPath); 
        $filePath = $pathData['full'];

        if (!File::exists($filePath) || File::isDirectory($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        $content = File::get($filePath);
        
        return view('file-manager.edit', [
            'content' => $content,
            'fileName' => basename($filePath),
            'relativePath' => $path, // Menggunakan path dari parameter
            'projectId' => $pathData['project_id']
        ]);
    }

    public function save(Request $request)
    {
        $path = $request->input('path');

        // 1. Decode path jika dalam bentuk Base64
        if (base64_encode(base64_decode($path, true)) === $path) {
            $path = base64_decode($path);
        }

        // 2. Ambil data path yang aman
        $pathData = $this->getSecurePath($request, $path);
        $fullPath = $pathData['full'];
        
        // 3. Tentukan folder induk untuk redirect (Agar bisa "kembali")
        $parentDir = dirname($path);
        $parentDir = ($parentDir == '.' || $parentDir == '/') ? '' : $parentDir;

        try {
            // Berikan izin tulis sebelum menyimpan
            shell_exec("sudo chmod 664 " . escapeshellarg($fullPath));

            // Tulis perubahan ke file
            File::put($fullPath, $request->input('content'));

            // Log Aktivitas
            \App\Models\ActivityLog::create([
                'user_id' => auth()->user()->user_id,
                'activity' => 'Edit File',
                'description' => auth()->user()->name . " mengedit file: " . $path
            ]);

            // REDIRECT: Ini yang akan menutup editor dan kembali ke daftar file
            return redirect()->route('filemanager.index', [
                'project_id' => $pathData['project_id'],
                'path' => $parentDir
            ])->with('success', "File berhasil disimpan.");

        } catch (\Exception $e) {
            // Jika ada error (misal chmod gagal), akan muncul pesan di halaman edit
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}
