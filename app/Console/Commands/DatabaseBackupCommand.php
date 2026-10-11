<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseBackupCommand extends Command
{
    protected $signature = 'vpp:backup {--keep=7 : Số ngày lưu trữ các bản sao lưu cũ} {--dry-run : Chỉ kiểm tra không thực thi ghi đĩa}';

    protected $description = 'Sao lưu an toàn cơ sở dữ liệu và dọn dẹp các bản sao lưu cũ';

    public function handle(): int
    {
        $this->info('🚀 Bắt đầu tiến trình sao lưu cơ sở dữ liệu...');

        $backupDir = storage_path('app/backups');
        if (!File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = now()->format('Ymd_His');
        $connection = config('database.default');
        $isDryRun = $this->option('dry-run');

        $this->line("• Kết nối cơ sở dữ liệu: <comment>{$connection}</comment>");

        if ($connection === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            if ($dbPath && File::exists($dbPath)) {
                $backupFile = "{$backupDir}/backup_{$timestamp}.sqlite";
                if (!$isDryRun) {
                    File::copy($dbPath, $backupFile);
                }
                $size = !$isDryRun && File::exists($backupFile) ? round(File::size($backupFile) / 1024, 2) . ' KB' : 'N/A';
                $this->info("✓ Đã sao lưu SQLite: {$backupFile} ({$size})");
            } else {
                // In-memory or temporary SQLite database (e.g. during test suite)
                $backupFile = "{$backupDir}/backup_{$timestamp}.sql";
                if (!$isDryRun) {
                    $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                    $dump = "-- VPP SQLite In-Memory Backup {$timestamp}\n";
                    foreach ($tables as $table) {
                        $tableName = $table->name;
                        $create = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$tableName]);
                        if ($create && !empty($create->sql)) {
                            $dump .= "\n{$create->sql};\n";
                            $rows = DB::table($tableName)->get();
                            foreach ($rows as $row) {
                                $data = (array) $row;
                                $keys = implode('`, `', array_keys($data));
                                $values = implode("', '", array_map(fn ($v) => addslashes((string) $v), array_values($data)));
                                $dump .= "INSERT INTO `{$tableName}` (`{$keys}`) VALUES ('{$values}');\n";
                            }
                        }
                    }
                    File::put($backupFile, $dump);
                }
                $this->info("✓ Đã sao lưu cấu trúc & dữ liệu: {$backupFile}");
            }
        } elseif ($connection === 'mysql') {
            $host = config('database.connections.mysql.host');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $port = config('database.connections.mysql.port', 3306);
            $backupFile = "{$backupDir}/backup_{$timestamp}.sql";

            if (!$isDryRun) {
                // Try native mysqldump command
                $command = sprintf(
                    'mysqldump --host=%s --port=%s --user=%s %s %s > %s',
                    escapeshellarg($host),
                    escapeshellarg((string) $port),
                    escapeshellarg($username),
                    $password ? '--password=' . escapeshellarg($password) : '',
                    escapeshellarg($database),
                    escapeshellarg($backupFile)
                );

                $returnVar = null;
                @exec($command, $output, $returnVar);

                // Fallback to PHP PDO dump if mysqldump binary is not available
                if ($returnVar !== 0 || !File::exists($backupFile) || File::size($backupFile) === 0) {
                    $dump = "-- VPP MySQL Dump Fallback {$timestamp}\n-- Database: {$database}\n\n";
                    $tables = DB::select('SHOW TABLES');
                    $tableKey = 'Tables_in_' . $database;

                    foreach ($tables as $t) {
                        $tableName = $t->$tableKey ?? current((array) $t);
                        $create = DB::selectOne("SHOW CREATE TABLE `{$tableName}`");
                        $createKey = 'Create Table';
                        if ($create && isset($create->$createKey)) {
                            $dump .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                            $dump .= $create->$createKey . ";\n\n";
                            $rows = DB::table($tableName)->get();
                            foreach ($rows as $row) {
                                $data = (array) $row;
                                if (!empty($data)) {
                                    $keys = implode('`, `', array_keys($data));
                                    $values = implode("', '", array_map(fn ($v) => addslashes((string) $v), array_values($data)));
                                    $dump .= "INSERT INTO `{$tableName}` (`{$keys}`) VALUES ('{$values}');\n";
                                }
                            }
                            $dump .= "\n";
                        }
                    }
                    File::put($backupFile, $dump);
                }
            }

            $size = !$isDryRun && File::exists($backupFile) ? round(File::size($backupFile) / 1024, 2) . ' KB' : 'N/A';
            $this->info("✓ Đã sao lưu MySQL: {$backupFile} ({$size})");
        } else {
            $this->warn("⚠️ Trình điều khiển cơ sở dữ liệu '{$connection}' chưa được cấu hình xuất dữ liệu đặc thù.");
        }

        // Dọn dẹp bản sao lưu cũ theo retention policy
        $keepDays = (int) $this->option('keep');
        $cleaned = 0;
        if ($keepDays > 0 && !$isDryRun) {
            $threshold = now()->subDays($keepDays)->timestamp;
            $files = File::files($backupDir);
            foreach ($files as $file) {
                if ($file->getMTime() < $threshold) {
                    File::delete($file->getRealPath());
                    $cleaned++;
                }
            }
        }

        $this->info("🧹 Dọn dẹp bản sao lưu cũ: Đã xóa {$cleaned} tệp tin cũ hơn {$keepDays} ngày.");
        $this->info('✅ Hoàn tất tiến trình sao lưu.');

        return self::SUCCESS;
    }
}
