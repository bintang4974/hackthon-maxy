<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <h1>Hello, world!</h1>
        <div class="card" style="width: 18rem;">
            <img src="{{ asset('assets/rumah.jpg') }}" class="card-img-top" alt="...">
            <div class="card-body">
                <h5 class="card-title">Perum Green Field</h5>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the
                    card's
                    content.</p>
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
                        <input type="number" name="price" class="form-control">
                    </div>
                    <hr>

                    <button type="submit" class="btn btn-primary">Checkout</button>
                </form>
                {{-- <a href="#" class="btn btn-primary">Go somewhere</a> --}}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>
