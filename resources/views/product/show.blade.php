@extends('layouts.app')

@section('content')
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <img src="{{ asset('images/products/' . $product->image_url) }}" alt="Product Image" class="img-fluid" />
                </div>
                <div class="col-lg-6">
                    <h2 class="font-weight-bold text-primary">{{ $product->name }}</h2>
                    <div class="price mb-2"><span>Rp{{ number_format($product->price, 0, ',', '.') }}</span></div>
                    <p>{{ $product->description }}</p>
                    <a href="#" class="btn btn-primary">Bayar</a>
                </div>
            </div>
        </div>
    </div>
@endsection
