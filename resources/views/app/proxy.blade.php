@extends('app.master')

@section('body')
	
	<div class="col-md-6">
		@if(Session::has('message'))
		    <div class="alert alert-success">
		        <p>{{ Session::get('message') }}</p>
		    </div>
		@endif
		<a href="{{	url('data/proxylist') }}" class="btn btn-primary">Update Proxy List</a>
		<table class="table">
			<tr>
				<th>IP</th>
				<th>Port</th>
			</tr>
			
			@forelse($proxys as $proxy)
			<tr>
				<td>{{ $proxy->ip }}</td>
				<td>{{ $proxy->port }}</td>
			</tr>
			@empty

			@endforelse
		</table>		
	</div>	
@stop