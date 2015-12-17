@extends('app/master')

@section('body')
	<?php 

		$urls = parse_url($url->name);

	?>
	<ul>
	@forelse($url->links as $link)
		<li><a href="{{ url('data/info', $link->id) }}">{{ $urls['host'].$link->name }}</a></li>
	@empty
		<li>No url found. Please scrap some url links</li>
	@endforelse
	</ul>
@stop