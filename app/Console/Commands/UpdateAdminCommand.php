<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class UpdateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:update 
                            {email? : Email tài khoản admin cần cập nhật (mặc định lấy tài khoản admin đầu tiên)}
                            {--name= : Họ và tên mới}
                            {--new-email= : Địa chỉ email mới}
                            {--password= : Mật khẩu mới}
                            {--phone= : Số điện thoại mới}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cập nhật thông tin tài khoản Quản Trị Viên (Tên, Email, Mật khẩu, SĐT) qua dòng lệnh';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');

        $query = User::query();
        if ($email) {
            $user = $query->where('email', $email)->first();
        } else {
            $user = $query->where('role', 'admin')->first() ?? $query->first();
        }

        if (!$user) {
            $this->error('Không tìm thấy tài khoản quản trị nào trong hệ thống!');
            return Command::FAILURE;
        }

        $this->info("Đang thao tác trên tài khoản: [ID: {$user->id}] {$user->name} ({$user->email}) - Vai trò: {$user->role}");

        $updated = false;

        $name = $this->option('name');
        if ($name) {
            $user->name = $name;
            $updated = true;
            $this->line("-> Đã đổi tên: {$name}");
        }

        $newEmail = $this->option('new-email');
        if ($newEmail) {
            $exists = User::where('email', $newEmail)->where('id', '!=', $user->id)->exists();
            if ($exists) {
                $this->error("Email '{$newEmail}' đã được tài khoản khác sử dụng!");
                return Command::FAILURE;
            }
            $user->email = $newEmail;
            $updated = true;
            $this->line("-> Đã đổi email: {$newEmail}");
        }

        $password = $this->option('password');
        if ($password) {
            $user->password = Hash::make($password);
            $updated = true;
            $this->line("-> Đã đổi mật khẩu mới thành công");
        }

        $phone = $this->option('phone');
        if ($phone) {
            $user->phone = $phone;
            $updated = true;
            $this->line("-> Đã đổi số điện thoại: {$phone}");
        }

        if (!$updated) {
            $this->warn('Không có tham số nào được truyền vào để cập nhật.');
            $this->line('Ví dụ sử dụng:');
            $this->line('  php artisan admin:update --password=MatKhauMoi123');
            $this->line('  php artisan admin:update admin@vpp.local --name="Phát Quản Trị" --password=MatKhauMoi123');
            $this->line('  php artisan admin:update --name="Phát Quản Trị" --new-email="phat@cuahang.com" --password=Secret123');
            return Command::INVALID;
        }

        $user->save();
        $this->info("✓ Cập nhật tài khoản [{$user->email}] thành công!");

        return Command::SUCCESS;
    }
}
