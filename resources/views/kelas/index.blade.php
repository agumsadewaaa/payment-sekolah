@extends('layouts.app')

@section('content')
<!--**********************************
    Content body start
***********************************-->
<section class="content-header">    
    <div class="container-fluid">
        <div class="page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Table</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Kelas</a></li>
            </ol>
        </div>

        <div class="content px-3">
            @include('flash::message')
            <div class="clearfix"></div>
        </div>
        <!-- row -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">List Kelas</h4>
                        @role('admin')
                            <a class="btn btn-primary float-right" href="{{ route('kelas.create') }}">Add New</a>
                        @endrole
                    </div>
                    @include('kelas.table')
                </div>
            </div>
        </div>
    </div>
</section>
<!--**********************************
    Content body end
***********************************-->
@endsection
