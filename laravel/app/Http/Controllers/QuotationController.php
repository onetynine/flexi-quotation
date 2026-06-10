<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Plan;
use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = Quotation::latest()->paginate(20);
        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        $plans     = Plan::where('active', true)->get();
        $customers = Customer::orderBy('name')->get();
        return view('quotations.create', compact('plans', 'customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'      => 'nullable|exists:customers,id',
            'customer_name'    => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'contact_number'   => 'nullable|string|max:50',
            'delivery_address' => 'nullable|string|max:500',
            'plan_id'          => 'nullable|exists:plans,id',
            'plan_name'        => 'required|string|max:255',
            'plan_specs'       => 'nullable|string|max:500',
            'is_custom_plan'   => 'boolean',
            'quantity'         => 'required|integer|min:1',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'delivery_option'  => 'required|string|max:100',
            'delivery_fee'     => 'required|numeric|min:0',
            'rate_per_day'     => 'required|numeric|min:0',
            'rate_type'        => 'required|in:daily,weekly,monthly',
            'deposit_option'   => 'required|string',
            'deposit_amount'   => 'required|numeric|min:0',
            'tax_percent'      => 'required|numeric|min:0',
            'agent_name'       => 'nullable|string|max:255',
            'agent_contact'    => 'nullable|string|max:50',
            'agent_email'      => 'nullable|email|max:255',
            'quotation_link'   => 'nullable|string|max:500',
            'status'           => 'required|string',
        ]);

        $start = Carbon::parse($data['start_date']);
        $end   = Carbon::parse($data['end_date']);
        $totalDays    = $start->diffInDays($end) + 1;
        $billingUnits = match($data['rate_type']) {
            'weekly'  => (int) ceil($totalDays / 7),
            'monthly' => (int) ceil($totalDays / 30),
            default   => $totalDays,
        };
        $rentalFee  = round($data['rate_per_day'] * $data['quantity'] * $billingUnits, 2);
        $subtotal   = round($rentalFee + $data['delivery_fee'], 2);
        $taxAmount  = round($subtotal * ($data['tax_percent'] / 100), 2);
        $total      = round($subtotal + $taxAmount + $data['deposit_amount'], 2);

        Quotation::create(array_merge($data, [
            'quotation_no' => Quotation::generateNumber(),
            'total_days'   => $totalDays,
            'rental_fee'   => $rentalFee,
            'tax_amount'   => $taxAmount,
            'subtotal'     => $subtotal,
            'total_payable'=> $total,
            'is_custom_plan' => $request->boolean('is_custom_plan'),
        ]));

        return redirect()->route('quotations.index')->with('success', 'Quotation created.');
    }

    public function show(Quotation $quotation)
    {
        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $plans     = Plan::where('active', true)->get();
        $customers = Customer::orderBy('name')->get();
        return view('quotations.edit', compact('quotation', 'plans', 'customers'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $data = $request->validate([
            'customer_id'      => 'nullable|exists:customers,id',
            'customer_name'    => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'contact_number'   => 'nullable|string|max:50',
            'delivery_address' => 'nullable|string|max:500',
            'plan_id'          => 'nullable|exists:plans,id',
            'plan_name'        => 'required|string|max:255',
            'plan_specs'       => 'nullable|string|max:500',
            'is_custom_plan'   => 'boolean',
            'quantity'         => 'required|integer|min:1',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'delivery_option'  => 'required|string|max:100',
            'delivery_fee'     => 'required|numeric|min:0',
            'rate_per_day'     => 'required|numeric|min:0',
            'rate_type'        => 'required|in:daily,weekly,monthly',
            'deposit_option'   => 'required|string',
            'deposit_amount'   => 'required|numeric|min:0',
            'tax_percent'      => 'required|numeric|min:0',
            'agent_name'       => 'nullable|string|max:255',
            'agent_contact'    => 'nullable|string|max:50',
            'agent_email'      => 'nullable|email|max:255',
            'quotation_link'   => 'nullable|string|max:500',
            'status'           => 'required|string',
        ]);

        $start = Carbon::parse($data['start_date']);
        $end   = Carbon::parse($data['end_date']);
        $totalDays    = $start->diffInDays($end) + 1;
        $billingUnits = match($data['rate_type']) {
            'weekly'  => (int) ceil($totalDays / 7),
            'monthly' => (int) ceil($totalDays / 30),
            default   => $totalDays,
        };
        $rentalFee  = round($data['rate_per_day'] * $data['quantity'] * $billingUnits, 2);
        $subtotal   = round($rentalFee + $data['delivery_fee'], 2);
        $taxAmount  = round($subtotal * ($data['tax_percent'] / 100), 2);
        $total      = round($subtotal + $taxAmount + $data['deposit_amount'], 2);

        $quotation->update(array_merge($data, [
            'total_days'    => $totalDays,
            'rental_fee'    => $rentalFee,
            'tax_amount'    => $taxAmount,
            'subtotal'      => $subtotal,
            'total_payable' => $total,
            'is_custom_plan'=> $request->boolean('is_custom_plan'),
        ]));

        return redirect()->route('quotations.show', $quotation)->with('success', 'Quotation updated.');
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->delete();
        return redirect()->route('quotations.index')->with('success', 'Quotation deleted.');
    }

    public function pdf(Quotation $quotation)
    {
        $pdf = Pdf::loadView('quotations.pdf', compact('quotation'))
            ->setPaper('a4', 'portrait');
        return $pdf->download($quotation->quotation_no . '.pdf');
    }
}
