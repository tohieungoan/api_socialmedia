<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>

    <form method="POST" action="{{ route('apilogout') }}">
        @csrf
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="avatar" placeholder="Avatar URL">
        <input type="date" name="birthday" placeholder="Birthday">
        <input type="text" name="sociallogin" placeholder="Social Login">
        <input type="tel" name="phone" placeholder="Phone">
        <button type="submit">Register</button>
    </form>
</body>
</html>