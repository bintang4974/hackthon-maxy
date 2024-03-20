@extends('layouts.app')

@section('content')
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <img src="{{ asset('assets/rumah.jpg') }}" alt="Product Image" class="img-fluid" />
                </div>
                <div class="col-lg-6">
                    {{-- <h2 class="font-weight-bold text-primary">{{ $product->name }}</h2>
                    <div class="price mb-2"><span>Rp{{ number_format($product->price, 0, ',', '.') }}</span></div>
                    <p>{{ $product->description }}</p>
                    <a href="#" class="btn btn-primary">Bayar</a> --}}

                    {{-- <img src="{{ asset('assets/rumah.jpg') }}" class="card-img-top" alt="..."> --}}
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text">{{ $product->description }}</p>
                        <form action="/checkout" method="post">
                            @csrf
                            <div class="form-group">
                                <label>No Ref</label>
                                <input type="text" name="no_reference" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" name="address" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Location</label>
                                <input type="text" name="location" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Developer</label>
                                <input type="text" name="developer" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Price</label>
                                <input type="text" name="price" class="form-control" value="{{ $product->price }}"
                                    readonly>
                            </div>
                            <hr>

                            <button type="submit" class="btn btn-primary">Checkout</button>
                        </form>
                        {{-- <a href="#" class="btn btn-primary">Go somewhere</a> --}}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
