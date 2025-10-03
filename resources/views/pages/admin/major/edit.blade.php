@extends('layouts.admin')

@section('title')
  Program Studi
@endsection

@section('content')
<div
class="section-content section-dashboard-home"
data-aos="fade-up"
>
<div class="container-fluid">
  <div class="dashboard-heading">
    <h2 class="dashboard-title">Program Studi</h2>
    <p class="dashboard-subtitle">
      Edit Program Studi
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
                <form action="{{ route('major.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Nama Program Studi</label>
                                <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Jenjang</label>
                                <input type="text" name="level" class="form-control" value="{{ $item->level }}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Skor UTBK Rata-Rata</label>
                                <input type="text" name="passing_score" class="form-control" value="{{ $item->passing_score }}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Daya Tampung</label>
                                <input type="text" name="quota" class="form-control" value="{{ $item->quota }}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Peminat</label>
                                <input type="text" name="peminat" class="form-control" value="{{ $item->peminat }}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Universitas</label>
                                <select name="university_id" class="form-control">
                                    <option value="{{ $item->University_id }}" selected>{{ $item->University->name }}</option>
                                    @foreach ($universities as $University)
                                    <option value="{{ $University->id }}">{{ $University->name }}</option>
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

