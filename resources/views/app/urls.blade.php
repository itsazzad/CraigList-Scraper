@extends('app/master')

@section('body')

	@if (count($errors) > 0)
	    <div class="alert alert-danger">
	        <ul>
	            @foreach ($errors->all() as $error)
	                <li>{{ $error }}</li>
	            @endforeach
	        </ul>
	    </div>
	@endif

	{!! Form::open(['url'=>'data/url', 'method'=>'POST']) !!}

		<div class="form-group">
			<label for="name">Url:</label>
			<input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}">
		</div>

		<div class="form-group">
			<button type="submit" class="btn btn-default">Add Url</button>
		</div>
	{!! Form::close() !!}
	
	@forelse($urls as $url)
		<li>{{ $url->name }} 
		<a href="{{ url('data/geturl').'?url='.$url->name }}">Scrap</a>
		<a href="{{ url('data/links', $url->id) }}">View All Links</a>
		</li>
	@empty
		<h2>No Url Found</h2>
	@endforelse
@stop