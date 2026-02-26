<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use App\Models\ActivityLog;
use ZipArchive;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index(){
        $stats = [
        'total_users'      => User::where('usertype', 'user')->count(),
        'pending_projects' => Project::where('status', 'pending')->count(),
        'active_projects'  => Project::where('status', 'active')->count(),
        'recent_activities'=> ActivityLog::latest()->take(5)->get(), // Ambil 5 aktivitas terakhir
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function projectsIndex()
    {
        // Mengambil data project yang statusnya pending beserta data mahasiswanya
        $projects = \App\Models\Project::with('user')->where('status', 'pending')->get();
        
        return view('admin.projects.daftar_antrian', compact('projects'));
    }

    public function activityLogs(Request $request)
    {
        $search = $request->input('search');

        $logs = ActivityLog::with('user')
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('activity', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    // Mencari berdasarkan nama admin di tabel users
                    ->orWhereHas('user', function($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.projects.activity_logs', compact('logs'));
    }

    public function activeProjects(Request $request)
    {
        $search = $request->input('search');

        $projects = Project::with('user')
        // Tampilkan proyek yang statusnya 'active' ATAU 'suspended'
        ->whereIn('status', ['active', 'suspended']) 
        ->when($search, function ($query, $search) {
            return $query->where(function($q) use ($search) {
                $q->where('project_name', 'like', "%{$search}%")
                ->orWhere('author_name', 'like', "%{$search}%")
                ->orWhere('subdomain', 'like', "%{$search}%");
            });
        })
        ->latest()
        ->paginate(15)
        ->withQueryString();

        return view('admin.projects.project_active', compact('projects'));
    }

    public function userIndex(Request $request)
    {
        $search = $request->input('search');

        $users = User::where('usertype', 'user')
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15) // Gunakan paginate agar mendukung banyak data
            ->withQueryString();

        return view('admin.projects.daftar_user', compact('users'));
    }

    public function userStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // Simpan ke database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'usertype' => 'user',
        ]);

        // --- CATAT LOG: TAMBAH USER ---
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'Tambah Mahasiswa',
            'description' => auth()->user()->name . " telah mendaftarkan mahasiswa baru: " . $user->name . " (" . $user->email . ")"
        ]);

        return back()->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function userDestroy($id)
    {
        $user = User::findOrFail($id);
        $namaMahasiswa = $user->name;
        $emailMahasiswa = $user->email;

        // --- CATAT LOG: HAPUS USER (Sebelum dihapus) ---
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'Hapus Mahasiswa',
            'description' => auth()->user()->name . " telah menghapus akun mahasiswa: " . $namaMahasiswa . " (" . $emailMahasiswa . ")"
        ]);

        $user->delete();

        return back()->with('success', 'Akun Mahasiswa berhasil dihapus.');
    }

    public function destroyProject($id)
    {
        $project = Project::findOrFail($id);
        $domain = $project->subdomain;
        $authorName = $project->author_name ?? 'Alumni (Akun Dihapus)';

        try {
            // --- 1. CATAT LOG AKTIVITAS (Sebelum Data Dihapus) ---
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(), // ID Admin yang sedang login
                'activity' => 'Hapus Proyek',
                'description' => auth()->user()->name . " telah menghapus permanen proyek: " . $project->project_name . " milik: " . $authorName
            ]);

            // --- 2. Hapus File Fisik di Storage ---
            if ($project->file_path && \Storage::disk('local')->exists($project->file_path)) {
                \Storage::disk('local')->delete($project->file_path);
            }

            if ($project->sql_path && \Storage::disk('local')->exists($project->sql_path)) {
                \Storage::disk('local')->delete($project->sql_path);
            }

            // --- 3. Bersihkan Infrastruktur Ubuntu ---
            if ($project->status === 'active') {

                // KHUSUS FLASK: Hapus Systemd Service
                if ($project->project_type === 'Flask') {
                    $subName = explode('.', $domain)[0];
                    $serviceName = "flask_" . $subName;

                    // Bersihkan Cache Pip: agar download ulang jika deploy lagi)
                    // Kita gunakan venv proyek tersebut untuk menjalankan perintah purge
                    if (File::exists($project->extract_path . "/venv/bin/pip")) {
                        shell_exec("sudo " . $project->extract_path . "/venv/bin/pip cache purge");
                    }

                    // Stop & Disable Service
                    shell_exec("sudo systemctl stop $serviceName");
                    shell_exec("sudo systemctl disable $serviceName");
                    
                    // Hapus file unit service
                    shell_exec("sudo rm -f /etc/systemd/system/$serviceName.service");
                    
                    // Refresh Systemd agar tidak ada service "ghost"
                    shell_exec("sudo systemctl daemon-reload");
                }

                // Hapus folder di /var/www/
                if ($project->extract_path && \File::exists($project->extract_path)) {
                    shell_exec("sudo rm -rf " . escapeshellarg($project->extract_path));
                }

                // Hapus Config Nginx
                shell_exec("sudo rm -f " . escapeshellarg("/etc/nginx/sites-available/{$domain}.conf"));
                shell_exec("sudo rm -f " . escapeshellarg("/etc/nginx/sites-enabled/{$domain}.conf"));

                // --- TAMBAHAN: Hapus File Log Error & Access ---
                // Menggunakan wildcard (*) untuk memastikan jika ada log rotasi (seperti .log.1) juga ikut terhapus
                $accessLogPath = "/var/log/nginx/{$domain}.access.log";
                $errorLogPath = "/var/log/nginx/{$domain}.error.log";

                shell_exec("sudo rm -f " . escapeshellarg($accessLogPath) . "*");
                shell_exec("sudo rm -f " . escapeshellarg($errorLogPath) . "*");

                shell_exec("sudo systemctl reload nginx");

                // --- 4. Hapus Database & User MySQL ---
                if ($project->db_name) {
                    \DB::statement("DROP DATABASE IF EXISTS `{$project->db_name}`");
                    if ($project->db_user) {
                        \DB::statement("DROP USER IF EXISTS '{$project->db_user}'@'localhost'");
                    }
                    \DB::statement("FLUSH PRIVILEGES");
                }
            }

            // --- 5. Hapus Record di Database ---
            $project->delete();

            return back()->with('success', "Proyek milik $authorName berhasil dihapus dan log aktivitas telah dicatat.");

        } catch (\Exception $e) {
            \Log::error("Admin Gagal hapus proyek: " . $e->getMessage());
            return back()->with('error', 'Gagal membersihkan infrastruktur: ' . $e->getMessage());
        }
    }

    // TERMINAL
    public function terminalIndex()
    {
        return view('admin.terminal');
    }

    public function terminalExecute(Request $request)
    {
        $command = $request->input('command');

        // 1. Keamanan: Batasi perintah berbahaya
        $blacklist = ['rm -rf /', 'shutdown', 'reboot', 'mkfs', ':(){ :|:& };:'];
        foreach ($blacklist as $bad) {
            if (str_contains(strtolower($command), $bad)) {
                return response()->json(['output' => 'Akses Ditolak: Perintah dilarang demi keamanan server!']);
            }
        }

        // 2. Eksekusi perintah (Jalankan di folder /var/www)
        // 2>&1 digunakan agar error terminal juga muncul di output web
        $output = shell_exec("cd /var/www && $command 2>&1");

        // 3. Catat ke Activity Log
        \App\Models\ActivityLog::create([
            'user_id' => auth()->user()->user_id,
            'activity' => 'Terminal',
            'description' => "Admin menjalankan perintah: $command"
        ]);

        return response()->json([
            'output' => $output ?: 'Command executed, no output returned.'
        ]);
    }

    /**
     * BACKUP MANAGER: Menampilkan daftar folder backup
     */
    public function backupIndex(Request $request)
    {
        $backupRoot = storage_path('app/backups');
        $backups = [];
        $search = $request->input('search');

        if (File::exists($backupRoot)) {
            $directories = File::directories($backupRoot);
            foreach ($directories as $dir) {
                $folderName = basename($dir);

                // Fitur Filter: Lewati jika ada search dan nama folder tidak cocok
                if ($search && !str_contains(strtolower($folderName), strtolower($search))) {
                    continue;
                }

                $files = File::files($dir);
                $backups[] = [
                    'author_name'   => $folderName,
                    'files'         => array_map(fn($f) => $f->getFilename(), $files),
                    'count'         => count($files),
                    'last_modified' => date("Y-m-d H:i", File::lastModified($dir)),
                    'size'          => round(collect($files)->sum(fn($f) => $f->getSize()) / 1024 / 1024, 2)
                ];
            }
        }

        return view('admin.backup_manager', compact('backups'));
    }

    /**
     * BACKUP MANAGER: Download file tunggal (.zip atau .sql)
     */
    public function downloadBackupFile($author, $filename)
    {
        $path = storage_path("app/backups/{$author}/{$filename}");
        if (!File::exists($path)) abort(404, "File tidak ditemukan.");
        
        return response()->download($path);
    }

    /**
     * BACKUP MANAGER: Download satu folder sekaligus (Dikonversi ke ZIP)
     */
    public function downloadBackupFolder($author)
    {
        $folderPath = storage_path("app/backups/{$author}");
        if (!File::exists($folderPath)) abort(404, "Folder tidak ditemukan.");

        $zipFileName = "Backup_{$author}.zip";
        $zipPath = storage_path("app/private/{$zipFileName}"); // Simpan sementara di private

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = File::files($folderPath);
            foreach ($files as $file) {
                $zip->addFile($file->getRealPath(), $file->getFilename());
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    /**
     * BACKUP MANAGER: Menghapus file tunggal dalam backup
     */
    public function destroyBackupFile($author, $filename)
    {
        $path = storage_path("app/backups/{$author}/{$filename}");
        
        if (File::exists($path)) {
            File::delete($path);
            
            ActivityLog::create([
                'user_id' => auth()->id(),
                'activity' => 'Hapus Backup File',
                'description' => auth()->user()->name . " menghapus file backup: $filename milik $author"
            ]);

            return back()->with('success', 'File backup berhasil dihapus.');
        }

        return back()->with('error', 'File tidak ditemukan.');
    }

    /**
     * BACKUP MANAGER: Menghapus seluruh folder backup mahasiswa
     */
    public function destroyBackupFolder($author)
    {
        $path = storage_path("app/backups/{$author}");
        
        if (File::exists($path)) {
            File::deleteDirectory($path);
            
            ActivityLog::create([
                'user_id' => auth()->id(),
                'activity' => 'Hapus Backup Folder',
                'description' => auth()->user()->name . " menghapus seluruh folder backup milik: $author"
            ]);

            return back()->with('success', 'Folder backup berhasil dihapus sepenuhnya.');
        }

        return back()->with('error', 'Folder tidak ditemukan.');
    }
}
