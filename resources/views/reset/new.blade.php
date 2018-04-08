
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">


    <title>ESSPE - Reset Password</title>

    <!-- Bootstrap core CSS -->
    <link href="https://getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="https://getbootstrap.com/docs/4.0/examples/sign-in/signin.css" rel="stylesheet">
</head>

<body class="text-center">
<form class="form-signin" method="post" action="{{route('reset.password')}}">
    <img class="rounded-circle" src="{{asset('public/images/logo.png')}}" alt="" width="100" height="100"><br><br>
    <h1 class="h3 mb-3 font-weight-normal"> ادخل الباسورد الجديد </h1>
    <input type="password" name="password" class="form-control" placeholder="Password" required>
    <input type="hidden" name="email" value="{{$resetRequest->email}}">
    <input type="hidden" name="token" value="{{$token}}">

    <button class="btn btn-lg btn-primary btn-block" type="submit"> تحديث الباسورد </button>
    {{csrf_field()}}
</form>
</body>
</html>
