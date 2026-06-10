<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }

    .page { padding: 30px 40px; }
    .page-1 { page-break-after: always; }

    /* Header */
    .header { display: table; width: 100%; margin-bottom: 30px; }
    .header-logo { display: table-cell; width: 120px; vertical-align: top; }
    .logo-box { background: #f5a623; padding: 8px 10px; text-align: center; font-weight: 900; font-size: 14px; color: white; line-height: 1.3; width: 90px; }
    .logo-box span { display: block; font-size: 11px; }
    .header-info { display: table-cell; vertical-align: top; padding-left: 10px; font-size: 10px; color: #555; line-height: 1.6; }
    .company-name { font-size: 16px; font-weight: bold; color: #222; margin-bottom: 2px; }

    /* Title banner */
    .title-banner { background: #f5a623; text-align: center; padding: 10px; font-weight: bold; font-size: 14px; letter-spacing: 1px; margin-bottom: 20px; color: #222; }

    /* Quotation meta */
    .meta { margin-bottom: 20px; font-size: 11px; }
    .meta div { margin-bottom: 4px; }
    .meta .customer-name { font-weight: bold; font-size: 13px; margin-bottom: 6px; }
    .meta-row { display: table; width: 100%; }
    .meta-left { display: table-cell; }
    .meta-right { display: table-cell; text-align: right; }

    /* Section header */
    .section-header { background: #f5a623; text-align: center; padding: 6px; font-weight: bold; font-size: 11px; margin-bottom: 0; }

    /* Tables */
    table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 11px; }
    td, th { border: 1px solid #ddd; padding: 7px 10px; }
    .plan-table td:last-child { text-align: right; font-weight: bold; }
    .duration-table th { background: #f5a623; font-weight: bold; text-align: center; }
    .duration-table td { text-align: center; }
    .duration-table td:last-child { font-weight: bold; }

    /* Financial table */
    .fin-wrap { width: 55%; margin-left: auto; margin-bottom: 15px; }
    .fin-table { width: 100%; border-collapse: collapse; font-size: 11px; }
    .fin-table td { border: none; padding: 5px 10px; }
    .fin-table .fin-label { color: #f5a623; text-align: right; }
    .fin-table .fin-value { font-weight: bold; border: 1px solid #eee; text-align: right; }
    .fin-table .fin-total-label { font-weight: bold; text-align: right; background: #f5a623; color: #fff; padding: 7px 10px; }
    .fin-table .fin-total-value { font-weight: bold; font-size: 13px; border: 2px solid #f5a623; text-align: right; }

    /* Disclaimer */
    .disclaimer { font-size: 9px; color: #555; font-style: italic; margin-top: 10px; line-height: 1.5; }

    /* Page 2 */
    .p2-title { font-style: italic; text-decoration: underline; font-weight: bold; margin-bottom: 15px; font-size: 12px; }
    .ref-line { font-weight: bold; margin-bottom: 15px; font-size: 12px; }
    .accept-text { margin-bottom: 20px; font-size: 11px; }
    .sig-box { border: 1px solid #999; width: 200px; height: 80px; margin-bottom: 10px; }
    .sig-label { font-weight: bold; margin-bottom: 12px; font-size: 11px; }

    /* Signature lines — table layout, border-bottom only, no underscores */
    .sig-lines { border: none; width: auto; margin-bottom: 0; }
    .sig-lines td { border: none; padding: 0 0 8px 0; font-size: 11px; vertical-align: bottom; }
    .sig-lines .sig-field-label { white-space: nowrap; padding-right: 6px; }
    .sig-lines .sig-field-line { border-bottom: 1px solid #555; width: 200px; }

    .agent-section { margin-top: 30px; font-size: 11px; line-height: 1.8; }
</style>
</head>
<body>

{{-- PAGE 1 --}}
<div class="page page-1">
    {{-- Header --}}
    <div class="header">
        <div class="header-logo">
            <img src="{{ public_path('images/logo.svg') }}" alt="Smart Rental" style="width:90px;height:auto;">
        </div>
        <div class="header-info">
            <div class="company-name">LINEAR CHANNEL SDN BHD <span style="font-weight:normal;font-size:12px;">(489598-X)</span></div>
            Lot 8-9, Level 8, Wisma Trax<br>
            Jln Lima, Off, Jalan Chan Sow Lin,<br>
            55200 Kuala Lumpur<br>
            Tel: 03-8084 4231 &nbsp;|&nbsp; Website: www.smartrental.asia
        </div>
    </div>

    {{-- Title --}}
    <div class="title-banner">FLEXI RENTAL - QUOTATION</div>

    {{-- Meta --}}
    <div class="meta">
        <div>Quotation No: <strong>{{ $quotation->quotation_no }}</strong></div>
        <div>Issue Date : {{ $quotation->created_at->format('d M Y') }}</div>
        <br>
        <div class="customer-name">{{ $quotation->customer_name }}</div>
        <div class="meta-row">
            <div class="meta-left">
                Delivery Address: {{ $quotation->delivery_address }}<br>
                Contact Number: {{ $quotation->contact_number }}
            </div>
            <div class="meta-right">
                Email: {{ $quotation->email }}
            </div>
        </div>
    </div>

    {{-- Plan Details --}}
    <div class="section-header">PLAN DETAILS</div>
    <table class="plan-table">
        <tr>
            <td>
                <strong>PLAN : {{ $quotation->plan_name }}</strong><br>
                @if($quotation->plan_specs)
                <span style="color:#555;">{{ $quotation->plan_specs }}</span>
                @endif
            </td>
            <td style="width:100px;">{{ $quotation->quantity }} units</td>
        </tr>
    </table>

    {{-- Rental Duration --}}
    <div class="section-header">TENTATIVE RENTAL DURATION</div>
    <table class="duration-table">
        <tr>
            <th>Start Date:</th>
            <td>{{ $quotation->start_date->format('d M Y') }}</td>
            <th>End Date:</th>
            <td>{{ $quotation->end_date->format('d M Y') }}</td>
            <td><strong>{{ $quotation->total_days }} days</strong></td>
        </tr>
    </table>

    {{-- Financial --}}
    <div class="fin-wrap">
        <table class="fin-table">
            <tr>
                <td class="fin-label">Rental Fee <em>(Rate {{ number_format($quotation->rate_per_day, 2) }} / {{ $quotation->rate_type === 'weekly' ? 'week' : ($quotation->rate_type === 'monthly' ? 'month' : 'day') }})</em></td>
                <td class="fin-value">RM {{ number_format($quotation->rental_fee, 2) }}</td>
            </tr>
            <tr>
                <td class="fin-label">Delivery</td>
                <td class="fin-value">RM {{ number_format($quotation->delivery_fee, 2) }}</td>
            </tr>
            <tr>
                <td class="fin-label" style="font-size:9px;color:#aaa;">Taxable Subtotal (RM)</td>
                <td class="fin-value" style="font-size:10px;color:#777;">RM {{ number_format($quotation->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td class="fin-label">Tax ({{ number_format($quotation->tax_percent, 0) }}%)</td>
                <td class="fin-value">RM {{ number_format($quotation->tax_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="fin-label">Deposit <em style="font-size:9px;">(No SST)</em></td>
                <td class="fin-value">RM {{ number_format($quotation->deposit_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="fin-total-label">Total Payable Amount<br>(RM)</td>
                <td class="fin-total-value">RM {{ number_format($quotation->total_payable, 2) }}</td>
            </tr>
        </table>
    </div>

    {{-- Disclaimer --}}
    <div class="disclaimer">
        You acknowledge that by accepting this quote, cancelling or ending the contract early will not result in a refund for
        any unused days. Any refund is subject to the unit's condition and will be processed within 14 days of its return, with
        damage charges applicable.
    </div>
</div>

{{-- PAGE 2 --}}
<div class="page">
    <div class="p2-title">For Client Only</div>
    <div class="ref-line">Ref. No.: {{ $quotation->quotation_no }}</div>
    <div class="accept-text">We hereby accept the above quotation based on the terms and conditions stated herein.</div>

    <div class="sig-box"></div>
    <div class="sig-label">Authorized Signature &amp; Company's Chop</div>

    <table class="sig-lines">
        <tr>
            <td class="sig-field-label">Name &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
            <td class="sig-field-line">&nbsp;</td>
        </tr>
        <tr>
            <td class="sig-field-label">Designation &nbsp;:</td>
            <td class="sig-field-line">&nbsp;</td>
        </tr>
        <tr>
            <td class="sig-field-label">Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
            <td class="sig-field-line">&nbsp;</td>
        </tr>
    </table>

    @if($quotation->agent_name)
    <div class="agent-section">
        <div style="margin-bottom:8px;">Quotation requested by:</div>
        <div>Agent Name: <em>{{ $quotation->agent_name }}</em></div>
        @if($quotation->agent_contact)
        <div>Agent Contact: <em>{{ $quotation->agent_contact }}</em></div>
        @endif
        @if($quotation->agent_email)
        <div>Agent Email: <em>{{ $quotation->agent_email }}</em></div>
        @endif
    </div>
    @endif
</div>

{{-- DomPDF page numbers via canvas script --}}
<script type="text/php">
    if (isset($pdf)) {
        $font = $fontMetrics->getFont("Arial");
        $pdf->page_text(535, 822, "{PAGE_NUM}", $font, 9, [0.6, 0.6, 0.6]);
    }
</script>

</body>
</html>
