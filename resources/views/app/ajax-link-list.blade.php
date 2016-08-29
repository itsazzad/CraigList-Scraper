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
	
