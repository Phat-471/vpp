<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BackupCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_backup_command_executes_successfully_and_creates_backup_file(): void
    {
        $backupDir = storage_path('app/backups');

        $this->artisan('vpp:backup', ['--keep' => 7])
            ->expectsOutputToContain('Bắt đầu tiến trình sao lưu cơ sở dữ liệu')
            ->expectsOutputToContain('Hoàn tất tiến trình sao lưu')
            ->assertSuccessful();

        $this->assertTrue(File::isDirectory($backupDir));

        $files = File::files($backupDir);
        $this->assertNotEmpty($files);

        $latest = end($files);
        $this->assertStringContainsString('backup_', $latest->getFilename());
        $this->assertGreaterThan(0, File::size($latest->getRealPath()));
    }

    public function test_backup_command_dry_run_does_not_fail(): void
    {
        $this->artisan('vpp:backup', ['--dry-run' => true])
            ->expectsOutputToContain('Bắt đầu tiến trình sao lưu cơ sở dữ liệu')
            ->assertSuccessful();
    }

    public function test_backup_command_cleans_up_old_files(): void
    {
        $backupDir = storage_path('app/backups');
        if (!File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        // Tạo 1 file cũ hơn 10 ngày
        $oldFile = "{$backupDir}/backup_20200101_000000.sql";
        File::put($oldFile, '-- Old backup file');
        touch($oldFile, now()->subDays(10)->timestamp);

        $this->assertTrue(File::exists($oldFile));

        $this->artisan('vpp:backup', ['--keep' => 7])
            ->assertSuccessful();

        // File cũ hơn 7 ngày phải bị xóa
        $this->assertFalse(File::exists($oldFile));
    }
}
