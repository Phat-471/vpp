<?php

namespace App\Http\Controllers;

use App\Exceptions\TaxLookupUnavailable;
use App\Http\Requests\BusinessTaxLookupRequest;
use App\Services\BusinessTaxLookup;

class BusinessTaxLookupController extends Controller
{
    public function __invoke(BusinessTaxLookupRequest $request, BusinessTaxLookup $lookup)
    {
        try {
            $data = $lookup->find($request->validated('tax_code'));

            return response()->json(['data' => $data, 'message' => $data ? 'Đã điền thông tin. Vui lòng kiểm tra lại trước khi đặt hàng.' : 'Không tìm thấy doanh nghiệp. Có thể nhập thông tin thủ công.'], $data ? 200 : 404);
        } catch (TaxLookupUnavailable $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }
    }
}
