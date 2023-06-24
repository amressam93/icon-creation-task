<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>list of users</title>
</head>
<body>
<h1>list of all users</h1>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Is Blocked</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->is_user_blocked }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="4">No users found</td>
        </tr>
    @endforelse
    </tbody>
</table>
</body>
</html>
