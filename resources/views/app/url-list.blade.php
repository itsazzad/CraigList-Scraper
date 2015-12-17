@extends('app/master')

@section('body')
	
	@forelse($urls as $url)
		<li>{{ $url->name }} 
		<a href="{{ url('data/geturl').'?url='.$url->name }}">Scrap</a>
		<a href="{{ url('data/links', $url->id) }}">View All Links</a>
		</li>
	@empty
		<h2>No Url Found</h2>
	@endforelse
	
@stop