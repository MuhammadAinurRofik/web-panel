<?php

namespace App\Http\Controllers;

use ZipArchive;
use Illuminate\Support\Facades\Auth; // untuk handle Auth
use Illuminate\Support\Str;  // untuk generate password acak
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Project;
use App\Models\Notification;
use App\Models\ActivityLog;
use App\Models\DeploymentLog;
use Illuminate\Http\Request;

class ProjectController extends Controller
{

    public function index()
    {
        $projects = Project::where('user_id', Auth::user()->user_id)->get();
        return view('dashboard', compact('projects'));
    }

    // FUNGSI UNTUK MENAMPILKAN FORM
    public function create()
    {
        return view('projects.create'); 
    }

    public function store(Request $request)
    {
        // 1. VALIDASI INPUT
        $request->validate([
            'project_name' => 'required|string|max:255',
            'runtime_type'   => 'required|in:php,python', // Ini menangkap nilai dari switcher Blade
            // PHP version wajib diisi jika runtime_type adalah php
            'php_version'    => 'required_if:runtime_type,php|nullable|string',
            // Python version wajib diisi jika runtime_type adalah python
            'python_version' => 'required_if:runtime_type,python|nullable|in:3.10,3.11,3.12',
            'file_zip'     => 'required|mimes:zip|max:1048576', // Max 1GB
            'sql_file'     => 'nullable|file|max:20480',
            'db_name_input'     => 'nullable|string|max:50|regex:/^[a-zA-Z0-9_]+$/',
            'db_user_input'     => 'nullable|string|max:50|regex:/^[a-zA-Z0-9_]+$/',
            'db_password_input' => 'nullable|string|min:4',
        ]);

        $user = Auth::user();
        $userSlug = \Illuminate\Support\Str::slug($user->name);
        $timestamp = time();

        // 2. CEK BATASAN: 1 USER 1 PROJECT
        $existingProject = Project::where('user_id', $user->user_id)->first();
        if ($existingProject) {
            return back()->with('error', 'Gagal: Anda sudah memiliki project yang terunggah. Silakan hapus project lama terlebih dahulu.');
        }

        // 3. LOGIKA PEMBUATAN SUBDOMAIN
        $slugNama = Str::slug($user->name, '');
        $subdomain = $slugNama . ".whypanel.site";

        // Cek jika subdomain sudah dipakai orang lain
        if (Project::where('subdomain', $subdomain)->exists()) {
            return back()->with('error', "Gagal: Subdomain '{$subdomain}' sudah digunakan mahasiswa lain.");
        }

        // 4. PERSIAPAN TAHAP CI (EXTRACTION)
        $file = $request->file('file_zip');
        $tempPath = storage_path('app/temp/' . Str::random(10));

        $zip = new \ZipArchive;
        if ($zip->open($file->getRealPath()) === TRUE) {
            $zip->extractTo($tempPath);
            
            // Menjalankan Analisis CI (Framework & Kebutuhan DB)
            $analysis = $this->runCIAnalysis($tempPath);
            
            $zip->close();
        } else {
            return back()->with('error', 'Gagal: File ZIP rusak atau tidak bisa dibuka.');
        }

        // VALIDASI CROSS-CHECK 
        // Cek jika mahasiswa pilih Python tapi isi file adalah PHP/Laravel
        if ($request->runtime_type == 'python' && in_array($analysis['type'], ['Laravel', 'PHP Native'])) {
            $this->cleanupTemp($tempPath); // Jangan lupa bersihkan temp
            return back()->with('error', "Gagal: Anda memilih Python Runtime, namun sistem mendeteksi ini adalah proyek PHP ({$analysis['type']}).");
        }

        // Cek jika mahasiswa pilih PHP tapi isi file adalah Flask
        if ($request->runtime_type == 'php' && $analysis['type'] == 'Flask') {
            $this->cleanupTemp($tempPath);
            return back()->with('error', "Gagal: Anda memilih PHP Runtime, namun sistem mendeteksi ini adalah proyek Python Flask.");
        }

        // 5. PENYIMPANAN FILE ZIP ASLI
        $fileZip = $request->file('file_zip');
        // Format: 1738855200_ainur_nama-file-asli.zip
        $zipName = $timestamp . '_' . $userSlug . '_' . $fileZip->getClientOriginalName();
        $filePath = $fileZip->storeAs('projects/private', $zipName);

        // --- BAGIAN SIMPAN SQL ---
        $sqlPath = null;
        if ($request->hasFile('sql_file')) {
            $fileSql = $request->file('sql_file');
            // Format: 1738855200_ainur_nama-db-asli.sql
            $sqlName = $timestamp . '_' . $userSlug . '_' . $fileSql->getClientOriginalName();
            $sqlPath = $fileSql->storeAs('sql_uploads/private', $sqlName);
        }

        $dbName = null; 
        $dbUser = null; 
        $dbPass = null;

        // --- LOGIKA GENERATE PREFIX (2 KATA PERTAMA) ---
        $nameParts = explode(' ', trim(auth()->user()->name));
        // Ambil kata pertama dan kedua (jika ada), gabungkan dengan underscore
        $twoWords = isset($nameParts[1]) ? $nameParts[0] . '_' . $nameParts[1] : $nameParts[0];
        // Slug-kan dan batasi maksimal 15 karakter agar rapi, tambahkan underscore di akhir
        $namePrefix = \Illuminate\Support\Str::limit(\Illuminate\Support\Str::slug($twoWords, '_'), 15, '') . '_';

        /**
         * Validasi: Database hanya dibuat jika:
         * 1. Input diisi (filled)
         * 2. Panjang karakter input LEBIH BESAR dari panjang prefix bawaan
         * (Artinya mahasiswa menambahkan teks di belakang muhammad_ainur_)
         */
        if ($request->filled('db_name_input') && strlen($request->db_name_input) > strlen($namePrefix)) {
            
            // 1. Ambil input mentah
            $inputDb = $request->db_name_input;
            $inputUser = $request->db_user_input;

            // 2. Bersihkan: Hilangkan 'db_' atau 'user_' jika mahasiswa iseng mengetiknya manual
            $inputDb = str_replace('db_', '', $inputDb);
            $inputUser = str_replace('user_', '', $inputUser);

            // 3. Ambil "akhiran" (suffix) saja dengan membuang prefix-nya
            $suffixDb = str_replace($namePrefix, '', $inputDb);
            $suffixUser = str_replace($namePrefix, '', $inputUser);

            // 4. Slug-kan akhiran agar aman dari spasi/karakter aneh
            $finalSuffixDb = \Illuminate\Support\Str::slug($suffixDb, '_');
            $finalSuffixUser = \Illuminate\Support\Str::slug($suffixUser, '_');

            // 5. Jika setelah di-slug akhiran tersebut tidak kosong, baru buat kredensial
            if (!empty($finalSuffixDb)) {
                // Hasil akhir: db_muhammad_ainur_absensi
                $dbName = "db_" . $namePrefix . $finalSuffixDb;
                $dbUser = "user_" . $namePrefix . $finalSuffixUser;
                
                // Password: Ambil dari input atau generate random 12 karakter
                $dbPass = $request->db_password_input ?? \Illuminate\Support\Str::random(12);
            }
        }

        // 7. SIMPAN DATA KE TABEL PROJECTS
        $project = Project::create([
            'user_id'      => $user->user_id,
            'author_name'  => auth()->user()->name,
            'project_name' => $request->project_name,
            'project_type' => $analysis['type'],

            // --- INPUT VERSI DARI MAHASISWA ---
            'php_version'    => ($request->runtime_type == 'php') ? $request->php_version : null,
            'python_version' => ($request->runtime_type == 'python') ? $request->python_version : null,
            // ---------------------------------

            'entry_point'    => $analysis['entry_file'],     
            'flask_instance' => $analysis['flask_instance'],      
            'file_path'    => $filePath,
            'sql_path'     => $sqlPath,
            'status'       => 'pending',
            'subdomain'    => $subdomain,
            'db_name'      => $dbName,
            'db_user'      => $dbUser,
            'db_password'  => $dbPass,
            'extract_path' => "/var/www/" . $subdomain,
            'need_db'      => !empty($dbName),
        ]);

        // 8. TAHAP CI - HASIL & NOTIFIKASI (Poin d)

        // Tentukan string versi untuk log
        $selectedVersion = $project->project_type == 'Flask' 
            ? "Python " . $project->python_version 
            : "PHP " . $project->php_version;

        // Log Utama Hasil Deteksi Framework & Versi
        DeploymentLog::create([
            'project_id' => $project->project_id,
            'process'    => 'CI Analysis',
            'status'     => 'Success',
            'message'    => "Sistem mendeteksi tipe: " . $analysis['type'] . 
                            " (" . $selectedVersion . ")" . 
                            " dengan Entry Point: " . $analysis['entry_file']
        ]);

        // Log Konfigurasi Database (Berdasarkan input form db_suffix)
        if ($project->db_name) {
            DeploymentLog::create([
                'project_id' => $project->project_id,
                'process'    => 'CI Analysis',
                'status'     => 'Success',
                'message'    => "Infrastruktur database disiapkan dengan nama: {$project->db_name}. Silakan gunakan kridensial ini pada file koneksi Anda."
            ]);
        }

        // Log Folder Bersarang (Jika path hasil deteksi berbeda dengan path awal)
        if ($analysis['path'] !== $tempPath) {
            DeploymentLog::create([
                'project_id' => $project->project_id,
                'process'    => 'CI Analysis',
                'status'     => 'Success',
                'message'    => "Struktur folder bersarang ditemukan dan telah disesuaikan secara otomatis."
            ]);
        }
        
        // Simpan ke Deployment Logs (Riwayat Teknis)
        $project->logs()->create([
            'process' => 'CI Validation',
            'status'  => 'Success',
            'message' => "Proyek terdeteksi sebagai {$analysis['type']}. " . ($project->db_name ? "Infrastruktur DB dialokasikan." : "Tanpa Database.")
        ]);

        // Simpan ke Notifications (Pesan untuk Mahasiswa)
        Notification::create([
            'user_id'    => $user->user_id,
            'project_id' => $project->project_id,
            'message'    => "Proyek '{$project->project_name}' berhasil melewati validasi CI ({$analysis['type']}). Menunggu persetujuan Admin.",
            'status'     => 'unread'
        ]);

        // 9. CLEANUP & FINISH
        $this->cleanupTemp($tempPath); // Hapus folder ekstraksi sementara

        ActivityLog::create([
            'user_id' => Auth::user()->user_id,
            'activity' => 'Upload',
            'description' => Auth::user()->name . " telah mengunggah proyek: " . $request->project_name
        ]);

        // Cari user dengan role 'admin'
        $admins = User::where('usertype', 'admin')->get();

        // Kirim notifikasi ke semua Admin
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'user_id'    => $admin->user_id, // ID Admin
                'project_id' => $project->project_id,
                'type'       => 'admin_alert',
                'message'    => "PROYEK BARU: {$project->user->name} mengunggah proyek '{$project->project_name}' ({$project->project_type}). Segera lakukan review dan approval.",
                'status'     => 'unread'
            ]);
        }

        return redirect()->route('dashboard')->with('success', "Proyek berhasil diunggah dan lolos validasi.");
    }

    private function runCIAnalysis($path)
    {
        // 1. HANDLING FOLDER DALAM FOLDER
        $allEntries = array_diff(scandir($path), array('.', '..'));
        if (count($allEntries) === 1) {
            $firstEntry = reset($allEntries);
            if (is_dir($path . '/' . $firstEntry)) {
                $path = $path . '/' . $firstEntry;
                $allEntries = array_diff(scandir($path), array('.', '..'));
            }
        }

        // 2. KANTONG BUKTI & SKOR
        $scores = [
            'Laravel' => 0,
            'Flask'   => 0,
        ];

        $metadata = [
            'type'           => 'HTML Static', // Default paling dasar
            'entry_file'     => 'index.html',
            'flask_instance' => 'app',
            'has_php_code'   => false
        ];

        // 3. PROSES INVESTIGASI (SCANNING)
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));

        foreach ($files as $fileInfo) {
            if ($fileInfo->isDir()) continue;

            $filePath = $fileInfo->getRealPath();
            $file = $fileInfo->getFilename();
            
            // Ambil path relatif (contoh: config/koneksi.php) untuk disimpan ke database
            $relativePath = str_replace($path . '/', '', $filePath);

            // A. Cek Nama File Khusus (Hanya di Root)
            if (dirname($relativePath) === '.') {
                if ($file === 'artisan') $scores['Laravel'] += 10;
                if ($file === 'requirements.txt') $scores['Flask'] += 2;
                
                if (in_array($file, ['index.php', 'index.html', 'home.php'])) {
                    $metadata['entry_file'] = $file;
                }
            }

            // B. Deep Scan Konten (Untuk Akurasi Tipe)
            if (preg_match('/\.(py|php|json)$/', $file)) {
                $content = file_get_contents($filePath);

                // Indikator PHP (Akan menandai sebagai PHP Native jika skor framework rendah)
                if (str_ends_with($file, '.php') || str_contains($content, '<?php')) {
                    $metadata['has_php_code'] = true;
                }

                // Sidik Jari Laravel (Berdasarkan Dependency)
                if ($file === 'composer.json' && str_contains($content, 'laravel/framework')) {
                    $scores['Laravel'] += 10;
                }

                // Sidik Jari Flask (Mencari Inisialisasi Instance)
                if (str_ends_with($file, '.py')) {
                    if (preg_match('/(\w+)\s*=\s*Flask\(__name__\)/', $content, $matches)) {
                        $scores['Flask'] += 15;
                        $metadata['entry_file'] = $file; // Entry point Flask adalah file .py utamanya
                        $metadata['flask_instance'] = $matches[1];
                    }
                }
            }
        }

        // 4. PENGAMBILAN KEPUTUSAN (DECISION MAKING)
        arsort($scores);
        $highestScore = reset($scores);
        $detectedType = key($scores);

        if ($highestScore >= 10) {
            // TERDETEKSI FRAMEWORK (Laravel / Flask)
            $metadata['type'] = $detectedType;

            // Penyesuaian Khusus Laravel
            if ($detectedType === 'Laravel') {
                // Laravel selalu masuk lewat public/index.php
                $metadata['entry_file'] = 'public/index.php';
            }
        } else {
            // JIKA TIDAK ADA FRAMEWORK, TENTUKAN PHP NATIVE ATAU HTML STATIC
            if ($metadata['has_php_code']) {
                $metadata['type'] = 'PHP Native';
                // Pastikan jika ada index.php di root, itu yang jadi entry point
                if (file_exists($path . '/index.php')) {
                    $metadata['entry_file'] = 'index.php';
                }
            } else {
                $metadata['type'] = 'HTML Static';
            }
        }

        return [
            'type'           => $metadata['type'],
            'entry_file'     => $metadata['entry_file'],
            'flask_instance' => $metadata['flask_instance'],
            'path'           => $path
        ];
    }

    private function cleanupTemp($path)
    {
        if (is_dir($path)) {
            \File::deleteDirectory($path);
        }
    }

    public function inspectZip($id)
    {
        $project = Project::findOrFail($id);
        $path = storage_path('app/private/' . $project->file_path);

        if (!file_exists($path)) {
            return response()->json(['error' => 'File ZIP tidak ditemukan di storage.'], 404);
        }

        $zip = new \ZipArchive;
        $files = [];

        if ($zip->open($path) === TRUE) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                $files[] = [
                    'name' => $stat['name'],
                    'size' => round($stat['size'] / 1024, 2) . ' KB',
                    'is_dir' => (substr($stat['name'], -1) == '/')
                ];
            }
            $zip->close();
        }

        return response()->json([
            'project_name' => $project->project_name,
            'type' => $project->project_type,
            'files' => $files
        ]);
    }

/**
     * Menghapus project milik user yang sedang login.
     */
    public function destroy($id)
    {
        // Cari proyek berdasarkan user yang login (keamanan)
        $project = Project::where('project_id', $id)
                        ->where('user_id', Auth::user()->user_id)
                        ->firstOrFail();

        $domain = $project->subdomain; // Contoh: budi.prodi.ac.id

        try {
            // --- LOG AKTIVITAS ---
            \App\Models\ActivityLog::create([
                'user_id' => Auth::user()->user_id,
                'activity' => 'Hapus',
                'description' => Auth::user()->name . " telah menghapus proyek: " . $project->project_name
            ]);

            // --- SKENARIO 1: HAPUS FILE ZIP & SQL ASLI (Local Storage) ---
            if ($project->file_path && Storage::disk('local')->exists($project->file_path)) {
                Storage::disk('local')->delete($project->file_path);
            }
            if ($project->sql_path && Storage::disk('local')->exists($project->sql_path)) {
                Storage::disk('local')->delete($project->sql_path);
            }

            // --- SKENARIO 2: HAPUS INFRASTRUKTUR SERVER (Hanya Jika Active) ---
            if ($project->status === 'active') {

                // A. KHUSUS FLASK: Hapus Systemd Service
                if ($project->project_type === 'Flask') {
                    // Ambil nama singkat (budi.prodi.ac.id -> budi)
                    $subName = explode('.', $domain)[0];
                    $serviceName = "flask_" . $subName;

                    // Bersihkan Cache Pip: agar download ulang jika deploy lagi)
                    // Kita gunakan venv proyek tersebut untuk menjalankan perintah purge
                    if (File::exists($project->extract_path . "/venv/bin/pip")) {
                        shell_exec("sudo " . $project->extract_path . "/venv/bin/pip cache purge");
                    }

                    // Stop, Disable, dan Hapus file service
                    shell_exec("sudo systemctl stop $serviceName");
                    shell_exec("sudo systemctl disable $serviceName");
                    shell_exec("sudo rm -f /etc/systemd/system/$serviceName.service");
                    shell_exec("sudo systemctl daemon-reload");
                }

                // B. Hapus Folder Proyek di /var/www/
                if ($project->extract_path && File::exists($project->extract_path)) {
                    shell_exec("sudo rm -rf " . escapeshellarg($project->extract_path));
                }

                // C. Hapus Konfigurasi Nginx
                $confPath = "/etc/nginx/sites-available/{$domain}.conf";
                $linkPath = "/etc/nginx/sites-enabled/{$domain}.conf";
                
                shell_exec("sudo rm -f " . escapeshellarg($linkPath));
                shell_exec("sudo rm -f " . escapeshellarg($confPath));

                // D. Hapus Log Nginx
                $accessLog = "/var/log/nginx/{$domain}.access.log";
                $errorLog = "/var/log/nginx/{$domain}.error.log";
                shell_exec("sudo rm -f " . escapeshellarg($accessLog));
                shell_exec("sudo rm -f " . escapeshellarg($errorLog));
                
                // Reload Nginx
                shell_exec("sudo systemctl reload nginx");

                // E. Hapus Database & User MySQL
                if ($project->db_name) {
                    \DB::statement("DROP DATABASE IF EXISTS `{$project->db_name}`");
                    if ($project->db_user) {
                        \DB::statement("DROP USER IF EXISTS '{$project->db_user}'@'localhost'");
                    }
                    \DB::statement("FLUSH PRIVILEGES");
                }
            }

            // --- SKENARIO 3: HAPUS RECORD DATABASE ---
            $project->delete();

            return redirect()->route('dashboard')->with('success', 'Proyek dan seluruh infrastruktur (File, DB, Service, Nginx) berhasil dibersihkan.');

        } catch (\Exception $e) {
            \Log::error("Gagal menghapus proyek: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat pembersihan: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        set_time_limit(3600);
        $project = Project::with('user')->findOrFail($id);
        $folderName = $project->subdomain;
        $extractPath = "/var/www/" . $folderName;
        $baseStorage = "/var/www/web-panel/storage/app/private/";
        $pathBackup = storage_path("app/backups/" . $project->author_name);
        $fullSourcePath = $baseStorage . $project->file_path;
        $fullSqlSourcePath = $project->sql_path ? $baseStorage . $project->sql_path : null;
        $zipDestination = $extractPath . "/project.zip";
        $domain = $project->subdomain;

        try {
            
            // PROSES BACKUP OTOMATIS
            // Buat folder backup berdasarkan nama author jika belum ada
            if (!File::exists($pathBackup)) {
                File::makeDirectory($pathBackup, 0755, true);
            }

            // Salin File ZIP ke folder backup
            if (File::exists($fullSourcePath)) {
                File::copy($fullSourcePath, $pathBackup . '/' . basename($project->file_path));
            }

            // Salin File SQL ke folder backup (jika ada)
            if ($fullSqlSourcePath && File::exists($fullSqlSourcePath)) {
                File::copy($fullSqlSourcePath, $pathBackup . '/' . basename($project->sql_path));
            }

            DeploymentLog::create([
                'project_id' => $project->project_id,
                'process'    => 'Backup System',
                'status'     => 'Success',
                'message'    => "Arsip disimpan di: storage/app/backups/{$project->author_name}"
            ]);

            // --- TAHAP 1: EKSTRAKSI ---
            if (!File::exists($extractPath)) {
                shell_exec("sudo mkdir -p " . escapeshellarg($extractPath));
            }
            shell_exec("sudo cp " . escapeshellarg($fullSourcePath) . " " . escapeshellarg($zipDestination));
            shell_exec("sudo unzip -o " . escapeshellarg($zipDestination) . " -d " . escapeshellarg($extractPath));
            shell_exec("sudo rm -f " . escapeshellarg($zipDestination));

            // PENANGANAN FOLDER BERSARANG (FLATTENING)
            // Kita cek apakah ada sub-folder di dalam $extractPath
            $allFiles = array_diff(scandir($extractPath), array('.', '..'));
            
            // Jika hanya ada 1 item dan item itu adalah folder
            if (count($allFiles) === 1) {
                $subFolder = reset($allFiles);
                $nestedPath = $extractPath . "/" . $subFolder;

                if (is_dir($nestedPath)) {
                    // Pindahkan semua isi sub-folder ke root $extractPath
                    shell_exec("sudo mv " . escapeshellarg($nestedPath) . "/* " . escapeshellarg($extractPath) . "/");
                    // Pindahkan file hidden (seperti .env atau .gitignore)
                    shell_exec("sudo mv " . escapeshellarg($nestedPath) . "/.* " . escapeshellarg($extractPath) . "/ 2>/dev/null");
                    // Hapus folder kosong sisanya
                    shell_exec("sudo rm -rf " . escapeshellarg($nestedPath));

                    DeploymentLog::create([
                        'project_id' => $project->project_id,
                        'process'    => 'Extraction',
                        'status'     => 'Success',
                        'message'    => "Folder bersarang '{$subFolder}' terdeteksi dan berhasil diratakan ke root."
                    ]);
                }
            }

            DeploymentLog::create([
                'project_id' => $project->project_id,
                'process'    => 'Extraction',
                'status'     => 'Success',
                'message'    => 'File berhasil diekstrak ke ' . $extractPath
            ]);

            // --- TAHAP 2: DATABASE SETUP (EKSEKUSI FISIK MYSQL) ---
            // Hanya dijalankan jika mahasiswa menginputkan kridensial saat upload
            if ($project->db_name) {
                // Pembuatan DB Fisik berdasarkan input mahasiswa
                \DB::statement("CREATE DATABASE IF NOT EXISTS `{$project->db_name}`");
                \DB::statement("CREATE USER IF NOT EXISTS '{$project->db_user}'@'localhost' IDENTIFIED BY '{$project->db_password}'");
                \DB::statement("ALTER USER '{$project->db_user}'@'localhost' IDENTIFIED BY '{$project->db_password}'");
                \DB::statement("GRANT ALL PRIVILEGES ON `{$project->db_name}`.* TO '{$project->db_user}'@'localhost'");
                \DB::statement("FLUSH PRIVILEGES");

                // Handle Import SQL jika ada file .sql
                if ($fullSqlSourcePath && file_exists($fullSqlSourcePath)) {
                    $sqlFileName = basename($fullSqlSourcePath);
                    $finalSqlPath = $extractPath . "/" . $sqlFileName;
                    shell_exec("sudo cp " . escapeshellarg($fullSqlSourcePath) . " " . escapeshellarg($finalSqlPath));
                    
                    $this->handleSqlImport($project, $finalSqlPath, $extractPath);
                }
                
                DeploymentLog::create([
                    'project_id' => $project->project_id,
                    'process'    => 'Database Setup',
                    'status'     => 'Success',
                    'message'    => "Database `{$project->db_name}` dan User `{$project->db_user}` berhasil dibuat fisik."
                ]);
            }

            // --- TAHAP 3: LOGIKA FRAMEWORK ---
            if ($project->project_type === 'Laravel') {
                // Ini akan memproses .env SEBELUM permission dikunci
                $this->handleLaravelLogic($project, $extractPath);
            }
            elseif ($project->project_type === 'Flask') {
                $this->handleFlaskLogic($project, $extractPath);
            } 
            else {
                // UNTUK PHP NATIVE DAN HTML STATIC
                $this->handlePhpNativeLogic($project, $extractPath);
                $this->setupNativeNginxConfig($project, $extractPath, $domain);
            }

            // --- TAHAP 4: PERMISSION FINAL & RELOAD ---
            // Jalankan chown PALING AKHIR setelah semua file (.env) selesai dimodifikasi
            shell_exec("sudo chown -R www-data:www-data " . escapeshellarg($extractPath));
            shell_exec("sudo chmod -R 755 " . escapeshellarg($extractPath));
            
            if ($project->project_type === 'Laravel') {
                shell_exec("sudo chmod -R 775 " . escapeshellarg($extractPath . "/storage"));
                shell_exec("sudo chmod -R 775 " . escapeshellarg($extractPath . "/bootstrap/cache"));
            }

            shell_exec("sudo systemctl reload nginx");

            // --- TAHAP 5: UPDATE STATUS & ACTIVITY LOG ---
            $project->update([
                'status' => 'active',
                'extract_path' => $extractPath
            ]);

            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'activity' => 'Approve',
                'description' => "Admin mendeploy proyek '{$project->project_name}' ke http://$domain"
            ]);

            // --- PENGIRIMAN NOTIFIKASI APPROVE ---
            \App\Models\Notification::create([
                'user_id'    => $project->user_id,
                'project_id' => $project->project_id,
                'type'       => 'success', // Opsional jika tabel Anda punya kolom type
                'message'    => "Selamat! Proyek '{$project->project_name}' sudah LIVE. " .
                                "URL: http://{$domain} | " .
                                "Database: {$project->db_name} | " .
                                "User DB: {$project->db_user} | " .
                                "Pass DB: {$project->db_password}",
                'status'     => 'unread'
            ]);

            return back()->with('success', "Proyek LIVE di http://$domain");

        } catch (\Exception $e) {
            DeploymentLog::create([
                'project_id' => $project->project_id,
                'process'    => 'System Error',
                'status'     => 'Failed',
                'message'    => $e->getMessage()
            ]);
            return back()->with('error', "Gagal: " . $e->getMessage());
        }
    }

    private function handleFlaskLogic($project, $extractPath)
    {
        $masterScript = storage_path("app/scripts/flask_deploy.sh");

        $args = [
            escapeshellarg($extractPath),               // $1
            escapeshellarg($project->db_name),          // $2
            escapeshellarg($project->db_user),          // $3
            escapeshellarg($project->db_password),      // $4
            escapeshellarg($project->subdomain),        // $5 (budi.prodi.ac.id)
            escapeshellarg($project->project_name),     // $6
            escapeshellarg($project->entry_point),      // $7 (app.py)
            escapeshellarg($project->flask_instance),   // $8 (app)
            escapeshellarg($project->python_version)    // $9
        ];

        // Eksekusi di background
        $command = "sudo -u www-data /usr/bin/bash $masterScript " . implode(' ', $args) . " > /dev/null 2>&1 &";
        shell_exec($command);

        // Ambil identitas singkat untuk log
        $subName = explode('.', $project->subdomain)[0];

        DeploymentLog::create([
            'project_id' => $project->project_id,
            'process'    => 'Flask Deployment',
            'status'     => 'Processing',
            'message'    => "Sistem sedang mengonfigurasi Flask environment. Service: flask_{$subName}.service"
        ]);

        return true;
    }

    private function handleLaravelLogic($project, $extractPath)
    {
        // Bersihkan folder vendor/node_modules bawaan Windows (Synchronous karena cepat)
        shell_exec("sudo rm -rf " . escapeshellarg($extractPath . "/vendor"));
        shell_exec("sudo rm -rf " . escapeshellarg($extractPath . "/node_modules"));
        shell_exec("sudo rm -rf " . escapeshellarg($extractPath . "/bootstrap/cache/*"));
        shell_exec("sudo rm -rf " . escapeshellarg($extractPath . "/storage/framework/views/*"));
        shell_exec("sudo rm -rf " . escapeshellarg($extractPath . "/storage/framework/cache/data/*"));
        shell_exec("sudo rm -rf " . escapeshellarg($extractPath . "/storage/framework/sessions/*"));

        // Memberikan izin tulis ke folder storage dan cache agar Laravel bisa menulis log error jika terjadi kegagalan
        shell_exec("sudo chown -R www-data:www-data " . escapeshellarg($extractPath));
        shell_exec("sudo chmod -R 775 " . escapeshellarg($extractPath . "/storage"));
        shell_exec("sudo chmod -R 775 " . escapeshellarg($extractPath . "/bootstrap/cache"));

        $envPath = $extractPath . "/.env";

        // Setup .env dari template jika tidak ada
        if (!File::exists($envPath) && File::exists($extractPath . "/.env.example")) {
            shell_exec("sudo cp " . escapeshellarg($extractPath . "/.env.example") . " " . escapeshellarg($envPath));
        }

        if (File::exists($envPath)) {
            // Berikan izin tulis sementara
            shell_exec("sudo chmod 666 " . escapeshellarg($envPath));

            $content = File::get($envPath);

            // LOGIKA UNCOMMENT: Hapus tanda '#' di depan baris DB_ agar bisa dibaca
            // Regex ini mencari baris yang diawali '#' lalu 'DB_' dan membuang '#' nya.
            $content = preg_replace('/^#\s*(DB_.*)/m', '$1', $content);
            
            // Suntikkan kridensial database (menggunakan petik ganda untuk password agar aman dari karakter spesial)
            $content = preg_replace('/DB_CONNECTION=.*/', "DB_CONNECTION=mysql", $content);
            $content = preg_replace('/DB_HOST=.*/', "DB_HOST=127.0.0.1", $content);
            $content = preg_replace('/DB_DATABASE=.*/', "DB_DATABASE={$project->db_name}", $content);
            $content = preg_replace('/DB_USERNAME=.*/', "DB_USERNAME={$project->db_user}", $content);
            $content = preg_replace('/DB_PASSWORD=.*/', "DB_PASSWORD=\"{$project->db_password}\"", $content);
            
            // Update URL & Nama Proyek
            $content = preg_replace('/APP_URL=.*/', "APP_URL=http://{$project->subdomain}", $content);
            $content = preg_replace('/APP_NAME=.*/', "APP_NAME=\"" . addslashes($project->project_name) . "\"", $content);

            // Simpan perubahan
            if (File::put($envPath, $content)) {
                // Cek jika APP_KEY masih kosong, maka generate otomatis
                if (!str_contains($content, 'base64:')) {
                    shell_exec("cd " . escapeshellarg($extractPath) . " && sudo -u www-data php artisan key:generate --force");
                }

                DeploymentLog::create([
                    'project_id' => $project->project_id,
                    'process'    => 'Environment Setup',
                    'status'     => 'Success',
                    'message'    => "Berhasil menyuntikkan kridensial database dan konfigurasi Laravel."
                ]);
            } else {
                DeploymentLog::create([
                    'project_id' => $project->project_id,
                    'process'    => 'Environment Setup',
                    'status'     => 'Failed',
                    'message'    => "Gagal menulis ke file .env."
                ]);
            }

            // Kembalikan permission ke mode aman
            shell_exec("sudo chown www-data:www-data " . escapeshellarg($envPath));
            shell_exec("sudo chmod 644 " . escapeshellarg($envPath));
        }

        // Ini akan mencari file .sql di root folder Laravel
        // $this->handleSqlImport($project, null, $extractPath);

        shell_exec("cd " . escapeshellarg($extractPath) . " && sudo -u www-data php artisan config:clear");

        // --- PANGGIL SCRIPT BASH (Background Process) ---
        $masterScript = storage_path("app/scripts/install_laravel.sh");
        $logFile = $extractPath . "/install_log.txt";
        $phpVer = $project->php_version; // Ambil versi PHP

        // Pastikan file log ada dan bisa ditulis oleh Bash
        shell_exec("sudo touch " . escapeshellarg($logFile));
        shell_exec("sudo chmod 666 " . escapeshellarg($logFile));

        // Argumen untuk script bash
        $args = [
            escapeshellarg($extractPath),       // $1
            escapeshellarg($project->subdomain), // $2
            escapeshellarg($phpVer)             // $3
        ];

        // Eksekusi script bash di background
        // Output dialihkan ke install_log.txt agar bisa dibaca Terminal Dashboard
        $command = "sudo /usr/bin/bash $masterScript " . implode(' ', $args) . " > " . escapeshellarg($logFile) . " 2>&1 &";
        shell_exec($command);

        // Catat Log Awal
        DeploymentLog::create([
            'project_id' => $project->project_id,
            'process'    => 'Laravel Deployment',
            'status'     => 'Processing',
            'message'    => "Sistem sedang menginstal dependensi dan mem-build aset Laravel."
        ]);

        return true;
    }

    private function handlePhpNativeLogic($project, $extractPath)
    {
        // 1. TAHAP IMPORT DATABASE
        // Karena pembuatan DB fisik (CREATE DATABASE & USER) sudah dilakukan di fungsi approve(),
        // di sini kita hanya perlu memastikan data tabel (SQL) masuk ke sana.
        if ($project->sql_path) {
            $fullSqlPath = $extractPath . "/" . basename($project->sql_path);
            
            if (File::exists($fullSqlPath)) {
                $this->handleSqlImport($project, $fullSqlPath, $extractPath);
                
                DeploymentLog::create([
                    'project_id' => $project->project_id,
                    'process'    => 'SQL Auto-Import',
                    'status'     => 'Success',
                    'message'    => "File database .sql berhasil diimpor ke `{$project->db_name}`."
                ]);
            }
        }

        // 2. LOGIKA PASIF
        // Kita berikan catatan di Log agar mahasiswa/admin tahu bahwa sistem tidak mengubah kode.
        DeploymentLog::create([
            'project_id' => $project->project_id,
            'process'    => 'Native PHP Setup',
            'status'     => 'Success',
            'message'    => "Deployment selesai. Sistem tidak mengubah file konfigurasi PHP Native. Mahasiswa wajib menyesuaikan kredensial database di kodingan sesuai informasi di Dashboard."
        ]);
    }

    private function setupNativeNginxConfig($project, $extractPath, $domain)
    {
        // Ambil versi dari DB, default ke 8.3 jika kosong
        $phpVer = $project->php_version;

        // PHP Native root ada di folder ekstraksi langsung (bukan /public)
        $nginxConfig = "
        server {
            listen 80;
            server_name $domain;
            root $extractPath;

            index {$project->entry_point} index.php index.html;

            # Pisahkan log ke file tersendiri
            access_log /var/log/nginx/{$domain}.access.log;
            error_log /var/log/nginx/{$domain}.error.log;

            location / {
                try_files \$uri \$uri/ /{$project->entry_point}?\$query_string;
            }

            location ~ \.php$ {
                include snippets/fastcgi-php.conf;
                fastcgi_pass unix:/var/run/php/php{$phpVer}-fpm.sock;
            }

            location ~ /\.ht {
                deny all;
            }

            location ~ \.(sql|zip|log|env|git)$ {
                deny all;
            }
        }";

        $availableConf = "/etc/nginx/sites-available/{$domain}.conf";
        $enabledConf = "/etc/nginx/sites-enabled/{$domain}.conf";
        $tempConf = "/tmp/native_{$domain}.conf";

        \File::put($tempConf, $nginxConfig);
        shell_exec("sudo mv " . escapeshellarg($tempConf) . " " . escapeshellarg($availableConf));
        shell_exec("sudo ln -sf " . escapeshellarg($availableConf) . " " . escapeshellarg($enabledConf));

        DeploymentLog::create([
            'project_id' => $project->project_id,
            'process'    => 'Nginx Config',
            'status'     => 'Success',
            'message'    => "Konfigurasi Nginx Native untuk $domain berhasil diaktifkan."
        ]);
    }

    public function getProjectErrorLog($id)
    {
        $project = Project::findOrFail($id);
        $logFile = "/var/log/nginx/{$project->subdomain}.error.log";

        // Cek apakah file ada
        if (!file_exists($logFile)) {
            return response()->json([
                'status' => 'empty',
                'message' => 'Belum ada catatan error untuk proyek ini.'
            ]);
        }

        // Jalankan tail via sudo (Pastikan sudah setting visudo www-data)
        $logContent = shell_exec("sudo tail -n 30 " . escapeshellarg($logFile));

        if (empty($logContent)) {
            return response()->json([
                'status' => 'empty',
                'message' => 'Log kosong (tidak ada error terdeteksi).'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $logContent
        ]);
    }

    private function handleSqlImport($project, $finalSqlPath, $extractPath)
    {
        $dbName = $project->db_name;
        $sqlToImport = null;
        $source = "";

        // 1. Prioritas Utama: File dari Form Upload (Sudah pasti benar)
        if ($finalSqlPath && file_exists($finalSqlPath)) {
            $sqlToImport = $finalSqlPath;
            $source = "Form Upload";
        } 
        // 2. Prioritas Kedua: Cari di ROOT FOLDER saja (Mengabaikan sub-folder)
        else {
            // File::files() hanya mengambil file yang ada di tingkat pertama folder tersebut
            $filesInRoot = \File::files($extractPath); 
            
            foreach ($filesInRoot as $file) {
                if ($file->getExtension() === 'sql') {
                    $sqlToImport = $file->getRealPath();
                    $source = "Root Folder (" . $file->getFilename() . ")";
                    break; // Ambil file SQL pertama yang ditemukan di root
                }
            }
        }

        // 3. Eksekusi Import
        if ($sqlToImport && $dbName) {
            $command = "mysql -u" . escapeshellarg(config('database.connections.mysql.username')) . 
                    " -p" . escapeshellarg(config('database.connections.mysql.password')) . 
                    " " . escapeshellarg($dbName) . " < " . escapeshellarg($sqlToImport) . " 2>&1";
            
            $output = shell_exec($command);
            $cleanOutput = str_replace('mysql: [Warning] Using a password on the command line interface can be insecure.', '', $output);

            if (empty(trim($cleanOutput))) {
                \App\Models\DeploymentLog::create([
                    'project_id' => $project->project_id,
                    'process'    => 'SQL Import',
                    'status'     => 'Success',
                    'message'    => "Database berhasil di-import dari $source."
                ]);
            } else {
                \App\Models\DeploymentLog::create([
                    'project_id' => $project->project_id,
                    'process'    => 'SQL Import',
                    'status'     => 'Failed',
                    'message'    => "Gagal import dari $source. Error: " . trim($cleanOutput)
                ]);
            }
        } 
        // 4. Catat Log jika benar-benar tidak ada SQL di Root maupun Form
        else {
            \App\Models\DeploymentLog::create([
                'project_id' => $project->project_id,
                'process'    => 'SQL Import',
                'status'     => 'Skipped',
                'message'    => 'Proses Import dilewati: Tidak ditemukan file .sql di root folder maupun form upload.'
            ]);
        }
    }

    public function getLogs($id)
    {
        $project = Project::findOrFail($id);

        // Keamanan: Jika yang mengakses bukan Admin DAN bukan pemilik proyek, maka TOLAK.
        if (auth()->user()->usertype !== 'admin' && $project->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = DeploymentLog::where('project_id', $id)
            ->orderBy('timestamp', 'desc')
            ->get();

        // Baca isi file fisik install_log.txt
        $logPath = $project->extract_path . "/install_log.txt";
        $fileContent = File::exists($logPath) ? File::get($logPath) : "Menunggu proses dimulai...";

        /* Agar tidak merubah struktur frontend, kita tambahkan isi file 
        ke dalam koleksi $logs sebagai item tambahan atau properti baru.
        Di sini saya masukkan ke dalam log paling atas (index 0).
        */
        if ($logs->count() > 0) {
            $logs[0]->file_content = $fileContent;
            $logs[0]->project_type = $project->project_type;
        }

        return response()->json($logs);
    }

    public function toggleStatus($id)
    {
        $project = Project::findOrFail($id);
        $domain = $project->subdomain;
        $availableConf = "/etc/nginx/sites-available/{$domain}.conf";
        $enabledConf = "/etc/nginx/sites-enabled/{$domain}.conf";

        try {
            if ($project->status === 'active') {
                // PROSES NON-AKTIFKAN (OFF)
                shell_exec("sudo rm -f " . escapeshellarg($enabledConf));
                $project->update(['status' => 'suspended']);
                
                // Catat ke Deployment Log
                DeploymentLog::create([
                    'project_id' => $project->project_id,
                    'process'    => 'Power Control',
                    'status'     => 'Success',
                    'message'    => 'Admin menonaktifkan server (Status: Suspended). Akses Nginx dicabut.'
                ]);

                $message = "Proyek $domain dinonaktifkan.";
            } else {
                // PROSES AKTIFKAN (ON)
                if (File::exists($availableConf)) {
                    shell_exec("sudo ln -sf " . escapeshellarg($availableConf) . " " . escapeshellarg($enabledConf));
                    $project->update(['status' => 'active']);
                    
                    DeploymentLog::create([
                        'project_id' => $project->project_id,
                        'process'    => 'Power Control',
                        'status'     => 'Success',
                        'message'    => 'Admin mengaktifkan kembali server (Status: Active). Akses Nginx dipulihkan.'
                    ]);

                    $message = "Proyek $domain diaktifkan kembali.";
                } else {
                    return back()->with('error', "File konfigurasi tidak ditemukan.");
                }
            }

            shell_exec("sudo systemctl reload nginx");
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'newStatus' => $project->status,
                    'message' => $message
                ]);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DeploymentLog::create([
                'project_id' => $project->project_id,
                'process'    => 'System Error',
                'status'     => 'Failed',
                'message'    => "Terjadi kesalahan sistem: " . $e->getMessage()
            ]);

            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            
            return back()->with('error', "Gagal: " . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        // Validasi input alasan
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        $project = Project::findOrFail($id);

        // 1. Kirim Notifikasi Penolakan dengan Alasan Spesifik
        \App\Models\Notification::create([
            'user_id'    => $project->user_id,
            'project_id' => $project->project_id,
            'message'    => "PROYEK DITOLAK: {$project->project_name}. Alasan: " . $request->reason,
            'status'     => 'unread'
        ]);

        // 2. Catat di Activity Log (Audit Trail)
        \App\Models\ActivityLog::create([
            'user_id'     => Auth::user()->user_id,
            'activity'    => 'Reject',
            'description' => "Admin menolak proyek milik " . $project->user->name . " dengan alasan: " . $request->reason
        ]);

        // 3. Ubah status agar mahasiswa bisa upload ulang (atau hapus file lama)
        $project->update(['status' => 'rejected']);

        return back()->with('success', 'Penolakan proyek berhasil dikirim kepada mahasiswa.');
    }
}
