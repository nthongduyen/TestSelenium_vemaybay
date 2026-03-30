<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BaiVietController extends Controller
{
    /**
     * POST /api/bai-viet - Xử lý tạo mới bài viết
     */
    public function store(Request $request)
    {
        // TC_BV_01: Validation bắt buộc Tiêu đề và Nội dung
        $validatedData = $request->validate([
            'id_danh_muc' => 'required|exists:danh_muc_bai_viet,id',
            'id_tac_gia' => 'required|exists:nguoi_dung,id',
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'trang_thai' => 'required|in:0,1', // 0: Nháp, 1: Công khai/Lên lịch
            'ngay_xuat_ban' => 'nullable|date',
            // luot_xem không cần validation vì nó là trường chỉ đọc
        ]);

        // TC_BV_02: Tạo Slug tự động
        $validatedData['slug'] = Str::slug($validatedData['tieu_de']);
        $validatedData['luot_xem'] = 0; // Đặt lượt xem ban đầu là 0

        // Xử lý logic Ngày xuất bản nếu trạng thái là công khai
        if ($validatedData['trang_thai'] == 1 && empty($validatedData['ngay_xuat_ban'])) {
            // TC_BV_03: Chuyển sang công khai ngay lập tức
            $validatedData['ngay_xuat_ban'] = now();
        }

        $baiViet = BaiViet::create($validatedData);

        return response()->json([
            'message' => 'Tạo bài viết thành công!',
            'data' => $baiViet
        ], 201); // Trả về 201 Created
    }

    /**
     * PUT/PATCH /api/bai-viet/{id} - Xử lý cập nhật bài viết
     */
    public function update(Request $request, BaiViet $baiViet)
    {
        $validatedData = $request->validate([
            'tieu_de' => 'sometimes|required|string|max:255',
            'noi_dung' => 'sometimes|required|string',
            'trang_thai' => 'sometimes|required|in:0,1',
            'ngay_xuat_ban' => 'nullable|date',
            'id_danh_muc' => 'sometimes|required|exists:danh_muc_bai_viet,id',
        ]);

        // Kiểm tra và xử lý logic Ngày xuất bản
        if (isset($validatedData['trang_thai']) && $validatedData['trang_thai'] == 1) {

            // Nếu chuyển sang Công khai (1) và không có ngày xuất bản cụ thể
            if (!isset($validatedData['ngay_xuat_ban']) || is_null($validatedData['ngay_xuat_ban'])) {
                // TC_BV_03: Xuất bản ngay lập tức
                $validatedData['ngay_xuat_ban'] = now();
            }
            // TC_BV_04: Nếu có ngay_xuat_ban (tương lai), giữ nguyên giá trị đó (Lên lịch)
        }

        // Cập nhật Slug nếu tiêu đề thay đổi
        if (isset($validatedData['tieu_de'])) {
            $validatedData['slug'] = Str::slug($validatedData['tieu_de']);
        }

        // TC_BV_07: Đảm bảo trường luot_xem KHÔNG BAO GIỜ được cập nhật
        // Mặc dù đã loại khỏi $fillable trong Model, đây là lớp bảo vệ thứ cấp
        if ($request->has('luot_xem')) {
            // Loại bỏ luot_xem khỏi mảng $validatedData trước khi cập nhật
            unset($validatedData['luot_xem']);
        }

        // Cập nhật Model bằng dữ liệu đã được validate và tính toán
        // FIX: Đảm bảo sử dụng $validatedData thay vì $request->except(['luot_xem'])
        $baiViet->update($validatedData);

        return response()->json([
            'message' => 'Cập nhật bài viết thành công!',
            'data' => $baiViet->fresh()
        ], 200);
    }

    /**
     * GET /api/bai-viet - Xem danh sách và tìm kiếm
     */
    public function index(Request $request)
    {
        $query = BaiViet::query();

        // TC_BV_05: Tìm kiếm theo tiêu đề (keyword)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where('tieu_de', 'like', "%{$keyword}%");
        }

        $baiViets = $query->paginate(10);

        return response()->json([
            'data' => $baiViets->items(),
            'pagination' => [
                'total' => $baiViets->total(),
            ]
        ], 200);
    }

    /**
     * DELETE /api/bai-viet/{id} - Xóa bài viết
     */
    public function destroy(BaiViet $baiViet)
    {
        // TC_BV_06: Xóa bài viết
        $baiViet->delete();

        return response()->json([
            'message' => 'Xóa bài viết thành công!'
        ], 200);
    }
}
