@extends('email.header')
@section('email-content')


<table
align="center"
border="0"
      style="
        font-size: 13px;
        line-height: 22px;
        margin: 0 auto;
        width: 600px;
        background: #f4f5fd;
        background-color: #f4f5fd;
        text-align: center;
        padding: 25px;
      "
    >
      <tbody>
        <tr>
          <td
            style="
              font-family: 'Droid Sans', 'Helvetica Neue', Arial, sans-serif;
              font-size: 36px;
              padding: 0px 0px 15px 0px;
            "
          >
          Hi {{ $datas['user_full_name'] }}

          </td>
        </tr>
        <tr>
          <td
            style="
              font-family: 'Droid Sans', 'Helvetica Neue', Arial, sans-serif;
              font-size: 16px;
              padding: 0px 0px 10px 0px;
            "
          >
          OTP for login to Elroi Consumer portal is <b>
            {{ $datas['factor_code'] }}</b>

        </td>
        </tr>
        <tr>
          <td
            style="
              font-family: 'Droid Sans', 'Helvetica Neue', Arial, sans-serif;
              font-size: 16px;
            "
          >
          Thank you, <br>Elroi Consumer Portal
        </td>
        </tr>
      </tbody>
    </table>

@endsection
