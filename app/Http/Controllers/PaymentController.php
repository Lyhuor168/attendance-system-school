<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        return view('payments.index', [
            'payments' => Payment::with('student')->latest('paid_at')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('payments.create', [
            'students' => Student::orderBy('name')->get(),
        ]);
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        Payment::create([
            ...$request->validated(),
            'recorded_by' => $request->user()->id,
        ]);

        return redirect()->route('payments.index')->with('status', 'Payment recorded.');
    }
}
