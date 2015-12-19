@extends('app/master')

@section('body')
	
	<table class="table">
			<tr>
				<th>Email</th>
				<td>Phone</td>
				<td>Name</td>
				<td>Title</td>
			</tr>
		@forelse( $leads as $lead )
			<tr>
				<td>{{ $lead->email }}</td>
				<td>{{ $lead->phone }}</td>
				<td>{{ $lead->name }}</td>
				<?php 
					$title = explode("-", $lead->title);
				?>
				<td>{{ $title[1] }}</td>
			</tr>
		@empty
			<tr>
				<td colspan="4"><h2>No Data Found</h2></td>
			</tr>
		@endforelse
	</table>

@stop