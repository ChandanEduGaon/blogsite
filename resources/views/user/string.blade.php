@extends('user.master')

@section('content')
    <div class="container mt-5">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('check_string') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="d-flex mb-4 align-items-center gap-3">

                <label for="csv">Enter String</label>
                <input type="text" placeholder="Enter string" name="string" required id="csv">
                <button type="submit" class="btn btn-info">Submit</button>
            </div> 
        </form>
    </div>
@endsection