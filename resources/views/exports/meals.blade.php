<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Name (English)</th>
        <th>Name (Arabic)</th>
        <th>Calories</th>
        <th>Price</th>
        <th>Category</th>
    </tr>
    </thead>
    <tbody>
    @foreach($meals as $key=>$meal)
        <tr>
            <td>{{ $key+1 }}</td>
            <td>{{ $meal->name_en }}</td>
            <td>{{ $meal->name_ar }}</td>
            <td>{{ $meal->calories }}</td>
            <td>{{ $meal->price }}</td>
            <td>{{ $meal->category->name_en }} - {{ $meal->category->name_ar }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
