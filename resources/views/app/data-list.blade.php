@extends('app/master')

@section('body')

	@if(Session::has('message'))
	    <div class="alert alert-success">
	        <p>{{ Session::get('message') }}</p>
	    </div>
	@endif
	
	{!! Form::open(array('url' => 'data/scrapedDataDownload', 'method' => 'POST')) !!}
		<table class="table">
			<tr>
				<td colspan="2">
				<div class="form-group">
					<select name="url" class="form-control view-link">
						<option value="0">Please Select A Url</option>
						@forelse($urls as $url)
						<option value="{{ $url->id }}">{{ $url->name }}</option>
						@empty
							<option value="0">No url found</option>
						@endforelse
					</select>
				</div>
				</td>
				<td></td>
				<td align="center"><button type="submit" class="btn btn-success">Download Scraped Data</button></td>
			</tr>
			<tr>
				<th><input type="checkbox" name="fileforp[]" value="email">	Email 	</th>
				<th><input type="checkbox" name="fileforp[]" value="phone">	phone 	</th>
				<th><input type="checkbox" name="fileforp[]" value="name">	Name 	</th>
				<th><input type="checkbox" name="fileforp[]" value="title">	Title 	</th>
			</tr>
		</table>
		<table class="table link-list">
			@forelse( $leads as $lead )
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
	{!! Form::close() !!}
@stop