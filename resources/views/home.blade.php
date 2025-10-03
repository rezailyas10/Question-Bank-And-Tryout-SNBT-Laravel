
@section('content')
    @role('ADMIN')
        @include('pages.admin.dashboard')
    @elserole('KONTRIBUTOR')
        @include('pages.kontributor.dashboard')
    @elserole('USER')
        @include('pages.home') <!-- atau dashboard user -->
    @else
        <p>Silakan login untuk mengakses konten.</p>
    @endrole
@endsection
