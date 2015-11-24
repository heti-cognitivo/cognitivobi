<!doctype html>
<html>
<head>
<title>Look at me Login</title>
</head>
<body>

{!! Form::open(array('url' => 'auth/login')) !!}
<h1>Login</h1>

<!-- if there are login errors, show them here -->
<p>
  @if( ! empty($loginerrors))
    {{$loginerrors}}
  @endif
</p>

<p>
    {!! Form::label('name', 'Nombre') !!}
    {!! Form::text('name', Input::old('name'), array('placeholder' => 'awesome')) !!}
</p>

<p>
    {!! Form::label('password', 'Password') !!}
    {!! Form::password('password') !!}
</p>

<p>{!! Form::submit('Submit!') !!}</p>
{!! Form::close() !!}
</body>
