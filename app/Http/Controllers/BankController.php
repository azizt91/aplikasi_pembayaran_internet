<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index()
    {
        
        $banks = Bank::all();
        return view('banks.index', compact('banks'));
    }
    
    public function create()
    {
        return view('banks.create');
    }
    
    
    public function store(Request $request)
    {
        $request->validate([
            'nama_bank' => 'required|string|max:255',
            'pemilik_rekening' => 'required|string|max:255',
            'nomor_rekening' => 'required|string|max:255',
            'url_icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = [
            'nama_bank' => $request->input('nama_bank'),
            'pemilik_rekening' => $request->input('pemilik_rekening'),
            'nomor_rekening' => $request->input('nomor_rekening'),
        ];

        if ($request->hasFile('url_icon')) {
            $image = $request->file('url_icon');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/icons', $imageName);
            $data['url_icon'] = 'icons/' . $imageName;
        }

        Bank::create($data);
        Alert::toast('Bank created successfully!', 'success');
        return redirect()->route('banks.index');
    }
    
    public function show(Bank $bank)
    {
        return view('banks.show', compact('bank'));
    }
    
    public function edit(Bank $bank)
    {
        return view('banks.edit', compact('bank'));
    }
    
    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'nama_bank' => 'required|string|max:255',
            'pemilik_rekening' => 'required|string|max:255',
            'nomor_rekening' => 'required|string|max:255',
            'url_icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $bank->nama_bank = $request->input('nama_bank');
        $bank->pemilik_rekening = $request->input('pemilik_rekening');
        $bank->nomor_rekening = $request->input('nomor_rekening');

        if ($request->hasFile('url_icon')) {
            if ($bank->url_icon) {
                Storage::delete('public/' . $bank->url_icon);
            }

            $image = $request->file('url_icon');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/icons', $imageName);
            $bank->url_icon = 'icons/' . $imageName;
        }

        $bank->save();
        Alert::toast('Bank updated successfully!', 'success');
        return redirect()->route('banks.index');
    }
    
    
    public function destroy(Bank $bank)
    {
        if ($bank->url_icon) {
            Storage::delete('public/' . $bank->url_icon);
        }

        $bank->delete();
        Alert::toast('Bank deleted successfully!', 'success');
        return redirect()->route('banks.index');
    }
}

