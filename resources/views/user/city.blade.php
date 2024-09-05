@extends('user.master')

@section('content')
    <div class="container mt-5">
        <form action="{{ route('city.save') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="d-flex mb-4 align-items-center gap-3">

                <label for="csv">Import CSV</label>
                <input type="file" placeholder="Import CSV" name="city" required id="csv">
                <button type="submit" class="btn btn-info">Import</button>
            </div> 
        </form>
        <div class="table-responsive">
            <table class="table table-primary">
                <thead>
                    <tr>
                        <th scope="col">Sr. No.</th>
                        <th scope="col">Name</th>
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
            url: "{{ route('city.list') }}",
            dataType: "json",
            success: function(response) {
                $("#dataTable").html('')

                if (response.status) {
                    let html = "";
                    response.data.forEach((e, i) => {
                        html += `<tr>
                                    <td>${i + 1}</td>
                                    <td>${e.name}</td>
                                    <td>${e.created_at}</td>
                                </tr>`
                    });

                    $("#dataTable").html(html)
                }

            }
        });
    </script>
@endpush
