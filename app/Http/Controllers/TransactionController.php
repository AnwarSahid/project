<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp;


class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        return view('transaction.user_transaction', compact('user'));
    }

    public function Status()
    {
        $user = Auth::user();
        return view('transaction.status_transaction', compact('user'));
    }

    public function listRequest()
    {
        $transaction = Transaction::paginate(10);
        return view('transaction.list-transaction', compact('transaction'));
    }
    public function validation($id)
    {
        $transaction = Transaction::find($id);
        return view('transaction.validation', compact('transaction'));
    }

    public function showFile1($path)
    {
        $file = public_path('storage' . '/' . $path);
        $header = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $path . '"'
        ];
        return response()->file($file, $header);
        // $transaction = Transaction::all();
        // return view('transaction.iframe-file1', compact('transaction'));
    }
    public function showFile2($path2)
    {
        $file = public_path('storage' . '/' . $path2);
        $header = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $path2 . '"'
        ];
        return response()->file($file, $header);
    }
    public function showFile3($path3)
    {
        $file = public_path('storage' . '/' . $path3);
        $header = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $path3 . '"'
        ];
        return response()->file($file, $header);
    }

    public function validationAccept(Request $request, Transaction $transaction, User $user, $id)
    {

        $this->validate($request, [
            'status' => 'required',
            'message' => 'required',
        ]);

        $transaction = Transaction::find($id);

        $transaction->update([
            'status' => $request['status'],
        ]);


        $phone = $transaction->transactions->phone;

        $message = $request['message'];
        $client = new Client();

        $url = "https://app.whatspie.com/api/messages";

        $request = $client->post(
            $url,
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Bearer ' . 'uTusgUQmz2vRHtmU4Q8FIcHKV79fhjTifmhxAfjzHZDptUVaOP'
                ],
                'form_params' => [
                    'receiver' => $phone,
                    'device' => '62816514372',
                    'message' => $message,
                    'type' => 'chat'
                ]
            ]
        );

        return redirect()->route('request.list')->with('success', ' Data telah diperbaharui!');
    }

    public function validationreject(Request $request, Transaction $transaction, $id)
    {
        $this->validate($request, [
            'status' => 'required',
            'message' => 'required',
        ]);

        $transaction = Transaction::find($id);

        $transaction->update([
            'status' => $request['status'],
        ]);


        $phone = $transaction->transactions->phone;

        $message = $request['message'];
        $client = new Client();

        $url = "https://app.whatspie.com/api/messages";

        $request = $client->post(
            $url,
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Bearer ' . 'uTusgUQmz2vRHtmU4Q8FIcHKV79fhjTifmhxAfjzHZDptUVaOP'
                ],
                'form_params' => [
                    'receiver' => $phone,
                    'device' => '62816514372',
                    'message' => $message,
                    'type' => 'chat'
                ]
            ]
        );

        return redirect()->route('request.list')->with('success', ' Data telah diperbaharui!');
    }


    public function message($phone)
    {

        $client = new Client();

        $url = "https://app.whatspie.com/api/messages";

        $request = $client->post(
            $url,
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Bearer ' . 'uTusgUQmz2vRHtmU4Q8FIcHKV79fhjTifmhxAfjzHZDptUVaOP'
                ],
                'form_params' => [
                    'receiver' => $phone,
                    'device' => '62816514372',
                    'message' => 'cobaa',
                    'type' => 'chat'
                ]
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $attr = $request->validate([
            'file1' => 'required|mimes:csv,txt,xlx,xls,pdf|max:2048',
            'file2' => 'required|mimes:csv,txt,xlx,xls,pdf|max:2048',
            'file3' => 'required|mimes:csv,txt,xlx,xls,pdf|max:2048',
            'token' => 'required',
            'periode_wisuda' => 'required',
        ]);

        $attr['user_id'] = auth()->user()->id;
        $attr['file1'] = $this->storeFile($request->file('file1'), 'surat-rekomendasi');
        $attr['file2'] = $this->storeFile($request->file('file2'), 'surat-keterangan');
        $attr['file3'] = $this->storeFile($request->file('file3'), 'sk');

        Transaction::create($attr);

        return back()->with('message', 'Data berhasil ditambahkan');
    }

    public function downloadFile1(Request $request)
    {
        $attr = $request->validate([
            'file1' => 'required|mimes:csv,txt,xlx,xls,pdf|max:2048',
            'file2' => 'required|mimes:csv,txt,xlx,xls,pdf|max:2048',
            'file3' => 'required|mimes:csv,txt,xlx,xls,pdf|max:2048',
            'token' => 'required',
            'periode_wisuda' => 'required',
        ]);

        $attr['user_id'] = auth()->user()->id;
        $attr['file1'] = $this->storeFile($request->file('file1'), 'surat-rekomendasi');
        $attr['file2'] = $this->storeFile($request->file('file2'), 'surat-keterangan');
        $attr['file3'] = $this->storeFile($request->file('file3'), 'sk');

        Transaction::create($attr);

        return back()->with('message', 'Data berhasil ditambahkan');
    }





    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTransactionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTransactionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTransactionRequest  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction, $id)
    {
        $transaction = Transaction::find($id);
        $transaction->delete();
        return redirect()->route('request.list')->with('success', ' Data telah Dihapus!');
    }
}
