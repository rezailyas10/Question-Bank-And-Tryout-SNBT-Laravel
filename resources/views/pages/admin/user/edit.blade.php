@extends('layouts.admin')

@section('title')
  User
@endsection

@section('content')
<div
class="section-content section-dashboard-home"
data-aos="fade-up"
>
<div class="container-fluid">
  <div class="dashboard-heading">
    <h2 class="dashboard-title">User</h2>
    <p class="dashboard-subtitle">
      Edit User
    </p>
  </div>
  <div class="dashboard-content">
   <div class="row">
    <div class="col-md-12">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card">
            <div class="card-body">
                <form action="{{ route('user.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="row">
                        <div class="col-md-12 d-none">
                            <div class="form-group">
                                <label for="">Nama User</label>
                                <input type="text" name="name" class="form-control" required value="{{ $item->name }}">
                            </div>
                        </div>
                        <div class="col-md-12 d-none">
                            <div class="form-group">
                                <label for="">Username User</label>
                                <input type="text" name="username" class="form-control" required value="{{ $item->username }}">
                            </div>
                        </div>
                        <div class="col-md-12 d-none">
                            <div class="form-group">
                                <label for="">Email User</label>
                                <input type="email" name="email" class="form-control" required value="{{ $item->email }}">
                            </div>
                        </div>
                        {{-- <div class="col-md-12 d-none">
                            <div class="form-group">
                                <label for="">Password User</label>
                                <input type="password" name="password" class="form-control" >
                                <small>Kosongan jika tidak ingin mengganti password</small>
                            </div>
                        </div> --}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Roles</label>
                                <select name="roles" class="form-control"  required>
                                    <option value="{{ $item->roles }}">Tidak diganti</option>
                                    <option value="ADMIN">Admin</option>
                                    <option value="KONTRIBUTOR">KONTRIBUTOR</option>
                                    <option value="USER">User</option>
                                    <option value="SALES">Sales</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                        <div class="form-group">
                            <label for="is_validator">Validator Pertanyaan?</label>
                            <select name="is_validator" class="form-control" >
                                <option value="0" {{ $item->is_validator == 0 ? 'selected' : '' }}>Tidak</option>
                                <option value="1" {{ $item->is_validator == 1 ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="sub_category_id">Spesialisasi Mata Pelajaran</label>
                            <select name="sub_category_id" class="form-control" >
                                @foreach($subCategories as $subCategory)
                                    <option value="{{ $subCategory->id }}" {{ $item->sub_category_id == $subCategory->id ? 'selected' : '' }}>
                                        {{ $subCategory->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    </div>
                    <div class="row">
                        <div class="col text-right">
                            <button type="submit" class="btn btn-success px-5">Save Now</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

   </div>
</div>
</div>

@endsection

