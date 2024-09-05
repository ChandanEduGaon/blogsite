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
        <form action="{{ route('my_file.save') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="d-flex mb-4 align-items-center gap-3">

                <label for="my_file">Upload File</label>
                <input type="file" placeholder="Import file" name="my_file" required id="my_file">
                <button type="submit" class="btn btn-info">Upload</button>
            </div> 
        </form>
        <div class="table-responsive">
            <table class="table table-primary">
                <thead>
                    <tr>
                        <th scope="col">Sr. No.</th>
                        <th scope="col">Name</th>
                        <th scope="col">Original Size</th>
                        <th scope="col">Compressed Size</th>
                        <th scope="col">URL</th>
                        <th scope="col">Date</th>
                    </tr>
                </thead>
                <tbody id="dataTable">

                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $.ajax({
            type: "GET",
            url: "{{ route('my_file.list') }}",
            dataType: "json",
            success: function(response) {
                $("#dataTable").html('')

                if (response.status) {
                    let html = "";
                    response.data.forEach((e, i) => {
                        html += `<tr>
                                    <td>${i + 1}</td>
                                    <td>${e.name}</td>
                                    <td>${e.original_size}</td>
                                    <td>${e.compressed_size}</td>
                                    <td>${e.url}</td>
                                    <td>${e.created_at}</td>
                                </tr>`
                    });

                    $("#dataTable").html(html)
                }

            }
        });
    </script>
@endpush
