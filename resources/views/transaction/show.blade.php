@extends('layout.app')

@section('contents')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detail Pembayaran</h6>
        </div>
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <!-- Transaction Detail -->
                <div class="mb-3 mb-md-0">
                    <h4 class="font-weight-bold">{{ rupiah($detail->amount) }}</h4>
                    <span class="badge badge-danger">{{ $detail->status }}</span>
                </div>
                <!-- Transaction ID -->
                <div class="text-left text-md-right">
                    <small class="text-muted">Transaction Detail</small>
                    <p class="font-weight-bold">#{{ $detail->reference }}</p>
                </div>
            </div>
            @if(isset($detail->qr_url))
            <!-- QR Code Section -->
            <div class="mt-4">
                <h6 class="text-muted">Scan QR Code</h6>
                <img src="{{ $detail->qr_url }}" alt="QR Code">
            </div>
            @elseif(isset($detail->checkout_url))
            <!-- Redirect URL Section -->
            <div class="mt-4">
                <h6 class="text-muted">Redirect to Payment</h6>
                <a href="{{ $detail->checkout_url }}" class="btn btn-primary">Bayar Sekarang</a>
            </div>
            @endif
            <!-- Instruction Section -->
            <div class="mt-4">
                <h6 class="text-muted">Instruction</h6>
                <div class="accordion" id="paymentInstruction">
                    @foreach($detail->instructions as $index => $instruction)
                        <div class="card">
                            <div class="card-header" id="heading{{ $index }}">
                                <h5 class="mb-0">
                                    <button class="btn btn-link {{ $index == 0 ? '' : 'collapsed' }}" type="button" data-toggle="collapse" data-target="#collapse{{ $index }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                                        {{ $instruction->title }}
                                    </button>
                                </h5>
                            </div>
                            <div id="collapse{{ $index }}" class="collapse {{ $index == 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index }}" data-parent="#paymentInstruction">
                                <div class="card-body">
                                    <!-- Instruction content for each type (Internet Banking, ATM, etc.) -->
                                    <ul>
                                        @foreach($instruction->steps as $step)
                                            <li>{!! $step !!}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection


