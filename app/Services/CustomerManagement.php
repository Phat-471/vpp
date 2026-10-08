<?php

namespace App\Services;

use App\Models\Customer;
use App\Support\ContactFormat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class CustomerManagement
{
    public function save(array $input, ?int $id = null): Customer
    {
        Gate::authorize($id ? 'update' : 'create', $id ? Customer::findOrFail($id) : Customer::class);
        $data = [];
        foreach (['name', 'phone', 'email', 'address', 'notes'] as $field) {
            $value = $input[$field] ?? '';
            $data[$field] = is_string($value) ? trim($value) : $value;
        }
        if (is_string($data['phone'])) {
            $data['phone'] = ContactFormat::phone($data['phone']);
        }
        $data = Validator::make($data, [
            'name' => ['required', 'string', 'min:2', 'max:100', 'regex:/\p{L}/u'],
            'phone' => ['required', 'string', 'regex:'.ContactFormat::PHONE_PATTERN],
            'email' => ['nullable', 'string', 'email:rfc', 'max:150', 'regex:/^[^\s@]+@[^\s@]+\.[^\s@]+$/u'],
            'address' => ['nullable', 'string', 'min:5', 'max:255', 'regex:/\p{L}/u'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'required' => 'Vui lòng nhập :attribute.',
            'min' => ':attribute phải có ít nhất :min ký tự.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'regex' => ':attribute không đúng định dạng.',
            'email' => 'Email không đúng định dạng.',
            'string' => ':attribute phải là văn bản.',
        ], ['name' => 'tên khách hàng', 'phone' => 'số điện thoại', 'email' => 'email', 'address' => 'địa chỉ', 'notes' => 'ghi chú'])->validate();

        // Serialize contact writes; legacy rows may contain formatted phone numbers.
        return Cache::lock('customers.contact-write', 10)->block(3, function () use ($data, $id) {
            return DB::transaction(function () use ($data, $id) {
                $customer = $id ? Customer::lockForUpdate()->findOrFail($id) : new Customer;
                Gate::authorize($id ? 'update' : 'create', $id ? $customer : Customer::class);
                if ($id && ContactFormat::phone($customer->phone) !== $data['phone'] && $this->hasAccount($customer)) {
                    throw ValidationException::withMessages(['phone' => 'Không thể đổi SĐT của khách có tài khoản đăng nhập.']);
                }
                $duplicate = $this->phoneQuery($data['phone'])->when($id, fn ($q) => $q->whereKeyNot($id))->exists();
                if ($duplicate) {
                    throw ValidationException::withMessages(['phone' => 'SĐT đã có trong danh sách. Hãy chọn hoặc sửa khách hàng hiện có.']);
                }
                foreach (['email', 'address', 'notes'] as $field) {
                    $data[$field] = $data[$field] ?: null;
                }
                $customer->fill($data)->save();

                return $customer;
            });
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $customer = Customer::lockForUpdate()->findOrFail($id);
            Gate::authorize('delete', $customer);
            $this->assertDeletable($customer);
            $customer->delete();
        });
    }

    public function findByPhone(string $phone): ?Customer
    {
        $customers = $this->phoneQuery(ContactFormat::phone($phone))->lockForUpdate()->limit(2)->get();
        if ($customers->count() > 1) {
            throw ValidationException::withMessages(['customer_phone' => 'Có nhiều khách dùng cùng SĐT. Quản trị viên cần kiểm tra trước khi bán.']);
        }

        return $customers->first();
    }

    private function phoneQuery(string $phone): Builder
    {
        $normalizedSql = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '.', ''), '-', ''), '(', ''), ')', ''), '+', '')";

        return Customer::whereIn(DB::raw($normalizedSql), [$phone, '84'.substr($phone, 1)]);
    }

    public function assertDeletable(Customer $customer): void
    {
        if ($this->hasAccount($customer) || $customer->orders()->exists() || $customer->repairTickets()->exists()
            || $customer->zaloVerifications()->exists() || (string) $customer->debt_balance !== '0.00') {
            throw ValidationException::withMessages(['customer' => 'Không thể xóa khách đã có giao dịch, công nợ hoặc tài khoản đăng nhập.']);
        }
    }

    private function hasAccount(Customer $customer): bool
    {
        return (bool) ($customer->password || $customer->phone_verified_at || $customer->zalo_id);
    }
}
