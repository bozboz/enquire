<?php $font = 'font-family: Helvetica Neue,Helvetica,Arial,sans-serif; font-size: 14px; color: #444444;'; ?>

<html lang="en">
<head>
    <style>
        body, #body {
            font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
            font-size: 14px;
            color: #444444;
            line-height: 1.3;
        }

        p {
            font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
            font-size: 14px;
            color: #444444;
            line-height: 1.3;
        }

        #emailContainer {
            width: 600px;
        }
    </style>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background: #eeeeee" id="body">
<center>
    <table border="0" cellpadding="20" cellspacing="20" style="background: #ffffff" id="emailContainer">
        <tr>
            <td align="left">
                <a href="{{Request::root()}}" border="0" style="border:0; width:100%; text-align:center;"><img src="{{ asset('assets/images/logo.jpg') }}" style="max-width:600px;" id="headerImage" /></a>
            </td>
        </tr>
        <tr>
            <td>
                @foreach ($form->fields as $field)
                    @if (array_key_exists($field->name, $input))
                        <p style="{{ $font }}"><strong>{!! $field->label !!}:</strong> {!! $field->formatInputForEmail($input) !!}</p>
                    @endif
                @endforeach
            </td>
        </tr>
    </table>
</center>
</body>
</html>
