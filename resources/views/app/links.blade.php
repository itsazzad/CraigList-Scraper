@extends('app/master')

@section('body')
	<ul>
	@forelse($url as $links)
		<li><a href="{{ url('data/info', $links->id) }}">{{ $links->link }}</a></li>
	@empty
		<li>No url found. Please scrap some url links</li>
	@endforelse
	</ul>
@stop