<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Faq;

class FaqController
{
    public function index()
    {
        try {
            // Hanya menarik FAQ yang statusnya aktif
            $faqs = Faq::where('is_active', true)
                       ->orderBy('created_at', 'desc')
                       ->get();

            return response()->json([
                'status' => 'success',
                'data' => $faqs
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Gagal menarik data panduan bantuan'
            ], 500);
        }
    }
}