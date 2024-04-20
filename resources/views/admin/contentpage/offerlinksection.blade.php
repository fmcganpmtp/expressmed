@extends('layouts.admin')
@section('content')
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>{{ _('messages.Whoops') }}!</strong> {{ _('messages.There were some problems with your input') }}.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">
            <ul>
                <li>{{ session('success') }}</li>
            </ul>
        </div>
    @endif
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Offer Link Section</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.contentpages') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf
          
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Offer Content</strong> <input type="text" name="offer_content" value="{{ $offer_contents->offer_content }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group">
                                    <strong>Offer Link</strong> eg:(http://url.com)
                                    <input type="text" name="offer_link" class="form-control" value="{{ (old('offer_link')!='')?old('offer_link'):$offer_contents->offer_link }}" placeholder="http://url.com">
                                </div>
                                    </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong></strong>
                                <select name="status" class="form-control" id="status">
                                    <option value="">Select Status</option>
                                    <option value="active" @if ($offer_contents->status == 'active'){{ 'selected' }}@endif>Active</option>
                                    <option value="inactive" @if ($offer_contents->status == 'inactive'){{ 'selected' }}@endif>Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        
                        
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <input type="submit" value="Update" class="btn btn-primary">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')
<script>
    $('.alert-success').fadeIn().delay(3000).fadeOut();
    
</script>
    

@endsection
