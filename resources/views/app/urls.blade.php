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

	@if(Session::has('message'))
	    <div class="alert alert-success">
	        <p>{{ Session::get('message') }}</p>
	    </div>
	@endif

	{!! Form::open(['url'=>'data/url', 'method'=>'POST']) !!}

		<div class="form-group">
			<label for="name">Url:</label>
			<input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}">
		</div>

		<div class="form-group">
			<center>
				<button type="submit" class="btn btn-default">Scrap Url</button>
			</center>
		</div>
	{!! Form::close() !!}
	
	@if(Session::has('leads'))
	    <table class="table">
			<tr>
				<th>Email</th>
				<td>Phone</td>
				<td>Name</td>
				<td>Title</td>
			</tr>
			@forelse( Session::get('leads') as $lead )
				<tr>
					<td>{{ $lead->email }}</td>
					<td>{{ $lead->phone }}</td>
					<td>{{ $lead->name }}</td>
					<td>{{ $lead->title }}</td>
				</tr>
			@empty
				<tr>
					<td colspan="4"><h2>No Data Found</h2></td>
				</tr>
			@endforelse
		</table>
	@endif


@stop