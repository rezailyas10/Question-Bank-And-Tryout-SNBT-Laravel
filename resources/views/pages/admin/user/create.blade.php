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
      Create New User
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
                <form action="{{ route('user.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Nama User</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Username User</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Email User</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Password User</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Roles</label>
                                <select name="roles" class="form-control"  required>
                                    <option value="ADMIN">Admin</option>
                                    <option value="KONTRIBUTOR">Kontributor</option>
                                    <option value="USER">User</option>
                                    <option value="SALES">Sales</option>
                                    <option value="VALIDATOR">Validator</option>
                                </select>
                            </div>
                        </div>
                        {{-- jika perannya adalah kontributor dan validator --}}
                          <div class="col-md-12">
                            <div class="form-group">
                                <label for="is_validator">Validator Pertanyaan?</label>
                                <select name="is_validator" class="form-control" >
                                    <option value="0">Tidak</option>
                                    <option value="1">Ya</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="sub_category_id">Spesialisasi Mata Pelajaran</label>
                                <select name="sub_category_id" class="form-control" >
                                    @foreach($subCategories as $subCategory)
                                        <option value="{{ $subCategory->id }}">{{ $subCategory->name }}</option>
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

