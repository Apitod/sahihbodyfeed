@extends('layouts.app')

@section('title', 'Sistem Repeat Order')

@section('content')
<div class="row row-cards justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Formulir Repeat Order (RO)</h3>
            </div>
            <form action="{{ route('agent.repeat_order.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Biaya Repeat Order: Rp {{ number_format((float)$amount, 0, ',', '.') }}</strong><br>
                        Silakan transfer nominal di atas dan unggah bukti transfer.
                        Setelah diverifikasi Admin, Anda akan mendapatkan tambahan +1 Poin.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Bukti Transfer</label>
                        <input type="file" name="proof_of_payment" class="form-control" accept="image/*" required>
                        <small class="form-hint">Maksimal 2MB. Format: JPG, PNG.</small>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">Ajukan Repeat Order</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
