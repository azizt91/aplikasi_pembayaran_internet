<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Http\Controllers\Payment\TripayController;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function show($reference)
    {
        $tripay = new TripayController();
        $detail = $tripay->detailTransaction($reference);
        return view('transaction.show', compact('detail'));
    }

    public function store(Request $request)
    {
        $tagihan = Tagihan::find($request->id);
        $method = $request->method;

        $tripay = new TripayController();
        $transaction = $tripay->requestTransaction($method, $tagihan);

        // Periksa jika $transaction mengandung properti reference
        if (isset($transaction->reference)) {
            $tagihan->reference = $transaction->reference;
            $tagihan->save();
            
            // Jika ada URL redirect, arahkan ke sana
            if (isset($transaction->checkout_url)) {
                return redirect()->away($transaction->checkout_url);
            }

            return redirect()->route('transaction.show', [
                'reference' => $transaction->reference,
            ]);
        } elseif (is_string($transaction)) {
            return redirect()->back()->with('error', 'Transaksi gagal: ' . $transaction);
        } else {
            return redirect()->back()->with('error', 'Transaksi gagal, reference tidak ditemukan');
        }
    }
}
